<?php
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$lesson_id    = (int)($input['lesson_id']  ?? 0);
$question_id  = isset($input['question_id']) && $input['question_id'] !== null
                ? (int)$input['question_id'] : null;
$student_msg  = trim($input['message'] ?? '');
$student_id   = STUDENT_ID;

if (!$lesson_id || $student_msg === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

if (mb_strlen($student_msg) > 500) {
    http_response_code(400);
    echo json_encode(['error' => 'Message too long']);
    exit;
}

$db = get_db();

function strip_for_ai(string $text): string {
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = strip_tags($text);
    $text = preg_replace('/\\\\\(|\\\\\)|\\\\\[|\\\\\]/', '', $text);
    return trim($text);
}

// ── Build context ───────────────────────────────────────────
if ($question_id) {
    // Mode: giải thích sai/đúng cho một câu hỏi cụ thể (review page)
    $q_stmt = $db->prepare('
        SELECT q.question, q.option_a, q.option_b, q.option_c, q.option_d, q.hint,
               l.title AS lesson_title,
               al.selected_answer
        FROM question q
        JOIN lesson l ON q.lesson_id = l.id
        LEFT JOIN (
            SELECT question_id, selected_answer FROM answer_log
            WHERE student_id = ?
            ORDER BY created_at DESC LIMIT 1
        ) al ON al.question_id = q.id
        WHERE q.id = ?
    ');
    $q_stmt->execute([$student_id, $question_id]);
    $q = $q_stmt->fetch();

    if (!$q) {
        http_response_code(404);
        echo json_encode(['error' => 'Question not found']);
        exit;
    }

    $context  = "Bài học: {$q['lesson_title']}\n";
    $context .= "Câu hỏi: " . strip_for_ai($q['question']) . "\n";
    $context .= "A. " . strip_for_ai($q['option_a']) . "\n";
    $context .= "B. " . strip_for_ai($q['option_b']) . "\n";
    $context .= "C. " . strip_for_ai($q['option_c']) . "\n";
    $context .= "D. " . strip_for_ai($q['option_d']) . "\n";
    if (!empty($q['selected_answer'])) {
        $context .= "Học sinh đã chọn: {$q['selected_answer']}\n";
    }
    if (!empty($q['hint'])) {
        $context .= "Gợi ý: " . strip_for_ai($q['hint']) . "\n";
    }
} else {
    // Mode: chat tự do về bài học (ai_explanation activity)
    $l_stmt = $db->prepare('SELECT title FROM lesson WHERE id = ?');
    $l_stmt->execute([$lesson_id]);
    $lesson_row = $l_stmt->fetch();

    $context = "Bài học: " . ($lesson_row['title'] ?? '') . "\n";
    $context .= "Học sinh đang học về chủ đề này và có câu hỏi tự do.\n";
}

$context .= "\nHọc sinh hỏi: " . $student_msg;

// ── System prompt ───────────────────────────────────────────
$system_prompt = <<<PROMPT
Bạn là giáo viên dạy Toán lớp 7 thân thiện và kiên nhẫn.
Đừng tiết lộ đáp án ngay lập tức.
Hướng dẫn học sinh từng bước một.
Nếu học sinh trả lời sai:
- Giải thích ngắn gọn tại sao sai
- Đặt câu hỏi gợi mở để học sinh tự tìm ra
- Đưa ra một ví dụ tương tự đơn giản hơn
Độ dài tối đa: 150 từ.
Dùng ngôn ngữ đơn giản, phù hợp với học sinh lớp 7.
Trả lời bằng tiếng Việt.
Quy tắc công thức toán học: Dùng \(...\) cho inline — ví dụ: \(\frac{a}{b}\), \(\sqrt{2}\). Dùng \[...\] cho công thức riêng dòng. KHÔNG dùng $ hoặc $$. KHÔNG dùng √, π, ×, x² — hãy dùng \sqrt{}, \pi, \times, x^2.
PROMPT;

// ── Call OpenAI ─────────────────────────────────────────────
$api_key = OPENAI_API_KEY;
if (empty($api_key)) {
    echo json_encode(['response' => 'Chưa cấu hình OpenAI API key.']);
    exit;
}

$payload = json_encode([
    'model'       => 'gpt-4o-mini',
    'max_tokens'  => 300,
    'temperature' => 0.4,
    'messages'    => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user',   'content' => $context],
    ],
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ],
    CURLOPT_TIMEOUT => 30,
]);

$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['response' => 'Không thể kết nối với AI. Vui lòng thử lại.']);
    exit;
}

$data        = json_decode($result, true);
$ai_response = $data['choices'][0]['message']['content'] ?? 'Không nhận được phản hồi.';

// Save to ai_chat
$db->prepare('
    INSERT INTO ai_chat (student_id, lesson_id, question_id, prompt, response)
    VALUES (?, ?, ?, ?, ?)
')->execute([$student_id, $lesson_id, $question_id, $student_msg, $ai_response]);

echo json_encode(['response' => $ai_response]);
