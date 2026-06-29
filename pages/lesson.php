<?php
require_once '../config.php';
require_once '../db.php';
require_once '../modules/activity/render.php';

$db = get_db();
$lesson_id  = (int)($_GET['id'] ?? 0);
$student_id = STUDENT_ID;

if (!$lesson_id) { header('Location: ' . BASE_URL . '/pages/math7.php'); exit; }

$lesson_stmt = $db->prepare('SELECT * FROM lesson WHERE id = ?');
$lesson_stmt->execute([$lesson_id]);
$lesson = $lesson_stmt->fetch();
if (!$lesson) { header('Location: ' . BASE_URL . '/pages/math7.php'); exit; }

// Load activities (new engine)
$act_stmt = $db->prepare('SELECT * FROM activity WHERE lesson_id = ? ORDER BY sort_order ASC');
$act_stmt->execute([$lesson_id]);
$activities = $act_stmt->fetchAll();

// Completed activity IDs for this student
$done_stmt = $db->prepare('SELECT activity_id FROM activity_progress WHERE student_id = ? AND is_complete = 1');
$done_stmt->execute([$student_id]);
$completed_ids = array_column($done_stmt->fetchAll(), 'activity_id');

// If no activities: fall back to legacy lesson_blocks view
$has_activities = !empty($activities);

if (!$has_activities) {
    // ── LEGACY MODE ──────────────────────────────────────
    $blocks_stmt = $db->prepare('SELECT * FROM lesson_block WHERE lesson_id = ? ORDER BY display_order ASC');
    $blocks_stmt->execute([$lesson_id]);
    $blocks = $blocks_stmt->fetchAll();

    $prog_stmt = $db->prepare('SELECT * FROM progress WHERE student_id = ? AND lesson_id = ?');
    $prog_stmt->execute([$student_id, $lesson_id]);
    $progress = $prog_stmt->fetch();

    $q_count = $db->prepare('SELECT COUNT(*) FROM question WHERE lesson_id = ?');
    $q_count->execute([$lesson_id]);
    $q_count = $q_count->fetchColumn();

    $block_meta = [
        'introduction' => ['label'=>'Mở đầu',    'icon'=>'bi-door-open',         'color'=>'intro'],
        'concept'      => ['label'=>'Kiến thức',  'icon'=>'bi-mortarboard-fill',  'color'=>'concept'],
        'example'      => ['label'=>'Ví dụ',      'icon'=>'bi-pencil-square',     'color'=>'example'],
        'note'         => ['label'=>'Lưu ý',      'icon'=>'bi-exclamation-circle','color'=>'note'],
        'exercise'     => ['label'=>'Bài tập',    'icon'=>'bi-check2-square',     'color'=>'exercise'],
        'summary'      => ['label'=>'Tóm tắt',    'icon'=>'bi-bookmark-star-fill','color'=>'summary'],
    ];

    $page_title  = $lesson['title'];
    $active_page = 'math7';
    include '../includes/header.php';
?>
<div class="container-narrow">
  <nav class="mb-3">
    <a href="<?= BASE_URL ?>/pages/math7.php" class="text-decoration-none text-muted small">
      <i class="bi bi-arrow-left me-1"></i>Toán 7
    </a>
  </nav>
  <div class="mb-4">
    <div class="small text-primary fw-semibold mb-1">BÀI <?= $lesson['lesson_no'] ?></div>
    <h2 class="fw-bold mb-2"><?= htmlspecialchars($lesson['title']) ?></h2>
    <?php if ($progress): ?>
    <span class="badge bg-success-soft text-success">
      <i class="bi bi-check-circle me-1"></i>Đã hoàn thành: <?= $progress['score'] ?>/<?= $progress['total'] ?> câu
    </span>
    <?php endif; ?>
  </div>
  <?php foreach ($blocks as $block): ?>
  <?php $meta = $block_meta[$block['block_type']] ?? ['label'=>'','icon'=>'bi-square','color'=>'intro']; ?>
  <div class="lesson-block lesson-block--<?= $meta['color'] ?> mb-4">
    <div class="lesson-block__header">
      <i class="bi <?= $meta['icon'] ?> me-2"></i><?= $meta['label'] ?>
      <?php if ($block['title']): ?><span class="fw-semibold ms-2">— <?= htmlspecialchars($block['title']) ?></span><?php endif; ?>
    </div>
    <div class="lesson-block__body mathjax-content"><?= $block['content'] ?></div>
  </div>
  <?php endforeach; ?>
  <?php if ($q_count > 0): ?>
  <div class="card border-0 shadow-sm bg-primary text-white mb-4">
    <div class="card-body d-flex align-items-center gap-3 py-4">
      <div>
        <div class="fw-bold fs-5 mb-1">Sẵn sàng luyện tập?</div>
        <div class="opacity-75 small"><?= $q_count ?> câu hỏi · khoảng 10 phút</div>
      </div>
      <a href="<?= BASE_URL ?>/pages/practice.php?lesson_id=<?= $lesson_id ?>"
         class="btn btn-white ms-auto fw-semibold px-4">
        Bắt đầu <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php
    include '../includes/footer.php';
    exit;
}

// ── ACTIVITY ENGINE MODE ─────────────────────────────────────
$total_acts    = count($activities);
$act_ids       = array_column($activities, 'id');
$completed_cnt = count(array_intersect($act_ids, $completed_ids));
$all_done      = $total_acts > 0 && $completed_cnt >= $total_acts;

$start_idx = 0;
if ($all_done) {
    // All activities done — engine will go straight to completion screen
    $start_idx = $total_acts;
} else {
    foreach ($activities as $i => $act) {
        if (!in_array($act['id'], $completed_ids)) { $start_idx = $i; break; }
    }
}

$activities_json = json_encode(array_map(fn($a) => [
    'id'   => (int)$a['id'],
    'type' => $a['type'],
    'data' => json_decode($a['data'], true),
], $activities), JSON_UNESCAPED_UNICODE);

$page_title  = $lesson['title'];
$active_page = 'math7';

$completed_ids_json = json_encode($completed_ids);
$base_url = BASE_URL;
$page_scripts = <<<JS
<script>
const ACTIVITIES    = $activities_json;
const LESSON_ID     = $lesson_id;
const STUDENT_ID    = $student_id;
const BASE_URL      = '$base_url';
const START_IDX     = $start_idx;
const COMPLETED_IDS = $completed_ids_json;
</script>
<script src="$base_url/assets/js/activity_engine.js"></script>
JS;

include '../includes/header.php';
?>

<div class="container-narrow">

  <!-- Lesson header -->
  <div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= BASE_URL ?>/pages/math7.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1 min-w-0">
      <div class="small text-muted">Bài <?= $lesson['lesson_no'] ?></div>
      <div class="fw-bold text-truncate"><?= htmlspecialchars($lesson['title']) ?></div>
    </div>
  </div>

  <!-- Progress bar -->
  <div class="mb-4">
    <div class="d-flex justify-content-between small text-muted mb-1">
      <span id="act-label">Hoạt động 1 / <?= $total_acts ?></span>
      <span id="act-type-label"></span>
    </div>
    <div class="progress" style="height:6px;border-radius:4px;">
      <div id="act-progress-bar" class="progress-bar bg-primary" style="width:0%;border-radius:4px;transition:width .35s;"></div>
    </div>
    <!-- Step dots -->
    <div class="act-dots mt-2">
      <?php foreach ($activities as $i => $act): ?>
      <div class="act-dot <?= in_array($act['id'], $completed_ids) ? 'act-dot--done' : '' ?>"
           id="dot-<?= $i ?>" title="<?= htmlspecialchars($act['title'] ?? $act['type']) ?>"></div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Activity steps (all rendered, JS shows current) -->
  <div id="activity-wrapper">
    <?php foreach ($activities as $i => $act): ?>
    <div class="activity-step" data-idx="<?= $i ?>" data-type="<?= htmlspecialchars($act['type']) ?>"
         data-activity-id="<?= $act['id'] ?>" style="display:none;">
      <?= render_activity($act, $i, $i === $total_acts - 1, $lesson_id) ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Completion screen -->
  <div id="completion-screen" class="d-none text-center py-5">
    <div style="font-size:4rem;">🎉</div>
    <h2 class="fw-bold mt-3 mb-2">Hoàn thành bài học!</h2>
    <p class="text-muted">Bài <?= $lesson['lesson_no'] ?> — <?= htmlspecialchars($lesson['title']) ?></p>
    <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
      <?php
      $next_stmt = $db->prepare('SELECT id, lesson_no, title FROM lesson WHERE chapter_no = ? AND lesson_no > ? ORDER BY lesson_no ASC LIMIT 1');
      $next_stmt->execute([$lesson['chapter_no'], $lesson['lesson_no']]);
      $next_lesson = $next_stmt->fetch();
      ?>
      <?php if ($next_lesson): ?>
      <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $next_lesson['id'] ?>" class="btn btn-primary px-4">
        Bài tiếp theo <i class="bi bi-arrow-right ms-1"></i>
      </a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/pages/math7.php" class="btn btn-outline-secondary px-4">
        Danh sách bài
      </a>
      <button class="btn btn-outline-secondary px-4" onclick="resetLesson(<?= $lesson_id ?>)">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Học lại
      </button>
    </div>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
