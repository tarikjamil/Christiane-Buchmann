<?php
$site = cb_site();
$tab = 0;
include cb_theme_dir() . '/templates/nav.php';
?>
  <main class="section portfoliohero">
    <div class="largecontainer">
      <div class="hide">
        <h1 data-w-id="38d932c6-81b7-845c-5ead-a022642c6013" style="-webkit-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, -100%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" class="h1 _48bottommargin">WORK</h1>
      </div>
      <div data-duration-in="300" data-duration-out="100" class="tabs-2 w-tabs">
        <div id="w-node-77d5f76ee2cb-e9ace7a9" class="tabs-menu-2 w-tab-menu">
          <?php foreach ($site['categories'] as $key => $cat) : $tab++; ?>
          <a data-w-tab="Tab <?php echo $tab; ?>" class="portfolio_cat w-inline-block w-tab-link<?php echo $tab === 1 ? ' w--current' : ''; ?>">
            <div class="div-block"></div>
            <div class="portoflio-title difference"><?php cb_e($cat['label']); ?></div>
          </a>
          <?php endforeach; ?>
        </div>
        <div id="w-node-77d5f76ee2d5-e9ace7a9" class="tabs-content-2 w-tab-content">
          <?php $tab = 0; foreach ($site['categories'] as $key => $cat) : $tab++; ?>
          <div data-w-tab="Tab <?php echo $tab; ?>" class="w-tab-pane<?php echo $tab === 1 ? ' w--tab-active' : ''; ?>">
            <div class="w-dyn-list">
              <div role="list" class="collection-list w-dyn-items">
<?php foreach (cb_projects_in($key) as $project) { include cb_theme_dir() . '/templates/project-card.php'; } ?>
              </div>
            </div>
            <a href="<?php echo esc_url($cat['showcase']); ?>" target="_blank" rel="noopener" class="btn blackbtn _32topmargin w-inline-block">
              <div class="h5">See more</div><img src="<?php echo esc_url(cb_asset('images/crew-unitedAsset-1_2.svg')); ?>" alt="" class="btn_arrow"></a>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </main>
<?php include cb_theme_dir() . '/templates/footer.php'; ?>
