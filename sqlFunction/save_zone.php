<?php
$pdo = new PDO('mysql:host=localhost;dbname=myapp;charset=utf8mb4', 'root', '');
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'Format invalide']);
  exit;
}

// Regrouper les zones par mapId
$zonesByMap = [];
foreach ($data as $item) {
  if (!isset($item['mapId']) || !is_numeric($item['mapId']) || !isset($item['zone'])) {
    continue;
  }
  $zonesByMap[$item['mapId']][] = $item['zone'];
}

$alreadyExists = [];

// Vérifier les zones déjà présentes dans la BDD
foreach ($zonesByMap as $mapId => $zoneNames) {
  // Supprimer les doublons pour la requête
  $placeholders = rtrim(str_repeat('?,', count($zoneNames)), ',');
  $query = "SELECT Zone FROM zonewithmap WHERE mapId = ? AND Zone IN ($placeholders)";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array_merge([$mapId], $zoneNames));

  $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
  $alreadyExists[$mapId] = array_map('strtolower', $results); // pour faire une comparaison insensible à la casse
}

// Préparer l’insertion
$stmtInsert = $pdo->prepare("INSERT INTO zonewithmap (Zone, mapId, draw_js) VALUES (:zone, :mapId, :draw_js)");

foreach ($data as $item) {
  $zone = strtolower($item['zone']);
  $mapId = $item['mapId'];

  if (!isset($alreadyExists[$mapId]) || !in_array($zone, $alreadyExists[$mapId])) {
    $stmtInsert->execute([
      ':zone' => $item['zone'],
      ':mapId' => $mapId,
      ':draw_js' => $item['draw_js']
    ]);
  }
}

echo json_encode(['success' => true]);
?>
