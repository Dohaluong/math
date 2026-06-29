<?php
require_once '../config.php';
require_once '../db.php';

$db = get_db();

// Load all modules + concepts
$modules = $db->query('SELECT * FROM mc_module ORDER BY sort_order')->fetchAll();

$concepts_raw = $db->query('
    SELECT c.*, m.name AS module_name, m.code AS module_code,
           l.title AS lesson_title
    FROM mc_concept c
    JOIN mc_module m ON m.id = c.module_id
    LEFT JOIN lesson l ON l.id = c.lesson_id
    ORDER BY c.sort_order
')->fetchAll();

// Group by module
$by_module = [];
foreach ($concepts_raw as $c) {
    $by_module[$c['module_id']][] = $c;
}

// Stats
$total     = count($concepts_raw);
$published = count(array_filter($concepts_raw, fn($c) => $c['status'] === 'published'));
$pct       = $total > 0 ? round($published / $total * 100) : 0;

$status_meta = [
    'draft'        => ['label'=>'Draft',        'color'=>'bg-secondary-subtle text-secondary'],
    'outline'      => ['label'=>'Outline',       'color'=>'bg-warning-subtle text-warning'],
    'ai_generated' => ['label'=>'AI Generated',  'color'=>'bg-info-subtle text-info'],
    'review'       => ['label'=>'Review',        'color'=>'bg-primary-subtle text-primary'],
    'published'    => ['label'=>'Published',     'color'=>'bg-success-subtle text-success'],
    'improved'     => ['label'=>'Improved',      'color'=>'bg-purple-subtle text-purple'],
];

$diff_label = [1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐'];

$base_url      = BASE_URL;
$page_title    = 'Curriculum';
$active_studio = 'curriculum';

$topbar_actions = '<a href="' . BASE_URL . '/studio/lessons.php" class="btn-studio-primary">
  <i class="bi bi-plus-lg"></i> Tạo bài từ Curriculum
</a>';

include '../includes/studio_header.php';
?>

<!-- Progress overview -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="studio-card" style="padding:20px 24px;">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <div class="fw-bold" style="font-size:15px;">Toán 7 — Kết nối tri thức</div>
          <div class="text-muted small"><?= $total ?> concepts · 7 modules</div>
        </div>
        <div class="text-end">
          <div style="font-size:28px; font-weight:800; color:var(--studio-accent);"><?= $pct ?>%</div>
          <div class="text-muted small"><?= $published ?>/<?= $total ?> published</div>
        </div>
      </div>
      <div class="progress" style="height:10px; border-radius:6px;">
        <div class="progress-bar bg-success" style="width:<?= $pct ?>%; border-radius:6px; transition:width .5s;"></div>
      </div>
      <div class="d-flex gap-3 mt-3 flex-wrap" style="font-size:12px;">
        <?php
        $counts = array_count_values(array_column($concepts_raw, 'status'));
        foreach ($status_meta as $s => $sm):
            $n = $counts[$s] ?? 0;
            if (!$n) continue;
        ?>
        <span class="badge <?= $sm['color'] ?>"><?= $sm['label'] ?>: <?= $n ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="studio-card h-100 d-flex flex-column justify-content-center" style="padding:20px 24px;">
      <div class="fw-bold mb-3" style="font-size:13px;">Tiến độ theo module</div>
      <?php foreach ($modules as $mod):
          $mod_concepts = $by_module[$mod['id']] ?? [];
          $mod_pub = count(array_filter($mod_concepts, fn($c) => $c['status'] === 'published'));
          $mod_total = count($mod_concepts);
          $mod_pct = $mod_total > 0 ? round($mod_pub / $mod_total * 100) : 0;
      ?>
      <div class="d-flex align-items-center gap-2 mb-2" style="font-size:12px;">
        <span style="width:24px; text-align:right; font-weight:700; color:#4a5568;"><?= $mod['code'] ?></span>
        <span style="width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($mod['name']) ?>"><?= htmlspecialchars($mod['name']) ?></span>
        <div class="progress flex-grow-1" style="height:6px; border-radius:4px;">
          <div class="progress-bar bg-success" style="width:<?= $mod_pct ?>%;"></div>
        </div>
        <span class="text-muted" style="width:40px; text-align:right;"><?= $mod_pub ?>/<?= $mod_total ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Concept table per module -->
<?php foreach ($modules as $mod):
    $mod_concepts = $by_module[$mod['id']] ?? [];
    $mod_pub = count(array_filter($mod_concepts, fn($c) => $c['status'] === 'published'));
?>
<div class="studio-card mb-3" style="padding:0; overflow:hidden;">
  <div class="d-flex align-items-center justify-content-between"
       style="padding:14px 18px; background:#f8fafc; border-bottom:1px solid var(--studio-border);">
    <div class="fw-bold" style="font-size:13px;">
      <?= htmlspecialchars($mod['code']) ?> — <?= htmlspecialchars($mod['name']) ?>
      <span class="text-muted fw-normal ms-2 small">Ch.<?= $mod['chapter_no'] ?> · <?= htmlspecialchars($mod['chapter_title']) ?></span>
    </div>
    <span class="badge bg-<?= $mod_pub === count($mod_concepts) ? 'success' : 'secondary' ?>-subtle text-<?= $mod_pub === count($mod_concepts) ? 'success' : 'secondary' ?>">
      <?= $mod_pub ?>/<?= count($mod_concepts) ?>
    </span>
  </div>
  <table class="table table-sm mb-0" style="font-size:13px;">
    <tbody>
      <?php foreach ($mod_concepts as $c):
          $sm = $status_meta[$c['status']] ?? ['label'=>$c['status'],'color'=>'bg-secondary-subtle text-secondary'];
      ?>
      <tr>
        <td style="width:50px; padding-left:18px; color:#a0aabf; font-family:monospace;"><?= $c['code'] ?></td>
        <td style="width:24px; text-align:center;" title="Độ khó"><?= $diff_label[$c['difficulty']] ?? '' ?></td>
        <td class="fw-medium"><?= htmlspecialchars($c['name']) ?></td>
        <td class="text-muted small d-none d-lg-table-cell" style="max-width:280px;">
          <?= htmlspecialchars(mb_substr($c['description'] ?? '', 0, 70)) ?>
        </td>
        <td>
          <?php if (!empty($c['prerequisite_codes'])): ?>
          <?php $prereqs = json_decode($c['prerequisite_codes'], true); ?>
          <span class="text-muted small" title="Cần học trước">
            ← <?= implode(', ', $prereqs) ?>
          </span>
          <?php endif; ?>
        </td>
        <td><span class="badge <?= $sm['color'] ?>" style="font-size:10px;"><?= $sm['label'] ?></span></td>
        <td class="text-end" style="padding-right:14px;">
          <?php if ($c['lesson_id']): ?>
          <div class="d-flex gap-1 justify-content-end">
            <a href="<?= BASE_URL ?>/studio/ai_studio.php?lesson_id=<?= $c['lesson_id'] ?>"
               class="btn-studio-outline" style="font-size:11px; padding:3px 8px;">
              <i class="bi bi-pencil-square"></i> Sửa
            </a>
            <a href="<?= BASE_URL ?>/pages/lesson.php?id=<?= $c['lesson_id'] ?>" target="_blank"
               class="btn-studio-outline" style="font-size:11px; padding:3px 8px;">
              <i class="bi bi-eye"></i>
            </a>
          </div>
          <?php else: ?>
          <button class="btn-studio-primary" style="font-size:11px; padding:3px 10px;"
                  onclick="openCreateFromConcept(
                    '<?= $c['code'] ?>',
                    <?= htmlspecialchars(json_encode($c['name']), ENT_QUOTES) ?>,
                    <?= $mod['chapter_no'] ?>,
                    <?= htmlspecialchars(json_encode($mod['chapter_title']), ENT_QUOTES) ?>,
                    <?= $c['id'] ?>
                  )">
            <i class="bi bi-plus"></i> Tạo bài
          </button>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endforeach; ?>

<!-- Modal: Create lesson from concept -->
<div class="modal fade" id="createFromConceptModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tạo bài học
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body pt-3">
        <div class="p-3 rounded mb-3" style="background:#f0f3ff; border:1px solid #c3cefd;">
          <div class="small text-muted mb-1">Concept</div>
          <div class="fw-bold" id="concept-display"></div>
          <input type="hidden" id="concept-id">
          <input type="hidden" id="concept-chapter-no">
          <input type="hidden" id="concept-chapter-title">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold small">Tên bài học <span class="text-muted fw-normal">(có thể sửa)</span></label>
          <input type="text" class="form-control" id="concept-lesson-title">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold small">Blueprint</label>
          <select class="form-select" id="concept-blueprint">
            <option value="">— Không dùng blueprint —</option>
            <?php
            $bps = $db->query('SELECT id, name, is_default FROM ls_blueprint ORDER BY is_default DESC, id')->fetchAll();
            foreach ($bps as $bp):
            ?>
            <option value="<?= $bp['id'] ?>" <?= $bp['is_default'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($bp['name']) ?> <?= $bp['is_default'] ? '(mặc định)' : '' ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button class="btn btn-light" data-bs-dismiss="modal">Huỷ</button>
        <button class="btn-studio-primary" id="create-concept-btn" onclick="createFromConcept()">
          <i class="bi bi-stars"></i> Tạo và mở AI Studio
        </button>
      </div>
    </div>
  </div>
</div>

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
    setTimeout(() => el.classList.remove('show'), 2800);
}

function openCreateFromConcept(code, name, chapterNo, chapterTitle, conceptId) {
    document.getElementById('concept-display').textContent = code + ' — ' + name;
    document.getElementById('concept-id').value            = conceptId;
    document.getElementById('concept-chapter-no').value   = chapterNo;
    document.getElementById('concept-chapter-title').value = chapterTitle;
    document.getElementById('concept-lesson-title').value  = name;
    new bootstrap.Modal(document.getElementById('createFromConceptModal')).show();
}

async function createFromConcept() {
    const btn = document.getElementById('create-concept-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> Đang tạo...';

    // Auto-derive lesson_no from existing lessons in this chapter
    const body = {
        concept_id:    parseInt(document.getElementById('concept-id').value),
        chapter_no:    parseInt(document.getElementById('concept-chapter-no').value),
        chapter_title: document.getElementById('concept-chapter-title').value,
        title:         document.getElementById('concept-lesson-title').value.trim(),
        blueprint_id:  document.getElementById('concept-blueprint').value || null,
    };

    try {
        const r = await fetch(BASE_URL + '/api/studio/create_lesson.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify(body)
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);
        toast('Đã tạo! Chuyển sang AI Studio...');
        setTimeout(() => {
            window.location.href = BASE_URL + '/studio/ai_studio.php?lesson_id=' + d.lesson_id;
        }, 700);
    } catch(e) {
        toast(e.message || 'Lỗi', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-stars"></i> Tạo và mở AI Studio';
    }
}
</script>
JS;
?>

<?php include '../includes/studio_footer.php'; ?>
