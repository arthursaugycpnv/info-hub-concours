<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . (isAdmin() ? '/admin/index.php' : '/index.php'));
    exit;
}

$error    = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email && $mdp) {
        $stmt = getDB()->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['nom'];
            $_SESSION['user_role'] = $user['role'];

            $dest = $user['role'] === 'admin'
                ? BASE_URL . '/admin/index.php'
                : ($redirect ?: BASE_URL . '/index.php');

            header('Location: ' . $dest);
            exit;
        }
    }
    $error = 'Email ou mot de passe incorrect.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — InfoHub CPNV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body style="background:#000;min-height:100vh;display:flex;align-items:center;justify-content:center">

<div style="width:100%;max-width:400px;padding:1rem">
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
        <div class="card-header" style="background:var(--nb-cyan) !important;font-size:.9rem">
            <i class="bi bi-box-arrow-in-right me-1"></i>Connexion membre
        </div>
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?= csrf_field() ?>
                <?php if ($redirect): ?>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="prenom.nom@eduvaud.ch"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-arrow-right me-1"></i>Se connecter
                </button>
            </form>

            <hr class="my-4">
            <div class="text-center nb-mono" style="font-size:.8rem;color:#555">
                Pas encore de compte ? <a href="<?= BASE_URL ?>/register.php" style="color:#000;font-weight:700">S'inscrire</a>
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
