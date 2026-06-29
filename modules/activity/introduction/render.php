<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-introduction">
  <div class="act-badge act-badge--intro">
    <i class="bi bi-door-open-fill me-1"></i>Mở đầu
  </div>

  <h3 class="act-title"><?= htmlspecialchars($data['title'] ?? $activity['title']) ?></h3>

  <p class="act-intro-text"><?= htmlspecialchars($data['intro'] ?? '') ?></p>

  <?php if (!empty($data['objectives'])): ?>
  <div class="act-objectives">
    <div class="act-objectives__label">
      <i class="bi bi-bullseye me-1"></i>Sau bài học này bạn sẽ:
    </div>
    <ul class="act-objectives__list">
      <?php foreach ($data['objectives'] as $obj): ?>
      <li><i class="bi bi-check2 text-success me-1"></i><?= htmlspecialchars($obj) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <div class="act-footer">
    <button class="btn btn-primary btn-lg w-100" onclick="engine.next()">
      Bắt đầu <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>
