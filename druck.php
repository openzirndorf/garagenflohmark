<?php
require_once "db.php";
$stmt = $pdo->query("SELECT adresse, beschreibung FROM teilnehmer WHERE sichtbar = 1 AND bestaetigt_at IS NOT NULL ORDER BY adresse ASC");
echo "<h1>Druckansicht</h1><ul>";
foreach ($stmt as $row) {
    echo "<li><strong>{$row['adresse']}</strong><br>{$row['beschreibung']}</li>";
}
echo "</ul><script>window.print();</script>";
?>