<?php
/* vars: $activity, $data, $step_idx, $is_last, $id */

// Normalize a quiz option per SPEC-005: strip legacy delimiters/prefix, wrap bare LaTeX in \(...\)
if (!function_exists('normalize_quiz_option')) {
    function normalize_quiz_option(string $opt): string {
        return render_inline(strip_formula_delimiters($opt));
    }
}
?>
<div class="act-card act-quiz" data-activity-id="<?= $id ?>" data-idx="<?= $step_idx ?>">
  <div class="act-badge act-badge--quiz">
    <i class="bi bi-question-circle-fill me-1"></i>Kiểm tra nhanh
  </div>

  <div class="act-quiz-question mathjax-content" id="q-text-<?= $id ?>">
    <?= wrap_bare_latex_in_text($data['question'] ?? '') ?>
  </div>

  <div class="act-quiz-options" id="opts-<?= $id ?>">
    <?php foreach ($data['options'] as $i => $opt): ?>
    <button class="act-option" data-idx="<?= $i ?>"
            onclick="quizSelect(<?= $id ?>, <?= $i ?>, <?= (int)$data['answer'] ?>, this)">
      <span class="act-option__letter"><?= chr(65 + $i) ?></span>
      <span class="mathjax-content"><?= normalize_quiz_option($opt) ?></span>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Hint -->
  <?php if (!empty($data['hint'])): ?>
  <div id="hint-<?= $id ?>" class="act-hint d-none">
    <i class="bi bi-lightbulb-fill me-1 text-warning"></i>
    <span class="mathjax-content"><?= wrap_bare_latex_in_text($data['hint'] ?? '') ?></span>
  </div>
  <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
          onclick="document.getElementById('hint-<?= $id ?>').classList.toggle('d-none'); if(window.MathJax) MathJax.typesetPromise([document.getElementById('hint-<?= $id ?>')])">
    <i class="bi bi-lightbulb me-1"></i>Xem gợi ý
  </button>
  <?php endif; ?>

  <!-- Feedback (hidden until submitted) -->
  <div id="feedback-<?= $id ?>" class="act-feedback d-none"></div>

  <div class="act-footer">
    <button class="btn btn-primary d-none" id="next-btn-<?= $id ?>" onclick="engine.next()">
      Tiếp theo <i class="bi bi-arrow-right ms-1"></i>
    </button>
  </div>
</div>

<script>
var quizExplanation_<?= $id ?> = <?= json_encode($data['explanation'] ?? '') ?>;
</script>
