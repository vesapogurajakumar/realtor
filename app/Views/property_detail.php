<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $property
 * @var array<int,array> $similar
 */
$p      = $property;
$images = $p['images'] ?? [];
$isRent = strtolower((string) ($p['status'] ?? '')) === 'for rent';
?>
<section class="section section--tight" style="padding-top:calc(var(--header-h) + 30px);">
  <div class="container">
    <nav class="breadcrumb" style="color:var(--gray-500);">
      <a href="<?= base_url('public/') ?>">Home</a><span>/</span>
      <a href="<?= base_url('public/listings') ?>">Listings</a><span>/</span>
      <span><?= esc($p['title']) ?></span>
    </nav>

    <!-- ============ GALLERY ============ -->
    <div class="detail-gallery" data-aos="fade-up">
      <div class="swiper gallery-main">
        <div class="swiper-wrapper">
          <?php foreach ($images as $i => $img): ?>
            <div class="swiper-slide">
              <a href="<?= esc(img_url($img, 1600, 80)) ?>" class="glightbox" data-gallery="property">
                <?= img_tag($img, $p['title'] . ' photo ' . ($i + 1), [
                    'width'         => 1200,
                    'widths'        => [600, 900, 1200, 1600],
                    'sizes'         => '(max-width: 1000px) 100vw, 66vw',
                    'loading'       => $i === 0 ? 'eager' : 'lazy',
                    'fetchpriority' => $i === 0 ? 'high' : '',
                ]) ?>
              </a>
            </div>
          <?php endforeach ?>
        </div>
        <span class="gallery-count"><ion-icon name="images-outline"></ion-icon> <?= count($images) ?> Photos</span>
      </div>
      <div class="swiper gallery-thumbs">
        <div class="swiper-wrapper">
          <?php foreach ($images as $img): ?>
            <div class="swiper-slide"><?= img_tag($img, 'thumbnail', ['width' => 220, 'widths' => [140, 220], 'sizes' => '120px']) ?></div>
          <?php endforeach ?>
        </div>
      </div>
    </div>

    <!-- ============ LAYOUT ============ -->
    <div class="detail-layout">
      <div class="detail-main">

        <div class="detail-header">
          <div>
            <span class="badge <?= status_class($p['status']) ?>" style="position:static; display:inline-block;"><?= esc($p['status']) ?></span>
            <h1 style="margin-top:.6rem;"><?= esc($p['title']) ?></h1>
            <p class="property-address mt-1"><ion-icon name="location-outline"></ion-icon> <?= esc($p['address'] . ', ' . $p['city'] . ', ' . $p['state'] . ' ' . $p['zip']) ?></p>
          </div>
          <div style="text-align:right;">
            <div class="detail-price"><?= price_label($p) ?></div>
            <button class="btn btn--ghost btn--sm mt-1" data-save="<?= esc($p['id'], 'attr') ?>"><ion-icon name="heart-outline"></ion-icon> Save</button>
          </div>
        </div>

        <div class="spec-grid">
          <div class="spec-box"><ion-icon name="bed-outline"></ion-icon><div class="v"><?= (int) $p['beds'] ?></div><div class="k">Bedrooms</div></div>
          <div class="spec-box"><ion-icon name="water-outline"></ion-icon><div class="v"><?= (int) $p['baths'] ?></div><div class="k">Bathrooms</div></div>
          <div class="spec-box"><ion-icon name="resize-outline"></ion-icon><div class="v"><?= sqft($p['sqft']) ?></div><div class="k">Sq Ft</div></div>
          <div class="spec-box"><ion-icon name="map-outline"></ion-icon><div class="v"><?= esc($p['lot_size'] ?? '—') ?></div><div class="k">Lot Size</div></div>
          <div class="spec-box"><ion-icon name="calendar-outline"></ion-icon><div class="v"><?= esc($p['year_built'] ?? '—') ?></div><div class="k">Year Built</div></div>
          <div class="spec-box"><ion-icon name="car-outline"></ion-icon><div class="v"><?= (int) ($p['garage'] ?? 0) ?></div><div class="k">Garage</div></div>
          <div class="spec-box"><ion-icon name="cash-outline"></ion-icon><div class="v"><?= ($p['hoa'] ?? 0) ? money($p['hoa']) : '—' ?></div><div class="k">HOA / mo</div></div>
          <div class="spec-box"><ion-icon name="pricetag-outline"></ion-icon><div class="v" style="font-size:.95rem;"><?= esc($p['mls'] ?? '—') ?></div><div class="k">MLS #</div></div>
        </div>

        <!-- Description -->
        <h2>About This Home</h2>
        <div class="detail-desc is-clamped" data-desc>
          <div class="desc-body"><p><?= nl2br(esc($p['description'] ?? '')) ?></p></div>
          <button class="read-more" data-desc-toggle>Read More <ion-icon name="chevron-down-outline"></ion-icon></button>
        </div>

        <!-- Features -->
        <?php if (! empty($p['features'])): ?>
          <hr class="divider">
          <h2>Features &amp; Amenities</h2>
          <ul class="feature-grid mt-2">
            <?php foreach ($p['features'] as $f): ?>
              <li><ion-icon name="checkmark-circle-outline"></ion-icon> <?= esc($f) ?></li>
            <?php endforeach ?>
          </ul>
        <?php endif ?>

        <!-- Map + POIs -->
        <hr class="divider">
        <div class="flex items-center" style="justify-content:space-between; flex-wrap:wrap; gap:1rem;">
          <h2 class="mb-0">Location &amp; Neighborhood</h2>
          <div class="flex items-center gap-1" style="gap:.5rem;">
            <a class="btn btn--ghost btn--sm" target="_blank" rel="noopener"
               href="https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=<?= $p['lat'] ?>,<?= $p['lng'] ?>"><ion-icon name="walk-outline"></ion-icon> Street View</a>
            <button class="btn btn--ghost btn--sm" data-map-fs><ion-icon name="expand-outline"></ion-icon> Fullscreen</button>
          </div>
        </div>

        <div class="score-row mt-2">
          <div class="score"><div class="ring" style="--v:<?= (int) ($p['walk_score'] ?? 0) ?>"><span><?= (int) ($p['walk_score'] ?? 0) ?></span></div><small>Walk Score</small></div>
          <div class="score"><div class="ring" style="--v:<?= (int) ($p['transit_score'] ?? 0) ?>"><span><?= (int) ($p['transit_score'] ?? 0) ?></span></div><small>Transit Score</small></div>
        </div>

        <div class="poi-toggles mt-2" data-poi-toggles>
          <label class="chip is-active"><input type="checkbox" data-poi="school" checked> 🎓 Schools</label>
          <label class="chip is-active"><input type="checkbox" data-poi="hospital" checked> 🏥 Hospitals</label>
          <label class="chip is-active"><input type="checkbox" data-poi="grocery" checked> 🛒 Grocery</label>
          <label class="chip is-active"><input type="checkbox" data-poi="transit" checked> 🚉 Transit</label>
        </div>

        <div class="map-panel" style="position:static; height:440px;" data-map-fs-target>
          <div id="property-map"></div>
        </div>
      </div>

      <!-- ============ STICKY SIDEBAR ============ -->
      <aside class="detail-sidebar">
        <div class="side-card">
          <div class="agent-row">
            <img src="<?= esc($p['agent']['photo'] ?? '') ?>" alt="<?= esc($p['agent']['name'] ?? 'Agent') ?>">
            <div>
              <strong><?= esc($p['agent']['name'] ?? '') ?></strong>
              <span><?= esc($p['agent']['title'] ?? 'Listing Advisor') ?></span>
            </div>
          </div>
          <div class="agent-actions">
            <a class="btn btn--ghost btn--sm" href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $p['agent']['phone'] ?? '')) ?>"><ion-icon name="call-outline"></ion-icon> Call</a>
            <a class="btn btn--ghost btn--sm" target="_blank" rel="noopener" href="https://wa.me/<?= esc($p['agent']['whatsapp'] ?? '') ?>"><ion-icon name="logo-whatsapp"></ion-icon> WhatsApp</a>
          </div>

          <form class="mt-2" data-ajax-form action="<?= base_url('public/contact') ?>" method="post">
            <?= csrf_field() ?>
            <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="interest" value="Buy">
            <input type="hidden" name="source" value="Property: <?= esc($p['title'], 'attr') ?> (<?= esc($p['id'], 'attr') ?>)">
            <div class="form-alert" data-alert></div>
            <div class="field"><input class="input" type="text" name="name" placeholder="Your name" required></div>
            <div class="field"><input class="input" type="email" name="email" placeholder="Email" required></div>
            <div class="field"><input class="input" type="tel" name="phone" placeholder="Phone (10 digits)" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Enter a 10-digit mobile number"></div>
            <div class="field"><textarea class="textarea" name="message" rows="3" placeholder="I'd like to know more about this property…">I'm interested in <?= esc($p['title']) ?> (<?= esc($p['mls'] ?? $p['id']) ?>).</textarea></div>
            <button type="submit" class="btn btn--gold btn--block">Request Info</button>
            <a href="<?= base_url('public/contact') ?>?interest=Tour&source=<?= urlencode('Tour: ' . $p['title']) ?>" class="btn btn--navy btn--block mt-1"><ion-icon name="calendar-outline"></ion-icon> Schedule a Tour</a>
            <?php if (! empty($p['virtual_tour'])): ?>
              <a href="<?= esc($p['virtual_tour']) ?>" target="_blank" rel="noopener" class="btn btn--ghost btn--block mt-1"><ion-icon name="videocam-outline"></ion-icon> Virtual Tour</a>
            <?php endif ?>
          </form>
        </div>

        <!-- Mortgage calculator -->
        <div class="side-card" data-mortgage>
          <h3 style="font-family:var(--font-body); font-size:1.1rem;">Mortgage Calculator</h3>
          <div class="field mt-1"><label>Home Price</label><input class="input" type="number" data-mc="price" value="<?= (int) $p['price'] ?>"></div>
          <div class="field-row">
            <div class="field"><label>Down Payment (%)</label><input class="input" type="number" data-mc="down" value="20" min="0" max="100"></div>
            <div class="field"><label>Rate (%)</label><input class="input" type="number" data-mc="rate" value="6.5" step="0.1"></div>
          </div>
          <div class="field"><label>Loan Term (years)</label>
            <select class="select" data-mc="term"><option value="30">30</option><option value="20">20</option><option value="15">15</option></select>
          </div>
          <div class="calc-output"><span>Est. Monthly Payment</span><span class="big" data-mc-monthly>—</span></div>
          <div class="flex" style="justify-content:space-between; font-size:.88rem; color:var(--gray-500);">
            <span>Loan amount: <strong data-mc-loan>—</strong></span>
            <span>Total interest: <strong data-mc-interest>—</strong></span>
          </div>
          <button class="amort-toggle mt-2" data-amort-toggle><ion-icon name="grid-outline"></ion-icon> Amortization schedule</button>
          <div data-amort hidden></div>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- ============ SIMILAR ============ -->
<?php if (! empty($similar)): ?>
<section class="section section--gray">
  <div class="container">
    <div class="section-head"><span class="eyebrow">You May Also Like</span><h2>Similar Properties</h2></div>
    <div class="card-grid">
      <?php foreach ($similar as $sp): ?>
        <?= view('partials/property_card', ['property' => $sp, 'aos' => true]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<!-- Sticky mobile tour CTA -->
<div class="sticky-tour">
  <a href="<?= base_url('public/contact') ?>?interest=Tour&source=<?= urlencode('Tour: ' . $p['title']) ?>" class="btn btn--gold btn--block btn--lg"><ion-icon name="calendar-outline"></ion-icon> Schedule a Tour</a>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.VESTA_PROPERTY = {
    lat: <?= (float) $p['lat'] ?>, lng: <?= (float) $p['lng'] ?>,
    title: <?= json_encode($p['title']) ?>,
    price: <?= json_encode(price_label($p)) ?>,
    pois: <?= json_encode($p['pois'] ?? [], JSON_UNESCAPED_SLASHES) ?>
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="<?= base_url('public/assets/js/property.js') ?>" defer></script>
<?= $this->endSection() ?>
