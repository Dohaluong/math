<?php
require_once '../config.php';
require_once '../db.php';
require_once '../includes/math_normalizer.php';

$db = get_db();

// Lesson selector
$lesson_id = (int)($_GET['lesson_id'] ?? 0);
$lesson    = null;
if ($lesson_id) {
    $ls = $db->prepare('SELECT * FROM lesson WHERE id = ?');
    $ls->execute([$lesson_id]);
    $lesson = $ls->fetch();
}

// Load lessons for selector
$all_lessons = $db->query('SELECT id, lesson_no, title FROM lesson ORDER BY lesson_no')->fetchAll();

// Load blueprints
$blueprints = $db->query('SELECT * FROM ls_blueprint ORDER BY is_default DESC, id')->fetchAll();

// Load drafts for selected lesson
$drafts = [];
if ($lesson_id) {
    $ds = $db->prepare('SELECT * FROM ls_draft WHERE lesson_id = ? ORDER BY sort_order, id');
    $ds->execute([$lesson_id]);
    $drafts = $ds->fetchAll();
}

$selected_draft_id = (int)($_GET['draft_id'] ?? 0);
$selected_draft    = null;
if ($selected_draft_id) {
    foreach ($drafts as $d) {
        if ($d['id'] === $selected_draft_id) { $selected_draft = $d; break; }
    }
}

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

$page_title    = 'AI Studio' . ($lesson ? ' — ' . $lesson['title'] : '');
$active_studio = 'ai_studio';

$base_url = BASE_URL;
$page_head = <<<HTML
<style>
  .draft-item { cursor:pointer; }
</style>
HTML;

$lesson_json     = $lesson ? json_encode($lesson, JSON_UNESCAPED_UNICODE) : 'null';
$drafts_json     = json_encode(array_map(fn($d) => [
    'id'     => (int)$d['id'],
    'type'   => $d['type'],
    'title'  => $d['title'],
    'status' => $d['status'],
], $drafts), JSON_UNESCAPED_UNICODE);
$blueprints_json = json_encode(array_map(fn($b) => [
    'id'        => (int)$b['id'],
    'name'      => $b['name'],
    'structure' => json_decode($b['structure'], true),
], $blueprints), JSON_UNESCAPED_UNICODE);

include '../includes/studio_header.php';
?>

<?php if (!$lesson_id): ?>
<!-- ── Lesson selector ──────────────────────────────────────── -->
<div class="studio-card" style="max-width:480px; margin:0 auto; margin-top:40px;">
  <h5 class="fw-bold mb-3"><i class="bi bi-stars me-2 text-primary"></i>Chọn bài học để bắt đầu</h5>
  <div class="list-group">
    <?php foreach ($all_lessons as $l): ?>
    <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $l['id'] ?>"
       class="list-group-item list-group-item-action d-flex align-items-center gap-3">
      <span class="badge bg-light text-dark border">Bài <?= $l['lesson_no'] ?></span>
      <span><?= htmlspecialchars($l['title']) ?></span>
      <i class="bi bi-arrow-right ms-auto text-muted"></i>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php else: ?>
<!-- ── AI Studio main layout ──────────────────────────────────── -->
<div class="ai-studio-layout">

  <!-- Left: Outline panel -->
  <div class="ai-studio-left">
    <div class="ai-studio-left__header">
      <div style="flex:1; min-width:0;">
        <div class="fw-bold" style="font-size:13px;">Bài <?= $lesson['lesson_no'] ?></div>
        <!-- Title display / inline edit -->
        <div id="lesson-title-view" class="d-flex align-items-center gap-1" style="min-width:0;">
          <span class="text-muted text-truncate" id="lesson-title-text"
                style="font-size:11px;"><?= htmlspecialchars($lesson['title']) ?></span>
          <button onclick="startRename()" title="Sửa tên"
                  style="background:none;border:none;padding:0 2px;color:#a0aabf;line-height:1;flex-shrink:0;"
                  onmouseover="this.style.color='#4a5568'" onmouseout="this.style.color='#a0aabf'">
            <i class="bi bi-pencil" style="font-size:10px;"></i>
          </button>
        </div>
        <div id="lesson-title-edit" class="d-none" style="margin-top:2px;">
          <input id="lesson-title-input" type="text"
                 value="<?= htmlspecialchars($lesson['title']) ?>"
                 style="font-size:11px; padding:2px 5px; width:100%; border:1px solid #cbd5e0; border-radius:4px;"
                 onkeydown="if(event.key==='Enter') saveRename(); if(event.key==='Escape') cancelRename();">
          <div class="d-flex gap-1 mt-1">
            <button onclick="saveRename()"
                    style="font-size:10px;padding:2px 7px;background:#4f6cf6;color:#fff;border:none;border-radius:3px;">Lưu</button>
            <button onclick="cancelRename()"
                    style="font-size:10px;padding:2px 7px;background:#e2e8f0;color:#4a5568;border:none;border-radius:3px;">Huỷ</button>
          </div>
        </div>
      </div>
      <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $lesson_id ?>" target="_blank"
         class="btn-studio-outline" style="font-size:11px; padding:4px 8px; flex-shrink:0;" title="Xem trang học sinh">
        <i class="bi bi-eye"></i>
      </a>
    </div>

    <!-- Outline actions -->
    <div style="padding:10px 8px; border-bottom:1px solid var(--studio-border); display:flex; gap:6px; flex-wrap:wrap;">
      <?php if (empty($drafts)): ?>
      <!-- Blueprint selector + generate outline -->
      <div class="dropdown" style="flex:1;">
        <button class="btn-studio-primary w-100" data-bs-toggle="dropdown" id="blueprintBtn">
          <i class="bi bi-lightning-fill"></i> Generate Outline
        </button>
        <ul class="dropdown-menu" style="font-size:13px; min-width:220px;">
          <?php foreach ($blueprints as $bp): ?>
          <li>
            <a class="dropdown-item" href="#"
               onclick="generateOutline(<?= $lesson_id ?>, <?= $bp['id'] ?>); return false;">
              <?= htmlspecialchars($bp['name']) ?>
              <?php if ($bp['is_default']): ?><span class="badge bg-primary ms-1" style="font-size:10px;">default</span><?php endif; ?>
            </a>
          </li>
          <?php endforeach; ?>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item" href="#" onclick="generateOutlineAI(<?= $lesson_id ?>); return false;">
              <i class="bi bi-stars me-1"></i> AI tự tạo outline
            </a>
          </li>
        </ul>
      </div>
      <?php else: ?>
      <button class="btn-studio-outline" style="font-size:11px; flex:1;"
              onclick="addActivity(<?= $lesson_id ?>)">
        <i class="bi bi-plus"></i> Thêm
      </button>
      <button class="btn-studio-primary" style="font-size:11px; flex:1;"
              onclick="generateAll()" id="genAllBtn" title="Generate AI cho tất cả hoạt động chưa có nội dung">
        <i class="bi bi-stars"></i> Generate All
      </button>
      <button class="btn-studio-outline" style="font-size:11px; padding:4px 8px;" title="Xoá toàn bộ draft"
              onclick="clearDrafts(<?= $lesson_id ?>)">
        <i class="bi bi-trash3"></i>
      </button>
      <?php endif; ?>
    </div>

    <!-- Draft list -->
    <div class="ai-studio-left__body">
      <?php if (empty($drafts)): ?>
      <div class="text-center text-muted py-4" style="font-size:13px;">
        <i class="bi bi-files" style="font-size:2rem; display:block; margin-bottom:8px; opacity:.4;"></i>
        Chưa có draft.<br>Tạo outline để bắt đầu.
      </div>
      <?php else: ?>
      <ul class="draft-list" id="draft-list">
        <?php foreach ($drafts as $i => $d): ?>
        <li class="draft-item <?= $d['id'] === $selected_draft_id ? 'selected' : '' ?>"
            data-id="<?= $d['id'] ?>" data-sort="<?= $d['sort_order'] ?>">
          <!-- Move buttons -->
          <div class="draft-item__move" onclick="event.stopPropagation()">
            <button title="Lên" onclick="moveItem(<?= $d['id'] ?>, -1)"
                    <?= $i === 0 ? 'disabled' : '' ?>>
              <i class="bi bi-chevron-up"></i>
            </button>
            <button title="Xuống" onclick="moveItem(<?= $d['id'] ?>, 1)"
                    <?= $i === count($drafts)-1 ? 'disabled' : '' ?>>
              <i class="bi bi-chevron-down"></i>
            </button>
          </div>
          <!-- Clickable area -->
          <div class="draft-item__body" onclick="selectDraft(<?= $d['id'] ?>)">
            <span class="draft-badge draft-badge--<?= $d['type'] ?>">
              <?= $type_labels[$d['type']] ?? $d['type'] ?>
            </span>
            <span class="draft-item__title"><?= htmlspecialchars($d['title'] ?: '(chưa đặt tên)') ?></span>
            <span class="draft-status draft-status--<?= $d['status'] ?>"><?= $d['status'] ?></span>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right: Activity editor -->
  <div class="ai-studio-right" id="editor-panel">
    <?php if ($selected_draft): ?>
    <?php include 'partials/draft_editor.php'; ?>
    <?php else: ?>
    <div class="d-flex align-items-center justify-content-center h-100 text-muted flex-column gap-2">
      <i class="bi bi-cursor-text" style="font-size:2.5rem; opacity:.3;"></i>
      <span style="font-size:13px;">Chọn một hoạt động để chỉnh sửa</span>
    </div>
    <?php endif; ?>
  </div>

</div><!-- /.ai-studio-layout -->
<?php endif; ?>

<!-- Modal: Add Activity -->
<div class="modal fade" id="addActivityModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i>Thêm hoạt động
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3 pb-0">
        <!-- Type picker badges -->
        <div class="fw-semibold small mb-2">Loại hoạt động</div>
        <div class="d-flex flex-wrap gap-2 mb-4" id="type-picker">
          <button class="draft-badge draft-badge--introduction act-type-btn" data-type="introduction" data-title="Mở đầu">Mở đầu</button>
          <button class="draft-badge draft-badge--theory act-type-btn" data-type="theory" data-title="Lý thuyết">Kiến thức</button>
          <button class="draft-badge draft-badge--example act-type-btn" data-type="example" data-title="Ví dụ minh họa">Ví dụ</button>
          <button class="draft-badge draft-badge--ai_explanation act-type-btn" data-type="ai_explanation" data-title="Hỏi AI">Hỏi AI</button>
          <button class="draft-badge draft-badge--quiz act-type-btn" data-type="quiz" data-title="Kiểm tra nhanh">Quiz</button>
          <button class="draft-badge draft-badge--guided_practice act-type-btn" data-type="guided_practice" data-title="Luyện tập có hướng dẫn">Luyện tập</button>
          <button class="draft-badge draft-badge--fill_answer act-type-btn" data-type="fill_answer" data-title="Điền đáp án">Điền đáp án</button>
          <button class="draft-badge draft-badge--reflection act-type-btn" data-type="reflection" data-title="Nhìn lại">Nhìn lại</button>
          <button class="draft-badge draft-badge--summary act-type-btn" data-type="summary" data-title="Tóm tắt">Tóm tắt</button>
        </div>
        <input type="hidden" id="new-act-type">
        <div class="mb-3">
          <label class="fw-semibold small mb-1">Tiêu đề</label>
          <input type="text" class="form-control" id="new-act-title" placeholder="VD: Kiểm tra nhanh 2">
        </div>
      </div>
      <div class="modal-footer border-0 pt-2">
        <button class="btn btn-light" data-bs-dismiss="modal">Huỷ</button>
        <button class="btn-studio-primary" id="add-act-btn" onclick="submitAddActivity()" disabled>
          <i class="bi bi-plus-lg"></i> Thêm
        </button>
      </div>
    </div>
  </div>
</div>

<div class="studio-toast" id="toast"></div>

<!-- Modal: Generate All progress -->
<div class="modal fade" id="genAllModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
    <div class="modal-content border-0 shadow">
      <div class="modal-body p-4 text-center">
        <div class="mb-3">
          <span class="studio-spinner" style="width:28px;height:28px;border-width:3px;"></span>
        </div>
        <div class="fw-bold mb-1" id="genAll-title">Đang generate...</div>
        <div class="text-muted small mb-3" id="genAll-sub"></div>
        <div class="progress" style="height:6px;border-radius:4px;">
          <div id="genAll-bar" class="progress-bar bg-primary" style="width:0%;transition:width .3s;"></div>
        </div>
        <div class="small text-muted mt-2" id="genAll-count"></div>
      </div>
    </div>
  </div>
</div>

<?php
$page_scripts = <<<JS
<script>
const BASE_URL      = '$base_url';
const LESSON        = $lesson_json;
const BLUEPRINTS    = $blueprints_json;
const DRAFT_LIST    = $drafts_json;

function toast(msg, ok=true) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.background = ok ? '#1a1f2e' : '#c53030';
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 2800);
}

function selectDraft(id) {
    window.location.href = BASE_URL + '/studio/ai_studio.php?lesson_id=' + LESSON.id + '&draft_id=' + id;
}

async function generateOutline(lessonId, blueprintId) {
    const btn = document.getElementById('blueprintBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> Đang tạo...';
    try {
        const r = await fetch(BASE_URL + '/api/studio/generate_outline.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({lesson_id: lessonId, blueprint_id: blueprintId})
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);
        toast('Outline đã được tạo!');
        location.reload();
    } catch(e) {
        toast(e.message || 'Lỗi tạo outline', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-fill"></i> Generate Outline';
    }
}

async function generateOutlineAI(lessonId) {
    const btn = document.getElementById('blueprintBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> AI đang tạo...';
    try {
        const r = await fetch(BASE_URL + '/api/studio/generate_outline.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({lesson_id: lessonId, blueprint_id: null})
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);
        toast('AI đã tạo outline!');
        location.reload();
    } catch(e) {
        toast(e.message || 'Lỗi tạo outline', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-lightning-fill"></i> Generate Outline';
    }
}

async function clearDrafts(lessonId) {
    if (!confirm('Xoá toàn bộ draft của bài học này? (Không ảnh hưởng bản live)')) return;
    const r = await fetch(BASE_URL + '/api/studio/clear_drafts.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({lesson_id: lessonId})
    });
    const d = await r.json();
    if (d.ok) { toast('Đã xoá draft.'); location.reload(); }
    else toast(d.error || 'Lỗi', false);
}

function addActivity(lessonId) {
    // Reset modal state
    document.querySelectorAll('.act-type-btn').forEach(b => b.classList.remove('act-type-selected'));
    document.getElementById('new-act-type').value  = '';
    document.getElementById('new-act-title').value = '';
    document.getElementById('add-act-btn').disabled = true;
    new bootstrap.Modal(document.getElementById('addActivityModal')).show();
}

// Badge picker selection
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.act-type-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.act-type-btn').forEach(b => b.classList.remove('act-type-selected'));
            btn.classList.add('act-type-selected');
            document.getElementById('new-act-type').value  = btn.dataset.type;
            const titleEl = document.getElementById('new-act-title');
            if (!titleEl.value) titleEl.value = btn.dataset.title;
            document.getElementById('add-act-btn').disabled = false;
        });
    });
});

async function submitAddActivity() {
    const type  = document.getElementById('new-act-type').value;
    const title = document.getElementById('new-act-title').value.trim();
    if (!type || !title) return;
    const btn = document.getElementById('add-act-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span>';
    const r = await fetch(BASE_URL + '/api/studio/save_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({lesson_id: LESSON.id, type, title, data: {}, sort_order: 9999})
    });
    const d = await r.json();
    if (d.id) {
        bootstrap.Modal.getInstance(document.getElementById('addActivityModal')).hide();
        toast('Đã thêm!');
        selectDraft(d.id);
    } else {
        toast(d.error || 'Lỗi', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-lg"></i> Thêm';
    }
}

async function generateAll() {
    if (!LESSON) return;
    // Only generate drafts not yet approved/published
    const queue = DRAFT_LIST.filter(d => d.status === 'draft' || d.status === 'rejected');
    if (queue.length === 0) {
        toast('Tất cả hoạt động đã có nội dung.', false);
        return;
    }
    if (!confirm('Generate AI cho ' + queue.length + ' hoạt động chưa có nội dung? Quá trình này có thể mất vài phút.')) return;

    const modal   = new bootstrap.Modal(document.getElementById('genAllModal'));
    const barEl   = document.getElementById('genAll-bar');
    const titleEl = document.getElementById('genAll-title');
    const subEl   = document.getElementById('genAll-sub');
    const cntEl   = document.getElementById('genAll-count');
    const btn     = document.getElementById('genAllBtn');
    btn.disabled  = true;
    modal.show();

    const errors = [];
    for (let i = 0; i < queue.length; i++) {
        const draft = queue[i];
        const pct   = Math.round((i / queue.length) * 100);
        barEl.style.width  = pct + '%';
        titleEl.textContent = 'Đang tạo: ' + (draft.title || draft.type);
        subEl.textContent   = draft.type;
        cntEl.textContent   = (i + 1) + ' / ' + queue.length;

        try {
            const r = await fetch(BASE_URL + '/api/studio/generate_activity.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ draft_id: draft.id, lesson_id: LESSON.id }),
            });
            const d = await r.json();
            if (d.error) errors.push(draft.title + ': ' + d.error);
        } catch(e) {
            errors.push(draft.title + ': kết nối thất bại');
        }
    }

    barEl.style.width = '100%';
    titleEl.textContent = 'Hoàn tất!';
    subEl.textContent   = errors.length ? errors.join('; ') : 'Tất cả hoạt động đã được tạo.';
    cntEl.textContent   = queue.length + ' / ' + queue.length;

    setTimeout(() => {
        modal.hide();
        location.reload();
    }, 1500);
}

async function moveItem(id, dir) {
    // Get current list order from DOM
    const items = [...document.querySelectorAll('#draft-list li')];
    const idx   = items.findIndex(li => parseInt(li.dataset.id) === id);
    const target = items[idx + dir];
    if (!target) return;

    // Swap sort_order values
    const mySort  = parseInt(items[idx].dataset.sort);
    const tgSort  = parseInt(target.dataset.sort);

    await Promise.all([
        fetch(BASE_URL + '/api/studio/update_draft.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({id: id, sort_order: tgSort})
        }),
        fetch(BASE_URL + '/api/studio/update_draft.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({id: parseInt(target.dataset.id), sort_order: mySort})
        }),
    ]);
    location.reload();
}

function startRename() {
    document.getElementById('lesson-title-view').classList.add('d-none');
    const edit = document.getElementById('lesson-title-edit');
    edit.classList.remove('d-none');
    const inp = document.getElementById('lesson-title-input');
    inp.focus();
    inp.select();
}

function cancelRename() {
    document.getElementById('lesson-title-edit').classList.add('d-none');
    document.getElementById('lesson-title-view').classList.remove('d-none');
}

async function saveRename() {
    const title = document.getElementById('lesson-title-input').value.trim();
    if (!title) return;
    const r = await fetch(BASE_URL + '/api/studio/rename_lesson.php', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id: LESSON.id, title})
    });
    const d = await r.json();
    if (d.ok) {
        document.getElementById('lesson-title-text').textContent = title;
        document.title = document.title.replace(/—.*$/, '— ' + title);
        cancelRename();
        toast('Đã đổi tên bài học.');
    } else {
        toast(d.error || 'Lỗi', false);
    }
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
