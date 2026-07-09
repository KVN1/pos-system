<?php
// Railway PHP built-in server router
// Handles URL rewriting that .htaccess would normally do

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Extract the url parameter from the path
$url = ltrim($uri, '/');

// Remove the app prefix if present
$url = preg_replace('#^POSu/#', '', $url);

// Pass to index.php
$_GET['url'] = $url;
require_once __DIR__ . '/index.php';
