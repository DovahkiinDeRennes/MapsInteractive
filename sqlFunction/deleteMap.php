<?php
$message = '';

$host = 'localhost';
$dbname = 'myapp';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $message = "❌ Erreur de connexion : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    try {
        $pdo->beginTransaction();

        $stmtGet = $pdo->prepare("SELECT url FROM map WHERE id = ?");
        $stmtGet->execute([$id]);
        $map = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if ($map && isset($map['url'])) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $map['url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt1 = $pdo->prepare("DELETE FROM zonewithmap WHERE mapId = ?");
        $stmt1->execute([$id]);

        $stmt2 = $pdo->prepare("DELETE FROM map WHERE id = ?");
        $stmt2->execute([$id]);

        $pdo->commit();

        $message = "✅ Map et image supprimées avec succès.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "❌ Erreur lors de la suppression : " . $e->getMessage();
    }
}

$sql = "SELECT id, name, url FROM map";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$maps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
