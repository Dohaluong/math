<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$lesson_id  = (int)($input['lesson_id']  ?? 0);
$type       = $input['type']  ?? '';
$title      = $input['title'] ?? '';
$data       = $input['data']  ?? [];
if (!$lesson_id || !$type) { echo json_encode(['error' => 'Missing params']); exit; }

$db = get_db();

// Auto-assign sort_order: after the current last draft for this lesson
$max_stmt = $db->prepare('SELECT MAX(sort_order) FROM ls_draft WHERE lesson_id = ?');
$max_stmt->execute([$lesson_id]);
$sort_order = ((int)$max_stmt->fetchColumn() ?: 0) + 10;

$db->prepare('INSERT INTO ls_draft (lesson_id, type, title, data, sort_order) VALUES (?,?,?,?,?)')
   ->execute([$lesson_id, $type, $title, json_encode($data, JSON_UNESCAPED_UNICODE), $sort_order]);

echo json_encode(['ok' => true, 'id' => (int)$db->lastInsertId()]);
