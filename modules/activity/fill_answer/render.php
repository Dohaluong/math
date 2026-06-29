<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-fill" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--fill">
    <i class="bi bi-input-cursor-text me-1"></i>Điền đáp án
  </div>

  <div class="act-fill-question mathjax-content">
    <?= wrap_bare_latex_in_text($data['question'] ?? '') ?>
  </div>

  <div class="act-fill-input-group">
    <input type="text" id="fill-input-<?= $id ?>"
           class="form-control act-fill-input"
           placeholder="Nhập đáp án..."
           onkeydown="if(event.key==='Enter') fillSubmit(<?= $id ?>)">
    <button class="btn btn-primary" onclick="fillSubmit(<?= $id ?>)">
      <i class="bi bi-check2"></i> Kiểm tra
    </button>
  </div>

  <?php if (!empty($data['hint'])): ?>
  <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none mt-2"
          onclick="document.getElementById('fill-hint-<?= $id ?>').classList.toggle('d-none')">
    <i class="bi bi-lightbulb me-1"></i>Xem gợi ý
  </button>
  <div id="fill-hint-<?= $id ?>" class="act-hint d-none mathjax-content">
    <?= wrap_bare_latex_in_text($data['hint'] ?? '') ?>
  </div>
  <?php endif; ?>

  <div id="fill-feedback-<?= $id ?>" class="act-feedback d-none"></div>

  <div class="act-footer">
    <button class="btn btn-primary d-none" id="next-btn-<?= $id ?>" onclick="engine.next()">
      Tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>

<script>
var fillAccepted_<?= $id ?>    = <?= json_encode(array_map('mb_strtolower', $data['accepted'] ?? [])) ?>;
var fillExplanation_<?= $id ?> = <?= json_encode($data['explanation'] ?? '') ?>;
</script>
