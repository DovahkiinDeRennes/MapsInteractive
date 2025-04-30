<?php 
include '../sqlFunction/deleteZone.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Zone</title>
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
    <h1 class="text-3xl font-bold text-center text-indigo-600 mb-8">Gestion des Zones</h1>
    <div class="space-y-4">
        <?php foreach ($zones as $zone): ?>
            <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition">
                <p class="text-lg font-semibold text-gray-700">
                    <?= htmlspecialchars($zone['id'] . " " . $zone['zone'] . " (Map: " . $zone['map_name'] . ")") ?>
                </p>
                <form method="POST" action="" onsubmit="return confirm('Supprimer cette zone de la map <?= htmlspecialchars($zone['map_name']) ?> ?');">
                    <input type="hidden" name="zoneId" value="<?= $zone['id'] ?>">
                    <input type="hidden" name="mapId" value="<?= $zone['mapId'] ?>">
                    <button type="submit" class="mt-4 bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition">
                        Supprimer
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-8 text-center">
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
