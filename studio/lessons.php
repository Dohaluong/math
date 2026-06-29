<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

$lessons = $db->query('
    SELECT l.*,
           COUNT(DISTINCT a.id)    AS live_count,
           COUNT(DISTINCT d.id)    AS draft_count
    FROM lesson l
    LEFT JOIN activity a ON a.lesson_id = l.id
    LEFT JOIN ls_draft d ON d.lesson_id = l.id AND d.status IN ("draft","approved")
    GROUP BY l.id
    ORDER BY l.chapter_no, l.lesson_no
')->fetchAll();

// Chapters for autocomplete
$chapters = $db->query('SELECT DISTINCT chapter_no, chapter_title FROM lesson ORDER BY chapter_no')->fetchAll();

// Blueprints for selector
$blueprints = $db->query('SELECT id, name, is_default FROM ls_blueprint ORDER BY is_default DESC, id')->fetchAll();

$base_url      = BASE_URL;
$page_title    = 'Bài học';
$active_studio = 'lessons';

$topbar_actions = '<button class="btn-studio-primary" data-bs-toggle="modal" data-bs-target="#newLessonModal">
  <i class="bi bi-plus-lg"></i> Tạo bài mới
</button>';

include '../includes/studio_header.php';
?>

<!-- Lesson table -->
<div class="studio-card">
  <table class="table table-hover mb-0" style="font-size:14px;">
    <thead class="table-light">
      <tr>
        <th>Chương</th>
        <th>Bài</th>
        <th>Tên bài học</th>
        <th class="text-center">Live</th>
        <th class="text-center">Draft</th>
        <th>Trạng thái</th>
        <th class="text-end">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $cur_chapter = null;
      foreach ($lessons as $l):
          $chapter_changed = $l['chapter_no'] !== $cur_chapter;
          $cur_chapter = $l['chapter_no'];
      ?>
      <?php if ($chapter_changed): ?>
      <tr class="table-light">
        <td colspan="7" class="fw-semibold small text-muted py-2" style="letter-spacing:.04em;">
          Chương <?= $l['chapter_no'] ?> — <?= htmlspecialchars($l['chapter_title']) ?>
        </td>
      </tr>
      <?php endif; ?>
      <tr>
        <td class="text-muted small"><?= $l['chapter_no'] ?></td>
        <td><span class="badge bg-light text-dark border">Bài <?= $l['lesson_no'] ?></span></td>
        <td class="fw-medium"><?= htmlspecialchars($l['title']) ?></td>
        <td class="text-center">
          <?php if ($l['live_count'] > 0): ?>
          <span class="badge bg-primary-subtle text-primary fw-semibold"><?= $l['live_count'] ?></span>
          <?php else: ?>
          <span class="text-muted small">—</span>
          <?php endif; ?>
        </td>
        <td class="text-center">
          <?php if ($l['draft_count'] > 0): ?>
          <span class="badge bg-warning-subtle text-warning fw-semibold"><?= $l['draft_count'] ?></span>
          <?php else: ?>
          <span class="text-muted small">—</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($l['live_count'] > 0): ?>
          <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Published</span>
          <?php elseif ($l['draft_count'] > 0): ?>
          <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock me-1"></i>In progress</span>
          <?php else: ?>
          <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-circle me-1"></i>Empty</span>
          <?php endif; ?>
        </td>
        <td class="text-end">
          <div class="d-flex gap-2 justify-content-end">
            <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $l['id'] ?>"
               class="btn-studio-primary" style="font-size:12px;">
              <i class="bi bi-stars"></i> AI Studio
            </a>
            <?php if ($l['live_count'] > 0): ?>
            <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $l['id'] ?>" target="_blank"
               class="btn-studio-outline" style="font-size:12px;">
              <i class="bi bi-eye"></i>
            </a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- ── Modal: Tạo bài học mới ──────────────────────────────── -->
<div class="modal fade" id="newLessonModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tạo bài học mới
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <form id="new-lesson-form">

          <!-- Chapter -->
          <div class="mb-3">
            <label class="form-label fw-semibold small">Chương</label>
            <div class="row g-2">
              <div class="col-3">
                <input type="number" class="form-control" id="chapter_no" name="chapter_no"
                       min="1" max="99" placeholder="Số" required
                       onchange="autofillChapterTitle(this.value)">
              </div>
              <div class="col-9">
                <input type="text" class="form-control" id="chapter_title" name="chapter_title"
                       placeholder="Tên chương" required>
              </div>
            </div>
            <?php if (!empty($chapters)): ?>
            <div class="mt-2 d-flex gap-2 flex-wrap">
              <span class="small text-muted">Chương có sẵn:</span>
              <?php foreach ($chapters as $ch): ?>
              <button type="button" class="badge bg-light text-dark border text-decoration-none"
                      style="cursor:pointer; font-size:11px;"
                      onclick="selectChapter(<?= $ch['chapter_no'] ?>, <?= json_encode($ch['chapter_title']) ?>)">
                C<?= $ch['chapter_no'] ?> — <?= htmlspecialchars($ch['chapter_title']) ?>
              </button>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>

          <!-- Lesson number & title -->
          <div class="row g-2 mb-3">
            <div class="col-3">
              <label class="form-label fw-semibold small">Bài số</label>
              <input type="number" class="form-control" id="lesson_no" name="lesson_no"
                     min="1" max="99" placeholder="4" required value="<?= count($lessons) + 1 ?>">
            </div>
            <div class="col-9">
              <label class="form-label fw-semibold small">Tên bài học</label>
              <input type="text" class="form-control" id="lesson_title" name="title"
                     placeholder="VD: Lũy thừa với số mũ tự nhiên" required>
            </div>
          </div>

          <!-- Blueprint -->
          <div class="mb-3">
            <label class="form-label fw-semibold small">Blueprint nội dung</label>
            <select class="form-select" id="blueprint_id" name="blueprint_id">
              <option value="">— Chỉ tạo bài, generate outline sau —</option>
              <?php foreach ($blueprints as $bp): ?>
              <option value="<?= $bp['id'] ?>" <?= $bp['is_default'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($bp['name']) ?>
                <?= $bp['is_default'] ? '(mặc định)' : '' ?>
              </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Nếu chọn blueprint, outline sẽ được tạo tự động và bạn sẽ được chuyển vào AI Studio.</div>
          </div>

        </form>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-light" data-bs-dismiss="modal">Huỷ</button>
        <button class="btn-studio-primary" id="create-btn" onclick="createLesson()">
          <i class="bi bi-plus-lg"></i> Tạo bài học
        </button>
      </div>
    </div>
  </div>
</div>

<div class="studio-toast" id="toast"></div>

<?php
$chapters_json  = json_encode(array_column($chapters, 'chapter_title', 'chapter_no'), JSON_UNESCAPED_UNICODE);
$page_scripts = <<<JS
<script>
const BASE_URL   = '$base_url';
const CHAPTERS   = $chapters_json;

function toast(msg, ok=true) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.background = ok ? '#1a1f2e' : '#c53030';
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 2800);
}

function selectChapter(no, title) {
    document.getElementById('chapter_no').value    = no;
    document.getElementById('chapter_title').value = title;
}

function autofillChapterTitle(no) {
    const title = CHAPTERS[no];
    if (title) document.getElementById('chapter_title').value = title;
}

async function createLesson() {
    const form = document.getElementById('new-lesson-form');
    if (!form.reportValidity()) return;

    const btn = document.getElementById('create-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> Đang tạo...';

    const body = {
        chapter_no:    parseInt(document.getElementById('chapter_no').value),
        chapter_title: document.getElementById('chapter_title').value.trim(),
        lesson_no:     parseInt(document.getElementById('lesson_no').value),
        title:         document.getElementById('lesson_title').value.trim(),
        blueprint_id:  document.getElementById('blueprint_id').value || null,
    };

    try {
        const r = await fetch(BASE_URL + '/api/studio/create_lesson.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(body),
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);

        toast('Đã tạo bài học! Đang chuyển đến AI Studio...');

        // Redirect to AI Studio (blueprint will have been applied server-side)
        setTimeout(() => {
            window.location.href = BASE_URL + '/studio/ai_studio.php?lesson_id=' + d.lesson_id;
        }, 700);

    } catch(e) {
        toast(e.message || 'Lỗi tạo bài học', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg"></i> Tạo bài học';
    }
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
