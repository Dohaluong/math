<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$student_id = STUDENT_ID;

$student = $db->prepare('SELECT name FROM student WHERE id = ?');
$student->execute([$student_id]);
$student = $student->fetch();
$name = $student ? $student['name'] : 'Bạn';

// Last studied lesson
$last_stmt = $db->prepare('
    SELECT p.score, p.total, p.last_learning, l.id AS lesson_id,
           l.chapter_no, l.lesson_no, l.title
    FROM progress p
    JOIN lesson l ON p.lesson_id = l.id
    WHERE p.student_id = ?
    ORDER BY p.last_learning DESC
    LIMIT 1
');
$last_stmt->execute([$student_id]);
$last = $last_stmt->fetch();

// Next unfinished lesson
$next_stmt = $db->prepare('
    SELECT l.*
    FROM lesson l
    LEFT JOIN progress p ON l.id = p.lesson_id AND p.student_id = ?
    WHERE p.id IS NULL
    ORDER BY l.chapter_no ASC, l.lesson_no ASC
    LIMIT 1
');
$next_stmt->execute([$student_id]);
$next_lesson = $next_stmt->fetch();

// Total lessons & done
$total_count = $db->query('SELECT COUNT(*) FROM lesson')->fetchColumn();
$done_count  = $db->prepare('SELECT COUNT(*) FROM progress WHERE student_id = ?');
$done_count->execute([$student_id]);
$done_count = $done_count->fetchColumn();

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Chào buổi sáng' : ($hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối');

$page_title  = 'Trang chủ';
$active_page = 'home';
include '../includes/header.php';
?>

<div class="container-narrow">

  <!-- Greeting -->
  <div class="mb-4">
    <h2 class="fw-bold mb-1"><?= htmlspecialchars($greeting) ?>, <?= htmlspecialchars($name) ?>!</h2>
    <p class="text-muted mb-0"><?= strftime_vi(date('l, d/m/Y')) ?></p>
  </div>

  <!-- Progress bar overall -->
  <?php if ($total_count > 0): ?>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold text-secondary small">TIẾN ĐỘ HỌC KỲ 1</span>
        <span class="fw-bold text-primary"><?= $done_count ?>/<?= $total_count ?> bài</span>
      </div>
      <div class="progress" style="height:10px;border-radius:8px;">
        <div class="progress-bar bg-primary" style="width:<?= $total_count > 0 ? round($done_count/$total_count*100) : 0 ?>%;border-radius:8px;"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Continue card -->
  <?php if ($last): ?>
  <div class="card border-0 shadow-sm mb-3 card-hover">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="icon-circle bg-primary-soft text-primary">
        <i class="bi bi-arrow-right-circle-fill fs-4"></i>
      </div>
      <div class="flex-grow-1 min-w-0">
        <div class="small text-muted mb-1">TIẾP TỤC HỌC</div>
        <div class="fw-semibold text-truncate">
          Bài <?= $last['lesson_no'] ?>. <?= htmlspecialchars($last['title']) ?>
        </div>
        <div class="small text-muted mt-1">
          Lần trước: <?= $last['score'] ?>/<?= $last['total'] ?> câu đúng
          · <?= date('d/m', strtotime($last['last_learning'])) ?>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $last['lesson_id'] ?>"
         class="btn btn-primary btn-sm px-3 flex-shrink-0">Học lại</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- Next lesson card -->
  <?php if ($next_lesson): ?>
  <div class="card border-0 shadow-sm mb-3 card-hover">
    <div class="card-body d-flex align-items-center gap-3">
      <div class="icon-circle bg-success-soft text-success">
        <i class="bi bi-lightbulb-fill fs-4"></i>
      </div>
      <div class="flex-grow-1 min-w-0">
        <div class="small text-muted mb-1">BÀI HỌC TIẾP THEO</div>
        <div class="fw-semibold text-truncate">
          Bài <?= $next_lesson['lesson_no'] ?>. <?= htmlspecialchars($next_lesson['title']) ?>
        </div>
        <div class="small text-muted mt-1">
          Chương <?= $next_lesson['chapter_no'] ?> — <?= htmlspecialchars($next_lesson['chapter_title']) ?>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $next_lesson['id'] ?>"
         class="btn btn-success btn-sm px-3 flex-shrink-0">Bắt đầu</a>
    </div>
  </div>
  <?php elseif ($done_count > 0): ?>
  <div class="alert alert-success border-0 shadow-sm">
    <i class="bi bi-trophy-fill me-2"></i>
    <strong>Tuyệt vời!</strong> Bạn đã hoàn thành tất cả các bài học.
  </div>
  <?php endif; ?>

  <!-- Quick links -->
  <div class="row g-3 mt-2">
    <div class="col-6">
      <a href="<?= BASE_URL ?>/pages/math7.php" class="card border-0 shadow-sm card-hover text-decoration-none">
        <div class="card-body text-center py-4">
          <i class="bi bi-grid-3x3-gap-fill text-primary fs-3 mb-2 d-block"></i>
          <div class="fw-semibold text-dark">Tất cả bài học</div>
          <div class="small text-muted">Toán 7</div>
        </div>
      </a>
    </div>
    <div class="col-6">
      <a href="<?= BASE_URL ?>/pages/history.php" class="card border-0 shadow-sm card-hover text-decoration-none">
        <div class="card-body text-center py-4">
          <i class="bi bi-clock-history text-warning fs-3 mb-2 d-block"></i>
          <div class="fw-semibold text-dark">Lịch sử học</div>
          <div class="small text-muted"><?= $done_count ?> bài đã học</div>
        </div>
      </a>
    </div>
  </div>

</div>

<?php
include '../includes/footer.php';

function strftime_vi(string $str): string {
    $days = ['Sunday'=>'Chủ nhật','Monday'=>'Thứ hai','Tuesday'=>'Thứ ba',
             'Wednesday'=>'Thứ tư','Thursday'=>'Thứ năm','Friday'=>'Thứ sáu','Saturday'=>'Thứ bảy'];
    foreach ($days as $en => $vi) $str = str_replace($en, $vi, $str);
    return $str;
}
?>
