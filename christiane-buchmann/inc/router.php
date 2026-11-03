<?php
/**
 * Serves every public URL from the theme, before WordPress runs its own query.
 * Anything not listed here (wp-json, wp-admin, wp-login, plugin webhooks) is left to WordPress.
 */

function cb_pages()
{
    return array(
        ''                    => 'home',
        'about'               => 'about',
        'portfolio'           => 'portfolio',
        'contact'             => 'contact',
        'gdpr-privacy-notice' => 'privacy',
    );
}

function cb_redirects()
{
    return array(
        'all-projects'                     => 'portfolio/',
        'project_categories/fiction'       => 'portfolio/',
        'project_categories/narrative'     => 'portfolio/',
        'project_categories/music-video'   => 'portfolio/',
        'project_categories/commercials'   => 'portfolio/',
        'project_categories/documentaries' => 'portfolio/',
        'category/uncategorized'           => '',
        'sample-page'                      => '',
        'styleguide'                       => '',
        'home'                             => '',
        'work'                             => 'portfolio/',
    );
}

function cb_request_path()
{
    $path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    $base = parse_url(home_url('/'), PHP_URL_PATH);
    if ($base && $base !== '/' && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
    }
    return trim(rawurldecode((string) $path), '/');
}

function cb_is_passthrough($path)
{
    if (preg_match('#^(wp-json|wp-admin|wp-login\.php|wp-cron\.php|xmlrpc\.php|wp-content|wp-includes)(/|$)#', $path)) {
        return true;
    }
    foreach (array('rest_route', 'wppusher-hook', 'preview', 'customize_changeset_uuid') as $param) {
        if (isset($_GET[$param])) {
            return true;
        }
    }
    return false;
}

function cb_handle_request()
{
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    $path = cb_request_path();
    if (cb_is_passthrough($path)) {
        return;
    }
    cb_canonical_host_redirect();
    cb_dispatch($path);
    exit;
}

/** Sends http:// and www. requests to the address set in Settings > General (once that is https). */
function cb_canonical_host_redirect()
{
    $home = parse_url(function_exists('get_option') ? get_option('home') : home_url('/'));
    if (empty($home['host']) || empty($home['scheme']) || $home['scheme'] !== 'https') {
        return;
    }
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : '';
    $secure = function_exists('is_ssl') ? is_ssl() : (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    if ($secure && $host === strtolower($home['host'])) {
        return;
    }
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    header('Location: https://' . $home['host'] . $uri, true, 301);
    exit;
}

function cb_redirect($path)
{
    header('Location: ' . cb_url($path), true, 301);
    exit;
}

function cb_dispatch($path)
{
    $uri_path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
    $has_slash = substr((string) $uri_path, -1) === '/';

    switch ($path) {
        case 'robots.txt':
            return cb_render_robots();
        case 'sitemap.xml':
        case 'sitemap_index.xml':
        case 'wp-sitemap.xml':
            return cb_render_sitemap();
        case 'llms.txt':
            return cb_render_llms();
        case 'favicon.ico':
            return cb_redirect_asset('images/favicon.jpg');
    }

    $redirects = cb_redirects();
    if (isset($redirects[$path])) {
        cb_redirect($redirects[$path]);
    }

    $pages = cb_pages();
    if (isset($pages[$path])) {
        if ($path !== '' && !$has_slash) {
            cb_redirect($path . '/');
        }
        return cb_render($pages[$path]);
    }

    if (preg_match('#^all-projects/([a-z0-9-]+)$#', $path, $m)) {
        $project = cb_project($m[1]);
        if ($project) {
            if (!$has_slash) {
                cb_redirect($path . '/');
            }
            return cb_render('project', array('project' => $project));
        }
    }

    return cb_render('404', array('status' => 404));
}

function cb_redirect_asset($path)
{
    header('Location: ' . cb_asset($path), true, 301);
    exit;
}

function cb_render($template, $vars = array())
{
    $page = array_merge(array('template' => $template, 'status' => 200), $vars);
    $page = array_merge($page, cb_page_meta($page));
    if (function_exists('status_header')) {
        status_header($page['status']);
    } else {
        http_response_code($page['status']);
    }
    header('Content-Type: text/html; charset=utf-8');
    include cb_theme_dir() . '/templates/layout.php';
}

function cb_partial($name, $vars = array())
{
    extract($vars);
    include cb_theme_dir() . '/templates/' . $name . '.php';
}
