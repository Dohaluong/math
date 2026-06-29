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

// Get all activity IDs for this lesson
$act_stmt = $db->prepare('SELECT id FROM activity WHERE lesson_id = ?');
$act_stmt->execute([$lesson_id]);
$act_ids = array_column($act_stmt->fetchAll(), 'id');

if (!empty($act_ids)) {
    $placeholders = implode(',', array_fill(0, count($act_ids), '?'));
    $db->prepare(
        "DELETE FROM activity_progress WHERE student_id = ? AND activity_id IN ($placeholders)"
    )->execute([$student_id, ...$act_ids]);
}

// Clear lesson-level progress
$db->prepare('DELETE FROM progress WHERE student_id = ? AND lesson_id = ?')
   ->execute([$student_id, $lesson_id]);

echo json_encode(['ok' => true]);
