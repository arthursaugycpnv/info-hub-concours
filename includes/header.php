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

<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/index.php" class="nb-logo-wrap me-3">
            <div class="nb-logo-deco"></div>
            <div class="nb-logo-box">
                <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="" height="34" style="border:2px solid #000">
                <div>
                    <div class="nb-logo-title">ASSO<span>.INFO</span></div>
                    <span class="nb-logo-sub">Section Informatique CPNV</span>
                </div>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="bi bi-list"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto gap-1 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link nb-nav-yellow <?= $currentPage === 'index.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nb-nav-pink <?= $currentPage === 'concours.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/concours.php">Concours</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nb-nav-cyan <?= in_array($currentPage, ['news.php']) ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/news.php">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nb-nav-yellow <?= in_array($currentPage, ['annonces.php','annonce.php']) ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/annonces.php">Annonces</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nb-nav-pink <?= $currentPage === 'pubs.php' ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/pubs.php">Pubs</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link nb-nav-cyan dropdown-toggle d-flex align-items-center gap-1" href="#" data-bs-toggle="dropdown">
                            <span class="fw-900 d-inline-flex align-items-center justify-content-center"
                                  style="width:22px;height:22px;background:#000;color:var(--nb-yellow);font-size:.7rem;font-weight:900;border:2px solid #000">
                                <?= mb_strtoupper(mb_substr($_SESSION['user_nom'], 0, 1)) ?>
                            </span>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width:180px">
                            <li class="px-3 py-1" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#555">
                                <?= ucfirst($_SESSION['user_role'] ?? '') ?>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard admin
                                </a></li>
                                <li><hr class="dropdown-divider my-1" style="border-color:#000"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link nb-nav-cyan" href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm fw-semibold" href="<?= BASE_URL ?>/register.php">S'inscrire</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (isAdmin()): ?>
<div class="admin-bar py-1 px-3 d-flex justify-content-between align-items-center">
    <span><i class="bi bi-shield-fill me-1"></i>Mode admin — <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong></span>
    <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-dark btn-sm py-0 px-2">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
    </a>
</div>
<?php endif; ?>

<main class="container py-4 flex-grow-1">
