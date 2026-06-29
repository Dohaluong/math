<?php
require_once '../../config.php';
require_once '../../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400); echo json_encode(['error'=>'Invalid JSON']); exit;
}

$activity_id  = (int)($input['activity_id'] ?? 0);
$step_idx     = (int)($input['step_idx']    ?? 0);
$student_ans  = trim($input['student_answer'] ?? '');
$student_id   = STUDENT_ID;

$db = get_db();

$act = $db->prepare('SELECT a.data, l.title AS lesson_title FROM activity a JOIN lesson l ON a.lesson_id = l.id WHERE a.id = ?');
$act->execute([$activity_id]);
$act = $act->fetch();
if (!$act) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }

$data  = json_decode($act['data'], true);
$steps = $data['steps'] ?? [];
$step  = $steps[$step_idx] ?? null;
if (!$step) { http_response_code(400); echo json_encode(['error'=>'Invalid step']); exit; }

$api_key = OPENAI_API_KEY;
if (empty($api_key)) {
    // Return static hint if no API key
    echo json_encode(['hint' => $step['hint'] ?? 'Hãy xem lại lý thuyết và thử lại.']);
    exit;
}

$context = "Bài học: {$act['lesson_title']}\n"
    . "Bài toán: " . strip_tags($data['problem'] ?? '') . "\n"
    . "Câu hỏi bước này: " . strip_tags($step['prompt']) . "\n"
    . "Học sinh trả lời: $student_ans\n"
    . "Gợi ý có sẵn: " . strip_tags($step['hint'] ?? '') . "\n\n"
    . "Học sinh trả lời sai. Hãy giúp học sinh bằng một câu gợi ý ngắn, không tiết lộ đáp án.";

$payload = json_encode([
    'model'      => 'gpt-4o-mini',
    'max_tokens' => 150,
    'temperature'=> 0.4,
    'messages'   => [
        ['role'=>'system','content'=>'Bạn là giáo viên Toán lớp 7 thân thiện. Đừng tiết lộ đáp án. Gợi ý ngắn, dưới 50 từ, tiếng Việt.'],
        ['role'=>'user',  'content'=>$context],
    ],
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json','Authorization: Bearer '.$api_key],
    CURLOPT_TIMEOUT        => 20,
]);
$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['hint' => $step['hint'] ?? 'Hãy thử lại.']);
    exit;
}

$resp = json_decode($result, true);
echo json_encode(['hint' => $resp['choices'][0]['message']['content'] ?? $step['hint'] ?? '']);
