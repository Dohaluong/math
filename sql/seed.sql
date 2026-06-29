USE math_tutor;
SET sql_mode = 'NO_BACKSLASH_ESCAPES';

-- Student
INSERT INTO student (id, name) VALUES (1, 'Học sinh');

-- ============================================================
-- CHƯƠNG 1 — Số hữu tỉ và số thực
-- ============================================================

-- BÀI 1: Tập hợp số hữu tỉ
INSERT INTO lesson (id, chapter_no, chapter_title, lesson_no, title)
VALUES (1, 1, 'Số hữu tỉ và số thực', 1, 'Tập hợp số hữu tỉ');

INSERT INTO lesson_block (lesson_id, block_type, title, content, display_order) VALUES
(1, 'introduction', 'Giới thiệu', 'Ở lớp 6, chúng ta đã biết đến số nguyên và phân số. Bài học này giới thiệu một tập hợp số rộng hơn — <strong>tập hợp số hữu tỉ</strong> — bao gồm tất cả các số đó.', 1),

(1, 'concept', 'Số hữu tỉ là gì?', '<strong>Định nghĩa:</strong> Số hữu tỉ là số viết được dưới dạng \(\dfrac{p}{q}\), trong đó \(p, q \in \mathbb{Z}\) và \(q \neq 0\).<br><br>
Tập hợp các số hữu tỉ ký hiệu là \(\mathbb{Q}\).<br><br>
<strong>Nhận xét quan trọng:</strong>
<ul>
  <li>Mọi số nguyên \(n\) đều là số hữu tỉ vì \(n = \dfrac{n}{1}\)</li>
  <li>Mọi phân số đều là số hữu tỉ</li>
  <li>Số hữu tỉ biểu diễn được dưới dạng số thập phân hữu hạn hoặc vô hạn tuần hoàn</li>
  <li>Quan hệ: \(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)</li>
</ul>', 2),

(1, 'example', 'Ví dụ minh họa', '<strong>Ví dụ 1:</strong> Các số sau có phải số hữu tỉ không?<br><br>
\(\dfrac{3}{4}\) → ✓ Có, vì viết được dạng \(\dfrac{p}{q}\)<br>
\(-5\) → ✓ Có, vì \(-5 = \dfrac{-5}{1}\)<br>
\(0\) → ✓ Có, vì \(0 = \dfrac{0}{1}\)<br>
\(2{,}5\) → ✓ Có, vì \(2{,}5 = \dfrac{5}{2}\)<br>
\(\sqrt{2}\) → ✗ Không, không viết được dạng \(\dfrac{p}{q}\) với \(p, q\) nguyên<br><br>
<strong>Ví dụ 2:</strong> So sánh \(\dfrac{-2}{3}\) và \(\dfrac{-3}{4}\)<br><br>
Quy đồng mẫu số 12: \(\dfrac{-8}{12}\) và \(\dfrac{-9}{12}\)<br>
Vì \(-8 > -9\) nên \(\dfrac{-2}{3} > \dfrac{-3}{4}\)', 3),

(1, 'note', 'Lưu ý', 'Biểu diễn số hữu tỉ trên <strong>trục số</strong>:<br>
<ul>
  <li>Số hữu tỉ dương nằm bên <em>phải</em> điểm 0</li>
  <li>Số hữu tỉ âm nằm bên <em>trái</em> điểm 0</li>
  <li>Trong hai số hữu tỉ, số nào nằm bên phải thì <strong>lớn hơn</strong></li>
</ul>', 4),

(1, 'summary', 'Tóm tắt', '<strong>Ghi nhớ:</strong>
<ul>
  <li>Số hữu tỉ: \(\dfrac{p}{q}\) với \(p, q \in \mathbb{Z},\; q \neq 0\)</li>
  <li>Ký hiệu tập số hữu tỉ: \(\mathbb{Q}\)</li>
  <li>Quan hệ: \(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)</li>
  <li>\(\sqrt{2}, \sqrt{3}, \pi\) <strong>không</strong> phải số hữu tỉ</li>
</ul>', 5);

-- Questions for Bài 1
INSERT INTO question (lesson_id, question, option_a, option_b, option_c, option_d, correct_answer, hint, explanation, display_order) VALUES
(1, 'Trong các số sau, số nào <strong>không phải</strong> là số hữu tỉ?',
 '\(\dfrac{3}{5}\)', '\(-7\)', '\(\sqrt{2}\)', '\(0{,}25\)',
 'C',
 'Số hữu tỉ phải viết được dưới dạng \(\dfrac{p}{q}\) với p, q là số nguyên và q ≠ 0.',
 '\(\sqrt{2}\) không viết được dưới dạng \(\dfrac{p}{q}\) với p, q nguyên, nên không phải số hữu tỉ. Các số còn lại: \(\dfrac{3}{5}\) là phân số, \(-7 = \dfrac{-7}{1}\), \(0{,}25 = \dfrac{1}{4}\) đều là số hữu tỉ.',
 1),

(1, 'Số nguyên \(-3\) có phải là số hữu tỉ không? Vì sao?',
 'Không, vì \(-3\) là số âm',
 'Có, vì \(-3 = \dfrac{-3}{1}\)',
 'Không, vì \(-3\) không phải phân số',
 'Có, nhưng không giải thích được',
 'B',
 'Mọi số nguyên n đều có thể viết dưới dạng phân số với mẫu số bằng 1.',
 'Mọi số nguyên \(n\) đều là số hữu tỉ vì \(n = \dfrac{n}{1}\). Do đó \(-3 = \dfrac{-3}{1}\) là số hữu tỉ.',
 2),

(1, 'Số \(0{,}75\) được viết dưới dạng phân số tối giản là:',
 '\(\dfrac{3}{5}\)', '\(\dfrac{7}{5}\)', '\(\dfrac{3}{4}\)', '\(\dfrac{75}{10}\)',
 'C',
 '0,75 = 75/100. Hãy rút gọn phân số này.',
 '\(0{,}75 = \dfrac{75}{100} = \dfrac{3}{4}\) (rút gọn bằng cách chia cả tử và mẫu cho 25).',
 3),

(1, 'Số hữu tỉ dương là số hữu tỉ:',
 'Có tử số dương',
 'Lớn hơn 0',
 'Có mẫu số dương',
 'Không âm',
 'B',
 'Hãy nghĩ về định nghĩa số dương trên trục số.',
 'Số hữu tỉ dương là số hữu tỉ lớn hơn 0. Ví dụ \(\dfrac{7}{3} > 0\) nên là số hữu tỉ dương.',
 4),

(1, 'So sánh \(\dfrac{-3}{5}\) và \(\dfrac{-4}{7}\):',
 '\(\dfrac{-3}{5} > \dfrac{-4}{7}\)',
 '\(\dfrac{-3}{5} < \dfrac{-4}{7}\)',
 '\(\dfrac{-3}{5} = \dfrac{-4}{7}\)',
 'Không so sánh được',
 'B',
 'Quy đồng mẫu số rồi so sánh tử số. Chú ý: với số âm, tử nhỏ hơn thì số nhỏ hơn.',
 'Quy đồng mẫu 35: \(\dfrac{-3}{5} = \dfrac{-21}{35}\) và \(\dfrac{-4}{7} = \dfrac{-20}{35}\). Vì \(-21 < -20\) nên \(\dfrac{-3}{5} < \dfrac{-4}{7}\).',
 5),

(1, 'Quan hệ giữa tập N, Z, Q là:',
 '\(\mathbb{Q} \subset \mathbb{Z} \subset \mathbb{N}\)',
 '\(\mathbb{N} \subset \mathbb{Q} \subset \mathbb{Z}\)',
 '\(\mathbb{Z} \subset \mathbb{N} \subset \mathbb{Q}\)',
 '\(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\)',
 'D',
 'Mọi số tự nhiên đều là số nguyên, và mọi số nguyên đều là số hữu tỉ.',
 '\(\mathbb{N} \subset \mathbb{Z} \subset \mathbb{Q}\): tập số tự nhiên là tập con của tập số nguyên, và tập số nguyên là tập con của tập số hữu tỉ.',
 6),

(1, 'Điểm biểu diễn số hữu tỉ \(\dfrac{-1}{2}\) trên trục số nằm ở đâu?',
 'Giữa 0 và 1',
 'Giữa \(-1\) và 0',
 'Tại điểm \(-1\)',
 'Giữa \(-2\) và \(-1\)',
 'B',
 '\(-\dfrac{1}{2} = -0{,}5\). Hãy xác định vị trí của \(-0{,}5\) trên trục số.',
 '\(-\dfrac{1}{2} = -0{,}5\), nằm giữa \(-1\) và \(0\) trên trục số, về phía bên trái điểm 0.',
 7),

(1, 'Số thập phân \(2{,}5\) được viết dưới dạng số hữu tỉ là:',
 '\(\dfrac{5}{2}\)', '\(\dfrac{2}{5}\)', '\(\dfrac{25}{100}\)', '\(\dfrac{1}{4}\)',
 'A',
 '\(2{,}5 = 2 + 0{,}5 = 2 + \dfrac{1}{2}\)',
 '\(2{,}5 = \dfrac{25}{10} = \dfrac{5}{2}\) (rút gọn). Ta cũng có: \(2{,}5 = 2 + \dfrac{1}{2} = \dfrac{5}{2}\).',
 8),

(1, 'Phân số nào sau đây bằng \(\dfrac{-3}{4}\)?',
 '\(\dfrac{3}{-4}\)',
 '\(\dfrac{-6}{8}\)',
 '\(\dfrac{6}{-8}\)',
 'Tất cả đều đúng',
 'D',
 'Hai phân số bằng nhau khi tích chéo bằng nhau. Hãy kiểm tra từng trường hợp.',
 '\(\dfrac{3}{-4} = -\dfrac{3}{4}\) ✓; \(\dfrac{-6}{8} = -\dfrac{3}{4}\) (chia 2) ✓; \(\dfrac{6}{-8} = -\dfrac{3}{4}\) (chia 2) ✓. Tất cả đều bằng \(\dfrac{-3}{4}\).',
 9),

(1, 'Số hữu tỉ lớn nhất trong các số: \(\dfrac{-1}{2},\; \dfrac{-3}{4},\; \dfrac{-1}{3},\; \dfrac{-2}{5}\) là:',
 '\(\dfrac{-1}{2}\)', '\(\dfrac{-3}{4}\)', '\(\dfrac{-1}{3}\)', '\(\dfrac{-2}{5}\)',
 'C',
 'Quy đồng mẫu 60: lần lượt là \(\dfrac{-30}{60}, \dfrac{-45}{60}, \dfrac{-20}{60}, \dfrac{-24}{60}\). Số nào có tử lớn nhất?',
 'Quy đồng mẫu 60: \(-\dfrac{1}{2}=-\dfrac{30}{60}\), \(-\dfrac{3}{4}=-\dfrac{45}{60}\), \(-\dfrac{1}{3}=-\dfrac{20}{60}\), \(-\dfrac{2}{5}=-\dfrac{24}{60}\). Vì \(-20>-24>-30>-45\) nên \(-\dfrac{1}{3}\) lớn nhất.',
 10);

-- ============================================================
-- BÀI 2: Cộng, trừ số hữu tỉ
-- ============================================================

INSERT INTO lesson (id, chapter_no, chapter_title, lesson_no, title)
VALUES (2, 1, 'Số hữu tỉ và số thực', 2, 'Cộng, trừ số hữu tỉ');

INSERT INTO lesson_block (lesson_id, block_type, title, content, display_order) VALUES
(2, 'introduction', 'Giới thiệu', 'Chúng ta đã biết cộng, trừ phân số. Với số hữu tỉ, quy tắc cũng tương tự: viết chúng về dạng phân số cùng mẫu rồi cộng hoặc trừ tử số.', 1),

(2, 'concept', 'Quy tắc cộng, trừ số hữu tỉ', 'Với \(x = \dfrac{a}{m}\) và \(y = \dfrac{b}{m}\) (cùng mẫu):<br><br>
\[x + y = \dfrac{a}{m} + \dfrac{b}{m} = \dfrac{a+b}{m}\]
\[x - y = \dfrac{a}{m} - \dfrac{b}{m} = \dfrac{a-b}{m}\]<br>
Nếu khác mẫu, ta <strong>quy đồng mẫu</strong> trước, rồi cộng hoặc trừ.<br><br>
<strong>Tính chất:</strong>
<ul>
  <li>Giao hoán: \(x + y = y + x\)</li>
  <li>Kết hợp: \((x + y) + z = x + (y + z)\)</li>
  <li>Cộng với 0: \(x + 0 = x\)</li>
  <li>Số đối: \(x + (-x) = 0\)</li>
</ul>', 2),

(2, 'example', 'Ví dụ', '<strong>Ví dụ 1:</strong> Tính \(\dfrac{3}{4} + \dfrac{1}{6}\)<br><br>
BCNN(4, 6) = 12<br>
\(\dfrac{3}{4} + \dfrac{1}{6} = \dfrac{9}{12} + \dfrac{2}{12} = \dfrac{11}{12}\)<br><br>
<strong>Ví dụ 2:</strong> Tính \(\dfrac{-5}{6} - \dfrac{1}{4}\)<br><br>
BCNN(6, 4) = 12<br>
\(\dfrac{-5}{6} - \dfrac{1}{4} = \dfrac{-10}{12} - \dfrac{3}{12} = \dfrac{-13}{12}\)<br><br>
<strong>Ví dụ 3:</strong> Tính \(1\dfrac{1}{3} + \dfrac{-5}{6}\)<br><br>
\(1\dfrac{1}{3} = \dfrac{4}{3}\)<br>
\(\dfrac{4}{3} + \dfrac{-5}{6} = \dfrac{8}{6} + \dfrac{-5}{6} = \dfrac{3}{6} = \dfrac{1}{2}\)', 3),

(2, 'note', 'Lưu ý', '<ul>
  <li>Trừ số hữu tỉ \(y\) bằng cộng với số đối của \(y\): \(x - y = x + (-y)\)</li>
  <li>Số đối của \(\dfrac{a}{b}\) là \(\dfrac{-a}{b}\)</li>
  <li>Khi kết quả là phân số, nhớ rút gọn về dạng tối giản</li>
</ul>', 4),

(2, 'summary', 'Tóm tắt', '<strong>Ghi nhớ:</strong>
<ul>
  <li>Cộng/trừ số hữu tỉ: quy đồng mẫu rồi cộng/trừ tử</li>
  <li>\(x - y = x + (-y)\)</li>
  <li>Áp dụng tính chất giao hoán, kết hợp để tính nhanh</li>
</ul>', 5);

INSERT INTO question (lesson_id, question, option_a, option_b, option_c, option_d, correct_answer, hint, explanation, display_order) VALUES
(2, 'Tính \(\dfrac{1}{3} + \dfrac{1}{4}\)',
 '\(\dfrac{2}{7}\)', '\(\dfrac{7}{12}\)', '\(\dfrac{1}{7}\)', '\(\dfrac{5}{12}\)',
 'B',
 'Tìm BCNN(3,4) = 12, rồi quy đồng mẫu.',
 'BCNN(3,4) = 12. \(\dfrac{1}{3} + \dfrac{1}{4} = \dfrac{4}{12} + \dfrac{3}{12} = \dfrac{7}{12}\).',
 1),

(2, 'Tính \(\dfrac{5}{6} - \dfrac{1}{4}\)',
 '\(\dfrac{4}{2}\)', '\(\dfrac{7}{12}\)', '\(\dfrac{4}{6}\)', '\(\dfrac{11}{12}\)',
 'B',
 'BCNN(6,4) = 12. Quy đồng rồi trừ tử số.',
 'BCNN(6,4) = 12. \(\dfrac{5}{6} - \dfrac{1}{4} = \dfrac{10}{12} - \dfrac{3}{12} = \dfrac{7}{12}\).',
 2),

(2, 'Tính \(\dfrac{-3}{4} + \dfrac{1}{2}\)',
 '\(\dfrac{-1}{4}\)', '\(\dfrac{-2}{6}\)', '\(\dfrac{-1}{2}\)', '\(\dfrac{1}{4}\)',
 'A',
 'Quy đồng mẫu số 4: \(\dfrac{1}{2} = \dfrac{2}{4}\)',
 '\(\dfrac{-3}{4} + \dfrac{1}{2} = \dfrac{-3}{4} + \dfrac{2}{4} = \dfrac{-1}{4}\).',
 3),

(2, 'Tính \(\dfrac{2}{3} - \dfrac{-1}{6}\)',
 '\(\dfrac{1}{2}\)', '\(\dfrac{5}{6}\)', '\(\dfrac{1}{6}\)', '\(\dfrac{3}{9}\)',
 'B',
 'Trừ số âm bằng cộng số dương. \(\dfrac{2}{3} - \dfrac{-1}{6} = \dfrac{2}{3} + \dfrac{1}{6}\)',
 '\(\dfrac{2}{3} - \dfrac{-1}{6} = \dfrac{2}{3} + \dfrac{1}{6} = \dfrac{4}{6} + \dfrac{1}{6} = \dfrac{5}{6}\).',
 4),

(2, 'Tổng \(\dfrac{1}{2} + \dfrac{1}{3} + \dfrac{1}{6}\) bằng:',
 '\(\dfrac{3}{11}\)', '\(\dfrac{3}{6}\)', '\(1\)', '\(\dfrac{5}{6}\)',
 'C',
 'BCNN(2,3,6) = 6. Quy đồng cả ba phân số.',
 'BCNN = 6: \(\dfrac{3}{6} + \dfrac{2}{6} + \dfrac{1}{6} = \dfrac{6}{6} = 1\).',
 5),

(2, 'Số đối của \(\dfrac{-5}{7}\) là:',
 '\(\dfrac{5}{-7}\)', '\(\dfrac{7}{5}\)', '\(\dfrac{5}{7}\)', '\(\dfrac{-7}{5}\)',
 'C',
 'Số đối của \(\dfrac{a}{b}\) là \(\dfrac{-a}{b}\).',
 'Số đối của \(\dfrac{-5}{7}\) là \(\dfrac{-(-5)}{7} = \dfrac{5}{7}\).',
 6),

(2, 'Tính \(0{,}5 + \dfrac{1}{4}\)',
 '\(\dfrac{3}{4}\)', '\(0{,}75\)', '\(\dfrac{1}{8}\)', 'Cả A và B đều đúng',
 'D',
 '\(0{,}5 = \dfrac{1}{2}\). Tính rồi kiểm tra các đáp án.',
 '\(0{,}5 + \dfrac{1}{4} = \dfrac{1}{2} + \dfrac{1}{4} = \dfrac{2}{4} + \dfrac{1}{4} = \dfrac{3}{4} = 0{,}75\). Cả A và B đều đúng.',
 7),

(2, 'Giá trị của \(x\) trong \(x + \dfrac{2}{5} = \dfrac{4}{5}\) là:',
 '\(\dfrac{6}{5}\)', '\(\dfrac{2}{5}\)', '\(\dfrac{2}{25}\)', '\(\dfrac{1}{2}\)',
 'B',
 'Chuyển \(\dfrac{2}{5}\) sang vế phải với dấu âm.',
 '\(x = \dfrac{4}{5} - \dfrac{2}{5} = \dfrac{2}{5}\).',
 8),

(2, 'Tính \(\dfrac{-7}{12} + \dfrac{5}{18}\)',
 '\(\dfrac{-31}{36}\)', '\(\dfrac{-11}{36}\)', '\(\dfrac{-2}{6}\)', '\(\dfrac{-11}{18}\)',
 'B',
 'BCNN(12,18) = 36. Quy đồng rồi cộng.',
 'BCNN(12,18) = 36. \(\dfrac{-7}{12} = \dfrac{-21}{36}\); \(\dfrac{5}{18} = \dfrac{10}{36}\). Tổng: \(\dfrac{-21+10}{36} = \dfrac{-11}{36}\).',
 9),

(2, 'Biểu thức \(\dfrac{3}{4} - \dfrac{1}{6} + \dfrac{-1}{12}\) bằng:',
 '\(\dfrac{1}{2}\)', '\(\dfrac{7}{12}\)', '\(\dfrac{5}{12}\)', '\(\dfrac{2}{3}\)',
 'A',
 'BCNN(4,6,12) = 12. Quy đồng tất cả.',
 'BCNN = 12: \(\dfrac{9}{12} - \dfrac{2}{12} + \dfrac{-1}{12} = \dfrac{9-2-1}{12} = \dfrac{6}{12} = \dfrac{1}{2}\).',
 10);

-- ============================================================
-- BÀI 3: Nhân, chia số hữu tỉ
-- ============================================================

INSERT INTO lesson (id, chapter_no, chapter_title, lesson_no, title)
VALUES (3, 1, 'Số hữu tỉ và số thực', 3, 'Nhân, chia số hữu tỉ');

INSERT INTO lesson_block (lesson_id, block_type, title, content, display_order) VALUES
(3, 'introduction', 'Giới thiệu', 'Nhân và chia số hữu tỉ được thực hiện tương tự như nhân và chia phân số. Không cần quy đồng mẫu số khi nhân hay chia.', 1),

(3, 'concept', 'Quy tắc nhân, chia số hữu tỉ', 'Với \(x = \dfrac{a}{b}\) và \(y = \dfrac{c}{d}\) (\(b, d \neq 0\)):<br><br>
<strong>Nhân:</strong> \[x \cdot y = \dfrac{a}{b} \cdot \dfrac{c}{d} = \dfrac{a \cdot c}{b \cdot d}\]<br>
<strong>Chia</strong> (\(y \neq 0\)): \[x \div y = \dfrac{a}{b} \div \dfrac{c}{d} = \dfrac{a}{b} \cdot \dfrac{d}{c} = \dfrac{a \cdot d}{b \cdot c}\]<br>
<strong>Tính chất của phép nhân:</strong>
<ul>
  <li>Giao hoán: \(x \cdot y = y \cdot x\)</li>
  <li>Kết hợp: \((x \cdot y) \cdot z = x \cdot (y \cdot z)\)</li>
  <li>Phân phối: \(x(y + z) = xy + xz\)</li>
  <li>Nhân với 1: \(x \cdot 1 = x\)</li>
</ul>', 2),

(3, 'example', 'Ví dụ', '<strong>Ví dụ 1:</strong> Tính \(\dfrac{3}{4} \cdot \dfrac{8}{9}\)<br><br>
\(\dfrac{3}{4} \cdot \dfrac{8}{9} = \dfrac{3 \times 8}{4 \times 9} = \dfrac{24}{36} = \dfrac{2}{3}\)<br><br>
(Có thể rút gọn trước: \(\dfrac{\not{3}}{{\not{4}}_1} \cdot \dfrac{{\not{8}}^2}{{\not{9}}_3} = \dfrac{2}{3}\))<br><br>
<strong>Ví dụ 2:</strong> Tính \(\dfrac{-5}{6} \div \dfrac{2}{3}\)<br><br>
\(\dfrac{-5}{6} \div \dfrac{2}{3} = \dfrac{-5}{6} \times \dfrac{3}{2} = \dfrac{-15}{12} = \dfrac{-5}{4}\)<br><br>
<strong>Ví dụ 3:</strong> Tính \((-3) \times \dfrac{2}{9}\)<br><br>
\((-3) \times \dfrac{2}{9} = \dfrac{-3}{1} \times \dfrac{2}{9} = \dfrac{-6}{9} = \dfrac{-2}{3}\)', 3),

(3, 'note', 'Lưu ý', '<ul>
  <li>Chia cho một số bằng nhân với <strong>số nghịch đảo</strong> của số đó</li>
  <li>Nghịch đảo của \(\dfrac{a}{b}\) là \(\dfrac{b}{a}\) (với \(a \neq 0\))</li>
  <li>Khi nhân phân số, có thể rút gọn chéo trước để đơn giản hơn</li>
  <li>Tích hai số âm là số <strong>dương</strong>. Tích một số dương và một số âm là số <strong>âm</strong></li>
</ul>', 4),

(3, 'summary', 'Tóm tắt', '<strong>Ghi nhớ:</strong>
<ul>
  <li>Nhân: \(\dfrac{a}{b} \cdot \dfrac{c}{d} = \dfrac{ac}{bd}\)</li>
  <li>Chia: \(\dfrac{a}{b} \div \dfrac{c}{d} = \dfrac{a}{b} \cdot \dfrac{d}{c} = \dfrac{ad}{bc}\)</li>
  <li>Dấu của tích/thương: cùng dấu → dương; khác dấu → âm</li>
</ul>', 5);

INSERT INTO question (lesson_id, question, option_a, option_b, option_c, option_d, correct_answer, hint, explanation, display_order) VALUES
(3, 'Tính \(\dfrac{2}{3} \times \dfrac{3}{4}\)',
 '\(\dfrac{6}{12}\)', '\(\dfrac{1}{2}\)', '\(\dfrac{5}{7}\)', 'Cả A và B đều đúng',
 'D',
 'Nhân tử với tử, mẫu với mẫu, rồi rút gọn.',
 '\(\dfrac{2}{3} \times \dfrac{3}{4} = \dfrac{6}{12} = \dfrac{1}{2}\). Cả A và B đều bằng \(\dfrac{1}{2}\), nên đều đúng.',
 1),

(3, 'Tính \(\dfrac{3}{5} \div \dfrac{9}{10}\)',
 '\(\dfrac{27}{50}\)', '\(\dfrac{2}{3}\)', '\(\dfrac{3}{2}\)', '\(\dfrac{1}{2}\)',
 'B',
 '\(\dfrac{3}{5} \div \dfrac{9}{10} = \dfrac{3}{5} \times \dfrac{10}{9}\). Hãy tính tiếp.',
 '\(\dfrac{3}{5} \div \dfrac{9}{10} = \dfrac{3}{5} \times \dfrac{10}{9} = \dfrac{30}{45} = \dfrac{2}{3}\).',
 2),

(3, 'Nghịch đảo của \(\dfrac{-4}{7}\) là:',
 '\(\dfrac{4}{7}\)', '\(\dfrac{7}{4}\)', '\(\dfrac{-7}{4}\)', '\(\dfrac{-4}{7}\)',
 'C',
 'Nghịch đảo của \(\dfrac{a}{b}\) là \(\dfrac{b}{a}\), giữ nguyên dấu.',
 'Nghịch đảo của \(\dfrac{-4}{7}\) là \(\dfrac{7}{-4} = \dfrac{-7}{4}\).',
 3),

(3, 'Tính \((-2) \times \dfrac{3}{8}\)',
 '\(\dfrac{3}{4}\)', '\(\dfrac{-3}{4}\)', '\(\dfrac{-6}{8}\)', 'Cả B và C đều đúng',
 'D',
 '(-2) × (3/8) = (-2 × 3) / 8. Tính và rút gọn.',
 '\((-2) \times \dfrac{3}{8} = \dfrac{-6}{8} = \dfrac{-3}{4}\). Cả B (\(\dfrac{-3}{4}\)) và C (\(\dfrac{-6}{8}\)) đều bằng nhau, nên đều đúng.',
 4),

(3, 'Kết quả của \(\dfrac{-1}{3} \times \dfrac{-6}{5}\) là:',
 '\(\dfrac{-6}{15}\)', '\(\dfrac{6}{15}\)', '\(\dfrac{2}{5}\)', '\(\dfrac{-2}{5}\)',
 'C',
 'Âm nhân âm bằng dương. Tích là \(\dfrac{6}{15}\), hãy rút gọn.',
 '\(\dfrac{-1}{3} \times \dfrac{-6}{5} = \dfrac{6}{15} = \dfrac{2}{5}\) (âm × âm = dương, rút gọn ÷3).',
 5),

(3, 'Tính \(\dfrac{4}{5} \div (-2)\)',
 '\(\dfrac{-2}{5}\)', '\(\dfrac{-4}{10}\)', '\(\dfrac{-2}{5}\)', 'Cả A và B đều đúng',
 'D',
 'Chia cho (-2) bằng nhân với \(\dfrac{-1}{2}\).',
 '\(\dfrac{4}{5} \div (-2) = \dfrac{4}{5} \times \dfrac{-1}{2} = \dfrac{-4}{10} = \dfrac{-2}{5}\). A và B đều bằng nhau.',
 6),

(3, 'Biểu thức \(\dfrac{3}{4} \times \dfrac{8}{3} \times \dfrac{1}{2}\) bằng:',
 '\(1\)', '\(\dfrac{1}{2}\)', '\(\dfrac{3}{2}\)', '\(\dfrac{2}{3}\)',
 'A',
 'Có thể rút gọn trước khi nhân để đơn giản hơn.',
 '\(\dfrac{3}{4} \times \dfrac{8}{3} \times \dfrac{1}{2} = \dfrac{3 \times 8 \times 1}{4 \times 3 \times 2} = \dfrac{24}{24} = 1\).',
 7),

(3, 'Tìm x biết \(x \times \dfrac{2}{3} = \dfrac{4}{9}\)',
 '\(\dfrac{2}{3}\)', '\(\dfrac{8}{27}\)', '\(\dfrac{6}{9}\)', '\(\dfrac{2}{9}\)',
 'A',
 '\(x = \dfrac{4}{9} \div \dfrac{2}{3}\). Chia cho một phân số bằng nhân với nghịch đảo.',
 '\(x = \dfrac{4}{9} \div \dfrac{2}{3} = \dfrac{4}{9} \times \dfrac{3}{2} = \dfrac{12}{18} = \dfrac{2}{3}\).',
 8),

(3, 'Tính \(\dfrac{-3}{7} \times \dfrac{14}{9}\)',
 '\(\dfrac{-2}{3}\)', '\(\dfrac{2}{3}\)', '\(\dfrac{-42}{63}\)', '\(\dfrac{-3}{4}\)',
 'A',
 'Rút gọn chéo trước: 7 và 14, 3 và 9.',
 '\(\dfrac{-3}{7} \times \dfrac{14}{9} = \dfrac{-3 \times 14}{7 \times 9} = \dfrac{-42}{63} = \dfrac{-2}{3}\).',
 9),

(3, 'Trong một ngày, bạn An học \(\dfrac{1}{4}\) giờ Toán và học Văn gấp \(\dfrac{3}{2}\) lần thời gian học Toán. Bạn An học Văn bao nhiêu giờ?',
 '\(\dfrac{3}{8}\) giờ', '\(\dfrac{1}{6}\) giờ', '\(\dfrac{5}{4}\) giờ', '\(\dfrac{3}{4}\) giờ',
 'A',
 'Thời gian học Văn = Thời gian học Toán × \(\dfrac{3}{2}\)',
 'Thời gian học Văn \(= \dfrac{1}{4} \times \dfrac{3}{2} = \dfrac{3}{8}\) giờ.',
 10);
