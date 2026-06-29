<?php
require_once '../config.php';
require_once '../db.php';
$db = get_db();

foreach ([100, 165] as $actId) {
    $row = $db->query("SELECT data FROM activity WHERE id=$actId")->fetch();
    if (!$row) { echo "id $actId not found\n"; continue; }
    $data = json_decode($row['data'], true);
    $opts = $data['options'] ?? [];
    echo "=== Activity $actId options (after json_decode) ===\n";
    foreach ($opts as $i => $o) {
        $bs = substr_count($o, '\\');
        echo "  [$i] backslashes=$bs : $o\n";
    }
    echo "\n";
}
