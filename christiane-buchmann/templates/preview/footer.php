<?php
$site = cb_site();
?>
<footer class="pv-footer">
  <div class="pv-footer__inner">
    <div class="pv-footer__top">
      <a class="pv-footer__brand" href="<?php echo esc_url(cb_preview_url('')); ?>" aria-label="<?php echo esc_attr('Christiane Buchmann Director of Photography · BVK – Home'); ?>">
        <span class="pv-footer__wordmark" data-pv-fliptext aria-hidden="true">Christiane<br>Buchmann</span>
        <span class="pv-footer__tag" aria-hidden="true">Director of Photography · BVK</span>
      </a>
      <?php cb_preview_ticket(cb_preview_url('contact'), 'Get in touch', 'Contact', 'light'); ?>
    </div>
  </div>

  <span class="pv-ruled__line pv-footer__rule" aria-hidden="true"></span>

  <div class="pv-footer__inner">
    <div class="pv-footer__grid">
      <div class="pv-footer__col">
        <h2 class="pv-footer__heading">Menu</h2>
        <?php cb_preview_hover_link(cb_preview_url(''), 'Home'); ?>
        <?php cb_preview_hover_link(cb_preview_url('portfolio'), 'Work'); ?>
        <?php cb_preview_hover_link(cb_preview_url('about'), 'About'); ?>
        <?php cb_preview_hover_link(cb_preview_url('contact'), 'Contact'); ?>
      </div>
      <div class="pv-footer__col">
        <h2 class="pv-footer__heading">Work</h2>
        <?php foreach ($site['categories'] as $key => $cat) : ?>
        <?php cb_preview_hover_link(cb_preview_url('portfolio') . '#' . $key, $cat['label']); ?>
        <?php endforeach; ?>
      </div>
      <div class="pv-footer__col">
        <h2 class="pv-footer__heading">Connect</h2>
        <?php foreach ($site['social'] as $link) : ?>
        <?php cb_preview_hover_link($link['url'], $link['label']); ?>
        <?php endforeach; ?>
      </div>
      <div class="pv-footer__col">
        <h2 class="pv-footer__heading">Studio</h2>
        <?php cb_preview_hover_link('mailto:' . $site['email'], $site['email']); ?>
        <?php cb_preview_hover_link('tel:' . $site['phone'], $site['phone_label']); ?>
        <span><?php cb_e(implode(' / ', $site['locations'])); ?></span>
        <?php cb_preview_hover_link($site['agency']['url'], $site['agency']['name']); ?>
      </div>
    </div>
  </div>

  <span class="pv-ruled__line pv-footer__rule" aria-hidden="true"></span>

  <div class="pv-footer__inner">
    <div class="pv-footer__bottom">
      <p>©<?php echo esc_html(gmdate('Y')); ?> <?php cb_e($site['full_name']); ?> — All rights reserved</p>
      <p class="pv-footer__credit">Design preview by <a href="https://www.novatail.co" target="_blank" rel="noopener">Novatail</a></p>
    </div>
  </div>
</footer>
