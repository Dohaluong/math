<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-theory">
  <div class="act-badge act-badge--theory">
    <i class="bi bi-mortarboard-fill me-1"></i>Kiến thức
  </div>

  <h4 class="act-subtitle"><?= htmlspecialchars($activity['title']) ?></h4>

  <div class="act-content mathjax-content">
    <?= $data['content'] ?? '' ?>
  </div>

  <div class="act-footer">
    <button class="btn btn-primary" onclick="engine.next()">
      Tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>
