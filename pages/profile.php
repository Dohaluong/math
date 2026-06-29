<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$student_id = STUDENT_ID;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $name = trim(htmlspecialchars($_POST['name']));
    if (mb_strlen($name) >= 2 && mb_strlen($name) <= 50) {
        $upd = $db->prepare('UPDATE student SET name = ? WHERE id = ?');
        $upd->execute([$name, $student_id]);
        $message = 'success';
    }
}

$student = $db->prepare('SELECT * FROM student WHERE id = ?');
$student->execute([$student_id]);
$student = $student->fetch();

// Stats
$stats_stmt = $db->prepare('
    SELECT COUNT(*) AS total_done,
           COALESCE(SUM(score),0) AS total_correct,
           COALESCE(SUM(total),0) AS total_questions,
           COALESCE(SUM(study_time),0) AS total_time
    FROM progress WHERE student_id = ?
');
$stats_stmt->execute([$student_id]);
$stats = $stats_stmt->fetch();

$page_title  = 'Hồ sơ';
$active_page = 'profile';
include '../includes/header.php';
?>

<div class="container-narrow">
  <h2 class="fw-bold mb-4">Hồ sơ học sinh</h2>

  <!-- Avatar + name -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center py-4">
      <div class="avatar-large mx-auto mb-3">
        <i class="bi bi-person-fill text-primary" style="font-size:2.5rem;"></i>
      </div>
      <h4 class="fw-bold mb-0"><?= htmlspecialchars($student['name']) ?></h4>
      <p class="text-muted small mt-1">Toán 7 · Kết nối tri thức</p>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-primary"><?= $stats['total_done'] ?></div>
        <div class="small text-muted">Bài đã học</div>
      </div>
    </div>
    <div class="col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <?php $acc = $stats['total_questions'] > 0 ? round($stats['total_correct']/$stats['total_questions']*100) : 0; ?>
        <div class="fs-3 fw-bold text-success"><?= $acc ?>%</div>
        <div class="small text-muted">Độ chính xác</div>
      </div>
    </div>
    <div class="col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-warning"><?= $stats['total_correct'] ?></div>
        <div class="small text-muted">Câu trả lời đúng</div>
      </div>
    </div>
    <div class="col-6">
      <div class="card border-0 shadow-sm text-center py-3">
        <?php $mins = $stats['total_time'] > 0 ? round($stats['total_time']/60) : 0; ?>
        <div class="fs-3 fw-bold text-info"><?= $mins ?></div>
        <div class="small text-muted">Phút học tập</div>
      </div>
    </div>
  </div>

  <!-- Edit name -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h6 class="fw-bold mb-3">Đổi tên</h6>
      <?php if ($message === 'success'): ?>
      <div class="alert alert-success py-2 small">Đã cập nhật tên thành công!</div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label small text-muted">Tên học sinh</label>
          <input type="text" name="name" class="form-control"
                 value="<?= htmlspecialchars($student['name']) ?>"
                 minlength="2" maxlength="50" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Lưu thay đổi</button>
      </form>
    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
