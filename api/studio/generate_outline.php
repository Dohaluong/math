<?php
require_once '../../config.php';
require_once '../../db.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$lesson_id    = (int)($input['lesson_id']    ?? 0);
$blueprint_id = isset($input['blueprint_id']) ? (int)$input['blueprint_id'] : null;

if (!$lesson_id) { echo json_encode(['error' => 'Missing lesson_id']); exit; }

$db = get_db();

$ls = $db->prepare('SELECT * FROM lesson WHERE id = ?');
$ls->execute([$lesson_id]);
$lesson = $ls->fetch();
if (!$lesson) { echo json_encode(['error' => 'Lesson not found']); exit; }

// If blueprint_id provided: use blueprint structure (no AI call)
if ($blueprint_id !== null && $blueprint_id > 0) {
    $bps = $db->prepare('SELECT * FROM ls_blueprint WHERE id = ?');
    $bps->execute([$blueprint_id]);
    $bp = $bps->fetch();
    if (!$bp) { echo json_encode(['error' => 'Blueprint not found']); exit; }

    $structure = json_decode($bp['structure'], true);
    insertDrafts($db, $lesson_id, $structure);
    echo json_encode(['ok' => true, 'count' => count($structure), 'source' => 'blueprint']);
    exit;
}

// AI outline generation
$api_key = OPENAI_API_KEY;
if (empty($api_key)) {
    // Fallback: use default blueprint
    $default_bp = $db->query('SELECT * FROM ls_blueprint WHERE is_default = 1 LIMIT 1')->fetch();
    if ($default_bp) {
        $structure = json_decode($default_bp['structure'], true);
        insertDrafts($db, $lesson_id, $structure);
        echo json_encode(['ok' => true, 'count' => count($structure), 'source' => 'default_blueprint_fallback']);
    } else {
        echo json_encode(['error' => 'Chưa cấu hình OpenAI API key và không có blueprint mặc định']);
    }
    exit;
}

// Load outline prompt
$prompt_row = $db->prepare('SELECT * FROM ls_prompt WHERE worker = "outline"');
$prompt_row->execute();
$prompt_row = $prompt_row->fetch();

$system = $prompt_row['system_prompt'] ?? 'You are an educational content designer. Output only valid JSON.';
$user   = str_replace(
    ['{{lesson_title}}', '{{chapter_title}}'],
    [$lesson['title'], 'Chương 1 — Số hữu tỉ'],
    $prompt_row['user_template'] ?? 'Create outline for: {{lesson_title}}'
);

$payload = json_encode([
    'model'       => 'gpt-4o-mini',
    'max_tokens'  => 800,
    'temperature' => 0.4,
    'messages'    => [
        ['role' => 'system', 'content' => $system],
        ['role' => 'user',   'content' => $user],
    ],
    'response_format' => ['type' => 'json_object'],
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $api_key],
    CURLOPT_TIMEOUT        => 30,
]);
$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['error' => 'OpenAI API error: ' . $http_code]);
    exit;
}

$ai_data  = json_decode($result, true);
$ai_text  = $ai_data['choices'][0]['message']['content'] ?? '';
$parsed   = json_decode($ai_text, true);
$outline  = $parsed['outline'] ?? [];

if (empty($outline)) {
    echo json_encode(['error' => 'AI returned empty outline', 'raw' => $ai_text]);
    exit;
}

insertDrafts($db, $lesson_id, $outline);

// Log
$db->prepare('INSERT INTO ls_generation_log (lesson_id, worker, tokens_input, tokens_output) VALUES (?,?,?,?)')
   ->execute([$lesson_id, 'outline',
       $ai_data['usage']['prompt_tokens'] ?? 0,
       $ai_data['usage']['completion_tokens'] ?? 0]);

echo json_encode(['ok' => true, 'count' => count($outline), 'source' => 'ai']);


function insertDrafts(PDO $db, int $lesson_id, array $structure): void {
    // Clear existing drafts for this lesson
    $db->prepare('DELETE FROM ls_draft WHERE lesson_id = ? AND status IN ("draft","rejected")')
       ->execute([$lesson_id]);

    $stmt = $db->prepare('INSERT INTO ls_draft (lesson_id, type, title, data, sort_order) VALUES (?,?,?,?,?)');
    foreach ($structure as $i => $item) {
        $stmt->execute([
            $lesson_id,
            $item['type']  ?? 'theory',
            $item['title'] ?? '',
            '{}',
            ($i + 1) * 10,
        ]);
    }
}
