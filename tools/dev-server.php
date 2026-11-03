<?php
/**
 * Local preview without WordPress:
 *   php -S localhost:8080 tools/dev-server.php
 * Uploaded images are loaded from the live site.
 */

define('CB_THEME_DIR', dirname(__DIR__) . '/christiane-buchmann');
define('CB_LIVE', 'http://christianebuchmann.com');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/wp-content/themes/christiane-buchmann/') === 0) {
    $file = CB_THEME_DIR . substr($path, strlen('/wp-content/themes/christiane-buchmann'));
    if (!is_file($file)) {
        http_response_code(404);
        return true;
    }
    $types = array('css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'svg' => 'image/svg+xml', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png');
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    header('Content-Type: ' . (isset($types[$ext]) ? $types[$ext] : 'application/octet-stream'));
    readfile($file);
    return true;
}

if (strpos($path, '/wp-content/uploads/') === 0) {
    header('Location: ' . CB_LIVE . $path, true, 302);
    return true;
}

function home_url($path = '')
{
    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($path, '/');
}

function get_template_directory_uri()
{
    return 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content/themes/christiane-buchmann';
}

function esc_html($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function esc_attr($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function esc_url($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function wp_json_encode($data, $flags = 0)
{
    return json_encode($data, $flags);
}

function is_admin()
{
    return false;
}

function status_header($code)
{
    http_response_code($code);
}

function add_action()
{
}

function add_filter()
{
}

require CB_THEME_DIR . '/functions.php';
cb_handle_request();
