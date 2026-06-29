<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<?php
/**
 * @var array $stats
 * @var array<int,array> $leads
 * @var array<int,array> $subscribers
 * @var array<int,array> $comments
 */
?>
<div class="admin-topbar">
  <h1 style="font-size:1.8rem;">Dashboard</h1>
  <a href="<?= base_url('public/admin/listings/new') ?>" class="btn btn--gold btn--sm"><ion-icon name="add-outline"></ion-icon> Add Listing</a>
</div>

<div class="admin-cards">
  <div class="admin-card"><div class="n"><?= (int) $stats['listings'] ?></div><div class="l">DB Listings</div></div>
  <div class="admin-card"><div class="n"><?= (int) $stats['leads'] ?></div><div class="l">Leads</div></div>
  <div class="admin-card"><div class="n"><?= (int) $stats['subscribers'] ?></div><div class="l">Subscribers</div></div>
  <div class="admin-card"><div class="n"><?= (int) $stats['comments'] ?></div><div class="l">Comments</div></div>
</div>

<div class="admin-panel">
  <div class="flex items-center" style="justify-content:space-between; margin-bottom:1rem;">
    <h3 style="font-family:var(--font-body); font-size:1.1rem;">Recent Leads</h3>
    <a href="<?= base_url('public/admin/leads') ?>" class="btn btn--ghost btn--sm">View all &amp; export →</a>
  </div>
  <?php if (empty($leads)): ?>
    <p class="muted">No leads captured yet (or DB not connected).</p>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Interest</th><th>Source</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($leads as $l): ?>
            <tr>
              <td><?= esc($l['name'] ?? '') ?></td>
              <td><a href="mailto:<?= esc($l['email'] ?? '') ?>"><?= esc($l['email'] ?? '') ?></a></td>
              <td><?= esc($l['phone'] ?? '') ?></td>
              <td><?= esc($l['interest'] ?? '') ?></td>
              <td><?= esc($l['source'] ?? '') ?></td>
              <td><?= esc($l['created_at'] ?? '') ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>
</div>

<div class="admin-grid2">
  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.1rem; margin-bottom:1rem;">Recent Subscribers</h3>
    <?php if (empty($subscribers)): ?>
      <p class="muted">None yet.</p>
    <?php else: ?>
      <table class="admin-table"><tbody>
        <?php foreach ($subscribers as $s): ?>
          <tr><td><?= esc($s['email'] ?? '') ?></td><td style="text-align:right;" class="muted"><?= esc($s['subscribed_at'] ?? '') ?></td></tr>
        <?php endforeach ?>
      </tbody></table>
    <?php endif ?>
  </div>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.1rem; margin-bottom:1rem;">Recent Comments</h3>
    <?php if (empty($comments)): ?>
      <p class="muted">None yet.</p>
    <?php else: ?>
      <table class="admin-table"><tbody>
        <?php foreach ($comments as $c): ?>
          <tr>
            <td><strong><?= esc($c['name'] ?? '') ?></strong><br><span class="muted" style="font-size:.82rem;"><?= esc(reading_excerpt((string) ($c['comment'] ?? ''), 70)) ?></span></td>
            <td style="text-align:right; white-space:nowrap;">
              <?php if ((int) ($c['is_approved'] ?? 0) === 1): ?>
                <span style="color:#1e6b3a; font-weight:600;">Approved</span>
              <?php else: ?>
                <form action="<?= base_url('public/admin/comments/' . $c['id'] . '/approve') ?>" method="post" style="display:inline;">
                  <?= csrf_field() ?>
                  <button class="btn btn--gold btn--sm">Approve</button>
                </form>
              <?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody></table>
      <p class="muted mt-1" style="font-size:.82rem;"><a href="<?= base_url('public/admin/comments') ?>">Manage all comments →</a></p>
    <?php endif ?>
  </div>
</div>
<?= $this->endSection() ?>
