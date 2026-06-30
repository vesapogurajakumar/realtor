<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array<int, array<string, mixed>> $featured
 * @var array<int, array<string, mixed>> $neighborhoods
 * @var array<int, array<string, mixed>> $posts
 * @var array<int, string> $cities
 * @var array<int, string> $types
 */
?>

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="hero-media">
    <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1920&q=80" alt="Luxury home exterior at dusk" fetchpriority="high">
  </div>
  <div class="container">
    <div class="hero-inner">
      <div class="hero-rating">
        <span class="stars">★★★★★</span>
        <span>Rated 4.9/5 by 1,200+ happy clients</span>
      </div>
      <h1>Find Your Dream Home</h1>
      <p class="lead">From waterfront villas to skyline penthouses — discover handpicked luxury listings and work with advisors who close.</p>

      <form class="search-card" action="<?= base_url('public/listings') ?>" method="get" data-aos="fade-up" data-aos-delay="150">
        <div class="search-tabs" data-search-tabs>
          <button type="button" class="search-tab is-active" data-status="">Buy</button>
          <button type="button" class="search-tab" data-status="For Rent">Rent</button>
          <button type="button" class="search-tab" data-status="Sold">Sold</button>
        </div>
        <input type="hidden" name="status" value="" data-status-input>
        <div class="search-fields">
          <div class="search-field">
            <label for="hero-q">Location</label>
            <input type="text" id="hero-q" name="q" autocomplete="off" list="city-list"
                   data-placeholder-cycle='["Search by city…","Search by ZIP…","Search by neighborhood…"]'>
            <datalist id="city-list">
              <?php foreach ($cities as $c): ?><option value="<?= esc($c, 'attr') ?>"><?php endforeach ?>
            </datalist>
          </div>
          <div class="search-field">
            <label for="hero-type">Property Type</label>
            <select id="hero-type" name="type">
              <option value="">Any type</option>
              <?php foreach ($types as $t): ?><option value="<?= esc($t, 'attr') ?>"><?= esc($t) ?></option><?php endforeach ?>
            </select>
          </div>
          <div class="search-field">
            <label for="hero-price">Max Price</label>
            <select id="hero-price" name="max_price">
              <option value="">No max</option>
              <option value="1000000">$1M</option>
              <option value="2500000">$2.5M</option>
              <option value="5000000">$5M</option>
              <option value="10000000">$10M</option>
            </select>
          </div>
          <button type="submit" class="btn btn--gold btn--lg"><ion-icon name="search-outline"></ion-icon> Search</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- ============ FEATURED ============ -->
<section class="section">
  <div class="container">
    <div class="flex items-center" style="justify-content:space-between; flex-wrap:wrap; gap:1rem;">
      <div class="section-head" style="margin-bottom:0;">
        <span class="eyebrow">Handpicked</span>
        <h2>Featured Properties</h2>
      </div>
      <a href="<?= base_url('public/listings') ?>" class="btn btn--ghost">View All Listings <ion-icon name="arrow-forward-outline"></ion-icon></a>
    </div>

    <div class="swiper featured-swiper mt-3" data-aos="fade-up">
      <div class="swiper-wrapper">
        <?php foreach ($featured as $property): ?>
          <div class="swiper-slide" style="height:auto;">
            <?= view('partials/property_card', ['property' => $property, 'aos' => false]) ?>
          </div>
        <?php endforeach ?>
      </div>
      <div class="swiper-pagination featured-pagination" style="position:static; margin-top:1.6rem;"></div>
    </div>
  </div>
</section>

<!-- ============ STATS ============ -->
<section class="section section--navy section--tight">
  <div class="container">
    <div class="stats-grid">
      <div data-aos="fade-up">
        <div class="stat-value" data-count="2400" data-suffix="+">0</div>
        <div class="stat-label">Active Listings</div>
      </div>
      <div data-aos="fade-up" data-aos-delay="100">
        <div class="stat-value" data-count="15" data-suffix=" yrs">0</div>
        <div class="stat-label">Years of Experience</div>
      </div>
      <div data-aos="fade-up" data-aos-delay="200">
        <div class="stat-value" data-count="98" data-suffix="%">0</div>
        <div class="stat-label">Client Satisfaction</div>
      </div>
      <div data-aos="fade-up" data-aos-delay="300">
        <div class="stat-value" data-count="500" data-suffix="+">0</div>
        <div class="stat-label">Homes Sold This Year</div>
      </div>
    </div>
  </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="section section--gray">
  <div class="container">
    <div class="section-head section-head--center">
      <span class="eyebrow">Simple Process</span>
      <h2>How It Works</h2>
      <p>Three considered steps between you and the keys to your next home.</p>
    </div>
    <div class="steps">
      <div class="step" data-aos="fade-up">
        <span class="step-num">01</span>
        <div class="step-icon"><ion-icon name="search-outline"></ion-icon></div>
        <h3>Search</h3>
        <p>Browse curated listings with rich media, maps and neighborhood insights tailored to your lifestyle.</p>
      </div>
      <div class="step" data-aos="fade-up" data-aos-delay="120">
        <span class="step-num">02</span>
        <div class="step-icon"><ion-icon name="calendar-outline"></ion-icon></div>
        <h3>Tour</h3>
        <p>Schedule private or virtual tours in a tap, and get expert guidance from a dedicated advisor.</p>
      </div>
      <div class="step" data-aos="fade-up" data-aos-delay="240">
        <span class="step-num">03</span>
        <div class="step-icon"><ion-icon name="key-outline"></ion-icon></div>
        <h3>Move In</h3>
        <p>We handle offers, negotiation and closing — so you can focus on settling into your new home.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ NEIGHBORHOODS ============ -->
<section class="section">
  <div class="container">
    <div class="section-head section-head--center">
      <span class="eyebrow">Explore</span>
      <h2>Featured Neighborhoods</h2>
      <p>Discover the communities our clients love most.</p>
    </div>
    <div class="hood-grid">
      <?php foreach ($neighborhoods as $i => $hood): ?>
        <a class="hood-card" data-aos="zoom-in" data-aos-delay="<?= ($i % 3) * 100 ?>"
           href="<?= base_url('public/listings?city=' . urlencode($hood['city'])) ?>">
          <img src="<?= esc($hood['image']) ?>" alt="<?= esc($hood['name']) ?>, <?= esc($hood['city']) ?>" loading="lazy">
          <div class="hood-body">
            <span class="hood-count"><?= esc($hood['city']) ?>, <?= esc($hood['state']) ?></span>
            <h3><?= esc($hood['name']) ?></h3>
            <p><?= esc($hood['tagline']) ?></p>
          </div>
        </a>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="section section--navy">
  <div class="container">
    <div class="section-head section-head--center">
      <span class="eyebrow">Loved by Clients</span>
      <h2>What Our Clients Say</h2>
    </div>
    <div class="swiper testimonial-swiper" data-aos="fade-up">
      <div class="swiper-wrapper">
        <?php
        $reviews = [
            ['q' => 'Vesta found us a waterfront home we didn\'t even know existed. The process was seamless and the negotiation saved us six figures.', 'n' => 'Jennifer & Paul R.', 't' => 'Bought in Austin, TX', 'p' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=120&q=80'],
            ['q' => 'Sold our penthouse above asking in nine days. The marketing and photography were on another level entirely.', 'n' => 'Marcus D.', 't' => 'Sold in Miami, FL', 'p' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?auto=format&fit=crop&w=120&q=80'],
            ['q' => 'As first-time buyers we felt guided every step of the way. Honest, responsive and genuinely on our side.', 'n' => 'Aisha K.', 't' => 'Bought in Seattle, WA', 'p' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=120&q=80'],
            ['q' => 'The market insight was unreal. We timed our sale perfectly thanks to their data-driven advice.', 'n' => 'The Hendersons', 't' => 'Sold in Aspen, CO', 'p' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=120&q=80'],
        ];
        foreach ($reviews as $r): ?>
          <div class="swiper-slide" style="height:auto;">
            <figure class="testimonial">
              <div class="stars">★★★★★</div>
              <blockquote>“<?= esc($r['q']) ?>”</blockquote>
              <figcaption class="testimonial-author">
                <img src="<?= esc($r['p']) ?>" alt="<?= esc($r['n']) ?>" loading="lazy">
                <span><strong><?= esc($r['n']) ?></strong><span><?= esc($r['t']) ?></span></span>
              </figcaption>
            </figure>
          </div>
        <?php endforeach ?>
      </div>
    </div>
  </div>
</section>

<!-- ============ BLOG PREVIEW ============ -->
<section class="section section--gray">
  <div class="container">
    <div class="flex items-center" style="justify-content:space-between; flex-wrap:wrap; gap:1rem;">
      <div class="section-head" style="margin-bottom:0;">
        <span class="eyebrow">Insights</span>
        <h2>From Our Blog</h2>
      </div>
      <a href="<?= base_url('public/blog') ?>" class="btn btn--ghost">All Articles <ion-icon name="arrow-forward-outline"></ion-icon></a>
    </div>
    <div class="card-grid mt-3">
      <?php foreach ($posts as $post): ?>
        <article class="post-card" data-aos="fade-up">
          <a class="post-media" href="<?= base_url('public/blog/' . $post['slug']) ?>">
            <img src="<?= esc($post['featured_image']) ?>" alt="<?= esc($post['title']) ?>" loading="lazy">
          </a>
          <div class="post-body">
            <span class="tag-pill"><?= esc($post['category']) ?></span>
            <h3><a href="<?= base_url('public/blog/' . $post['slug']) ?>"><?= esc($post['title']) ?></a></h3>
            <p class="muted"><?= esc(reading_excerpt($post['excerpt'], 110)) ?></p>
            <div class="post-meta">
              <img src="<?= esc($post['author']['photo']) ?>" alt="<?= esc($post['author']['name']) ?>" loading="lazy">
              <span><?= esc($post['author']['name']) ?></span>
              <span>·</span>
              <span><?= nice_date($post['date']) ?></span>
              <span>·</span>
              <span><?= (int) $post['read_time'] ?> min read</span>
            </div>
          </div>
        </article>
      <?php endforeach ?>
    </div>
  </div>
</section>

<!-- ============ LEAD CAPTURE ============ -->
<section class="section section--navy">
  <div class="container">
    <div class="cta-split">
      <div data-aos="fade-right">
        <span class="eyebrow">Free Valuation</span>
        <h2>Get a Free Property Valuation</h2>
        <p class="mt-1">Curious what your home is worth in today's market? Our advisors will prepare a complimentary, data-backed valuation — no obligation.</p>
        <ul class="mt-2" style="display:grid; gap:.8rem;">
          <li class="flex items-center gap-1"><ion-icon name="checkmark-circle" style="color:var(--gold); font-size:1.4rem;"></ion-icon> Accurate, comparable-based pricing</li>
          <li class="flex items-center gap-1"><ion-icon name="checkmark-circle" style="color:var(--gold); font-size:1.4rem;"></ion-icon> Neighborhood demand & trend report</li>
          <li class="flex items-center gap-1"><ion-icon name="checkmark-circle" style="color:var(--gold); font-size:1.4rem;"></ion-icon> A tailored selling strategy</li>
        </ul>
      </div>
      <div data-aos="fade-left">
        <form data-ajax-form action="<?= base_url('public/contact') ?>" method="post" style="background:var(--navy-700); padding:2.2rem; border-radius:var(--radius-lg);">
          <?= csrf_field() ?>
          <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
          <input type="hidden" name="interest" value="Sell">
          <input type="hidden" name="source" value="Home Valuation CTA">
          <div class="form-alert" data-alert></div>
          <div class="field-row">
            <div class="field"><label>Full Name</label><input class="input" type="text" name="name" required></div>
            <div class="field"><label>Phone</label><input class="input" type="tel" name="phone" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Enter a 10-digit mobile number" required></div>
          </div>
          <div class="field"><label>Email</label><input class="input" type="email" name="email" required></div>
          <div class="field"><label>Property Address / Message</label><textarea class="textarea" name="message" rows="3" placeholder="Tell us about your property…"></textarea></div>
          <button type="submit" class="btn btn--gold btn--block btn--lg">Get My Free Valuation</button>
        </form>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.addEventListener('load', function () {
    if (!window.Swiper) return;
    new Swiper('.featured-swiper', {
      slidesPerView: 1.1, spaceBetween: 24, grabCursor: true,
      pagination: { el: '.featured-pagination', clickable: true },
      breakpoints: { 600: { slidesPerView: 2 }, 1000: { slidesPerView: 3 } }
    });
    new Swiper('.testimonial-swiper', {
      slidesPerView: 1, spaceBetween: 24, grabCursor: true, autoplay: { delay: 5000 }, loop: true,
      breakpoints: { 700: { slidesPerView: 2 }, 1100: { slidesPerView: 3 } }
    });
  });
</script>
<?= $this->endSection() ?>
