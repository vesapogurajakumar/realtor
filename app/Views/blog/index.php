<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array|null $featured
 * @var array<int,array> $posts
 * @var array<int,string> $categories
 * @var string $activeCategory
 * @var string $search
 * @var array<int,array> $popular
 * @var array<int,string> $tags
 */
$blogUrl = base_url('public/blog');
?>
<section class="page-hero">
  <?= img_tag('https://images.unsplash.com/photo-1554469384-e58fac16e23a', 'Real estate insights', ['width' => 1920, 'widths' => [768, 1280, 1920], 'sizes' => '100vw', 'quality' => 72, 'loading' => 'eager', 'fetchpriority' => 'high']) ?>
  <div class="container">
    <nav class="breadcrumb"><a href="<?= base_url('public/') ?>">Home</a><span>/</span><span>Blog</span></nav>
    <h1>Insights &amp; Market Intelligence</h1>
    <p class="mt-1" style="color:rgba(255,255,255,.8); max-width:620px;">Expert guidance on buying, selling, investing and the neighborhoods we love.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">

    <div class="blog-tabs">
      <a href="<?= $blogUrl ?>" class="blog-tab <?= $activeCategory === '' ? 'is-active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= $blogUrl ?>?category=<?= urlencode($cat) ?>" class="blog-tab <?= $activeCategory === $cat ? 'is-active' : '' ?>"><?= esc($cat) ?></a>
      <?php endforeach ?>
    </div>

    <div class="blog-layout">
      <div>
        <?php if ($featured): ?>
          <a class="featured-post" href="<?= base_url('public/blog/' . $featured['slug']) ?>" data-aos="fade-up">
            <?= img_tag($featured['featured_image'], $featured['title'], ['width' => 1200, 'widths' => [600, 900, 1200, 1600], 'sizes' => '(max-width: 980px) 100vw, 66vw', 'loading' => 'eager']) ?>
            <div class="fp-body">
              <span class="tag-pill"><?= esc($featured['category']) ?></span>
              <h2 class="mt-1"><?= esc($featured['title']) ?></h2>
              <p style="color:rgba(255,255,255,.82); margin-top:.6rem;"><?= esc(reading_excerpt($featured['excerpt'], 150)) ?></p>
              <div class="post-meta" style="border:0; color:rgba(255,255,255,.7);">
                <img src="<?= esc($featured['author']['photo']) ?>" alt="<?= esc($featured['author']['name']) ?>">
                <span><?= esc($featured['author']['name']) ?></span><span>·</span>
                <span><?= nice_date($featured['date']) ?></span><span>·</span>
                <span><?= (int) $featured['read_time'] ?> min read</span>
              </div>
            </div>
          </a>
        <?php endif ?>

        <?php if ($search !== ''): ?>
          <p class="muted mb-0" style="margin-bottom:1.5rem;">Showing results for “<?= esc($search) ?>” — <?= count($posts) ?> found.</p>
        <?php endif ?>

        <?php if (empty($posts) && ! $featured): ?>
          <div class="listings-empty"><ion-icon name="newspaper-outline" style="font-size:3rem;"></ion-icon><h3 class="mt-1">No articles found</h3><p>Try another category or search term.</p></div>
        <?php else: ?>
          <div class="card-grid">
            <?php foreach ($posts as $post): ?>
              <article class="post-card" data-aos="fade-up">
                <a class="post-media" href="<?= base_url('public/blog/' . $post['slug']) ?>">
                  <?= img_tag($post['featured_image'], $post['title'], ['width' => 700, 'widths' => [400, 600, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 980px) 60vw, 380px']) ?>
                </a>
                <div class="post-body">
                  <span class="tag-pill"><?= esc($post['category']) ?></span>
                  <h3><a href="<?= base_url('public/blog/' . $post['slug']) ?>"><?= esc($post['title']) ?></a></h3>
                  <p class="muted"><?= esc(reading_excerpt($post['excerpt'], 110)) ?></p>
                  <div class="post-meta">
                    <img src="<?= esc($post['author']['photo']) ?>" alt="<?= esc($post['author']['name']) ?>">
                    <span><?= esc($post['author']['name']) ?></span><span>·</span>
                    <span><?= nice_date($post['date']) ?></span><span>·</span>
                    <span><?= (int) $post['read_time'] ?> min</span>
                  </div>
                </div>
              </article>
            <?php endforeach ?>
          </div>
        <?php endif ?>
      </div>

      <!-- ============ SIDEBAR ============ -->
      <aside>
        <div class="sidebar-widget">
          <h4>Search Articles</h4>
          <form action="<?= $blogUrl ?>" method="get" style="display:flex; gap:.5rem;">
            <input class="input" type="search" name="q" value="<?= esc($search, 'attr') ?>" placeholder="Search…">
            <button class="btn btn--navy" aria-label="Search"><ion-icon name="search-outline"></ion-icon></button>
          </form>
        </div>

        <div class="sidebar-widget">
          <h4>Popular Posts</h4>
          <?php foreach ($popular as $pop): ?>
            <a class="popular-item" href="<?= base_url('public/blog/' . $pop['slug']) ?>">
              <?= img_tag($pop['featured_image'], $pop['title'], ['width' => 160, 'widths' => [120, 160], 'sizes' => '70px']) ?>
              <div><h5><?= esc($pop['title']) ?></h5><span><?= nice_date($pop['date']) ?></span></div>
            </a>
          <?php endforeach ?>
        </div>

        <div class="sidebar-widget">
          <h4>Categories</h4>
          <ul class="cat-list">
            <?php foreach ($categories as $cat): ?>
              <li><a href="<?= $blogUrl ?>?category=<?= urlencode($cat) ?>"><?= esc($cat) ?></a></li>
            <?php endforeach ?>
          </ul>
        </div>

        <div class="sidebar-widget">
          <h4>Tags</h4>
          <div class="tag-cloud">
            <?php foreach ($tags as $tag): ?>
              <a href="<?= $blogUrl ?>?q=<?= urlencode($tag) ?>"><?= esc($tag) ?></a>
            <?php endforeach ?>
          </div>
        </div>

        <div class="sidebar-widget" style="background:var(--navy); color:#fff;">
          <h4 style="color:#fff;">Newsletter</h4>
          <p style="font-size:.9rem; color:rgba(255,255,255,.75); margin-bottom:1rem;">Get market insights in your inbox monthly.</p>
          <form data-ajax-form action="<?= base_url('public/subscribe') ?>" method="post">
            <?= csrf_field() ?>
            <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
            <div class="form-alert" data-alert></div>
            <div class="field"><input class="input" type="email" name="email" placeholder="Your email" required></div>
            <button class="btn btn--gold btn--block">Subscribe</button>
          </form>
        </div>
      </aside>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
