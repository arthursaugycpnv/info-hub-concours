<?php
require_once __DIR__ . '/config.php';

// Déjà connecté
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . (isAdmin() ? '/admin/index.php' : '/index.php'));
    exit;
}

$error    = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="card shadow" style="width:380px">
        <div class="card-body p-4">
            <h4 class="text-center fw-bold mb-1">
                <i class="bi bi-cpu me-2 text-warning"></i>InfoHub CPNV
            </h4>
            <p class="text-center text-muted small mb-4">Connexion à votre compte</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <?php if ($redirect): ?>
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control"
                           placeholder="prenom.nom@eduvaud.ch"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                </button>
            </form>
            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>/index.php" class="text-muted small">← Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
