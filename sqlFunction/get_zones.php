<?php
$pdo = new PDO('mysql:host=localhost;dbname=myapp;charset=utf8mb4', 'root', '');

// Tu peux filtrer par ID de carte si tu veux
$mapId = isset($_GET['mapId']) ? intval($_GET['mapId']) : null;

$sql = "SELECT * FROM zonewithmap";
$params = [];

if ($mapId !== null) {
    $sql .= " WHERE mapId = :mapId";
    $params[':mapId'] = $mapId;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($zones);
?>
