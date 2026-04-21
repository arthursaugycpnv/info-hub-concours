<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Annonces — InfoHub CPNV';
$db = getDB();

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

<div class="nb-section-header mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div class="nb-tag nb-tag-yellow d-inline-block"><i class="bi bi-tag-fill me-1"></i>Petites annonces</div>
            <h1 class="nb-section-title">Annonces</h1>
            <p class="nb-mono mt-1 mb-0" style="font-size:.75rem;color:#555">
                Réservées en priorité aux étudiants de la section info de Sainte-Croix.<br>
                Pour poster : <a href="mailto:CPNV_Infohub@eduvaud.ch" style="color:#000;font-weight:700">CPNV_Infohub@eduvaud.ch</a>
            </p>
        </div>
        <?php if (isAdmin()): ?>
            <a href="<?= BASE_URL ?>/admin/index.php?tab=annonces" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Gérer
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filtre -->
<form method="GET" class="card mb-4" style="box-shadow:var(--nb-shadow-sm)">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Recherche</label>
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Titre ou description…"
                       value="<?= htmlspecialchars($recherche) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Trier par</label>
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
    </div>
</form>

<?php if ($annonces): ?>
    <?php if ($recherche): ?>
        <p class="nb-mono mb-3" style="font-size:.8rem;color:#555">
            <?= count($annonces) ?> résultat(s) pour « <?= htmlspecialchars($recherche) ?> »
        </p>
    <?php endif; ?>
    <?php $colors = ['var(--nb-yellow)','var(--nb-cyan)','var(--nb-pink)']; $ci = 0; ?>
    <div class="row g-4">
        <?php foreach ($annonces as $annonce): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= BASE_URL ?>/annonce.php?id=<?= $annonce['id'] ?>" class="text-decoration-none text-reset">
                    <div class="card h-100">
                        <?php if ($annonce['image']): ?>
                            <img src="<?= BASE_URL . htmlspecialchars($annonce['image']) ?>" class="card-img-top" alt="" style="height:200px;object-fit:cover">
                        <?php else: ?>
                            <div style="height:8px;background:<?= $colors[$ci % 3] ?>;border-bottom:var(--nb-border)"></div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h2 class="h6 fw-bold nb-upper mb-0"><?= htmlspecialchars($annonce['titre']) ?></h2>
                                <?php if ($annonce['prix'] !== null): ?>
                                    <span class="badge bg-success ms-2 flex-shrink-0">CHF <?= number_format($annonce['prix'], 0, '.', "'") ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary ms-2 flex-shrink-0">À discuter</span>
                                <?php endif; ?>
                            </div>
                            <p class="small flex-grow-1" style="color:#555;white-space:pre-line">
                                <?= htmlspecialchars(mb_substr($annonce['description'], 0, 120)) ?>…
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <span class="nb-mono" style="font-size:.7rem;color:#555">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                            </span>
                            <span class="btn btn-dark btn-sm">Voir <i class="bi bi-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php $ci++; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div style="text-align:center;padding:4rem 0;color:#555">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:.3"><i class="bi bi-tag"></i></div>
        <?php if ($recherche): ?>
            <p class="nb-mono">Aucune annonce pour « <?= htmlspecialchars($recherche) ?> ».</p>
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-dark btn-sm mt-2">Voir toutes les annonces</a>
        <?php else: ?>
            <p class="nb-mono">Aucune annonce pour le moment.<br>
               Pour publier : <a href="mailto:CPNV_Infohub@eduvaud.ch" style="color:#000;font-weight:700">CPNV_Infohub@eduvaud.ch</a>
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
