<?php
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ' . BASE_URL . '/annonces.php');
    exit;
}

$db = getDB();

$annonce = $db->prepare('SELECT * FROM annonces WHERE id = ? AND actif = 1');
$annonce->execute([$id]);
$annonce = $annonce->fetch();

if (!$annonce) {
    header('Location: ' . BASE_URL . '/annonces.php');
    exit;
}

// Traitement d'un nouveau commentaire
$erreurCommentaire = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auteur  = trim($_POST['auteur'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    if ($auteur && $contenu) {
        $db->prepare('INSERT INTO commentaires (annonce_id, auteur, contenu) VALUES (?, ?, ?)')
           ->execute([$id, $auteur, $contenu]);
        header('Location: ' . BASE_URL . '/annonce.php?id=' . $id . '#commentaires');
        exit;
    } else {
        $erreurCommentaire = 'Nom et message obligatoires.';
    }
}

$commentaires = $db->prepare('SELECT * FROM commentaires WHERE annonce_id = ? ORDER BY created_at ASC');
$commentaires->execute([$id]);
$commentaires = $commentaires->fetchAll();

$pageTitle = htmlspecialchars($annonce['titre']) . ' — Annonces InfoHub CPNV';
require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/annonces.php">Annonces</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($annonce['titre']) ?></li>
    </ol>
</nav>

<div class="row g-4">

    <!-- Détail annonce -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <?php if ($annonce['image']): ?>
                <img src="<?= htmlspecialchars($annonce['image']) ?>"
                     class="card-img-top"
                     alt="<?= htmlspecialchars($annonce['titre']) ?>"
                     style="max-height:400px; object-fit:cover">
            <?php endif; ?>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h1 class="h3 fw-bold mb-0"><?= htmlspecialchars($annonce['titre']) ?></h1>
                    <?php if ($annonce['prix'] !== null): ?>
                        <span class="badge bg-success fs-6 px-3 py-2">
                            CHF <?= number_format($annonce['prix'], 2, '.', "'") ?>.-
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary fs-6 px-3 py-2">Prix à discuter</span>
                    <?php endif; ?>
                </div>

                <p class="text-muted small mb-4">
                    <i class="bi bi-calendar3 me-1"></i>
                    Publié le <?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                </p>

                <div class="fs-6 lh-lg" style="white-space: pre-line">
                    <?= htmlspecialchars($annonce['description']) ?>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Toutes les annonces
            </a>
        </div>
    </div>

    <!-- Sidebar : contact + commentaires -->
    <div class="col-lg-5">

        <!-- Contact -->
        <div class="card shadow-sm mb-4">
            <div class="card-header fw-semibold">
                <i class="bi bi-person-circle me-1"></i>Contacter le vendeur
            </div>
            <div class="card-body">
                <p class="mb-2 small text-muted">
                    Réponse le vendredi suivant l'annonce.<br>
                    Paiement par Twint à l'association si affaire conclue.
                </p>
                <a href="mailto:<?= htmlspecialchars($annonce['contact_email']) ?>"
                   class="btn btn-dark w-100">
                    <i class="bi bi-envelope me-1"></i>
                    <?= htmlspecialchars($annonce['contact_email']) ?>
                </a>
            </div>
        </div>

        <!-- Commentaires -->
        <div class="card shadow-sm" id="commentaires">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-chat-dots me-1"></i>Commentaires</span>
                <span class="badge bg-secondary"><?= count($commentaires) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if ($commentaires): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($commentaires as $c): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="small"><?= htmlspecialchars($c['auteur']) ?></strong>
                                    <span class="text-muted small"><?= date('d.m.Y', strtotime($c['created_at'])) ?></span>
                                </div>
                                <p class="mb-0 small"><?= nl2br(htmlspecialchars($c['contenu'])) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted small p-3 mb-0">Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <?php if ($erreurCommentaire): ?>
                    <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($erreurCommentaire) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-2">
                        <input type="text" name="auteur" class="form-control form-control-sm"
                               placeholder="Votre nom *" required
                               value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>">
                    </div>
                    <div class="mb-2">
                        <textarea name="contenu" class="form-control form-control-sm" rows="3"
                                  placeholder="Votre message *" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-send me-1"></i>Envoyer
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
