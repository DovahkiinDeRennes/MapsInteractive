<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'myapp';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zoneId'], $_POST['mapId'])) {
    $zoneId = (int) $_POST['zoneId'];
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM zonewithmap WHERE id = ?");
        $stmt->execute([$zoneId]);
        $pdo->commit();

        $message = "✅ Zone supprimée avec succès.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}
$sql = "SELECT zm.id, zm.zone, zm.mapId, m.name AS map_name FROM zonewithmap zm LEFT JOIN map m ON zm.mapId = m.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);


