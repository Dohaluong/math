<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

// SPEC-005 §12 — AI Prompt Rules
// Formula-only fields (options, accepted, formulas, value): bare LaTeX, NO delimiters.
// Mixed text+formula fields (question, content, problem, hint, explanation):
//   use \(...\) for inline and \[...\] for display math.
$latex_rule = <<<'RULE'

Quy tắc công thức toán học (SPEC-005 bắt buộc):

FIELD chứa CÔNG THỨC THUẦN (options, accepted, formulas, value, answer):
- Chỉ ghi LaTeX trần, KHÔNG có delimiter nào.
- Đúng: \frac{a}{b}   |   Sai: $\frac{a}{b}$

FIELD chứa VĂN BẢN + CÔNG THỨC (question, content, problem, hint, explanation):
- Bọc công thức inline trong \(...\)
- Đúng: Tính \(\frac{a}{b}\) rồi so sánh   |   Sai: Tính $\frac{a}{b}$ rồi so sánh
- Công thức riêng dòng: \[\frac{a}{b}\]

TUYỆT ĐỐI KHÔNG dùng: $, $$, √, π, ×, ÷, x², Unicode toán học
TUYỆT ĐỐI KHÔNG dùng: \text{sqrt{2}} — hãy viết \sqrt{2}
TUYỆT ĐỐI KHÔNG dùng: \text{pi} — hãy viết \pi

Trong JSON: mỗi backslash phải escape thành \\. Ví dụ: "\\frac{a}{b}", "\\sqrt{2}", "\\pi"
RULE;

$rows = $db->query('SELECT worker, system_prompt FROM ls_prompt ORDER BY worker')->fetchAll();

foreach ($rows as $row) {
    $prompt = $row['system_prompt'];
    // Replace old rule block if present, otherwise append
    if (preg_match('/Quy tắc (công thức|LaTeX)/u', $prompt)) {
        $prompt = preg_replace('/\n*Quy tắc (công thức|LaTeX).*$/su', '', $prompt);
    }
    $prompt .= $latex_rule;
    $db->prepare('UPDATE ls_prompt SET system_prompt = ? WHERE worker = ?')
       ->execute([$prompt, $row['worker']]);
    echo "Updated: {$row['worker']}\n";
}
echo "\nDone — SPEC-005 prompt rules applied to all workers.\n";
