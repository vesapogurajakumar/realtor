<?php
/** @var string $active */
$active = $active ?? '';
$nav    = [
    'dashboard' => ['Dashboard', 'grid-outline', base_url('public/admin')],
    'listings'  => ['Listings', 'business-outline', base_url('public/admin/listings')],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title><?= esc($title ?? 'Vesta Admin') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('public/assets/css/app.css') ?>">
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
</head>
<body>
  <div class="admin-shell">
    <aside class="admin-side">
      <a href="<?= base_url('public/admin') ?>" class="brand" style="color:#fff;">Ves<b style="color:var(--gold);">ta</b> <span style="font-size:.7rem; opacity:.6;">ADMIN</span></a>
      <nav>
        <?php foreach ($nav as $key => $item): ?>
          <a href="<?= $item[2] ?>" class="<?= $active === $key ? 'is-active' : '' ?>"><ion-icon name="<?= $item[1] ?>"></ion-icon> <?= $item[0] ?></a>
        <?php endforeach ?>
        <a href="<?= base_url('public/admin/listings/new') ?>"><ion-icon name="add-circle-outline"></ion-icon> Add Listing</a>
        <a href="<?= base_url('public/') ?>" target="_blank"><ion-icon name="open-outline"></ion-icon> View Site</a>
        <a href="<?= base_url('public/admin/logout') ?>"><ion-icon name="log-out-outline"></ion-icon> Sign Out</a>
      </nav>
    </aside>
    <main class="admin-main">
      <?php if (session()->getFlashdata('message')): ?>
        <div class="admin-flash admin-flash--ok"><?= esc(session()->getFlashdata('message')) ?></div>
      <?php endif ?>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="admin-flash admin-flash--err"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif ?>
      <?= $this->renderSection('admin_content') ?>
    </main>
  </div>
</body>
</html>
