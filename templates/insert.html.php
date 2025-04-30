<?php 
include '../sqlFunction/insert.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload d'image</title>
    <!-- Intégration de Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
<header class="bg-gray-200 text-black w-full py-6 shadow-md">
    <nav class="max-w-7xl mx-auto px-4">
      <ul class="flex space-x-8 justify-center items-center text-lg font-medium">
        <li><a href="../index.html" class="hover:text-indigo-800 transition">Accueil</a></li>
        <li><a href="insert.html.php" class="hover:text-indigo-800 transition">Ajouter une map</a></li>
        <li><a href="deleteMap.html.php" class="hover:text-indigo-800 transition">Supprimer une map</a></li>
        <li><a href="deleteZone.html.php" class="hover:text-indigo-800 transition">Supprimer une zone</a></li>
        <li><a href="infos.html" class="hover:text-indigo-800 transition">Infos</a></li>
      </ul>
    </nav>
  </header>
    <div class="container mx-auto p-6">
        <!-- Titre de la page -->
        <h2 class="text-3xl font-bold text-center text-indigo-600 mb-8">Ajouter une map avec image</h2>
        <!-- Formulaire d'upload d'image -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-lg space-y-6">
            <div>
                <label for="name" class="block text-lg font-semibold text-gray-700">Nom :</label>
                <input type="text" name="name" id="name" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <label for="image" class="block text-lg font-semibold text-gray-700">Image (jpg/png) :</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                    Envoyer
                </button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <a href="index.html" class="text-indigo-600 hover:text-indigo-800">Retour</a>
        </div>
        <?php if (!empty($message)): ?>
    <?php
        $isSuccess = str_starts_with($message, '✅');
        $alertClasses = $isSuccess
            ? 'bg-green-100 border border-green-400 text-green-700'
            : 'bg-red-100 border border-red-400 text-red-700';
    ?>
    <div class="mt-5 <?= $alertClasses ?> px-4 py-3 rounded relative mb-4">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>
    </div>
</body>
</html>
