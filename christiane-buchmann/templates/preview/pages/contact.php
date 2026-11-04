<?php
$site = cb_site();
$a = $site['agency'];
$pv_nav_dark = false;
include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-contact">
  <section class="pv-contact__hero">
    <p class="pv-kicker pv-kicker--ink">Contact</p>
    <h1 class="pv-contact__title">Let’s talk<br>about the next frame</h1>
  </section>
  <section class="pv-contact__grid">
    <div class="pv-contact__block">
      <h2 class="pv-footer__heading">Direct</h2>
      <a href="mailto:<?php echo esc_attr($site['email']); ?>"><?php cb_e($site['email']); ?></a>
      <a href="tel:<?php echo esc_attr($site['phone']); ?>"><?php cb_e($site['phone_label']); ?></a>
      <p><?php cb_e(implode(' / ', $site['locations'])); ?></p>
    </div>
    <div class="pv-contact__block">
      <h2 class="pv-footer__heading">Agency</h2>
      <p><?php cb_e($a['name']); ?> · <?php cb_e($a['agent']); ?></p>
      <a href="mailto:<?php echo esc_attr($a['email']); ?>"><?php cb_e($a['email']); ?></a>
      <a href="tel:<?php echo esc_attr($a['phone']); ?>"><?php cb_e($a['phone_label']); ?></a>
      <?php cb_preview_ticket($a['url'], 'Agency', 'Open', 'ink', 'target="_blank" rel="noopener"'); ?>
    </div>
  </section>
</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
