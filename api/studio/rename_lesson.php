<?php
require_once '../../config.php';
require_once '../../db.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$id    = (int)($input['id']    ?? 0);
$title = trim((string)($input['title'] ?? ''));

if (!$id || $title === '') {
    echo json_encode(['error' => 'Thiếu id hoặc title']); exit;
}

$db = get_db();
$db->prepare('UPDATE lesson SET title = ? WHERE id = ?')->execute([$title, $id]);
echo json_encode(['ok' => true, 'title' => $title]);
