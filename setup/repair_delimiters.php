<?php
/**
 * SPEC-005 Repair Script
 *
 * For each activity row in `activity` and `ls_draft`:
 *  - Strip control chars (chr 0–8, 11–12, 14–31)
 *  - Fix semantic LaTeX errors (\text{sqrt{}} → \sqrt{})
 *  - Fix over-escaped backslashes (\\\\cmd → \\cmd in JSON source)
 *  - Formula-only fields (options, accepted, formulas): strip render delimiters → bare LaTeX
 *  - Text fields with $...$ delimiters: convert to \(...\) for MathJax compatibility
 */

require_once '../config.php';
require_once '../db.php';
require_once '../includes/math_normalizer.php';

$db = get_db();

// ── Helpers ─────────────────────────────────────────────────────────────────

function clean_string(string $s): string {
    // Strip control chars
    $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s);
    // Fix semantic LaTeX: \text{sqrt{x}} → \sqrt{x}
    $s = preg_replace('/\\\\text\{sqrt\{([^}]*)\}\}/', '\\\\sqrt{$1}', $s);
    // Fix \text{pi} → \pi etc.
    $s = preg_replace(
        '/\\\\text\{(pi|alpha|beta|gamma|delta|theta|lambda|mu|nu|sigma|omega|infty|sqrt)\}/',
        '\\\\$1', $s
    );
    // Fix over-escaped: \\\\cmd → \\cmd (in stored JSON source)
    $s = preg_replace('/\\\\\\\\([a-zA-Z(\[)\]])/', '\\\\$1', $s);
    return $s;
}

function walk_clean(mixed $val): mixed {
    if (is_string($val)) return clean_string($val);
    if (is_array($val))  return array_map('walk_clean', $val);
    return $val;
}

/** Convert inline $...$ → \(...\) in text strings (for mixed-content fields) */
function text_delimiters_to_latex(string $s): string {
    // $$...$$ → \[...\]
    $s = preg_replace_callback('/\$\$(.+?)\$\$/su', fn($m) => '\\[' . $m[1] . '\\]', $s);
    // $...$ → \(...\)
    $s = preg_replace_callback('/\$([^\$\n]+)\$/su', fn($m) => '\\(' . $m[1] . '\\)', $s);
    return $s;
}

function walk_text(mixed $val): mixed {
    if (is_string($val)) return text_delimiters_to_latex($val);
    if (is_array($val))  return array_map('walk_text', $val);
    return $val;
}

/** Strip render delimiters from formula-only fields per activity type */
function repair_formula_fields(array $data, string $type): array {
    switch ($type) {
        case 'quiz':
            if (isset($data['options']))
                $data['options'] = array_map('strip_formula_delimiters', $data['options']);
            break;

        case 'fill_answer':
            if (isset($data['accepted']))
                $data['accepted'] = array_map('strip_formula_delimiters', $data['accepted']);
            break;

        case 'summary':
            if (isset($data['formulas']))
                $data['formulas'] = array_map('strip_formula_delimiters', $data['formulas']);
            break;

        case 'guided_practice':
            if (isset($data['steps'])) {
                foreach ($data['steps'] as &$step) {
                    if (isset($step['accepted']))
                        $step['accepted'] = array_map('strip_formula_delimiters', $step['accepted']);
                    if (isset($step['answer']))
                        $step['answer'] = strip_formula_delimiters($step['answer']);
                }
            }
            break;
    }
    return $data;
}

// ── Main repair loop ─────────────────────────────────────────────────────────

$total = 0;
foreach (['activity', 'ls_draft'] as $table) {
    $rows  = $db->query("SELECT id, type, data FROM $table")->fetchAll();
    $stmt  = $db->prepare("UPDATE $table SET data = ? WHERE id = ?");
    $count = 0;

    foreach ($rows as $row) {
        $original = $row['data'];
        $type     = $row['type'] ?? '';

        $data = json_decode($original, true);
        if (!$data) continue;

        // 1. Clean all strings (control chars, semantic LaTeX, backslash)
        $data = walk_clean($data);

        // 2. Convert $...$ → \(...\) in ALL fields (text-safe)
        $data = walk_text($data);

        // 3. Strip delimiters from pure-formula fields (they now become bare LaTeX)
        $data = repair_formula_fields($data, $type);

        $new = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($new !== $original) {
            $stmt->execute([$new, $row['id']]);
            $count++;
        }
    }

    echo "  $table: $count rows updated\n";
    $total += $count;
}

echo "\nTổng: $total rows updated.\n";
echo "SPEC-005 Step 1 repair complete.\n";
