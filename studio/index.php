<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

// Stats
$stats = [
    'lessons'   => $db->query('SELECT COUNT(*) FROM lesson')->fetchColumn(),
    'activities' => $db->query('SELECT COUNT(*) FROM activity')->fetchColumn(),
    'drafts'    => $db->query('SELECT COUNT(*) FROM ls_draft WHERE status="draft"')->fetchColumn(),
    'approved'  => $db->query('SELECT COUNT(*) FROM ls_draft WHERE status="approved"')->fetchColumn(),
    'published' => $db->query('SELECT COUNT(*) FROM ls_draft WHERE status="published"')->fetchColumn(),
    'prompts'   => $db->query('SELECT COUNT(*) FROM ls_prompt')->fetchColumn(),
];

// Recent activity by lesson
$lessons = $db->query('
    SELECT l.id, l.lesson_no, l.title,
           COUNT(DISTINCT a.id) AS live_count,
           COUNT(DISTINCT d.id) AS draft_count
    FROM lesson l
    LEFT JOIN activity  a ON a.lesson_id = l.id
    LEFT JOIN ls_draft  d ON d.lesson_id = l.id AND d.status IN ("draft","approved")
    GROUP BY l.id
    ORDER BY l.lesson_no
')->fetchAll();

$page_title   = 'Dashboard';
$active_studio = 'dashboard';
include '../includes/studio_header.php';
?>

<div class="studio-stats">
  <div class="studio-stat">
    <div class="studio-stat__value"><?= $stats['lessons'] ?></div>
    <div class="studio-stat__label">Bài học</div>
  </div>
  <div class="studio-stat">
    <div class="studio-stat__value"><?= $stats['activities'] ?></div>
    <div class="studio-stat__label">Activities (live)</div>
  </div>
  <div class="studio-stat">
    <div class="studio-stat__value" style="color:#e8a838;"><?= $stats['drafts'] ?></div>
    <div class="studio-stat__label">Chờ review</div>
  </div>
  <div class="studio-stat">
    <div class="studio-stat__value" style="color:#38a169;"><?= $stats['approved'] ?></div>
    <div class="studio-stat__label">Đã duyệt</div>
  </div>
  <div class="studio-stat">
    <div class="studio-stat__value" style="color:#3182ce;"><?= $stats['published'] ?></div>
    <div class="studio-stat__label">Đã publish</div>
  </div>
  <div class="studio-stat">
    <div class="studio-stat__value" style="color:#805ad5;"><?= $stats['prompts'] ?></div>
    <div class="studio-stat__label">AI Prompts</div>
  </div>
</div>

<div class="row g-4">
  <!-- Lesson overview -->
  <div class="col-lg-7">
    <div class="studio-card">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0">Bài học</h6>
        <a href="<?= BASE_URL ?>/studio/lessons.php" class="btn-studio-outline" style="font-size:12px; padding:4px 12px;">
          Xem tất cả
        </a>
      </div>
      <table class="table table-sm table-hover mb-0" style="font-size:13px;">
        <thead class="table-light">
          <tr>
            <th>Bài</th>
            <th>Tên</th>
            <th class="text-center">Live</th>
            <th class="text-center">Draft</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lessons as $l): ?>
          <tr>
            <td class="text-muted fw-semibold"><?= $l['lesson_no'] ?></td>
            <td><?= htmlspecialchars($l['title']) ?></td>
            <td class="text-center">
              <?php if ($l['live_count'] > 0): ?>
              <span class="badge bg-primary-subtle text-primary"><?= $l['live_count'] ?></span>
              <?php else: ?>
              <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($l['draft_count'] > 0): ?>
              <span class="badge bg-warning-subtle text-warning"><?= $l['draft_count'] ?></span>
              <?php else: ?>
              <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $l['id'] ?>"
                 class="btn-studio-primary" style="font-size:11px; padding:3px 10px;">
                <i class="bi bi-stars"></i> AI Studio
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Quick actions -->
  <div class="col-lg-5">
    <div class="studio-card mb-3">
      <h6 class="fw-bold mb-3">Thao tác nhanh</h6>
      <div class="d-grid gap-2">
        <a href="<?= BASE_URL ?>/studio/ai_studio.php" class="btn-studio-primary">
          <i class="bi bi-stars"></i> Mở AI Studio
        </a>
        <a href="<?= BASE_URL ?>/studio/review.php" class="btn-studio-outline">
          <i class="bi bi-check2-square"></i> Review Queue
          <?php if ($stats['drafts'] > 0): ?>
          <span class="badge bg-warning text-dark ms-1"><?= $stats['drafts'] ?></span>
          <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/studio/publish.php" class="btn-studio-outline">
          <i class="bi bi-send-fill"></i> Publish Center
          <?php if ($stats['approved'] > 0): ?>
          <span class="badge bg-success ms-1"><?= $stats['approved'] ?></span>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <div class="studio-card">
      <h6 class="fw-bold mb-3">Workflow</h6>
      <div style="font-size:12px; color:#4a5568; line-height:2;">
        <div><span class="badge bg-secondary me-2">1</span> Chọn bài học trong AI Studio</div>
        <div><span class="badge bg-secondary me-2">2</span> Tạo outline (hoặc chọn blueprint)</div>
        <div><span class="badge bg-secondary me-2">3</span> Generate từng activity</div>
        <div><span class="badge bg-secondary me-2">4</span> Review &amp; Approve trong Review Queue</div>
        <div><span class="badge bg-secondary me-2">5</span> Publish vào Publish Center</div>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/studio_footer.php'; ?>
