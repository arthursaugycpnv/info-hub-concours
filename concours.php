<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Concours du mois — InfoHub CPNV';
$db = getDB();

$concours = $db->query('SELECT * FROM concours WHERE actif = 1 ORDER BY created_at DESC LIMIT 1')->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($concours): ?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">Concours du mois</li>
    </ol>
</nav>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- En-tête -->
        <div class="card border-warning shadow mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                    <div>
                        <span class="badge bg-warning text-dark mb-2">
                            <i class="bi bi-trophy-fill me-1"></i>Concours du mois
                        </span>
                        <h1 class="h2 fw-bold mb-0"><?= htmlspecialchars($concours['titre']) ?></h1>
                    </div>
                    <?php if ($concours['prix']): ?>
                        <div class="text-center">
                            <div class="fs-3 fw-bold text-warning">CHF <?= number_format($concours['prix'], 2, '.', "'") ?>.-</div>
                            <div class="text-muted small">Prix à gagner</div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($concours['deadline']): ?>
                    <?php
                        $deadline = new DateTime($concours['deadline']);
                        $today    = new DateTime();
                        $diff     = $today->diff($deadline);
                        $joursRestants = (int)$diff->format('%r%a');
                    ?>
                    <div class="alert <?= $joursRestants <= 7 ? 'alert-danger' : 'alert-warning' ?> py-2 mb-3">
                        <i class="bi bi-calendar-event me-1"></i>
                        <strong>Deadline : <?= $deadline->format('d.m.Y') ?></strong>
                        <?php if ($joursRestants > 0): ?>
                            — <span><?= $joursRestants ?> jour<?= $joursRestants > 1 ? 's' : '' ?> restant<?= $joursRestants > 1 ? 's' : '' ?></span>
                        <?php elseif ($joursRestants === 0): ?>
                            — <span>C'est aujourd'hui !</span>
                        <?php else: ?>
                            — <span>Délai dépassé</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="fs-6" style="white-space: pre-line"><?= htmlspecialchars($concours['description']) ?></div>

                <?php if ($concours['pdf_url']): ?>
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($concours['pdf_url']) ?>" class="btn btn-outline-warning" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Télécharger le descriptif complet
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire d'inscription -->
        <?php if (!isset($concours['deadline']) || new DateTime() <= new DateTime($concours['deadline'])): ?>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-pencil-square me-1"></i>Annoncer ma participation
            </div>
            <div class="card-body">
                <?php if (isset($_GET['inscrit'])): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Inscription enregistrée ! Vous recevrez une confirmation à votre adresse email.
                    </div>
                <?php else: ?>
                <form method="POST" action="/inscription.php">
                    <input type="hidden" name="concours_id" value="<?= $concours['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nom complet *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email (@eduvaud.ch) *</label>
                            <input type="email" name="email" class="form-control" placeholder="prenom.nom@eduvaud.ch" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Participation</label>
                            <select name="type" class="form-select">
                                <option value="individuel">Individuelle</option>
                                <option value="groupe">En groupe</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Membres du groupe</label>
                            <input type="text" name="membres" class="form-control" placeholder="Prénom Nom, Prénom Nom…">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning fw-semibold">
                                <i class="bi bi-send me-1"></i>M'inscrire au concours
                            </button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php else: ?>
<div class="text-center py-5 text-muted">
    <i class="bi bi-trophy fs-1 d-block mb-3"></i>
    <p>Aucun concours en cours pour le moment.<br>Revenez bientôt !</p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary mt-2">← Retour à l'accueil</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
