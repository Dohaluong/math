<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id    = (int)($input['id'] ?? 0);
if (!$id) { echo json_encode(['error' => 'Missing id']); exit; }

$db = get_db();
$db->prepare('DELETE FROM ls_draft WHERE id = ?')->execute([$id]);
echo json_encode(['ok' => true]);
