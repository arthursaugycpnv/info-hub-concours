<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'InfoHub CPNV — Accueil';
$db = getDB();

$concours = $db->query('SELECT * FROM concours WHERE actif = 1 ORDER BY created_at DESC LIMIT 1')->fetch();
$news     = $db->query('SELECT * FROM news ORDER BY created_at DESC LIMIT 3')->fetchAll();
$annonces = $db->query('SELECT * FROM annonces WHERE actif = 1 ORDER BY created_at DESC LIMIT 6')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['registered'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 mb-4">
        <i class="bi bi-check-circle me-1"></i>
        Bienvenue ! Votre compte a été créé. Vous pouvez maintenant commenter les annonces et poster des publicités.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- HERO -->
<div class="ih-hero mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge-hero"><i class="bi bi-cpu me-1"></i>Assoce Info — Section informatique CPNV</span>
            <h1>Bienvenue sur<br><span style="color:var(--ih-yellow)">InfoHub</span></h1>
            <p class="lead mt-3 mb-4">
                Retrouvez le concours du mois, les dernières news de l'école
                et les annonces de la communauté. Réservé aux étudiants et enseignants de Sainte-Croix.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>/concours.php" class="btn btn-warning fw-semibold">
                    <i class="bi bi-trophy me-1"></i>Voir le concours
                </a>
                <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-light">
                    <i class="bi bi-tag me-1"></i>Annonces
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-outline-light">
                        <i class="bi bi-person-plus me-1"></i>Rejoindre
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-end">
            <div class="d-flex gap-3 flex-wrap justify-content-end">
                <div class="text-center p-3 rounded-3" style="background:rgba(255,255,255,.05)">
                    <div class="fw-bold fs-2" style="color:var(--ih-yellow)"><?= $db->query('SELECT COUNT(*) FROM news')->fetchColumn() ?></div>
                    <div class="small text-secondary">News</div>
                </div>
                <div class="text-center p-3 rounded-3" style="background:rgba(255,255,255,.05)">
                    <div class="fw-bold fs-2" style="color:var(--ih-yellow)"><?= $db->query('SELECT COUNT(*) FROM annonces WHERE actif=1')->fetchColumn() ?></div>
                    <div class="small text-secondary">Annonces</div>
                </div>
                <div class="text-center p-3 rounded-3" style="background:rgba(255,255,255,.05)">
                    <div class="fw-bold fs-2" style="color:var(--ih-yellow)"><?= $db->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn() ?></div>
                    <div class="small text-secondary">Membres</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($concours): ?>
<!-- CONCOURS DU MOIS -->
<section class="mb-5">
    <h2 class="ih-section-title">
        <span class="ih-icon ih-icon-yellow"><i class="bi bi-trophy-fill"></i></span>
        Concours du mois
    </h2>
    <div class="card concours-card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($concours['titre']) ?></h3>
                    <?php if ($concours['deadline']): ?>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-calendar-event me-1"></i>
                            Deadline : <strong><?= date('d.m.Y', strtotime($concours['deadline'])) ?></strong>
                        </p>
                    <?php endif; ?>
                </div>
                <?php if ($concours['prix']): ?>
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="bi bi-currency-exchange me-1"></i>CHF <?= number_format($concours['prix'], 2, '.', "'") ?>.-
                    </span>
                <?php endif; ?>
            </div>
            <p class="card-text mb-4" style="white-space:pre-line"><?= htmlspecialchars(mb_substr($concours['description'], 0, 300)) ?>…</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>/concours.php" class="btn btn-warning fw-semibold">
                    <i class="bi bi-arrow-right me-1"></i>Voir le concours complet
                </a>
                <?php if ($concours['pdf_url']): ?>
                    <a href="<?= htmlspecialchars($concours['pdf_url']) ?>" class="btn btn-outline-secondary" target="_blank">
                        <i class="bi bi-file-pdf me-1"></i>Descriptif PDF
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- NEWS -->
<section class="mb-5">
    <h2 class="ih-section-title">
        <span class="ih-icon ih-icon-blue"><i class="bi bi-newspaper"></i></span>
        Dernières news
    </h2>
    <?php if ($news): ?>
        <div class="row g-4">
            <?php foreach ($news as $article): ?>
                <div class="col-md-4">
                    <a href="<?= BASE_URL ?>/news.php?id=<?= $article['id'] ?>" class="text-decoration-none">
                        <div class="card h-100 card-hover shadow-sm">
                            <?php if ($article['image']): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($article['image']) ?>"
                                     class="card-img-top" alt=""
                                     style="height:160px;object-fit:cover">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-semibold text-dark mb-2" style="font-size:.95rem">
                                    <?= htmlspecialchars($article['titre']) ?>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= htmlspecialchars(mb_substr($article['contenu'], 0, 110)) ?>…
                                </p>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center small">
                                <span class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?></span>
                                <span class="text-muted"><?= date('d.m.Y', strtotime($article['created_at'])) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-secondary btn-sm">
                Toutes les news <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucune news pour le moment.</p>
    <?php endif; ?>
</section>

<!-- ANNONCES -->
<section class="mb-5">
    <h2 class="ih-section-title">
        <span class="ih-icon ih-icon-green"><i class="bi bi-tag-fill"></i></span>
        Annonces récentes
    </h2>
    <?php if ($annonces): ?>
        <div class="row g-3">
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-md-6 col-lg-4">
                    <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none">
                        <div class="card h-100 card-hover shadow-sm">
                            <?php if ($annonce['image']): ?>
                                <img src="<?= BASE_URL . htmlspecialchars($annonce['image']) ?>"
                                     class="card-img-top" alt=""
                                     style="height:130px;object-fit:cover">
                            <?php endif; ?>
                            <div class="card-body pb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <h6 class="card-title fw-semibold text-dark mb-0" style="font-size:.88rem">
                                        <?= htmlspecialchars($annonce['titre']) ?>
                                    </h6>
                                    <?php if ($annonce['prix'] !== null): ?>
                                        <span class="badge bg-success text-nowrap">CHF <?= number_format($annonce['prix'], 2, '.', "'") ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border">À discuter</span>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text text-muted small mb-0">
                                    <?= htmlspecialchars(mb_substr($annonce['description'], 0, 80)) ?>…
                                </p>
                            </div>
                            <div class="card-footer small text-muted">
                                <?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary btn-sm">
                Toutes les annonces <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucune annonce pour le moment.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
