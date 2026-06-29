<?php
/**
 * Run once: http://localhost/Math/setup/seed_activities_bai3.php
 * Seeds Bài 3 — Nhân, chia số hữu tỉ với Activity Engine
 */
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$db->prepare('DELETE FROM activity WHERE lesson_id = 3')->execute();

$activities = [

    // ─── 1. Introduction ──────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'introduction',
        'title'      => 'Bắt đầu bài học',
        'sort_order' => 1,
        'data'       => [
            'title' => 'Nhân, chia số hữu tỉ',
            'intro' => 'Nhân và chia số hữu tỉ được thực hiện tương tự như nhân và chia phân số — không cần quy đồng mẫu. Bài học này mở rộng kỹ năng đó sang toàn bộ số hữu tỉ, kể cả số âm.',
            'objectives' => [
                'Nắm được quy tắc nhân và chia hai số hữu tỉ',
                'Xác định đúng dấu của tích và thương',
                'Tìm nghịch đảo của một số hữu tỉ',
                'Vận dụng các tính chất của phép nhân để tính nhanh',
            ],
        ],
    ],

    // ─── 2. Theory — Nhân ─────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'theory',
        'title'      => 'Quy tắc nhân, chia số hữu tỉ',
        'sort_order' => 2,
        'data'       => [
            'content' =>
                'Với \(x = \dfrac{a}{b}\) và \(y = \dfrac{c}{d}\) (\(b, d \neq 0\)):<br><br>'
                . '<strong>Nhân:</strong>'
                . '\[x \cdot y = \dfrac{a}{b} \cdot \dfrac{c}{d} = \dfrac{a \cdot c}{b \cdot d}\]'
                . '<strong>Chia</strong> (\(y \neq 0\)):'
                . '\[x \div y = \dfrac{a}{b} \div \dfrac{c}{d} = \dfrac{a}{b} \cdot \dfrac{d}{c} = \dfrac{a \cdot d}{b \cdot c}\]'
                . '<strong>Quy tắc dấu:</strong>'
                . '<ul>'
                . '<li>Cùng dấu (+ × + hoặc − × −) → kết quả <strong>dương</strong></li>'
                . '<li>Khác dấu (+ × − hoặc − × +) → kết quả <strong>âm</strong></li>'
                . '</ul>',
        ],
    ],

    // ─── 3. Theory — Nghịch đảo & tính chất ──────────────
    [
        'lesson_id'  => 3,
        'type'       => 'theory',
        'title'      => 'Nghịch đảo và tính chất phép nhân',
        'sort_order' => 3,
        'data'       => [
            'content' =>
                '<strong>Số nghịch đảo:</strong> Nghịch đảo của \(\dfrac{a}{b}\) là \(\dfrac{b}{a}\) (với \(a \neq 0\)).<br>'
                . 'Tích hai số nghịch đảo bằng 1: \(\dfrac{a}{b} \times \dfrac{b}{a} = 1\)<br><br>'
                . '<strong>Tính chất của phép nhân số hữu tỉ:</strong>'
                . '<ul>'
                . '<li>Giao hoán: \(x \cdot y = y \cdot x\)</li>'
                . '<li>Kết hợp: \((x \cdot y) \cdot z = x \cdot (y \cdot z)\)</li>'
                . '<li>Phân phối: \(x(y + z) = xy + xz\)</li>'
                . '<li>Nhân với 1: \(x \cdot 1 = x\)</li>'
                . '</ul>'
                . '<em>Mẹo:</em> Có thể rút gọn chéo trước khi nhân để tính đơn giản hơn.',
        ],
    ],

    // ─── 4. Example ───────────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'example',
        'title'      => 'Ví dụ nhân và chia số hữu tỉ',
        'sort_order' => 4,
        'data'       => [
            'problem' => 'Tính: (a) \(\dfrac{3}{4} \cdot \dfrac{8}{9}\) và (b) \(\dfrac{-5}{6} \div \dfrac{2}{3}\)',
            'steps'   => [
                [
                    'label'   => 'Phần (a)',
                    'title'   => 'Nhân phân số — có thể rút gọn chéo',
                    'content' =>
                        '\[\dfrac{3}{4} \cdot \dfrac{8}{9} = \dfrac{3 \times 8}{4 \times 9} = \dfrac{24}{36} = \dfrac{2}{3}\]'
                        . 'Hoặc rút gọn trước: \(\dfrac{\not{3}}{{\not{4}}_2} \cdot \dfrac{{\not{8}}^2}{{\not{9}}_3} = \dfrac{2}{3}\)',
                ],
                [
                    'label'   => 'Phần (b)',
                    'title'   => 'Chia = nhân với nghịch đảo',
                    'content' =>
                        'Nghịch đảo của \(\dfrac{2}{3}\) là \(\dfrac{3}{2}\)<br>'
                        . '\[\dfrac{-5}{6} \div \dfrac{2}{3} = \dfrac{-5}{6} \times \dfrac{3}{2} = \dfrac{-5 \times 3}{6 \times 2} = \dfrac{-15}{12} = \dfrac{-5}{4}\]',
                ],
                [
                    'label'   => 'Kiểm tra dấu',
                    'title'   => 'Xác định dấu kết quả',
                    'content' =>
                        'Phần (a): \(+\) × \(+\) = \(+\) → kết quả dương \(\dfrac{2}{3}\) ✓<br>'
                        . 'Phần (b): \(-\) × \(+\) = \(-\) → kết quả âm \(\dfrac{-5}{4}\) ✓',
                ],
            ],
        ],
    ],

    // ─── 5. AI Explanation ────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'ai_explanation',
        'title'      => 'Hỏi AI về nhân chia số hữu tỉ',
        'sort_order' => 5,
        'data'       => [
            'topic'       => 'Nhân, chia số hữu tỉ',
            'suggestions' => [
                'Tại sao chia cho một số lại bằng nhân với nghịch đảo?',
                'Khi nào tích của hai số âm lại là số dương?',
                'Rút gọn chéo trước khi nhân có luôn đúng không?',
            ],
        ],
    ],

    // ─── 6. Quiz 1 ────────────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'quiz',
        'title'      => 'Chia số hữu tỉ',
        'sort_order' => 6,
        'data'       => [
            'question'    => 'Tính \(\dfrac{3}{5} \div \dfrac{9}{10}\)',
            'options'     => [
                '\(\dfrac{27}{50}\)',
                '\(\dfrac{2}{3}\)',
                '\(\dfrac{3}{2}\)',
                '\(\dfrac{1}{2}\)',
            ],
            'answer'      => 1,
            'hint'        => 'Đổi phép chia thành nhân với nghịch đảo: \(\dfrac{3}{5} \times \dfrac{10}{9}\).',
            'explanation' => '\(\dfrac{3}{5} \div \dfrac{9}{10} = \dfrac{3}{5} \times \dfrac{10}{9} = \dfrac{30}{45} = \dfrac{2}{3}\).',
        ],
    ],

    // ─── 7. Quiz 2 ────────────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'quiz',
        'title'      => 'Số nghịch đảo',
        'sort_order' => 7,
        'data'       => [
            'question'    => 'Nghịch đảo của \(\dfrac{-4}{7}\) là:',
            'options'     => [
                '\(\dfrac{4}{7}\)',
                '\(\dfrac{7}{4}\)',
                '\(\dfrac{-7}{4}\)',
                '\(\dfrac{-4}{7}\)',
            ],
            'answer'      => 2,
            'hint'        => 'Nghịch đảo của \(\dfrac{a}{b}\) là \(\dfrac{b}{a}\). Dấu không thay đổi.',
            'explanation' => 'Nghịch đảo của \(\dfrac{-4}{7}\) là \(\dfrac{7}{-4} = \dfrac{-7}{4}\). Kiểm tra: \(\dfrac{-4}{7} \times \dfrac{-7}{4} = 1\) ✓.',
        ],
    ],

    // ─── 8. Guided Practice ───────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'guided_practice',
        'title'      => 'Luyện tập nhân số hữu tỉ',
        'sort_order' => 8,
        'data'       => [
            'problem' => 'Tính \(\dfrac{-5}{6} \times \dfrac{3}{10}\)',
            'steps'   => [
                [
                    'prompt'      => 'Bước 1: Dấu của kết quả là gì? (nhập "dương" hoặc "âm")',
                    'answer'      => 'âm',
                    'accepted'    => ['âm', 'am', '-'],
                    'hint'        => 'Nhìn vào dấu của hai thừa số: −5/6 là âm, 3/10 là dương.',
                    'explanation' => 'Âm × Dương = Âm. Vậy kết quả sẽ mang dấu âm.',
                ],
                [
                    'prompt'      => 'Bước 2: Tích của tử số |(-5) × 3| = ?',
                    'answer'      => '15',
                    'accepted'    => ['15'],
                    'hint'        => 'Chỉ tính trị tuyệt đối: 5 × 3 = ?',
                    'explanation' => '5 × 3 = 15. Vậy tử số (chưa rút gọn) là −15.',
                ],
                [
                    'prompt'      => 'Bước 3: Tích của mẫu số 6 × 10 = ?',
                    'answer'      => '60',
                    'accepted'    => ['60'],
                    'hint'        => '6 × 10 = ?',
                    'explanation' => '6 × 10 = 60. Phân số chưa rút gọn là \(\dfrac{-15}{60}\).',
                ],
                [
                    'prompt'      => 'Bước 4: Rút gọn \(\dfrac{-15}{60}\), kết quả là? (dạng a/b)',
                    'answer'      => '-1/4',
                    'accepted'    => ['-1/4', '−1/4'],
                    'hint'        => 'ƯCLN(15, 60) = 15. Chia cả tử và mẫu cho 15.',
                    'explanation' => '\(\dfrac{-15}{60} = \dfrac{-15 \div 15}{60 \div 15} = \dfrac{-1}{4}\).',
                ],
            ],
        ],
    ],

    // ─── 9. Fill Answer ───────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'fill_answer',
        'title'      => 'Điền đáp án',
        'sort_order' => 9,
        'data'       => [
            'question'    => 'Tính \(\dfrac{3}{4} \times \dfrac{8}{3} \times \dfrac{1}{2}\)',
            'accepted'    => ['1', '1/1'],
            'hint'        => 'Dùng tính chất kết hợp: nhân \(\dfrac{3}{4} \times \dfrac{8}{3}\) trước, rồi nhân tiếp với \(\dfrac{1}{2}\).',
            'explanation' => '\(\dfrac{3}{4} \times \dfrac{8}{3} = \dfrac{24}{12} = 2\), rồi \(2 \times \dfrac{1}{2} = 1\).',
        ],
    ],

    // ─── 10. Quiz 3 ───────────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'quiz',
        'title'      => 'Tìm x',
        'sort_order' => 10,
        'data'       => [
            'question'    => 'Tìm \(x\) biết \(x \times \dfrac{2}{3} = \dfrac{4}{9}\)',
            'options'     => [
                '\(\dfrac{2}{3}\)',
                '\(\dfrac{8}{27}\)',
                '\(\dfrac{6}{9}\)',
                '\(\dfrac{2}{9}\)',
            ],
            'answer'      => 0,
            'hint'        => '\(x = \dfrac{4}{9} \div \dfrac{2}{3}\). Đổi phép chia thành nhân với nghịch đảo.',
            'explanation' => '\(x = \dfrac{4}{9} \div \dfrac{2}{3} = \dfrac{4}{9} \times \dfrac{3}{2} = \dfrac{12}{18} = \dfrac{2}{3}\).',
        ],
    ],

    // ─── 11. Reflection ───────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'reflection',
        'title'      => 'Nhìn lại bài học',
        'sort_order' => 11,
        'data'       => [
            'prompt'  => 'Bạn cảm thấy thế nào về bài nhân chia số hữu tỉ?',
            'options' => [
                '😀 Hiểu rõ, làm bài thuần thục',
                '🤔 Nắm được quy tắc, còn cần luyện thêm',
                '😕 Hay nhầm dấu, cần ôn lại',
                '❓ Chưa hiểu cách chia cho phân số',
            ],
        ],
    ],

    // ─── 12. Summary ──────────────────────────────────────
    [
        'lesson_id'  => 3,
        'type'       => 'summary',
        'title'      => 'Tóm tắt bài học',
        'sort_order' => 12,
        'data'       => [
            'points' => [
                'Nhân số hữu tỉ: nhân tử với tử, mẫu với mẫu; giữ nguyên quy tắc dấu',
                'Chia số hữu tỉ: nhân với nghịch đảo của số chia',
                'Nghịch đảo của \(\dfrac{a}{b}\) là \(\dfrac{b}{a}\) (tích của chúng = 1)',
                'Quy tắc dấu: cùng dấu → dương; khác dấu → âm',
                'Có thể rút gọn chéo trước khi nhân để đơn giản hóa tính toán',
            ],
            'formulas' => [
                '\(\dfrac{a}{b} \times \dfrac{c}{d} = \dfrac{ac}{bd}\) — phép nhân',
                '\(\dfrac{a}{b} \div \dfrac{c}{d} = \dfrac{a}{b} \times \dfrac{d}{c} = \dfrac{ad}{bc}\) — phép chia',
            ],
            'common_mistakes' => [
                'Nhầm nghịch đảo: nghịch đảo của \(\dfrac{-4}{7}\) là \(\dfrac{-7}{4}\), không phải \(\dfrac{7}{4}\)',
                'Quên rút gọn kết quả về phân số tối giản',
                'Nhầm dấu khi nhân hai số âm: âm × âm = dương',
            ],
        ],
    ],

];

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

echo "<h2>✅ Bài 3 seeded: $count activities</h2>";
echo "<p><a href='/Math/pages/lesson.php?id=3'>→ Xem Bài 3</a></p>";
