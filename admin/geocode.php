<?php
require_once "../db.php";
require_once "../functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $beschreibung = $_POST['beschreibung'] ?? '';

    $stmt = $pdo->prepare("UPDATE teilnehmer SET beschreibung = ? WHERE id = ?");
    $stmt->execute([$beschreibung, $id]);

    $stmt = $pdo->prepare("SELECT adresse FROM teilnehmer WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $geo = geocodeAdresse($row['adresse']);
        if ($geo) {
            updateGeolocation($pdo, $id, (float)$geo['lat'], (float)$geo['lng']);
            $info = "✅ Beschreibung aktualisiert und Geodaten neu gesetzt.";
        } else {
            $info = "❌ Adresse konnte nicht geokodiert werden.";
        }
    } else {
        $info = "❌ Adresse nicht gefunden.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Geodaten bearbeiten</title>
  <link rel="stylesheet" href="../assets/style.css">
  <style>
    table { width: 100%; max-width: 1000px; margin: 2rem auto; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 0.5rem; text-align: left; }
    input[type="text"] { width: 100%; padding: 0.3rem; }
    button { padding: 0.5rem 1rem; }
  </style>
</head>
<body>
  <header style="position: sticky; top: 0; background-color: #f0f4f8; padding: 1rem 0; z-index: 100; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <h1 style="margin: 0; text-align: center; font-size: 1.75rem;">Admin: Manuelle Geokodierung</h1>
  </header>
  <main style="max-width: 1000px; margin: 2rem auto;">
    <?php if (isset($info)) echo "<p style='text-align:center;color:green;'>$info</p>"; ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Adresse</th>
        <th>Beschreibung</th>
        <th>Koordinaten</th>
        <th>Aktion</th>
      </tr>
      <?php
      $stmt = $pdo->query("SELECT id, email, adresse, beschreibung, lat, lng FROM teilnehmer ORDER BY id DESC");
      foreach ($stmt as $row): ?>
        <tr>
          <form method="POST" action="geocode.php">
            <td><?= htmlspecialchars($row['id']) ?><input type="hidden" name="id" value="<?= $row['id'] ?>"></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['adresse']) ?></td>
            <td><input type="text" name="beschreibung" value="<?= htmlspecialchars($row['beschreibung']) ?>"></td>
            <td>lat: <?= $row['lat'] ?><br>lng: <?= $row['lng'] ?></td>
            <td><button type="submit">Speichern + Geodaten</button></td>
          </form>
        </tr>
      <?php endforeach; ?>
    </table>
  </main>
  <footer style="max-width:1000px; margin: 2rem auto; text-align:center;">
    <a href="../index.php">Zurück zur Startseite</a>
  </footer>
</body>
</html>
