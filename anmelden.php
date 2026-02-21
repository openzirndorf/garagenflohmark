<?php
// === RECAPTCHA v3 VALIDIERUNG ===
$env = parse_ini_file(__DIR__ . '/.env');
$secret = $env['RECAPTCHA_SECRET'];
$token = $_POST['recaptcha_token'] ?? '';
$remoteIp = $_SERVER['REMOTE_ADDR'];

$response = file_get_contents(
  "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$token&remoteip=$remoteIp"
);
$result = json_decode($response, true);

// ReCAPTCHA-Score prüfen
if (!$result['success'] || $result['score'] < 0.5) {
    die("🚫 Spamverdacht – Anmeldung wurde blockiert.");
}

// === HONEYPOT ===
if (!empty($_POST['website'])) {
    die("🚫 Spamverdacht – Bot erkannt.");
}

// === WEGWERFDOMAINEN PRÜFEN ===
$disposable_domains = [
    '10minutemail.com', 'mailinator.com', 'guerrillamail.com', 'tempmail.com',
    'trashmail.com', 'fakeinbox.com', 'yopmail.com', 'maildrop.cc'
];
$email = $_POST['email'] ?? '';
$email_domain = strtolower(substr(strrchr($email, "@"), 1));
if (in_array($email_domain, $disposable_domains)) {
    die("🚫 Bitte verwende eine echte E-Mail-Adresse.");
}

// === SPAM-KEYWORDS PRÜFEN ===
$beschreibung = strtolower($_POST['beschreibung'] ?? '');
$blacklist = ['viagra', 'casino', 'bit.ly', 'href=', '<script', '[url', 'bitcoin', 'nude', 'porn', 'sex', 'escort'];
foreach ($blacklist as $badword) {
    if (strpos($beschreibung, $badword) !== false) {
        die("🚫 Deine Beschreibung enthält unzulässige Inhalte.");
    }
}

// === DATENVERARBEITUNG ===
require_once "db.php";
require_once "functions.php";

$adresse = $_POST['adresse'] ?? '';
$sichtbar = isset($_POST['sichtbar']);

// Validierung
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($adresse)) {
    die("🚫 Ungültige Eingabe.");
}

$id = saveEntry($pdo, $email, $adresse, $sichtbar, $beschreibung);
$hash = md5($id . $email);
$link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/bestaetigen.php?id=$id&token=$hash";

// E-Mail versenden
$from = "From: Garagenflohmarkt Zirndorf <root@openzirndorf.de>\r\n";
mail($email, "Garagenflohmarkt bestätigen", "Bitte klicke auf folgenden Link zur Bestätigung: $link", $from);

// Hinweis ausgeben
echo "✅ Bitte bestätige deine Teilnahme über den Link in der E-Mail.";
?>




