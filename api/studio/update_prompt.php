<?php
require_once '../../config.php';
require_once '../../db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id    = (int)($input['id'] ?? 0);
if (!$id) { echo json_encode(['error' => 'Missing id']); exit; }

$db = get_db();
$db->prepare('UPDATE ls_prompt SET system_prompt=?, user_template=?, output_example=?, version=version+1 WHERE id=?')
   ->execute([
       $input['system_prompt']  ?? '',
       $input['user_template']  ?? '',
       $input['output_example'] ?? '',
       $id,
   ]);

$version = $db->prepare('SELECT version FROM ls_prompt WHERE id=?');
$version->execute([$id]);
echo json_encode(['ok' => true, 'version' => (int)$version->fetchColumn()]);
