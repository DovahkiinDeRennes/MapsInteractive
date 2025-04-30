<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
// Paramètres de connexion
$host = 'localhost';
$dbname = 'myApp';  // ⚠️ à modifier
$username = 'root';          // par défaut sous Laragon
$password = '';              // mot de passe vide sous Laragon

try {
    // Connexion à la base avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Activer les erreurs PDO (très utile pour le débogage)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie.";
    // Optionnel : message si succès
    // echo "Connexion réussie à la base de données.";
} catch (PDOException $e) {
    // En cas d'erreur, afficher le message

    echo "❌ Erreur de connexion : " . $e->getMessage();
}
?>
