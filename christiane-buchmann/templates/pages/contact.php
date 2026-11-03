<?php
$site = cb_site();
$agency = $site['agency'];
include cb_theme_dir() . '/templates/nav.php';
?>
  <main id="hero" class="section contact">
    <div class="largecontainer _700max">
      <h1 class="h1 outline_black">Get in touch</h1>
      <div class="w-layout-grid grid25">
        <h2 id="w-node-8347ee29cd0d-c9e60f22" class="h5">AGENCY</h2>
        <div id="w-node-b1c4d09dcb73-c9e60f22" class="_8margin"><a href="<?php echo esc_url($agency['url']); ?>" target="_blank" rel="noopener" class="blacklink link"><?php cb_e($agency['name']); ?></a><br><?php cb_e($agency['agent']); ?><br>Tel.: <a href="tel:<?php echo esc_attr($agency['phone']); ?>" class="blacklink link"><?php cb_e($agency['phone_label']); ?></a><br><a href="mailto:<?php echo esc_attr($agency['email']); ?>" class="blacklink link"><?php cb_e($agency['email']); ?></a></div>
      </div>
      <div class="w-layout-grid grid25">
        <h2 id="w-node-7e80b6203f21-c9e60f22" class="h5">Call me</h2>
        <div id="w-node-7e80b6203f23-c9e60f22" class="_8margin blacklink"><a href="tel:<?php echo esc_attr($site['phone']); ?>" class="blacklink"><?php cb_e($site['phone_label']); ?></a></div>
      </div>
      <div class="w-layout-grid grid25">
        <h2 class="h5 contact_label">Write me</h2>
        <div class="_8margin blacklink contact_value"><a href="mailto:<?php echo esc_attr($site['email']); ?>" class="blacklink"><?php cb_e($site['email']); ?></a></div>
      </div>
      <div class="w-layout-grid grid25">
        <h2 id="w-node-c1111074433e-c9e60f22" class="h5">Meet me</h2>
        <div id="w-node-c11110744340-c9e60f22" class="_8margin">Hamburg / Frankfurt</div>
      </div>
    </div>
  </main>
<?php include cb_theme_dir() . '/templates/footer.php'; ?>
