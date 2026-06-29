<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input     = json_decode(file_get_contents('php://input'), true);
$lesson_id = (int)($input['lesson_id'] ?? 0);
if (!$lesson_id) { echo json_encode(['error' => 'Missing lesson_id']); exit; }

$db = get_db();
$db->prepare('DELETE FROM ls_draft WHERE lesson_id = ? AND status IN ("draft","rejected")')->execute([$lesson_id]);
echo json_encode(['ok' => true]);
