<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$id     = (int)($input['id'] ?? 0);
$status = $input['status'] ?? '';

$allowed = ['draft', 'approved', 'rejected'];
if (!$id || !in_array($status, $allowed)) {
    echo json_encode(['error' => 'Invalid params']); exit;
}

$db = get_db();
$db->prepare('UPDATE ls_draft SET status = ? WHERE id = ?')->execute([$status, $id]);
echo json_encode(['ok' => true]);
