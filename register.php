<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors  = [];
$values  = ['nom' => '', 'email' => '', 'role' => 'etudiant'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nom     = trim($_POST['nom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mdp     = $_POST['mot_de_passe'] ?? '';
    $confirm = $_POST['confirmation'] ?? '';
    $role    = in_array($_POST['role'] ?? '', ['etudiant', 'enseignant']) ? $_POST['role'] : 'etudiant';

    $values = compact('nom', 'email', 'role');

    if (!$nom)                                               $errors[] = 'Le nom est obligatoire.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                             $errors[] = 'Email invalide.';
    if (strlen($mdp) < 8)                                   $errors[] = 'Le mot de passe doit faire au moins 8 caractères.';
    if ($mdp !== $confirm)                                   $errors[] = 'Les mots de passe ne correspondent pas.';

    if (!$errors) {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($mdp, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)')
               ->execute([$nom, $email, $hash, $role]);

            $user = $db->prepare('SELECT * FROM utilisateurs WHERE email = ?');
            $user->execute([$email]);
            $user = $user->fetch();

            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: ' . BASE_URL . '/index.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — InfoHub CPNV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body style="background:#000;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 0">

<div style="width:100%;max-width:440px;padding:1rem">
    <!-- Logo -->
    <div style="text-align:center;margin-bottom:2rem">
        <a href="<?= BASE_URL ?>/index.php" style="display:inline-flex;align-items:center;gap:.6rem;background:var(--nb-yellow);border:var(--nb-border);padding:.5rem 1.25rem;box-shadow:var(--nb-shadow);text-decoration:none">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="" height="36" style="border:2px solid #000">
            <div>
                <div style="font-weight:900;font-size:1.1rem;text-transform:uppercase;color:#000;line-height:1">ASSO<span style="color:var(--nb-pink)">.INFO</span></div>
                <div style="font-family:'Courier New',monospace;font-size:.6rem;color:#333">Section Informatique CPNV</div>
            </div>
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="background:var(--nb-yellow) !important;font-size:.9rem">
            <i class="bi bi-person-plus me-1"></i>Créer un compte membre
        </div>
        <div class="card-body p-4">
            <?php if ($errors): ?>
                <div class="alert alert-danger py-2 mb-3">
                    <ul class="mb-0 ps-3" style="font-size:.85rem">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Nom complet *</label>
                    <input type="text" name="nom" class="form-control"
                           placeholder="Prénom Nom"
                           value="<?= htmlspecialchars($values['nom']) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="prenom.nom@eduvaud.ch"
                           value="<?= htmlspecialchars($values['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rôle *</label>
                    <select name="role" class="form-select">
                        <option value="etudiant" <?= $values['role'] === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                        <option value="enseignant" <?= $values['role'] === 'enseignant' ? 'selected' : '' ?>>Enseignant</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe * <span style="font-weight:400;text-transform:none;font-size:.75rem;color:#777">(min. 8 caractères)</span></label>
                    <input type="password" name="mot_de_passe" class="form-control" required minlength="8">
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirmer le mot de passe *</label>
                    <input type="password" name="confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-person-check me-1"></i>Créer mon compte
                </button>
            </form>

            <hr class="my-4">
            <div class="text-center nb-mono" style="font-size:.8rem;color:#555">
                Déjà un compte ? <a href="<?= BASE_URL ?>/login.php" style="color:#000;font-weight:700">Se connecter</a>
            </div>
            <div class="text-center mt-2">
                <a href="<?= BASE_URL ?>/index.php" class="nb-mono" style="font-size:.75rem;color:#888">← Retour au site</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
