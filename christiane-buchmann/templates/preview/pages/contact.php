<?php
$site = cb_site();
$agency = $site['agency'];
$pv_nav_dark = false;
include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-contact">
  <section class="pv-ptitle pv-contact__title">
    <p class="pv-kicker pv-kicker--ink">Contact</p>
    <h1 class="pv-ptitle__name">
      <span class="pv-ruled">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <span class="pv-ptitle__line">Get in touch</span>
        <span class="pv-ruled__line" aria-hidden="true"></span>
      </span>
    </h1>
  </section>

  <section class="pv-pbody">
    <p class="pv-pbody__desc">I look forward to your questions and connecting in more detail — about story, light, and how we can shape the image together.</p>
  </section>

  <section class="pv-pinfo">
    <div class="pv-meta">
      <div class="pv-meta__row">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <div class="pv-meta__content">
          <span class="pv-meta__label">Agency</span>
          <span class="pv-meta__value pv-meta__value--stack">
            <a href="<?php echo esc_url($agency['url']); ?>" target="_blank" rel="noopener"><?php cb_e($agency['name']); ?></a>
            <span><?php cb_e($agency['agent']); ?></span>
            <a href="tel:<?php echo esc_attr($agency['phone']); ?>">Tel.: <?php cb_e($agency['phone_label']); ?></a>
            <a href="mailto:<?php echo esc_attr($agency['email']); ?>"><?php cb_e($agency['email']); ?></a>
          </span>
        </div>
      </div>
      <div class="pv-meta__row">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <div class="pv-meta__content">
          <span class="pv-meta__label">Write me</span>
          <span class="pv-meta__value">
            <a href="mailto:<?php echo esc_attr($site['email']); ?>"><?php cb_e($site['email']); ?></a>
          </span>
        </div>
      </div>
      <div class="pv-meta__row">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <div class="pv-meta__content">
          <span class="pv-meta__label">Call me</span>
          <span class="pv-meta__value">
            <a href="tel:<?php echo esc_attr($site['phone']); ?>"><?php cb_e($site['phone_label']); ?></a>
          </span>
        </div>
      </div>
      <div class="pv-meta__row">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <div class="pv-meta__content">
          <span class="pv-meta__label">Meet me</span>
          <span class="pv-meta__value"><?php cb_e(implode(' / ', $site['locations'])); ?></span>
        </div>
      </div>
      <span class="pv-ruled__line" aria-hidden="true"></span>
    </div>
  </section>
</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
