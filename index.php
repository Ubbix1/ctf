<?php
// index.php - Root Router
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Start Output Buffering
// This holds all HTML in memory instead of sending it immediately.
ob_start();

// 2. Parse URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');

// 3. Define Routes
$routes = [
    ''              => 'src/views/home.php',
    'ctf'           => 'src/views/ctf-hub.php',
    'login'    => 'src/views/login.php',
    'base64'        => 'src/tools/base64.php',
    'steg'          => 'src/tools/steg.php',
    'invert'        => 'src/tools/invert.php',
    'pcap'          => 'src/tools/pcap.php',
    'cap-reader'    => 'src/tools/pcap.php',
    'ctf/caesar' => 'src/tools/crypto/caesar.php',

    // Add to $routes
    'ctf/meta' => 'src/tools/ctf/metadata.php',
    'ctf/password' => 'src/tools/ctf/password.php',

    'ctf/redirect' => 'src/tools/ctf/redirect.php',
    'ctf/ports'    => 'src/tools/ctf/ports.php',

    'ctf/base64' => 'src/tools/ctf/base64.php',
    'ctf/xss'    => 'src/tools/ctf/xss.php',
    'ctf/md5'    => 'src/tools/ctf/md5.php',
];

// 4. Resolve Route
if (array_key_exists($path, $routes)) {
    $content_view = $routes[$path];
} else {
    http_response_code(404);
    $content_view = 'src/views/404.php'; // Ensure you have a 404 handler or just echo text
}

// 5. Check if file exists to prevent crashes
if (!file_exists($content_view) && $path !== '') {
    // Fallback for missing files during dev
    echo "<div style='color:white; padding:50px;'>Error: File $content_view not found.</div>";
    exit;
}

// 6. Load the Master Layout
// The layout will include the $content_view
require_once 'src/layout.php';

// 7. Flush Buffer
// Send the final HTML to the browser
ob_end_flush();
?>