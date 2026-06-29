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

$lesson_id   = (int)($input['lesson_id']  ?? 0);
$answers     = $input['answers']           ?? [];
$study_time  = (int)($input['study_time'] ?? 0);
$student_id  = STUDENT_ID;

if (!$lesson_id || empty($answers)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$db = get_db();

try {
    $db->beginTransaction();

    $insert = $db->prepare('
        INSERT INTO answer_log (student_id, question_id, selected_answer, is_correct)
        VALUES (?, ?, ?, ?)
    ');

    $score = 0;
    foreach ($answers as $ans) {
        $q_id      = (int)$ans['question_id'];
        $selected  = strtoupper(substr(trim($ans['selected_answer']), 0, 1));
        $is_correct = (int)$ans['is_correct'];
        if (!in_array($selected, ['A','B','C','D'])) continue;
        $insert->execute([$student_id, $q_id, $selected, $is_correct]);
        $score += $is_correct;
    }

    $total = count($answers);

    // Upsert progress
    $upsert = $db->prepare('
        INSERT INTO progress (student_id, lesson_id, score, total, study_time)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            score = VALUES(score),
            total = VALUES(total),
            study_time = VALUES(study_time),
            last_learning = CURRENT_TIMESTAMP
    ');
    $upsert->execute([$student_id, $lesson_id, $score, $total, $study_time]);

    $db->commit();

    echo json_encode(['success' => true, 'score' => $score, 'total' => $total]);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
