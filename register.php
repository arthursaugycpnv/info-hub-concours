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

    if (!rate_limit('register', 3, 3600))
        $errors[] = 'Trop de tentatives de création de compte. Réessayez dans une heure.';
    if (!$nom)
        $errors[] = 'Le nom est obligatoire.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email invalide.';
    elseif (strtolower(substr(strrchr($email, '@'), 1)) !== 'eduvaud.ch')
        $errors[] = 'Seules les adresses @eduvaud.ch sont acceptées.';
    if (strlen($mdp) < 8)
        $errors[] = 'Le mot de passe doit faire au moins 8 caractères.';
    if ($mdp !== $confirm)
        $errors[] = 'Les mots de passe ne correspondent pas.';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="card shadow" style="width:420px">
        <div class="card-body p-4">
            <h4 class="text-center fw-bold mb-1">
                <i class="bi bi-cpu me-2 text-warning"></i>InfoHub CPNV
            </h4>
            <p class="text-center text-muted small mb-4">Créer un compte membre</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nom complet *</label>
                    <input type="text" name="nom" class="form-control"
                           placeholder="Prénom Nom"
                           value="<?= htmlspecialchars($values['nom']) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="prenom.nom@eduvaud.ch"
                           value="<?= htmlspecialchars($values['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Rôle *</label>
                    <select name="role" class="form-select">
                        <option value="etudiant" <?= $values['role'] === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                        <option value="enseignant" <?= $values['role'] === 'enseignant' ? 'selected' : '' ?>>Enseignant</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Mot de passe * <span class="fw-normal text-muted">(min. 8 caractères)</span></label>
                    <input type="password" name="mot_de_passe" class="form-control" required minlength="8" autocomplete="new-password">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Confirmer le mot de passe *</label>
                    <input type="password" name="confirmation" class="form-control" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-semibold">
                    <i class="bi bi-person-plus me-1"></i>Créer mon compte
                </button>
            </form>

            <hr class="my-3">
            <div class="text-center small">
                Déjà un compte ? <a href="<?= BASE_URL ?>/login.php">Se connecter</a>
            </div>
            <div class="text-center mt-2">
                <a href="<?= BASE_URL ?>/index.php" class="text-muted small">← Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
