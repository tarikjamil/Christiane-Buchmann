<?php
$site = cb_site();
$pv_nav_dark = true;
$panels = array(
    array(
        'id' => 'enter',
        'kind' => 'intro',
        'kicker' => 'Director of Photography · BVK',
        'title' => 'Christiane\nBuchmann',
        'copy' => $site['quote'],
        'cta' => array('Enter', cb_preview_url('portfolio'), 'Explore'),
        'image' => null,
        'video' => $site['hero_video'],
    ),
    array(
        'id' => 'about',
        'kind' => 'story',
        'kicker' => 'About',
        'title' => "Stories\nthat change\nthe *frame*",
        'copy' => 'Hamburg & Frankfurt. Narrative, music video, commercial, documentary.',
        'cta' => array('About', cb_preview_url('about')),
        'image' => 'images/about-me_retouched.jpg',
    ),
    array(
        'id' => 'narrative',
        'kind' => 'work',
        'kicker' => 'Narrative',
        'title' => 'Features\n& series',
        'copy' => 'Atmospheres built for character, conflict, and quiet tension.',
        'cta' => array('View narrative', cb_preview_url('portfolio') . '#narrative'),
        'image' => 'images/Feature-bearbeitet.jpg',
    ),
    array(
        'id' => 'music',
        'kind' => 'work',
        'kicker' => 'Music video',
        'title' => 'Rhythm\n& image',
        'copy' => 'Camera as instrument — cut to the pulse.',
        'cta' => array('View music', cb_preview_url('portfolio') . '#music-video'),
        'image' => 'images/aboutpage.jpg',
    ),
    array(
        'id' => 'commercial',
        'kind' => 'work',
        'kicker' => 'Commercial',
        'title' => 'Brand\n& craft',
        'copy' => 'Precision lighting for products that need to feel lived-in.',
        'cta' => array('View commercial', cb_preview_url('portfolio') . '#commercial'),
        'image' => 'images/whatido.jpg',
    ),
    array(
        'id' => 'documentary',
        'kind' => 'work',
        'kicker' => 'Documentary',
        'title' => 'Presence\n& truth',
        'copy' => 'Observational frames that leave room for people.',
        'cta' => array('View documentary', cb_preview_url('portfolio') . '#documentary'),
        'image' => 'images/Documentaries-bearbeitet.jpg',
    ),
);
/* Same projects / order as live homepage showcase */
$showcase_slugs = array('schatten', 'the-only-witness', 'la-cigale', 'annalog', 'sinema-leo');
$by_slug = array();
foreach (cb_projects() as $p) {
    $by_slug[$p['slug']] = $p;
}
$featured = array();
foreach ($showcase_slugs as $slug) {
    if (!empty($by_slug[$slug])) {
        $featured[] = $by_slug[$slug];
    }
}
include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-home">
  <div class="pv-scroll" data-pv-scroll>
    <?php foreach ($panels as $i => $panel) : ?>
    <section class="pv-panel pv-panel--<?php echo esc_attr($panel['kind']); ?><?php echo $i === 0 ? ' is-inview' : ''; ?>" id="<?php echo esc_attr($panel['id']); ?>" data-pv-panel data-pv-parallax data-pv-parallax-travel="12">
      <?php if (!empty($panel['video'])) : ?>
      <div class="pv-panel__media" data-pv-parallax-layer aria-hidden="true">
        <video class="pv-panel__video" autoplay muted loop playsinline poster="">
          <?php foreach ($panel['video'] as $src) : ?>
          <source src="<?php echo esc_url($src); ?>" type="video/<?php echo substr($src, -4) === 'webm' ? 'webm' : 'mp4'; ?>">
          <?php endforeach; ?>
        </video>
        <div class="pv-panel__shade"></div>
      </div>
      <?php elseif (!empty($panel['image'])) : ?>
      <div class="pv-panel__media" data-pv-parallax-layer aria-hidden="true">
        <img class="pv-panel__img"<?php echo cb_img_attrs($panel['image'], '100vw'); ?> alt="" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>"<?php echo $i === 0 ? ' fetchpriority="high"' : ''; ?> decoding="async">
        <div class="pv-panel__shade"></div>
      </div>
      <?php endif; ?>
      <div class="pv-panel__content">
        <p class="pv-kicker"><?php cb_e($panel['kicker']); ?></p>
        <h1 class="pv-panel__title"><?php
          $pv_lines = preg_split("/\n/", str_replace('\n', "\n", $panel['title']));
          foreach ($pv_lines as $pv_line) {
            $pv_line = esc_html($pv_line);
            $pv_line = preg_replace('/\*(.+?)\*/u', '<span class="pv-panel__em">$1</span>', $pv_line);
            echo '<span class="pv-panel__line">' . $pv_line . '</span>';
          }
        ?></h1>
        <?php if (!empty($panel['copy'])) : ?>
        <p class="pv-panel__copy"><?php cb_e($panel['copy']); ?></p>
        <?php endif; ?>
        <?php cb_preview_ticket($panel['cta'][1], $panel['cta'][0], isset($panel['cta'][2]) ? $panel['cta'][2] : 'Explore'); ?>
      </div>
    </section>
    <?php endforeach; ?>

    <section class="pv-panel pv-panel--showcase" data-pv-panel>
      <div class="pv-showcase" data-pv-showcase>
        <div class="pv-showcase__head">
          <h2 class="pv-showcase__title">Showcase</h2>
          <div class="pv-showcase__nav" role="group" aria-label="Showcase slides">
            <button type="button" class="pv-chip pv-chip--start pv-showcase__btn" data-pv-showcase-prev aria-label="Previous">
              <?php cb_preview_chip_shell('start'); ?>
              <span class="pv-chip__text" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14"><path fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="square" d="M12 8 H3.75 M7.75 4 L3.25 8 L7.75 12"/></svg>
              </span>
            </button>
            <button type="button" class="pv-chip pv-chip--end pv-showcase__btn" data-pv-showcase-next aria-label="Next">
              <?php cb_preview_chip_shell('end'); ?>
              <span class="pv-chip__text" aria-hidden="true">
                <svg viewBox="0 0 16 16" width="14" height="14"><path fill="none" stroke="currentColor" stroke-width="1.35" stroke-linecap="square" d="M3 8 H12.25 M8.25 4 L12.75 8 L8.25 12"/></svg>
              </span>
            </button>
          </div>
        </div>
        <div class="pv-showcase__rail" data-pv-showcase-rail tabindex="0">
          <?php foreach ($featured as $i => $project) :
            $img_path = !empty($project['image']) ? $project['image'] : 'images/corious_retouched.jpg';
            $cat = $site['categories'][$project['category']]['label'];
            $year = cb_project_year($project);
          ?>
          <a class="pv-showcase__card" href="<?php echo esc_url(cb_preview_project_url($project['slug'])); ?>">
            <div class="pv-showcase__visual">
              <img<?php echo cb_img_attrs($img_path, '(max-width: 899px) 85vw, 28vw'); ?> alt="<?php echo esc_attr(cb_project_alt($project)); ?>" loading="lazy" decoding="async">
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
        </div>
        <div class="pv-showcase__foot">
          <?php cb_preview_ticket(cb_preview_url('portfolio'), 'All work', 'Explore', 'ink'); ?>
        </div>
      </div>
    </section>
  </div>
</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
