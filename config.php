<?php

// Détecte automatiquement le sous-dossier (vhost .test ou localhost/info-hub-concours-private/)
$_root    = str_replace('\\', '/', realpath(__DIR__));
$_docroot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
define('BASE_URL', rtrim(str_replace($_docroot, '', $_root), '/'));
unset($_root, $_docroot);

// Session disponible partout (les pages admin l'ont déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin';
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'infohub');
define('DB_USER', 'root');       // À adapter selon ton environnement
define('DB_PASS', '');           // À adapter selon ton environnement
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
