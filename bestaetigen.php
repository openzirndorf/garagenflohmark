<?php
require_once "db.php";
require_once "functions.php";

$id = (int)($_GET['id'] ?? 0);
$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT email, adresse FROM teilnehmer WHERE id = ?");
$stmt->execute([$id]);
$eintrag = $stmt->fetch();

if ($eintrag && md5($id . $eintrag['email']) === $token) {
    markAsConfirmed($pdo, $id);
    $geo = geocodeAdresse($eintrag['adresse']);
    if ($geo) {
        updateGeolocation($pdo, $id, (float)$geo['lat'], (float)$geo['lng']);
    }
    header("Location: index.php?bestaetigt=1");
    exit;
} else {
    echo "Bestätigung fehlgeschlagen.";
}
?>