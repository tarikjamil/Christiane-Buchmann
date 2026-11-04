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
  <div class="pv-nav__bar">
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
      <span class="pv-nav__menu-label" data-label-open="Menu" data-label-close="Close">Menu</span>
      <span class="pv-nav__menu-plus" aria-hidden="true"></span>
    </button>
  </div>
</header>
<div id="pv-drawer" class="pv-drawer" hidden data-pv-drawer>
  <div class="pv-drawer__scrim" data-pv-drawer-close tabindex="-1" aria-hidden="true"></div>
  <nav class="pv-drawer__panel" aria-label="Mobile" data-lenis-prevent>
    <ul class="pv-drawer__list">
      <?php foreach ($links as $i => $l) : ?>
      <li class="pv-drawer__item" style="--pv-i: <?php echo (int) ($i + 1); ?>">
        <a class="pv-drawer__link<?php echo $here === $l[2] ? ' is-active' : ''; ?>" href="<?php echo esc_url($l[1]); ?>"<?php echo $here === $l[2] ? ' aria-current="page"' : ''; ?> data-pv-drawer-close><?php cb_e($l[0]); ?></a>
      </li>
      <?php endforeach; ?>
    </ul>
  </nav>
</div>
