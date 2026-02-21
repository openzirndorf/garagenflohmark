<?php
require_once "db.php";
require_once "functions.php";
if (isset($_GET['bestaetigt'])) {
    echo "<p style='color: green;'>✅ Deine Teilnahme wurde erfolgreich bestätigt.</p>";
}
$visible_addresses = getVisibleAddresses($pdo);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Garagenflohmarkt Zirndorf</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/leaflet/leaflet.css">
  <script src="assets/leaflet/leaflet.js"></script>
</head>
<body>
  <header class="header">
    <h1>Garagenflohmarkt Zirndorf</h1>
    <p class="event-date">📅 Sonntag, 14. September 2025 – 10:00 bis 16:00 Uhr</p>
  </header>


  <?php if (isset($_GET['bestaetigt'])): ?>
    <p class="success-message">✅ Deine Teilnahme wurde erfolgreich bestätigt.</p>
  <?php endif; ?>

  <div class="teilnahme-box">
    <h2>📝 Teilnehmen</h2>
    <form action="anmelden.php" method="POST">
      <label for="email">E-Mail-Adresse</label>
      <input type="email" name="email" required>

      <label for="beschreibung">Beschreibung (optional)</label>
      <textarea name="beschreibung" rows="3"></textarea>

      <label for="adresse">Verkaufsadresse</label>
      <input type="text" name="adresse" required>

      <label class="checkbox">
        <input type="checkbox" name="sichtbar" value="1">
        Ich will meine Adresse auf der Karte sichtbar machen
      </label>

      <label class="checkbox">
        <input type="checkbox" name="teilnahmebedingungen" required>
        Ich bestätige, dass ich die Teilnahmebedingungen erfülle.
      </label>
	  <div style="display:none;">
  <label for="website">Lass dieses Feld leer</label>
  <input type="text" name="website" id="website" autocomplete="off">
</div>
      <button type="submit">Teilnehmen</button>
    </form>
  </div>

  <div class="teilnahme-box">
    <h2>📌 Teilnahmebedingungen</h2>
    <ul>
      <li>Nur <strong>Privatverkäufe</strong> erlaubt – keine gewerblichen Anbieter.</li>
      <li>Die Teilnahme ist <strong>nur auf eigenem Grundstück</strong> im Gebiet der Stadt Zirndorf gestattet.</li>
      <li>Der Betreiber dieser Seite stellt lediglich eine Plattform zur Sichtbarmachung bereit.</li>
      <li><strong>Termin:</strong> 10:00–16:00 Uhr</li>
      <li><strong>Warenangebot:</strong> Nur Trödel, keine Neuwaren, keine gewerblichen Artikel, keine Lebensmittel, Kosmetik, Fahrzeuge, Waffen oder politische Propaganda.</li>
      <li><strong>Standaufbau:</strong> Nur auf Privatgrundstück, <u>nicht</u> auf Gehwegen, Straßen oder öffentlichen Flächen. Markiere deinen Stand mit <strong>mind. 3 bunten Luftballons</strong>.</li>
      <li><strong>Haftung:</strong> Jeder Teilnehmende ist selbst für Versicherung und Sicherheit verantwortlich. Der Seitenbetreiber übernimmt keinerlei Haftung.</li>
    </ul>
  </div>

  <div class="teilnahme-box">
    <h2>📤 Austragen</h2>
    <div class="austragen-wrapper">
      <a href="austragen.php" class="austragen-button">Austragen</a>
    </div>
  </div>

  <div class="teilnahme-box">
    <h2>🗺️ Übersichtskarte</h2>
    <div id="map"></div>
  </div>

  <script>
    const map = L.map('map').setView([49.4424, 10.9547], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = <?php echo json_encode($visible_addresses); ?>;
    markers.forEach(entry => {
      if (entry.lat && entry.lng) {
        L.marker([entry.lat, entry.lng], {
          icon: L.icon({
            iconUrl: 'assets/leaflet/images/marker-icon.png',
            shadowUrl: 'assets/leaflet/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
          })
        }).addTo(map)
          .bindPopup(`<b>${entry.adresse}</b><br>${entry.beschreibung ?? ''}`);
      }
    });
  </script>
	
  <div class="address-table">
  <h2>🗂️ Übersicht aller Teilnehmer</h2>
  <table>
    <tr>
      <th>Adresse</th>
      <th>Beschreibung</th>
    </tr>
    <?php foreach ($visible_addresses as $entry): ?>
      <tr>
        <td><?= htmlspecialchars($entry['adresse']) ?></td>
        <td><?= htmlspecialchars($entry['beschreibung']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

  <footer>
    <p><a href="impressum.html">Impressum</a> | <a href="datenschutz.html">Datenschutz</a></p>
    <p>Hinweis: Es werden nur E-Mail-Adresse, Verkaufsadresse, optional eine Beschreibung sowie ein Zeitstempel zur Teilnahme gespeichert. Die E-Mail-Adresse wird ausschließlich zur Bestätigung verwendet.<br>
Zur Spamvermeidung setzen wir Google reCAPTCHA v3 ein. Dabei können personenbezogene Daten (z. B. IP-Adresse, Mausbewegungen) an Google übertragen werden.<br>
Weitere Informationen findest du in der <a href=https://policies.google.com/privacy>Datenschutzerklärung von Google</a> und in <a href="datenschutz.html">unserer Datenschutzerklärung.</a></p>
  </footer>
<script src="https://www.google.com/recaptcha/api.js?render=6Lev6owrAAAAAMWciAs7P9nD-bqT54p4CwvX5eOm"></script>
<script>
  grecaptcha.ready(function () {
    grecaptcha.execute('6Lev6owrAAAAAMWciAs7P9nD-bqT54p4CwvX5eOm', { action: 'anmelden' }).then(function (token) {
      const recaptchaResponse = document.createElement('input');
      recaptchaResponse.setAttribute('type', 'hidden');
      recaptchaResponse.setAttribute('name', 'recaptcha_token');
      recaptchaResponse.setAttribute('value', token);
      document.querySelector('form').appendChild(recaptchaResponse);
    });
  });
</script>
</body>
</html>
