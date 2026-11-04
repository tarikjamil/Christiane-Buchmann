<?php
$site = cb_site();
$projects = cb_projects();
$pv_nav_dark = true;
include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-work">
  <section class="pv-whero" data-pv-parallax data-pv-parallax-travel="14">
    <div class="pv-whero__media" data-pv-parallax-layer aria-hidden="true">
      <img<?php echo cb_img_attrs('images/Feature-bearbeitet.jpg', '100vw'); ?> alt="" class="pv-whero__img" fetchpriority="high" decoding="async">
      <div class="pv-whero__shade"></div>
    </div>
    <div class="pv-whero__content">
      <h1 class="pv-whero__title">Work</h1>
      <p class="pv-whero__copy">Selected cinematography across narrative, music video, commercial, and documentary — frames built for story, atmosphere, and character.</p>
    </div>
  </section>

  <section class="pv-wintro">
    <span class="pv-ruled__line" aria-hidden="true"></span>
    <div class="pv-wintro__inner">
      <h2 class="pv-wintro__title">In service of the story,<br><em>framed with intent</em></h2>
    </div>
    <span class="pv-ruled__line" aria-hidden="true"></span>
  </section>

  <section class="pv-wtools" aria-label="Filter work">
    <div class="pv-filters" role="tablist" data-pv-filters>
      <?php
      $pv_filters = array_merge(array(array('all', 'All')), array_map(function ($key, $cat) {
          return array($key, $cat['label']);
      }, array_keys($site['categories']), array_values($site['categories'])));
      $pv_filter_n = count($pv_filters);
      foreach ($pv_filters as $i => $f) {
          if ($pv_filter_n === 1) {
              $pos = 'solo';
          } elseif ($i === 0) {
              $pos = 'start';
          } elseif ($i === $pv_filter_n - 1) {
              $pos = 'end';
          } else {
              $pos = 'middle';
          }
          cb_preview_filter($f[0], $f[1], $i === 0, $pos);
      }
      ?>
    </div>
    <?php cb_preview_search('Search titles…'); ?>
  </section>

  <section class="pv-wgrid" data-pv-grid>
    <?php foreach ($projects as $i => $project) :
      $img_path = !empty($project['image']) ? $project['image'] : 'images/corious_retouched.jpg';
      $cat = $site['categories'][$project['category']]['label'];
      $year = cb_project_year($project);
      $search = strtolower(cb_project_name($project) . ' ' . $project['title'] . ' ' . $project['subtitle'] . ' ' . $cat . ' ' . $year);
    ?>
    <a class="pv-showcase__card pv-wcard" href="<?php echo esc_url(cb_preview_project_url($project['slug'])); ?>" data-category="<?php echo esc_attr($project['category']); ?>" data-search="<?php echo esc_attr($search); ?>">
      <div class="pv-showcase__visual">
        <img<?php echo cb_img_attrs($img_path, '(max-width: 899px) 100vw, 33vw'); ?> alt="<?php echo esc_attr(cb_project_alt($project)); ?>" loading="lazy" decoding="async">
      </div>
      <div class="pv-showcase__content">
        <div class="pv-showcase__top">
          <?php if ($year) : ?><span class="pv-showcase__num">[<?php echo esc_html($year); ?>]</span><?php endif; ?>
          <span class="pv-showcase__cat"><?php cb_e($cat); ?></span>
        </div>
        <h3 class="pv-showcase__name"><?php cb_e(cb_project_name($project)); ?></h3>
      </div>
    </a>
    <?php endforeach; ?>
    <p class="pv-wempty" data-pv-empty hidden>No projects match that filter.</p>
  </section>
</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
