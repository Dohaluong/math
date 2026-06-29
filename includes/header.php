<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'Toán 7') ?> — Toán 7</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/app.css" rel="stylesheet">
<script>
  MathJax = {
    tex: { inlineMath: [['\\(','\\)'], ['$','$']], displayMath: [['\\[','\\]'], ['$$','$$']] },
    options: { skipHtmlTags: ['script','noscript','style','textarea'] }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" async></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top">
  <div class="container-fluid px-3" style="max-width:780px;margin:auto;">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="<?= BASE_URL ?>/">
      <i class="bi bi-calculator-fill"></i> Toán 7
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto gap-1">
        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'home' ? 'active fw-semibold' : '' ?>"
             href="<?= BASE_URL ?>/pages/home.php">
            <i class="bi bi-house"></i> Trang chủ
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'math7' ? 'active fw-semibold' : '' ?>"
             href="<?= BASE_URL ?>/pages/math7.php">
            <i class="bi bi-book"></i> Toán 7
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'history' ? 'active fw-semibold' : '' ?>"
             href="<?= BASE_URL ?>/pages/history.php">
            <i class="bi bi-clock-history"></i> Lịch sử
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($active_page ?? '') === 'profile' ? 'active fw-semibold' : '' ?>"
             href="<?= BASE_URL ?>/pages/profile.php">
            <i class="bi bi-person-circle"></i> Hồ sơ
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="main-container py-4">
