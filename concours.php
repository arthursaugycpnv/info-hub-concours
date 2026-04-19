<?php
require_once __DIR__ . '/config.php';

$pageTitle = 'Concours du mois — InfoHub CPNV';
$db = getDB();

$concours = $db->query('SELECT * FROM concours WHERE actif = 1 ORDER BY created_at DESC LIMIT 1')->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">Concours du mois</li>
    </ol>
</nav>

<?php if ($concours): ?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- En-tête concours -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--nb-pink) !important;color:#fff">
                <span><i class="bi bi-trophy-fill me-1"></i>Concours du mois</span>
                <?php if ($concours['prix']): ?>
                    <span style="font-size:1.1rem;font-weight:900">CHF <?= number_format($concours['prix'], 0, '.', "'") ?>.-</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-4">
                <div class="nb-tag nb-tag-pink d-inline-block mb-3">
                    <i class="bi bi-trophy me-1"></i>À gagner
                </div>
                <h1 class="nb-upper mb-3" style="font-size:1.75rem;font-weight:900">
                    <?= htmlspecialchars($concours['titre']) ?>
                </h1>

                <?php if ($concours['deadline']): ?>
                    <?php
                        $deadline = new DateTime($concours['deadline']);
                        $today    = new DateTime();
                        $jours    = (int)$today->diff($deadline)->format('%r%a');
                    ?>
                    <div class="mb-4 p-3" style="background:<?= $jours <= 7 ? 'var(--nb-pink)' : 'var(--nb-yellow)' ?>;border:var(--nb-border);color:<?= $jours <= 7 ? '#fff' : '#000' ?>">
                        <i class="bi bi-calendar-event me-1"></i>
                        <strong>Deadline : <?= $deadline->format('d.m.Y') ?></strong>
                        <?php if ($jours > 0): ?>
                            — <?= $jours ?> jour<?= $jours > 1 ? 's' : '' ?> restant<?= $jours > 1 ? 's' : '' ?>
                        <?php elseif ($jours === 0): ?>
                            — <strong>C'est aujourd'hui !</strong>
                        <?php else: ?>
                            — Délai dépassé
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div style="line-height:1.8;white-space:pre-line"><?= htmlspecialchars($concours['description']) ?></div>

                <?php if ($concours['pdf_url']): ?>
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($concours['pdf_url']) ?>" class="btn btn-outline-dark btn-sm" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Télécharger le descriptif
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulaire d'inscription -->
        <?php if (!isset($concours['deadline']) || new DateTime() <= new DateTime($concours['deadline'])): ?>
        <div class="card">
            <div class="card-header" style="background:var(--nb-cyan) !important">
                <i class="bi bi-pencil-square me-1"></i>Annoncer ma participation
            </div>
            <div class="card-body p-4">
                <?php if (isset($_GET['inscrit'])): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Inscription enregistrée ! Vous recevrez une confirmation à votre adresse email.
                    </div>
                <?php else: ?>
                <form method="POST" action="<?= BASE_URL ?>/inscription.php">
                    <input type="hidden" name="concours_id" value="<?= $concours['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom complet *</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (@eduvaud.ch) *</label>
                            <input type="email" name="email" class="form-control" placeholder="prenom.nom@eduvaud.ch" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Participation</label>
                            <select name="type" class="form-select">
                                <option value="individuel">Individuelle</option>
                                <option value="groupe">En groupe</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Membres du groupe</label>
                            <input type="text" name="membres" class="form-control" placeholder="Prénom Nom, Prénom Nom…">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-dark">
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
<div style="text-align:center;padding:5rem 0;color:#555">
    <div style="font-size:4rem;margin-bottom:1rem;opacity:.3"><i class="bi bi-trophy"></i></div>
    <p class="nb-mono">Aucun concours en cours pour le moment.<br>Revenez bientôt !</p>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-dark btn-sm mt-2">← Retour à l'accueil</a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
