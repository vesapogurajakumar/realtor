<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title><?= esc($title ?? 'Admin Login') ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('public/assets/css/app.css') ?>">
</head>
<body>
  <div class="admin-login">
    <div class="admin-login-card">
      <div class="brand" style="font-family:var(--font-head); font-weight:700; font-size:1.6rem; color:var(--navy); margin-bottom:.3rem;">Ves<b style="color:var(--gold);">ta</b></div>
      <p class="muted" style="margin-bottom:1.6rem;">Sign in to manage listings &amp; leads.</p>
      <?php if (session()->getFlashdata('error')): ?>
        <div class="form-alert form-alert--error is-visible"><?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif ?>
      <form action="<?= base_url('public/admin/login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="field">
          <label>Admin Password</label>
          <input class="input" type="password" name="password" autofocus required>
        </div>
        <button class="btn btn--gold btn--block btn--lg">Sign In</button>
      </form>
    </div>
  </div>
</body>
</html>
