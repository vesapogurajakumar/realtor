<footer class="site-footer">
  <div class="container">
    <div class="footer-top">

      <div class="footer-about">
        <a href="<?= base_url('public/') ?>" class="brand"><span>Ves<b>ta</b></span></a>
        <p>Vesta is a boutique luxury brokerage connecting discerning buyers and sellers with the world's most distinctive homes — backed by data, design, and white-glove service.</p>
        <ul class="footer-contact">
          <li><ion-icon name="location-outline"></ion-icon><span>500 Congress Ave, Suite 1200, Austin, TX 78701</span></li>
          <li><ion-icon name="call-outline"></ion-icon><a href="tel:+15125550192">+1 (512) 555-0192</a></li>
          <li><ion-icon name="mail-outline"></ion-icon><a href="mailto:hello@vestarealty.com">hello@vestarealty.com</a></li>
        </ul>
        <div class="footer-social">
          <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><ion-icon name="logo-facebook"></ion-icon></a>
          <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><ion-icon name="logo-instagram"></ion-icon></a>
          <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><ion-icon name="logo-linkedin"></ion-icon></a>
          <a href="https://youtube.com" target="_blank" rel="noopener" aria-label="YouTube"><ion-icon name="logo-youtube"></ion-icon></a>
        </div>
      </div>

      <div class="footer-col">
        <h4>Explore</h4>
        <ul>
          <li><a href="<?= base_url('public/listings') ?>">All Listings</a></li>
          <li><a href="<?= base_url('public/listings?status=For+Sale') ?>">For Sale</a></li>
          <li><a href="<?= base_url('public/listings?status=For+Rent') ?>">For Rent</a></li>
          <li><a href="<?= base_url('public/blog') ?>">Blog & Guides</a></li>
          <li><a href="<?= base_url('public/about') ?>">About Us</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="<?= base_url('public/about') ?>">Our Story</a></li>
          <li><a href="<?= base_url('public/contact') ?>">Contact</a></li>
          <li><a href="<?= base_url('public/contact') ?>">Careers</a></li>
          <li><a href="<?= base_url('public/contact') ?>">List Your Property</a></li>
          <li><a href="<?= base_url('public/blog') ?>">Market Reports</a></li>
        </ul>
      </div>

      <div class="footer-newsletter footer-col">
        <h4>Stay in the know</h4>
        <p style="font-size:.92rem; margin-bottom:1rem;">Monthly market insights and new listings, straight to your inbox.</p>
        <form data-ajax-form action="<?= base_url('public/subscribe') ?>" method="post">
          <?= csrf_field() ?>
          <input type="text" name="company" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
          <div class="form-alert" data-alert></div>
          <div class="field" style="display:flex; gap:.5rem;">
            <input type="email" name="email" class="input" placeholder="Email address" required>
            <button type="submit" class="btn btn--gold" aria-label="Subscribe"><ion-icon name="arrow-forward-outline"></ion-icon></button>
          </div>
        </form>
        <div class="footer-map mt-2">
          <iframe title="Vesta office location" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            src="https://www.openstreetmap.org/export/embed.html?bbox=-97.7500%2C30.2600%2C-97.7350%2C30.2720&layer=mapnik&marker=30.2660%2C-97.7425"></iframe>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> Vesta Real Estate. All rights reserved.</p>
      <p>Crafted for discerning buyers &amp; sellers · <a href="<?= base_url('public/') ?>">Privacy</a> · <a href="<?= base_url('public/') ?>">Terms</a></p>
    </div>
  </div>
</footer>
