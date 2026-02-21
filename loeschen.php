<?php
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);
$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT email FROM teilnehmer WHERE id = ?");
$stmt->execute([$id]);
$eintrag = $stmt->fetch();

if ($eintrag && md5($id . $eintrag['email']) === $token) {
    $stmt = $pdo->prepare("DELETE FROM teilnehmer WHERE id = ?");
    $stmt->execute([$id]);
    echo "✅ Deine Teilnahme wurde erfolgreich gelöscht.";
} else {
    echo "Austragung fehlgeschlagen.";
}
