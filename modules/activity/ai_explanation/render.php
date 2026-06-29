<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-ai" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--ai">
    <i class="bi bi-robot me-1"></i>Hỏi AI
  </div>

  <p class="text-muted small mb-3">
    Có thắc mắc gì về <strong><?= htmlspecialchars($data['topic'] ?? '') ?></strong>? Hãy hỏi AI!
  </p>

  <?php if (!empty($data['suggestions'])): ?>
  <div class="act-ai-suggestions mb-3">
    <div class="small text-muted mb-2">Gợi ý câu hỏi:</div>
    <div class="d-flex flex-wrap gap-2">
      <?php foreach ($data['suggestions'] as $sug): ?>
      <button class="btn btn-sm btn-outline-primary rounded-pill"
              onclick="var inp=document.getElementById('ai-input-<?= $id ?>'); inp.value=<?= htmlspecialchars(json_encode($sug, JSON_UNESCAPED_UNICODE), ENT_QUOTES) ?>; inp.focus();">
        <?= htmlspecialchars($sug) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="ai-chat-box">
    <div class="ai-messages" id="ai-msgs-<?= $id ?>"></div>
    <div class="d-flex gap-2 mt-2">
      <input type="text" id="ai-input-<?= $id ?>" class="form-control form-control-sm"
             placeholder="Hỏi AI bất cứ điều gì..."
             onkeydown="if(event.key==='Enter') aiFreeChat(<?= $id ?>, <?= (int)($lesson_id ?? 0) ?>)">
      <button class="btn btn-primary btn-sm"
              onclick="aiFreeChat(<?= $id ?>, <?= (int)($lesson_id ?? 0) ?>)">
        <i class="bi bi-send-fill"></i>
      </button>
    </div>
  </div>

  <div class="act-footer">
    <button class="btn btn-primary" onclick="engine.next()">
      Tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>
