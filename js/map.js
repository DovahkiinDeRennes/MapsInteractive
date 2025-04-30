// === PARAMÈTRES GÉNÉRAUX ===
const imageWidth = 4000;
const imageHeight = 4000;
const bounds = [[0, 0], [imageHeight, imageWidth]];

const defaultImageUrl = 'images/fond.webp'; // Par exemple une image blanche ou avec "Carte par défaut"
const defaultOverlay = L.imageOverlay(defaultImageUrl, bounds);

// === INITIALISATION DE LA CARTE ===
const map = L.map('map', {
  crs: L.CRS.Simple,
  minZoom: -3,
  maxZoom: 1,
  maxBounds: bounds,
  maxBoundsViscosity: 1.0
});

// === COUCHES & GROUPES ===
const markerGroup = L.layerGroup().addTo(map);
const drawnItems = new L.FeatureGroup().addTo(map);
const newDrawnItems = new L.FeatureGroup().addTo(map);


const baseMaps = {
  'Carte par défaut': defaultOverlay
};
const overlays = {
  "Marqueurs": markerGroup,
  "Zones dessinées": drawnItems,
};

const mapNameToId = {};
let currentMapId = null;
let isSaving = false;

defaultOverlay.addTo(map);      // Affiche la carte par défaut au démarrage
map.fitBounds(bounds); 
// === CHARGEMENT DES CARTES ===
let layersControl; // ✅ Déclaré en dehors pour garder une référence

fetch('sqlFunction/get_maps.php')
  .then(res => res.json())
  .then(data => {
    console.log("✅ Cartes récupérées :", data);

    data.forEach((mapData, index) => {
      let imageUrl = mapData.url;
      if (imageUrl.startsWith("uploads/")) {
        imageUrl = "http://localhost:81/myApp/" + imageUrl;
      }

      mapNameToId[mapData.name] = mapData.id;

      const img = new Image();
      img.src = imageUrl;

      img.onload = () => {
        const overlay = L.imageOverlay(imageUrl, bounds);
        baseMaps[mapData.name] = overlay;

        // ✅ Crée le layersControl une seule fois
        if (!layersControl) {
          layersControl = L.control.layers(baseMaps, overlays).addTo(map);
        } else {
          layersControl.addBaseLayer(overlay, mapData.name);
        }
      };
    });

    // ✅ Réagir au changement de fond de carte
    map.on('baselayerchange', (e) => {
      const mapId = mapNameToId[e.name];
      console.log('🗺️ Carte sélectionnée :', e.name, 'ID :', mapId);
    
      if (mapId) {
        loadZones(mapId);
      } else {
        // Aucune carte personnalisée, donc efface les zones existantes
        drawnItems.clearLayers();
      }
    });
  })
  .catch(err => console.error("❌ Erreur chargement des cartes :", err));


// === MARQUEURS ===
const savedMarkers = JSON.parse(localStorage.getItem('markers') || '[]');
savedMarkers.forEach(m => addMarker(m.x, m.y, m.title));

function addMarker(x, y, title = "Marqueur personnalisé") {
  const marker = L.marker([y, x]).bindPopup(`<b>${title}</b><br>x: ${x}<br>y: ${y}`);
  markerGroup.addLayer(marker);
}

function saveMarkers() {
  const markers = [];
  markerGroup.eachLayer(layer => {
    const { lat, lng } = layer.getLatLng();
    const title = layer.getPopup()?.getContent().match(/<b>(.*?)<\/b>/)?.[1] || "Marqueur";
    markers.push({ x: lng, y: lat, title });
  });
  localStorage.setItem('markers', JSON.stringify(markers));
}

map.on('mousedown', (e) => {
  if (e.originalEvent.button === 1) {
    const x = Math.round(e.latlng.lng);
    const y = Math.round(e.latlng.lat);
    const title = prompt("Titre du marqueur ?", "Nouveau lieu");
    if (title) {
      addMarker(x, y, title);
      savedMarkers.push({ x, y, title });
      saveMarkers();
    }
  }
});

// === AFFICHAGE COORDONNÉES SOURIS ===
const coordDiv = L.control({ position: 'bottomleft' });
coordDiv.onAdd = () => {
  const div = L.DomUtil.create('div', 'mouse-coordinates');
  div.innerHTML = 'Survolez la carte';
  coordDiv._div = div;
  return div;
};
coordDiv.update = latlng => {
  coordDiv._div.innerHTML = latlng
    ? `<b>Position souris</b><br>X: ${Math.round(latlng.lng)}<br>Y: ${Math.round(latlng.lat)}`
    : 'Survolez la carte';
};
coordDiv.addTo(map);

let lastMouseLatLng = null;
map.on('mousemove', (e) => {
  lastMouseLatLng = e.latlng;
  coordDiv.update(e.latlng);
});
map.on('zoom', () => {
  if (lastMouseLatLng) {
    const containerPoint = map.latLngToContainerPoint(lastMouseLatLng);
    coordDiv.update(map.containerPointToLatLng(containerPoint));
  }
});

// === ZONES DESSINÉES ===
function loadZones(mapId) {
  fetch(`sqlFunction/get_zones.php?mapId=${mapId}`)
    .then(res => res.json())
    .then(zones => {
      drawnItems.clearLayers();
      zones.forEach(zone => {
        const coords = JSON.parse(zone.draw_js).geometry.coordinates[0];
        const latLngs = coords.map(([x, y]) => [y, x]);
        const polygon = L.polygon(latLngs, {
          color: zone.color || 'blue',
          fillColor: zone.color || 'blue',
          fillOpacity: 0.1,
          weight: 2,
          opacity: 1
        });
        polygon.bindTooltip(zone.Zone, {
          permanent: true,
          direction: 'center',
          className: 'zone-label'
        }).openTooltip();
        drawnItems.addLayer(polygon);
      });
    })
    .catch(err => console.error("❌ Erreur chargement zones :", err));
}

// === CONTRÔLE DE DESSIN ===
map.addLayer(newDrawnItems);
map.addControl(new L.Control.Draw({
  edit: { featureGroup: newDrawnItems },
  draw: {
    polygon: true,
    polyline: true,
    rectangle: true,
    circle: true,
    marker: true
  }
}));

function customizeAndAddLayer(layer) {
  const name = prompt("Nom de votre dessin ?", "Dessin sans nom");
  const color = prompt("Choisissez une couleur (ex: 'red', 'blue') :", "red");

  if (layer.setStyle) {
    layer.setStyle({ color });
  }

  layer.bindPopup(`<b>${name}</b><br>Couleur: ${color}`);
  newDrawnItems.addLayer(layer);
}

function saveDrawings(mapId) {
  if (!mapId || isSaving) return;

  isSaving = true;
  const drawings = [];

  newDrawnItems.eachLayer(layer => {
    const geoJSON = layer.toGeoJSON();
    if (geoJSON.geometry?.type === "Polygon") {
      geoJSON.geometry.coordinates[0] = geoJSON.geometry.coordinates[0].map(
        ([lng, lat]) => [Math.round(lng), Math.round(lat)]
      );

      const popup = layer.getPopup()?.getContent() || "Sans nom";
      const [, name = "Sans nom", color = "red"] = popup.match(/<b>(.*?)<\/b><br>Couleur: (.*)/) || [];

      geoJSON.properties = { name, color };

      drawings.push({
        zone: name,
        color,
        mapId,
        draw_js: JSON.stringify(geoJSON)
      });
    }
  });

  fetch('sqlFunction/save_zone.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(drawings)
  })
    .then(res => res.json())
    .then(res => {
      if (res.success) {
        console.log("✅ Zones enregistrées !");
        newDrawnItems.clearLayers();
        loadZones(mapId); // ⬅️ Recharge depuis le backend
      } else {
        console.error("❌ Erreur :", res.error);
      }
    })
    .catch(err => console.error("❌ Erreur réseau :", err))
    .finally(() => isSaving = false);
}

// === GESTION DES ÉVÉNEMENTS DE DESSIN ===
map.on('draw:created', e => {
  customizeAndAddLayer(e.layer);
  saveDrawings(currentMapId);
});
map.on('draw:edited', () => setTimeout(() => saveDrawings(currentMapId), 1000));
map.on('draw:deleted', () => setTimeout(() => saveDrawings(currentMapId), 1000));

// === SYNCHRONISATION ID CARTE COURANTE ===
fetch('sqlFunction/get_maps.php')
  .then(res => res.json())
  .then(data => {
    data.forEach(mapData => {
      mapNameToId[mapData.name] = mapData.id;
    });

    map.on('baselayerchange', e => {
      currentMapId = mapNameToId[e.name];
      window.dispatchEvent(new CustomEvent('mapIdChanged', { detail: currentMapId }));
    });
  })
  .catch(error => console.error("❌ Erreur lors du chargement des maps :", error));

window.addEventListener('mapIdChanged', (e) => {
  currentMapId = e.detail;
  console.log("ID de la carte dans draw.js :", currentMapId);
});
