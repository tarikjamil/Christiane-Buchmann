<?php
$site = cb_site();
$pv_nav_dark = true;
$awards = $site['awards'];
/* Same organic film mask as project trailer */
$pv_film_d = 'M457.525 1.148c-20.789-3.198-193.979 1.16-283.854 2.496 11.104-.178 1.297-2.868-81.146-2.496-103.5.468-86 102.499-86 109.999s-7 524.5-6.5 547.5 10 59 6.5 99c-2.8 32-1.167 234.667 0 332.003.5 75 62.5 66.5 67 68.5s38.5 0 81.5 0 436 6 526 10.5 438.995-.5 505.495 0 330.01-12.5 417.51-12.5 230.99 2 270.99 0 40.5-16 51-31.5 12.5-61 12.5-105.5c0-44.503 7.01-274.504 7.01-348.004s-3.51-159.998-7.01-230.998 0-256.002 0-318.002 7.01-92.998-22.5-110.999c-18.79-11.471-81.99-9.999-133.49-9.999H853.525c-29 0-370 4-396 0Z';

if (!function_exists('pv_render_award_item')) :
function pv_render_award_item($award)
{
    ?>
  <article class="pv-areviews__item">
    <svg class="pv-areviews__laurel" viewBox="0 0 64 64" width="40" height="40" aria-hidden="true" focusable="false">
      <path fill="none" stroke="currentColor" stroke-width="1.5" d="M32 58 C22 48 14 38 14 28 C14 18 22 12 32 18 C42 12 50 18 50 28 C50 38 42 48 32 58 Z"/>
      <path fill="none" stroke="currentColor" stroke-width="1.2" d="M32 18 V52"/>
      <path fill="none" stroke="currentColor" stroke-width="1.2" d="M22 26 C26 28 28 32 32 34 C36 32 38 28 42 26"/>
      <path fill="none" stroke="currentColor" stroke-width="1.2" d="M20 34 C25 36 28 39 32 42 C36 39 39 36 44 34"/>
      <path fill="none" stroke="currentColor" stroke-width="1.2" d="M19 42 C25 44 28 46 32 49 C36 46 39 44 45 42"/>
    </svg>
    <p class="pv-areviews__label"><?php cb_e($award['label']); ?></p>
    <h3 class="pv-areviews__fest"><?php cb_e($award['title']); ?></h3>
    <?php if (!empty($award['detail'])) : ?>
    <p class="pv-areviews__film"><?php cb_e($award['detail']); ?></p>
    <?php endif; ?>
  </article>
    <?php
}
endif;

include cb_theme_dir() . '/templates/preview/nav.php';
?>
<main id="pv-main" class="pv-about">
  <section class="pv-ahero" data-pv-parallax data-pv-parallax-travel="14">
    <div class="pv-ahero__media" data-pv-parallax-layer aria-hidden="true">
      <img<?php echo cb_img_attrs('images/about.jpg', '100vw'); ?> alt="Christiane Buchmann, Director of Photography, on set" fetchpriority="high" decoding="async">
      <div class="pv-ahero__shade"></div>
    </div>
    <div class="pv-ahero__content">
      <p class="pv-kicker">About</p>
      <h1 class="pv-ahero__title">
        <span class="pv-ruled">
          <span class="pv-ruled__line" aria-hidden="true"></span>
          <span class="pv-ahero__title-text"><?php cb_e($site['full_name']); ?></span>
          <span class="pv-ruled__line" aria-hidden="true"></span>
        </span>
      </h1>
      <p class="pv-ahero__role"><?php cb_e($site['job_title']); ?></p>
    </div>
  </section>

  <section class="pv-abody">
    <blockquote class="pv-abody__quote"><?php cb_e($site['quote']); ?></blockquote>
    <p class="pv-abody__attr">
      <span><?php cb_e($site['full_name']); ?>, <?php cb_e(strtoupper($site['job_title'])); ?></span>
      <span>German Society of Cinematographers · BVK</span>
    </p>
  </section>

  <section class="pv-astory" aria-label="Biography">
    <figure class="pv-astory__frame pv-astory__frame--side" data-pv-parallax data-pv-parallax-travel="12">
      <div class="pv-astory__media" data-pv-parallax-layer>
        <img<?php echo cb_img_attrs('images/bio.jpg', '(max-width: 899px) 100vw, 40vw'); ?> alt="Christiane Buchmann operating a camera" loading="lazy" decoding="async">
      </div>
    </figure>
    <figure class="pv-astory__frame pv-astory__frame--large" data-pv-parallax data-pv-parallax-travel="14">
      <div class="pv-astory__media" data-pv-parallax-layer>
        <img<?php echo cb_img_attrs('images/aboutpage.jpg', '(max-width: 899px) 100vw, 60vw'); ?> alt="On set with Christiane Buchmann" loading="lazy" decoding="async">
      </div>
    </figure>
    <div class="pv-astory__copy">
      <?php foreach ($site['bio'] as $para) : ?>
      <p><?php cb_e($para); ?></p>
      <?php endforeach; ?>
    </div>
  </section>

  <?php
  $awards_left = array();
  $awards_right = array();
  foreach ($awards as $i => $award) {
      if ($i % 2 === 0) {
          $awards_left[] = $award;
      } else {
          $awards_right[] = $award;
      }
  }
  if (!$awards_right) {
      $awards_right = $awards_left;
  }
  ?>
  <section class="pv-areviews" data-pv-awards aria-label="Awards and recognitions">
    <svg class="pv-areviews__defs" aria-hidden="true" focusable="false">
      <defs>
        <clipPath id="pv-awards-clip" clipPathUnits="objectBoundingBox">
          <path transform="scale(0.0005139987561, 0.0008543065594)" d="<?php echo esc_attr($pv_film_d); ?>"/>
        </clipPath>
      </defs>
    </svg>

    <div class="pv-areviews__head">
      <h2 class="pv-areviews__heading">
        <span class="pv-ruled">
          <span class="pv-ruled__line" aria-hidden="true"></span>
          <span class="pv-areviews__heading-text">Awards &amp; Recognitions</span>
          <span class="pv-ruled__line" aria-hidden="true"></span>
        </span>
      </h2>
    </div>

    <div class="pv-areviews__frame">
      <div class="pv-areviews__clip">
        <img class="pv-areviews__bg"<?php echo cb_img_attrs('images/bio_awards.jpg', '100vw'); ?> alt="" aria-hidden="true" loading="lazy" decoding="async">
        <div class="pv-areviews__shade"></div>
        <div class="pv-areviews__viewport">
          <div class="pv-areviews__cols">
            <div class="pv-areviews__track pv-areviews__track--up" data-pv-awards-track>
              <div class="pv-areviews__group">
                <?php foreach ($awards_left as $award) {
                    pv_render_award_item($award);
                } ?>
              </div>
              <div class="pv-areviews__group" aria-hidden="true">
                <?php foreach ($awards_left as $award) {
                    pv_render_award_item($award);
                } ?>
              </div>
            </div>
            <div class="pv-areviews__track pv-areviews__track--down" data-pv-awards-track>
              <div class="pv-areviews__group">
                <?php foreach ($awards_right as $award) {
                    pv_render_award_item($award);
                } ?>
              </div>
              <div class="pv-areviews__group" aria-hidden="true">
                <?php foreach ($awards_right as $award) {
                    pv_render_award_item($award);
                } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>
<?php include cb_theme_dir() . '/templates/preview/footer.php'; ?>
