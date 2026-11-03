<?php
$current = $page['template'];
$menu = array(
    'home'      => array('Home', cb_url('/')),
    'about'     => array('About', cb_url('about/')),
    'portfolio' => array('WORK', cb_url('portfolio/')),
    'contact'   => array('Contact', cb_url('contact/')),
);
?>
  <div class="navbar_parent">
    <div class="black_bg div-block-5"></div>
    <div class="menu">
      <nav class="largecontainer menu_container" aria-label="Main menu">
        <h2 class="h1 _48bottommargin">MENU</h2>
        <?php foreach ($menu as $key => $item) : ?>
        <a href="<?php echo esc_url($item[1]); ?>"<?php echo $key === $current ? ' aria-current="page"' : ''; ?> class="portfoliocategory_name w-inline-block<?php echo $key === $current ? ' w--current' : ''; ?>">
          <div class="div-block"></div>
          <div class="h2 difference"><?php cb_e($item[0]); ?></div>
        </a>
        <?php endforeach; ?>
      </nav>
    </div>
    <div data-w-id="d097a041-71f2-05a5-3eed-f33e0a241f1e" class="navbar">
      <div class="largecontainer navigation_parent"><a href="<?php echo esc_url(cb_url('/')); ?>"<?php echo $current === 'home' ? ' aria-current="page"' : ''; ?> class="brand_logo w-inline-block<?php echo $current === 'home' ? ' w--current' : ''; ?>" aria-label="Christiane Buchmann – Home"><img src="<?php echo esc_url(cb_asset('images/LOGO-christiane.svg')); ?>" alt="Christiane Buchmann" class="brand_img"><img src="<?php echo esc_url(cb_asset('images/LOGO-christiane.svg')); ?>" alt="" class="brand_img"></a>
        <a id="w-node-f33e0a241f22-0a241f1c" href="#" class="menu_icon w-inline-block" aria-label="Open menu">
          <div data-w-id="d097a041-71f2-05a5-3eed-f33e0a241f23" data-animation-type="lottie" data-src="<?php echo esc_url(cb_asset('documents/lottieflow-menu-nav-08-000000-easey.json')); ?>" data-loop="0" data-direction="1" data-autoplay="0" data-is-ix2-target="1" data-renderer="svg" data-default-duration="2.0208333333333335" data-duration="0" data-ix2-initial-state="0" class="menu_lottie"></div>
        </a>
      </div>
    </div>
  </div>
