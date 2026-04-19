<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$db = getDB();
$message = '';
$erreur  = '';

// ── Traitement des formulaires ──────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_concours') {
        $titre       = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix        = $_POST['prix'] !== '' ? (float)$_POST['prix'] : null;
        $deadline    = $_POST['deadline'] !== '' ? $_POST['deadline'] : null;

        if ($titre && $description) {
            // Désactiver les anciens concours
            $db->exec('UPDATE concours SET actif = 0');
            $stmt = $db->prepare('INSERT INTO concours (titre, description, prix, deadline, actif) VALUES (?, ?, ?, ?, 1)');
            $stmt->execute([$titre, $description, $prix, $deadline]);
            $message = 'Concours ajouté avec succès.';
        } else {
            $erreur = 'Titre et description obligatoires.';
        }
    }

    if ($action === 'add_news') {
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $auteur  = trim($_POST['auteur'] ?? '');

        if ($titre && $contenu && $auteur) {
            $stmt = $db->prepare('INSERT INTO news (titre, contenu, auteur) VALUES (?, ?, ?)');
            $stmt->execute([$titre, $contenu, $auteur]);
            $message = 'News ajoutée avec succès.';
        } else {
            $erreur = 'Tous les champs sont obligatoires.';
        }
    }

    if ($action === 'add_annonce') {
        $titre       = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix        = $_POST['prix'] !== '' ? (float)$_POST['prix'] : null;
        $contact     = trim($_POST['contact_email'] ?? '');

        if ($titre && $description && $contact) {
            $stmt = $db->prepare('INSERT INTO annonces (titre, description, prix, contact_email, actif) VALUES (?, ?, ?, ?, 1)');
            $stmt->execute([$titre, $description, $prix, $contact]);
            $message = 'Annonce ajoutée avec succès.';
        } else {
            $erreur = 'Titre, description et email obligatoires.';
        }
    }

    if ($action === 'edit_news') {
        $id      = (int)($_POST['id'] ?? 0);
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');
        $auteur  = trim($_POST['auteur'] ?? '');
        if ($id && $titre && $contenu && $auteur) {
            $db->prepare('UPDATE news SET titre=?, contenu=?, auteur=? WHERE id=?')
               ->execute([$titre, $contenu, $auteur, $id]);
            $message = 'News modifiée.';
        } else {
            $erreur = 'Tous les champs sont obligatoires.';
        }
    }

    if ($action === 'edit_annonce') {
        $id          = (int)($_POST['id'] ?? 0);
        $titre       = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix        = $_POST['prix'] !== '' ? (float)$_POST['prix'] : null;
        $contact     = trim($_POST['contact_email'] ?? '');
        if ($id && $titre && $description && $contact) {
            $db->prepare('UPDATE annonces SET titre=?, description=?, prix=?, contact_email=? WHERE id=?')
               ->execute([$titre, $description, $prix, $contact, $id]);
            $message = 'Annonce modifiée.';
        } else {
            $erreur = 'Titre, description et email obligatoires.';
        }
    }

    if ($action === 'edit_concours') {
        $id          = (int)($_POST['id'] ?? 0);
        $titre       = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $prix        = $_POST['prix'] !== '' ? (float)$_POST['prix'] : null;
        $deadline    = $_POST['deadline'] !== '' ? $_POST['deadline'] : null;
        if ($id && $titre && $description) {
            $db->prepare('UPDATE concours SET titre=?, description=?, prix=?, deadline=? WHERE id=?')
               ->execute([$titre, $description, $prix, $deadline, $id]);
            $message = 'Concours modifié.';
        } else {
            $erreur = 'Titre et description obligatoires.';
        }
    }

    if ($action === 'delete_news') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $db->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
            $message = 'News supprimée.';
        }
    }

    if ($action === 'delete_annonce') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $db->prepare('DELETE FROM annonces WHERE id = ?')->execute([$id]);
            $message = 'Annonce supprimée.';
        }
    }

    if ($action === 'approve_comment') {
        $id = (int)($_POST['comment_id'] ?? 0);
        if ($id) {
            $db->prepare('UPDATE commentaires SET approuve = 1 WHERE id = ?')->execute([$id]);
            $message = 'Commentaire approuvé.';
        }
    }

    if ($action === 'delete_comment') {
        $id = (int)($_POST['comment_id'] ?? 0);
        if ($id) {
            $db->prepare('DELETE FROM commentaires WHERE id = ?')->execute([$id]);
            $message = 'Commentaire supprimé.';
        }
    }
}

// ── Données pour l'affichage ────────────────────────────────────────────────
$concours = $db->query('SELECT * FROM concours ORDER BY created_at DESC LIMIT 5')->fetchAll();
$news     = $db->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
$annonces = $db->query('
    SELECT a.*, COUNT(c.id) AS nb_commentaires
    FROM annonces a
    LEFT JOIN commentaires c ON c.annonce_id = a.id
    GROUP BY a.id
    ORDER BY a.created_at DESC
')->fetchAll();
$inscriptions = $db->query('
    SELECT i.*, co.titre AS concours_titre
    FROM inscriptions i
    JOIN concours co ON co.id = i.concours_id
    ORDER BY i.created_at DESC
')->fetchAll();

$pendingComments = $db->query('
    SELECT c.*, a.titre AS annonce_titre
    FROM commentaires c
    JOIN annonces a ON a.id = c.annonce_id
    WHERE c.approuve = 0
    ORDER BY c.created_at ASC
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — InfoHub CPNV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/admin/index.php">
        <i class="bi bi-cpu me-2"></i>InfoHub — Admin
    </a>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-light btn-sm" target="_blank">
            <i class="bi bi-globe me-1"></i>Voir le site
        </a>
        <span class="text-secondary small d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>
</nav>

<div class="container py-4">

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show py-2">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2">
            <?= htmlspecialchars($erreur) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php
        $activeTab = $_GET['tab'] ?? 'concours';
        $editNews  = (int)($_GET['edit'] ?? 0);
    ?>
    <!-- TABS -->
    <ul class="nav nav-tabs mb-4" id="adminTabs">
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'concours' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-concours">
                <i class="bi bi-trophy me-1"></i>Concours
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'news' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-news">
                <i class="bi bi-newspaper me-1"></i>News
                <span class="badge bg-secondary ms-1"><?= count($news) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'annonces' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-annonces">
                <i class="bi bi-tag me-1"></i>Annonces
                <span class="badge bg-secondary ms-1"><?= count($annonces) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'inscriptions' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-inscriptions">
                <i class="bi bi-people me-1"></i>Inscriptions
                <span class="badge bg-secondary ms-1"><?= count($inscriptions) ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'moderation' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-moderation">
                <i class="bi bi-shield-check me-1"></i>Modération
                <?php if (count($pendingComments)): ?>
                    <span class="badge bg-danger ms-1"><?= count($pendingComments) ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ── CONCOURS ── -->
        <div class="tab-pane fade <?= $activeTab === 'concours' ? 'show active' : '' ?>" id="tab-concours">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">Nouveau concours</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_concours">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Titre *</label>
                                    <input type="text" name="titre" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Description *</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="5" required></textarea>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label class="form-label small fw-semibold">Prix (CHF)</label>
                                        <input type="number" name="prix" class="form-control form-control-sm" step="0.01" min="0">
                                    </div>
                                    <div class="col">
                                        <label class="form-label small fw-semibold">Deadline</label>
                                        <input type="date" name="deadline" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-info-circle me-1"></i>L'ancien concours actif sera archivé automatiquement.
                                </p>
                                <button type="submit" class="btn btn-dark btn-sm w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Publier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">Historique</div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr><th>Titre</th><th>Prix</th><th>Deadline</th><th>Statut</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($concours as $c): ?>
                                    <tr style="cursor:pointer" onclick="window.open('<?= BASE_URL ?>/concours.php', '_blank')" title="Voir le concours">
                                        <td class="small fw-semibold"><?= htmlspecialchars($c['titre']) ?></td>
                                        <td class="small"><?= $c['prix'] ? 'CHF '.$c['prix'] : '—' ?></td>
                                        <td class="small"><?= $c['deadline'] ? date('d.m.Y', strtotime($c['deadline'])) : '—' ?></td>
                                        <td>
                                            <?= $c['actif'] ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-secondary">Archivé</span>' ?>
                                        </td>
                                        <td onclick="event.stopPropagation()">
                                            <button class="btn btn-outline-warning btn-sm py-0 px-1"
                                                data-bs-toggle="modal" data-bs-target="#modalEditConcours"
                                                data-id="<?= $c['id'] ?>"
                                                data-titre="<?= htmlspecialchars($c['titre'], ENT_QUOTES) ?>"
                                                data-description="<?= htmlspecialchars($c['description'], ENT_QUOTES) ?>"
                                                data-prix="<?= $c['prix'] ?? '' ?>"
                                                data-deadline="<?= $c['deadline'] ?? '' ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── NEWS ── -->
        <div class="tab-pane fade <?= $activeTab === 'news' ? 'show active' : '' ?>" id="tab-news">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">Nouvelle news</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_news">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Titre *</label>
                                    <input type="text" name="titre" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Contenu *</label>
                                    <textarea name="contenu" class="form-control form-control-sm" rows="6" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Auteur *</label>
                                    <input type="text" name="auteur" class="form-control form-control-sm" required>
                                </div>
                                <button type="submit" class="btn btn-dark btn-sm w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Publier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">News publiées</div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr><th>Titre</th><th>Auteur</th><th>Date</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($news as $n): ?>
                                    <tr style="cursor:pointer" onclick="window.open('<?= BASE_URL ?>/news.php?id=<?= $n['id'] ?>', '_blank')" title="Voir la news">
                                        <td class="small fw-semibold"><?= htmlspecialchars($n['titre']) ?></td>
                                        <td class="small"><?= htmlspecialchars($n['auteur']) ?></td>
                                        <td class="small text-muted"><?= date('d.m.Y', strtotime($n['created_at'])) ?></td>
                                        <td onclick="event.stopPropagation()">
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-outline-warning btn-sm py-0 px-1"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditNews"
                                                    data-id="<?= $n['id'] ?>"
                                                    data-titre="<?= htmlspecialchars($n['titre'], ENT_QUOTES) ?>"
                                                    data-contenu="<?= htmlspecialchars($n['contenu'], ENT_QUOTES) ?>"
                                                    data-auteur="<?= htmlspecialchars($n['auteur'], ENT_QUOTES) ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" onsubmit="return confirm('Supprimer cette news ?')">
                                                    <input type="hidden" name="action" value="delete_news">
                                                    <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-1">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── ANNONCES ── -->
        <div class="tab-pane fade <?= $activeTab === 'annonces' ? 'show active' : '' ?>" id="tab-annonces">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">Nouvelle annonce</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_annonce">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Titre *</label>
                                    <input type="text" name="titre" class="form-control form-control-sm" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Description *</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="4" required></textarea>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label class="form-label small fw-semibold">Prix (CHF)</label>
                                        <input type="number" name="prix" class="form-control form-control-sm" step="0.01" min="0">
                                    </div>
                                    <div class="col">
                                        <label class="form-label small fw-semibold">Email contact *</label>
                                        <input type="email" name="contact_email" class="form-control form-control-sm" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-dark btn-sm w-100">
                                    <i class="bi bi-plus-circle me-1"></i>Publier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header fw-semibold">Annonces publiées</div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Prix</th>
                                        <th>Contact</th>
                                        <th>Date</th>
                                        <th><i class="bi bi-chat-dots"></i></th>
                                        <th>Statut</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($annonces as $a): ?>
                                    <tr style="cursor:pointer" onclick="window.open('<?= BASE_URL ?>/annonce.php?id=<?= $a['id'] ?>', '_blank')" title="Voir l'annonce">
                                        <td class="small fw-semibold"><?= htmlspecialchars($a['titre']) ?></td>
                                        <td class="small"><?= $a['prix'] !== null ? 'CHF '.$a['prix'] : '—' ?></td>
                                        <td class="small">
                                            <a href="mailto:<?= htmlspecialchars($a['contact_email']) ?>" class="text-decoration-none" onclick="event.stopPropagation()">
                                                <?= htmlspecialchars($a['contact_email']) ?>
                                            </a>
                                        </td>
                                        <td class="small text-muted"><?= date('d.m.Y', strtotime($a['created_at'])) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $a['nb_commentaires'] > 0 ? 'bg-primary' : 'bg-light text-muted border' ?>">
                                                <?= $a['nb_commentaires'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $a['actif'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?>
                                        </td>
                                        <td onclick="event.stopPropagation()">
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-outline-warning btn-sm py-0 px-1"
                                                    data-bs-toggle="modal" data-bs-target="#modalEditAnnonce"
                                                    data-id="<?= $a['id'] ?>"
                                                    data-titre="<?= htmlspecialchars($a['titre'], ENT_QUOTES) ?>"
                                                    data-description="<?= htmlspecialchars($a['description'], ENT_QUOTES) ?>"
                                                    data-prix="<?= $a['prix'] ?? '' ?>"
                                                    data-contact="<?= htmlspecialchars($a['contact_email'], ENT_QUOTES) ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST" onsubmit="return confirm('Supprimer cette annonce ?')">
                                                    <input type="hidden" name="action" value="delete_annonce">
                                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-1">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── INSCRIPTIONS ── -->
        <div class="tab-pane fade <?= $activeTab === 'inscriptions' ? 'show active' : '' ?>" id="tab-inscriptions">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people me-1"></i>Inscriptions aux concours</span>
                    <span class="badge bg-secondary"><?= count($inscriptions) ?></span>
                </div>
                <div class="card-body p-0">
                    <?php if ($inscriptions): ?>
                        <?php
                            // Grouper par concours
                            $grouped = [];
                            foreach ($inscriptions as $i) {
                                $grouped[$i['concours_titre']][] = $i;
                            }
                        ?>
                        <?php foreach ($grouped as $titreConcours => $liste): ?>
                            <div class="px-3 pt-3 pb-1">
                                <h6 class="fw-semibold text-muted small text-uppercase mb-2">
                                    <i class="bi bi-trophy me-1"></i><?= htmlspecialchars($titreConcours) ?>
                                    <span class="badge bg-secondary ms-1"><?= count($liste) ?></span>
                                </h6>
                            </div>
                            <table class="table table-hover mb-0 align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Membres</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($liste as $idx => $insc): ?>
                                    <tr>
                                        <td class="text-muted"><?= $idx + 1 ?></td>
                                        <td class="fw-semibold"><?= htmlspecialchars($insc['nom']) ?></td>
                                        <td>
                                            <a href="mailto:<?= htmlspecialchars($insc['email']) ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($insc['email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($insc['type'] === 'groupe'): ?>
                                                <span class="badge bg-info text-dark">Groupe</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">Individuel</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted">
                                            <?php if ($insc['membres']): ?>
                                                <span title="<?= htmlspecialchars($insc['membres']) ?>">
                                                    <?= htmlspecialchars(mb_strimwidth($insc['membres'], 0, 60, '…')) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap text-muted"><?= date('d.m.Y H:i', strtotime($insc['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Aucune inscription pour l'instant.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── MODÉRATION ── -->
        <div class="tab-pane fade <?= $activeTab === 'moderation' ? 'show active' : '' ?>" id="tab-moderation">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-shield-check me-1"></i>Commentaires en attente de modération</span>
                    <span class="badge bg-<?= count($pendingComments) ? 'danger' : 'success' ?>">
                        <?= count($pendingComments) ?>
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if ($pendingComments): ?>
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light small">
                                <tr>
                                    <th>Annonce</th>
                                    <th>Auteur</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php foreach ($pendingComments as $c): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>/annonce.php?id=<?= $c['annonce_id'] ?>" target="_blank" class="text-decoration-none">
                                            <?= htmlspecialchars($c['annonce_titre']) ?>
                                            <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($c['auteur']) ?></td>
                                    <td class="text-muted" style="max-width:300px">
                                        <?= htmlspecialchars(mb_strimwidth($c['contenu'], 0, 100, '…')) ?>
                                    </td>
                                    <td class="text-nowrap"><?= date('d.m.Y H:i', strtotime($c['created_at'])) ?></td>
                                    <td class="text-center text-nowrap" onclick="event.stopPropagation()">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="approve_comment">
                                            <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                            <button class="btn btn-outline-success btn-sm" title="Approuver">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce commentaire ?')">
                                            <input type="hidden" name="action" value="delete_comment">
                                            <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                            <button class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="bi bi-check-circle text-success me-2 fs-4"></i><br>
                            Aucun commentaire en attente.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /tab-content -->
</div><!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ── Modal édition News ── -->
<div class="modal fade" id="modalEditNews" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_news">
                <input type="hidden" name="id" id="editNewsId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-1"></i>Modifier la news</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Titre *</label>
                        <input type="text" name="titre" id="editNewsTitre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Contenu *</label>
                        <textarea name="contenu" id="editNewsContenu" class="form-control" rows="8" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Auteur *</label>
                        <input type="text" name="auteur" id="editNewsAuteur" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal édition Concours ── -->
<div class="modal fade" id="modalEditConcours" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_concours">
                <input type="hidden" name="id" id="editConcoursId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-1"></i>Modifier le concours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Titre *</label>
                        <input type="text" name="titre" id="editConcoursTitre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description *</label>
                        <textarea name="description" id="editConcoursDescription" class="form-control" rows="6" required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label small fw-semibold">Prix (CHF)</label>
                            <input type="number" name="prix" id="editConcoursPrix" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Deadline</label>
                            <input type="date" name="deadline" id="editConcoursDeadline" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal édition Annonce ── -->
<div class="modal fade" id="modalEditAnnonce" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit_annonce">
                <input type="hidden" name="id" id="editAnnonceId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-1"></i>Modifier l'annonce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Titre *</label>
                        <input type="text" name="titre" id="editAnnonceTitre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description *</label>
                        <textarea name="description" id="editAnnonceDescription" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label small fw-semibold">Prix (CHF)</label>
                            <input type="number" name="prix" id="editAnnoncePrix" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col">
                            <label class="form-label small fw-semibold">Email contact *</label>
                            <input type="email" name="contact_email" id="editAnnonceContact" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Pré-remplissage des modals d'édition
document.querySelectorAll('[data-bs-target="#modalEditNews"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editNewsId').value      = btn.dataset.id;
        document.getElementById('editNewsTitre').value   = btn.dataset.titre;
        document.getElementById('editNewsContenu').value = btn.dataset.contenu;
        document.getElementById('editNewsAuteur').value  = btn.dataset.auteur;
    });
});
document.querySelectorAll('[data-bs-target="#modalEditConcours"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editConcoursId').value          = btn.dataset.id;
        document.getElementById('editConcoursTitre').value       = btn.dataset.titre;
        document.getElementById('editConcoursDescription').value = btn.dataset.description;
        document.getElementById('editConcoursPrix').value        = btn.dataset.prix;
        document.getElementById('editConcoursDeadline').value    = btn.dataset.deadline;
    });
});
document.querySelectorAll('[data-bs-target="#modalEditAnnonce"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('editAnnonceId').value          = btn.dataset.id;
        document.getElementById('editAnnonceTitre').value       = btn.dataset.titre;
        document.getElementById('editAnnonceDescription').value = btn.dataset.description;
        document.getElementById('editAnnoncePrix').value        = btn.dataset.prix;
        document.getElementById('editAnnonceContact').value     = btn.dataset.contact;
    });
});
</script>
</body>
</html>
