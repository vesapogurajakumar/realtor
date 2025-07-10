<!-- Leaflet Map Partial - Include CSS and JS dependencies before this -->
<!-- Dependencies: Leaflet CSS, Leaflet JS, Bootstrap CSS (optional) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<style>
.container {
    max-width: 60%;
    margin: 0 auto;
    padding: 20px;
}

.map-container {
    position: relative;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

#map {
    height: 500px;
    width: 100%;
}

.controls {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.control-btn {
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}

.control-btn:hover {
    background: rgba(255,255,255,1);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.info-panel {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: rgba(255,255,255,0.95);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
    max-width: 300px;
    z-index: 1000;
}

.info-panel h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.1rem;
}

.info-panel p {
    margin: 5px 0;
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="container mt-2 mb-2">
    <div class="map-container">
        <div class="controls">
            <button class="control-btn" onclick="toggleLayer()">Toggle Layer</button>
            <button class="control-btn" onclick="flyToLocation()">Fly to Hyderabad</button>
        </div>
        
        <div class="info-panel">
            <h3>Map Information</h3>
            <p id="markerCount">Markers: 1</p>
            <p id="currentView">View: City</p>
            <p id="lastAction">Action: Map initialized</p>
        </div>
        
        <div id="map"></div>
    </div>
</div>

<script>
// Initialize map
const map = L.map('map', {
    center: [17.4442252, 78.3869568], // Hyderabad coordinates
    zoom: 12,
    zoomControl: true
});

// Add tile layer
let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Alternative tile layer
const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Tiles © Esri'
});

// Array to store markers
let markers = [];
let markerCount = 1;
let layerToggled = false;

// Add initial marker
const initialMarker = L.marker([17.4442252, 78.3869568])
    .addTo(map)
    .bindPopup('<div style="text-align: center;"><h3>Welcome to Hyderabad!</h3><p>Click the controls to explore</p></div>');

markers.push(initialMarker);

function toggleLayer() {
    if (!layerToggled) {
        map.removeLayer(currentLayer);
        satelliteLayer.addTo(map);
        layerToggled = true;
        updateInfo('lastAction', 'Switched to satellite view');
    } else {
        map.removeLayer(satelliteLayer);
        currentLayer.addTo(map);
        layerToggled = false;
        updateInfo('lastAction', 'Switched to street view');
    }
}

function flyToLocation() {
    map.flyTo([17.4442252, 78.3869568], 12, {
        duration: 2,
        easeLinearity: 0.25
    });
    updateInfo('currentView', 'View: Hyderabad City');
    updateInfo('lastAction', 'Flew to Hyderabad');
}

function updateInfo(id, text) {
    document.getElementById(id).textContent = text;
}

// Map event listeners
map.on('zoomend', function() {
    const zoom = map.getZoom();
    if (zoom > 15) {
        updateInfo('currentView', 'View: Street level');
    } else if (zoom > 10) {
        updateInfo('currentView', 'View: City level');
    } else {
        updateInfo('currentView', 'View: Regional');
    }
});

// Initialize info panel
updateInfo('markerCount', `Markers: ${markerCount}`);
</script>