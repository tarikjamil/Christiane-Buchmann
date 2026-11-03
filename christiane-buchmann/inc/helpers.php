<?php

function cb_site($key = null)
{
    static $site = null;
    if ($site === null) {
        $site = require __DIR__ . '/site.php';
    }
    return $key === null ? $site : $site[$key];
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

function cb_asset($path)
{
    $file = cb_theme_dir() . '/' . $path;
    $version = file_exists($file) ? filemtime($file) : '';
    return get_template_directory_uri() . '/' . $path . ($version ? '?v=' . $version : '');
}

/** srcset for a theme image, using the Webflow "-p-{width}.jpeg" variants next to it. */
function cb_srcset($path)
{
    $file = cb_theme_dir() . '/' . $path;
    $base = preg_replace('/\.[a-z]+$/i', '', $path);
    $sources = array();
    foreach (glob(cb_theme_dir() . '/' . $base . '-p-*.jpeg') as $variant) {
        if (preg_match('/-p-(\d+)\.jpeg$/', $variant, $m)) {
            $sources[(int) $m[1]] = cb_asset($base . '-p-' . $m[1] . '.jpeg') . ' ' . $m[1] . 'w';
        }
    }
    if (!$sources || !file_exists($file)) {
        return '';
    }
    $size = getimagesize($file);
    $sources[$size[0]] = cb_asset($path) . ' ' . $size[0] . 'w';
    ksort($sources);
    return ' srcset="' . esc_attr(implode(', ', $sources)) . '" sizes="100vw"';
}

function cb_upload($path)
{
    if ($path === '' || preg_match('#^https?://#', $path)) {
        return $path;
    }
    return cb_url($path);
}

function cb_e($text)
{
    echo esc_html($text);
}

function cb_video_embed_url($video)
{
    if ($video['provider'] === 'youtube') {
        return 'https://www.youtube-nocookie.com/embed/' . rawurlencode($video['id']) . '?rel=0';
    }
    return 'https://player.vimeo.com/video/' . rawurlencode($video['id']) . '?title=0&byline=0&portrait=0&dnt=1';
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
