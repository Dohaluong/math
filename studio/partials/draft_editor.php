<?php
// Available in this file: $selected_draft, $lesson, $lesson_id, $type_labels
$d    = $selected_draft;
$data = json_decode($d['data'] ?? '{}', true) ?? [];
$json_pretty = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
<div class="studio-editor__header">
  <span class="draft-badge draft-badge--<?= $d['type'] ?>">
    <?= $type_labels[$d['type']] ?? $d['type'] ?>
  </span>
  <span class="fw-semibold ms-1" style="font-size:14px;"><?= htmlspecialchars($d['title']) ?></span>
  <span class="draft-status draft-status--<?= $d['status'] ?> ms-2"><?= $d['status'] ?></span>
  <div class="ms-auto d-flex gap-2">
    <?php if ($d['status'] === 'draft'): ?>
    <button class="btn-studio-primary" onclick="approveDraft(<?= $d['id'] ?>)">
      <i class="bi bi-check-lg"></i> Approve
    </button>
    <?php elseif ($d['status'] === 'approved'): ?>
    <span class="text-success fw-semibold small"><i class="bi bi-check-circle-fill me-1"></i>Approved</span>
    <button class="btn-studio-outline" onclick="unapprove(<?= $d['id'] ?>)">
      <i class="bi bi-arrow-counterclockwise"></i> Unapprove
    </button>
    <?php endif; ?>
    <button class="btn-studio-outline" style="color:#c53030; border-color:#c53030;"
            onclick="deleteDraft(<?= $d['id'] ?>)">
      <i class="bi bi-trash3"></i>
    </button>
  </div>
</div>

<div class="studio-editor__tabs">
  <div class="studio-editor__tab active" id="tab-generate" onclick="switchTab('generate')">
    <i class="bi bi-stars me-1"></i>Generate
  </div>
  <div class="studio-editor__tab" id="tab-json" onclick="switchTab('json')">
    <i class="bi bi-code-slash me-1"></i>JSON
  </div>
  <div class="studio-editor__tab" id="tab-notes" onclick="switchTab('notes')">
    <i class="bi bi-chat-left-text me-1"></i>Notes
  </div>
</div>

<div class="studio-editor__body">

  <!-- Generate tab -->
  <div id="pane-generate">
    <div class="mb-2">
      <textarea id="gen-hint" rows="2"
        class="form-control form-control-sm"
        placeholder="Gợi ý cho AI (tùy chọn): câu hỏi cụ thể, số liệu, yêu cầu đặc biệt..."
        style="font-size:12px; resize:vertical;"></textarea>
    </div>
    <div class="d-flex align-items-center gap-2 mb-3">
      <button class="btn-studio-primary" id="gen-btn" onclick="generateContent(<?= $d['id'] ?>, '<?= $d['type'] ?>')">
        <i class="bi bi-stars"></i> Generate với AI
      </button>
      <span class="text-muted small">Nhập gợi ý bên trên để AI bám theo, hoặc để trống để AI tự chọn.</span>
    </div>

    <!-- Current data preview -->
    <?php if (!empty($data)): ?>
    <div class="mb-3">
      <div class="fw-semibold mb-2" style="font-size:13px; color:#4a5568;">Nội dung hiện tại:</div>
      <div class="studio-preview" style="font-size:13px; max-height:300px; overflow-y:auto;">
        <?= renderDraftPreview($d['type'], $data) ?>
      </div>
    </div>
    <?php else: ?>
    <div class="studio-preview d-flex align-items-center justify-content-center" style="min-height:120px; color:#a0aabf; font-size:13px;">
      <span><i class="bi bi-inbox me-2"></i>Chưa có nội dung. Nhấn Generate để tạo.</span>
    </div>
    <?php endif; ?>
  </div>

  <!-- JSON tab -->
  <div id="pane-json" style="display:none;">
    <div class="d-flex gap-2 mb-2">
      <button class="btn-studio-primary" onclick="saveJson(<?= $d['id'] ?>)">
        <i class="bi bi-floppy-fill"></i> Lưu JSON
      </button>
      <span class="text-muted small align-self-center">Chỉnh sửa trực tiếp JSON rồi lưu.</span>
    </div>
    <textarea class="json-editor" id="json-editor" rows="20"><?= htmlspecialchars($json_pretty) ?></textarea>
  </div>

  <!-- Notes tab -->
  <div id="pane-notes" style="display:none;">
    <div class="mb-2">
      <label class="fw-semibold small mb-1">Ghi chú reviewer</label>
      <textarea class="form-control" id="notes-editor" rows="5" style="font-size:13px;"><?= htmlspecialchars($d['notes'] ?? '') ?></textarea>
    </div>
    <button class="btn-studio-outline" onclick="saveNotes(<?= $d['id'] ?>)">
      <i class="bi bi-floppy-fill"></i> Lưu ghi chú
    </button>
  </div>

</div><!-- /.studio-editor__body -->

<script>
function switchTab(name) {
    ['generate','json','notes'].forEach(t => {
        document.getElementById('tab-' + t).classList.toggle('active', t === name);
        document.getElementById('pane-' + t).style.display = t === name ? 'block' : 'none';
    });
}

async function generateContent(draftId, type) {
    const btn  = document.getElementById('gen-btn');
    const hint = (document.getElementById('gen-hint')?.value ?? '').trim();
    btn.disabled = true;
    btn.innerHTML = '<span class="studio-spinner"></span> Đang generate...';
    try {
        const body = {draft_id: draftId, lesson_id: LESSON.id};
        if (hint) body.user_hint = hint;
        const r = await fetch(BASE_URL + '/api/studio/generate_activity.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify(body)
        });
        const d = await r.json();
        if (d.error) throw new Error(d.error);
        toast('Đã generate! Trang sẽ refresh...');
        setTimeout(() => location.reload(), 800);
    } catch(e) {
        toast(e.message || 'Lỗi generate', false);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-stars"></i> Generate với AI';
    }
}

async function saveJson(draftId) {
    let data;
    try { data = JSON.parse(document.getElementById('json-editor').value); }
    catch(e) { toast('JSON không hợp lệ: ' + e.message, false); return; }

    const r = await fetch(BASE_URL + '/api/studio/update_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId, data})
    });
    const d = await r.json();
    if (d.ok) toast('Đã lưu JSON!');
    else toast(d.error || 'Lỗi', false);
}

async function saveNotes(draftId) {
    const notes = document.getElementById('notes-editor').value;
    const r = await fetch(BASE_URL + '/api/studio/update_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId, notes})
    });
    const d = await r.json();
    if (d.ok) toast('Đã lưu ghi chú!');
    else toast(d.error || 'Lỗi', false);
}

async function approveDraft(draftId) {
    const r = await fetch(BASE_URL + '/api/studio/approve_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId, status: 'approved'})
    });
    const d = await r.json();
    if (d.ok) { toast('Đã approve!'); location.reload(); }
    else toast(d.error || 'Lỗi', false);
}

async function unapprove(draftId) {
    const r = await fetch(BASE_URL + '/api/studio/approve_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId, status: 'draft'})
    });
    const d = await r.json();
    if (d.ok) { toast('Đã đưa về draft.'); location.reload(); }
    else toast(d.error || 'Lỗi', false);
}

async function deleteDraft(draftId) {
    if (!confirm('Xoá hoạt động này?')) return;
    const r = await fetch(BASE_URL + '/api/studio/delete_draft.php', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({id: draftId})
    });
    const d = await r.json();
    if (d.ok) { toast('Đã xoá.'); window.location.href = BASE_URL + '/studio/ai_studio.php?lesson_id=' + LESSON.id; }
    else toast(d.error || 'Lỗi', false);
}
</script>

<?php
function renderDraftPreview(string $type, array $data): string {
    switch ($type) {
        case 'introduction':
            $out = '<strong>' . htmlspecialchars($data['title'] ?? '') . '</strong>';
            if (!empty($data['intro'])) $out .= '<br><em>' . htmlspecialchars($data['intro']) . '</em>';
            if (!empty($data['objectives'])) {
                $out .= '<ul class="mt-2 mb-0">';
                foreach ($data['objectives'] as $o) $out .= '<li>' . htmlspecialchars($o) . '</li>';
                $out .= '</ul>';
            }
            return $out;
        case 'theory':
            return $data['content'] ?? '';
        case 'example':
            $out = '<strong>Bài toán:</strong> ' . ($data['problem'] ?? '');
            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $i => $s) {
                    $out .= '<div class="mt-2"><span class="badge bg-secondary me-1">' . ($s['label'] ?? 'Bước '.($i+1)) . '</span>'
                          . '<strong>' . htmlspecialchars($s['title'] ?? '') . '</strong><br>'
                          . ($s['content'] ?? '') . '</div>';
                }
            }
            return $out;
        case 'quiz':
            $out = '<div class="mathjax-content"><strong>Câu hỏi:</strong> ' . ($data['question'] ?? '') . '</div>';
            if (!empty($data['options'])) {
                foreach ($data['options'] as $i => $o) {
                    $letter = chr(65 + $i);
                    $correct = isset($data['answer']) && $data['answer'] === $i;
                    $out .= '<div class="' . ($correct ? 'text-success fw-bold' : '') . '">'
                          . $letter . '. <span class="mathjax-content">' . render_inline(strip_formula_delimiters($o)) . '</span>'
                          . ($correct ? ' ✓' : '') . '</div>';
                }
            }
            if (!empty($data['explanation'])) $out .= '<div class="text-muted mt-1 small mathjax-content"><i class="bi bi-lightbulb me-1"></i>' . wrap_bare_latex_in_text($data['explanation']) . '</div>';
            return $out;
        case 'guided_practice':
            $out = '<div class="mathjax-content"><strong>Bài toán:</strong> ' . ($data['problem'] ?? '') . '</div>';
            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $i => $s) {
                    $answer_rendered = render_inline(strip_formula_delimiters($s['answer'] ?? ''));
                    $out .= '<div class="mt-2 p-2 border rounded mathjax-content"><strong>Bước ' . ($i+1) . ':</strong> '
                          . wrap_bare_latex_in_text($s['prompt'] ?? '') . '<br>'
                          . '<em class="text-success">→ ' . $answer_rendered . '</em></div>';
                }
            }
            return $out;
        case 'fill_answer':
            $out = '<div class="mathjax-content">' . ($data['question'] ?? '') . '</div>';
            $accepted_rendered = array_map(fn($a) => render_inline(strip_formula_delimiters($a)), $data['accepted'] ?? []);
            $out .= '<span class="badge bg-success mathjax-content">Accepted: ' . implode(', ', $accepted_rendered) . '</span>';
            if (!empty($data['explanation'])) $out .= '<div class="text-muted small mt-1 mathjax-content">' . wrap_bare_latex_in_text($data['explanation']) . '</div>';
            return $out;
        case 'reflection':
            $out = '<em>' . htmlspecialchars($data['prompt'] ?? '') . '</em><br>';
            foreach ($data['options'] ?? [] as $o) $out .= '<div class="mt-1">• ' . htmlspecialchars($o) . '</div>';
            return $out;
        case 'summary':
            $out = '';
            if (!empty($data['points'])) {
                $out .= '<strong>Kiến thức:</strong><ul class="mathjax-content">';
                foreach ($data['points'] as $p) $out .= '<li>' . wrap_bare_latex_in_text($p) . '</li>';
                $out .= '</ul>';
            }
            if (!empty($data['formulas'])) {
                $out .= '<strong>Công thức:</strong>';
                foreach ($data['formulas'] as $f) {
                    $out .= '<div class="mathjax-content">' . render_display(strip_formula_delimiters($f)) . '</div>';
                }
            }
            if (!empty($data['common_mistakes'])) {
                $out .= '<strong class="text-danger">Lỗi hay gặp:</strong><ul class="mathjax-content">';
                foreach ($data['common_mistakes'] as $m) $out .= '<li>' . wrap_bare_latex_in_text($m) . '</li>';
                $out .= '</ul>';
            }
            return $out ?: '(chưa có nội dung)';
        case 'ai_explanation':
            $out = '<strong>Chủ đề:</strong> ' . htmlspecialchars($data['topic'] ?? '') . '<br>';
            if (!empty($data['suggestions'])) {
                $out .= '<ul>';
                foreach ($data['suggestions'] as $s) $out .= '<li>' . htmlspecialchars($s) . '</li>';
                $out .= '</ul>';
            }
            return $out;
        default:
            return '<pre style="font-size:11px;">' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . '</pre>';
    }
}
?>
