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

// Support URLs like /index.php/ctf when mod_rewrite isn't available
if ($path === 'index.php') {
    $path = '';
} elseif (strpos($path, 'index.php/') === 0) {
    $path = substr($path, strlen('index.php/'));
}

// 2.1 CTF Access Guard
$is_ctf_route = ($path === 'ctf' || strpos($path, 'ctf/') === 0);
if ($is_ctf_route) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    require_once __DIR__ . '/src/config/auth.php';

    if (!is_logged_in()) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Please log in to start the CTF.',
        ];
        header('Location: /login');
        exit;
    }

    if (!isset($_SESSION['ctf_duration'])) {
        $_SESSION['ctf_duration'] = 3600;
    }

    if (!isset($_SESSION['ctf_active'])) {
        $_SESSION['ctf_active'] = false;
    }

    if (!empty($_SESSION['ctf_active']) && isset($_SESSION['ctf_session_id'])) {
        $active_session = get_active_ctf_session($_SESSION['user_id']);
        if ($active_session) {
            $ends_at = strtotime($active_session['ends_at'] ?? '');
            if ($ends_at && time() >= $ends_at) {
                expire_ctf_session($active_session['id']);
                $_SESSION['ctf_active'] = false;
                $_SESSION['ctf_expired_at'] = time();
                unset($_SESSION['ctf_session_id']);
            }
        } else {
            $_SESSION['ctf_active'] = false;
            unset($_SESSION['ctf_session_id']);
        }
    }

    $ctf_non_challenge_routes = [
        'ctf',
        'ctf/dashboard',
        'ctf/scoreboard-stream',
        'ctf/matchmaking',
        'ctf/sandbox',
    ];
    if (!in_array($path, $ctf_non_challenge_routes, true) && empty($_SESSION['ctf_active'])) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'CTF is not active. Click "Start CTF" to begin.',
        ];
        header('Location: /ctf');
        exit;
    }
}

// 3. Define Routes
$routes = [
    ''              => 'src/views/home.php',
    'ctf'           => 'src/views/ctf-hub.php',
    'login'         => 'src/views/login.php',
    'signup'        => 'src/views/signup.php',
    'logout'        => 'src/views/logout.php',
    'dashboard'     => 'src/views/dashboard.php',
    'account'       => 'src/views/account.php',
    'base64'        => 'src/tools/base64.php',
    'steg'          => 'src/tools/steg.php',
    'invert'        => 'src/tools/invert.php',
    'pcap'          => 'src/tools/pcap.php',
    'cap-reader'    => 'src/tools/pcap.php',
    'ctf/caesar'    => 'src/tools/crypto/caesar.php',

    // CTF Challenges
    'ctf/meta'      => 'src/tools/ctf/metadata.php',
    'ctf/password'  => 'src/tools/ctf/password.php',
    'ctf/redirect'  => 'src/tools/ctf/redirect.php',
    'ctf/ports'     => 'src/tools/ctf/ports.php',
    'ctf/base64'    => 'src/tools/ctf/base64.php',
    'ctf/xss'       => 'src/tools/ctf/xss.php',
    'ctf/md5'       => 'src/tools/ctf/md5.php',
    'ctf/desync'    => 'src/tools/ctf/desync.php',
    'ctf/blind'     => 'src/tools/ctf/blind.php',
    'ctf/chain'     => 'src/tools/ctf/chain.php',
    'ctf/pickle'    => 'src/tools/ctf/pickle.php',
    'ctf/c2'        => 'src/tools/ctf/c2.php',
    'ctf/dashboard' => 'src/views/dashboard.php',
    'ctf/scoreboard-stream' => 'src/views/scoreboard-stream.php',
    'ctf/matchmaking' => 'src/views/matchmaking.php',
    'ctf/sandbox'   => 'src/views/sandbox.php',
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
if ($path === 'ctf/scoreboard-stream') {
    include $content_view;
} else {
    require_once 'src/layout.php';
}

// 7. Flush Buffer
// Send the final HTML to the browser
ob_end_flush();
?>
