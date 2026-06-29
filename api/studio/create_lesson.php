<?php
require_once '../../config.php';
require_once '../../db.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$input = json_decode(file_get_contents('php://input'), true);

$chapter_no    = (int)($input['chapter_no']    ?? 0);
$chapter_title = trim($input['chapter_title']  ?? '');
$lesson_no     = isset($input['lesson_no']) && $input['lesson_no'] !== null
                 ? (int)$input['lesson_no'] : null;
$title         = trim($input['title']          ?? '');
$blueprint_id  = isset($input['blueprint_id']) && $input['blueprint_id'] !== null
                 ? (int)$input['blueprint_id'] : null;
$concept_id    = isset($input['concept_id']) && $input['concept_id'] !== null
                 ? (int)$input['concept_id'] : null;

if (!$chapter_no || !$chapter_title || !$title) {
    echo json_encode(['error' => 'Thiếu thông tin bắt buộc']); exit;
}

$db = get_db();

// Auto-assign lesson_no if not provided: max existing in chapter + 1
if (!$lesson_no) {
    $max = $db->prepare('SELECT COALESCE(MAX(lesson_no), 0) FROM lesson WHERE chapter_no = ?');
    $max->execute([$chapter_no]);
    $lesson_no = (int)$max->fetchColumn() + 1;
}

// Check duplicate
$dup = $db->prepare('SELECT id FROM lesson WHERE chapter_no = ? AND lesson_no = ?');
$dup->execute([$chapter_no, $lesson_no]);
if ($dup->fetch()) {
    echo json_encode(['error' => "Chương $chapter_no Bài $lesson_no đã tồn tại"]); exit;
}

// Insert lesson
$db->prepare('INSERT INTO lesson (chapter_no, chapter_title, lesson_no, title) VALUES (?,?,?,?)')
   ->execute([$chapter_no, $chapter_title, $lesson_no, $title]);

$lesson_id = (int)$db->lastInsertId();

// Link to curriculum concept if provided
if ($concept_id) {
    $db->prepare('UPDATE mc_concept SET lesson_id = ?, status = "outline" WHERE id = ? AND lesson_id IS NULL')
       ->execute([$lesson_id, $concept_id]);
}

// If blueprint selected, create draft outline immediately
if ($blueprint_id) {
    $bps = $db->prepare('SELECT * FROM ls_blueprint WHERE id = ?');
    $bps->execute([$blueprint_id]);
    $bp = $bps->fetch();

    if ($bp) {
        $structure = json_decode($bp['structure'], true);
        $stmt = $db->prepare('INSERT INTO ls_draft (lesson_id, type, title, data, sort_order) VALUES (?,?,?,?,?)');
        foreach ($structure as $i => $item) {
            $stmt->execute([
                $lesson_id,
                $item['type']  ?? 'theory',
                $item['title'] ?? '',
                '{}',
                ($i + 1) * 10,
            ]);
        }
    }
}

echo json_encode(['ok' => true, 'lesson_id' => $lesson_id]);
