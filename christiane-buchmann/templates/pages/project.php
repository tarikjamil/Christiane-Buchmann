<?php
$project = $page['project'];
$ix_down = '-webkit-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)';
$ix_up105 = str_replace('-100%', '105%', $ix_down);
$ix_up = str_replace('-100%', '100%', $ix_down);
$ix_left = '-webkit-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)';
$name = cb_project_name($project);
include cb_theme_dir() . '/templates/nav.php';
?>
  <main id="hero" class="main">
    <article>
    <div class="section project_topsection">
      <div class="largecontainer">
        <div class="w-layout-grid grid_2els">
          <div>
            <div class="hide">
              <h1 data-w-id="5397d4d1-d132-49c9-b651-e3d8fdb1fdaa" style="<?php echo $ix_down; ?>" class="h3"><?php cb_e($project['title']); ?></h1>
            </div>
            <div class="hide">
              <p class="h5"><?php cb_e($project['subtitle']); ?></p>
            </div>
            <div class="hide">
              <p><?php cb_e($project['tagline']); ?></p>
            </div>
          </div>
          <div class="hide">
            <p data-w-id="56662b6c-a404-64c4-e1ea-38e678ad2920" style="<?php echo $ix_up105; ?>"><?php cb_e($project['description']); ?></p>
          </div>
        </div>
      </div>
    </div>
    <div class="section notoppadding">
      <div class="largecontainer">
        <div class="w-richtext">
          <?php foreach ($project['videos'] as $video) : ?>
          <div class="project_video"><iframe src="<?php echo esc_url(cb_video_embed_url($video)); ?>" title="<?php echo esc_attr($name . ' – Trailer'); ?>" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>
          <?php endforeach; ?>
          <?php if (!empty($project['trailer_link'])) : ?>
          <div class="inline project_trailer_link">
            <a href="<?php echo esc_url($project['trailer_link']['url']); ?>" target="_blank" rel="noopener" class="btn blackbtn w-inline-block">
              <div class="h5"><?php cb_e($project['trailer_link']['label']); ?></div><img src="<?php echo esc_url(cb_asset('images/crew-unitedAsset-1_2.svg')); ?>" alt="" class="btn_arrow"></a>
          </div>
          <?php endif; ?>
          <?php if ($project['gallery']) : ?>
          <div class="project_gallery">
            <?php foreach ($project['gallery'] as $i => $img) : ?>
            <figure><img src="<?php echo esc_url(cb_upload($img['src'])); ?>" alt="<?php echo esc_attr(cb_project_alt($project, 'film still ' . ($i + 1))); ?>" loading="lazy"><?php if ($img['caption']) : ?><figcaption><?php cb_e($img['caption']); ?></figcaption><?php endif; ?></figure>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <?php foreach ($project['notes'] as $note) : ?>
          <p><?php cb_e($note); ?></p>
          <?php endforeach; ?>
        </div>
        <p class="_24topmargin"><?php cb_e($project['details']); ?></p>
      </div>
      <div class="largecontainer _24topmargin">
        <?php if ($project['credits']) : ?>
        <div class="w-richtext"><p><?php echo implode("<br>\n", array_map('esc_html', $project['credits'])); ?></p></div>
        <?php endif; ?>
        <?php if ($project['crew_united']) : ?>
        <div class="_24topmargin"><strong>Full Cast &amp; Crew</strong></div><a href="<?php echo esc_url($project['crew_united']); ?>" target="_blank" rel="noopener" class="socialmedia_link w-inline-block" aria-label="<?php echo esc_attr($name . ' on Crew United'); ?>"><img src="<?php echo esc_url(cb_asset('images/crew-unitedAsset-1_1.svg')); ?>" alt="Crew United" class="socialmedia_img"></a>
        <?php endif; ?>
      </div>
    </div>
    </article>
    <div class="fullsection"><img src="<?php echo esc_url($project['image'] ? cb_upload($project['image']) : cb_asset('images/corious_retouched.jpg')); ?>" alt="<?php echo esc_attr($project['image'] ? cb_project_alt($project) : ''); ?>" class="fullimg" loading="lazy">
      <div data-w-id="5f3fe868-5889-a951-c063-f290b0bd470a" class="largecontainer">
        <div class="hide">
          <h2 data-w-id="5f3fe868-5889-a951-c063-f290b0bd470b" style="<?php echo $ix_up; ?>" class="h1">Curious ?</h2>
        </div>
        <div class="hide">
          <div style="<?php echo $ix_left; ?>" class="inline _48topmargin">
            <a href="<?php echo esc_url(cb_url('contact/')); ?>" class="btn w-inline-block">
              <div class="h5">Contact</div><img src="<?php echo esc_url(cb_asset('images/btn.svg')); ?>" alt="" class="btn_arrow"></a>
          </div>
        </div>
      </div>
    </div>
  </main>
<?php include cb_theme_dir() . '/templates/footer.php'; ?>
