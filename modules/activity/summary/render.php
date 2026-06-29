<?php
/* vars: $activity, $data, $step_idx, $is_last, $id */

// SPEC-005: DB stores bare LaTeX, renderer adds \[...\]
if (!function_exists('wrap_display_math')) {
    function wrap_display_math(string $f): string {
        return render_display(strip_formula_delimiters($f));
    }
}
?>
<div class="act-card act-summary" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--summary">
    <i class="bi bi-bookmark-star-fill me-1"></i>Tóm tắt
  </div>

  <?php if (!empty($data['points'])): ?>
  <div class="act-summary-section">
    <div class="act-summary-label">Kiến thức chính</div>
    <ul class="act-summary-list">
      <?php foreach ($data['points'] as $pt): ?>
      <li class="mathjax-content"><?= $pt ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <?php if (!empty($data['formulas'])): ?>
  <div class="act-summary-section">
    <div class="act-summary-label">Công thức</div>
    <?php foreach ($data['formulas'] as $f): ?>
    <div class="act-formula mathjax-content"><?= wrap_display_math($f) ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($data['common_mistakes'])): ?>
  <div class="act-summary-section">
    <div class="act-summary-label text-danger">
      <i class="bi bi-exclamation-triangle-fill me-1"></i>Lỗi hay gặp
    </div>
    <ul class="act-summary-list act-summary-list--mistakes">
      <?php foreach ($data['common_mistakes'] as $m): ?>
      <li class="mathjax-content"><?= $m ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="act-footer">
    <button class="btn btn-success btn-lg w-100" onclick="engine.finish()">
      <i class="bi bi-trophy-fill me-2"></i>Hoàn thành bài học!
    </button>
  </div>
</div>
