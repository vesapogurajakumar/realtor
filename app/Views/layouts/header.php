<?php
/** @var string $activeNav */
$activeNav = $activeNav ?? '';
$nav       = [
    'home'     => ['label' => 'Home',       'url' => base_url('public/')],
    'listings' => ['label' => 'Listings',   'url' => base_url('public/listings')],
    'blog'     => ['label' => 'Blog',        'url' => base_url('public/blog')],
    'about'    => ['label' => 'About',       'url' => base_url('public/about')],
    'contact'  => ['label' => 'Contact',     'url' => base_url('public/contact')],
];
?>
<div class="nav-backdrop" data-nav-backdrop></div>
<header class="site-header" data-header>
  <div class="container">
    <a href="<?= base_url('public/') ?>" class="brand" aria-label="Vesta home">
      <svg class="brand-mark" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <path d="M20 3 4 16v21h11V25h10v12h11V16L20 3Z" fill="#C9A84C"/>
        <path d="M20 3 4 16v21h11V25h10v12h11V16L20 3Z" stroke="#C9A84C" stroke-width="1.5" stroke-linejoin="round"/>
      </svg>
      <span>Ves<b>ta</b></span>
    </a>

    <nav class="nav" data-nav aria-label="Primary">
      <button class="nav-close" data-nav-close aria-label="Close menu"><ion-icon name="close-outline"></ion-icon></button>
      <ul class="nav-list">
        <?php foreach ($nav as $key => $item): ?>
          <li>
            <a href="<?= $item['url'] ?>" class="nav-link <?= $activeNav === $key ? 'is-active' : '' ?>" data-nav-link><?= esc($item['label']) ?></a>
          </li>
        <?php endforeach ?>
      </ul>
    </nav>

    <div class="header-actions">
      <a href="tel:+15125550192" class="header-phone">
        <ion-icon name="call-outline"></ion-icon>
        <span class="label">+1 (512) 555-0192</span>
      </a>
      <a href="<?= base_url('public/contact') ?>" class="btn btn--gold btn--sm">List Your Property</a>
      <button class="nav-toggle" data-nav-toggle aria-label="Open menu" aria-expanded="false">
        <ion-icon name="menu-outline"></ion-icon>
      </button>
    </div>
  </div>
</header>
