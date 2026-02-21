#
# 🏷️ Garagenflohmarkt Zirndorf
Webanwendung zur Anmeldung und Verwaltung von Angeboten für den Garagenflohmarkt in Zirndorf.
Entwickelt von Open Zirndorf – offen, lokal, gemeinschaftlich.

## 🛠️ Technologien

* PHP 
* MySQL / MariaDB via PDO
* Google reCAPTCHA v2 – Spam-Schutz im Anmeldeformular (hierfür braucht man ein Secret)

## 🚀 Installation
Voraussetzungen
* PHP 8.x
* MySQL / MariaDB
* Apache-Webserver mit mod_rewrite

# Setup

1. Repository klonen:

```
bash   git clone https://github.com/openzirndorf/garagenflohmark.git
```

2. .env-Datei aus der Vorlage anlegen:
```
bash   cp .env.example .env
```

3. .env mit eigenen Werten befüllen (siehe Konfiguration)
4. Datenbank anlegen und Schema importieren:
```
bash   mysql -u BENUTZER -p DATENBANKNAME < db/schema.sql
```
5. .htpasswd für den Admin-Bereich generieren (siehe Sicherheit)

# ⚙️ Konfiguration
Alle sensiblen Zugangsdaten werden über eine .env-Datei bereitgestellt, die nicht im Repository liegt.
.env.example (Vorlage – diese Datei ist im Repo):
```
# Datenbankverbindung
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USER=
DB_PASS=

# Google reCAPTCHA
RECAPTCHA_SECRET=
```

Die .env-Datei muss manuell auf dem Server angelegt und befüllt werden.
Den reCAPTCHA Secret Key bekommst du unter google.com/recaptcha.

## Datenbankschema
Die Datenstruktur ist:

```
CREATE TABLE `teilnehmer` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `beschreibung` text,
  `sichtbar` tinyint(1) DEFAULT '0',
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `bestaetigt_at` datetime DEFAULT NULL,
  `erstellt_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

ALTER TABLE `teilnehmer`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `teilnehmer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
```

Anpassbar via:
```
 mysql -u BENUTZER -p DATENBANKNAME < dateiname.sql
```

# 🔒 Sicherheit
## .gitignore
Folgende Dateien werden niemals ins Repository eingecheckt:
* .env
* admin/.htpasswd

## .htaccess (Webroot)
Schützt sensible Dateien vor direktem HTTP-Zugriff:
```
apache<FilesMatch "^\.env|\.htpasswd|db\.php">
    Order allow,deny
    Deny from all
</FilesMatch>
```
## Admin-Bereich: HTTP Basic Auth
Der Ordner admin/ ist durch HTTP Basic Authentication geschützt.
admin/.htaccess:
```
apacheAuthType Basic
AuthName "Adminbereich"
AuthUserFile /var/www/vhosts/path/to/application/httpdocs/admin/.htpasswd
Require valid-user
```
⚠️ Den Pfad in AuthUserFile bei einem Serverwechsel anpassen!

## .htpasswd generieren (einmalig auf dem Server ausführen):
```
# Neue Datei erstellen:
htpasswd -c /pfad/zum/admin/.htpasswd BENUTZERNAME

# Weiteren Benutzer hinzufügen (ohne -c, sonst wird die Datei überschrieben!):
htpasswd /pfad/zum/admin/.htpasswd WEITERER_BENUTZER
```
Die .htpasswd-Datei liegt direkt im admin/-Ordner auf dem Server und wird nicht ins Repo eingecheckt.