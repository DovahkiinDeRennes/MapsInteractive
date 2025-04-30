// Exemple de check-drawing.php
<?php
// Récupérer les données envoyées
$dataRaw = file_get_contents("php://input");
$data = json_decode($dataRaw, true);

// Vérifier si un dessin similaire existe déjà dans la base
$zone = $data['zone'];
$mapId = $data['mapId'];
$drawJs = $data['draw_js'];

var_dump($data, $zone, $mapId, $drawJs);
die;

// Implémentation de la logique pour vérifier la base de données, par exemple :
$query = "SELECT COUNT(*) FROM drawings WHERE map_id = :mapId AND zone = :zone";
$stmt = $pdo->prepare($query);
$stmt->execute(['mapId' => $mapId, 'zone' => $zone]);

$result = $stmt->fetchColumn();
if ($result > 0) {
    // Si le dessin existe déjà
    echo json_encode(['exists' => true]);
} else {
    // Si le dessin n'existe pas
    echo json_encode(['exists' => false]);
}
?>
