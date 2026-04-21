<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'InfoHub CPNV') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="L'Assoce Info" height="46"
                 style="border-radius:.5rem; box-shadow:0 0 0 2px rgba(255,255,255,.15)">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1">
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
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/pubs.php"><i class="bi bi-megaphone me-1"></i>Pubs</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                  style="width:26px;height:26px;font-size:.75rem;background:var(--ed-amber);color:#fff">
                                <?= mb_strtoupper(mb_substr($_SESSION['user_nom'], 0, 1)) ?>
                            </span>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:180px">
                            <li class="dropdown-header small text-muted py-1"><?= ucfirst($_SESSION['user_role'] ?? '') ?></li>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php">
                                    <i class="bi bi-speedometer2 me-2 text-warning"></i>Dashboard admin
                                </a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item d-flex align-items-center gap-2">
                        <a class="nav-link" href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Connexion</a>
                        <a class="btn btn-warning btn-sm fw-semibold px-3"
                           href="<?= BASE_URL ?>/register.php">S'inscrire</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (isAdmin()): ?>
<div class="admin-bar py-1 px-3 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-shield-fill me-1"></i>Mode admin — <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong></span>
    <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-dark btn-sm py-0 px-2" style="font-size:.75rem">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
    </a>
</div>
<?php endif; ?>

<main class="container py-4 flex-grow-1">
