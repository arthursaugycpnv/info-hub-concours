<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$erreur  = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $titre   = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    if ($titre && $contenu) {
        $db->prepare('INSERT INTO pubs (user_id, titre, contenu) VALUES (?, ?, ?)')
           ->execute([$_SESSION['user_id'], $titre, $contenu]);
        header('Location: ' . BASE_URL . '/pubs.php?ok=1');
        exit;
    } else {
        $erreur = 'Titre et message obligatoires.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete_pub') {
        $id = (int)($_POST['pub_id'] ?? 0);
        if ($id) {
            $db->prepare('DELETE FROM pubs WHERE id = ?')->execute([$id]);
            header('Location: ' . BASE_URL . '/pubs.php');
            exit;
        }
    }
}

$pubs = $db->query('
    SELECT p.*, u.nom AS auteur_nom, u.role AS auteur_role
    FROM pubs p
    JOIN utilisateurs u ON u.id = p.user_id
    ORDER BY p.created_at DESC
')->fetchAll();

$pageTitle = 'Publicités — InfoHub CPNV';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 fw-bold mb-1"><i class="bi bi-megaphone me-2"></i>Publicités</h1>
        <p class="text-muted mb-0 small">Petites annonces gratuites réservées aux membres de l'école.</p>
    </div>
    <?php if (isLoggedIn()): ?>
        <button class="btn btn-warning fw-semibold" data-bs-toggle="collapse" data-bs-target="#formPub">
            <i class="bi bi-plus-lg me-1"></i>Déposer une pub
        </button>
    <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
           class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-in-right me-1"></i>Connectez-vous pour publier
        </a>
    <?php endif; ?>
</div>

<?php if (isLoggedIn()): ?>
<div class="collapse <?= $erreur ? 'show' : '' ?> mb-4" id="formPub">
    <div class="card shadow-sm border-warning">
        <div class="card-header fw-semibold bg-warning bg-opacity-25">
            <i class="bi bi-pencil me-1"></i>Nouvelle publicité
        </div>
        <div class="card-body">
            <?php if ($erreur): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Titre *</label>
                    <input type="text" name="titre" class="form-control" maxlength="200"
                           placeholder="Ex. : Recherche co-voiturage, Donne clavier mécanique…"
                           value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Message *</label>
                    <textarea name="contenu" class="form-control" rows="4"
                              placeholder="Décrivez votre annonce…" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-warning fw-semibold">
                    <i class="bi bi-send me-1"></i>Publier
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success py-2 small mb-4">
        <i class="bi bi-check-circle me-1"></i>Votre publicité a été publiée.
    </div>
<?php endif; ?>

<?php if ($pubs): ?>
    <div class="row g-3">
        <?php foreach ($pubs as $pub): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-semibold mb-2"><?= htmlspecialchars($pub['titre']) ?></h5>
                    <p class="card-text small text-muted lh-lg" style="white-space:pre-line">
                        <?= htmlspecialchars($pub['contenu']) ?>
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center small text-muted">
                    <span>
                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($pub['auteur_nom']) ?>
                        <?php if (in_array($pub['auteur_role'], ['admin', 'enseignant'])): ?>
                            <span class="badge bg-secondary ms-1"><?= $pub['auteur_role'] ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="d-flex align-items-center gap-2">
                        <span><?= date('d.m.Y', strtotime($pub['created_at'])) ?></span>
                        <?php if (isAdmin()): ?>
                            <form method="POST" onsubmit="return confirm('Supprimer cette publicité ?')">
                                <input type="hidden" name="action" value="delete_pub">
                                <input type="hidden" name="pub_id" value="<?= $pub['id'] ?>">
                                <button class="btn btn-outline-danger btn-sm py-0 px-1 border-0" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5 text-muted">
        <i class="bi bi-megaphone fs-1 d-block mb-3 opacity-25"></i>
        Aucune publicité pour l'instant.<br>
        <?php if (isLoggedIn()): ?>
            Soyez le premier à publier !
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php">Connectez-vous</a> pour déposer la première.
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
