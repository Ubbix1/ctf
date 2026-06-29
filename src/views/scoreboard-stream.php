<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

// Stream leaderboard updates via Server-Sent Events (SSE)
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
// Cloudflare proxied: cap stream at 55s so the worker is freed before CF's 100s timeout.
// The client EventSource auto-reconnects using the retry interval below.
header('X-Accel-Buffering: no');

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
session_write_close();
set_time_limit(60);
$started_at = time();
$max_runtime = 55; // seconds — safely under Cloudflare's 100s origin timeout

echo "retry: 5000\n\n";
@ob_flush();
@flush();

while (true) {
    if (connection_aborted()) {
        break;
    }

    // Exit before Cloudflare kills the connection with a 524
    if ((time() - $started_at) >= $max_runtime) {
        break;
    }

    $leaderboard = get_leaderboard(50);
    $payload = [
        'current_user_id' => $current_user_id,
        'leaderboard'     => $leaderboard,
        'generated_at'    => time(),
    ];

    echo "event: leaderboard\n";
    echo "data: " . json_encode($payload) . "\n\n";

    @ob_flush();
    @flush();

    sleep(5);
}
?>

