<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();
$prompts = $db->query('SELECT * FROM ls_prompt ORDER BY worker')->fetchAll();

$page_title    = 'Prompt Library';
$active_studio = 'prompts';
$base_url = BASE_URL;
include '../includes/studio_header.php';
?>

<div class="mb-3" style="font-size:13px; color:#4a5568;">
  <i class="bi bi-info-circle me-1"></i>
  Mỗi Worker có một prompt riêng. Thay đổi prompt sẽ ảnh hưởng đến toàn bộ lần generate tiếp theo.
  Version tăng tự động khi lưu.
</div>

<?php foreach ($prompts as $p): ?>
<div class="prompt-card">
  <div class="prompt-card__header">
    <div>
      <span class="fw-bold"><?= htmlspecialchars($p['name']) ?></span>
      <span class="badge bg-secondary ms-2" style="font-size:10px;"><?= $p['worker'] ?></span>
      <span class="text-muted small ms-2">v<?= $p['version'] ?></span>
    </div>
    <button class="btn-studio-primary" onclick="savePrompt(<?= $p['id'] ?>)">
      <i class="bi bi-floppy-fill"></i> Lưu
    </button>
  </div>
  <div class="prompt-card__body">
    <div class="row g-3">
      <div class="col-12">
        <label class="fw-semibold small mb-1">System Prompt</label>
        <textarea class="prompt-textarea" id="sys-<?= $p['id'] ?>" rows="3"><?= htmlspecialchars($p['system_prompt']) ?></textarea>
      </div>
      <div class="col-12">
        <label class="fw-semibold small mb-1">User Template
          <span class="text-muted fw-normal">— biến: <code>{{lesson_title}}</code> <code>{{chapter_title}}</code> <code>{{activity_title}}</code> <code>{{theory_summary}}</code> <code>{{lesson_summary}}</code></span>
        </label>
        <textarea class="prompt-textarea" id="usr-<?= $p['id'] ?>" rows="10"><?= htmlspecialchars($p['user_template']) ?></textarea>
      </div>
      <div class="col-12">
        <label class="fw-semibold small mb-1 text-muted">Output Example (tham khảo — không gửi AI)</label>
        <textarea class="prompt-textarea" id="ex-<?= $p['id'] ?>" rows="3" style="background:#f8f8f8; color:#718096;"><?= htmlspecialchars($p['output_example'] ?? '') ?></textarea>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<div class="studio-toast" id="toast"></div>

<?php
$page_scripts = <<<JS
<script>
const BASE_URL = '$base_url';

function toast(msg, ok=true) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.style.background = ok ? '#1a1f2e' : '#c53030';
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 2500);
}

async function savePrompt(id) {
    const body = {
        id,
        system_prompt:  document.getElementById('sys-' + id).value,
        user_template:  document.getElementById('usr-' + id).value,
        output_example: document.getElementById('ex-' + id).value,
    };
    const r = await fetch(BASE_URL + '/api/studio/update_prompt.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify(body)
    });
    const d = await r.json();
    if (d.ok) toast('Đã lưu prompt (v' + d.version + ')');
    else toast(d.error || 'Lỗi', false);
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
