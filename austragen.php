<?php
require_once "db.php";

$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Ungültige E-Mail-Adresse.");
    }
    $stmt = $pdo->prepare("SELECT id FROM teilnehmer WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $eintrag = $stmt->fetch();

    if ($eintrag) {
        $id = $eintrag['id'];
        $hash = md5($id . $email);
        $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/loeschen.php?id=$id&token=$hash";

        $from = "From: Garagenflohmarkt Zirndorf <noreply@openzirndorf.de>\r\n";
        mail($email, "Teilnahme austragen bestätigen", "Bitte klicke auf folgenden Link zur Bestätigung der Austragung: $link", $from);

        echo "Bitte bestätige die Austragung über den Link in der E-Mail.";
    } else {
        echo "Keine Anmeldung mit dieser E-Mail gefunden.";
    }
} else {
    echo '<form method="POST"><label>E-Mail:</label><input type="email" name="email" required><button type="submit">Austragen</button></form>';
}

?>