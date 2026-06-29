<?php
/**
 * Run once: http://localhost/Math/setup/seed_activities_bai1.php
 * Seeds Bài 1 — Tập hợp số hữu tỉ với Activity Engine
 */
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$db->prepare('DELETE FROM activity WHERE lesson_id = 1')->execute();

$activities = [

    // ─── 1. Introduction ──────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'introduction',
        'title'      => 'Bắt đầu bài học',
        'sort_order' => 1,
        'data'       => [
            'title' => 'Tập hợp số hữu tỉ',
            'intro' => 'Ở lớp 6, bạn đã biết đến số nguyên và phân số. Bài học này giới thiệu một tập hợp số rộng hơn — tập hợp số hữu tỉ — bao gồm tất cả các số đó.',
            'objectives' => [
                'Hiểu định nghĩa và ký hiệu tập số hữu tỉ \(\mathbb{Q}\)',
                'Nhận biết một số có phải số hữu tỉ hay không',
                'Biểu diễn số hữu tỉ trên trục số',
                'So sánh hai số hữu tỉ bất kỳ',
            ],
        ],
    ],

    // ─── 2. Theory — Định nghĩa ───────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'theory',
        'title'      => 'Định nghĩa số hữu tỉ',
        'sort_order' => 2,
        'data'       => [
            'content' =>
                '<strong>Định nghĩa:</strong> Số hữu tỉ là số viết được dưới dạng \(\dfrac{p}{q}\), trong đó \(p, q \in \mathbb{Z}\) và \(q \neq 0\).<br><br>'
                . 'Tập hợp các số hữu tỉ ký hiệu là \(\mathbb{Q}\).<br><br>'
                . '<strong>Nhận xét quan trọng:</strong>'
                . '<ul>'
                . '<li>Mọi số nguyên \(n\) đều là số hữu tỉ vì \(n = \dfrac{n}{1}\)</li>'
                . '<li>Mọi phân số đều là số hữu tỉ</li>'
                . '<li>Số thập phân hữu hạn và vô hạn tuần hoàn đều là số hữu tỉ</li>'
                . '<li>Quan hệ giữa các tập số: \(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)</li>'
                . '</ul>',
        ],
    ],

    // ─── 3. Theory — So sánh và trục số ──────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'theory',
        'title'      => 'So sánh số hữu tỉ và trục số',
        'sort_order' => 3,
        'data'       => [
            'content' =>
                '<strong>Biểu diễn trên trục số:</strong>'
                . '<ul>'
                . '<li>Số hữu tỉ dương nằm bên <em>phải</em> điểm 0</li>'
                . '<li>Số hữu tỉ âm nằm bên <em>trái</em> điểm 0</li>'
                . '<li>Số nào nằm bên phải thì <strong>lớn hơn</strong></li>'
                . '</ul>'
                . '<strong>So sánh hai số hữu tỉ:</strong> Quy đồng mẫu số dương rồi so sánh tử.<br><br>'
                . '<em>Ví dụ:</em> So sánh \(\dfrac{-2}{3}\) và \(\dfrac{-3}{4}\)<br>'
                . 'BCNN(3,4) = 12: \(\dfrac{-2}{3} = \dfrac{-8}{12}\), \(\dfrac{-3}{4} = \dfrac{-9}{12}\)<br>'
                . 'Vì \(-8 > -9\) nên \(\dfrac{-2}{3} > \dfrac{-3}{4}\)',
        ],
    ],

    // ─── 4. Example ───────────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'example',
        'title'      => 'Ví dụ nhận biết và so sánh',
        'sort_order' => 4,
        'data'       => [
            'problem' => 'Nhận biết số hữu tỉ và so sánh \(\dfrac{-3}{5}\) với \(\dfrac{-4}{7}\)',
            'steps'   => [
                [
                    'label'   => 'Bước 1',
                    'title'   => 'Nhận biết số hữu tỉ',
                    'content' =>
                        '\(\dfrac{3}{4}\) ✓ — phân số, viết được dạng \(\dfrac{p}{q}\)<br>'
                        . '\(-5\) ✓ — vì \(-5 = \dfrac{-5}{1}\)<br>'
                        . '\(2{,}5\) ✓ — vì \(2{,}5 = \dfrac{5}{2}\)<br>'
                        . '\(\sqrt{2}\) ✗ — không viết được dạng \(\dfrac{p}{q}\) với p, q nguyên',
                ],
                [
                    'label'   => 'Bước 2',
                    'title'   => 'Tìm BCNN',
                    'content' => 'So sánh \(\dfrac{-3}{5}\) và \(\dfrac{-4}{7}\): BCNN(5, 7) = 35',
                ],
                [
                    'label'   => 'Bước 3',
                    'title'   => 'Quy đồng mẫu',
                    'content' => '\[\dfrac{-3}{5} = \dfrac{-3 \times 7}{5 \times 7} = \dfrac{-21}{35} \qquad \dfrac{-4}{7} = \dfrac{-4 \times 5}{7 \times 5} = \dfrac{-20}{35}\]',
                ],
                [
                    'label'   => 'Bước 4',
                    'title'   => 'So sánh',
                    'content' => 'Vì \(-21 < -20\) nên \(\dfrac{-21}{35} < \dfrac{-20}{35}\), do đó \(\dfrac{-3}{5} < \dfrac{-4}{7}\)',
                ],
            ],
        ],
    ],

    // ─── 5. AI Explanation ────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'ai_explanation',
        'title'      => 'Hỏi AI về số hữu tỉ',
        'sort_order' => 5,
        'data'       => [
            'topic'       => 'Tập hợp số hữu tỉ',
            'suggestions' => [
                'Tại sao \(\sqrt{2}\) không phải số hữu tỉ?',
                '\(\pi\) có phải số hữu tỉ không? Tại sao?',
                'Làm sao biết số thập phân nào là số hữu tỉ?',
            ],
        ],
    ],

    // ─── 6. Quiz 1 ────────────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'quiz',
        'title'      => 'Nhận biết số hữu tỉ',
        'sort_order' => 6,
        'data'       => [
            'question'    => 'Trong các số sau, số nào <strong>không phải</strong> là số hữu tỉ?',
            'options'     => [
                '\(\dfrac{3}{5}\)',
                '\(-7\)',
                '\(\sqrt{2}\)',
                '\(0{,}25\)',
            ],
            'answer'      => 2,
            'hint'        => 'Số hữu tỉ phải viết được dạng \(\dfrac{p}{q}\) với p, q nguyên, q ≠ 0.',
            'explanation' => '\(\sqrt{2}\) không thể viết được dưới dạng \(\dfrac{p}{q}\) với p, q là số nguyên. Ba số còn lại đều là số hữu tỉ.',
        ],
    ],

    // ─── 7. Quiz 2 ────────────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'quiz',
        'title'      => 'Quan hệ tập hợp số',
        'sort_order' => 7,
        'data'       => [
            'question'    => 'Quan hệ giữa tập \(\mathbb{N}\), \(\mathbb{Z}\), \(\mathbb{Q}\) là:',
            'options'     => [
                '\(\mathbb{Q} \subset \mathbb{Z} \subset \mathbb{N}\)',
                '\(\mathbb{N} \subset \mathbb{Q} \subset \mathbb{Z}\)',
                '\(\mathbb{Z} \subset \mathbb{N} \subset \mathbb{Q}\)',
                '\(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)',
            ],
            'answer'      => 3,
            'hint'        => 'Mọi số tự nhiên đều là số nguyên. Mọi số nguyên đều là số hữu tỉ.',
            'explanation' => '\(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\): Số tự nhiên ⊂ Số nguyên ⊂ Số hữu tỉ. Tập sau luôn chứa tập trước.',
        ],
    ],

    // ─── 8. Guided Practice ───────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'guided_practice',
        'title'      => 'Luyện tập so sánh số hữu tỉ',
        'sort_order' => 8,
        'data'       => [
            'problem' => 'So sánh hai số hữu tỉ: \(\dfrac{-3}{5}\) và \(\dfrac{-4}{7}\)',
            'steps'   => [
                [
                    'prompt'      => 'Bước 1: BCNN(5, 7) = ?',
                    'answer'      => '35',
                    'accepted'    => ['35'],
                    'hint'        => '5 và 7 là hai số nguyên tố cùng nhau, nên BCNN = 5 × 7.',
                    'explanation' => 'BCNN(5, 7) = 35 vì 5 và 7 nguyên tố cùng nhau.',
                ],
                [
                    'prompt'      => 'Bước 2: \(\dfrac{-3}{5}\) quy đồng được \(\dfrac{?}{35}\) (nhập tử số)',
                    'answer'      => '-21',
                    'accepted'    => ['-21'],
                    'hint'        => '\(\dfrac{-3}{5} = \dfrac{-3 \times 7}{5 \times 7}\)',
                    'explanation' => '\(\dfrac{-3}{5} = \dfrac{-21}{35}\) (nhân cả tử và mẫu với 7).',
                ],
                [
                    'prompt'      => 'Bước 3: \(\dfrac{-4}{7}\) quy đồng được \(\dfrac{?}{35}\) (nhập tử số)',
                    'answer'      => '-20',
                    'accepted'    => ['-20'],
                    'hint'        => '\(\dfrac{-4}{7} = \dfrac{-4 \times 5}{7 \times 5}\)',
                    'explanation' => '\(\dfrac{-4}{7} = \dfrac{-20}{35}\) (nhân cả tử và mẫu với 5).',
                ],
                [
                    'prompt'      => 'Bước 4: Vì -21 __ -20 (nhập dấu <, > hoặc =)',
                    'answer'      => '<',
                    'accepted'    => ['<', 'nhỏ hơn'],
                    'hint'        => 'Trên trục số, -21 nằm bên trái -20.',
                    'explanation' => 'Vì -21 < -20 nên \(\dfrac{-3}{5} < \dfrac{-4}{7}\).',
                ],
            ],
        ],
    ],

    // ─── 9. Fill Answer ───────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'fill_answer',
        'title'      => 'Điền đáp án',
        'sort_order' => 9,
        'data'       => [
            'question'    => 'Viết số thập phân \(0{,}75\) dưới dạng phân số tối giản.',
            'accepted'    => ['3/4'],
            'hint'        => '\(0{,}75 = \dfrac{75}{100}\). Rút gọn bằng cách chia cả tử và mẫu cho ƯCLN.',
            'explanation' => '\(0{,}75 = \dfrac{75}{100} = \dfrac{3}{4}\) (chia tử và mẫu cho 25).',
        ],
    ],

    // ─── 10. Quiz 3 ───────────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'quiz',
        'title'      => 'Số hữu tỉ lớn nhất',
        'sort_order' => 10,
        'data'       => [
            'question'    => 'Số hữu tỉ lớn nhất trong các số: \(\dfrac{-1}{2},\; \dfrac{-3}{4},\; \dfrac{-1}{3},\; \dfrac{-2}{5}\) là:',
            'options'     => [
                '\(\dfrac{-1}{2}\)',
                '\(\dfrac{-3}{4}\)',
                '\(\dfrac{-1}{3}\)',
                '\(\dfrac{-2}{5}\)',
            ],
            'answer'      => 2,
            'hint'        => 'Số âm càng gần 0 thì càng lớn. Hãy đổi về số thập phân để so sánh.',
            'explanation' => '\(\dfrac{-1}{3} \approx -0{,}333\) là gần 0 nhất trong bốn số, nên là lớn nhất. Kiểm tra: \(\dfrac{-1}{2}=-0{,}5\), \(\dfrac{-3}{4}=-0{,}75\), \(\dfrac{-2}{5}=-0{,}4\).',
        ],
    ],

    // ─── 11. Reflection ───────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'reflection',
        'title'      => 'Nhìn lại bài học',
        'sort_order' => 11,
        'data'       => [
            'prompt'  => 'Bạn cảm thấy thế nào về bài học hôm nay?',
            'options' => [
                '😀 Hiểu rõ, làm được hết',
                '🤔 Hiểu phần lớn, còn vài chỗ chưa chắc',
                '😕 Khó, cần xem lại lý thuyết',
                '❓ Chưa hiểu, cần giải thích thêm',
            ],
        ],
    ],

    // ─── 12. Summary ──────────────────────────────────────
    [
        'lesson_id'  => 1,
        'type'       => 'summary',
        'title'      => 'Tóm tắt bài học',
        'sort_order' => 12,
        'data'       => [
            'points' => [
                'Số hữu tỉ là số viết được dạng \(\dfrac{p}{q}\) với \(p, q \in \mathbb{Z},\; q \neq 0\)',
                'Tập số hữu tỉ ký hiệu \(\mathbb{Q}\); quan hệ: \(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)',
                'Số hữu tỉ biểu diễn được trên trục số; số nằm phải > số nằm trái',
                'So sánh: quy đồng mẫu dương rồi so sánh tử số',
            ],
            'formulas' => [
                '\(\dfrac{p}{q}\) với \(p, q \in \mathbb{Z},\; q \neq 0\) — định nghĩa số hữu tỉ',
                '\(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\) — quan hệ tập hợp số',
            ],
            'common_mistakes' => [
                'Nhầm \(\sqrt{2}, \sqrt{3}, \pi\) là số hữu tỉ — chúng là số vô tỉ',
                'So sánh số hữu tỉ âm: số âm có trị tuyệt đối nhỏ hơn thì lớn hơn',
                'Quên quy đồng về mẫu dương trước khi so sánh',
            ],
        ],
    ],

];

// Insert all activities
$stmt = $db->prepare('
    INSERT INTO activity (lesson_id, type, title, data, sort_order)
    VALUES (:lesson_id, :type, :title, :data, :sort_order)
');

$count = 0;
foreach ($activities as $act) {
    $stmt->execute([
        ':lesson_id'  => $act['lesson_id'],
        ':type'       => $act['type'],
        ':title'      => $act['title'],
        ':data'       => json_encode($act['data'], JSON_UNESCAPED_UNICODE),
        ':sort_order' => $act['sort_order'],
    ]);
    $count++;
}

echo "<h2>✅ Bài 1 seeded: $count activities</h2>";
echo "<p><a href='/Math/pages/lesson.php?id=1'>→ Xem Bài 1</a></p>";
