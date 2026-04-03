<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Annonces — InfoHub CPNV';
$db = getDB();

// ── Filtre et tri ────────────────────────────────────────────────────────────
$recherche = trim($_GET['q'] ?? '');
$tri       = in_array($_GET['tri'] ?? '', ['date_desc', 'date_asc', 'prix_asc', 'prix_desc']) ? $_GET['tri'] : 'date_desc';

$orderBy = match($tri) {
    'date_asc'   => 'created_at ASC',
    'prix_asc'   => 'prix IS NULL ASC, prix ASC',
    'prix_desc'  => 'prix IS NULL ASC, prix DESC',
    default      => 'created_at DESC',
};

$params = [];
$where  = 'WHERE actif = 1';
if ($recherche !== '') {
    $where   .= ' AND (titre LIKE ? OR description LIKE ?)';
    $params[] = "%$recherche%";
    $params[] = "%$recherche%";
}

$stmt = $db->prepare("SELECT * FROM annonces $where ORDER BY $orderBy");
$stmt->execute($params);
$annonces = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">Annonces</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
    <h1 class="h2 fw-bold mb-0">
        <i class="bi bi-tag-fill me-2"></i>Annonces
    </h1>
    <?php if (isAdmin()): ?>
        <a href="<?= BASE_URL ?>/admin/index.php?tab=annonces" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Gérer les annonces
        </a>
    <?php endif; ?>
</div>
<p class="text-muted small mb-4">
    Réservées en priorité aux étudiants de la section informatique de Sainte-Croix.
    Pour poster une annonce : <a href="mailto:CPNV_Infohub@eduvaud.ch">CPNV_Infohub@eduvaud.ch</a>
</p>

<!-- Filtre -->
<form method="GET" class="card card-body shadow-sm mb-4 p-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small fw-semibold mb-1">Recherche</label>
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Titre ou description…"
                   value="<?= htmlspecialchars($recherche) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold mb-1">Trier par</label>
            <select name="tri" class="form-select form-select-sm">
                <option value="date_desc"  <?= $tri === 'date_desc'  ? 'selected' : '' ?>>Plus récentes</option>
                <option value="date_asc"   <?= $tri === 'date_asc'   ? 'selected' : '' ?>>Plus anciennes</option>
                <option value="prix_asc"   <?= $tri === 'prix_asc'   ? 'selected' : '' ?>>Prix croissant</option>
                <option value="prix_desc"  <?= $tri === 'prix_desc'  ? 'selected' : '' ?>>Prix décroissant</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">
                <i class="bi bi-search me-1"></i>Filtrer
            </button>
            <?php if ($recherche || $tri !== 'date_desc'): ?>
                <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($annonces): ?>
    <?php if ($recherche): ?>
        <p class="text-muted small mb-3"><?= count($annonces) ?> résultat(s) pour « <?= htmlspecialchars($recherche) ?> »</p>
    <?php endif; ?>
    <div class="row g-4">
        <?php foreach ($annonces as $annonce): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm card-hover">
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
                            <p class="card-text text-muted small flex-grow-1" style="white-space:pre-line">
                                <?= htmlspecialchars(mb_substr($annonce['description'], 0, 120)) ?>…
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                            </span>
                            <span class="btn btn-dark btn-sm">Voir <i class="bi bi-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-tag fs-1 d-block mb-3"></i>
        <?php if ($recherche): ?>
            <p>Aucune annonce pour « <?= htmlspecialchars($recherche) ?> ».</p>
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary mt-2">Voir toutes les annonces</a>
        <?php else: ?>
            <p>Aucune annonce pour le moment.<br>
               Pour publier une annonce, contactez <a href="mailto:CPNV_Infohub@eduvaud.ch">CPNV_Infohub@eduvaud.ch</a>
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
.card-hover { transition: transform .15s, box-shadow .15s; }
.card-hover:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
