<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-example" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--example">
    <i class="bi bi-pencil-square me-1"></i>Ví dụ từng bước
  </div>

  <div class="act-example-problem mathjax-content">
    <?= wrap_bare_latex_in_text($data['problem'] ?? '') ?>
  </div>

  <div class="act-steps" id="steps-<?= $id ?>">
    <?php foreach ($data['steps'] as $i => $step): ?>
    <div class="act-step" id="step-<?= $id ?>-<?= $i ?>" style="display:<?= $i === 0 ? 'block' : 'none' ?>;">
      <div class="act-step__header">
        <span class="act-step__label"><?= htmlspecialchars($step['label']) ?></span>
        <span class="act-step__title"><?= htmlspecialchars($step['title']) ?></span>
      </div>
      <div class="act-step__body mathjax-content">
        <?= $step['content'] ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="act-footer d-flex gap-2 align-items-center">
    <span class="text-muted small" id="step-counter-<?= $id ?>">
      Bước 1 / <?= count($data['steps']) ?>
    </span>
    <button class="btn btn-outline-primary ms-auto" id="btn-step-<?= $id ?>"
            onclick="exampleNext(<?= $id ?>, <?= count($data['steps']) ?>, <?= $step_idx ?>)">
      Bước tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>
