<?php
// Railway PHP built-in server router
// Handles URL rewriting since .htaccess doesn't work with built-in server

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly (css, js, images, fonts)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Strip leading slash and pass as url parameter
$url = ltrim($uri, '/');

// Remove any POSu prefix if present
$url = preg_replace('#^POSu/?#', '', $url);

// Set the url parameter for index.php routing
$_GET['url'] = $url;

// Load index.php
require_once __DIR__ . '/index.php';
