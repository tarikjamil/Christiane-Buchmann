<?php include cb_theme_dir() . '/templates/nav.php'; ?>
  <main id="hero" class="section hero">
    <div class="largecontainer">
      <h1 class="h1 outline_black">Page not found</h1>
      <p class="_24topmargin">The page you are looking for doesn't exist or has moved.</p>
      <div class="inline _48topmargin">
        <a href="<?php echo esc_url(cb_url('portfolio/')); ?>" class="btn blackbtn w-inline-block">
          <div class="h5">See the work</div><img src="<?php echo esc_url(cb_asset('images/crew-unitedAsset-1_2.svg')); ?>" alt="" class="btn_arrow"></a>
      </div>
    </div>
  </main>
<?php include cb_theme_dir() . '/templates/footer.php'; ?>
