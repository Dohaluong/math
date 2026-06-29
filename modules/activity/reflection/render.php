<?php /* vars: $activity, $data, $step_idx, $is_last, $id */ ?>
<div class="act-card act-reflection" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--reflection">
    <i class="bi bi-emoji-smile me-1"></i>Nhìn lại
  </div>

  <div class="act-reflection-prompt">
    <?= htmlspecialchars($data['prompt'] ?? '') ?>
  </div>

  <div class="act-reflection-options" id="refl-opts-<?= $id ?>">
    <?php foreach ($data['options'] as $i => $opt): ?>
    <button class="act-refl-option" data-idx="<?= $i ?>"
            onclick="reflectionSelect(<?= $id ?>, <?= $i ?>, this)">
      <?= htmlspecialchars($opt) ?>
    </button>
    <?php endforeach; ?>
  </div>

  <div class="act-footer">
    <button class="btn btn-primary d-none" id="next-btn-<?= $id ?>" onclick="engine.next()">
      <?= $is_last ? 'Hoàn thành bài học <i class="bi bi-trophy ms-1"></i>' : 'Tiếp theo <i class="bi bi-arrow-right ms-1"></i>' ?>
    </button>
  </div>
</div>
