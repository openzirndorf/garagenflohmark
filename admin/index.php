<?php
session_start();
if (!isset($_SESSION['admin'])) {
    if ($_POST['pw'] ?? '' === 'adminpasswort') {
        $_SESSION['admin'] = true;
    } else {
        die('<form method="POST"><input type="password" name="pw"><button>Login</button></form>');
    }
}

require_once "../db.php";
$stmt = $pdo->query("SELECT * FROM teilnehmer ORDER BY erstellt_at DESC");
echo "<h1>Adminbereich</h1><table border='1'><tr><th>ID</th><th>E-Mail</th><th>Adresse</th><th>Aktion</th></tr>";
foreach ($stmt as $row) {
    echo "<tr><td>{$row['id']}</td><td>{$row['email']}</td><td>{$row['adresse']}</td><td><a href='delete.php?id={$row['id']}'>Löschen</a></td></tr>";
}
echo "</table>";