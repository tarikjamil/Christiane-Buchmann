<?php
$site = cb_site();
$btn = cb_asset('images/btn.svg');
$t_up = '-webkit-transform:translate3d(0, 100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)';
$t_up104 = str_replace('100%', '104%', $t_up);
$t_left = '-webkit-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(-100%, 0, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)';

$work = array(
    array('id' => 'features', 'label' => 'Narrative', 'image' => 'images/Feature-bearbeitet.jpg'),
    array('id' => 'music', 'label' => 'Music video', 'image' => 'images/aboutpage.jpg'),
    array('id' => 'commercials', 'label' => 'Commercial', 'image' => 'images/whatido.jpg'),
    array('id' => 'documentaries', 'label' => 'Documentary', 'image' => 'images/Documentaries-bearbeitet.jpg'),
);

$showcase = array(
    array('id' => '1', 'label' => 'Wo kein Schatten fällt', 'image' => 'images/Work_WKSF-Filmstill-19_01.jpg', 'url' => cb_project_url('schatten'), 'cta' => 'VIEW PROJECT'),
    array('id' => '2', 'label' => 'The only Witness', 'image' => 'images/two.jpg', 'url' => cb_project_url('the-only-witness'), 'cta' => 'VIEW PROJECT'),
    array('id' => '3', 'label' => 'La Cigale et la Fourmi', 'image' => 'images/La-Cigale-et-la-Fourmi-03.jpg', 'url' => cb_project_url('la-cigale'), 'cta' => 'VIEW PROJECT'),
    array('id' => '4', 'label' => 'Anna Log', 'image' => 'images/annalog.jpg', 'url' => cb_project_url('annalog'), 'cta' => 'VIEW PROJECT'),
    array('id' => '6', 'label' => 'Sinema Leo', 'image' => 'images/Sinema-Leo_gespiegelt_Bild-10.jpg', 'url' => cb_url('portfolio/'), 'cta' => 'VIEW PROJECTS'),
);
?>
  <div style="width:100%" class="loadingtop"></div>
  <div data-w-id="818d1955-e6ea-3ebe-1d30-e8071866219c" style="width:100%" class="blackloading"></div>
<?php include cb_theme_dir() . '/templates/nav.php'; ?>
  <main class="main">
    <div data-autoplay="true" data-loop="true" data-wf-ignore="true" id="hero" class="fullsection middle w-background-video w-background-video-atom"><video autoplay="" loop="" muted="" playsinline="" data-wf-ignore="true" data-object-fit="cover"><?php foreach ($site['hero_video'] as $src) : ?><source src="<?php echo esc_url($src); ?>" type="video/<?php echo substr($src, -4) === 'webm' ? 'webm' : 'mp4'; ?>" data-wf-ignore="true"><?php endforeach; ?></video>
      <div class="largecontainer centertext">
        <div class="hide">
          <h1 data-w-id="3fc4b993-92fa-bb08-487c-1c353739dbd0" style="<?php echo $t_up; ?>" class="h1 uppercase">Christiane Buchmann, BVK</h1>
        </div>
        <div class="hide">
          <div data-w-id="7ef7157b-2d91-b5c6-51a9-83672317ca8d" style="<?php echo $t_up104; ?>" class="h4 uppercase title">Director of Photography</div>
        </div>
        <div class="hide">
          <div data-w-id="18bc11ab-581e-ec49-86d7-f800de77a412" style="<?php echo $t_up; ?>" class="inline _48topmargin">
            <a href="<?php echo esc_url(cb_url('contact/')); ?>" class="btn w-inline-block">
              <div class="h5">Contact</div><img src="<?php echo esc_url($btn); ?>" alt="" class="btn_arrow"></a>
          </div>
        </div>
      </div>
    </div>
    <div id="hero" class="fullsection about"><img src="<?php echo esc_url(cb_asset('images/about-me_retouched.jpg')); ?>" alt="Christiane Buchmann, Director of Photography, behind the camera" class="fullimg" loading="lazy">
      <div class="projectlinkwrapper">
        <div class="largecontainer right">
          <div class="inline">
            <a href="<?php echo esc_url(cb_url('about/')); ?>" class="btn w-inline-block">
              <div class="h5">About</div><img src="<?php echo esc_url($btn); ?>" alt="" class="btn_arrow"></a>
          </div>
        </div>
      </div>
      <div class="largecontainer centertext">
        <div class="overflow">
          <div class="hide">
            <p class="h5 normalfont _23p"><?php cb_e($site['quote']); ?></p>
          </div>
          <div class="hide"></div>
        </div>
      </div>
    </div>
    <div id="whatido" class="div-block-6">
      <div id="hero" data-w-id="dd440d77-8e0c-35b8-79d0-f776dfab196e" class="relative _2">
        <div class="fixed whatidofixed">
          <div class="largecontainer flexleft">
            <div class="hide">
              <h2 style="<?php echo $t_left; ?>" class="h1">WORK</h2>
            </div>
            <?php foreach ($work as $w) : ?>
            <div class="hide"><a href="#<?php echo esc_attr($w['id']); ?>" class="h4 casestudylink"><?php cb_e($w['label']); ?></a></div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php foreach ($work as $w) : ?>
        <div id="<?php echo esc_attr($w['id']); ?>" class="fullsection"><img src="<?php echo esc_url(cb_asset($w['image'])); ?>"<?php echo cb_srcset($w['image']); ?> alt="<?php echo esc_attr($w['label'] . ' work by cinematographer Christiane Buchmann'); ?>" class="fullimg" loading="lazy">
          <div class="projectlinkwrapper">
            <div class="largecontainer right">
              <div class="inline">
                <a href="<?php echo esc_url(cb_url('portfolio/')); ?>" class="btn w-inline-block" aria-label="<?php echo esc_attr('See ' . strtolower($w['label']) . ' work'); ?>">
                  <div class="h5"></div><img src="<?php echo esc_url($btn); ?>" alt="" class="btn_arrow"></a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div id="hero" data-w-id="9584e294-eb5b-6d71-c873-5850de4f89bd" class="relative">
      <div class="fixed">
        <div class="largecontainer flexleft">
          <div class="hide">
            <h2 style="<?php echo $t_left; ?>" class="h1">SHOWCASE</h2>
          </div>
          <?php foreach ($showcase as $s) : ?>
          <div class="hide"><a href="#<?php echo esc_attr($s['id']); ?>" class="h4 casestudylink"><?php cb_e($s['label']); ?></a></div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php foreach ($showcase as $s) : ?>
      <div id="<?php echo esc_attr($s['id']); ?>" class="fullsection"><img src="<?php echo esc_url(cb_asset($s['image'])); ?>"<?php echo cb_srcset($s['image']); ?> alt="<?php echo esc_attr($s['label'] . ', film still, cinematography by Christiane Buchmann'); ?>" class="fullimg" loading="lazy">
        <div class="projectlinkwrapper">
          <div class="largecontainer right">
            <div class="inline">
              <a href="<?php echo esc_url($s['url']); ?>" class="btn w-inline-block">
                <div class="h5"><?php cb_e($s['cta']); ?></div><img src="<?php echo esc_url($btn); ?>" alt="" class="btn_arrow"></a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div id="hero" class="fullsection top"><img src="<?php echo esc_url(cb_asset('images/corious_retouched.jpg')); ?>" alt="" class="fullimg" loading="lazy">
      <div class="largecontainer">
        <div class="hide">
          <h2 class="h1">Curious ?</h2>
        </div>
        <div class="inline _48topmargin">
          <a href="<?php echo esc_url(cb_url('contact/')); ?>" class="btn w-inline-block">
            <div class="h5">Contact</div><img src="<?php echo esc_url($btn); ?>" alt="" class="btn_arrow"></a>
        </div>
      </div>
    </div>
<?php include cb_theme_dir() . '/templates/footer.php'; ?>
  </main>
