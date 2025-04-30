// let currentMapId = null;
// let isSaving = false;

// // Groupe pour les nouveaux dessins
// const newDrawnItems = new L.FeatureGroup();    

// map.addLayer(newDrawnItems);

// // Récupération des cartes
// fetch('sqlFunction/get_maps.php')
//   .then(response => response.json())
//   .then(data => {
//     let mapNameToId = {};


//     data.forEach(mapData => {
//       mapNameToId[mapData.name] = mapData.id;
//     });

    


//     map.on('baselayerchange', function (e) {
//       currentMapId = mapNameToId[e.name];
//       window.dispatchEvent(new CustomEvent('mapIdChanged', { detail: currentMapId }));
//     });

//   })
//   .catch(error => console.error("❌ Erreur lors du chargement des maps :", error));

// // Écouteur d'événements pour la sélection de la carte
// window.addEventListener('mapIdChanged', function (event) {
//   currentMapId = event.detail;
//   console.log("ID de la carte dans draw.js :", currentMapId);
// });

// // Contrôle de dessin
// const drawControl = new L.Control.Draw({
//   edit: {
//     featureGroup: newDrawnItems  // Seuls les nouveaux dessins peuvent être modifiés
//   },
//   draw: {
//     polygon: true,
//     polyline: true,
//     rectangle: true,
//     circle: true,
//     marker: true
//   }
// });
// map.addControl(drawControl);

// // Fonction pour personnaliser et ajouter un dessin
// function customizeAndAddLayer(layer) {
//   const name = prompt("Nom de votre dessin ?", "Dessin sans nom");
//   const color = prompt("Choisissez une couleur (ex: 'red', 'blue') :", "red");

//   // Applique la couleur et ajoute un popup
//   if (layer instanceof L.Polygon || layer instanceof L.Polyline || layer instanceof L.Rectangle || layer instanceof L.Circle) {
//     layer.setStyle({ color: color });
//   }

//   layer.bindPopup(`<b>${name}</b><br>Couleur: ${color}`);
  
//   // Ajoute le dessin dans le groupe des nouveaux dessins
//   newDrawnItems.addLayer(layer);
// }

// // Fonction pour sauvegarder les dessins
// function saveDrawings(mapId) {
//   if (!mapId || isSaving) return;

//   isSaving = true;
//   const drawings = [];

//   newDrawnItems.eachLayer(function (layer) {
//     if (layer.toGeoJSON) {
//       const geoJSON = layer.toGeoJSON();

//       if (geoJSON.geometry?.coordinates && geoJSON.geometry.type === "Polygon") {
//         geoJSON.geometry.coordinates[0] = geoJSON.geometry.coordinates[0].map(
//           ([lng, lat]) => [Math.round(lng), Math.round(lat)]
//         );

//         const popupContent = layer.getPopup()?.getContent() || "Sans nom";
//         const match = popupContent.match(/<b>(.*?)<\/b><br>Couleur: (.*)/);
//         const name = match ? match[1] : "Sans nom";
//         const color = match ? match[2] : (layer.options?.color || "red");

//         geoJSON.properties = { name, color };

//         drawings.push({
//           zone: name,
//           color: color,
//           mapId: mapId,
//           draw_js: JSON.stringify(geoJSON)
//         });
//       }
//     }
//   });

//   // Sauvegarde dans la base de données
//   fetch('sqlFunction/save_zone.php', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/json' },
//     body: JSON.stringify(drawings)
//   })
//   .then(res => res.json())
//   .then(res => {
//     if (res.success) {
//       console.log("✅ Zones enregistrées !");
//       // Transfert des nouveaux dessins dans le groupe des dessins principaux
//       newDrawnItems.eachLayer(layer => {
//         drawnItems.addLayer(layer);
//       });
//       newDrawnItems.clearLayers();  // Vider le groupe des nouveaux dessins
//     } else {
//       console.error("❌ Erreur :", res.error);
//     }
//   })
//   .catch(err => console.error("❌ Erreur réseau :", err))
//   .finally(() => {
//     isSaving = false;
//   });
// }

// // Gestion des événements de dessin
// map.on('draw:created', function (e) {
//   const layer = e.layer;
//   customizeAndAddLayer(layer);  // Ajouter le dessin à la carte
//   saveDrawings(currentMapId);  // Sauvegarder les dessins après leur création
// });

// // Sauvegarder lors de la modification ou suppression des éléments dessinés
// map.on('draw:edited', function () {
//   setTimeout(() => saveDrawings(currentMapId), 1000);
// });

// // Sauvegarder lors de la suppression d'un dessin
// map.on('draw:deleted', function () {
//   setTimeout(() => saveDrawings(currentMapId), 1000);
// });
