<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id    = (int)($input['id'] ?? 0);
if (!$id) { echo json_encode(['error' => 'Missing id']); exit; }

$db = get_db();
$sets = []; $params = [];

if (array_key_exists('data', $input)) {
    $sets[]   = 'data = ?';
    $params[] = json_encode($input['data'], JSON_UNESCAPED_UNICODE);
}
if (array_key_exists('title', $input)) {
    $sets[]   = 'title = ?';
    $params[] = $input['title'];
}
if (array_key_exists('notes', $input)) {
    $sets[]   = 'notes = ?';
    $params[] = $input['notes'];
}
if (array_key_exists('sort_order', $input)) {
    $sets[]   = 'sort_order = ?';
    $params[] = (int)$input['sort_order'];
}

if (empty($sets)) { echo json_encode(['error' => 'Nothing to update']); exit; }

$params[] = $id;
$db->prepare('UPDATE ls_draft SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
echo json_encode(['ok' => true]);
