<?php
/**
 * Reusable property card.
 * @var array<string, mixed> $property
 * @var bool $aos  (optional) whether to add AOS attributes
 */
$p   = $property;
$img = $p['images'][0] ?? base_url('public/assets/images/property-1.jpg');
$aos = $aos ?? true;
?>
<article class="property-card" <?= $aos ? 'data-aos="fade-up"' : '' ?>>
  <div class="property-media">
    <span class="badge <?= status_class($p['status'] ?? '') ?>"><?= esc($p['status'] ?? 'For Sale') ?></span>
    <span class="badge badge--type"><?= esc($p['type'] ?? 'Home') ?></span>
    <a href="<?= property_url($p['id']) ?>" aria-label="View <?= esc($p['title'] ?? 'property') ?>">
      <img src="<?= esc($img) ?>" alt="<?= esc($p['title'] ?? 'Property') ?> in <?= esc($p['city'] ?? '') ?>" loading="lazy">
    </a>
    <button class="save-btn" data-save="<?= esc($p['id'], 'attr') ?>" aria-label="Save this property">
      <ion-icon name="heart-outline"></ion-icon>
    </button>
    <?php if (! empty($p['agent']['photo'])): ?>
      <img class="property-agent" src="<?= esc($p['agent']['photo']) ?>" alt="<?= esc($p['agent']['name'] ?? 'Agent') ?>" loading="lazy" title="<?= esc($p['agent']['name'] ?? '') ?>">
    <?php endif ?>
  </div>
  <div class="property-body">
    <p class="property-price"><?= price_label($p) ?></p>
    <h3 class="property-title"><a href="<?= property_url($p['id']) ?>"><?= esc($p['title'] ?? 'Untitled Property') ?></a></h3>
    <p class="property-address"><ion-icon name="location-outline"></ion-icon><?= esc(($p['neighborhood'] ?? '') . ', ' . ($p['city'] ?? '') . ', ' . ($p['state'] ?? '')) ?></p>
    <div class="property-specs">
      <span><ion-icon name="bed-outline"></ion-icon><?= (int) ($p['beds'] ?? 0) ?> Beds</span>
      <span><ion-icon name="water-outline"></ion-icon><?= (int) ($p['baths'] ?? 0) ?> Baths</span>
      <span><ion-icon name="resize-outline"></ion-icon><?= sqft($p['sqft'] ?? 0) ?> sqft</span>
    </div>
  </div>
</article>
