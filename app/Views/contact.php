<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/** @var array<int,array> $agents */
// Build a unique advisor list from the listing data.
$seen = [];
$advisors = [];
foreach ($agents as $prop) {
    $a = $prop['agent'] ?? null;
    if ($a && empty($seen[$a['email'] ?? ''])) {
        $seen[$a['email'] ?? ''] = true;
        $advisors[] = $a;
    }
}
$advisors    = array_slice($advisors, 0, 4);
$prefInterest = (string) (service('request')->getGet('interest') ?? '');
$prefSource   = (string) (service('request')->getGet('source') ?? '');
?>
<section class="page-hero" style="padding:calc(var(--header-h) + 60px) 0 60px;">
  <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1920&q=80" alt="Modern office" loading="eager">
  <div class="container">
    <nav class="breadcrumb"><a href="<?= base_url('public/') ?>">Home</a><span>/</span><span>Contact</span></nav>
    <h1>Let's Find Your Next Home</h1>
    <p class="mt-1" style="color:rgba(255,255,255,.8); max-width:560px;">Tell us what you're looking for and a dedicated advisor will be in touch within one business day.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="contact-split">

      <!-- FORM -->
      <div>
        <span class="eyebrow">Send a Message</span>
        <h2>How Can We Help?</h2>
        <form class="mt-2" data-ajax-form action="<?= base_url('public/contact') ?>" method="post">
          <?= csrf_field() ?>
          <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
          <input type="hidden" name="source" value="<?= esc($prefSource ?: 'Contact Page', 'attr') ?>">
          <div class="form-alert" data-alert></div>

          <div class="field-row">
            <div class="field"><label>Full Name *</label><input class="input" type="text" name="name" required></div>
            <div class="field"><label>Phone</label><input class="input" type="tel" name="phone"></div>
          </div>
          <div class="field"><label>Email *</label><input class="input" type="email" name="email" required></div>
          <div class="field-row">
            <div class="field">
              <label>I'm interested in</label>
              <select class="select" name="interest">
                <?php foreach (['Buy', 'Sell', 'Rent', 'Invest'] as $opt): ?>
                  <option value="<?= $opt ?>" <?= $prefInterest === $opt ? 'selected' : '' ?>><?= $opt ?>ing</option>
                <?php endforeach ?>
                <?php if ($prefInterest && ! in_array($prefInterest, ['Buy', 'Sell', 'Rent', 'Invest'], true)): ?>
                  <option value="<?= esc($prefInterest, 'attr') ?>" selected><?= esc($prefInterest) ?></option>
                <?php endif ?>
              </select>
            </div>
            <div class="field">
              <label>Preferred Contact Time</label>
              <select class="select" name="preferred_time">
                <option value="">Any time</option>
                <option>Morning (8am–12pm)</option>
                <option>Afternoon (12pm–5pm)</option>
                <option>Evening (5pm–8pm)</option>
              </select>
            </div>
          </div>
          <div class="field"><label>Message</label><textarea class="textarea" name="message" rows="5" placeholder="Tell us about your goals…"><?= $prefSource ? esc('Regarding: ' . $prefSource) : '' ?></textarea></div>
          <button type="submit" class="btn btn--gold btn--lg">Send Message <ion-icon name="paper-plane-outline"></ion-icon></button>
        </form>
      </div>

      <!-- MAP + INFO + AGENTS -->
      <div>
        <div class="map-panel" style="position:static; height:300px; margin-bottom:1.5rem;">
          <iframe title="Vesta office" style="width:100%;height:100%;border:0;" loading="lazy"
            src="https://www.openstreetmap.org/export/embed.html?bbox=-97.755%2C30.258%2C-97.730%2C30.275&layer=mapnik&marker=30.2666%2C-97.7425"></iframe>
        </div>

        <div class="contact-info-row">
          <div class="ic"><ion-icon name="location-outline"></ion-icon></div>
          <div><strong>Visit Us</strong><p>500 Congress Ave, Suite 1200<br>Austin, TX 78701</p></div>
        </div>
        <div class="contact-info-row">
          <div class="ic"><ion-icon name="call-outline"></ion-icon></div>
          <div><strong>Call Us</strong><p><a href="tel:+15125550192">+1 (512) 555-0192</a> · Mon–Sat, 8am–8pm</p></div>
        </div>
        <div class="contact-info-row">
          <div class="ic"><ion-icon name="mail-outline"></ion-icon></div>
          <div><strong>Email Us</strong><p><a href="mailto:hello@vestarealty.com">hello@vestarealty.com</a></p></div>
        </div>

        <h3 class="mt-3" style="font-family:var(--font-body); font-size:1.15rem;">Meet Our Advisors</h3>
        <div class="contact-cards mt-2">
          <?php foreach ($advisors as $a): ?>
            <div class="contact-agent">
              <img src="<?= esc($a['photo'] ?? '') ?>" alt="<?= esc($a['name'] ?? '') ?>" loading="lazy">
              <div>
                <strong><?= esc($a['name'] ?? '') ?></strong>
                <div class="muted" style="font-size:.85rem;"><?= esc($a['title'] ?? 'Advisor') ?></div>
                <div class="flex gap-1" style="gap:.8rem; margin-top:.4rem; font-size:1.2rem;">
                  <a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $a['phone'] ?? '')) ?>" aria-label="Call"><ion-icon name="call-outline"></ion-icon></a>
                  <a href="mailto:<?= esc($a['email'] ?? '') ?>" aria-label="Email"><ion-icon name="mail-outline"></ion-icon></a>
                  <a href="https://wa.me/<?= esc($a['whatsapp'] ?? '') ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><ion-icon name="logo-whatsapp"></ion-icon></a>
                </div>
              </div>
            </div>
          <?php endforeach ?>
        </div>
      </div>

    </div>
  </div>
</section>
<?= $this->endSection() ?>
