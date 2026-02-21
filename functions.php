<?php
function getVisibleAddresses(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT adresse, lat, lng, beschreibung FROM teilnehmer WHERE sichtbar = 1 AND bestaetigt_at IS NOT NULL");
    $stmt->execute();
    return $stmt->fetchAll();
}

function saveEntry(PDO $pdo, string $email, string $adresse, bool $sichtbar, string $beschreibung = ''): int {
    $stmt = $pdo->prepare("INSERT INTO teilnehmer (email, adresse, sichtbar, beschreibung) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $adresse, $sichtbar ? 1 : 0, $beschreibung]);
    return $pdo->lastInsertId();
}

function markAsConfirmed(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("UPDATE teilnehmer SET bestaetigt_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
}

function geocodeAdresse(string $adresse): ?array {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($adresse);
    $options = [
        "http" => [
            "header" => "User-Agent: Garagenflohmarkt/1.0
"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);
    if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
        return ['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']];
    }
    return null;
}
  

function updateGeolocation(PDO $pdo, int $id, float $lat, float $lng): void {
    $stmt = $pdo->prepare("UPDATE teilnehmer SET lat = ?, lng = ? WHERE id = ?");
    $stmt->execute([$lat, $lng, $id]);
}

?>