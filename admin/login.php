<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: /admin/index.php');
    exit;
}

require_once __DIR__ . '/../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if ($email && $mdp) {
        $stmt = getDB()->prepare('SELECT * FROM utilisateurs WHERE email = ? AND role = "admin"');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            header('Location: /admin/index.php');
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
    <title>Admin — InfoHub CPNV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="card shadow" style="width:360px">
        <div class="card-body p-4">
            <h4 class="text-center fw-bold mb-1"><i class="bi bi-cpu me-2"></i>InfoHub</h4>
            <p class="text-center text-muted small mb-4">Administration</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-dark w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                </button>
            </form>
            <div class="text-center mt-3">
                <a href="/index.php" class="text-muted small">← Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
