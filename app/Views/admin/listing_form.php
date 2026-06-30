<?= $this->extend('admin/layout') ?>

<?= $this->section('admin_content') ?>
<?php
$errors  = session()->getFlashdata('errors') ?? [];
$L       = $listing ?? [];
$isEdit  = ! empty($L);
$action  = $action ?? base_url('public/admin/listings');
$agent   = $L['agent'] ?? [];
$imgText = ! empty($L['images']) ? implode("\n", (array) $L['images']) : '';
$featTxt = ! empty($L['features']) ? implode(', ', (array) $L['features']) : '';
$amenTxt = ! empty($L['amenities']) ? implode(', ', (array) $L['amenities']) : '';

/** Prefill value: submitted value (after validation error) > existing listing > default. */
$v = static fn (string $key, $def = '') => esc(old($key, $L[$key] ?? $def), 'attr');
?>
<div class="admin-topbar">
  <h1 style="font-size:1.8rem;"><?= $isEdit ? 'Edit Listing' : 'Add Listing' ?></h1>
  <a href="<?= base_url('public/admin/listings') ?>" class="btn btn--ghost btn--sm">← Back</a>
</div>

<?php if (! empty($errors)): ?>
  <div class="admin-flash admin-flash--err">
    <strong>Please fix:</strong>
    <ul style="margin:.4rem 0 0 1.2rem;"><?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach ?></ul>
  </div>
<?php endif ?>

<form action="<?= esc($action) ?>" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.05rem; margin-bottom:1.2rem;">Basics</h3>
    <div class="field"><label>Title *</label><input class="input" name="title" value="<?= $v('title') ?>" required></div>
    <div class="admin-grid3">
      <div class="field"><label>Type</label>
        <select class="select" name="type">
          <?php foreach (['Villa', 'Penthouse', 'Estate', 'Townhouse', 'Condo', 'Single Family'] as $t): ?>
            <option <?= old('type', $L['type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="field"><label>Status</label>
        <select class="select" name="status">
          <?php foreach (['For Sale', 'For Rent', 'Sold'] as $s): ?>
            <option <?= old('status', $L['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="field"><label>Price (USD) *</label><input class="input" type="number" name="price" value="<?= $v('price') ?>" required></div>
    </div>
    <label class="check-item" style="margin-top:.5rem;"><input type="checkbox" name="featured" value="1" <?= old('featured', ! empty($L['featured'])) ? 'checked' : '' ?>> Feature on homepage</label>
  </div>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.05rem; margin-bottom:1.2rem;">Specs</h3>
    <div class="admin-grid3">
      <div class="field"><label>Beds</label><input class="input" type="number" name="beds" value="<?= $v('beds', '0') ?>"></div>
      <div class="field"><label>Baths</label><input class="input" type="number" name="baths" value="<?= $v('baths', '0') ?>"></div>
      <div class="field"><label>Sq Ft</label><input class="input" type="number" name="sqft" value="<?= $v('sqft', '0') ?>"></div>
      <div class="field"><label>Lot Size</label><input class="input" name="lot_size" value="<?= $v('lot_size') ?>" placeholder="0.5 acres"></div>
      <div class="field"><label>Year Built</label><input class="input" type="number" name="year_built" value="<?= $v('year_built') ?>"></div>
      <div class="field"><label>Garage</label><input class="input" type="number" name="garage" value="<?= $v('garage', '0') ?>"></div>
      <div class="field"><label>HOA / mo</label><input class="input" type="number" name="hoa" value="<?= $v('hoa', '0') ?>"></div>
      <div class="field"><label>Walk Score</label><input class="input" type="number" name="walk_score" value="<?= $v('walk_score', '0') ?>"></div>
      <div class="field"><label>Transit Score</label><input class="input" type="number" name="transit_score" value="<?= $v('transit_score', '0') ?>"></div>
    </div>
    <div class="field"><label>MLS #</label><input class="input" name="mls" value="<?= $v('mls') ?>"></div>
  </div>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.05rem; margin-bottom:1.2rem;">Location</h3>
    <div class="field"><label>Address</label><input class="input" name="address" value="<?= $v('address') ?>"></div>
    <div class="admin-grid3">
      <div class="field"><label>City *</label><input class="input" name="city" value="<?= $v('city') ?>" required></div>
      <div class="field"><label>State</label><input class="input" name="state" value="<?= $v('state') ?>"></div>
      <div class="field"><label>ZIP</label><input class="input" name="zip" value="<?= $v('zip') ?>"></div>
      <div class="field"><label>Neighborhood</label><input class="input" name="neighborhood" value="<?= $v('neighborhood') ?>"></div>
      <div class="field"><label>Latitude</label><input class="input" name="lat" value="<?= $v('lat') ?>" placeholder="30.2672"></div>
      <div class="field"><label>Longitude</label><input class="input" name="lng" value="<?= $v('lng') ?>" placeholder="-97.7431"></div>
    </div>
    <p class="muted" style="font-size:.82rem;">Tip: lat/lng power the map. Leave blank if unknown.</p>
  </div>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.05rem; margin-bottom:1.2rem;">Media &amp; Details</h3>

    <?php if ($isEdit && ! empty($L['images'])): ?>
      <div class="field">
        <label>Current Photos</label>
        <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
          <?php foreach ($L['images'] as $im): ?>
            <img src="<?= esc($im) ?>" alt="current photo" style="width:90px; height:70px; object-fit:cover; border-radius:8px; border:1px solid var(--line);" loading="lazy">
          <?php endforeach ?>
        </div>
        <p class="muted" style="font-size:.82rem; margin-top:.4rem;">To remove a photo, delete its line from the box below. New uploads are added on top.</p>
      </div>
    <?php endif ?>

    <div class="field">
      <label><?= $isEdit ? 'Add More Photos' : 'Upload Photos (from your computer)' ?></label>
      <input class="input" type="file" name="images_files[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
      <p class="muted" style="font-size:.82rem; margin-top:.4rem;">JPG/PNG/WebP/GIF, up to 8&nbsp;MB each. Large images are auto-resized &amp; compressed. The first photo (uploaded or URL) becomes the cover.</p>
    </div>
    <div class="field"><label>Image URLs / paths (one per line)</label><textarea class="textarea" name="images" rows="4" placeholder="https://images.unsplash.com/..."><?= esc(old('images', $imgText)) ?></textarea></div>
    <div class="admin-grid2">
      <div class="field"><label>Features (comma separated)</label><textarea class="textarea" name="features" rows="2" placeholder="Pool, Smart Home, 3-Car Garage"><?= esc(old('features', $featTxt)) ?></textarea></div>
      <div class="field"><label>Amenities (comma separated)</label><textarea class="textarea" name="amenities" rows="2" placeholder="Pool, Gym, Waterfront"><?= esc(old('amenities', $amenTxt)) ?></textarea></div>
    </div>
    <div class="field"><label>Description</label><textarea class="textarea" name="description" rows="4"><?= esc(old('description', $L['description'] ?? '')) ?></textarea></div>
    <div class="field"><label>Virtual Tour URL</label><input class="input" name="virtual_tour" value="<?= $v('virtual_tour') ?>"></div>
  </div>

  <div class="admin-panel">
    <h3 style="font-family:var(--font-body); font-size:1.05rem; margin-bottom:1.2rem;">Listing Agent</h3>
    <div class="admin-grid2">
      <div class="field"><label>Name</label><input class="input" name="agent_name" value="<?= esc(old('agent_name', $agent['name'] ?? ''), 'attr') ?>"></div>
      <div class="field"><label>Title</label><input class="input" name="agent_title" value="<?= esc(old('agent_title', $agent['title'] ?? ''), 'attr') ?>" placeholder="Listing Advisor"></div>
      <div class="field"><label>Phone</label><input class="input" type="tel" name="agent_phone" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" title="Enter a 10-digit mobile number" value="<?= esc(old('agent_phone', $agent['phone'] ?? ''), 'attr') ?>"></div>
      <div class="field"><label>Email</label><input class="input" type="email" name="agent_email" value="<?= esc(old('agent_email', $agent['email'] ?? ''), 'attr') ?>"></div>
    </div>
    <div class="field"><label>Agent Photo URL</label><input class="input" name="agent_photo" value="<?= esc(old('agent_photo', $agent['photo'] ?? ''), 'attr') ?>"></div>
  </div>

  <button class="btn btn--gold btn--lg"><ion-icon name="checkmark-outline"></ion-icon> <?= $isEdit ? 'Save Changes' : 'Publish Listing' ?></button>
</form>
<?= $this->endSection() ?>
