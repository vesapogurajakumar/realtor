/* =============================================================
   VESTA — Property detail: gallery, mortgage calc, POI map
   ============================================================= */
(function () {
  'use strict';
  const data = window.VESTA_PROPERTY || { lat: 0, lng: 0, pois: [] };
  const $ = (s, c = document) => c.querySelector(s);

  /* ---------- Gallery (Swiper thumbs + GLightbox) ---------- */
  window.addEventListener('load', function () {
    if (window.Swiper) {
      const thumbs = new Swiper('.gallery-thumbs', {
        slidesPerView: 4, spaceBetween: 10, watchSlidesProgress: true,
        breakpoints: { 700: { slidesPerView: 6 } },
      });
      new Swiper('.gallery-main', {
        spaceBetween: 10, loop: false, thumbs: { swiper: thumbs },
      });
    }
    if (window.GLightbox) GLightbox({ selector: '.glightbox', loop: true });
  });

  /* ---------- Read more ---------- */
  const desc = $('[data-desc]');
  const descToggle = $('[data-desc-toggle]');
  if (descToggle && desc) {
    // only clamp if content overflows
    if (desc.querySelector('.desc-body').scrollHeight <= 200) {
      desc.classList.remove('is-clamped');
      descToggle.style.display = 'none';
    }
    descToggle.addEventListener('click', () => {
      const clamped = desc.classList.toggle('is-clamped');
      descToggle.innerHTML = clamped
        ? 'Read More <ion-icon name="chevron-down-outline"></ion-icon>'
        : 'Read Less <ion-icon name="chevron-up-outline"></ion-icon>';
    });
  }

  /* ---------- Mortgage calculator ---------- */
  const calc = $('[data-mortgage]');
  if (calc) {
    const get = (k) => parseFloat(calc.querySelector('[data-mc="' + k + '"]').value) || 0;
    const fmt = (n) => '$' + Math.round(n).toLocaleString('en-US');
    const monthlyEl  = $('[data-mc-monthly]', calc);
    const loanEl     = $('[data-mc-loan]', calc);
    const interestEl = $('[data-mc-interest]', calc);
    const amortWrap  = $('[data-amort]', calc);

    function compute() {
      const price = get('price');
      const down  = price * (get('down') / 100);
      const P     = Math.max(price - down, 0);
      const r     = get('rate') / 100 / 12;
      const n     = get('term') * 12;
      let monthly = r > 0 ? (P * r) / (1 - Math.pow(1 + r, -n)) : (n ? P / n : 0);
      if (!isFinite(monthly)) monthly = 0;
      monthlyEl.textContent  = fmt(monthly);
      loanEl.textContent     = fmt(P);
      interestEl.textContent = fmt(monthly * n - P);
      buildAmort(P, r, n, monthly);
    }

    function buildAmort(P, r, n, monthly) {
      if (!amortWrap) return;
      let bal = P, rows = '';
      for (let y = 1; y <= n / 12; y++) {
        let interestYr = 0, principalYr = 0;
        for (let m = 0; m < 12; m++) {
          const interest = bal * r;
          const principal = monthly - interest;
          interestYr += interest; principalYr += principal; bal -= principal;
        }
        rows += '<tr><td>' + y + '</td><td>' + fmt(principalYr) + '</td><td>' + fmt(interestYr) + '</td><td>' + fmt(Math.max(bal, 0)) + '</td></tr>';
      }
      amortWrap.innerHTML = '<table class="amort-table"><thead><tr><th>Year</th><th>Principal</th><th>Interest</th><th>Balance</th></tr></thead><tbody>' + rows + '</tbody></table>';
    }

    calc.addEventListener('input', compute);
    calc.addEventListener('change', compute);
    const amortToggle = $('[data-amort-toggle]', calc);
    if (amortToggle) amortToggle.addEventListener('click', () => amortWrap.toggleAttribute('hidden'));
    compute();
  }

  /* ---------- POI Map ---------- */
  if (window.L && document.getElementById('property-map')) {
    const map = L.map('property-map', { scrollWheelZoom: false });
    const layers = {
      standard: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', { attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19 }),
      satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Tiles &copy; Esri', maxZoom: 19 }),
    };
    layers.standard.addTo(map);
    L.control.layers({ 'Dark': layers.standard, 'Satellite': layers.satellite }, null, { position: 'topright' }).addTo(map);

    const homeIcon = L.divIcon({
      className: 'map-marker-pin',
      html: '<svg width="40" height="48" viewBox="0 0 34 42" xmlns="http://www.w3.org/2000/svg"><path d="M17 0C7.6 0 0 7.6 0 17c0 12 17 25 17 25s17-13 17-25C34 7.6 26.4 0 17 0z" fill="#C9A84C"/><path d="M17 8l-8 6.5V25h5v-6h6v6h5V14.5L17 8z" fill="#0A1628"/></svg>',
      iconSize: [40, 48], iconAnchor: [20, 48], popupAnchor: [0, -44],
    });
    L.marker([data.lat, data.lng], { icon: homeIcon }).addTo(map)
      .bindPopup('<div class="map-popup-body"><div class="map-popup-price">' + data.price + '</div><div class="map-popup-title">' + data.title + '</div></div>').openPopup();

    // 1-mile radius circle
    L.circle([data.lat, data.lng], { radius: 1609, color: '#C9A84C', weight: 1, fillOpacity: 0.06 }).addTo(map);

    const POI_EMOJI = { school: '🎓', hospital: '🏥', grocery: '🛒', transit: '🚉' };
    const poiLayers = {};
    (data.pois || []).forEach((poi) => {
      const icon = L.divIcon({ className: 'poi-marker', html: '<div style="background:#fff;border:2px solid #0A1628;border-radius:50%;width:30px;height:30px;display:grid;place-items:center;font-size:14px;box-shadow:0 2px 6px rgba(0,0,0,.3)">' + (POI_EMOJI[poi.type] || '📍') + '</div>', iconSize: [30, 30], iconAnchor: [15, 15] });
      const m = L.marker([poi.lat, poi.lng], { icon }).bindPopup(
        '<div class="map-popup-body"><strong>' + poi.name + '</strong><br><span class="map-popup-specs">' +
        (poi.rating ? '★ ' + poi.rating + '/10 · ' : '') + (poi.distance || '') + '</span></div>'
      );
      (poiLayers[poi.type] = poiLayers[poi.type] || L.layerGroup().addTo(map)).addLayer(m);
    });

    // Fit bounds to property + POIs
    const pts = [[data.lat, data.lng]].concat((data.pois || []).map((p) => [p.lat, p.lng]));
    map.fitBounds(pts, { padding: [50, 50], maxZoom: 14 });

    // POI toggles
    document.querySelectorAll('[data-poi]').forEach((cb) => {
      cb.addEventListener('change', () => {
        const layer = poiLayers[cb.dataset.poi];
        cb.closest('.chip').classList.toggle('is-active', cb.checked);
        if (!layer) return;
        if (cb.checked) map.addLayer(layer); else map.removeLayer(layer);
      });
    });

    // Fullscreen
    const fsBtn = document.querySelector('[data-map-fs]');
    const fsTarget = document.querySelector('[data-map-fs-target]');
    if (fsBtn && fsTarget) fsBtn.addEventListener('click', () => {
      if (!document.fullscreenElement) fsTarget.requestFullscreen?.();
      else document.exitFullscreen?.();
      setTimeout(() => map.invalidateSize(), 300);
    });
  }
})();
