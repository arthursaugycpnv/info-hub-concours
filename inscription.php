<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/concours.php');
    exit;
}

csrf_verify();

$nom          = trim($_POST['nom'] ?? '');
$email        = trim($_POST['email'] ?? '');
$type         = $_POST['type'] === 'groupe' ? 'groupe' : 'individuel';
$membres      = trim($_POST['membres'] ?? '');
$concours_id  = (int)($_POST['concours_id'] ?? 0);

if (!$nom || !$email || !$concours_id) {
    header('Location: ' . BASE_URL . '/concours.php');
    exit;
}

$db = getDB();
$stmt = $db->prepare('SELECT id FROM concours WHERE id = ? AND actif = 1');
$stmt->execute([$concours_id]);
if (!$stmt->fetch()) {
    header('Location: ' . BASE_URL . '/concours.php');
    exit;
}

$db->prepare('INSERT INTO inscriptions (concours_id, nom, email, type, membres) VALUES (?, ?, ?, ?, ?)')
   ->execute([$concours_id, $nom, $email, $type, $membres]);

header('Location: ' . BASE_URL . '/concours.php?inscrit=1');
exit;
