<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

// Restore LaTeX backslashes that were corrupted by JSON \f, \t, \n, \r, \b escape handling.
// chr(12) = form feed = was \frac; chr(9) = tab = was \text/\times;
// chr(10) = newline = could be \neq etc.; chr(13) = CR = \right; chr(8) = BS = \begin
function restoreLatex(mixed $val): mixed {
    if (is_string($val)) {
        // Restore control chars that came from LaTeX commands followed by letters
        $val = preg_replace('/\x0c([a-zA-Z])/', '\\\\f$1', $val); // \frac, \forall, etc.
        $val = preg_replace('/\x09([a-zA-Z])/', '\\\\t$1', $val); // \text, \times, \theta, etc.
        $val = preg_replace('/\x0d([a-zA-Z])/', '\\\\r$1', $val); // \right, \rho, etc.
        $val = preg_replace('/\x08([a-zA-Z])/', '\\\\b$1', $val); // \begin, etc.
        // Note: chr(10) (\n) intentionally skipped — real newlines in text are common
        return $val;
    }
    if (is_array($val)) {
        return array_map('restoreLatex', $val);
    }
    return $val;
}

$rows = $db->query('SELECT id, data FROM ls_draft')->fetchAll();
$fixed = 0;

foreach ($rows as $row) {
    $original = $row['data'];
    $data = json_decode($original, true);
    if (!$data) continue;

    $repaired = restoreLatex($data);
    $new_json  = json_encode($repaired, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($new_json !== json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) {
        $db->prepare('UPDATE ls_draft SET data = ? WHERE id = ?')
           ->execute([$new_json, $row['id']]);
        echo "Fixed draft #{$row['id']}\n";
        $fixed++;
    }
}

echo "\nDone. Fixed $fixed rows.\n";
