<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$student_id = STUDENT_ID;

$stmt = $db->prepare('
    SELECT p.*, l.chapter_no, l.chapter_title, l.lesson_no, l.title AS lesson_title
    FROM progress p
    JOIN lesson l ON p.lesson_id = l.id
    WHERE p.student_id = ?
    ORDER BY p.last_learning DESC
');
$stmt->execute([$student_id]);
$history = $stmt->fetchAll();

$page_title  = 'Lịch sử học';
$active_page = 'history';
include '../includes/header.php';
?>

<div class="container-narrow">
  <h2 class="fw-bold mb-1">Lịch sử học</h2>
  <p class="text-muted mb-4"><?= count($history) ?> bài đã hoàn thành</p>

  <?php if (empty($history)): ?>
  <div class="text-center py-5">
    <i class="bi bi-clock-history text-muted" style="font-size:3rem;"></i>
    <p class="text-muted mt-3">Chưa có bài học nào.<br>
       <a href="<?= BASE_URL ?>/pages/math7.php">Bắt đầu học ngay!</a>
    </p>
  </div>
  <?php else: ?>

  <div class="list-group gap-2">
    <?php foreach ($history as $item): ?>
    <?php
      $pct = $item['total'] > 0 ? round($item['score']/$item['total']*100) : 0;
      $badge_class = $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-warning text-dark' : 'bg-danger');
    ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="score-badge <?= $badge_class ?>">
          <?= $item['score'] ?>/<?= $item['total'] ?>
        </div>
        <div class="flex-grow-1 min-w-0">
          <div class="fw-semibold text-truncate">
            Bài <?= $item['lesson_no'] ?>. <?= htmlspecialchars($item['lesson_title']) ?>
          </div>
          <div class="small text-muted mt-1">
            Chương <?= $item['chapter_no'] ?>
            · <?= date('d/m/Y H:i', strtotime($item['last_learning'])) ?>
            <?php if ($item['study_time'] > 0): ?>
            · <?= gmdate('i:s', $item['study_time']) ?> phút
            <?php endif; ?>
          </div>
        </div>
        <div class="d-flex gap-1 flex-shrink-0">
          <a href="<?= BASE_URL ?>/pages/review.php?lesson_id=<?= $item['lesson_id'] ?>"
             class="btn btn-sm btn-outline-secondary" title="Xem kết quả">
            <i class="bi bi-eye"></i>
          </a>
          <a href="<?= BASE_URL ?>/pages/practice.php?lesson_id=<?= $item['lesson_id'] ?>"
             class="btn btn-sm btn-outline-primary" title="Làm lại">
            <i class="bi bi-arrow-repeat"></i>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
