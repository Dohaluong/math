<?php
/**
 * Math Normalizer — SPEC-005
 *
 * DB stores bare LaTeX (no delimiters).
 * Renderer adds \(...\) or \[...\] when outputting.
 */

// ── Strip render delimiters from a pure-formula string ─────────────────────
function strip_formula_delimiters(string $s): string {
    $s = trim($s);
    // Legacy: strip letter prefix "A. ", "B) " etc.
    $s = preg_replace('/^[A-D][.):\s]+\s*/u', '', $s);
    // Strip $$...$$
    if (preg_match('/^\$\$(.+)\$\$$/su', $s, $m)) return trim($m[1]);
    // Strip $...$
    if (preg_match('/^\$([^\$]+)\$$/su', $s, $m)) return trim($m[1]);
    // Strip \(...\)
    if (preg_match('/^\\\\\((.+)\\\\\)$/su', $s, $m)) return trim($m[1]);
    // Strip \[...\]
    if (preg_match('/^\\\\\[(.+)\\\\\]$/su', $s, $m)) return trim($m[1]);
    return trim($s);
}

// ── Convert Unicode math symbols → LaTeX ───────────────────────────────────
function unicode_to_latex(string $s): string {
    static $map = [
        'π' => '\pi',   'α' => '\alpha',  'β' => '\beta',   'γ' => '\gamma',
        'δ' => '\delta','θ' => '\theta',  'λ' => '\lambda', 'μ' => '\mu',
        'σ' => '\sigma','ω' => '\omega',  '∞' => '\infty',
        '×' => '\times','÷' => '\div',    '±' => '\pm',
        '≤' => '\leq',  '≥' => '\geq',   '≠' => '\neq',    '≈' => '\approx',
        '∈' => '\in',   '∉' => '\notin', '⊂' => '\subset', '⊃' => '\supset',
        '∑' => '\sum',  '∏' => '\prod',  '∫' => '\int',
        '²' => '^{2}',  '³' => '^{3}',   '¹' => '^{1}',
        '½' => '\frac{1}{2}', '¼' => '\frac{1}{4}', '¾' => '\frac{3}{4}',
    ];
    // √x → \sqrt{x} where x is next char/group
    $s = preg_replace_callback('/√(\{[^}]+\}|[0-9a-zA-Z])/', fn($m) => '\sqrt{' . trim($m[1], '{}') . '}', $s);
    $s = str_replace('√', '\sqrt', $s);  // bare √ with no argument
    return str_replace(array_keys($map), array_values($map), $s);
}

// ── Apply to all string values recursively ─────────────────────────────────
function normalize_all_strings(mixed $val): mixed {
    if (is_string($val)) return unicode_to_latex($val);
    if (is_array($val))  return array_map('normalize_all_strings', $val);
    return $val;
}

// ── Strip delimiters from known pure-formula fields by activity type ────────
function normalize_formula_fields(array $data, string $type): array {
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

// ── Wrap bare LaTeX commands in mixed text fields ──────────────────────────

/**
 * Scan a mixed Vietnamese+LaTeX string and wrap each bare \cmd expression
 * in \(...\). Existing \(...\), \[...\], $...$, $$...$$ are left untouched.
 * Handles nested braces correctly (e.g. \frac{2}{\sqrt{0}}).
 */
function wrap_bare_latex_in_text(string $text): string {
    $result = '';
    $len    = mb_strlen($text, 'UTF-8');
    $i      = 0;

    while ($i < $len) {
        $ch = mb_substr($text, $i, 1, 'UTF-8');

        // --- Existing delimiter: \( or \[ → copy until closing \) or \] ---
        if ($ch === '\\' && $i + 1 < $len) {
            $next = mb_substr($text, $i + 1, 1, 'UTF-8');
            if ($next === '(' || $next === '[') {
                $close = $next === '(' ? '\\)' : '\\]';
                $end   = mb_strpos($text, $close, $i + 2);
                if ($end !== false) {
                    $result .= mb_substr($text, $i, $end + 2 - $i, 'UTF-8');
                    $i = $end + 2;
                    continue;
                }
            }
        }

        // --- Existing delimiter: $$ ---
        if ($ch === '$' && mb_substr($text, $i, 2, 'UTF-8') === '$$') {
            $end = mb_strpos($text, '$$', $i + 2);
            if ($end !== false) {
                $result .= mb_substr($text, $i, $end + 2 - $i, 'UTF-8');
                $i = $end + 2;
                continue;
            }
        }

        // --- Existing delimiter: $ ---
        if ($ch === '$') {
            $end = mb_strpos($text, '$', $i + 1);
            if ($end !== false) {
                $result .= mb_substr($text, $i, $end + 1 - $i, 'UTF-8');
                $i = $end + 1;
                continue;
            }
        }

        // --- Bare LaTeX command: \cmd ---
        if ($ch === '\\' && $i + 1 < $len) {
            $next = mb_substr($text, $i + 1, 1, 'UTF-8');
            if (ctype_alpha($next)) {
                $start = $i;
                $i++;  // skip backslash
                // Consume command name
                while ($i < $len && ctype_alpha(mb_substr($text, $i, 1, 'UTF-8'))) $i++;
                // Consume brace groups with proper depth counting
                while ($i < $len && mb_substr($text, $i, 1, 'UTF-8') === '{') {
                    $depth = 1;
                    $i++;
                    while ($i < $len && $depth > 0) {
                        $c = mb_substr($text, $i, 1, 'UTF-8');
                        if ($c === '{') $depth++;
                        elseif ($c === '}') $depth--;
                        $i++;
                    }
                }
                $result .= '\\(' . mb_substr($text, $start, $i - $start, 'UTF-8') . '\\)';
                continue;
            }
        }

        $result .= $ch;
        $i++;
    }
    return $result;
}

// ── Renderer helpers: wrap bare LaTeX for HTML output ──────────────────────

/** Inline formula: wrap bare LaTeX in \(...\). Plain text returned as-is. */
function render_inline(string $formula): string {
    $formula = trim($formula);
    if ($formula === '') return '';
    // If it already has delimiters (legacy), output as-is
    if (preg_match('/^\$|\\\\\(|\\\\\[/', $formula)) return $formula;
    // Wrap if it looks like LaTeX (has \ commands, ^, _, {})
    if (preg_match('/\\\\[a-zA-Z]|[_^{}]/', $formula)) {
        return '\\(' . $formula . '\\)';
    }
    return htmlspecialchars($formula, ENT_QUOTES);
}

/** Display (block) formula: wrap bare LaTeX in \[...\] */
function render_display(string $formula): string {
    $formula = trim($formula);
    if ($formula === '') return '';
    if (preg_match('/^\$\$|\\\\\[/', $formula)) return $formula;
    if (preg_match('/^\$|\\\\\(/', $formula)) return $formula; // already inline, output as-is
    return '\\[' . $formula . '\\]';
}
