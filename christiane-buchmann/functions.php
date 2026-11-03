<?php

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/router.php';
require_once __DIR__ . '/inc/seo.php';

add_action('parse_request', 'cb_handle_request', 0);

add_filter('wp_sitemaps_enabled', '__return_false');
add_filter('xmlrpc_enabled', '__return_false');

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
});
