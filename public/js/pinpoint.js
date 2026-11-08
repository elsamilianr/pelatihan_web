/**
 * Ikon pinpoint custom untuk Leaflet.
 * Pakai: L.marker([lat, lng], { icon: createPinIcon({ color: '#e11d48' }) })
 * Harus dimuat SETELAH leaflet.js.
 */
(function () {
  if (document.getElementById('pinpoint-style')) return;

  const style = document.createElement('style');
  style.id = 'pinpoint-style';
  style.textContent = `
    .pinpoint { background: none; border: none; }
    .pinpoint .pin-wrap {
      width: 100%; height: 100%;
      transition: transform .15s ease;
      transform-origin: 50% 100%;
      filter: drop-shadow(0 3px 3px rgba(0,0,0,.35));
    }
    .pinpoint .pin-shadow {
      position: absolute; left: 50%; bottom: 0; width: 14px; height: 5px;
      margin-left: -7px; border-radius: 50%;
      background: rgba(0,0,0,.3); transition: all .15s ease;
    }
    /* efek "terangkat" saat pin sedang digeser */
    .pinpoint.leaflet-drag-target .pin-wrap { transform: translateY(-8px) scale(1.12); }
    .pinpoint.leaflet-drag-target .pin-shadow { transform: scale(.6); opacity: .5; }
    .pinpoint.leaflet-marker-draggable { cursor: grab; }
    .pinpoint.leaflet-drag-target { cursor: grabbing; }
  `;
  document.head.appendChild(style);

  window.createPinIcon = function (opts) {
    opts = opts || {};
    const color = opts.color || '#e11d48';
    const w = opts.width || 36;
    const h = opts.height || 48;

    const svg = `
      <svg class="pin-wrap" viewBox="0 0 36 48" xmlns="http://www.w3.org/2000/svg">
        <path d="M18 1C9 1 2 8 2 17c0 12 16 29 16 29s16-17 16-29C34 8 27 1 18 1z"
              fill="${color}" stroke="#fff" stroke-width="2.5" stroke-linejoin="round"/>
        <circle cx="18" cy="17" r="6.5" fill="#fff"/>
        <circle cx="18" cy="17" r="3" fill="${color}"/>
      </svg>
      <span class="pin-shadow"></span>`;

    return L.divIcon({
      className: 'pinpoint',
      html: svg,
      iconSize: [w, h],
      iconAnchor: [w / 2, h - 2], // ujung bawah pin = titik koordinat
      popupAnchor: [0, -h + 6],
    });
  };
})();
