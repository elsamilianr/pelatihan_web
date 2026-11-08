<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Peta Cabang Toko Suku Cadang</title>
  <link rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <style>
    body { margin: 0; font-family: sans-serif; }
    #map { height: 100vh; }
  </style>
</head>
<body>
  <div id="map"></div>
  <button id="btn-near"
  style="position:absolute; z-index:1000; top:10px; right:10px;
         padding:8px 12px; cursor:pointer;">
  Cabang Terdekat
</button>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="{{ asset('js/pinpoint.js') }}"></script>
  <script>
    const branches = @json($branches);
    const map = L.map('map').setView([-6.2, 106.82], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    const markers = [];
branches.forEach(b => {
  const m = L.marker([b.latitude, b.longitude], { icon: createPinIcon() }).addTo(map);
  m.bindPopup('<b>' + b.nama + '</b><br>' + b.alamat);
  markers.push(m);
});
if (markers.length) {
  map.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
}
function jarakKm(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const rad = d => d * Math.PI / 180;
  const dLat = rad(lat2 - lat1), dLon = rad(lon2 - lon1);
  const a = Math.sin(dLat / 2) ** 2 +
    Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLon / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(a));
}
document.getElementById('btn-near').addEventListener('click', () => {
  navigator.geolocation.getCurrentPosition(pos => {
    const { latitude, longitude } = pos.coords;
    let best = null;
    branches.forEach((b, i) => {
      const d = jarakKm(latitude, longitude, b.latitude, b.longitude);
      if (!best || d < best.d) best = { d, i };
    });
    L.circleMarker([latitude, longitude], { radius: 8 }).addTo(map)
      .bindPopup('Lokasi Anda');
    const b = branches[best.i];
    markers[best.i]
      .bindPopup('<b>' + b.nama + '</b><br>Jarak: ' + best.d.toFixed(2) + ' km')
      .openPopup();
    map.fitBounds(L.latLngBounds(
      [[latitude, longitude], [b.latitude, b.longitude]]).pad(0.3));
  }, () => alert('Lokasi tidak dapat diakses. Periksa izin browser.'));
});

  </script>
</body>
</html>