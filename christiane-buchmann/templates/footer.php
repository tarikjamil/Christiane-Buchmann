<?php
/*
 * The home page footer and the shared footer symbol use different Webflow node IDs,
 * which the Webflow CSS grid placement depends on.
 */
$site = cb_site();
$is_home = $page['template'] === 'home';
$ids = $is_home
    ? array('141f898460ec-7bcb6f7c', '141f898460f1-7bcb6f7c', '141f898460fd-7bcb6f7c', '141f89846109-7bcb6f7c', '141f8984610e-7bcb6f7c')
    : array('eafcf1055d55-bc8fd8f2', 'eafcf1055d5a-bc8fd8f2', 'eafcf1055d66-bc8fd8f2', 'eafcf1055d72-bc8fd8f2', 'eafcf1055d77-bc8fd8f2');
?>
  <footer id="<?php echo $is_home ? 'hero' : 'footer'; ?>" class="fullsection <?php echo $is_home ? 'contactsection' : 'footersec'; ?>">
    <div class="largecontainer"><a href="<?php echo esc_url(cb_url('/')); ?>" class="brand_logo footer_logo w-inline-block" aria-label="Christiane Buchmann – Home"><img src="<?php echo esc_url(cb_asset('images/LOGO-christiane_white.svg')); ?>" alt="Christiane Buchmann" class="brand_img footer_img"><img src="<?php echo esc_url(cb_asset('images/LOGO-christiane_white.svg')); ?>" alt="" class="brand_img footer_img"></a>
      <div class="w-layout-grid grid-2">
        <div id="w-node-<?php echo $ids[0]; ?>" class="footer_column<?php echo $is_home ? ' hideonmobile' : ''; ?>">
          <h3 class="h5">Menu</h3><a href="<?php echo esc_url(cb_url('/')); ?>" class="h5 footerlink">Home</a><a href="<?php echo esc_url(cb_url('about/')); ?>" class="h5 footerlink">About</a><a href="<?php echo esc_url(cb_url('portfolio/')); ?>" class="h5 footerlink">Work</a><a href="<?php echo esc_url(cb_url('contact/')); ?>" class="h5 footerlink">Contact</a></div>
        <div id="w-node-<?php echo $ids[1]; ?>" class="footer_column">
          <h3 class="h5">Add me on</h3>
          <div class="socialmedia_parent"><?php foreach ($site['social'] as $link) : ?><a href="<?php echo esc_url($link['url']); ?>" target="_blank" rel="noopener me" class="socialmedia_link w-inline-block" aria-label="<?php echo esc_attr($link['label']); ?>"><img src="<?php echo esc_url(cb_asset('images/' . $link['icon'])); ?>" alt="<?php echo esc_attr($link['label']); ?>" class="socialmedia_img"></a><?php endforeach; ?></div>
        </div>
        <div id="w-node-<?php echo $ids[2]; ?>" class="footer_column">
          <h3 class="h5">Networks</h3>
          <div class="socialmedia_parent"><?php foreach ($site['networks'] as $link) : ?><a href="<?php echo esc_url($link['url']); ?>" target="_blank" rel="noopener me" class="socialmedia_link w-inline-block" aria-label="<?php echo esc_attr($link['label']); ?>"><img src="<?php echo esc_url(cb_asset('images/' . $link['icon'])); ?>" alt="<?php echo esc_attr($link['label']); ?>" class="socialmedia_img<?php echo isset($link['class']) ? ' ' . esc_attr($link['class']) : ''; ?>"></a><?php endforeach; ?></div>
        </div>
        <div id="w-node-<?php echo $ids[3]; ?>" class="footer_column">
          <h3 class="h5">Get in touch</h3><a href="tel:<?php echo esc_attr($site['phone']); ?>" class="h5 footerlink">mobile <?php cb_e($site['phone_label']); ?></a><a href="mailto:<?php echo esc_attr($site['email']); ?>" class="h5 footerlink"><?php cb_e($site['email']); ?></a><span class="h5 footerlink">HAMBURG / FRANKFURT</span><a href="<?php echo esc_url(cb_url('gdpr-privacy-notice/')); ?>" class="h5 footerlink">GDPR Privacy Notice</a></div>
        <div id="w-node-<?php echo $ids[4]; ?>">©<?php echo esc_html(gmdate('Y')); ?> Christiane Buchmann, BVK - All rights reserved, website made by <a href="https://www.novatail.co" target="_blank" rel="noopener" class="novatail_link">Novatail.co</a></div>
      </div>
    </div>
  </footer>
