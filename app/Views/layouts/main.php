<?php
/**
 * Main layout shell for the Vesta real-estate site.
 * Expected (optional) data: $title, $metaDescription, $ogImage, $canonical,
 *                           $bodyClass, $jsonld (raw JSON-LD string), $activeNav.
 */
$title           = $title           ?? 'Vesta | Luxury Real Estate & Homes for Sale';
$metaDescription = $metaDescription ?? 'Discover luxury homes, waterfront villas, penthouses and estates. Vesta connects discerning buyers with the finest properties and expert agents.';
$ogImage         = $ogImage         ?? base_url('public/assets/images/hero-banner.png');
$canonical       = $canonical       ?? current_url();
$bodyClass       = $bodyClass       ?? '';
$activeNav       = $activeNav       ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= esc($title) ?></title>
  <meta name="description" content="<?= esc($metaDescription) ?>">
  <link rel="canonical" href="<?= esc($canonical) ?>">
  <meta name="theme-color" content="#0A1628">

  <!-- Open Graph / Twitter -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Vesta Real Estate">
  <meta property="og:title" content="<?= esc($title) ?>">
  <meta property="og:description" content="<?= esc($metaDescription) ?>">
  <meta property="og:image" content="<?= esc($ogImage) ?>">
  <meta property="og:url" content="<?= esc($canonical) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= esc($title) ?>">
  <meta name="twitter:description" content="<?= esc($metaDescription) ?>">
  <meta name="twitter:image" content="<?= esc($ogImage) ?>">

  <link rel="shortcut icon" href="<?= base_url('public/favicon.ico') ?>">

  <!-- Perf: warm up image + font CDNs early -->
  <link rel="preconnect" href="https://images.unsplash.com" crossorigin>
  <link rel="dns-prefetch" href="https://images.unsplash.com">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">

  <!-- AOS scroll animations -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
  <!-- Swiper -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <!-- App styles -->
  <link rel="stylesheet" href="<?= base_url('public/assets/css/app.css') ?>">

  <?= $this->renderSection('styles') ?>

  <?php if (! empty($jsonld)): ?>
  <script type="application/ld+json"><?= $jsonld ?></script>
  <?php endif ?>
</head>
<body class="<?= esc($bodyClass, 'attr') ?>">

  <?= view('layouts/header', ['activeNav' => $activeNav]) ?>

  <main id="main">
    <?= $this->renderSection('content') ?>
  </main>

  <?= view('layouts/footer') ?>

  <!-- Floating actions -->
  <div class="float-stack">
    <a href="https://wa.me/15125550192" target="_blank" rel="noopener" class="float-btn float-btn--wa" aria-label="Chat on WhatsApp">
      <ion-icon name="logo-whatsapp"></ion-icon>
    </a>
    <button class="float-btn float-btn--top" data-scroll-top aria-label="Back to top">
      <ion-icon name="arrow-up-outline"></ion-icon>
    </button>
  </div>

  <!-- Exit-intent lead capture -->
  <div class="modal" data-exit-modal aria-hidden="true">
    <div class="modal-backdrop" data-exit-close></div>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="exitTitle">
      <button class="modal-close" data-exit-close aria-label="Close">&times;</button>
      <div class="icon-badge"><ion-icon name="document-text-outline"></ion-icon></div>
      <h3 id="exitTitle">Don't leave empty-handed</h3>
      <p class="mt-1">Get our free 2025 Market Report with neighborhood price trends and forecasts.</p>
      <form class="mt-2" data-ajax-form action="<?= base_url('public/subscribe') ?>" method="post">
        <?= csrf_field() ?>
        <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="form-alert" data-alert></div>
        <div class="field">
          <input type="email" name="email" class="input" placeholder="Your email address" required>
        </div>
        <button type="submit" class="btn btn--gold btn--block">Send Me the Report</button>
        <p class="form-note mt-1">No spam. Unsubscribe anytime.</p>
      </form>
    </div>
  </div>

  <!-- Live chat placeholder (Tawk.to) — replace PROPERTY_ID to activate
  <script>
    var Tawk_API = Tawk_API || {};
    (function(){ var s=document.createElement("script"); s.async=true;
      s.src="https://embed.tawk.to/PROPERTY_ID/default";
      document.head.appendChild(s); })();
  </script>
  -->

  <!-- Core libraries -->
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

  <script>window.VESTA = { csrfUrl: <?= json_encode(base_url('public/csrf')) ?> };</script>
  <script src="<?= base_url('public/assets/js/app.js') ?>" defer></script>

  <?= $this->renderSection('scripts') ?>
</body>
</html>
