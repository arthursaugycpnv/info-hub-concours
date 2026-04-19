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
    csrf_verify();
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
        <div class="card">
            <?php if ($annonce['image']): ?>
                <img src="<?= BASE_URL . htmlspecialchars($annonce['image']) ?>"
                     class="card-img-top" alt="<?= htmlspecialchars($annonce['titre']) ?>"
                     style="max-height:400px;object-fit:cover">
            <?php else: ?>
                <div style="height:8px;background:var(--nb-yellow);border-bottom:var(--nb-border)"></div>
            <?php endif; ?>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h1 class="nb-upper" style="font-size:1.5rem;font-weight:900;margin-bottom:0">
                        <?= htmlspecialchars($annonce['titre']) ?>
                    </h1>
                    <?php if ($annonce['prix'] !== null): ?>
                        <div style="background:var(--nb-cyan);border:var(--nb-border);padding:.5rem 1rem;font-size:1.4rem;font-weight:900;box-shadow:var(--nb-shadow-sm)">
                            CHF <?= number_format($annonce['prix'], 2, '.', "'") ?>.-
                        </div>
                    <?php else: ?>
                        <span class="badge bg-secondary" style="font-size:.8rem;padding:.5rem .85rem">À discuter</span>
                    <?php endif; ?>
                </div>
                <p class="nb-mono mb-4" style="font-size:.75rem;color:#555">
                    <i class="bi bi-calendar3 me-1"></i>Publié le <?= date('d.m.Y', strtotime($annonce['created_at'])) ?>
                </p>
                <div style="line-height:1.8;white-space:pre-line"><?= htmlspecialchars($annonce['description']) ?></div>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="<?= BASE_URL ?>/annonces.php" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Toutes les annonces
            </a>
            <?php if (isAdmin()): ?>
                <a href="<?= BASE_URL ?>/admin/index.php?tab=annonces" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil me-1"></i>Gérer dans l'admin
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-5">

        <!-- Contact vendeur -->
        <div class="card mb-4">
            <div class="card-header" style="background:var(--nb-pink) !important;color:#fff">
                <i class="bi bi-person-circle me-1"></i>Contacter le vendeur
            </div>
            <div class="card-body p-4">
                <p class="nb-mono mb-3" style="font-size:.8rem;color:#555">
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
        <div class="card" id="commentaires">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:var(--nb-cyan) !important">
                <span><i class="bi bi-chat-dots me-1"></i>Commentaires</span>
                <span class="badge bg-dark"><?= count($commentaires) ?></span>
            </div>
            <div class="card-body p-0">
                <?php if ($commentaires): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($commentaires as $c): ?>
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong class="nb-upper" style="font-size:.75rem"><?= htmlspecialchars($c['auteur']) ?></strong>
                                        <span class="nb-mono ms-2" style="font-size:.7rem;color:#555"><?= date('d.m.Y', strtotime($c['created_at'])) ?></span>
                                        <?php if (!$c['approuve']): ?>
                                            <span class="badge bg-warning ms-1">En attente</span>
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
                    <p class="nb-mono p-3 mb-0" style="font-size:.8rem;color:#555">Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer p-3">
                <?php if (isset($_GET['pending'])): ?>
                    <div class="alert alert-warning py-2 mb-3" style="font-size:.8rem">
                        <i class="bi bi-clock me-1"></i>Votre commentaire est en attente de modération.
                    </div>
                <?php endif; ?>
                <?php if ($erreurCommentaire): ?>
                    <div class="alert alert-danger py-2 mb-3" style="font-size:.8rem"><?= htmlspecialchars($erreurCommentaire) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?php if (!isLoggedIn()): ?>
                        <div class="mb-2">
                            <input type="text" name="auteur" class="form-control form-control-sm"
                                   placeholder="Votre nom *" required
                                   value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>">
                        </div>
                        <p class="nb-mono mb-2" style="font-size:.75rem;color:#555">
                            <i class="bi bi-info-circle me-1"></i>
                            <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" style="color:#000;font-weight:700">Connectez-vous</a>
                            pour publier immédiatement.
                        </p>
                    <?php else: ?>
                        <p class="nb-mono mb-2" style="font-size:.75rem;color:#555">
                            <i class="bi bi-person-check me-1"></i>En tant que <strong><?= htmlspecialchars($_SESSION['user_nom']) ?></strong>
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
