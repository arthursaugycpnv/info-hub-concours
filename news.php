<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── Mode détail ──────────────────────────────────────────────────────────────
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
        <article class="card">
            <?php if ($article['image']): ?>
                <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>" class="card-img-top" alt="" style="max-height:350px;object-fit:cover">
            <?php else: ?>
                <div style="height:8px;background:var(--nb-cyan);border-bottom:var(--nb-border)"></div>
            <?php endif; ?>
            <div class="card-body p-4">
                <div class="nb-tag nb-tag-cyan d-inline-block mb-3">
                    <i class="bi bi-newspaper me-1"></i>News
                </div>
                <h1 class="nb-upper" style="font-size:1.75rem;font-weight:900;margin-bottom:.75rem">
                    <?= htmlspecialchars($article['titre']) ?>
                </h1>
                <p class="nb-mono mb-4" style="font-size:.8rem;color:#555">
                    <i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?>
                    <span class="mx-2">·</span>
                    <i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y', strtotime($article['created_at'])) ?>
                </p>
                <div style="line-height:1.8;white-space:pre-line"><?= htmlspecialchars($article['contenu']) ?></div>
            </div>
        </article>
        <div class="mt-3 d-flex gap-2">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Toutes les news
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/admin/index.php?tab=news&edit=<?= $article['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Modifier
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

<div class="nb-section-header mb-4">
    <div class="nb-tag nb-tag-cyan d-inline-block"><i class="bi bi-newspaper me-1"></i>Actualités</div>
    <h1 class="nb-section-title">Les dernières nouvelles</h1>
    <p class="nb-mono mt-1 mb-0" style="font-size:.8rem;color:#555">Restez informé de tout ce qui se passe dans l'asso</p>
</div>

<?php if ($news): ?>
    <?php $colors = ['var(--nb-yellow)','var(--nb-cyan)','var(--nb-pink)']; $ci = 0; ?>
    <!-- Article principal (premier) -->
    <?php $featured = $news[0]; ?>
    <div class="card mb-5 position-relative">
        <div class="row g-0">
            <?php if ($featured['image']): ?>
                <div class="col-md-5">
                    <img src="<?= BASE_URL . htmlspecialchars($featured['image']) ?>"
                         class="img-fluid" alt=""
                         style="height:100%;min-height:280px;object-fit:cover;border-right:var(--nb-border)">
                </div>
                <div class="col-md-7">
            <?php else: ?>
                <div style="height:8px;background:var(--nb-yellow);border-bottom:var(--nb-border)"></div>
                <div class="col-12">
            <?php endif; ?>
                    <div class="card-body p-4 d-flex flex-column justify-content-between h-100">
                        <div>
                            <div class="nb-tag d-inline-block mb-3" style="background:var(--nb-yellow);transform:none">
                                <i class="bi bi-star-fill me-1"></i>À la une
                            </div>
                            <h2 class="nb-upper" style="font-size:1.5rem;font-weight:900;margin-bottom:.75rem">
                                <?= htmlspecialchars($featured['titre']) ?>
                            </h2>
                            <p class="nb-mono mb-3" style="font-size:.75rem;color:#555">
                                <i class="bi bi-person me-1"></i><?= htmlspecialchars($featured['auteur']) ?>
                                <span class="mx-2">·</span><?= date('d.m.Y', strtotime($featured['created_at'])) ?>
                            </p>
                            <p style="color:#555;line-height:1.7">
                                <?= htmlspecialchars(mb_substr($featured['contenu'], 0, 200)) ?>…
                            </p>
                        </div>
                        <a href="<?= BASE_URL ?>/news.php?id=<?= $featured['id'] ?>" class="btn btn-dark mt-3 align-self-start">
                            Lire la suite <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
        </div>
    </div>

    <!-- Grille des autres articles -->
    <?php if (count($news) > 1): ?>
    <div class="row g-4">
        <?php foreach (array_slice($news, 1) as $article): ?>
            <div class="col-md-6 col-lg-4">
                <article class="card h-100">
                    <?php if ($article['image']): ?>
                        <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>" class="card-img-top" alt="" style="height:180px;object-fit:cover">
                    <?php else: ?>
                        <div style="height:8px;background:<?= $colors[$ci % 3] ?>;border-bottom:var(--nb-border)"></div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="nb-tag d-inline-block mb-2" style="background:<?= $colors[$ci % 3] ?>;transform:none">
                            <?= date('d.m.Y', strtotime($article['created_at'])) ?>
                        </div>
                        <h2 class="h6 nb-upper fw-bold mb-2"><?= htmlspecialchars($article['titre']) ?></h2>
                        <p class="small flex-grow-1" style="color:#555">
                            <?= htmlspecialchars(mb_substr($article['contenu'], 0, 150)) ?>…
                        </p>
                        <a href="<?= BASE_URL ?>/news.php?id=<?= $article['id'] ?>" class="btn btn-dark btn-sm mt-2 align-self-start">
                            Lire <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <span class="nb-mono" style="font-size:.7rem;color:#555">
                            <i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?>
                        </span>
                    </div>
                </article>
            </div>
            <?php $ci++; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
<?php else: ?>
    <div style="text-align:center;padding:4rem 0;color:#555">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:.3"><i class="bi bi-newspaper"></i></div>
        <p class="nb-mono">Aucune news pour le moment.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-dark btn-sm mt-2">← Retour à l'accueil</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
