<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=myapp', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id, name, url FROM map";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $maps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($maps);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur BDD: ' . $e->getMessage()]);
}