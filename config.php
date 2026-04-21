<?php

// Détecte automatiquement le sous-dossier (vhost .test ou localhost/info-hub-concours-private/)
$_root    = str_replace('\\', '/', realpath(__DIR__));
$_docroot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
define('BASE_URL', rtrim(str_replace($_docroot, '', $_root), '/'));
unset($_root, $_docroot);

// Session disponible partout (les pages admin l'ont déjà démarrée)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ── Rate limiting (session-based) ─────────────────────────────────
// Returns false when the caller has exceeded $max actions in $window seconds.
function rate_limit(string $key, int $max, int $window): bool {
    $now = time();
    $k   = 'rl_' . $key;
    $_SESSION[$k] = array_values(array_filter(
        $_SESSION[$k] ?? [],
        fn(int $t) => ($now - $t) < $window
    ));
    if (count($_SESSION[$k]) >= $max) return false;
    $_SESSION[$k][] = $now;
    return true;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            exit('Requête invalide (CSRF).');
        }
    }
}

function handleImageUpload(string $field): ?string {
    if (empty($_FILES[$field]['name'])) return null;

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo   = new finfo(FILEINFO_MIME_TYPE);
    $mime    = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) return null;

    if ($file['size'] > 5 * 1024 * 1024) return null; // 5 MB max

    $ext      = match($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    };
    $filename = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest     = __DIR__ . '/uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

    return '/uploads/' . $filename;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin';
}

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'infohub');
define('DB_USER', 'root');       // À adapter selon ton environnement
define('DB_PASS', '');            // À adapter selon ton environnement
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
