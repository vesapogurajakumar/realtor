<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<?php
/**
 * @var array<int,array> $rows
 * @var int $total
 * @var int $page
 * @var int $pages
 * @var callable $pageUrl
 */
?>
<div class="admin-topbar">
  <h1 style="font-size:1.8rem;">Leads <span class="muted" style="font-size:1rem;">(<?= (int) $total ?> total)</span></h1>
  <a href="<?= base_url('public/admin/leads/export') ?>" class="btn btn--gold btn--sm"><ion-icon name="download-outline"></ion-icon> Export CSV</a>
</div>

<div class="admin-panel">
  <?php if (empty($rows)): ?>
    <div style="text-align:center; padding:3rem 1rem;">
      <ion-icon name="people-outline" style="font-size:3rem; color:var(--gray-300);"></ion-icon>
      <h3 class="mt-1" style="font-family:var(--font-body);">No leads yet</h3>
      <p class="muted">Submissions from the contact, valuation and property forms will appear here.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Interest</th><th>Source</th><th>Message</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $l): ?>
            <tr>
              <td><strong><?= esc($l['name'] ?? '') ?></strong></td>
              <td><a href="mailto:<?= esc($l['email'] ?? '') ?>"><?= esc($l['email'] ?? '') ?></a></td>
              <td><?= esc($l['phone'] ?? '') ?></td>
              <td><?= esc($l['interest'] ?? '') ?></td>
              <td><?= esc($l['source'] ?? '') ?></td>
              <td style="max-width:280px;"><span class="muted" style="font-size:.85rem;"><?= esc(reading_excerpt((string) ($l['message'] ?? ''), 80)) ?></span></td>
              <td style="white-space:nowrap;"><?= esc($l['created_at'] ?? '') ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="pager" aria-label="Leads pagination">
        <a class="pager-btn <?= $page <= 1 ? '' : '' ?>" href="<?= $pageUrl(max(1, $page - 1)) ?>" <?= $page <= 1 ? 'aria-disabled="true" style="pointer-events:none;opacity:.4;"' : '' ?>>‹</a>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a class="pager-btn <?= $i === $page ? 'is-active' : '' ?>" href="<?= $pageUrl($i) ?>"><?= $i ?></a>
        <?php endfor ?>
        <a class="pager-btn" href="<?= $pageUrl(min($pages, $page + 1)) ?>" <?= $page >= $pages ? 'aria-disabled="true" style="pointer-events:none;opacity:.4;"' : '' ?>>›</a>
      </nav>
    <?php endif ?>
  <?php endif ?>
</div>
<?= $this->endSection() ?>
