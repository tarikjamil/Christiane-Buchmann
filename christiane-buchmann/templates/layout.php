<?php
$site = cb_site();
$wf_pages = $site['webflow_pages'];
$wf_page = isset($wf_pages[$page['template']]) ? $wf_pages[$page['template']] : $wf_pages['404'];
?><!DOCTYPE html>
<html lang="en" data-wf-page="<?php echo esc_attr($wf_page); ?>" data-wf-site="<?php echo esc_attr($site['webflow_site']); ?>">
<head>
  <meta charset="utf-8">
  <title><?php cb_e($page['title']); ?></title>
  <meta name="description" content="<?php echo esc_attr($page['description']); ?>">
  <meta name="robots" content="<?php echo esc_attr($page['robots']); ?>">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <?php if ($page['status'] === 200) : ?>
  <link rel="canonical" href="<?php echo esc_url($page['canonical']); ?>">
  <?php endif; ?>
  <meta property="og:site_name" content="<?php echo esc_attr($site['full_name']); ?>">
  <meta property="og:locale" content="en_US">
  <meta property="og:type" content="<?php echo esc_attr($page['og_type']); ?>">
  <meta property="og:title" content="<?php echo esc_attr($page['title']); ?>">
  <meta property="og:description" content="<?php echo esc_attr($page['description']); ?>">
  <meta property="og:url" content="<?php echo esc_url($page['canonical']); ?>">
  <meta property="og:image" content="<?php echo esc_url($page['image']); ?>">
  <meta name="twitter:card" content="summary_large_image">
  <link href="<?php echo esc_url(cb_asset('css/normalize.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo esc_url(cb_asset('css/components.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo esc_url(cb_asset('css/christiane-buchmann.css')); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo esc_url(cb_asset('css/theme.css')); ?>" rel="stylesheet" type="text/css">
  <script src="https://use.typekit.net/qtk2vpz.js" type="text/javascript"></script>
  <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="<?php echo esc_url(cb_asset('images/favicon.jpg', false)); ?>" rel="shortcut icon" type="image/x-icon">
  <link href="<?php echo esc_url(cb_asset('images/webclip.jpg', false)); ?>" rel="apple-touch-icon">
  <script type="application/ld+json"><?php echo wp_json_encode($page['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
</head>
<body class="page-<?php echo esc_attr($page['template']); ?>">
<?php include cb_theme_dir() . '/templates/pages/' . $page['template'] . '.php'; ?>
  <script src="<?php echo esc_url(cb_asset('js/jquery.min.js')); ?>" type="text/javascript"></script>
  <script src="<?php echo esc_url(cb_asset('js/christiane-buchmann.js')); ?>" type="text/javascript"></script>
  <script src="<?php echo esc_url(cb_asset('js/grained.js')); ?>"></script>
  <script>
    if (document.getElementById("hero")) grained("#hero", {
      animate: true,
      patternWidth: 600,
      patternHeight: 600,
      grainOpacity: 0.19,
      grainDensity: 1,
      grainWidth: 0.7,
      grainHeight: 0.7
    });
  </script>
<?php if ($page['template'] === 'project' && !empty($page['project']['videos'])) : ?>
  <script src="<?php echo esc_url(cb_asset('js/trailer-player.js')); ?>" defer></script>
<?php endif; ?>
<?php if ($page['template'] === 'home') : ?>
  <script src="<?php echo esc_url(cb_asset('js/jquery.scrollify.min.js')); ?>"></script>
  <script>
    $.scrollify({
      section: ".fullsection",
      scrollSpeed: 1000,
      easing: "easeOutExpo",
      offset: 0,
      scrollbars: true,
      setHeights: true,
      updateHash: false,
      afterResize: function () {
        if ($(window).width() < 767) {
          $.scrollify.disable();
        } else {
          $.scrollify.enable();
        }
      }
    });
  </script>
<?php endif; ?>
</body>
</html>
