<!DOCTYPE html>
<html>
<head>
    <title>Register Agency</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 400px; }
    </style>
</head>
<body>
    <h1>Register Your Agency</h1>
    <form action="save_agency.php" method="POST">
        <label>Agency Name:</label>
        <input type="text" name="name" required><br><br>
        
        <label>Address:</label>
        <input type="text" name="address" id="address" required>
        <button type="button" onclick="searchAddress()">Search</button><br><br>
        
        <input type="hidden" name="lat" id="lat">
        <input type="hidden" name="lng" id="lng">
        
        <div id="map"></div>
        <p>Click on the map to select location</p>
        
        <button type="submit">Register Agency</button>
    </form>
<p>Maps © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors</p>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        let map, marker;

        // Initialize map
        map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Handle map clicks
        map.on('click', function(e) {
            if (marker) marker.remove();
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('lat').value = e.latlng.lat;
            document.getElementById('lng').value = e.latlng.lng;
            reverseGeocode(e.latlng);
        });

        // Address search function
        async function searchAddress() {
            const address = document.getElementById('address').value;
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
            const data = await response.json();
            
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                map.setView([lat, lng], 14);
                if (marker) marker.remove();
                marker = L.marker([lat, lng]).addTo(map);
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
            }

            
        }

        // Reverse geocoding
        async function reverseGeocode(latlng) {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`
            );
            const data = await response.json();
            if (data.address) {
                document.getElementById('address').value = 
                    data.display_name || '';
            }
        }
    </script>
</body>
</html>