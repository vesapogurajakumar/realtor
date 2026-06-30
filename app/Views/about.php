<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php /** @var array<int,array> $team */ ?>
<section class="page-hero" style="padding:calc(var(--header-h) + 70px) 0 70px;">
  <?= img_tag('https://images.unsplash.com/photo-1600585154340-be6161a56a0c', 'Luxury interior', ['width' => 1920, 'widths' => [768, 1280, 1920], 'sizes' => '100vw', 'quality' => 72, 'loading' => 'eager', 'fetchpriority' => 'high']) ?>
  <div class="container">
    <nav class="breadcrumb"><a href="<?= base_url('public/') ?>">Home</a><span>/</span><span>About</span></nav>
    <h1>A Boutique Brokerage,<br>Built on Trust</h1>
    <p class="mt-1" style="color:rgba(255,255,255,.8); max-width:600px;">For fifteen years, Vesta has paired data-driven insight with white-glove service to help clients buy and sell the homes that matter most.</p>
  </div>
</section>

<!-- Story -->
<section class="section">
  <div class="container">
    <div class="cta-split">
      <div data-aos="fade-right">
        <span class="eyebrow">Our Story</span>
        <h2>Real estate, done with intention</h2>
        <p class="mt-2">Vesta began with a simple conviction: that buying or selling a home should feel less like a transaction and more like a partnership. We built a brokerage around senior advisors, honest counsel, and marketing that genuinely moves the needle.</p>
        <p class="mt-1">Today we represent some of the most distinctive homes across the country — from waterfront villas to skyline penthouses — while never losing the personal, considered service we started with.</p>
        <a href="<?= base_url('public/contact') ?>" class="btn btn--navy mt-2">Work With Us <ion-icon name="arrow-forward-outline"></ion-icon></a>
      </div>
      <div data-aos="fade-left">
        <?= img_tag('https://images.unsplash.com/photo-1497366811353-6870744d04b2', 'Vesta team at work', ['width' => 900, 'widths' => [500, 700, 900], 'sizes' => '(max-width: 900px) 92vw, 50vw', 'style' => 'border-radius:var(--radius-lg); box-shadow:var(--shadow-lg);']) ?>
      </div>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="section section--navy section--tight">
  <div class="container">
    <div class="stats-grid">
      <div data-aos="fade-up"><div class="stat-value" data-count="2400" data-suffix="+">0</div><div class="stat-label">Listings Represented</div></div>
      <div data-aos="fade-up" data-aos-delay="100"><div class="stat-value" data-count="3" data-prefix="$" data-suffix="B+">0</div><div class="stat-label">In Sales Volume</div></div>
      <div data-aos="fade-up" data-aos-delay="200"><div class="stat-value" data-count="15" data-suffix=" yrs">0</div><div class="stat-label">Of Experience</div></div>
      <div data-aos="fade-up" data-aos-delay="300"><div class="stat-value" data-count="98" data-suffix="%">0</div><div class="stat-label">Client Satisfaction</div></div>
    </div>
  </div>
</section>

<!-- Timeline -->
<section class="section section--gray">
  <div class="container">
    <div class="section-head section-head--center"><span class="eyebrow">Our Journey</span><h2>Milestones</h2></div>
    <div class="timeline">
      <?php
      $milestones = [
          ['2010', 'Vesta founded', 'Opened our first office with three advisors and a belief in service-first real estate.'],
          ['2014', 'Expanded coastal', 'Launched dedicated waterfront and coastal divisions across three states.'],
          ['2018', 'Crossed $1B in sales', 'Reached a billion dollars in cumulative transaction volume.'],
          ['2022', 'National footprint', 'Grew to advisors in eight metro markets, from Austin to Aspen.'],
          ['2025', 'A digital-first experience', 'Relaunched with data-rich listings, interactive maps and concierge tools.'],
      ];
      foreach ($milestones as $i => $m): ?>
        <div class="timeline-item" data-aos="fade-up" data-aos-delay="<?= $i * 60 ?>">
          <span class="yr"><?= esc($m[0]) ?></span>
          <h3 style="font-family:var(--font-body); font-size:1.15rem; margin-top:.2rem;"><?= esc($m[1]) ?></h3>
          <p class="mt-1"><?= esc($m[2]) ?></p>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- Team -->
<section class="section">
  <div class="container">
    <div class="section-head section-head--center"><span class="eyebrow">Our People</span><h2>Meet the Team</h2><p>Senior advisors who treat your move like their own.</p></div>
    <div class="team-grid">
      <?php foreach ($team as $member): ?>
        <div class="team-card" data-aos="fade-up">
          <div class="photo"><img src="<?= esc($member['photo']) ?>" alt="<?= esc($member['name']) ?>" loading="lazy"></div>
          <strong><?= esc($member['name']) ?></strong>
          <div><span><?= esc($member['title']) ?></span></div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- Awards -->
<section class="section section--gray section--tight">
  <div class="container">
    <div class="section-head section-head--center" style="margin-bottom:2rem;"><span class="eyebrow">Recognition</span><h2>Awards &amp; Certifications</h2></div>
    <div class="awards-row">
      <div class="award"><ion-icon name="trophy-outline" style="color:var(--gold);"></ion-icon> Top Brokerage 2024</div>
      <div class="award"><ion-icon name="ribbon-outline" style="color:var(--gold);"></ion-icon> Realtor® of the Year</div>
      <div class="award"><ion-icon name="star-outline" style="color:var(--gold);"></ion-icon> Luxury Specialist Network</div>
      <div class="award"><ion-icon name="shield-checkmark-outline" style="color:var(--gold);"></ion-icon> NAR Certified</div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section section--navy">
  <div class="container text-center">
    <h2>Ready to make your move?</h2>
    <p class="mt-1">Let's talk about your goals — no pressure, just expert guidance.</p>
    <a href="<?= base_url('public/contact') ?>" class="btn btn--gold btn--lg mt-2">Get in Touch</a>
  </div>
</section>
<?= $this->endSection() ?>
