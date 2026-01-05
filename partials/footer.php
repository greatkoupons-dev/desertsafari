<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
?>
<footer class="footer" id="footer">
  <?php if(!empty($footer_bg_url)): ?><div class="footerBg" style="background-image:url('<?= e($footer_bg_url) ?>')"></div><?php endif; ?>
  <div class="container">
    <div class="inner">
      <div>
        <div class="brand" style="margin-bottom:10px">
          <div class="logo"></div>
          <div style="font-weight:900"><?= e($site_name ?? 'DesertSafariGo') ?></div>
        </div>
        <div class="small">
          Premium Dubai bookings with fast pickup, clear pricing, and responsive support. Built for conversions, mobile-first.
        </div>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="<?= e(url('#packages')) ?>">Packages</a>
        <a href="<?= e(url('#why')) ?>">Why us</a>
        <a href="<?= e(url('#reviews')) ?>">Testimonials</a>
        <a href="<?= e(url('blog')) ?>">Blog</a>
      </div>
      <div>
        <h4>Contact</h4>
        <?php if(!empty($phone)): ?><a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a><?php endif; ?>
        <?php if(!empty($email)): ?><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><?php endif; ?>
        <?php if(!empty($whatsapp)): ?><a class="js-wa" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $whatsapp)) ?>" target="_blank">WhatsApp</a><?php endif; ?>
        <div class="small" style="margin-top:10px">© <?= date('Y') ?> <?= e($site_name ?? 'DesertSafariGo') ?>. All rights reserved.</div>
      </div>
    </div>
  </div>
</footer>

<script src="<?= e(url('assets/js/main.js')) ?>?v=<?= (string)@filemtime(__DIR__.'/../assets/js/main.js') ?>"></script>
</body>
</html>
