@php
    $config = $field->getConfig();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-data="{
            config: @js($config),

            // Objek Leaflet disimpan di elemen DOM (bukan di data Alpine)
            // supaya tidak dibungkus proxy reaktif Alpine.
            getMap() { return this.$refs.map.__lmap; },
            getMarker() { return this.$refs.map.__lmarker; },

            loadLeaflet() {
                return new Promise((resolve) => {
                    if (window.L) { resolve(); return; }

                    if (! document.querySelector('link[data-leaflet]')) {
                        const css = document.createElement('link');
                        css.rel = 'stylesheet';
                        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        css.setAttribute('data-leaflet', '1');
                        document.head.appendChild(css);
                    }

                    let script = document.querySelector('script[data-leaflet]');
                    if (! script) {
                        script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.setAttribute('data-leaflet', '1');
                        document.head.appendChild(script);
                    }
                    script.addEventListener('load', () => resolve());
                });
            },

            pinIcon() {
                const color = this.config.pinColor;
                return L.divIcon({
                    className: '',
                    html: `<svg xmlns='http://www.w3.org/2000/svg' width='32' height='42' viewBox='0 0 24 32'><path d='M12 0C5.4 0 0 5.4 0 12c0 9 12 20 12 20s12-11 12-20C24 5.4 18.6 0 12 0z' fill='${color}' stroke='#ffffff' stroke-width='1.5'/><circle cx='12' cy='12' r='4.5' fill='#ffffff'/></svg>`,
                    iconSize: [32, 42],
                    iconAnchor: [16, 42],
                });
            },

            // Baca latitude/longitude dari input form. Hasil null jika kosong atau tidak valid.
            readInputs() {
                const lat = parseFloat(String(this.$wire.$get(this.config.latPath) ?? '').replace(',', '.'));
                const lng = parseFloat(String(this.$wire.$get(this.config.lngPath) ?? '').replace(',', '.'));

                if (! Number.isFinite(lat) || ! Number.isFinite(lng)) return null;
                if (Math.abs(lat) > 90 || Math.abs(lng) > 180) return null;

                return [lat, lng];
            },

            // Tampilkan marker (buat baru jika belum ada, geser jika sudah ada).
            placeMarker(lat, lng) {
                const existing = this.getMarker();

                if (existing) {
                    existing.setLatLng([lat, lng]);
                    return;
                }

                const marker = L.marker([lat, lng], { draggable: true, icon: this.pinIcon() }).addTo(this.getMap());

                marker.on('dragend', () => {
                    const p = marker.getLatLng();
                    this.setPoint(p.lat, p.lng);
                });

                this.$refs.map.__lmarker = marker;
            },

            // Dipanggil saat peta diklik atau pin digeser: pindahkan marker dan isi form.
            setPoint(lat, lng) {
                lat = Number(lat.toFixed(7));
                lng = Number(lng.toFixed(7));

                this.placeMarker(lat, lng);

                this.$wire.$set(this.config.latPath, lat, false);
                this.$wire.$set(this.config.lngPath, lng, false);
            },

            // Dipanggil saat input latitude/longitude diketik manual: marker mengikuti.
            syncFromInputs() {
                const pos = this.readInputs();
                if (! pos || ! this.getMap()) return;

                const current = this.getMarker() ? this.getMarker().getLatLng() : null;
                if (current && Math.abs(current.lat - pos[0]) < 1e-7 && Math.abs(current.lng - pos[1]) < 1e-7) return;

                this.placeMarker(pos[0], pos[1]);
                this.getMap().setView(pos, Math.max(this.getMap().getZoom(), 15));
            },

            async init() {
                await this.loadLeaflet();

                const c = this.config;
                const start = this.readInputs();

                const map = L.map(this.$refs.map).setView(
                    start ?? [c.defaultLat, c.defaultLng],
                    start ? 16 : c.defaultZoom
                );
                this.$refs.map.__lmap = map;

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap',
                }).addTo(map);

                if (start) this.placeMarker(start[0], start[1]);

                map.on('click', (e) => this.setPoint(e.latlng.lat, e.latlng.lng));

                this.$wire.$watch(c.latPath, () => this.syncFromInputs());
                this.$wire.$watch(c.lngPath, () => this.syncFromInputs());

                setTimeout(() => map.invalidateSize(), 250);
            },
        }"
    >
        <div
            x-ref="map"
            style="height: {{ $config['height'] }}; width: 100%; border-radius: 0.5rem; position: relative; z-index: 0; isolation: isolate;"
        ></div>

        <p style="margin-top: 0.5rem; font-size: 0.875rem; opacity: 0.7;">
            Klik pada peta untuk menaruh pin, atau geser pin ke lokasi yang tepat.
            Latitude dan longitude akan terisi otomatis.
        </p>
    </div>
</x-dynamic-component>
