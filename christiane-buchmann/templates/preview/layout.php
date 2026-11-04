<?php
$site = cb_site();
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php cb_e($page['title']); ?></title>
  <meta name="description" content="<?php echo esc_attr($page['description']); ?>">
  <meta name="robots" content="noindex, nofollow">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta name="theme-color" content="#0a0a0a">
  <meta property="og:site_name" content="<?php echo esc_attr($site['full_name']); ?>">
  <meta property="og:locale" content="en_US">
  <meta property="og:type" content="<?php echo esc_attr($page['og_type']); ?>">
  <meta property="og:title" content="<?php echo esc_attr($page['title']); ?>">
  <meta property="og:description" content="<?php echo esc_attr($page['description']); ?>">
  <meta property="og:url" content="<?php echo esc_url($page['canonical']); ?>">
  <meta property="og:image" content="<?php echo esc_url($page['image']); ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo esc_attr($page['title']); ?>">
  <meta name="twitter:description" content="<?php echo esc_attr($page['description']); ?>">
  <meta name="twitter:image" content="<?php echo esc_url($page['image']); ?>">
  <link rel="icon" href="<?php echo esc_url(cb_asset('images/favicon.jpg', false)); ?>">
  <link rel="stylesheet" href="<?php echo esc_url(cb_asset('css/preview.css')); ?>">
<?php
$pv_has_trailer = $page['template'] === 'project'
    && (!empty($page['project']['videos']) || !empty($page['project']['trailer_link']));
if ($pv_has_trailer) :
?>
  <link rel="stylesheet" href="<?php echo esc_url(cb_asset('css/theme.css')); ?>">
<?php endif; ?>
</head>
<body class="pv pv-page-<?php echo esc_attr($page['template']); ?>">
  <a class="pv-skip" href="#pv-main">Skip to content</a>
<?php include cb_theme_dir() . '/templates/preview/pages/' . $page['template'] . '.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.20/dist/lenis.min.js"></script>
  <script src="<?php echo esc_url(cb_asset('js/preview.js')); ?>" defer></script>
<?php if ($pv_has_trailer && !empty($page['project']['videos'])) : ?>
  <script src="<?php echo esc_url(cb_asset('js/trailer-player.js')); ?>" defer></script>
<?php endif; ?>
</body>
</html>
