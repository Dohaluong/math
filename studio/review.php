<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

$filter_status  = $_GET['status']    ?? 'draft';
$filter_lesson  = (int)($_GET['lesson'] ?? 0);

$where  = 'WHERE d.status = ?';
$params = [$filter_status];
if ($filter_lesson) { $where .= ' AND d.lesson_id = ?'; $params[] = $filter_lesson; }

$drafts = $db->prepare("
    SELECT d.*, l.title AS lesson_title, l.lesson_no
    FROM ls_draft d
    JOIN lesson l ON l.id = d.lesson_id
    $where
    ORDER BY d.lesson_id, d.sort_order, d.id
");
$drafts->execute($params);
$drafts = $drafts->fetchAll();

$lessons = $db->query('SELECT id, lesson_no, title FROM lesson ORDER BY lesson_no')->fetchAll();

$status_counts = $db->query("SELECT status, COUNT(*) AS cnt FROM ls_draft GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

$type_labels = [
    'introduction'    => 'Mở đầu',
    'theory'          => 'Kiến thức',
    'example'         => 'Ví dụ',
    'ai_explanation'  => 'Hỏi AI',
    'quiz'            => 'Quiz',
    'guided_practice' => 'Luyện tập',
    'fill_answer'     => 'Điền đáp án',
    'reflection'      => 'Nhìn lại',
    'summary'         => 'Tóm tắt',
];

$page_title    = 'Review Queue';
$active_studio = 'review';
$base_url = BASE_URL;
include '../includes/studio_header.php';
?>

<!-- Filters -->
<div class="d-flex gap-3 mb-4 flex-wrap align-items-center">
  <div class="d-flex gap-2">
    <?php
    $statuses = ['draft' => 'Draft', 'approved' => 'Approved', 'rejected' => 'Rejected', 'published' => 'Published'];
    foreach ($statuses as $s => $label):
        $cnt = $status_counts[$s] ?? 0;
        $active = $filter_status === $s;
    ?>
    <a href="?status=<?= $s ?><?= $filter_lesson ? '&lesson='.$filter_lesson : '' ?>"
       class="btn btn-sm <?= $active ? 'btn-dark' : 'btn-outline-secondary' ?>">
      <?= $label ?>
      <?php if ($cnt > 0): ?><span class="badge <?= $active ? 'bg-white text-dark' : 'bg-secondary' ?> ms-1"><?= $cnt ?></span><?php endif; ?>
    </a>
    <?php endforeach; ?>
  </div>
  <select class="form-select form-select-sm" style="max-width:220px;"
          onchange="location.href='?status=<?= $filter_status ?>&lesson='+this.value">
    <option value="0">Tất cả bài học</option>
    <?php foreach ($lessons as $l): ?>
    <option value="<?= $l['id'] ?>" <?= $filter_lesson === $l['id'] ? 'selected' : '' ?>>
      Bài <?= $l['lesson_no'] ?> — <?= htmlspecialchars($l['title']) ?>
    </option>
    <?php endforeach; ?>
  </select>
  <?php if ($filter_status === 'draft' && !empty($drafts)): ?>
  <button class="btn btn-sm btn-success ms-auto" onclick="approveAll()">
    <i class="bi bi-check-all me-1"></i>Approve tất cả
  </button>
  <?php endif; ?>
</div>

<!-- Draft table -->
<?php if (empty($drafts)): ?>
<div class="studio-card text-center py-5 text-muted">
  <i class="bi bi-inbox" style="font-size:2.5rem; opacity:.3; display:block; margin-bottom:8px;"></i>
  Không có draft nào với trạng thái "<?= $filter_status ?>".
</div>
<?php else: ?>
<div class="studio-card" style="padding:0; overflow:hidden;">
  <table class="table table-hover mb-0" style="font-size:13px;">
    <thead class="table-light">
      <tr>
        <th style="padding-left:16px;">Bài</th>
        <th>Loại</th>
        <th>Tiêu đề</th>
        <th>Trạng thái</th>
        <th>Cập nhật</th>
        <th class="text-end" style="padding-right:16px;">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($drafts as $d): ?>
      <tr id="row-<?= $d['id'] ?>">
        <td style="padding-left:16px; color:#718096;">Bài <?= $d['lesson_no'] ?></td>
        <td>
          <span class="draft-badge draft-badge--<?= $d['type'] ?>">
            <?= $type_labels[$d['type']] ?? $d['type'] ?>
          </span>
        </td>
        <td class="fw-medium"><?= htmlspecialchars($d['title']) ?></td>
        <td><span class="draft-status draft-status--<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
        <td class="text-muted"><?= date('d/m H:i', strtotime($d['updated_at'])) ?></td>
        <td class="text-end" style="padding-right:16px;">
          <div class="d-flex gap-1 justify-content-end flex-wrap">
            <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $d['lesson_id'] ?>&draft_id=<?= $d['id'] ?>"
               class="btn-studio-outline" style="font-size:11px; padding:3px 10px;">
              <i class="bi bi-pencil"></i> Sửa
            </a>
            <?php if ($d['status'] === 'draft'): ?>
            <button class="btn btn-sm btn-success" style="font-size:11px;"
                    onclick="setStatus(<?= $d['id'] ?>, 'approved')">
              <i class="bi bi-check-lg"></i> Approve
            </button>
            <button class="btn btn-sm btn-outline-danger" style="font-size:11px;"
                    onclick="setStatus(<?= $d['id'] ?>, 'rejected')">
              <i class="bi bi-x-lg"></i> Reject
            </button>
            <?php elseif ($d['status'] === 'approved'): ?>
            <button class="btn btn-sm btn-outline-secondary" style="font-size:11px;"
                    onclick="setStatus(<?= $d['id'] ?>, 'draft')">
              <i class="bi bi-arrow-counterclockwise"></i> Unapprove
            </button>
            <?php elseif ($d['status'] === 'rejected'): ?>
            <button class="btn btn-sm btn-outline-success" style="font-size:11px;"
                    onclick="setStatus(<?= $d['id'] ?>, 'draft')">
              <i class="bi bi-arrow-counterclockwise"></i> Reopen
            </button>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php if (!empty($d['notes'])): ?>
      <tr style="background:#fffbeb;">
        <td colspan="6" style="padding:4px 16px; font-size:12px; color:#744210;">
          <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($d['notes']) ?>
        </td>
      </tr>
      <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
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
    setTimeout(() => el.classList.remove('show'), 2800);
}

async function setStatus(draftId, status) {
    const r = await fetch(BASE_URL + '/api/studio/approve_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId, status})
    });
    const d = await r.json();
    if (d.ok) {
        toast('Đã cập nhật: ' + status);
        document.getElementById('row-' + draftId)?.remove();
    } else toast(d.error || 'Lỗi', false);
}

async function approveAll() {
    const rows = document.querySelectorAll('tbody tr[id^="row-"]');
    for (const row of rows) {
        const id = parseInt(row.id.replace('row-',''));
        await setStatus(id, 'approved');
    }
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
