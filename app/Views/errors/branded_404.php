<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php /** @var array<int,array> $featured */ ?>
<section class="section" style="padding-top:calc(var(--header-h) + 70px);">
  <div class="container text-center">
    <div style="font-family:var(--font-head); font-size:clamp(5rem,18vw,11rem); font-weight:800; color:var(--gold); line-height:1;">404</div>
    <h1 class="mt-1">We couldn't find that page</h1>
    <p class="mt-1 muted" style="max-width:520px; margin-inline:auto;">The page may have moved or sold. Let's get you back to finding your dream home.</p>

    <form action="<?= base_url('public/listings') ?>" method="get" class="search-card" style="margin-inline:auto; max-width:560px; background:#fff; border:1px solid var(--line);">
      <div class="search-fields" style="grid-template-columns:1fr auto;">
        <div class="search-field"><label>Search Listings</label><input type="text" name="q" placeholder="City, ZIP or neighborhood"></div>
        <button class="btn btn--gold btn--lg"><ion-icon name="search-outline"></ion-icon> Search</button>
      </div>
    </form>

    <div class="flex items-center gap-1" style="justify-content:center; margin-top:1.5rem;">
      <a href="<?= base_url('public/') ?>" class="btn btn--navy"><ion-icon name="home-outline"></ion-icon> Back Home</a>
      <a href="<?= base_url('public/listings') ?>" class="btn btn--ghost">Browse Listings</a>
    </div>
  </div>
</section>

<?php if (! empty($featured)): ?>
<section class="section section--gray section--tight">
  <div class="container">
    <div class="section-head section-head--center"><span class="eyebrow">While You're Here</span><h2>Featured Listings</h2></div>
    <div class="card-grid">
      <?php foreach ($featured as $property): ?>
        <?= view('partials/property_card', ['property' => $property, 'aos' => false]) ?>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>
<?= $this->endSection() ?>
