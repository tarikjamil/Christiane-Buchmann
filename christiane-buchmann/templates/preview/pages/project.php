<?php
$project = $page['project'];
$site = cb_site();
$name = cb_project_name($project);
$cat = isset($site['categories'][$project['category']]) ? $site['categories'][$project['category']]['label'] : $project['category'];
$hero_path = !empty($project['image']) ? $project['image'] : 'images/corious_retouched.jpg';
$hero = cb_upload($hero_path);
$credits = cb_preview_credits($project);
$meta_rows = cb_preview_meta_rows($project);
$pv_nav_dark = true;
include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-project">
  <section class="pv-phero" data-pv-parallax data-pv-parallax-travel="14">
    <div class="pv-phero__media" data-pv-parallax-layer aria-hidden="true">
      <img class="pv-phero__img"<?php echo cb_img_attrs($hero_path, '100vw'); ?> alt="" fetchpriority="high" decoding="async">
      <div class="pv-phero__shade"></div>
    </div>
  </section>

  <section class="pv-ptitle">
    <p class="pv-kicker pv-kicker--ink"><?php cb_e(strtoupper($cat)); ?></p>
    <?php
    $display_title = cb_preview_project_title($project);
    $title_lines = cb_preview_title_lines($display_title);
    ?>
    <h1 class="pv-ptitle__name" data-pv-balance-title="<?php echo esc_attr($display_title); ?>">
      <span class="pv-ruled" data-pv-balance-ruled>
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <?php foreach ($title_lines as $line) : ?>
        <span class="pv-ptitle__line"><?php cb_e($line); ?></span>
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <?php endforeach; ?>
      </span>
    </h1>
  </section>

  <?php if ($credits || $meta_rows) : ?>
  <section class="pv-pinfo">
    <?php if ($credits) : ?>
    <div class="pv-credits">
      <?php foreach ($credits as $c) :
        $name_lines = cb_preview_credit_name_lines($c['names']);
        $people = array_values(array_filter($name_lines, function ($n) { return !$n[1]; }));
        $people_count = count($people);
        $pi = 0;
        $notes = array_values(array_filter($name_lines, function ($n) { return $n[1]; }));
      ?>
      <span class="pv-credit">
        <span class="pv-credit__role"><?php cb_e(strtoupper($c['label'])); ?></span>
        <?php foreach ($people as $person) :
          $pi++;
          $comma = $pi < $people_count;
        ?>
        <span class="pv-credit__name"><?php cb_e($person[0]); ?><?php echo $comma ? ',' : ''; ?></span>
        <?php endforeach; ?>
      </span>
      <?php foreach ($notes as $note) : ?>
      <span class="pv-credit__note"><?php cb_e($note[0]); ?></span>
      <?php endforeach; ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($meta_rows) : ?>
    <div class="pv-meta">
      <?php foreach ($meta_rows as $row) : ?>
      <div class="pv-meta__row">
        <span class="pv-ruled__line" aria-hidden="true"></span>
        <div class="pv-meta__content">
          <span class="pv-meta__label"><?php cb_e(strtoupper($row[0])); ?></span>
          <span class="pv-meta__value"><?php cb_e($row[1]); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
      <span class="pv-ruled__line" aria-hidden="true"></span>
    </div>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <?php if ($project['description']) : ?>
  <section class="pv-pbody">
    <p class="pv-pbody__desc"><?php cb_e($project['description']); ?></p>
  </section>
  <?php endif; ?>

  <?php if (!empty($project['videos']) || !empty($project['trailer_link'])) : ?>
  <section class="pv-ptrailer">
    <div class="pv-ptrailer__frame">
      <?php
      /* Siena Savoy organic film mask — clip + ink stroke share one path */
      $pv_film_d = 'M457.525 1.148c-20.789-3.198-193.979 1.16-283.854 2.496 11.104-.178 1.297-2.868-81.146-2.496-103.5.468-86 102.499-86 109.999s-7 524.5-6.5 547.5 10 59 6.5 99c-2.8 32-1.167 234.667 0 332.003.5 75 62.5 66.5 67 68.5s38.5 0 81.5 0 436 6 526 10.5 438.995-.5 505.495 0 330.01-12.5 417.51-12.5 230.99 2 270.99 0 40.5-16 51-31.5 12.5-61 12.5-105.5c0-44.503 7.01-274.504 7.01-348.004s-3.51-159.998-7.01-230.998 0-256.002 0-318.002 7.01-92.998-22.5-110.999c-18.79-11.471-81.99-9.999-133.49-9.999H853.525c-29 0-370 4-396 0Z';
      ?>
      <svg class="pv-ptrailer__defs" aria-hidden="true" focusable="false">
        <defs>
          <clipPath id="pv-film-clip" clipPathUnits="objectBoundingBox">
            <path transform="scale(0.0005139987561, 0.0008543065594)" d="<?php echo esc_attr($pv_film_d); ?>"/>
          </clipPath>
        </defs>
      </svg>
      <div class="pv-ptrailer__clip">
        <?php if (!empty($project['videos'])) : ?>
          <?php foreach ($project['videos'] as $video) : ?>
          <?php
            $duration = cb_video_duration($video);
            $is_embed = in_array($video['provider'], array('vimeo', 'youtube'), true);
          ?>
        <a class="trailer pv-trailer" href="<?php echo esc_url(cb_video_watch_url($video)); ?>"<?php
          if ($is_embed) {
              echo ' data-trailer="' . esc_attr($video['provider']) . '"';
              echo ' data-trailer-id="' . esc_attr($video['id']) . '"';
              echo ' data-trailer-title="' . esc_attr($name) . '"';
              echo ' data-trailer-duration="' . (int) cb_video_seconds($video) . '"';
          } else {
              echo ' target="_blank" rel="noopener"';
          }
        ?> aria-label="<?php echo esc_attr(cb_video_label($project) . ': ' . $name . ($duration ? ', ' . $duration : '')); ?>">
          <img class="trailer_poster" src="<?php echo esc_url(cb_video_thumb($video, $hero)); ?>" alt="" width="1280" height="720">
          <span class="trailer_shade" aria-hidden="true"></span>
          <span class="pv-trailer__cta" aria-hidden="true">
            <span class="pv-trailer__play">
              <svg viewBox="0 0 72 72" focusable="false">
                <path class="pv-trailer__play-glyph" d="M28 22v28l24-14z"/>
              </svg>
            </span>
            <span class="pv-trailer__label"><?php cb_e(strtoupper(cb_video_label($project))); ?></span>
          </span>
        </a>
          <?php endforeach; ?>
        <?php else : ?>
        <a class="trailer pv-trailer pv-trailer--still" href="<?php echo esc_url($project['trailer_link']['url']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($project['trailer_link']['label'] . ': ' . $name); ?>">
          <img class="trailer_poster" src="<?php echo esc_url($hero); ?>" alt="" width="1280" height="720">
          <span class="trailer_shade" aria-hidden="true"></span>
          <span class="pv-trailer__cta pv-trailer__cta--external" aria-hidden="true">
            <span class="pv-trailer__label-text"><?php cb_e(strtoupper($project['trailer_link']['label'])); ?></span>
          </span>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($project['crew_united'])) : ?>
  <section class="pv-pbody pv-pbody--cu">
    <div class="pv-pbody__cu">
      <?php cb_preview_ticket($project['crew_united'], 'Full cast & crew on Crew United', 'Open', 'ink', 'target="_blank" rel="noopener"'); ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($project['gallery'])) : ?>
  <section class="pv-gallery" aria-label="Stills" data-pv-gallery>
    <div class="pv-gallery__inner">
      <?php foreach ($project['gallery'] as $i => $img) :
        $place = ($i % 5) + 1;
        $src = cb_upload($img['src']);
        $alt = cb_project_alt($project, 'film still ' . ($i + 1));
      ?>
      <figure class="pv-gallery__item is--0<?php echo (int) $place; ?>">
        <button
          type="button"
          class="pv-gallery__trigger"
          data-pv-lightbox
          data-pv-lightbox-src="<?php echo esc_url($src); ?>"
          data-pv-lightbox-alt="<?php echo esc_attr($alt); ?>"
          data-pv-lightbox-index="<?php echo (int) $i; ?>"
          aria-label="<?php echo esc_attr('View still ' . ($i + 1)); ?>"
        >
          <span class="pv-gallery__frame" data-pv-parallax data-pv-parallax-travel="<?php echo $place === 2 || $place === 4 ? '10' : '14'; ?>">
            <img class="pv-gallery__img" data-pv-parallax-layer<?php echo cb_img_attrs($img['src'], '(max-width: 899px) 90vw, 45vw'); ?> alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
          </span>
        </button>
        <?php if (!empty($img['caption'])) : ?>
        <figcaption class="pv-gallery__caption"><?php cb_e($img['caption']); ?></figcaption>
        <?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php
  $curious_img = $hero;
  if (!empty($project['gallery'])) {
      foreach ($project['gallery'] as $g) {
          if (empty($g['src'])) {
              continue;
          }
          $candidate = cb_upload($g['src']);
          if ($candidate && $candidate !== $hero) {
              $curious_img = $candidate;
              break;
          }
      }
      if ($curious_img === $hero && !empty($project['gallery'][0]['src'])) {
          $curious_img = cb_upload($project['gallery'][0]['src']);
      }
  }
  ?>
  <section class="pv-pcurious" data-pv-parallax data-pv-parallax-travel="12">
    <div class="pv-pcurious__media" data-pv-parallax-layer aria-hidden="true">
      <img class="pv-pcurious__img" src="<?php echo esc_url($curious_img); ?>" alt="">
      <div class="pv-pcurious__shade"></div>
    </div>
    <div class="pv-pcurious__content">
      <h2>Curious?</h2>
      <?php cb_preview_ticket(cb_preview_url('contact'), 'Contact', 'Write', 'light'); ?>
    </div>
  </section>
</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
