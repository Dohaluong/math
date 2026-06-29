<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$student_id = STUDENT_ID;

// Get all lessons with progress
$stmt = $db->prepare('
    SELECT l.*, p.score, p.total, p.last_learning
    FROM lesson l
    LEFT JOIN progress p ON l.id = p.lesson_id AND p.student_id = ?
    ORDER BY l.chapter_no ASC, l.lesson_no ASC
');
$stmt->execute([$student_id]);
$all_lessons = $stmt->fetchAll();

// Group by chapter
$chapters = [];
foreach ($all_lessons as $lesson) {
    $ch = $lesson['chapter_no'];
    if (!isset($chapters[$ch])) {
        $chapters[$ch] = ['title' => $lesson['chapter_title'], 'lessons' => []];
    }
    $chapters[$ch]['lessons'][] = $lesson;
}

$page_title  = 'Toán 7';
$active_page = 'math7';
include '../includes/header.php';
?>

<div class="container-narrow">
  <h2 class="fw-bold mb-1">Toán 7</h2>
  <p class="text-muted mb-4">Kết nối tri thức với cuộc sống</p>

  <div class="accordion accordion-flush" id="chapterAccordion">
    <?php foreach ($chapters as $ch_no => $chapter): ?>
    <?php
      $done  = count(array_filter($chapter['lessons'], fn($l) => $l['score'] !== null));
      $total = count($chapter['lessons']);
      $open  = ($ch_no === array_key_first($chapters)) ? '' : 'collapsed';
      $show  = ($ch_no === array_key_first($chapters)) ? 'show' : '';
    ?>
    <div class="card border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
      <div class="card-header bg-white border-0 p-0">
        <button class="accordion-button <?= $open ?> fw-semibold py-3 px-4"
                type="button" data-bs-toggle="collapse"
                data-bs-target="#ch<?= $ch_no ?>">
          <div>
            <div class="small text-muted fw-normal">Chương <?= $ch_no ?></div>
            <?= htmlspecialchars($chapter['title']) ?>
          </div>
          <span class="badge bg-primary-soft text-primary ms-auto me-3 fw-normal">
            <?= $done ?>/<?= $total ?>
          </span>
        </button>
      </div>
      <div id="ch<?= $ch_no ?>" class="accordion-collapse collapse <?= $show ?>">
        <div class="list-group list-group-flush">
          <?php foreach ($chapter['lessons'] as $lesson): ?>
          <?php $done_lesson = $lesson['score'] !== null; ?>
          <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $lesson['id'] ?>"
             class="list-group-item list-group-item-action border-0 px-4 py-3 lesson-item">
            <div class="d-flex align-items-center gap-3">
              <div class="lesson-status-icon <?= $done_lesson ? 'done' : 'todo' ?>">
                <?php if ($done_lesson): ?>
                  <i class="bi bi-check-circle-fill text-success"></i>
                <?php else: ?>
                  <i class="bi bi-circle text-muted"></i>
                <?php endif; ?>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-dark text-truncate">
                  Bài <?= $lesson['lesson_no'] ?>. <?= htmlspecialchars($lesson['title']) ?>
                </div>
                <?php if ($done_lesson): ?>
                <div class="small text-muted mt-1">
                  <?php if ((int)$lesson['total'] > 0): ?>
                    <span class="text-success fw-semibold"><?= $lesson['score'] ?>/<?= $lesson['total'] ?> câu đúng</span>
                  <?php else: ?>
                    <span class="text-success fw-semibold"><i class="bi bi-check2 me-1"></i>Hoàn thành</span>
                  <?php endif; ?>
                  · <?= date('d/m/Y', strtotime($lesson['last_learning'])) ?>
                </div>
                <?php else: ?>
                <div class="small text-muted mt-1">Chưa học</div>
                <?php endif; ?>
              </div>
              <i class="bi bi-chevron-right text-muted flex-shrink-0"></i>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<?php include '../includes/footer.php'; ?>
