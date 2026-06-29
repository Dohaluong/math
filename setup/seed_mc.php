<?php
/**
 * Run once: http://localhost/Math/setup/seed_mc.php
 * Seeds Master Curriculum — 50 concepts Toán 7 (Kết nối tri thức)
 */
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$db->exec('SET FOREIGN_KEY_CHECKS=0');
$db->exec('DELETE FROM mc_concept');
$db->exec('DELETE FROM mc_module');
$db->exec('SET FOREIGN_KEY_CHECKS=1');

// ─── Modules ──────────────────────────────────────────────────
$modules = [
    ['code'=>'M1', 'name'=>'Số hữu tỉ',         'chapter_no'=>1, 'chapter_title'=>'Số hữu tỉ và số thực', 'sort_order'=>1],
    ['code'=>'M2', 'name'=>'Số thực',             'chapter_no'=>1, 'chapter_title'=>'Số hữu tỉ và số thực', 'sort_order'=>2],
    ['code'=>'M3', 'name'=>'Đại số',              'chapter_no'=>2, 'chapter_title'=>'Đại số',               'sort_order'=>3],
    ['code'=>'M4', 'name'=>'Hằng đẳng thức',      'chapter_no'=>2, 'chapter_title'=>'Đại số',               'sort_order'=>4],
    ['code'=>'M5', 'name'=>'Phân tích đa thức',   'chapter_no'=>2, 'chapter_title'=>'Đại số',               'sort_order'=>5],
    ['code'=>'M6', 'name'=>'Hàm số',              'chapter_no'=>3, 'chapter_title'=>'Hàm số và đồ thị',    'sort_order'=>6],
    ['code'=>'M7', 'name'=>'Hình học',             'chapter_no'=>4, 'chapter_title'=>'Hình học phẳng',      'sort_order'=>7],
];

$mod_stmt = $db->prepare('INSERT INTO mc_module (code,name,chapter_no,chapter_title,sort_order) VALUES (?,?,?,?,?)');
$module_ids = [];
foreach ($modules as $m) {
    $mod_stmt->execute([$m['code'], $m['name'], $m['chapter_no'], $m['chapter_title'], $m['sort_order']]);
    $module_ids[$m['code']] = (int)$db->lastInsertId();
}

// ─── Concepts ─────────────────────────────────────────────────
// Link existing lessons to matching concepts
$existing = $db->query('SELECT id, lesson_no FROM lesson WHERE chapter_no=1')->fetchAll(PDO::FETCH_KEY_PAIR);
// lesson_no => id mapping
// Bài 1 (lesson_no=1) → concepts 001,002,003
// Bài 2 (lesson_no=2) → concepts 005,006
// Bài 3 (lesson_no=3) → concepts 007,008

$concepts = [
    // ── Module 1: Số hữu tỉ ─────────────────────────────────
    ['module'=>'M1','code'=>'001','name'=>'Khái niệm số hữu tỉ',
     'desc'=>'Định nghĩa số hữu tỉ, tập Q, nhận biết số hữu tỉ trong thực tế',
     'prereq'=>null,'diff'=>1,'lesson_no'=>1],

    ['module'=>'M1','code'=>'002','name'=>'Biểu diễn số hữu tỉ trên trục số',
     'desc'=>'Xác định vị trí số hữu tỉ trên trục số, quan hệ với điểm gốc',
     'prereq'=>['001'],'diff'=>1,'lesson_no'=>1],

    ['module'=>'M1','code'=>'003','name'=>'So sánh số hữu tỉ',
     'desc'=>'Quy đồng mẫu dương, so sánh tử, áp dụng với số âm',
     'prereq'=>['001','002'],'diff'=>2,'lesson_no'=>1],

    ['module'=>'M1','code'=>'004','name'=>'Giá trị tuyệt đối của số hữu tỉ',
     'desc'=>'Định nghĩa |x|, tính chất, bài toán tìm x với |x| = a',
     'prereq'=>['003'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M1','code'=>'005','name'=>'Cộng số hữu tỉ',
     'desc'=>'Quy tắc cộng, tính chất giao hoán/kết hợp, ứng dụng tính nhanh',
     'prereq'=>['003'],'diff'=>1,'lesson_no'=>2],

    ['module'=>'M1','code'=>'006','name'=>'Trừ số hữu tỉ',
     'desc'=>'Trừ = cộng số đối, các bước thực hiện',
     'prereq'=>['005'],'diff'=>1,'lesson_no'=>2],

    ['module'=>'M1','code'=>'007','name'=>'Nhân số hữu tỉ',
     'desc'=>'Quy tắc nhân phân số, quy tắc dấu, rút gọn chéo',
     'prereq'=>['003'],'diff'=>1,'lesson_no'=>3],

    ['module'=>'M1','code'=>'008','name'=>'Chia số hữu tỉ',
     'desc'=>'Số nghịch đảo, chia = nhân nghịch đảo, quy tắc dấu',
     'prereq'=>['007'],'diff'=>1,'lesson_no'=>3],

    ['module'=>'M1','code'=>'009','name'=>'Thứ tự thực hiện phép tính',
     'desc'=>'BODMAS/PEMDAS với số hữu tỉ, dấu ngoặc, lũy thừa',
     'prereq'=>['005','006','007','008'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M1','code'=>'010','name'=>'Bài toán thực tế về số hữu tỉ',
     'desc'=>'Vận dụng số hữu tỉ vào bài toán đời sống, chuyển đổi đơn vị',
     'prereq'=>['009'],'diff'=>3,'lesson_no'=>null],

    // ── Module 2: Số thực ────────────────────────────────────
    ['module'=>'M2','code'=>'011','name'=>'Căn bậc hai số học',
     'desc'=>'Định nghĩa √a (a≥0), tính căn bậc hai chính xác và gần đúng',
     'prereq'=>['001'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M2','code'=>'012','name'=>'Số vô tỉ',
     'desc'=>'√2, π, số thập phân vô hạn không tuần hoàn, phân biệt với hữu tỉ',
     'prereq'=>['011'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M2','code'=>'013','name'=>'Số thực',
     'desc'=>'Tập R = Q ∪ I, quan hệ N⊂Z⊂Q⊂R, biểu diễn trên trục số',
     'prereq'=>['001','012'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M2','code'=>'014','name'=>'Làm tròn số',
     'desc'=>'Làm tròn đến hàng, chữ số thập phân; ký hiệu ≈; sai số',
     'prereq'=>['013'],'diff'=>1,'lesson_no'=>null],

    ['module'=>'M2','code'=>'015','name'=>'Trục số thực',
     'desc'=>'Mỗi số thực ứng với một điểm trên trục số và ngược lại',
     'prereq'=>['013'],'diff'=>1,'lesson_no'=>null],

    // ── Module 3: Đại số ─────────────────────────────────────
    ['module'=>'M3','code'=>'016','name'=>'Biểu thức đại số',
     'desc'=>'Khái niệm biến, biểu thức đại số, ví dụ thực tế',
     'prereq'=>['001'],'diff'=>1,'lesson_no'=>null],

    ['module'=>'M3','code'=>'017','name'=>'Giá trị biểu thức',
     'desc'=>'Tính giá trị biểu thức tại giá trị cụ thể của biến',
     'prereq'=>['016'],'diff'=>1,'lesson_no'=>null],

    ['module'=>'M3','code'=>'018','name'=>'Đơn thức',
     'desc'=>'Định nghĩa đơn thức, hệ số, phần biến, bậc của đơn thức',
     'prereq'=>['016'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'019','name'=>'Đa thức',
     'desc'=>'Định nghĩa đa thức, bậc của đa thức, đa thức một biến',
     'prereq'=>['018'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'020','name'=>'Thu gọn đa thức',
     'desc'=>'Nhóm các đơn thức đồng dạng, sắp xếp theo lũy thừa giảm dần',
     'prereq'=>['019'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'021','name'=>'Cộng đa thức',
     'desc'=>'Cộng hai đa thức, đa thức đối, các bước thực hiện',
     'prereq'=>['020'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'022','name'=>'Trừ đa thức',
     'desc'=>'Trừ đa thức = cộng đa thức đối',
     'prereq'=>['021'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'023','name'=>'Đơn thức đồng dạng',
     'desc'=>'Nhận biết đơn thức đồng dạng, cộng trừ đơn thức đồng dạng',
     'prereq'=>['018'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'024','name'=>'Nhân đơn thức với đa thức',
     'desc'=>'Quy tắc nhân, áp dụng tính chất phân phối',
     'prereq'=>['019'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M3','code'=>'025','name'=>'Nhân đa thức với đa thức',
     'desc'=>'Nhân từng hạng tử, thu gọn kết quả',
     'prereq'=>['024'],'diff'=>3,'lesson_no'=>null],

    // ── Module 4: Hằng đẳng thức ─────────────────────────────
    ['module'=>'M4','code'=>'026','name'=>'Bình phương tổng',
     'desc'=>'(a+b)² = a² + 2ab + b², nhận biết và áp dụng',
     'prereq'=>['025'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M4','code'=>'027','name'=>'Bình phương hiệu',
     'desc'=>'(a−b)² = a² − 2ab + b², nhận biết và áp dụng',
     'prereq'=>['026'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M4','code'=>'028','name'=>'Hiệu hai bình phương',
     'desc'=>'(a+b)(a−b) = a² − b², phân tích và tổng hợp',
     'prereq'=>['026','027'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M4','code'=>'029','name'=>'Vận dụng hằng đẳng thức',
     'desc'=>'Tính nhanh, chứng minh đẳng thức, bài toán tổng hợp',
     'prereq'=>['026','027','028'],'diff'=>3,'lesson_no'=>null],

    // ── Module 5: Phân tích đa thức ──────────────────────────
    ['module'=>'M5','code'=>'030','name'=>'Đặt nhân tử chung',
     'desc'=>'Tìm ƯCLN, đặt nhân tử chung, kiểm tra bằng nhân lại',
     'prereq'=>['020'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M5','code'=>'031','name'=>'Dùng hằng đẳng thức phân tích',
     'desc'=>'Nhận dạng và áp dụng 3 hằng đẳng thức để phân tích',
     'prereq'=>['029','030'],'diff'=>3,'lesson_no'=>null],

    ['module'=>'M5','code'=>'032','name'=>'Nhóm hạng tử',
     'desc'=>'Phân tích bằng cách nhóm, tìm nhân tử chung của nhóm',
     'prereq'=>['030'],'diff'=>3,'lesson_no'=>null],

    ['module'=>'M5','code'=>'033','name'=>'Phối hợp các phương pháp',
     'desc'=>'Kết hợp đặt nhân tử, hằng đẳng thức, nhóm hạng tử',
     'prereq'=>['030','031','032'],'diff'=>3,'lesson_no'=>null],

    // ── Module 6: Hàm số ─────────────────────────────────────
    ['module'=>'M6','code'=>'034','name'=>'Đại lượng tỉ lệ thuận',
     'desc'=>'y = kx (k≠0), bảng giá trị, tính chất, bài toán tỉ lệ',
     'prereq'=>['009'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M6','code'=>'035','name'=>'Đại lượng tỉ lệ nghịch',
     'desc'=>'y = k/x (k≠0), phân biệt với tỉ lệ thuận, ứng dụng',
     'prereq'=>['034'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M6','code'=>'036','name'=>'Hàm số',
     'desc'=>'Định nghĩa hàm số, biến độc lập, biến phụ thuộc, f(x)',
     'prereq'=>['034'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M6','code'=>'037','name'=>'Mặt phẳng tọa độ',
     'desc'=>'Hệ trục tọa độ Oxy, cách xác định điểm, tọa độ điểm',
     'prereq'=>['036'],'diff'=>1,'lesson_no'=>null],

    ['module'=>'M6','code'=>'038','name'=>'Đồ thị hàm số y = ax + b',
     'desc'=>'Vẽ đồ thị, đọc đồ thị, hệ số góc, tung độ gốc',
     'prereq'=>['036','037'],'diff'=>3,'lesson_no'=>null],

    // ── Module 7: Hình học ───────────────────────────────────
    ['module'=>'M7','code'=>'039','name'=>'Góc',
     'desc'=>'Các loại góc, đo góc bằng thước đo góc, góc kề bù',
     'prereq'=>null,'diff'=>1,'lesson_no'=>null],

    ['module'=>'M7','code'=>'040','name'=>'Hai góc đối đỉnh',
     'desc'=>'Định nghĩa, định lý hai góc đối đỉnh bằng nhau, chứng minh',
     'prereq'=>['039'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'041','name'=>'Hai đường thẳng vuông góc',
     'desc'=>'Định nghĩa, ký hiệu ⊥, đường trung trực, tính chất',
     'prereq'=>['039'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'042','name'=>'Hai đường thẳng song song',
     'desc'=>'Định nghĩa, ký hiệu ∥, tính chất góc so le trong/đồng vị',
     'prereq'=>['039'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'043','name'=>'Tiên đề Euclid',
     'desc'=>'Nội dung tiên đề, hệ quả, vai trò trong hình học phẳng',
     'prereq'=>['042'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'044','name'=>'Góc tạo bởi cát tuyến',
     'desc'=>'Góc so le trong, đồng vị, trong cùng phía; điều kiện song song',
     'prereq'=>['042','043'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'045','name'=>'Tam giác',
     'desc'=>'Định nghĩa, phân loại theo cạnh và góc, đường cao, trung tuyến',
     'prereq'=>['039'],'diff'=>1,'lesson_no'=>null],

    ['module'=>'M7','code'=>'046','name'=>'Tổng ba góc của tam giác',
     'desc'=>'Định lý tổng = 180°, góc ngoài tam giác, hệ quả',
     'prereq'=>['045','044'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'047','name'=>'Tam giác cân',
     'desc'=>'Định nghĩa, tính chất góc đáy, đường trung trực đáy',
     'prereq'=>['046'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'048','name'=>'Tam giác đều',
     'desc'=>'Định nghĩa, tính chất 3 góc = 60°, tâm tam giác đều',
     'prereq'=>['047'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'049','name'=>'Định lý Pythagore',
     'desc'=>'a² + b² = c², bộ ba Pythagore, ứng dụng tính cạnh',
     'prereq'=>['045','011'],'diff'=>2,'lesson_no'=>null],

    ['module'=>'M7','code'=>'050','name'=>'Quan hệ cạnh và góc trong tam giác',
     'desc'=>'Cạnh lớn đối góc lớn, bất đẳng thức tam giác',
     'prereq'=>['046'],'diff'=>3,'lesson_no'=>null],
];

// Map lesson_no → lesson.id for auto-linking existing lessons
$lesson_map = [];
$rows = $db->prepare('SELECT id, lesson_no FROM lesson WHERE chapter_no = 1');
$rows->execute();
foreach ($rows->fetchAll() as $r) {
    $lesson_map[(int)$r['lesson_no']] = (int)$r['id'];
}

$con_stmt = $db->prepare('
    INSERT INTO mc_concept (module_id,code,name,description,difficulty,prerequisite_codes,status,lesson_id,sort_order)
    VALUES (?,?,?,?,?,?,?,?,?)
');

foreach ($concepts as $i => $c) {
    $mid      = $module_ids[$c['module']];
    $lesson_id = ($c['lesson_no'] && isset($lesson_map[$c['lesson_no']])) ? $lesson_map[$c['lesson_no']] : null;
    $status   = $lesson_id ? 'published' : 'draft';
    $prereq   = $c['prereq'] ? json_encode($c['prereq']) : null;

    $con_stmt->execute([
        $mid,
        $c['code'],
        $c['name'],
        $c['desc'],
        $c['diff'],
        $prereq,
        $status,
        $lesson_id,
        ($i + 1) * 10,
    ]);
}

$total    = count($concepts);
$linked   = count(array_filter($concepts, fn($c) => $c['lesson_no']));
$unlinked = $total - $linked;

echo "<h2>✅ Master Curriculum seeded</h2>";
echo "<ul>";
echo "<li>" . count($modules) . " modules</li>";
echo "<li>$total concepts (50 theo SPEC-004)</li>";
echo "<li>$linked concepts đã có bài học</li>";
echo "<li>$unlinked concepts chờ tạo bài</li>";
echo "</ul>";
echo "<p><a href='/Math/studio/curriculum.php'>→ Mở Curriculum</a></p>";
