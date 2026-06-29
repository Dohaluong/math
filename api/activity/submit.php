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

$activity_id = (int)($input['activity_id'] ?? 0);
$is_complete = (int)($input['is_complete'] ?? 1);
$response    = $input['response']   ?? null;
$is_correct  = isset($input['is_correct']) ? (int)$input['is_correct'] : null;
$student_id  = STUDENT_ID;

if (!$activity_id) {
    http_response_code(400); echo json_encode(['error'=>'Missing activity_id']); exit;
}

$db = get_db();

$db->prepare('
    INSERT INTO activity_progress (student_id, activity_id, is_complete, response, is_correct, attempts, completed_at)
    VALUES (?, ?, ?, ?, ?, 1, NOW())
    ON DUPLICATE KEY UPDATE
        is_complete  = VALUES(is_complete),
        response     = VALUES(response),
        is_correct   = VALUES(is_correct),
        attempts     = attempts + 1,
        completed_at = IF(VALUES(is_complete)=1, NOW(), completed_at)
')->execute([
    $student_id,
    $activity_id,
    $is_complete,
    $response !== null ? json_encode($response) : null,
    $is_correct,
]);

echo json_encode(['success' => true]);
