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

// Get latest answers for this student + lesson
$answers_stmt = $db->prepare('
    SELECT al.id AS log_id, al.selected_answer, al.is_correct, al.created_at,
           q.id AS question_id, q.question, q.option_a, q.option_b, q.option_c, q.option_d,
           q.correct_answer, q.explanation, q.hint, q.display_order
    FROM answer_log al
    JOIN question q ON al.question_id = q.id
    WHERE al.student_id = ? AND q.lesson_id = ?
    AND al.created_at = (
        SELECT MAX(al2.created_at)
        FROM answer_log al2
        JOIN question q2 ON al2.question_id = q2.id
        WHERE al2.student_id = al.student_id AND q2.lesson_id = q.lesson_id AND al2.question_id = al.question_id
    )
    ORDER BY q.display_order ASC
');
$answers_stmt->execute([$student_id, $lesson_id]);
$answers = $answers_stmt->fetchAll();

// Get progress
$prog_stmt = $db->prepare('SELECT * FROM progress WHERE student_id = ? AND lesson_id = ?');
$prog_stmt->execute([$student_id, $lesson_id]);
$progress = $prog_stmt->fetch();

$score    = $progress ? $progress['score'] : array_sum(array_column($answers, 'is_correct'));
$total    = $progress ? $progress['total']  : count($answers);
$pct      = $total > 0 ? round($score / $total * 100) : 0;

$option_labels = ['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'];

$page_title  = 'Kết quả — ' . $lesson['title'];
$active_page = 'math7';
include '../includes/header.php';
?>

<div class="container-narrow">

  <!-- Back -->
  <nav class="mb-3">
    <a href="<?= BASE_URL ?>/pages/math7.php" class="text-decoration-none text-muted small">
      <i class="bi bi-arrow-left me-1"></i>Toán 7
    </a>
  </nav>

  <!-- Score card -->
  <div class="card border-0 shadow-sm mb-4 text-center">
    <div class="card-body py-4">
      <div class="score-circle mx-auto mb-3 <?= $pct >= 80 ? 'score-great' : ($pct >= 50 ? 'score-ok' : 'score-low') ?>">
        <div class="score-number"><?= $score ?></div>
        <div class="score-total">/ <?= $total ?></div>
      </div>
      <h4 class="fw-bold mb-1">
        <?php if ($pct >= 90): ?>Xuất sắc! 🌟
        <?php elseif ($pct >= 70): ?>Giỏi lắm! 👍
        <?php elseif ($pct >= 50): ?>Cố gắng thêm nhé! 💪
        <?php else: ?>Ôn lại kiến thức nhé! 📚
        <?php endif; ?>
      </h4>
      <p class="text-muted small mb-0">
        Bài <?= $lesson['lesson_no'] ?> — <?= htmlspecialchars($lesson['title']) ?>
      </p>
    </div>
  </div>

  <!-- Answers list -->
  <h5 class="fw-bold mb-3">Chi tiết từng câu</h5>

  <?php foreach ($answers as $i => $ans): ?>
  <?php $is_correct = (bool)$ans['is_correct']; ?>
  <div class="card border-0 shadow-sm mb-3 review-card <?= $is_correct ? 'review-card--correct' : 'review-card--wrong' ?>">
    <div class="card-body">

      <!-- Question -->
      <div class="d-flex gap-2 mb-3">
        <span class="badge <?= $is_correct ? 'bg-success' : 'bg-danger' ?> rounded-circle flex-shrink-0"
              style="width:28px;height:28px;line-height:20px;font-size:.8rem;">
          <?= $i+1 ?>
        </span>
        <div class="mathjax-content fw-semibold">
          <?= $ans['question'] ?>
        </div>
      </div>

      <!-- Options -->
      <div class="options-list mb-3">
        <?php foreach (['A','B','C','D'] as $opt): ?>
        <?php
          $col  = $option_labels[$opt];
          $is_selected = $ans['selected_answer'] === $opt;
          $is_right    = $ans['correct_answer']  === $opt;
          $cls = '';
          if ($is_right)    $cls = 'option--correct';
          elseif ($is_selected) $cls = 'option--wrong';
        ?>
        <div class="option-row <?= $cls ?>">
          <span class="option-letter"><?= $opt ?></span>
          <span class="mathjax-content"><?= $ans[$col] ?></span>
          <?php if ($is_right): ?>
            <i class="bi bi-check-circle-fill text-success ms-auto"></i>
          <?php elseif ($is_selected): ?>
            <i class="bi bi-x-circle-fill text-danger ms-auto"></i>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Explanation (collapsed by default if correct) -->
      <?php if ($ans['explanation']): ?>
      <div class="<?= $is_correct ? 'collapse' : '' ?>" id="exp-<?= $ans['question_id'] ?>">
        <div class="alert alert-info small mb-2 mathjax-content">
          <strong><i class="bi bi-info-circle me-1"></i>Giải thích:</strong><br>
          <?= $ans['explanation'] ?>
        </div>
      </div>
      <?php if ($is_correct): ?>
      <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
              data-bs-toggle="collapse" data-bs-target="#exp-<?= $ans['question_id'] ?>">
        <i class="bi bi-chevron-down me-1"></i>Xem giải thích
      </button>
      <?php endif; ?>
      <?php endif; ?>

      <!-- AI Chat (only for wrong answers) -->
      <?php if (!$is_correct): ?>
      <div class="mt-3 ai-chat-section" id="chat-section-<?= $ans['question_id'] ?>">
        <button class="btn btn-sm btn-outline-primary"
                onclick="toggleAiChat(<?= $ans['question_id'] ?>)">
          <i class="bi bi-robot me-1"></i>Hỏi AI
        </button>
        <div class="ai-chat-box mt-2 d-none" id="chat-box-<?= $ans['question_id'] ?>">
          <div class="ai-messages" id="chat-msgs-<?= $ans['question_id'] ?>"></div>
          <div class="d-flex gap-2 mt-2">
            <input type="text" class="form-control form-control-sm"
                   id="chat-input-<?= $ans['question_id'] ?>"
                   placeholder="Hỏi AI về câu này..."
                   onkeydown="if(event.key==='Enter') sendAiMessage(<?= $ans['question_id'] ?>, <?= $lesson_id ?>)">
            <button class="btn btn-primary btn-sm px-3"
                    onclick="sendAiMessage(<?= $ans['question_id'] ?>, <?= $lesson_id ?>)">
              <i class="bi bi-send-fill"></i>
            </button>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
  <?php endforeach; ?>

  <!-- Action buttons -->
  <div class="d-flex gap-2 mt-4 mb-4">
    <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $lesson_id ?>"
       class="btn btn-outline-secondary flex-fill">
      <i class="bi bi-book me-1"></i>Xem lại bài
    </a>
    <a href="<?= BASE_URL ?>/pages/practice.php?lesson_id=<?= $lesson_id ?>"
       class="btn btn-primary flex-fill">
      <i class="bi bi-arrow-repeat me-1"></i>Làm lại
    </a>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
