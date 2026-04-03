<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'InfoHub CPNV';
$db = getDB();

$concours = $db->query('SELECT * FROM concours WHERE actif = 1 ORDER BY created_at DESC LIMIT 1')->fetch();
$news     = $db->query('SELECT * FROM news ORDER BY created_at DESC LIMIT 3')->fetchAll();
$annonces = $db->query('SELECT * FROM annonces WHERE actif = 1 ORDER BY created_at DESC LIMIT 6')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($concours): ?>
<!-- CONCOURS DU MOIS -->
<section id="concours" class="mb-5">
    <h2 class="fw-bold border-bottom pb-2 mb-4">
        <i class="bi bi-trophy-fill text-warning me-2"></i>Concours du mois
    </h2>
    <div class="card border-warning shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <h3 class="card-title fw-bold mb-1"><?= htmlspecialchars($concours['titre']) ?></h3>
                <?php if ($concours['prix']): ?>
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                        CHF <?= number_format($concours['prix'], 2, '.', "'") ?>.-
                    </span>
                <?php endif; ?>
            </div>
            <?php if ($concours['deadline']): ?>
                <p class="text-muted small mb-3">
                    <i class="bi bi-calendar-event me-1"></i>
                    Deadline : <strong><?= date('d.m.Y', strtotime($concours['deadline'])) ?></strong>
                </p>
            <?php endif; ?>
            <p class="card-text" style="white-space: pre-line"><?= htmlspecialchars($concours['description']) ?></p>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>/concours.php" class="btn btn-warning fw-semibold">
                    <i class="bi bi-arrow-right me-1"></i>Voir le concours complet
                </a>
                <?php if ($concours['pdf_url']): ?>
                    <a href="<?= htmlspecialchars($concours['pdf_url']) ?>" class="btn btn-outline-warning" target="_blank">
                        <i class="bi bi-file-pdf me-1"></i>Descriptif PDF
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- NEWS -->
<section id="news" class="mb-5">
    <h2 class="fw-bold border-bottom pb-2 mb-4">
        <i class="bi bi-newspaper me-2"></i>News
    </h2>
    <?php if ($news): ?>
        <div class="row g-4">
            <?php foreach ($news as $article): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-semibold"><?= htmlspecialchars($article['titre']) ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= htmlspecialchars(mb_substr($article['contenu'], 0, 120)) ?>…
                            </p>
                            <a href="<?= BASE_URL ?>/news.php?id=<?= $article['id'] ?>" class="btn btn-outline-dark btn-sm mt-2 align-self-start">
                                Lire la suite <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-footer text-muted small d-flex justify-content-between">
                            <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($article['auteur']) ?></span>
                            <span><?= date('d.m.Y', strtotime($article['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/news.php" class="btn btn-outline-secondary btn-sm">Toutes les news <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucune news pour le moment.</p>
    <?php endif; ?>
</section>

<!-- ANNONCES -->
<section id="annonces" class="mb-5">
    <h2 class="fw-bold border-bottom pb-2 mb-4">
        <i class="bi bi-tag-fill me-2"></i>Annonces
    </h2>
    <?php if ($annonces): ?>
        <div class="row g-3">
            <?php foreach ($annonces as $annonce): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="card-title fw-semibold mb-1"><?= htmlspecialchars($annonce['titre']) ?></h6>
                                <?php if ($annonce['prix'] !== null): ?>
                                    <span class="badge bg-success ms-2">CHF <?= number_format($annonce['prix'], 2, '.', "'") ?>.-</span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text text-muted small mt-2">
                                <?= htmlspecialchars(mb_substr($annonce['description'], 0, 100)) ?>…
                            </p>
                        </div>
                        <div class="card-footer small d-flex justify-content-between align-items-center">
                            <a href="mailto:<?= htmlspecialchars($annonce['contact_email']) ?>" class="text-decoration-none text-muted">
                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($annonce['contact_email']) ?>
                            </a>
                            <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-outline-dark btn-sm py-0">
                                Voir <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-end mt-3">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary btn-sm">Toutes les annonces <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
    <?php else: ?>
        <p class="text-muted">Aucune annonce pour le moment.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
