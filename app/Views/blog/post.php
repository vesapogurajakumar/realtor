<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/**
 * @var array $post
 * @var array<int,array> $related
 * @var array<int,array> $comments
 */
$shareUrl = urlencode(base_url('public/blog/' . $post['slug']));
$shareTxt = urlencode($post['title']);
?>
<article>
  <header class="article-hero">
    <?= img_tag($post['featured_image'], $post['title'], ['width' => 1920, 'widths' => [768, 1280, 1920], 'sizes' => '100vw', 'quality' => 72, 'loading' => 'eager', 'fetchpriority' => 'high']) ?>
    <div class="container">
      <nav class="breadcrumb"><a href="<?= base_url('public/') ?>">Home</a><span>/</span><a href="<?= base_url('public/blog') ?>">Blog</a><span>/</span><span><?= esc($post['category']) ?></span></nav>
      <span class="tag-pill"><?= esc($post['category']) ?></span>
      <h1 class="mt-1"><?= esc($post['title']) ?></h1>
    </div>
  </header>

  <div class="section section--tight">
    <div class="container">
      <div class="article-layout">

        <!-- TOC -->
        <aside class="toc" data-toc>
          <h4>On This Page</h4>
          <nav data-toc-list></nav>
        </aside>

        <div>
          <!-- Author / share bar -->
          <div class="author-bar">
            <img src="<?= esc($post['author']['photo']) ?>" alt="<?= esc($post['author']['name']) ?>">
            <div>
              <strong><?= esc($post['author']['name']) ?></strong>
              <div class="muted" style="font-size:.85rem;"><?= nice_date($post['date']) ?> · <?= (int) $post['read_time'] ?> min read</div>
            </div>
            <div class="share-row">
              <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTxt ?>" target="_blank" rel="noopener" aria-label="Share on X"><ion-icon name="logo-twitter"></ion-icon></a>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><ion-icon name="logo-facebook"></ion-icon></a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><ion-icon name="logo-linkedin"></ion-icon></a>
              <a href="#" data-copy-link aria-label="Copy link"><ion-icon name="link-outline"></ion-icon></a>
            </div>
          </div>

          <!-- Body -->
          <div class="prose" data-article-body>
            <?= $post['content'] // trusted authored HTML from blog.json ?>
          </div>

          <!-- Tags -->
          <hr class="divider">
          <div class="tag-cloud">
            <?php foreach (($post['tags'] ?? []) as $tag): ?>
              <a href="<?= base_url('public/blog') ?>?q=<?= urlencode($tag) ?>">#<?= esc($tag) ?></a>
            <?php endforeach ?>
          </div>

          <!-- Comments -->
          <hr class="divider">
          <section id="comments">
            <h2>Comments <?= ! empty($comments) ? '(' . count($comments) . ')' : '' ?></h2>
            <div class="mt-2">
              <?php if (empty($comments)): ?>
                <p class="muted">Be the first to share your thoughts.</p>
              <?php else: ?>
                <?php foreach ($comments as $c): ?>
                  <div class="comment">
                    <div class="avatar"><?= esc(strtoupper(mb_substr((string) $c['name'], 0, 1))) ?></div>
                    <div>
                      <strong><?= esc($c['name']) ?></strong> <span class="muted" style="font-size:.82rem;">· <?= nice_date(substr((string) $c['created_at'], 0, 10)) ?></span>
                      <p style="margin-top:.3rem;"><?= esc($c['comment']) ?></p>
                    </div>
                  </div>
                <?php endforeach ?>
              <?php endif ?>
            </div>

            <h3 class="mt-3">Leave a Comment</h3>
            <form class="mt-2" data-ajax-form action="<?= base_url('public/blog/comment') ?>" method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="post_slug" value="<?= esc($post['slug'], 'attr') ?>">
              <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div class="form-alert" data-alert></div>
              <div class="field-row">
                <div class="field"><label>Name</label><input class="input" type="text" name="name" required></div>
                <div class="field"><label>Email (not published)</label><input class="input" type="email" name="email" required></div>
              </div>
              <div class="field"><label>Comment</label><textarea class="textarea" name="comment" rows="4" required></textarea></div>
              <button class="btn btn--navy">Post Comment</button>
            </form>
          </section>
        </div>
      </div>
    </div>
  </div>
</article>

<!-- Related -->
<?php if (! empty($related)): ?>
<section class="section section--gray">
  <div class="container">
    <div class="section-head"><span class="eyebrow">Keep Reading</span><h2>Related Articles</h2></div>
    <div class="card-grid">
      <?php foreach ($related as $rp): ?>
        <article class="post-card" data-aos="fade-up">
          <a class="post-media" href="<?= base_url('public/blog/' . $rp['slug']) ?>"><?= img_tag($rp['featured_image'], $rp['title'], ['width' => 700, 'widths' => [400, 600, 800], 'sizes' => '(max-width: 600px) 92vw, (max-width: 1000px) 46vw, 380px']) ?></a>
          <div class="post-body">
            <span class="tag-pill"><?= esc($rp['category']) ?></span>
            <h3><a href="<?= base_url('public/blog/' . $rp['slug']) ?>"><?= esc($rp['title']) ?></a></h3>
            <p class="muted"><?= esc(reading_excerpt($rp['excerpt'], 90)) ?></p>
          </div>
        </article>
      <?php endforeach ?>
    </div>
  </div>
</section>
<?php endif ?>

<!-- Mid-article lead magnet template (injected by JS) -->
<template data-lead-magnet>
  <div class="lead-magnet">
    <span class="eyebrow" style="color:var(--gold);">Free Download</span>
    <h3>Get Our Free Buyer's Guide</h3>
    <p style="color:rgba(255,255,255,.78); margin:.5rem 0 1.2rem;">Everything you need to know before making an offer — from pre-approval to closing.</p>
    <form data-ajax-form action="<?= base_url('public/subscribe') ?>" method="post" style="display:flex; gap:.6rem; flex-wrap:wrap;">
      <?= csrf_field() ?>
      <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
      <input class="input" type="email" name="email" placeholder="Your email" required style="flex:1; min-width:220px;">
      <button class="btn btn--gold">Send Me the Guide</button>
      <div class="form-alert" data-alert style="flex-basis:100%;"></div>
    </form>
  </div>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  window.addEventListener('load', function () {
    var body = document.querySelector('[data-article-body]');
    var tocList = document.querySelector('[data-toc-list]');
    if (body && tocList) {
      var heads = body.querySelectorAll('h2, h3');
      if (!heads.length) { document.querySelector('[data-toc]').style.display = 'none'; }
      heads.forEach(function (h, i) {
        var id = 'sec-' + i + '-' + h.textContent.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        h.id = id;
        var a = document.createElement('a');
        a.href = '#' + id; a.textContent = h.textContent;
        a.style.paddingLeft = h.tagName === 'H3' ? '1.6rem' : '.9rem';
        tocList.appendChild(a);
      });
      // scrollspy
      var links = tocList.querySelectorAll('a');
      if ('IntersectionObserver' in window) {
        var spy = new IntersectionObserver(function (entries) {
          entries.forEach(function (e) {
            if (e.isIntersecting) {
              links.forEach(function (l) { l.classList.toggle('is-active', l.getAttribute('href') === '#' + e.target.id); });
            }
          });
        }, { rootMargin: '-100px 0px -70% 0px' });
        heads.forEach(function (h) { spy.observe(h); });
      }
    }

    // Inject mid-article lead magnet after the 2nd H2
    var tpl = document.querySelector('[data-lead-magnet]');
    if (body && tpl) {
      var h2s = body.querySelectorAll('h2');
      var anchor = h2s[1] || h2s[0];
      if (anchor) anchor.parentNode.insertBefore(tpl.content.cloneNode(true), anchor);
      document.querySelectorAll('[data-lead-magnet] ~ * [data-ajax-form], .lead-magnet [data-ajax-form]').forEach(function (f) {
        if (window.VestaForm) window.VestaForm.bind(f);
      });
    }

    // Copy link
    var copy = document.querySelector('[data-copy-link]');
    if (copy) copy.addEventListener('click', function (e) {
      e.preventDefault();
      navigator.clipboard && navigator.clipboard.writeText(location.href);
      copy.innerHTML = '<ion-icon name="checkmark-outline"></ion-icon>';
      setTimeout(function () { copy.innerHTML = '<ion-icon name="link-outline"></ion-icon>'; }, 1500);
    });
  });
</script>
<?= $this->endSection() ?>
