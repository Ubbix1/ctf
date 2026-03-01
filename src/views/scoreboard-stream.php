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

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
set_time_limit(0);
echo "retry: 5000\n\n";
@ob_flush();
@flush();

while (true) {
    if (connection_aborted()) {
        break;
    }

    $leaderboard = get_leaderboard(50);
    $payload = [
        'current_user_id' => $current_user_id,
        'leaderboard' => $leaderboard,
        'generated_at' => time(),
    ];

    echo "event: leaderboard\n";
    echo "data: " . json_encode($payload) . "\n\n";

    @ob_flush();
    @flush();

    sleep(5);
}
?>
