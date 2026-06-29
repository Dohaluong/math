<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$lesson_id  = (int)($_GET['lesson_id'] ?? 0);
$student_id = STUDENT_ID;

if (!$lesson_id) { header('Location: ' . BASE_URL . '/pages/math7.php'); exit; }

$lesson_stmt = $db->prepare('SELECT * FROM lesson WHERE id = ?');
$lesson_stmt->execute([$lesson_id]);
$lesson = $lesson_stmt->fetch();
if (!$lesson) { header('Location: ' . BASE_URL . '/pages/math7.php'); exit; }

$q_stmt = $db->prepare('SELECT * FROM question WHERE lesson_id = ? ORDER BY display_order ASC LIMIT 10');
$q_stmt->execute([$lesson_id]);
$questions = $q_stmt->fetchAll();

if (empty($questions)) {
    header('Location: ' . BASE_URL . '/pages/lesson.php?id=' . $lesson_id);
    exit;
}

$questions_json = json_encode($questions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);

$page_title  = 'Luyện tập — ' . $lesson['title'];
$active_page = 'math7';

$base_url = BASE_URL;
$page_scripts = <<<JS
<script>
const QUESTIONS   = $questions_json;
const BASE_URL    = '$base_url';
const LESSON_ID   = $lesson_id;
const STUDENT_ID  = $student_id;
</script>
<script src="$base_url/assets/js/practice.js"></script>
JS;

include '../includes/header.php';
?>

<div class="container-narrow">

  <!-- Header -->
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $lesson_id ?>" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left"></i>
    </a>
    <div>
      <div class="small text-muted">Bài <?= $lesson['lesson_no'] ?></div>
      <div class="fw-bold"><?= htmlspecialchars($lesson['title']) ?></div>
    </div>
    <div class="ms-auto text-end">
      <div class="small text-muted">Thời gian</div>
      <div class="fw-semibold text-primary" id="timer">00:00</div>
    </div>
  </div>

  <!-- Progress -->
  <div class="mb-3">
    <div class="d-flex justify-content-between small text-muted mb-1">
      <span>Câu <span id="q-current">1</span> / <?= count($questions) ?></span>
      <span id="q-answered">0 đã trả lời</span>
    </div>
    <div class="progress" style="height:6px;border-radius:4px;">
      <div id="q-progress" class="progress-bar bg-primary" style="width:0%;border-radius:4px;transition:width .3s;"></div>
    </div>
  </div>

  <!-- Question card -->
  <div id="quiz-container">
    <!-- Rendered by practice.js -->
  </div>

  <!-- Navigation buttons -->
  <div class="d-flex gap-2 mt-3">
    <button id="btn-prev" class="btn btn-outline-secondary flex-fill" onclick="prevQuestion()" disabled>
      <i class="bi bi-arrow-left me-1"></i>Trước
    </button>
    <button id="btn-next" class="btn btn-primary flex-fill" onclick="nextQuestion()">
      Tiếp <i class="bi bi-arrow-right ms-1"></i>
    </button>
    <button id="btn-submit" class="btn btn-success flex-fill d-none" onclick="submitQuiz()">
      <i class="bi bi-check2-circle me-1"></i>Nộp bài
    </button>
  </div>

  <!-- Hint toggle -->
  <div class="mt-3 text-center">
    <button class="btn btn-sm btn-link text-muted text-decoration-none" onclick="toggleHint()">
      <i class="bi bi-lightbulb me-1"></i>Xem gợi ý
    </button>
  </div>
  <div id="hint-box" class="alert alert-warning small mt-2 d-none"></div>

</div>

<?php include '../includes/footer.php'; ?>
