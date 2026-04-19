<?php
require_once __DIR__ . '/config.php';

$db = getDB();
$erreur  = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_verify(); }

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

$pubs = $db->query('
    SELECT p.*, u.nom AS auteur_nom, u.role AS auteur_role
    FROM pubs p
    JOIN utilisateurs u ON u.id = p.user_id
    ORDER BY p.created_at DESC
')->fetchAll();

$pageTitle = 'Publicités — InfoHub CPNV';
require_once __DIR__ . '/includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Accueil</a></li>
        <li class="breadcrumb-item active">Pubs</li>
    </ol>
</nav>

<div class="nb-section-header mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <div class="nb-tag nb-tag-cyan d-inline-block"><i class="bi bi-megaphone me-1"></i>Communauté</div>
            <h1 class="nb-section-title">Publicités</h1>
            <p class="nb-mono mt-1 mb-0" style="font-size:.75rem;color:#555">Petites annonces gratuites réservées aux membres de l'école.</p>
        </div>
        <?php if (isLoggedIn()): ?>
            <button class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#formPub">
                <i class="bi bi-plus-lg me-1"></i>Déposer une pub
            </button>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
               class="btn btn-outline-dark">
                <i class="bi bi-box-arrow-in-right me-1"></i>Connexion pour publier
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (isLoggedIn()): ?>
<div class="collapse <?= $erreur ? 'show' : '' ?> mb-4" id="formPub">
    <div class="card" style="border-color:#000 !important">
        <div class="card-header" style="background:var(--nb-yellow) !important">
            <i class="bi bi-pencil me-1"></i>Nouvelle publicité
        </div>
        <div class="card-body p-4">
            <?php if ($erreur): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="titre" class="form-control" maxlength="200"
                           placeholder="Ex. : Recherche co-voiturage, Donne clavier mécanique…"
                           value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message *</label>
                    <textarea name="contenu" class="form-control" rows="4"
                              placeholder="Décrivez votre annonce…" required><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-send me-1"></i>Publier
                </button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success mb-4" style="font-size:.85rem">
        <i class="bi bi-check-circle me-1"></i>Votre publicité a été publiée.
    </div>
<?php endif; ?>

<?php if ($pubs): ?>
    <?php $colors = ['var(--nb-yellow)','var(--nb-cyan)','var(--nb-pink)']; $ci = 0; ?>
    <div class="row g-3">
        <?php foreach ($pubs as $pub): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div style="height:6px;background:<?= $colors[$ci % 3] ?>;border-bottom:var(--nb-border)"></div>
                <div class="card-body">
                    <h2 class="h6 nb-upper fw-bold mb-2"><?= htmlspecialchars($pub['titre']) ?></h2>
                    <p class="small mb-0" style="color:#555;white-space:pre-line;line-height:1.7">
                        <?= htmlspecialchars($pub['contenu']) ?>
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="nb-mono" style="font-size:.7rem;color:#555">
                        <i class="bi bi-person me-1"></i><?= htmlspecialchars($pub['auteur_nom']) ?>
                        <?php if (in_array($pub['auteur_role'], ['admin', 'enseignant'])): ?>
                            <span class="badge bg-dark ms-1"><?= $pub['auteur_role'] ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="d-flex align-items-center gap-2 nb-mono" style="font-size:.7rem;color:#555">
                        <?= date('d.m.Y', strtotime($pub['created_at'])) ?>
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
        <?php $ci++; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div style="text-align:center;padding:4rem 0;color:#555">
        <div style="font-size:3rem;margin-bottom:1rem;opacity:.3"><i class="bi bi-megaphone"></i></div>
        <p class="nb-mono">Aucune publicité pour l'instant.<br>
        <?php if (isLoggedIn()): ?>
            Soyez le premier à publier !
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php" style="color:#000;font-weight:700">Connectez-vous</a> pour déposer la première.
        <?php endif; ?>
        </p>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
