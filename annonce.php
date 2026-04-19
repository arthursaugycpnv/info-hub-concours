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

// Traitement des formulaires
$erreurCommentaire = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'add_comment';

    if ($action === 'delete_comment' && isAdmin()) {
        $cid = (int)($_POST['comment_id'] ?? 0);
        if ($cid) {
            $db->prepare('DELETE FROM commentaires WHERE id = ? AND annonce_id = ?')->execute([$cid, $id]);
        }
        header('Location: ' . BASE_URL . '/annonce.php?id=' . $id . '#commentaires');
        exit;
    }

    if ($action === 'approve_comment' && isAdmin()) {
        $cid = (int)($_POST['comment_id'] ?? 0);
        if ($cid) {
            $db->prepare('UPDATE commentaires SET approuve = 1 WHERE id = ? AND annonce_id = ?')->execute([$cid, $id]);
        }
        header('Location: ' . BASE_URL . '/annonce.php?id=' . $id . '#commentaires');
        exit;
    }

    $auteur  = isLoggedIn() ? $_SESSION['user_nom'] : trim($_POST['auteur'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    if ($auteur && $contenu) {
        $approuve = isLoggedIn() ? 1 : 0;
        $userId   = isLoggedIn() ? $_SESSION['user_id'] : null;
        $db->prepare('INSERT INTO commentaires (annonce_id, user_id, auteur, contenu, approuve) VALUES (?, ?, ?, ?, ?)')
           ->execute([$id, $userId, $auteur, $contenu, $approuve]);
        $msg = $approuve ? '' : '?pending=1';
        header('Location: ' . BASE_URL . '/annonce.php?id=' . $id . $msg . '#commentaires');
        exit;
    } else {
        $erreurCommentaire = 'Nom et message obligatoires.';
    }
}

// Admin voit tout, visiteurs voient seulement les approuvés
$commentaires = isAdmin()
    ? $db->prepare('SELECT * FROM commentaires WHERE annonce_id = ? ORDER BY created_at ASC')
    : $db->prepare('SELECT * FROM commentaires WHERE annonce_id = ? AND approuve = 1 ORDER BY created_at ASC');
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
                <img src="<?= BASE_URL . htmlspecialchars($annonce['image']) ?>"
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

        <div class="mt-3 d-flex gap-2">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Toutes les annonces
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/admin/index.php#tab-annonces" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Gérer dans l'admin
                </a>
            <?php endif; ?>
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
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong class="small"><?= htmlspecialchars($c['auteur']) ?></strong>
                                        <span class="text-muted small ms-2"><?= date('d.m.Y', strtotime($c['created_at'])) ?></span>
                                        <?php if (!$c['approuve']): ?>
                                            <span class="badge bg-warning text-dark ms-1 small">En attente</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (isAdmin()): ?>
                                        <div class="d-flex gap-1 ms-2">
                                            <?php if (!$c['approuve']): ?>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="approve_comment">
                                                    <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                                    <button class="btn btn-outline-success btn-sm py-0 px-1 border-0" title="Approuver">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" onsubmit="return confirm('Supprimer ce commentaire ?')">
                                                <input type="hidden" name="action" value="delete_comment">
                                                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                                <button class="btn btn-outline-danger btn-sm py-0 px-1 border-0" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
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
                <?php if (isset($_GET['pending'])): ?>
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-clock me-1"></i>Votre commentaire est en attente de modération.
                    </div>
                <?php endif; ?>
                <?php if ($erreurCommentaire): ?>
                    <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($erreurCommentaire) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?php if (!isLoggedIn()): ?>
                        <div class="mb-2">
                            <input type="text" name="auteur" class="form-control form-control-sm"
                                   placeholder="Votre nom *" required
                                   value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>">
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-info-circle me-1"></i>
                            <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Connectez-vous</a>
                            pour que votre commentaire soit publié immédiatement.
                        </p>
                    <?php else: ?>
                        <p class="small text-muted mb-2">
                            <i class="bi bi-person-check me-1"></i>Commentaire en tant que <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong>
                        </p>
                    <?php endif; ?>
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
