<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Annonces — InfoHub CPNV';
$db = getDB();

$annonces = $db->query('SELECT * FROM annonces WHERE actif = 1 ORDER BY created_at DESC')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">Annonces</li>
    </ol>
</nav>

<h1 class="h2 fw-bold border-bottom pb-2 mb-2">
    <i class="bi bi-tag-fill me-2"></i>Annonces
</h1>
<p class="text-muted small mb-4">
    Réservées en priorité aux étudiants de la section informatique de Sainte-Croix.
    Pour poster une annonce : <a href="mailto:CPNV_Infohub@eduvaud.ch">CPNV_Infohub@eduvaud.ch</a>
</p>

<?php if ($annonces): ?>
    <div class="row g-4">
        <?php foreach ($annonces as $annonce): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($annonce['image']): ?>
                        <img src="<?= htmlspecialchars($annonce['image']) ?>" class="card-img-top" alt="" style="height:200px; object-fit:cover">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:120px">
                            <i class="bi bi-tag text-secondary fs-1"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h2 class="h6 fw-semibold mb-0"><?= htmlspecialchars($annonce['titre']) ?></h2>
                            <?php if ($annonce['prix'] !== null): ?>
                                <span class="badge bg-success ms-2 flex-shrink-0">
                                    CHF <?= number_format($annonce['prix'], 2, '.', "'") ?>.-
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary ms-2 flex-shrink-0">Prix à discuter</span>
                            <?php endif; ?>
                        </div>
                        <p class="card-text text-muted small flex-grow-1" style="white-space: pre-line">
                            <?= htmlspecialchars($annonce['description']) ?>
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                        </span>
                        <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-dark btn-sm">
                            Voir <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-tag fs-1 d-block mb-3"></i>
        <p>Aucune annonce pour le moment.<br>
           Pour publier une annonce, contactez <a href="mailto:CPNV_Infohub@eduvaud.ch">CPNV_Infohub@eduvaud.ch</a>
        </p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary mt-2">← Retour à l'accueil</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
