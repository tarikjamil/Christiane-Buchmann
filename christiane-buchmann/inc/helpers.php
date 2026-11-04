<?php

function cb_site($key = null)
{
    static $site = null;
    if ($site === null) {
        $site = require __DIR__ . '/site.php';
    }
    return $key === null ? $site : $site[$key];
}

/**
 * Print GA4 / GTM snippets for the live site head (+ optional body noscript).
 * Prefer GTM when gtm_id is set; otherwise load GA4 via gtag.js.
 * Skips draft / noindex pages. Not used on /preview/.
 *
 * @param string $where 'head' | 'body'
 */
function cb_analytics($where = 'head')
{
    global $page;
    if (!empty($page['is_preview'])) {
        return;
    }
    if (!empty($page['robots']) && stripos($page['robots'], 'noindex') !== false) {
        return;
    }

    $gtm = trim((string) cb_site('gtm_id'));
    $ga4 = trim((string) cb_site('ga4_id'));

    // IDs are site-config constants; still escape for attribute / JS string safety
    $esc_attr = function_exists('esc_attr') ? 'esc_attr' : function ($s) {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    };
    $esc_js = function_exists('esc_js') ? 'esc_js' : function ($s) {
        return str_replace(array('\\', "'", '"', "\n", "\r"), array('\\\\', "\\'", '\\"', '\\n', ''), (string) $s);
    };

    if ($gtm !== '') {
        if ($where === 'head') {
            $id = $esc_js($gtm);
            echo "<!-- Google Tag Manager -->\n";
            echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\n";
            echo "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\n";
            echo "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n";
            echo "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n";
            echo "})(window,document,'script','dataLayer','{$id}');</script>\n";
            echo "<!-- End Google Tag Manager -->\n";
        } elseif ($where === 'body') {
            $id = $esc_attr($gtm);
            echo "<!-- Google Tag Manager (noscript) -->\n";
            echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . $id . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
            echo "<!-- End Google Tag Manager (noscript) -->\n";
        }
        return;
    }

    if ($ga4 === '' || $where !== 'head') {
        return;
    }
    $id = $esc_js($ga4);
    echo "<!-- Google tag (gtag.js) -->\n";
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $esc_attr($ga4) . '"></script>' . "\n";
    echo "<script>\n";
    echo "  window.dataLayer = window.dataLayer || [];\n";
    echo "  function gtag(){dataLayer.push(arguments);}\n";
    echo "  gtag('js', new Date());\n";
    echo "  gtag('config', '{$id}');\n";
    echo "</script>\n";
}

/** True when $url points at a different host than this site. */
function cb_is_external_url($url)
{
    $url = trim((string) $url);
    if ($url === '' || $url[0] === '#' || stripos($url, 'mailto:') === 0 || stripos($url, 'tel:') === 0 || stripos($url, 'javascript:') === 0) {
        return false;
    }
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) {
        return false; // relative / same-origin path
    }
    $site_host = parse_url(cb_url('/'), PHP_URL_HOST);
    if (!$site_host) {
        return true;
    }
    return strcasecmp($host, $site_host) !== 0;
}

/** Append target=_blank rel=noopener for external URLs unless already set. */
function cb_external_link_attrs($url, $attrs = '')
{
    $attrs = trim((string) $attrs);
    if (!cb_is_external_url($url) || preg_match('/\btarget\s*=/i', $attrs)) {
        return $attrs;
    }
    return trim($attrs . ' target="_blank" rel="noopener"');
}

function cb_projects($include_drafts = false)
{
    static $projects = null;
    if ($projects === null) {
        $projects = require __DIR__ . '/projects.php';
    }
    if ($include_drafts) {
        return $projects;
    }
    return array_values(array_filter($projects, function ($p) {
        return empty($p['draft']);
    }));
}

function cb_project($slug)
{
    foreach (cb_projects(true) as $p) {
        if ($p['slug'] === $slug) {
            return $p;
        }
    }
    return null;
}

function cb_projects_in($category)
{
    return array_values(array_filter(cb_projects(), function ($p) use ($category) {
        return $p['category'] === $category;
    }));
}

function cb_media($key)
{
    static $media = null;
    if ($media === null) {
        $file = __DIR__ . '/media.php';
        $media = file_exists($file) ? require $file : array();
    }
    return isset($media[$key]) ? $media[$key] : array();
}

/** width/height attributes for an uploaded image, so the layout doesn't jump while it loads. */
function cb_size_attrs($path)
{
    $sizes = cb_media('sizes');
    return isset($sizes[$path]) ? ' width="' . (int) $sizes[$path][0] . '" height="' . (int) $sizes[$path][1] . '"' : '';
}

function cb_video_thumb($video, $fallback)
{
    $thumbs = cb_media('thumbs');
    $key = $video['provider'] . ':' . $video['id'];
    return isset($thumbs[$key]) ? $thumbs[$key] : $fallback;
}

function cb_video_seconds($video)
{
    $durations = cb_media('durations');
    $key = $video['provider'] . ':' . $video['id'];
    return empty($durations[$key]) ? 0 : (int) $durations[$key];
}

/** Runtime as m:ss, or '' when unknown. */
function cb_video_duration($video)
{
    $s = cb_video_seconds($video);
    return $s ? floor($s / 60) . ':' . str_pad((string) ($s % 60), 2, '0', STR_PAD_LEFT) : '';
}

function cb_video_watch_url($video)
{
    return $video['provider'] === 'youtube'
        ? 'https://www.youtube.com/watch?v=' . rawurlencode($video['id'])
        : 'https://vimeo.com/' . rawurlencode($video['id']);
}

function cb_video_label($project)
{
    $labels = array('narrative' => 'Watch trailer', 'documentary' => 'Watch trailer', 'music-video' => 'Watch video', 'commercial' => 'Watch film');
    return isset($labels[$project['category']]) ? $labels[$project['category']] : 'Watch';
}

function cb_theme_dir()
{
    return dirname(__DIR__);
}

function cb_url($path = '')
{
    return home_url('/' . ltrim($path, '/'));
}

function cb_project_url($slug)
{
    return cb_url('all-projects/' . $slug . '/');
}

/** Prefer a sibling .avif when the client accepts it (JPEG/PNG kept as fallback). */
function cb_avif_path($path)
{
    if (!preg_match('/\.(jpe?g|png|webp)$/i', $path)) {
        return $path;
    }
    $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    if (strpos($accept, 'image/avif') === false) {
        return $path;
    }
    $avif = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $path);
    if ($avif === $path) {
        return $path;
    }
    $file = cb_theme_dir() . '/' . ltrim($avif, '/');
    return is_file($file) ? $avif : $path;
}

function cb_asset($path, $prefer_avif = true)
{
    if ($prefer_avif) {
        $path = cb_avif_path($path);
    }
    $file = cb_theme_dir() . '/' . $path;
    $version = file_exists($file) ? filemtime($file) : '';
    return get_template_directory_uri() . '/' . $path . ($version ? '?v=' . $version : '');
}

/** srcset for a theme image, using the Webflow "-p-{width}" variants next to it. */
function cb_srcset($path, $sizes = '100vw')
{
    $file = cb_theme_dir() . '/' . $path;
    $base = preg_replace('/\.[a-z]+$/i', '', $path);
    $use_avif = cb_avif_path($path) !== $path;
    $sources = array();
    $ext = 'jpeg';
    if ($use_avif) {
        $avif_variants = glob(cb_theme_dir() . '/' . $base . '-p-*.avif') ?: array();
        if ($avif_variants) {
            $ext = 'avif';
            foreach ($avif_variants as $variant) {
                if (preg_match('/-p-(\d+)\.avif$/', $variant, $m)) {
                    $sources[(int) $m[1]] = cb_asset($base . '-p-' . $m[1] . '.avif', false) . ' ' . $m[1] . 'w';
                }
            }
        }
    }
    if (!$sources) {
        foreach (glob(cb_theme_dir() . '/' . $base . '-p-*.jpeg') ?: array() as $variant) {
            if (preg_match('/-p-(\d+)\.jpeg$/', $variant, $m)) {
                $sources[(int) $m[1]] = cb_asset($base . '-p-' . $m[1] . '.jpeg', false) . ' ' . $m[1] . 'w';
            }
        }
        $ext = 'jpeg';
    }
    if (!$sources || !file_exists($file)) {
        return '';
    }
    $size = @getimagesize($file);
    $main = ($use_avif && $ext === 'avif') ? cb_avif_path($path) : $path;
    if ($size) {
        $sources[(int) $size[0]] = cb_asset($main, false) . ' ' . (int) $size[0] . 'w';
    }
    ksort($sources);
    return ' srcset="' . esc_attr(implode(', ', $sources)) . '" sizes="' . esc_attr($sizes) . '"';
}

/**
 * Map a WP uploads path (or live absolute URL) to a local theme copy under images/uploads/,
 * when tools/build_upload_derivatives.py has materialized it.
 */
function cb_upload_theme_path($path)
{
    if ($path === '') {
        return null;
    }
    if (preg_match('#^https?://(?:www\.)?christianebuchmann\.com(/wp-content/uploads/.+)$#i', $path, $m)) {
        $path = $m[1];
    }
    $path = '/' . ltrim($path, '/');
    if (strpos($path, '/wp-content/uploads/') !== 0) {
        return null;
    }
    $rel = 'images/uploads/' . substr($path, strlen('/wp-content/uploads/'));
    $file = cb_theme_dir() . '/' . $rel;
    return is_file($file) ? $rel : null;
}

/**
 * Build src (+ optional srcset/sizes/width/height) attributes for a theme or upload image.
 * $path is a theme-relative path (images/…), an uploads path, or an absolute URL.
 */
function cb_img_attrs($path, $sizes = '100vw')
{
    if ($path === '') {
        return '';
    }
    $theme_path = strpos($path, 'images/') === 0 ? $path : cb_upload_theme_path($path);
    if ($theme_path) {
        $attrs = ' src="' . esc_url(cb_asset($theme_path)) . '"';
        $srcset = cb_srcset($theme_path, $sizes);
        if ($srcset) {
            $attrs .= $srcset;
        }
        $file = cb_theme_dir() . '/' . $theme_path;
        if (is_file($file) && ($dim = @getimagesize($file))) {
            $attrs .= ' width="' . (int) $dim[0] . '" height="' . (int) $dim[1] . '"';
        }
        return $attrs;
    }
    $attrs = ' src="' . esc_url(cb_upload($path)) . '"';
    $attrs .= cb_size_attrs($path);
    return $attrs;
}

function cb_upload($path)
{
    if ($path === '') {
        return $path;
    }
    if (preg_match('#^https?://#', $path)) {
        $local = cb_upload_theme_path($path);
        if ($local) {
            return cb_asset($local);
        }
        // Live media should always load over HTTPS.
        if (preg_match('#^http://((?:www\.)?christianebuchmann\.com)/#i', $path)) {
            return preg_replace('#^http://#i', 'https://', $path);
        }
        return $path;
    }
    if (strpos($path, 'images/') === 0) {
        return cb_asset($path);
    }
    $path = '/' . ltrim($path, '/');
    $local = cb_upload_theme_path($path);
    if ($local) {
        return cb_asset($local);
    }
    // Preview server: point uploads straight at the live HTTPS origin (skip http→https hop).
    if (defined('CB_LIVE') && strpos($path, '/wp-content/uploads/') === 0) {
        return rtrim(CB_LIVE, '/') . $path;
    }
    return cb_url($path);
}

function cb_e($text)
{
    echo esc_html($text);
}

function cb_video_embed_url($video, $autoplay = false)
{
    $play = $autoplay ? '&autoplay=1' : '';
    if ($video['provider'] === 'youtube') {
        return 'https://www.youtube-nocookie.com/embed/' . rawurlencode($video['id']) . '?rel=0' . $play;
    }
    return 'https://player.vimeo.com/video/' . rawurlencode($video['id']) . '?title=0&byline=0&portrait=0&dnt=1' . $play;
}

function cb_project_alt($project, $suffix = 'film still')
{
    return trim($project['title'] . ($project['subtitle'] ? ' – ' . $project['subtitle'] : '')) . ', ' . $suffix . ', cinematography by Christiane Buchmann';
}

/** Value after "Role:" in a credits line, e.g. cb_credit($p, 'Director'). */
function cb_credit($project, $role)
{
    foreach ($project['credits'] as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) === 2 && preg_match('/(^|&|\s)' . preg_quote($role, '/') . '(\s|$|&)/i', $parts[0])) {
            return trim($parts[1]);
        }
    }
    return '';
}

function cb_project_year($project)
{
    return preg_match('/\b(19|20)\d{2}\b/', $project['details'], $m) ? $m[0] : '';
}

function cb_excerpt($text, $length = 155)
{
    $text = trim(preg_replace('/\s+/', ' ', $text));
    if (function_exists('mb_strlen') ? mb_strlen($text) <= $length : strlen($text) <= $length) {
        return $text;
    }
    $cut = function_exists('mb_substr') ? mb_substr($text, 0, $length) : substr($text, 0, $length);
    return preg_replace('/\s+\S*$/', '', $cut) . '…';
}
