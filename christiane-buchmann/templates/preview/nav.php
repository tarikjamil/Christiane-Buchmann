<?php
$site = cb_site();
$here = isset($page['template']) ? $page['template'] : '';
$links = array(
    array('Home', cb_preview_url(''), 'home'),
    array('Work', cb_preview_url('portfolio'), 'portfolio'),
    array('About', cb_preview_url('about'), 'about'),
    array('Contact', cb_preview_url('contact'), 'contact'),
);
$dark = !empty($pv_nav_dark);
?>
<header class="pv-nav<?php echo $dark ? ' is-dark' : ''; ?>">
  <a class="pv-nav__brand" href="<?php echo esc_url(cb_preview_url('')); ?>" aria-label="<?php echo esc_attr($site['full_name'] . ' – Home'); ?>">
    <span class="pv-nav__name">Christiane Buchmann</span>
    <span class="pv-nav__role">DoP · BVK</span>
  </a>
  <nav class="pv-nav__links" aria-label="Preview">
    <?php foreach ($links as $l) : ?>
    <?php cb_preview_hover_link($l[1], $l[0], 'pv-nav__link', $here === $l[2]); ?>
    <?php endforeach; ?>
  </nav>
  <button type="button" class="pv-nav__menu" aria-expanded="false" aria-controls="pv-drawer" data-pv-menu>
    <span class="pv-nav__menu-lines" aria-hidden="true"></span>
    <span class="pv-nav__menu-label">Menu</span>
  </button>
</header>
<div id="pv-drawer" class="pv-drawer" hidden>
  <nav class="pv-drawer__nav" aria-label="Mobile">
    <?php foreach ($links as $l) : ?>
    <?php cb_preview_hover_link($l[1], $l[0]); ?>
    <?php endforeach; ?>
  </nav>
</div>
