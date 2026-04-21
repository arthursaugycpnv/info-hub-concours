<?php
require_once __DIR__ . '/config.php';

$db = getDB();

// ── Mode détail ──────────────────────────────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $article = $db->prepare('SELECT * FROM news WHERE id = ?');
    $article->execute([$id]);
    $article = $article->fetch();

    if (!$article) {
        header('Location: ' . BASE_URL . '/news.php');
        exit;
    }

    $pageTitle = htmlspecialchars($article['titre']) . ' — InfoHub CPNV';
    require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/news.php">News</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($article['titre']) ?></li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <article class="card shadow-sm">
            <?php if ($article['image']): ?>
                <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>" class="card-img-top" alt="" style="max-height:350px; object-fit:cover">
            <?php endif; ?>
            <div class="card-body p-4">
                <h1 class="h2 fw-bold mb-2"><?= htmlspecialchars($article['titre']) ?></h1>
                <p class="text-muted small mb-4">
                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?>
                    <span class="mx-2">·</span>
                    <i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y', strtotime($article['created_at'])) ?>
                </p>
                <div class="fs-6 lh-lg" style="white-space: pre-line"><?= htmlspecialchars($article['contenu']) ?></div>
            </div>
        </article>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Toutes les news
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/admin/index.php?tab=news&edit=<?= $article['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Modifier cette news
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// ── Mode liste ───────────────────────────────────────────────────────────────
$pageTitle = 'News — InfoHub CPNV';
$news = $db->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">News</li>
    </ol>
</nav>

<h1 class="ih-section-title">
    <span class="ih-icon ih-icon-blue"><i class="bi bi-newspaper"></i></span>News
</h1>

<?php if ($news): ?>
    <div class="row g-4">
        <?php foreach ($news as $article): ?>
            <div class="col-md-6 col-lg-4">
                <article class="card h-100 shadow-sm">
                    <?php if ($article['image']): ?>
                        <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>" class="card-img-top" alt="" style="height:180px; object-fit:cover">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 card-title fw-semibold"><?= htmlspecialchars($article['titre']) ?></h2>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars(mb_substr($article['contenu'], 0, 150)) ?>…
                        </p>
                        <a href="<?= BASE_URL ?>/news.php?id=<?= $article['id'] ?>" class="btn btn-outline-dark btn-sm mt-2 align-self-start">
                            Lire la suite <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-footer text-muted small d-flex justify-content-between">
                        <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?></span>
                        <span><?= date('d.m.Y', strtotime($article['created_at'])) ?></span>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-newspaper fs-1 d-block mb-3"></i>
        <p>Aucune news pour le moment.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary mt-2">← Retour à l'accueil</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
