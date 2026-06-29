<?php
require_once '../../config.php';
require_once '../../db.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$input     = json_decode(file_get_contents('php://input'), true);
$lesson_id = (int)($input['lesson_id'] ?? 0);
$student_id = STUDENT_ID;

if (!$lesson_id) { echo json_encode(['error' => 'Missing lesson_id']); exit; }

$db = get_db();

// Only quiz, fill_answer, guided_practice count toward score
$scored_types      = ['quiz', 'fill_answer', 'guided_practice'];
$type_placeholders = implode(',', array_fill(0, count($scored_types), '?'));

$acts = $db->prepare(
    "SELECT id FROM activity WHERE lesson_id = ? AND type IN ($type_placeholders) ORDER BY sort_order"
);
$acts->execute([$lesson_id, ...$scored_types]);
$scored_ids = array_column($acts->fetchAll(), 'id');
$total = count($scored_ids);

if ($total === 0) {
    $db->prepare('
        INSERT INTO progress (student_id, lesson_id, score, total, last_learning)
        VALUES (?, ?, 0, 0, NOW())
        ON DUPLICATE KEY UPDATE score=0, total=0, last_learning=NOW()
    ')->execute([$student_id, $lesson_id]);
    echo json_encode(['ok' => true, 'score' => 0, 'total' => 0]);
    exit;
}

$placeholders = implode(',', array_fill(0, $total, '?'));
$prog = $db->prepare(
    "SELECT SUM(is_correct = 1) as correct
     FROM activity_progress
     WHERE student_id = ? AND activity_id IN ($placeholders) AND is_complete = 1"
);
$prog->execute([$student_id, ...$scored_ids]);
$row   = $prog->fetch();
$score = (int)($row['correct'] ?? 0);

// Upsert progress row
$db->prepare('
    INSERT INTO progress (student_id, lesson_id, score, total, last_learning)
    VALUES (?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        score        = VALUES(score),
        total        = VALUES(total),
        last_learning = NOW()
')->execute([$student_id, $lesson_id, $score, $total]);

echo json_encode(['ok' => true, 'score' => $score, 'total' => $total]);
