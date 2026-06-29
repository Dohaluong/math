<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-guided" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--guided">
    <i class="bi bi-hand-index-thumb-fill me-1"></i>Luyện tập có hướng dẫn
  </div>

  <div class="act-guided-problem mathjax-content">
    <?= wrap_bare_latex_in_text($data['problem'] ?? '') ?>
  </div>

  <div id="guided-steps-<?= $id ?>">
    <?php foreach ($data['steps'] as $i => $step): ?>
    <div class="act-guided-step <?= $i > 0 ? 'd-none' : '' ?>" id="gstep-<?= $id ?>-<?= $i ?>">
      <div class="act-guided-step__prompt mathjax-content">
        <?= wrap_bare_latex_in_text($step['prompt'] ?? '') ?>
      </div>
      <div class="d-flex gap-2">
        <input type="text" class="form-control"
               id="ginput-<?= $id ?>-<?= $i ?>"
               placeholder="Nhập đáp án..."
               onkeydown="if(event.key==='Enter') guidedSubmit(<?= $id ?>, <?= $i ?>, <?= count($data['steps']) ?>)">
        <button class="btn btn-primary flex-shrink-0"
                onclick="guidedSubmit(<?= $id ?>, <?= $i ?>, <?= count($data['steps']) ?>)">
          <i class="bi bi-check2"></i>
        </button>
      </div>
      <div id="gfeedback-<?= $id ?>-<?= $i ?>" class="act-feedback d-none mt-2"></div>
    </div>
    <?php endforeach; ?>
  </div>

  <div id="guided-complete-<?= $id ?>" class="d-none">
    <div class="act-feedback act-feedback--correct">
      <i class="bi bi-trophy-fill me-2"></i>Xuất sắc! Bạn đã hoàn thành tất cả các bước.
    </div>
  </div>

  <div class="act-footer">
    <button class="btn btn-primary d-none" id="next-btn-<?= $id ?>" onclick="engine.next()">
      Tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>

<script>
var guidedSteps_<?= $id ?> = <?= json_encode($data['steps']) ?>;
</script>
