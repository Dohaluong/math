<?php
/**
 * Run once: http://localhost/Math/setup/seed_studio_prompts.php
 * Seeds default AI worker prompts and blueprints for Learning Studio
 */
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$db->exec('DELETE FROM ls_prompt');
$db->exec('DELETE FROM ls_blueprint');

// ─── AI Worker Prompts ────────────────────────────────────────────
$system_base = 'Bạn là chuyên gia thiết kế nội dung học Toán lớp 7 theo chương trình "Kết nối tri thức" của Việt Nam. Bạn chỉ trả về JSON hợp lệ, không kèm giải thích hay markdown code block.';

$prompts = [

    // Outline Worker
    [
        'worker'  => 'outline',
        'name'    => 'Outline Worker — Tạo danh sách hoạt động',
        'system'  => $system_base,
        'user'    => 'Tạo outline cho bài học sau:
Bài: {{lesson_title}}
Chương: {{chapter_title}}
Môn: Toán lớp 7

Tạo 8-10 hoạt động theo cấu trúc hợp lý. Các type hợp lệ:
introduction, theory, example, ai_explanation, quiz, guided_practice, fill_answer, reflection, summary

Trả về JSON:
{"outline": [{"type": "introduction", "title": "Mở đầu"}, ...]}

CHỈ trả về JSON.',
        'example' => '{"outline":[{"type":"introduction","title":"Mở đầu"},{"type":"theory","title":"Định nghĩa và quy tắc"},{"type":"example","title":"Ví dụ minh họa"},{"type":"quiz","title":"Kiểm tra nhanh 1"},{"type":"guided_practice","title":"Luyện tập có hướng dẫn"},{"type":"fill_answer","title":"Điền đáp án"},{"type":"quiz","title":"Kiểm tra nhanh 2"},{"type":"reflection","title":"Nhìn lại"},{"type":"summary","title":"Tóm tắt"}]}',
    ],

    // Introduction Worker
    [
        'worker'  => 'introduction',
        'name'    => 'Introduction Worker — Tạo màn mở đầu',
        'system'  => $system_base,
        'user'    => 'Tạo nội dung "Mở đầu" cho bài học:
Bài: {{lesson_title}}
Chương: {{chapter_title}}
Tiêu đề hoạt động: {{activity_title}}

Trả về JSON:
{
  "title": "tên bài học",
  "intro": "1-2 câu dẫn dắt ngắn gọn cho học sinh lớp 7",
  "objectives": ["mục tiêu 1", "mục tiêu 2", "mục tiêu 3", "mục tiêu 4"]
}

Objectives viết dạng "Học sinh sẽ...". CHỈ trả về JSON.',
        'example' => '{"title":"Cộng, trừ số hữu tỉ","intro":"Ở lớp 6, bạn đã biết cộng và trừ phân số. Bài học này mở rộng kỹ năng đó sang toàn bộ số hữu tỉ.","objectives":["Nắm được quy tắc cộng và trừ hai số hữu tỉ","Thực hiện được phép tính trên số hữu tỉ bất kỳ","Vận dụng tính chất giao hoán và kết hợp","Giải được bài toán thực tế liên quan"]}',
    ],

    // Theory Worker
    [
        'worker'  => 'theory',
        'name'    => 'Theory Worker — Tạo nội dung lý thuyết',
        'system'  => $system_base,
        'user'    => 'Tạo nội dung lý thuyết cho hoạt động:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}

Yêu cầu:
- Viết tiếng Việt, phù hợp học sinh lớp 7
- Dùng LaTeX: \\(...\\) cho inline, \\[...\\] cho block
- HTML tags được phép: <strong>, <ul>, <li>, <br>, <em>
- Ngắn gọn, rõ ràng, có định nghĩa + ví dụ nhỏ

Trả về JSON:
{"content": "nội dung HTML với LaTeX"}

CHỈ trả về JSON.',
        'example' => '{"content":"<strong>Quy tắc:</strong> Viết các số hữu tỉ dưới dạng phân số có cùng mẫu dương.<br><br><strong>Khi cùng mẫu:</strong>\\[\\frac{a}{m} + \\frac{b}{m} = \\frac{a+b}{m}\\]"}',
    ],

    // Example Worker
    [
        'worker'  => 'example',
        'name'    => 'Example Worker — Tạo ví dụ từng bước',
        'system'  => $system_base,
        'user'    => 'Tạo ví dụ minh họa từng bước cho bài:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}
Ngữ cảnh lý thuyết: {{theory_summary}}

Tạo một bài toán cụ thể và giải theo từng bước (3-4 bước).
Dùng LaTeX: \\(...\\) inline, \\[...\\] block.

Trả về JSON:
{
  "problem": "Đề bài với LaTeX",
  "steps": [
    {"label": "Bước 1", "title": "tên bước", "content": "nội dung HTML+LaTeX"},
    ...
  ]
}

CHỈ trả về JSON.',
        'example' => '{"problem":"Tính \\(\\dfrac{3}{4} + \\dfrac{1}{6}\\)","steps":[{"label":"Bước 1","title":"Tìm BCNN","content":"BCNN(4, 6) = 12"},{"label":"Bước 2","title":"Quy đồng mẫu","content":"\\[\\frac{3}{4}=\\frac{9}{12},\\quad \\frac{1}{6}=\\frac{2}{12}\\]"},{"label":"Bước 3","title":"Tính tổng","content":"\\[\\frac{9}{12}+\\frac{2}{12}=\\frac{11}{12}\\]"}]}',
    ],

    // Quiz Worker
    [
        'worker'  => 'quiz',
        'name'    => 'Quiz Worker — Tạo câu hỏi trắc nghiệm',
        'system'  => $system_base,
        'user'    => 'Tạo một câu hỏi trắc nghiệm 4 đáp án cho bài:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}
Ngữ cảnh: {{theory_summary}}

Yêu cầu:
- 1 câu hỏi rõ ràng, chính xác về mặt toán học
- 4 đáp án (A, B, C, D): chỉ 1 đúng, 3 sai nhưng có vẻ hợp lý (không quá dễ đoán)
- Có gợi ý và giải thích
- answer là chỉ số 0-3 (A=0, B=1, C=2, D=3)
- Dùng LaTeX: \\(...\\) inline

Trả về JSON:
{
  "question": "câu hỏi",
  "options": ["A", "B", "C", "D"],
  "answer": 0,
  "hint": "gợi ý ngắn",
  "explanation": "giải thích đầy đủ"
}

CHỈ trả về JSON.',
        'example' => '{"question":"Tính \\(\\dfrac{1}{3}+\\dfrac{1}{4}\\)","options":["\\(\\dfrac{7}{12}\\)","\\(\\dfrac{2}{7}\\)","\\(\\dfrac{5}{12}\\)","\\(\\dfrac{1}{6}\\)"],"answer":0,"hint":"BCNN(3,4)=12. Quy đồng rồi cộng tử.","explanation":"BCNN=12: \\(\\dfrac{4}{12}+\\dfrac{3}{12}=\\dfrac{7}{12}\\)."}',
    ],

    // Guided Practice Worker
    [
        'worker'  => 'guided_practice',
        'name'    => 'Guided Practice Worker — Tạo bài luyện tập có hướng dẫn',
        'system'  => $system_base,
        'user'    => 'Tạo bài luyện tập có hướng dẫn từng bước cho bài:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}
Ngữ cảnh: {{theory_summary}}

Tạo một bài toán và chia thành 3-4 bước nhỏ để học sinh điền kết quả từng bước.
Mỗi bước: câu hỏi ngắn → đáp án cụ thể học sinh cần nhập.
accepted: mảng các đáp án chấp nhận được (string).

Trả về JSON:
{
  "problem": "đề bài",
  "steps": [
    {
      "prompt": "câu hỏi bước này",
      "answer": "đáp án chuẩn",
      "accepted": ["đáp án 1", "đáp án 2"],
      "hint": "gợi ý",
      "explanation": "giải thích"
    }
  ]
}

CHỈ trả về JSON.',
        'example' => '{"problem":"Tính \\(\\dfrac{-5}{6}-\\dfrac{1}{4}\\)","steps":[{"prompt":"BCNN(6,4)=?","answer":"12","accepted":["12"],"hint":"6=2×3, 4=2². BCNN=2²×3=12.","explanation":"BCNN(6,4)=12."},{"prompt":"\\(\\dfrac{-5}{6}\\) quy đồng được tử số?","answer":"-10","accepted":["-10"],"hint":"Nhân tử và mẫu với 2.","explanation":"\\(\\dfrac{-10}{12}\\)."}]}',
    ],

    // Fill Answer Worker
    [
        'worker'  => 'fill_answer',
        'name'    => 'Fill Answer Worker — Tạo câu điền đáp án',
        'system'  => $system_base,
        'user'    => 'Tạo câu hỏi điền đáp án cho bài:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}

Tạo 1 câu toán có đáp án ngắn gọn (số, phân số, biểu thức đơn giản).
accepted: mảng các cách viết đáp án chấp nhận (ví dụ: ["3/4", "0.75"]).

Trả về JSON:
{
  "question": "câu hỏi với LaTeX",
  "accepted": ["đáp án 1", "đáp án 2"],
  "hint": "gợi ý",
  "explanation": "giải thích"
}

CHỈ trả về JSON.',
        'example' => '{"question":"Rút gọn phân số \\(\\dfrac{12}{18}\\)","accepted":["2/3"],"hint":"ƯCLN(12,18)=6. Chia cả tử và mẫu cho 6.","explanation":"\\(\\dfrac{12}{18}=\\dfrac{12\\div6}{18\\div6}=\\dfrac{2}{3}\\)."}',
    ],

    // Reflection Worker
    [
        'worker'  => 'reflection',
        'name'    => 'Reflection Worker — Tạo câu hỏi nhìn lại',
        'system'  => $system_base,
        'user'    => 'Tạo câu hỏi nhìn lại (self-reflection) cho bài:
Bài: {{lesson_title}}

Trả về JSON với câu hỏi và 4 lựa chọn cảm xúc/mức độ hiểu:
{
  "prompt": "câu hỏi nhìn lại",
  "options": ["😀 Hiểu rõ...", "🤔 ...", "😕 ...", "❓ ..."]
}

CHỈ trả về JSON.',
        'example' => '{"prompt":"Bạn cảm thấy thế nào về bài học hôm nay?","options":["😀 Hiểu rõ, làm được hết","🤔 Hiểu phần lớn, còn vài chỗ chưa chắc","😕 Khó, cần xem lại lý thuyết","❓ Chưa hiểu, cần giải thích thêm"]}',
    ],

    // Summary Worker
    [
        'worker'  => 'summary',
        'name'    => 'Summary Worker — Tạo tóm tắt bài học',
        'system'  => $system_base,
        'user'    => 'Tạo tóm tắt bài học:
Bài: {{lesson_title}}
Nội dung bài học: {{lesson_summary}}

Tóm tắt các điểm quan trọng, công thức, và lỗi hay gặp.
Dùng LaTeX: \\(...\\) inline, \\[...\\] block.

Trả về JSON:
{
  "points": ["điểm kiến thức 1", "điểm kiến thức 2", ...],
  "formulas": ["công thức 1 với LaTeX", ...],
  "common_mistakes": ["lỗi hay gặp 1", ...]
}

CHỈ trả về JSON.',
        'example' => '{"points":["Số hữu tỉ viết được dạng p/q với p,q nguyên, q≠0","Quan hệ: N⊂Z⊂Q"],"formulas":["\\(\\dfrac{a}{m}+\\dfrac{b}{m}=\\dfrac{a+b}{m}\\)"],"common_mistakes":["Nhầm √2 là số hữu tỉ","Quên quy đồng mẫu dương trước khi so sánh"]}',
    ],

    // AI Explanation Worker
    [
        'worker'  => 'ai_explanation',
        'name'    => 'AI Explanation Worker — Tạo chat AI tự do',
        'system'  => $system_base,
        'user'    => 'Tạo cấu hình cho hoạt động hỏi AI tự do, chủ đề:
Bài: {{lesson_title}}
Tiêu đề: {{activity_title}}

Đề xuất 3 câu hỏi gợi ý học sinh có thể hỏi AI.

Trả về JSON:
{
  "topic": "chủ đề",
  "suggestions": ["câu hỏi gợi ý 1", "câu hỏi gợi ý 2", "câu hỏi gợi ý 3"]
}

CHỈ trả về JSON.',
        'example' => '{"topic":"Cộng, trừ số hữu tỉ","suggestions":["Tại sao phải quy đồng mẫu trước khi cộng?","Khi nào thì không cần quy đồng?","Làm sao cộng nhanh hai số hữu tỉ đơn giản?"]}',
    ],
];

$stmt = $db->prepare('
    INSERT INTO ls_prompt (worker, name, system_prompt, user_template, output_example)
    VALUES (:worker, :name, :system, :user, :example)
    ON DUPLICATE KEY UPDATE
        name=VALUES(name), system_prompt=VALUES(system_prompt),
        user_template=VALUES(user_template), output_example=VALUES(output_example),
        version=version+1
');

foreach ($prompts as $p) {
    $stmt->execute([
        ':worker'  => $p['worker'],
        ':name'    => $p['name'],
        ':system'  => $p['system'],
        ':user'    => $p['user'],
        ':example' => $p['example'],
    ]);
}

// ─── Blueprints ───────────────────────────────────────────────────
$blueprints = [
    [
        'name'        => 'Khái niệm mới',
        'description' => 'Cho bài học giới thiệu khái niệm toán học mới',
        'is_default'  => 1,
        'structure'   => [
            ['type' => 'introduction',    'title' => 'Mở đầu'],
            ['type' => 'theory',          'title' => 'Định nghĩa và lý thuyết'],
            ['type' => 'example',         'title' => 'Ví dụ minh họa'],
            ['type' => 'ai_explanation',  'title' => 'Hỏi AI'],
            ['type' => 'quiz',            'title' => 'Kiểm tra nhanh 1'],
            ['type' => 'guided_practice', 'title' => 'Luyện tập có hướng dẫn'],
            ['type' => 'fill_answer',     'title' => 'Điền đáp án'],
            ['type' => 'quiz',            'title' => 'Kiểm tra nhanh 2'],
            ['type' => 'reflection',      'title' => 'Nhìn lại'],
            ['type' => 'summary',         'title' => 'Tóm tắt'],
        ],
    ],
    [
        'name'        => 'Ôn tập',
        'description' => 'Cho bài ôn tập kiến thức đã học',
        'is_default'  => 0,
        'structure'   => [
            ['type' => 'introduction', 'title' => 'Mở đầu ôn tập'],
            ['type' => 'theory',       'title' => 'Ôn lại lý thuyết'],
            ['type' => 'quiz',         'title' => 'Quiz 1'],
            ['type' => 'quiz',         'title' => 'Quiz 2'],
            ['type' => 'quiz',         'title' => 'Quiz 3'],
            ['type' => 'fill_answer',  'title' => 'Điền đáp án'],
            ['type' => 'reflection',   'title' => 'Nhìn lại'],
            ['type' => 'summary',      'title' => 'Tóm tắt'],
        ],
    ],
    [
        'name'        => 'Kỹ năng tính toán',
        'description' => 'Cho bài học phép tính cụ thể (cộng, trừ, nhân, chia...)',
        'is_default'  => 0,
        'structure'   => [
            ['type' => 'introduction',    'title' => 'Mở đầu'],
            ['type' => 'theory',          'title' => 'Quy tắc tính toán'],
            ['type' => 'theory',          'title' => 'Lưu ý và tính chất'],
            ['type' => 'example',         'title' => 'Ví dụ từng bước'],
            ['type' => 'ai_explanation',  'title' => 'Hỏi AI'],
            ['type' => 'quiz',            'title' => 'Kiểm tra nhanh'],
            ['type' => 'guided_practice', 'title' => 'Luyện tập có hướng dẫn'],
            ['type' => 'fill_answer',     'title' => 'Điền đáp án'],
            ['type' => 'quiz',            'title' => 'Bài tập nâng cao'],
            ['type' => 'reflection',      'title' => 'Nhìn lại'],
            ['type' => 'summary',         'title' => 'Tóm tắt'],
        ],
    ],
];

$bstmt = $db->prepare('
    INSERT INTO ls_blueprint (name, description, structure, is_default)
    VALUES (:name, :desc, :structure, :is_default)
');
foreach ($blueprints as $bp) {
    $bstmt->execute([
        ':name'       => $bp['name'],
        ':desc'       => $bp['description'],
        ':structure'  => json_encode($bp['structure'], JSON_UNESCAPED_UNICODE),
        ':is_default' => $bp['is_default'],
    ]);
}

echo '<h2>✅ Studio seeded</h2>';
echo '<ul>';
echo '<li>' . count($prompts) . ' AI worker prompts</li>';
echo '<li>' . count($blueprints) . ' blueprints</li>';
echo '</ul>';
echo '<p><a href="/Math/studio/">→ Mở Learning Studio</a></p>';
