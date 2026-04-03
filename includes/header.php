<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'InfoHub CPNV') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">
            <i class="bi bi-cpu me-2"></i>InfoHub CPNV
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="bi bi-house me-1"></i>Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/concours.php"><i class="bi bi-trophy me-1"></i>Concours</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/news.php"><i class="bi bi-newspaper me-1"></i>News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/annonces.php"><i class="bi bi-tag me-1"></i>Annonces</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php if (isAdmin()): ?>
<div class="bg-warning py-1 px-3 d-flex justify-content-between align-items-center small">
    <span><i class="bi bi-shield-fill me-1"></i>Connecté en tant qu'admin : <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong></span>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-dark btn-sm py-0">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-outline-dark btn-sm py-0">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>
</div>
<?php endif; ?>

<main class="container py-4">
