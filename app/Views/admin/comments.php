<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<?php
/** @var array<int,array> $rows */
$pending = array_filter($rows, static fn ($r) => (int) ($r['is_approved'] ?? 0) === 0);
?>
<div class="admin-topbar">
  <h1 style="font-size:1.8rem;">Comments <span class="muted" style="font-size:1rem;">(<?= count($pending) ?> pending)</span></h1>
</div>

<div class="admin-panel">
  <?php if (empty($rows)): ?>
    <div style="text-align:center; padding:3rem 1rem;">
      <ion-icon name="chatbubbles-outline" style="font-size:3rem; color:var(--gray-300);"></ion-icon>
      <h3 class="mt-1" style="font-family:var(--font-body);">No comments yet</h3>
      <p class="muted">Comments submitted on blog posts will appear here for moderation.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto;">
      <table class="admin-table">
        <thead><tr><th>Author</th><th>Comment</th><th>Post</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
          <?php foreach ($rows as $c): ?>
            <?php $approved = (int) ($c['is_approved'] ?? 0) === 1; ?>
            <tr>
              <td>
                <strong><?= esc($c['name'] ?? '') ?></strong><br>
                <span class="muted" style="font-size:.8rem;"><?= esc($c['email'] ?? '') ?></span><br>
                <span class="muted" style="font-size:.78rem;"><?= esc($c['created_at'] ?? '') ?></span>
              </td>
              <td style="max-width:360px;"><?= esc($c['comment'] ?? '') ?></td>
              <td><a href="<?= base_url('public/blog/' . ($c['post_slug'] ?? '')) ?>" target="_blank"><?= esc($c['post_slug'] ?? '') ?></a></td>
              <td>
                <?= $approved
                    ? '<span style="color:#1e6b3a; font-weight:600;">Approved</span>'
                    : '<span style="color:#a4271b; font-weight:600;">Pending</span>' ?>
              </td>
              <td style="text-align:right; white-space:nowrap;">
                <?php if (! $approved): ?>
                  <form action="<?= base_url('public/admin/comments/' . $c['id'] . '/approve') ?>" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <button class="btn btn--gold btn--sm">Approve</button>
                  </form>
                <?php else: ?>
                  <form action="<?= base_url('public/admin/comments/' . $c['id'] . '/unapprove') ?>" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <button class="btn btn--ghost btn--sm">Hide</button>
                  </form>
                <?php endif ?>
                <form action="<?= base_url('public/admin/comments/' . $c['id'] . '/delete') ?>" method="post" style="display:inline;" onsubmit="return confirm('Delete this comment?');">
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
