<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? 'Learning Studio') ?> · Learning Studio</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/studio.css">
  <?= $page_head ?? '' ?>
  <script>
    MathJax = {
      tex: { inlineMath: [['\\(','\\)'], ['$','$']], displayMath: [['\\[','\\]'], ['$$','$$']] },
      options: { skipHtmlTags: ['script','noscript','style','textarea'] }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>
</head>
<body class="studio-body">

<div class="studio-layout">

  <!-- Sidebar -->
  <nav class="studio-sidebar">
    <div class="studio-brand">
      <i class="bi bi-mortarboard-fill me-2"></i>Learning Studio
    </div>
    <ul class="studio-nav">
      <li>
        <a href="<?= BASE_URL ?>/studio/" class="studio-nav__link <?= ($active_studio ?? '') === 'dashboard' ? 'active' : '' ?>">
          <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
      </li>
      <li class="studio-nav__section">Nội dung</li>
      <li>
        <a href="<?= BASE_URL ?>/studio/curriculum.php" class="studio-nav__link <?= ($active_studio ?? '') === 'curriculum' ? 'active' : '' ?>">
          <i class="bi bi-map-fill"></i> Curriculum
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/studio/lessons.php" class="studio-nav__link <?= ($active_studio ?? '') === 'lessons' ? 'active' : '' ?>">
          <i class="bi bi-journal-text"></i> Bài học
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/studio/ai_studio.php" class="studio-nav__link <?= ($active_studio ?? '') === 'ai_studio' ? 'active' : '' ?>">
          <i class="bi bi-stars"></i> AI Studio
        </a>
      </li>
      <li class="studio-nav__section">Quy trình</li>
      <li>
        <a href="<?= BASE_URL ?>/studio/review.php" class="studio-nav__link <?= ($active_studio ?? '') === 'review' ? 'active' : '' ?>">
          <i class="bi bi-check2-square"></i> Review Queue
          <?php
          // Badge for pending drafts
          global $db;
          if (!isset($db)) { $db = get_db(); }
          $pending = $db->query('SELECT COUNT(*) FROM ls_draft WHERE status="draft"')->fetchColumn();
          if ($pending > 0): ?>
          <span class="badge bg-warning text-dark ms-auto"><?= $pending ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/studio/publish.php" class="studio-nav__link <?= ($active_studio ?? '') === 'publish' ? 'active' : '' ?>">
          <i class="bi bi-send-fill"></i> Publish Center
          <?php
          $approved = $db->query('SELECT COUNT(*) FROM ls_draft WHERE status="approved"')->fetchColumn();
          if ($approved > 0): ?>
          <span class="badge bg-success ms-auto"><?= $approved ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="studio-nav__section">Cấu hình</li>
      <li>
        <a href="<?= BASE_URL ?>/studio/prompts.php" class="studio-nav__link <?= ($active_studio ?? '') === 'prompts' ? 'active' : '' ?>">
          <i class="bi bi-code-slash"></i> Prompt Library
        </a>
      </li>
    </ul>
    <div class="studio-sidebar__footer">
      <a href="<?= BASE_URL ?>/pages/math7.php" class="text-muted small text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Về trang học sinh
      </a>
    </div>
  </nav>

  <!-- Main content -->
  <main class="studio-main">
    <div class="studio-topbar">
      <h1 class="studio-topbar__title"><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
      <?= $topbar_actions ?? '' ?>
    </div>
    <div class="studio-content">
