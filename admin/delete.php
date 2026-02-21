<?php
require_once "../db.php";
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM teilnehmer WHERE id = ?");
$stmt->execute([$id]);
header("Location: index.php");
exit;