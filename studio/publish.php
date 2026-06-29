<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

$filter_lesson = (int)($_GET['lesson'] ?? 0);
$where  = "WHERE d.status = 'approved'";
$params = [];
if ($filter_lesson) { $where .= ' AND d.lesson_id = ?'; $params[] = $filter_lesson; }

$drafts_stmt = $db->prepare("
    SELECT d.*, l.title AS lesson_title, l.lesson_no
    FROM ls_draft d JOIN lesson l ON l.id = d.lesson_id
    $where
    ORDER BY d.lesson_id, d.sort_order, d.id
");
$drafts_stmt->execute($params);
$approved = $drafts_stmt->fetchAll();

// Group by lesson
$by_lesson = [];
foreach ($approved as $d) {
    $by_lesson[$d['lesson_id']] ??= ['lesson_no' => $d['lesson_no'], 'title' => $d['lesson_title'], 'items' => []];
    $by_lesson[$d['lesson_id']]['items'][] = $d;
}

$lessons = $db->query('SELECT id, lesson_no, title FROM lesson ORDER BY lesson_no')->fetchAll();

$type_labels = [
    'introduction' => 'Mở đầu', 'theory' => 'Kiến thức', 'example' => 'Ví dụ',
    'ai_explanation' => 'Hỏi AI', 'quiz' => 'Quiz', 'guided_practice' => 'Luyện tập',
    'fill_answer' => 'Điền đáp án', 'reflection' => 'Nhìn lại', 'summary' => 'Tóm tắt',
];

$page_title    = 'Publish Center';
$active_studio = 'publish';
$base_url = BASE_URL;
include '../includes/studio_header.php';
?>

<?php if (empty($approved)): ?>
<div class="studio-card text-center py-5 text-muted">
  <i class="bi bi-send" style="font-size:2.5rem; opacity:.3; display:block; margin-bottom:8px;"></i>
  Không có draft nào được approve.<br>
  <a href="<?= BASE_URL ?>/studio/review.php" class="btn-studio-primary mt-3" style="display:inline-flex;">
    Đi đến Review Queue
  </a>
</div>
<?php else: ?>
<div class="alert alert-info border-0 mb-4" style="font-size:13px; background:#ebf8ff; color:#2b6cb0;">
  <i class="bi bi-info-circle me-2"></i>
  Publish sẽ <strong>thay thế toàn bộ</strong> activities live của bài học đó bằng các draft đã approve.
  Không thể hoàn tác tự động — hãy review kỹ trước khi publish.
</div>

<?php foreach ($by_lesson as $lid => $group): ?>
<div class="studio-card mb-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <span class="badge bg-light text-dark border me-2">Bài <?= $group['lesson_no'] ?></span>
      <span class="fw-semibold"><?= htmlspecialchars($group['title']) ?></span>
      <span class="text-muted small ms-2"><?= count($group['items']) ?> activities</span>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $lid ?>" target="_blank"
         class="btn-studio-outline" style="font-size:12px;">
        <i class="bi bi-eye"></i> Live hiện tại
      </a>
      <button class="btn-studio-primary" id="pub-btn-<?= $lid ?>"
              onclick="publishLesson(<?= $lid ?>)">
        <i class="bi bi-send-fill"></i> Publish Bài <?= $group['lesson_no'] ?>
      </button>
    </div>
  </div>

  <table class="table table-sm mb-0" style="font-size:13px;">
    <thead class="table-light">
      <tr>
        <th>#</th><th>Loại</th><th>Tiêu đề</th><th>Preview</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($group['items'] as $i => $d): ?>
      <tr>
        <td class="text-muted"><?= $i+1 ?></td>
        <td><span class="draft-badge draft-badge--<?= $d['type'] ?>"><?= $type_labels[$d['type']] ?? $d['type'] ?></span></td>
        <td><?= htmlspecialchars($d['title']) ?></td>
        <td>
          <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $lid ?>&draft_id=<?= $d['id'] ?>"
             class="text-muted small"><i class="bi bi-pencil-square"></i></a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endforeach; ?>
<?php endif; ?>

<div class="studio-toast" id="toast"></div>

<?php
$page_scripts = <<<JS
<script>
const BASE_URL = '$base_url';

function toast(msg, ok=true) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.background = ok ? '#1a1f2e' : '#c53030';
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 3000);
}

async function publishLesson(lessonId) {
    const btn = document.getElementById('pub-btn-' + lessonId);
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> Đang publish...';
    try {
        const r = await fetch(BASE_URL + '/api/studio/publish.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({lesson_id: lessonId})
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);
        toast('Publish thành công! ' + d.count + ' activities đã đưa vào live.');
        setTimeout(() => location.reload(), 1200);
    } catch(e) {
        toast(e.message || 'Lỗi publish', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill"></i> Publish';
    }
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
