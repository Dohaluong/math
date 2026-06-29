<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input     = json_decode(file_get_contents('php://input'), true);
$lesson_id = (int)($input['lesson_id'] ?? 0);
if (!$lesson_id) { echo json_encode(['error' => 'Missing lesson_id']); exit; }

$db = get_db();

// Get all approved + previously-published drafts for this lesson
$drafts = $db->prepare('SELECT * FROM ls_draft WHERE lesson_id = ? AND status IN ("approved","published") ORDER BY sort_order, id');
$drafts->execute([$lesson_id]);
$drafts = $drafts->fetchAll();

if (empty($drafts)) {
    echo json_encode(['error' => 'Không có draft nào để publish cho bài học này']); exit;
}

// Replace live activities
$db->prepare('DELETE FROM activity WHERE lesson_id = ?')->execute([$lesson_id]);

$stmt = $db->prepare('INSERT INTO activity (lesson_id, type, title, data, sort_order) VALUES (?,?,?,?,?)');
$count = 0;
foreach ($drafts as $i => $d) {
    $stmt->execute([$lesson_id, $d['type'], $d['title'], $d['data'], ($i + 1) * 10]);
    $count++;
}

// Mark drafts as published
$db->prepare('UPDATE ls_draft SET status = "published" WHERE lesson_id = ? AND status = "approved"')
   ->execute([$lesson_id]);

// Log
$db->prepare('INSERT INTO ls_generation_log (lesson_id, worker) VALUES (?, "publish")')
   ->execute([$lesson_id]);

echo json_encode(['ok' => true, 'count' => $count]);
