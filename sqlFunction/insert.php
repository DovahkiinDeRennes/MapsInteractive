<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion BDD
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

// Variable pour les messages
$message = '';

// Récupérer le nom
$name = $_POST['name'] ?? null;

// Gestion de l'image
if ($name && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    $webPathPrefix = '/myApp/uploads/'; // <- Ce sera utilisé pour les URLs

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES['image']['tmp_name'];
    $originalName = basename($_FILES['image']['name']);
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Générer un nom de fichier unique
    $fileName = uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;       // Chemin serveur (pour move_uploaded_file)
    $urlForDB = $webPathPrefix . $fileName;   // Chemin web (à enregistrer dans la BDD)

    // Déplacer le fichier
    if (move_uploaded_file($tmpName, $filePath)) {
        // Insérer le chemin dans la BDD
        $stmt = $pdo->prepare("INSERT INTO map (name, url) VALUES (:name, :url)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':url', $urlForDB);

        if ($stmt->execute()) {
            // Message de succès
            $message = "✅ Image et map ajoutées avec succès.";
        } else {
            $message = "❌ Insertion échouée.";
        }
    } else {
        $message = "❌ Erreur lors du déplacement du fichier.";
    }
}
?>
