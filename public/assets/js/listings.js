/* =============================================================
   VESTA — Listings: filters, AJAX results, cluster map
   ============================================================= */
(function () {
  'use strict';
  const cfg = window.VESTA_LISTINGS || { apiUrl: '', markers: [] };

  const form        = document.querySelector('[data-filter-form]');
  const grid        = document.querySelector('[data-results-grid]');
  const resultsWrap = document.querySelector('.listings-results');
  const countEl     = document.querySelector('[data-result-count]');
  const pagerWrap   = document.querySelector('[data-pagination]');
  const sortSel     = document.querySelector('[data-sort]');
  let currentPage   = 1;

  /* ---------- noUiSlider: price ---------- */
  const priceEl = document.querySelector('[data-price-slider]');
  const fmtMoney = (n) => '$' + Math.round(n).toLocaleString('en-US');
  if (priceEl && window.noUiSlider) {
    const min = +priceEl.dataset.min, max = +priceEl.dataset.max;
    const start = +priceEl.dataset.start || min, end = +priceEl.dataset.end || max;
    noUiSlider.create(priceEl, {
      start: [start, end], connect: true, step: 25000,
      range: { min, max },
    });
    const minOut = document.querySelector('[data-price-min]');
    const maxOut = document.querySelector('[data-price-max]');
    const minIn  = form.querySelector('[name="min_price"]');
    const maxIn  = form.querySelector('[name="max_price"]');
    priceEl.noUiSlider.on('update', (vals) => {
      if (minOut) minOut.textContent = fmtMoney(vals[0]);
      if (maxOut) maxOut.textContent = fmtMoney(vals[1]);
    });
    priceEl.noUiSlider.on('change', (vals) => {
      minIn.value = Math.round(vals[0]) <= min ? '' : Math.round(vals[0]);
      maxIn.value = Math.round(vals[1]) >= max ? '' : Math.round(vals[1]);
      applyFilters();
    });
  }

  /* ---------- Choices: amenities ---------- */
  const amenEl = document.querySelector('[data-amenities]');
  if (amenEl && window.Choices) {
    new Choices(amenEl, { removeItemButton: true, placeholderValue: 'Select amenities', searchPlaceholderValue: 'Search…', shouldSort: false });
    amenEl.addEventListener('change', applyFilters);
  }

  /* ---------- Chip active state ---------- */
  document.querySelectorAll('.chip-row').forEach((row) => {
    row.addEventListener('change', () => {
      row.querySelectorAll('.chip').forEach((c) => c.classList.remove('is-active'));
      const checked = row.querySelector('input:checked');
      if (checked) checked.closest('.chip').classList.add('is-active');
    });
  });

  /* ---------- Build query string from form ---------- */
  function buildQuery() {
    const data = new FormData(form);
    const params = new URLSearchParams();
    for (const [k, v] of data.entries()) {
      if (v !== '' && v != null) params.append(k, v);
    }
    if (sortSel && sortSel.value) params.set('sort', sortSel.value);
    params.set('page', currentPage);
    return params.toString();
  }

  /* ---------- Debounce ---------- */
  let timer;
  function applyFilters(resetPage = true) {
    if (resetPage) currentPage = 1;
    clearTimeout(timer);
    timer = setTimeout(fetchResults, 300);
  }

  /* ---------- Fetch + render ---------- */
  async function fetchResults() {
    const qs = buildQuery();
    if (grid) grid.classList.add('is-loading');
    // keep the address bar shareable
    history.replaceState(null, '', location.pathname + '?' + qs);
    try {
      const res = await fetch(cfg.apiUrl + '?' + qs, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();
      if (grid) {
        grid.innerHTML = data.html && data.total > 0
          ? data.html
          : '<div class="listings-empty"><ion-icon name="home-outline" style="font-size:3rem;"></ion-icon><h3 class="mt-1">No homes match your filters</h3><p>Try widening your price range or clearing a filter.</p></div>';
      }
      if (countEl) countEl.textContent = data.summary;
      if (pagerWrap) pagerWrap.innerHTML = data.pagination || '';
      if (window.syncSavedHearts) window.syncSavedHearts();
      updateMarkers(data.markers || []);
    } catch (e) {
      if (countEl) countEl.textContent = 'Could not load results. Please try again.';
    } finally {
      if (grid) grid.classList.remove('is-loading');
    }
  }

  if (form) {
    form.addEventListener('input', (e) => {
      if (e.target.matches('input[name="q"]')) applyFilters();
    });
    form.addEventListener('change', (e) => {
      if (e.target.matches('input[name="status"], input[name="beds"], input[name="baths"], input[name="type[]"]')) applyFilters();
    });
    form.addEventListener('submit', (e) => { e.preventDefault(); applyFilters(); });
  }
  if (sortSel) sortSel.addEventListener('change', () => applyFilters());

  // reset
  const resetBtn = document.querySelector('[data-filter-reset]');
  if (resetBtn) resetBtn.addEventListener('click', () => {
    form.reset();
    document.querySelectorAll('.chip').forEach((c) => c.classList.remove('is-active'));
    document.querySelectorAll('.chip-row').forEach((r) => { const f = r.querySelector('input'); if (f) { f.checked = true; f.closest('.chip').classList.add('is-active'); } });
    if (priceEl && priceEl.noUiSlider) priceEl.noUiSlider.set([+priceEl.dataset.min, +priceEl.dataset.max]);
    location.href = location.pathname;
  });

  // pagination (delegated)
  if (pagerWrap) pagerWrap.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-page]');
    if (!btn || btn.disabled) return;
    currentPage = +btn.dataset.page;
    fetchResults();
    if (resultsWrap) resultsWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  /* ---------- View toggle (grid / list) ---------- */
  const viewToggle = document.querySelector('[data-view-toggle]');
  if (viewToggle) viewToggle.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-view]');
    if (!btn) return;
    viewToggle.querySelectorAll('button').forEach((b) => b.classList.remove('is-active'));
    btn.classList.add('is-active');
    grid.classList.toggle('card-grid--list', btn.dataset.view === 'list');
  });

  /* =============================================================
     MAP (Leaflet + cluster)
     ============================================================= */
  let map, clusterGroup;
  const layout    = document.querySelector('[data-listings-layout]');
  const mapPanel  = document.querySelector('[data-map-panel]');
  const mapToggle = document.querySelector('[data-map-toggle]');

  function goldPin() {
    return L.divIcon({
      className: 'map-marker-pin',
      html: '<svg width="34" height="42" viewBox="0 0 34 42" xmlns="http://www.w3.org/2000/svg"><path d="M17 0C7.6 0 0 7.6 0 17c0 12 17 25 17 25s17-13 17-25C34 7.6 26.4 0 17 0z" fill="#C9A84C"/><path d="M17 8l-8 6.5V25h5v-6h6v6h5V14.5L17 8z" fill="#0A1628"/></svg>',
      iconSize: [34, 42], iconAnchor: [17, 42], popupAnchor: [0, -38],
    });
  }

  function popupHtml(m) {
    return '<div class="map-popup">' +
      (m.img ? '<img src="' + m.img + '" alt="' + m.title + '" loading="lazy">' : '') +
      '<div class="map-popup-body">' +
      '<div class="map-popup-price">' + m.price + '</div>' +
      '<div class="map-popup-title">' + m.title + '</div>' +
      '<div class="map-popup-specs">' + m.beds + ' bd · ' + m.baths + ' ba · ' + (m.sqft ? m.sqft.toLocaleString() + ' sqft' : m.type) + '</div>' +
      '<a class="btn btn--navy btn--sm btn--block" href="' + m.url + '">View Property</a>' +
      '</div></div>';
  }

  function initMap() {
    if (map || !window.L) return;
    map = L.map('listings-map', { scrollWheelZoom: true, zoomControl: true });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap &copy; CARTO', maxZoom: 19, subdomains: 'abcd',
    }).addTo(map);
    clusterGroup = L.markerClusterGroup({
      iconCreateFunction: (cluster) => L.divIcon({
        html: '<div>' + cluster.getChildCount() + '</div>',
        className: 'marker-cluster-vesta', iconSize: [46, 46],
      }),
    });
    map.addLayer(clusterGroup);
    updateMarkers(cfg.markers);
  }

  function updateMarkers(markers) {
    if (!map || !clusterGroup) return;
    clusterGroup.clearLayers();
    const latlngs = [];
    markers.forEach((m) => {
      const marker = L.marker([m.lat, m.lng], { icon: goldPin() }).bindPopup(popupHtml(m));
      clusterGroup.addLayer(marker);
      latlngs.push([m.lat, m.lng]);
    });
    if (latlngs.length) map.fitBounds(latlngs, { padding: [40, 40], maxZoom: 12 });
    else map.setView([39.5, -98.35], 4);
  }

  if (mapToggle) mapToggle.addEventListener('click', () => {
    const show = mapPanel.hasAttribute('hidden');
    mapPanel.toggleAttribute('hidden', !show);
    layout.classList.toggle('has-map', show);
    mapToggle.querySelector('span').textContent = show ? 'Hide Map' : 'Show Map';
    if (show) { initMap(); setTimeout(() => map && map.invalidateSize(), 250); }
  });
})();
