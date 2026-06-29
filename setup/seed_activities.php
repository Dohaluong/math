<?php
/**
 * Run once: http://localhost/Math/setup/seed_activities.php
 * Seeds Bài 2 — Cộng, trừ số hữu tỉ với Activity Engine
 */
require_once '../config.php';
require_once '../db.php';

$db = get_db();

// Clear existing activities for lesson 2
$db->prepare('DELETE FROM activity WHERE lesson_id = 2')->execute();

$activities = [

    // ─── 1. Introduction ──────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'introduction',
        'title'      => 'Bắt đầu bài học',
        'sort_order' => 1,
        'data'       => [
            'title' => 'Cộng, trừ số hữu tỉ',
            'objectives' => [
                'Nắm được quy tắc cộng và trừ hai số hữu tỉ',
                'Thực hiện được phép tính cộng, trừ trên số hữu tỉ bất kỳ',
                'Vận dụng tính chất giao hoán và kết hợp để tính nhanh',
            ],
            'intro' => 'Ở lớp 6, bạn đã biết cộng và trừ phân số. Bài học này mở rộng kỹ năng đó sang toàn bộ số hữu tỉ — bao gồm cả số nguyên, phân số âm và hỗn số.',
        ],
    ],

    // ─── 2. Theory ────────────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'theory',
        'title'      => 'Quy tắc cộng, trừ số hữu tỉ',
        'sort_order' => 2,
        'data'       => [
            'content' =>
                '<strong>Quy tắc:</strong> Viết các số hữu tỉ dưới dạng phân số có cùng mẫu dương, rồi cộng hoặc trừ như phân số thông thường.<br><br>'
                . '<strong>Khi cùng mẫu:</strong>'
                . '\[\frac{a}{m} + \frac{b}{m} = \frac{a+b}{m} \qquad \frac{a}{m} - \frac{b}{m} = \frac{a-b}{m}\]'
                . '<strong>Khi khác mẫu:</strong> Quy đồng mẫu số (tìm BCNN) trước, rồi cộng hoặc trừ.<br><br>'
                . '<strong>Tính chất:</strong>'
                . '<ul>'
                . '<li>Giao hoán: \(x + y = y + x\)</li>'
                . '<li>Kết hợp: \((x + y) + z = x + (y + z)\)</li>'
                . '<li>Trừ = cộng số đối: \(x - y = x + (-y)\)</li>'
                . '</ul>',
        ],
    ],

    // ─── 3. Example (step-by-step) ───────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'example',
        'title'      => 'Ví dụ từng bước',
        'sort_order' => 3,
        'data'       => [
            'problem' => 'Tính \(\dfrac{3}{4} + \dfrac{1}{6}\)',
            'steps'   => [
                [
                    'label'   => 'Bước 1',
                    'title'   => 'Tìm BCNN',
                    'content' => 'BCNN(4, 6) = 12',
                ],
                [
                    'label'   => 'Bước 2',
                    'title'   => 'Quy đồng mẫu số',
                    'content' => '\[\frac{3}{4} = \frac{3 \times 3}{4 \times 3} = \frac{9}{12} \qquad \frac{1}{6} = \frac{1 \times 2}{6 \times 2} = \frac{2}{12}\]',
                ],
                [
                    'label'   => 'Bước 3',
                    'title'   => 'Cộng tử số',
                    'content' => '\[\frac{9}{12} + \frac{2}{12} = \frac{9 + 2}{12} = \frac{11}{12}\]',
                ],
            ],
        ],
    ],

    // ─── 4. AI Explanation ───────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'ai_explanation',
        'title'      => 'Hỏi AI',
        'sort_order' => 4,
        'data'       => [
            'topic'       => 'Cộng, trừ số hữu tỉ',
            'suggestions' => [
                'Tại sao phải quy đồng mẫu trước khi cộng?',
                'Tại sao không thể cộng thẳng tử với tử và mẫu với mẫu?',
                'Khi nào thì kết quả là số nguyên?',
            ],
        ],
    ],

    // ─── 5. Quiz checkpoint ──────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'quiz',
        'title'      => 'Kiểm tra nhanh',
        'sort_order' => 5,
        'data'       => [
            'question'    => 'Tính \(\dfrac{1}{3} + \dfrac{1}{4}\)',
            'options'     => ['\(\dfrac{7}{12}\)', '\(\dfrac{2}{7}\)', '\(\dfrac{5}{12}\)', '\(\dfrac{1}{6}\)'],
            'answer'      => 0,
            'hint'        => 'BCNN(3, 4) = 12. Quy đồng rồi cộng tử số.',
            'explanation' => 'BCNN(3,4) = 12. \(\dfrac{1}{3} = \dfrac{4}{12}\), \(\dfrac{1}{4} = \dfrac{3}{12}\). Tổng: \(\dfrac{7}{12}\).',
        ],
    ],

    // ─── 6. Guided Practice ──────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'guided_practice',
        'title'      => 'Luyện tập có hướng dẫn',
        'sort_order' => 6,
        'data'       => [
            'problem' => 'Tính \(\dfrac{-5}{6} - \dfrac{1}{4}\)',
            'steps'   => [
                [
                    'prompt'      => 'BCNN(6, 4) bằng bao nhiêu?',
                    'answer'      => '12',
                    'accepted'    => ['12'],
                    'hint'        => 'Bội chung nhỏ nhất của 6 và 4: 6 = 2×3, 4 = 2². BCNN = 2²×3 = 12.',
                    'explanation' => 'BCNN(6, 4) = 12.',
                ],
                [
                    'prompt'      => 'Viết \(\dfrac{-5}{6}\) với mẫu 12:',
                    'answer'      => '-10/12',
                    'accepted'    => ['-10/12', '−10/12'],
                    'hint'        => '\(\dfrac{-5}{6} = \dfrac{-5 \times 2}{6 \times 2}\)',
                    'explanation' => '\(\dfrac{-5}{6} = \dfrac{-10}{12}\).',
                ],
                [
                    'prompt'      => 'Viết \(\dfrac{1}{4}\) với mẫu 12:',
                    'answer'      => '3/12',
                    'accepted'    => ['3/12'],
                    'hint'        => '\(\dfrac{1}{4} = \dfrac{1 \times 3}{4 \times 3}\)',
                    'explanation' => '\(\dfrac{1}{4} = \dfrac{3}{12}\).',
                ],
                [
                    'prompt'      => 'Vậy kết quả là:',
                    'answer'      => '-13/12',
                    'accepted'    => ['-13/12', '−13/12'],
                    'hint'        => '\(\dfrac{-10}{12} - \dfrac{3}{12} = \dfrac{-10 - 3}{12}\)',
                    'explanation' => '\(\dfrac{-10}{12} - \dfrac{3}{12} = \dfrac{-13}{12}\).',
                ],
            ],
        ],
    ],

    // ─── 7. Fill Answer ──────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'fill_answer',
        'title'      => 'Điền đáp án',
        'sort_order' => 7,
        'data'       => [
            'question'    => 'Tính \(0{,}5 + \dfrac{1}{4}\) = ?',
            'accepted'    => ['3/4', '0.75', '0,75'],
            'hint'        => '\(0{,}5 = \dfrac{1}{2}\). Hãy quy đồng rồi tính.',
            'explanation' => '\(0{,}5 = \dfrac{1}{2} = \dfrac{2}{4}\). Vậy \(\dfrac{2}{4} + \dfrac{1}{4} = \dfrac{3}{4}\).',
        ],
    ],

    // ─── 8. Quiz 2 ───────────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'quiz',
        'title'      => 'Kiểm tra nhanh 2',
        'sort_order' => 8,
        'data'       => [
            'question'    => 'Kết quả của \(\dfrac{2}{3} - \dfrac{-1}{6}\) là:',
            'options'     => ['\(\dfrac{1}{2}\)', '\(\dfrac{5}{6}\)', '\(\dfrac{1}{6}\)', '\(\dfrac{1}{3}\)'],
            'answer'      => 1,
            'hint'        => 'Trừ số âm bằng cộng số dương: \(\dfrac{2}{3} - \dfrac{-1}{6} = \dfrac{2}{3} + \dfrac{1}{6}\)',
            'explanation' => '\(\dfrac{2}{3} - \dfrac{-1}{6} = \dfrac{2}{3} + \dfrac{1}{6} = \dfrac{4}{6} + \dfrac{1}{6} = \dfrac{5}{6}\).',
        ],
    ],

    // ─── 9. Reflection ───────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'reflection',
        'title'      => 'Nhìn lại bài học',
        'sort_order' => 9,
        'data'       => [
            'prompt'  => 'Bạn thấy phần nào khó nhất trong bài học này?',
            'options' => ['Tìm BCNN', 'Quy đồng mẫu số', 'Cộng/trừ tử số', 'Rút gọn kết quả'],
        ],
    ],

    // ─── 10. Summary ─────────────────────────────────────
    [
        'lesson_id'  => 2,
        'type'       => 'summary',
        'title'      => 'Tóm tắt bài học',
        'sort_order' => 10,
        'data'       => [
            'points' => [
                'Cùng mẫu: cộng/trừ tử số, giữ nguyên mẫu',
                'Khác mẫu: tìm BCNN → quy đồng → cộng/trừ tử',
                'Trừ \(y\) bằng cộng số đối: \(x - y = x + (-y)\)',
                'Dùng tính chất giao hoán và kết hợp để tính nhanh',
            ],
            'formulas' => [
                '\[\frac{a}{m} \pm \frac{b}{m} = \frac{a \pm b}{m}\]',
            ],
            'common_mistakes' => [
                'Cộng cả tử và mẫu: \(\dfrac{1}{3} + \dfrac{1}{4} \neq \dfrac{2}{7}\) ✗',
                'Quên đổi dấu khi trừ số âm',
            ],
        ],
    ],
];

$stmt = $db->prepare('
    INSERT INTO activity (lesson_id, type, title, data, sort_order)
    VALUES (:lesson_id, :type, :title, :data, :sort_order)
');

foreach ($activities as $act) {
    $stmt->execute([
        ':lesson_id'  => $act['lesson_id'],
        ':type'       => $act['type'],
        ':title'      => $act['title'],
        ':data'       => json_encode($act['data'], JSON_UNESCAPED_UNICODE),
        ':sort_order' => $act['sort_order'],
    ]);
}

$count = $db->query('SELECT COUNT(*) FROM activity WHERE lesson_id = 2')->fetchColumn();
echo "<p style='font-family:monospace;color:green'>✓ Đã tạo $count activities cho Bài 2 — Cộng, trừ số hữu tỉ.</p>";
echo "<p><a href='/Math/pages/lesson.php?id=2'>→ Xem bài học</a></p>";
