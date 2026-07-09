<?php
// Railway PHP built-in server router

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Get file extension
$ext = pathinfo($uri, PATHINFO_EXTENSION);

// Serve static files directly
$static_extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 
                       'ico', 'woff', 'woff2', 'ttf', 'eot', 'map', 'pdf'];

if (in_array(strtolower($ext), $static_extensions)) {
    $file = __DIR__ . $uri;
    if (file_exists($file)) {
        // Set correct MIME type
        $mime_types = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
        ];
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        readfile($file);
        exit;
    }
}

// Strip leading slash
$url = ltrim($uri, '/');

// Remove POSu prefix if present
$url = preg_replace('#^POSu/?#', '', $url);

// Pass to index.php
$_GET['url'] = $url;
require_once __DIR__ . '/index.php';
