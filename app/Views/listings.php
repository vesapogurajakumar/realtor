<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $result   ['results','total','page','pages','per_page']
 * @var array $params
 * @var array{min:int,max:int} $priceRange
 * @var array<int,string> $types
 * @var array<int,string> $amenities
 * @var array<int,string> $cities
 * @var array $markers
 */
$p = $params;
?>
<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1920&q=80" alt="City skyline" loading="eager">
  <div class="container">
    <nav class="breadcrumb"><a href="<?= base_url('public/') ?>">Home</a><span>/</span><span>Listings</span></nav>
    <h1>Find Your Next Home</h1>
    <p class="mt-1" style="color:rgba(255,255,255,.8); max-width:560px;">Browse our curated portfolio of luxury homes for sale and rent across the country.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">

    <div class="listings-toolbar">
      <p class="result-count" data-result-count><?= esc($result['total']) ?> <?= $result['total'] === 1 ? 'home' : 'homes' ?> found</p>
      <div class="toolbar-controls">
        <select class="select" data-sort style="max-width:200px;">
          <option value="newest" <?= ($p['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest</option>
          <option value="price_asc" <?= ($p['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
          <option value="price_desc" <?= ($p['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
          <option value="sqft_desc" <?= ($p['sort'] ?? '') === 'sqft_desc' ? 'selected' : '' ?>>Largest (sqft)</option>
        </select>
        <div class="view-toggle" data-view-toggle>
          <button data-view="grid" class="is-active" aria-label="Grid view"><ion-icon name="grid-outline"></ion-icon></button>
          <button data-view="list" aria-label="List view"><ion-icon name="list-outline"></ion-icon></button>
        </div>
        <button class="btn btn--ghost btn--sm" data-map-toggle><ion-icon name="map-outline"></ion-icon> <span>Show Map</span></button>
      </div>
    </div>

    <div class="listings-layout" data-listings-layout>

      <!-- ============ FILTER SIDEBAR ============ -->
      <aside class="filter-sidebar">
        <form data-filter-form>
          <div class="filter-group">
            <h4>Search</h4>
            <input class="input" type="text" name="q" value="<?= esc($p['q'] ?? '', 'attr') ?>" placeholder="City, ZIP or neighborhood" list="all-cities">
            <datalist id="all-cities"><?php foreach ($cities as $c): ?><option value="<?= esc($c, 'attr') ?>"><?php endforeach ?></datalist>
          </div>

          <div class="filter-group">
            <h4>Status</h4>
            <div class="chip-row">
              <?php foreach (['' => 'Any', 'For Sale' => 'For Sale', 'For Rent' => 'For Rent', 'Sold' => 'Sold'] as $val => $label): ?>
                <label class="chip <?= ($p['status'] ?? '') === $val ? 'is-active' : '' ?>">
                  <input type="radio" name="status" value="<?= esc($val, 'attr') ?>" <?= ($p['status'] ?? '') === $val ? 'checked' : '' ?>><?= esc($label) ?>
                </label>
              <?php endforeach ?>
            </div>
          </div>

          <div class="filter-group">
            <h4>Price Range</h4>
            <div data-price-slider
                 data-min="<?= (int) $priceRange['min'] ?>" data-max="<?= (int) $priceRange['max'] ?>"
                 data-start="<?= (int) ($p['min_price'] ?? $priceRange['min']) ?>" data-end="<?= (int) ($p['max_price'] ?? $priceRange['max']) ?>"></div>
            <div class="range-values">
              <span data-price-min></span><span data-price-max></span>
            </div>
            <input type="hidden" name="min_price" value="<?= esc($p['min_price'] ?? '', 'attr') ?>">
            <input type="hidden" name="max_price" value="<?= esc($p['max_price'] ?? '', 'attr') ?>">
          </div>

          <div class="filter-group">
            <h4>Bedrooms</h4>
            <div class="chip-row">
              <?php foreach (['' => 'Any', '1' => '1+', '2' => '2+', '3' => '3+', '4' => '4+', '5' => '5+'] as $val => $label): ?>
                <label class="chip <?= (string) ($p['beds'] ?? '') === $val ? 'is-active' : '' ?>">
                  <input type="radio" name="beds" value="<?= $val ?>" <?= (string) ($p['beds'] ?? '') === $val ? 'checked' : '' ?>><?= $label ?>
                </label>
              <?php endforeach ?>
            </div>
          </div>

          <div class="filter-group">
            <h4>Bathrooms</h4>
            <div class="chip-row">
              <?php foreach (['' => 'Any', '1' => '1+', '2' => '2+', '3' => '3+', '4' => '4+'] as $val => $label): ?>
                <label class="chip <?= (string) ($p['baths'] ?? '') === $val ? 'is-active' : '' ?>">
                  <input type="radio" name="baths" value="<?= $val ?>" <?= (string) ($p['baths'] ?? '') === $val ? 'checked' : '' ?>><?= $label ?>
                </label>
              <?php endforeach ?>
            </div>
          </div>

          <div class="filter-group">
            <h4>Property Type</h4>
            <div class="check-list">
              <?php $selTypes = is_array($p['type'] ?? null) ? $p['type'] : array_filter(explode(',', (string) ($p['type'] ?? ''))); ?>
              <?php foreach ($types as $t): ?>
                <label class="check-item">
                  <input type="checkbox" name="type[]" value="<?= esc($t, 'attr') ?>" <?= in_array($t, $selTypes, true) ? 'checked' : '' ?>><?= esc($t) ?>
                </label>
              <?php endforeach ?>
            </div>
          </div>

          <div class="filter-group">
            <h4>Amenities</h4>
            <select name="amenities[]" multiple data-amenities>
              <?php $selAmen = is_array($p['amenities'] ?? null) ? $p['amenities'] : array_filter(explode(',', (string) ($p['amenities'] ?? ''))); ?>
              <?php foreach ($amenities as $a): ?>
                <option value="<?= esc($a, 'attr') ?>" <?= in_array($a, $selAmen, true) ? 'selected' : '' ?>><?= esc($a) ?></option>
              <?php endforeach ?>
            </select>
          </div>

          <div class="filter-group">
            <button type="button" class="btn btn--ghost btn--sm btn--block" data-filter-reset>Reset Filters</button>
          </div>
        </form>
      </aside>

      <!-- ============ RESULTS ============ -->
      <div class="listings-results">
        <div class="card-grid" data-results-grid>
          <?php if (empty($result['results'])): ?>
            <div class="listings-empty"><ion-icon name="home-outline" style="font-size:3rem;"></ion-icon><h3 class="mt-1">No homes match your filters</h3><p>Try widening your price range or clearing a filter.</p></div>
          <?php else: ?>
            <?php foreach ($result['results'] as $property): ?>
              <?= view('partials/property_card', ['property' => $property, 'aos' => false]) ?>
            <?php endforeach ?>
          <?php endif ?>
        </div>
        <div data-pagination></div>
      </div>

      <!-- ============ MAP PANEL ============ -->
      <div class="map-panel" data-map-panel hidden>
        <div id="listings-map"></div>
      </div>

    </div>
  </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.VESTA_LISTINGS = {
    apiUrl: <?= json_encode(base_url('public/api/properties')) ?>,
    markers: <?= json_encode($markers, JSON_UNESCAPED_SLASHES) ?>
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
<script src="<?= base_url('public/assets/js/listings.js') ?>" defer></script>
<?= $this->endSection() ?>
