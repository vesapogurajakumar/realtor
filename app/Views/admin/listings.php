<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<?php /** @var array<int,array> $rows */ ?>
<div class="admin-topbar">
  <h1 style="font-size:1.8rem;">Listings <span class="muted" style="font-size:1rem;">(<?= count($rows) ?> in database)</span></h1>
  <a href="<?= base_url('public/admin/listings/new') ?>" class="btn btn--gold btn--sm"><ion-icon name="add-outline"></ion-icon> Add Listing</a>
</div>

<div class="admin-panel">
  <?php if (empty($rows)): ?>
    <div style="text-align:center; padding:3rem 1rem;">
      <ion-icon name="business-outline" style="font-size:3rem; color:var(--gray-300);"></ion-icon>
      <h3 class="mt-1" style="font-family:var(--font-body);">No database listings yet</h3>
      <p class="muted">The 12 sample listings come from the JSON file. Add your own here — they'll appear on the site instantly.</p>
      <a href="<?= base_url('public/admin/listings/new') ?>" class="btn btn--navy mt-2">Add Your First Listing</a>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Price</th><th>Location</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><strong><?= esc($r['title']) ?></strong></td>
              <td><?= esc($r['type']) ?></td>
              <td><?= esc($r['status']) ?></td>
              <td><?= money($r['price']) ?></td>
              <td><?= esc(trim(($r['city'] ?? '') . ', ' . ($r['state'] ?? ''), ', ')) ?></td>
              <td style="text-align:right; white-space:nowrap;">
                <a href="<?= property_url($r['code']) ?>" target="_blank" class="btn btn--ghost btn--sm">View</a>
                <a href="<?= base_url('public/admin/listings/' . $r['id'] . '/edit') ?>" class="btn btn--ghost btn--sm">Edit</a>
                <form action="<?= base_url('public/admin/listings/' . $r['id'] . '/delete') ?>" method="post" style="display:inline;" onsubmit="return confirm('Delete this listing?');">
                  <?= csrf_field() ?>
                  <button class="btn btn--ghost btn--sm" style="color:#a4271b; border-color:#f3c0bb;">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>
</div>
<?= $this->endSection() ?>
