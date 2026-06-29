<?php
require_once '../../config.php';
require_once '../../db.php';
require_once '../../includes/math_normalizer.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$input     = json_decode(file_get_contents('php://input'), true);
$draft_id  = (int)($input['draft_id']  ?? 0);
$lesson_id = (int)($input['lesson_id'] ?? 0);
$user_hint = trim((string)($input['user_hint'] ?? ''));

if (!$draft_id || !$lesson_id) { echo json_encode(['error' => 'Missing params']); exit; }

$db = get_db();

$ds = $db->prepare('SELECT * FROM ls_draft WHERE id = ?');
$ds->execute([$draft_id]);
$draft = $ds->fetch();
if (!$draft) { echo json_encode(['error' => 'Draft not found']); exit; }

$ls = $db->prepare('SELECT * FROM lesson WHERE id = ?');
$ls->execute([$lesson_id]);
$lesson = $ls->fetch();

// Get theory context from existing drafts/activities
$theory_parts = $db->prepare("
    SELECT data FROM ls_draft WHERE lesson_id = ? AND type = 'theory' AND id != ?
    UNION
    SELECT data FROM activity WHERE lesson_id = ? AND type = 'theory'
    LIMIT 2
");
$theory_parts->execute([$lesson_id, $draft_id, $lesson_id]);
$theory_summary = '';
foreach ($theory_parts->fetchAll() as $row) {
    $d = json_decode($row['data'], true);
    $theory_summary .= strip_tags($d['content'] ?? '') . "\n";
}

// Lesson summary (all content)
$all_content = $db->prepare("
    (SELECT type, data FROM ls_draft WHERE lesson_id = ? ORDER BY sort_order)
    UNION
    (SELECT type, data FROM activity WHERE lesson_id = ? ORDER BY sort_order)
");
$all_content->execute([$lesson_id, $lesson_id]);
$lesson_summary = '';
foreach ($all_content->fetchAll() as $row) {
    $d = json_decode($row['data'], true);
    $text = strip_tags($d['content'] ?? $d['problem'] ?? $d['question'] ?? $d['title'] ?? '');
    if ($text) $lesson_summary .= "[$row[type]] $text\n";
}

// Anti-duplicate context: collect existing question/problem text for exercise types
$existing_questions_block = '';
if (in_array($draft['type'], ['quiz', 'fill_answer', 'guided_practice'])) {
    $eq_stmt = $db->prepare("
        SELECT type, data FROM ls_draft
        WHERE lesson_id = ? AND id != ?
          AND type IN ('quiz','fill_answer','guided_practice')
          AND data IS NOT NULL AND data != '{}'
        UNION
        SELECT type, data FROM activity
        WHERE lesson_id = ?
          AND type IN ('quiz','fill_answer','guided_practice')
    ");
    $eq_stmt->execute([$lesson_id, $draft_id, $lesson_id]);
    $existing_lines = [];
    foreach ($eq_stmt->fetchAll() as $eqr) {
        $d = json_decode($eqr['data'], true);
        $q = strip_tags($d['question'] ?? $d['problem'] ?? '');
        if ($q) $existing_lines[] = '- ' . mb_substr($q, 0, 120);
    }
    if ($existing_lines) {
        $existing_questions_block = "\n\nCHÚ Ý BẮT BUỘC: Câu hỏi/bài tập này phải HOÀN TOÀN KHÁC về nội dung và số liệu với các câu sau đây đã có trong bài:\n"
            . implode("\n", $existing_lines)
            . "\nTuyệt đối không hỏi cùng khái niệm, cùng phép tính, hoặc dùng cùng số liệu.";
    }
}

// Load worker prompt
$worker = $draft['type'];
$ps = $db->prepare('SELECT * FROM ls_prompt WHERE worker = ?');
$ps->execute([$worker]);
$prompt_row = $ps->fetch();

$api_key = OPENAI_API_KEY;

if (empty($api_key)) {
    echo json_encode(['error' => 'Chưa cấu hình OpenAI API key']);
    exit;
}

if (!$prompt_row) {
    echo json_encode(['error' => "Không tìm thấy prompt cho worker: $worker"]);
    exit;
}

$system = $prompt_row['system_prompt'];
$user   = str_replace(
    ['{{lesson_title}}', '{{chapter_title}}', '{{activity_title}}', '{{theory_summary}}', '{{lesson_summary}}'],
    [
        $lesson['title'] ?? '',
        'Chương 1 — Số hữu tỉ',
        $draft['title'] ?? '',
        mb_substr($theory_summary, 0, 600),
        mb_substr($lesson_summary, 0, 800),
    ],
    $prompt_row['user_template']
);

// Append anti-duplicate block for exercise types
$user .= $existing_questions_block;

// Append teacher hint if provided
if ($user_hint !== '') {
    $user .= "\n\nYÊU CẦU CỤ THỂ TỪ GIÁO VIÊN (ưu tiên cao nhất, bám sát yêu cầu này):\n" . $user_hint;
}

// Higher temperature for exercise types to encourage variety
$temperature = in_array($worker, ['quiz', 'fill_answer', 'guided_practice']) ? 0.8 : 0.5;

// ── Helper functions ────────────────────────────────────────────────────────

function fix_latex_backslashes(mixed $val): mixed {
    if (is_string($val)) return preg_replace('/\\\\\\\\([a-zA-Z(\[)\]])/', '\\\\$1', $val);
    if (is_array($val))  return array_map('fix_latex_backslashes', $val);
    return $val;
}

function fix_latex_commands(mixed $val): mixed {
    if (is_string($val)) {
        $val = preg_replace('/\\\\text\{sqrt\{([^}]*)\}\}/', '\\\\sqrt{$1}', $val);
        $val = preg_replace('/\\\\text\{(pi|alpha|beta|gamma|delta|theta|lambda|mu|nu|sigma|omega|infty|sqrt)\}/', '\\\\$1', $val);
        return $val;
    }
    if (is_array($val)) return array_map('fix_latex_commands', $val);
    return $val;
}

function strip_control_chars(mixed $val): mixed {
    if (is_string($val)) return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $val);
    if (is_array($val))  return array_map('strip_control_chars', $val);
    return $val;
}

/**
 * For quiz: resolve correct_option text → answer index.
 * AI writes correct_option = exact text from options[]; we find the index.
 * Removes correct_option from the array and sets/overwrites answer.
 */
function resolve_quiz_answer(array &$parsed): void {
    if (empty($parsed['correct_option']) || empty($parsed['options'])) return;

    $target = trim((string)$parsed['correct_option']);
    $found  = null;

    // Exact match first
    foreach ($parsed['options'] as $i => $opt) {
        if (trim((string)$opt) === $target) { $found = $i; break; }
    }
    // Case-insensitive fallback
    if ($found === null) {
        foreach ($parsed['options'] as $i => $opt) {
            if (mb_strtolower(trim((string)$opt)) === mb_strtolower($target)) { $found = $i; break; }
        }
    }

    if ($found !== null) $parsed['answer'] = $found;
    unset($parsed['correct_option']); // clean up temp field before saving
}

/** Returns null if data is valid for the given worker, or an error string. */
function validate_activity(array $parsed, string $worker): ?string {
    if ($worker === 'quiz') {
        $opts = $parsed['options'] ?? [];
        if (count($opts) !== 4)
            return 'options phải có đúng 4 phần tử, nhận được ' . count($opts);
        $dummy = ['a','b','c','d','a.','b.','c.','d.','a)','b)','c)','d)'];
        foreach ($opts as $o) {
            if (in_array(mb_strtolower(trim((string)$o)), $dummy, true))
                return 'options[' . $o . '] là ký tự giả — phải là giá trị thực (số, biểu thức, phương án)';
        }
        if (!isset($parsed['answer']) || !is_int($parsed['answer']) || $parsed['answer'] < 0 || $parsed['answer'] > 3)
            return 'answer phải là số nguyên 0–3';
    }
    return null; // valid
}

/** One OpenAI call → returns ['parsed'=>array, 'usage'=>array] or throws on HTTP/JSON error. */
function call_openai(string $api_key, string $system_msg, string $user_msg, float $temperature): array {
    $payload = json_encode([
        'model'           => 'gpt-4o-mini',
        'max_tokens'      => 1200,
        'temperature'     => $temperature,
        'messages'        => [
            ['role' => 'system', 'content' => $system_msg],
            ['role' => 'user',   'content' => $user_msg],
        ],
        'response_format' => ['type' => 'json_object'],
    ]);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . $api_key],
        CURLOPT_TIMEOUT        => 45,
    ]);
    $result    = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200)
        throw new RuntimeException('OpenAI HTTP ' . $http_code . ': ' . $result);

    $ai_data = json_decode($result, true);
    $ai_text = $ai_data['choices'][0]['message']['content'] ?? '';

    // Fix bare LaTeX before json_decode (\frac → \\frac)
    $ai_text = preg_replace_callback(
        '/(\\\\\\\\)|\\\\([a-zA-Z])(?=[a-zA-Z{])/',
        fn($m) => $m[1] !== '' ? $m[1] : '\\\\' . $m[2],
        $ai_text
    );

    $parsed = json_decode($ai_text, true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($parsed))
        throw new RuntimeException('Invalid JSON from AI: ' . substr($ai_text, 0, 300));

    return ['parsed' => $parsed, 'usage' => $ai_data['usage'] ?? []];
}

// ── AI call with retry on validation failure ─────────────────────────────────

$parsed    = null;
$ai_usage  = [];
$MAX_TRIES = 3;

for ($try = 1; $try <= $MAX_TRIES; $try++) {
    $current_user = $user;
    if ($try > 1) {
        // On retry: append explicit correction instruction
        $current_user .= "\n\nLỖI LẦN TRƯỚC: {$last_validation_error}\n"
            . "Hãy tạo lại và đảm bảo mảng options[] chứa GIÁ TRỊ THỰC, không phải chữ cái A/B/C/D.";
    }

    try {
        $res      = call_openai($api_key, $system, $current_user, $temperature);
        $attempt  = $res['parsed'];
        $ai_usage = $res['usage'];
    } catch (RuntimeException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }

    // Apply all post-processing fixes
    $attempt = fix_latex_backslashes($attempt);
    $attempt = fix_latex_commands($attempt);
    $attempt = strip_control_chars($attempt);
    $attempt = normalize_all_strings($attempt);
    $attempt = normalize_formula_fields($attempt, $worker);

    // Quiz: derive answer index from correct_option text (avoids off-by-one errors)
    if ($worker === 'quiz') resolve_quiz_answer($attempt);

    $validation_error = validate_activity($attempt, $worker);
    if ($validation_error === null) {
        $parsed = $attempt;
        break;
    }
    $last_validation_error = $validation_error;
}

if ($parsed === null) {
    echo json_encode(['error' => "AI liên tục trả về dữ liệu không hợp lệ sau {$MAX_TRIES} lần: {$last_validation_error}"]);
    exit;
}

// Save to draft
$db->prepare('UPDATE ls_draft SET data = ?, status = "draft", updated_at = NOW() WHERE id = ?')
   ->execute([json_encode($parsed, JSON_UNESCAPED_UNICODE), $draft_id]);

// Log (record total tries in tokens_output as annotation)
$db->prepare('INSERT INTO ls_generation_log (draft_id, lesson_id, worker, tokens_input, tokens_output) VALUES (?,?,?,?,?)')
   ->execute([
       $draft_id, $lesson_id, $worker,
       $ai_usage['prompt_tokens']     ?? 0,
       $ai_usage['completion_tokens'] ?? 0,
   ]);

echo json_encode(['ok' => true, 'draft_id' => $draft_id, 'type' => $worker]);
