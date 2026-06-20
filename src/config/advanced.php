<?php
if (defined('ADVANCED_AUTH_FUNCTIONS_LOADED')) {
    return;
}
define('ADVANCED_AUTH_FUNCTIONS_LOADED', true);

if (!function_exists('ensure_advanced_ctf_tables')) {
function ensure_advanced_ctf_tables() {
    static $initialized = false;
    if ($initialized) {
        return true;
    }

    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    $queries = [
        "CREATE TABLE IF NOT EXISTS ctf_attempt_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            challenge_id VARCHAR(50) NOT NULL,
            attempt_fingerprint CHAR(40) NOT NULL,
            success TINYINT(1) NOT NULL DEFAULT 0,
            source_ip VARCHAR(45) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_attempt_user (user_id),
            INDEX idx_attempt_challenge (challenge_id),
            INDEX idx_attempt_success (success),
            INDEX idx_attempt_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_hint_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            challenge_id VARCHAR(50) NOT NULL,
            hints_used INT NOT NULL DEFAULT 0,
            last_hint_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_hint_progress (user_id, challenge_id),
            INDEX idx_hint_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_war_room_notes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            note VARCHAR(500) NOT NULL,
            priority ENUM('low','normal','high','critical') DEFAULT 'normal',
            status ENUM('open','in_progress','done') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_war_room_user (user_id),
            INDEX idx_war_room_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_proctor_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            event_type VARCHAR(64) NOT NULL,
            severity ENUM('low','medium','high') DEFAULT 'low',
            details TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_proctor_user (user_id),
            INDEX idx_proctor_event (event_type),
            INDEX idx_proctor_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_simulation_runs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            scenario VARCHAR(120) NOT NULL,
            score INT NOT NULL DEFAULT 0,
            status ENUM('passed','failed') DEFAULT 'failed',
            duration_seconds INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sim_user (user_id),
            INDEX idx_sim_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_sandbox_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            command_text VARCHAR(255) NOT NULL,
            status ENUM('allowed','blocked') DEFAULT 'allowed',
            output_excerpt VARCHAR(500) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_sandbox_user (user_id),
            INDEX idx_sandbox_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "CREATE TABLE IF NOT EXISTS ctf_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            duration_seconds INT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ends_at TIMESTAMP NULL DEFAULT NULL,
            completed_at TIMESTAMP NULL DEFAULT NULL,
            INDEX idx_session_user (user_id),
            INDEX idx_session_status (status),
            CONSTRAINT fk_ctf_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    try {
        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }
        $initialized = true;
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('get_ctf_challenge_catalog')) {
function get_ctf_challenge_catalog() {
    return [
        'caesar' => ['title' => 'Caesar Cipher', 'difficulty' => 'easy', 'base_points' => 10, 'url' => '/ctf/caesar'],
        'meta' => ['title' => 'Metadata Analysis', 'difficulty' => 'easy', 'base_points' => 10, 'url' => '/ctf/meta'],
        'base64' => ['title' => 'Base64 Decode', 'difficulty' => 'easy', 'base_points' => 10, 'url' => '/ctf/base64'],
        'redirect' => ['title' => 'Open Redirect', 'difficulty' => 'medium', 'base_points' => 20, 'url' => '/ctf/redirect'],
        'pass' => ['title' => 'Password Crack', 'difficulty' => 'medium', 'base_points' => 20, 'url' => '/ctf/password'],
        'ports' => ['title' => 'Open Ports', 'difficulty' => 'medium', 'base_points' => 20, 'url' => '/ctf/ports'],
        'xss' => ['title' => 'XSS Injection', 'difficulty' => 'hard', 'base_points' => 30, 'url' => '/ctf/xss'],
        'md5' => ['title' => 'MD5 Cracking', 'difficulty' => 'hard', 'base_points' => 30, 'url' => '/ctf/md5'],
        'desync' => ['title' => 'desync - Request Smuggling', 'difficulty' => 'extreme', 'base_points' => 50, 'url' => '/ctf/desync'],
        'blind' => ['title' => 'blind - Blind RCE', 'difficulty' => 'extreme', 'base_points' => 50, 'url' => '/ctf/blind'],
        'chain' => ['title' => 'chain - Full Attack Path', 'difficulty' => 'extreme', 'base_points' => 60, 'url' => '/ctf/chain'],
        'pickle' => ['title' => 'pickle - Python Deserialization', 'difficulty' => 'extreme', 'base_points' => 50, 'url' => '/ctf/pickle'],
        'c2' => ['title' => 'c2 - Memory Forensics', 'difficulty' => 'extreme', 'base_points' => 60, 'url' => '/ctf/c2'],
    ];
}
}

if (!function_exists('get_ctf_challenge_aliases')) {
function get_ctf_challenge_aliases() {
    return [
        'password' => 'pass',
        'metadata' => 'meta',
        'caesar-cipher' => 'caesar',
        'desync-room' => 'desync',
        'blind-rce' => 'blind',
        'attack-chain' => 'chain',
        'pickle-rce' => 'pickle',
        'memory-c2' => 'c2',
    ];
}
}

if (!function_exists('normalize_ctf_challenge_id')) {
function normalize_ctf_challenge_id($challenge_id) {
    $raw = strtolower(trim((string) $challenge_id));
    $aliases = get_ctf_challenge_aliases();
    return $aliases[$raw] ?? $raw;
}
}

if (!function_exists('get_ctf_flag_map')) {
function get_ctf_flag_map() {
    // Centralized CTF flag definitions used by Mission Control (/ctf).
    return [
        'caesar' => 'flag{plexaur_caesar}',
        'meta' => 'flag{plexaur_meta_data}',
        'base64' => 'flag{plexaur_decoded_successfully}',
        'redirect' => 'flag{plexaur_redirect_caught}',
        'pass' => 'flag{plexaur_password_found}',
        'ports' => 'flag{plexaur_ports_opened}',
        'xss' => 'flag{plexaur_xss_caught}',
        'md5' => 'flag{plexaur_md5_qwerty_cracked}',
        'desync' => 'flag{desync_admin_flag_path_smuggled}',
        'blind' => 'flag{blind_rce_oast_exfil_complete}',
        'chain' => 'flag{chain_root_privesc_completed}',
        'pickle' => 'flag{pickle_deserialization_rce_confirmed}',
        'c2' => 'flag{powershell_beacon_c2corp.net_4312}',
    ];
}
}

if (!function_exists('validate_ctf_flag_submission')) {
function validate_ctf_flag_submission($challenge_id, $submitted_flag) {
    $normalized_challenge = normalize_ctf_challenge_id($challenge_id);
    $normalized_flag = strtolower(trim((string) $submitted_flag));
    $flag_map = get_ctf_flag_map();

    if (!isset($flag_map[$normalized_challenge])) {
        return [
            'valid' => false,
            'challenge_id' => $normalized_challenge,
            'error' => 'Unknown challenge id.',
        ];
    }

    return [
        'valid' => hash_equals($flag_map[$normalized_challenge], $normalized_flag),
        'challenge_id' => $normalized_challenge,
    ];
}
}

if (!function_exists('get_ctf_hint_catalog')) {
function get_ctf_hint_catalog() {
    return [
        'caesar' => ['Check repeated letters.', 'Test shifts 1 to 13.', 'One fixed shift fully decodes it.'],
        'meta' => ['Inspect EXIF fields.', 'Check custom metadata tags.', 'Hidden values can appear in comment-like fields.'],
        'base64' => ['Look for padding =', 'Decode once first.', 'Try one extra decode if needed.'],
        'redirect' => ['Inspect return URL validation.', 'Try controlled target URL.', 'Flag appears when untrusted redirect is accepted.'],
        'pass' => ['Try dictionary words first.', 'Common passwords crack fast.', 'Target hash maps to a weak common password.'],
        'ports' => ['Run scan and inspect open ports.', 'Look at unusual service exposure.', 'Service details indicate the vulnerable path.'],
        'xss' => ['Check reflected output paths.', 'Try small event-handler payloads.', 'Confirm execution in rendered DOM.'],
        'md5' => ['Use common-word wordlist.', 'Compare exact hash value.', 'Challenge uses a common demo-style input.'],
        'desync' => ['Use CL.TE or TE.CL mismatch to desync proxy/backend parsing.', 'Smuggle a second request toward /internal/admin.', 'Target flag endpoint is /internal/admin/flag.'],
        'blind' => ['No output means use OAST or DNS callbacks.', 'Try payloads with nslookup/curl to an interaction domain.', 'Focus on exfiltration side effects, not response body.'],
        'chain' => ['Enumerate first, escalate second, chain third.', 'Check SUID, cron PATH and docker group privileges.', 'End goal is reading /root/final_flag.txt.'],
        'pickle' => ['Session cookie decodes to python pickle bytes.', 'Craft payload via __reduce__ with safe command proof.', 'Base64-encode serialized payload before submission.'],
        'c2' => ['Run Volatility3 style process and network correlation.', 'Combine process name + c2 domain + pid.', 'Expected flag format: flag{processname_c2domain_pid}.'],
    ];
}
}
if (!function_exists('get_user_mastery_profile')) {
function get_user_mastery_profile($user_id) {
    $catalog = get_ctf_challenge_catalog();
    $solved = get_user_challenges($user_id);
    $solved = is_array($solved) ? $solved : [];

    $difficulty_totals = ['easy' => 0, 'medium' => 0, 'hard' => 0, 'extreme' => 0];
    foreach ($solved as $challenge_id) {
        if (!isset($catalog[$challenge_id])) {
            continue;
        }
        $diff = $catalog[$challenge_id]['difficulty'];
        if (!isset($difficulty_totals[$diff])) {
            $difficulty_totals[$diff] = 0;
        }
        $difficulty_totals[$diff]++;
    }

    $attempt_total = 0;
    $attempt_success = 0;
    if (ensure_advanced_ctf_tables()) {
        $pdo = get_db();
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) AS success_total FROM ctf_attempt_logs WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $row = $stmt->fetch();
                $attempt_total = isset($row['total']) ? (int) $row['total'] : 0;
                $attempt_success = isset($row['success_total']) ? (int) $row['success_total'] : 0;
            } catch (PDOException $e) {
                $attempt_total = 0;
                $attempt_success = 0;
            }
        }
    }

    $accuracy = $attempt_total > 0 ? round(($attempt_success / $attempt_total) * 100, 1) : 0.0;
    $solved_total = count($solved);
    $tier_bonus = (($difficulty_totals['hard'] ?? 0) * 8) + (($difficulty_totals['extreme'] ?? 0) * 12);
    $mastery_score = min(100, ($solved_total * 9) + $tier_bonus + (($accuracy / 100) * 20));

    $level = 'Novice';
    if ($mastery_score >= 80) {
        $level = 'Elite';
    } elseif ($mastery_score >= 55) {
        $level = 'Advanced';
    } elseif ($mastery_score >= 30) {
        $level = 'Practitioner';
    }

    return [
        'level' => $level,
        'mastery_score' => round($mastery_score, 1),
        'solved_total' => $solved_total,
        'accuracy' => $accuracy,
        'difficulty_totals' => $difficulty_totals,
    ];
}
}

if (!function_exists('get_dynamic_challenge_points')) {
function get_dynamic_challenge_points($user_id, $challenge_id) {
    $catalog = get_ctf_challenge_catalog();
    if (!isset($catalog[$challenge_id])) {
        return 10;
    }

    $base = (int) $catalog[$challenge_id]['base_points'];
    $diff = $catalog[$challenge_id]['difficulty'];
    $level = get_user_mastery_profile($user_id)['level'];

    $multipliers = [
        'Novice' => ['easy' => 1.2, 'medium' => 1.1, 'hard' => 1.0, 'extreme' => 1.0],
        'Practitioner' => ['easy' => 1.0, 'medium' => 1.0, 'hard' => 1.0, 'extreme' => 1.0],
        'Advanced' => ['easy' => 0.9, 'medium' => 1.0, 'hard' => 1.1, 'extreme' => 1.15],
        'Elite' => ['easy' => 0.8, 'medium' => 0.95, 'hard' => 1.15, 'extreme' => 1.2],
    ];
    $multiplier = $multipliers[$level][$diff] ?? 1.0;

    return max(5, (int) round($base * $multiplier));
}
}

if (!function_exists('get_adaptive_challenge_pathway')) {
function get_adaptive_challenge_pathway($user_id, $limit = 3) {
    $catalog = get_ctf_challenge_catalog();
    $solved = get_user_challenges($user_id);
    $lookup = array_fill_keys(is_array($solved) ? $solved : [], true);
    $level = get_user_mastery_profile($user_id)['level'];

    $priority = ['easy' => 1, 'medium' => 2, 'hard' => 3, 'extreme' => 4];
    if ($level === 'Practitioner') {
        $priority = ['medium' => 1, 'hard' => 2, 'easy' => 3, 'extreme' => 4];
    }
    if ($level === 'Advanced' || $level === 'Elite') {
        $priority = ['extreme' => 1, 'hard' => 2, 'medium' => 3, 'easy' => 4];
    }

    $items = [];
    foreach ($catalog as $id => $meta) {
        if (isset($lookup[$id])) {
            continue;
        }
        $diff = $meta['difficulty'];
        $items[] = [
            'id' => $id,
            'title' => $meta['title'],
            'difficulty' => ucfirst($diff),
            'url' => $meta['url'],
            'dynamic_points' => get_dynamic_challenge_points($user_id, $id),
            'sort_weight' => $priority[$diff] ?? 99,
            'reason' => ($level === 'Novice' && $diff === 'easy')
                ? 'Best next step to strengthen fundamentals.'
                : (($level === 'Advanced' && $diff === 'hard')
                    ? 'High-value target aligned with your current level.'
                    : 'Balanced progression recommendation.'),
        ];
    }

    usort($items, function($a, $b) {
        if ($a['sort_weight'] === $b['sort_weight']) {
            return strcmp($a['title'], $b['title']);
        }
        return $a['sort_weight'] <=> $b['sort_weight'];
    });

    return array_slice($items, 0, max(1, (int) $limit));
}
}
if (!function_exists('record_proctor_event')) {
function record_proctor_event($user_id, $event_type, $severity, $details, $dedupe_window_seconds = 180) {
    if (!ensure_advanced_ctf_tables()) {
        return false;
    }
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    try {
        $cutoff = date('Y-m-d H:i:s', time() - max(0, (int) $dedupe_window_seconds));
        $check = $pdo->prepare("SELECT id FROM ctf_proctor_events WHERE user_id = ? AND event_type = ? AND created_at >= ? LIMIT 1");
        $check->execute([$user_id, $event_type, $cutoff]);
        if ($check->fetch()) {
            return false;
        }

        $stmt = $pdo->prepare("INSERT INTO ctf_proctor_events (user_id, event_type, severity, details) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $event_type, $severity, $details]);
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('log_ctf_attempt')) {
function log_ctf_attempt($user_id, $challenge_id, $flag_input, $success) {
    if (!ensure_advanced_ctf_tables()) {
        return false;
    }
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    $fingerprint = sha1(strtolower(trim((string) $flag_input)));
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO ctf_attempt_logs (user_id, challenge_id, attempt_fingerprint, success, source_ip) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $challenge_id, $fingerprint, $success ? 1 : 0, $ip]);

        $rapidFailStmt = $pdo->prepare("SELECT COUNT(*) AS fail_count FROM ctf_attempt_logs WHERE user_id = ? AND success = 0 AND created_at >= DATE_SUB(NOW(), INTERVAL 60 SECOND)");
        $rapidFailStmt->execute([$user_id]);
        if ((int) $rapidFailStmt->fetchColumn() >= 6) {
            record_proctor_event($user_id, 'rapid_failed_submissions', 'high', 'Multiple failed submissions in a short interval.', 240);
        }

        $repeatStmt = $pdo->prepare("SELECT COUNT(*) AS repeat_count FROM ctf_attempt_logs WHERE user_id = ? AND challenge_id = ? AND success = 0 AND attempt_fingerprint = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
        $repeatStmt->execute([$user_id, $challenge_id, $fingerprint]);
        if ((int) $repeatStmt->fetchColumn() >= 3) {
            record_proctor_event($user_id, 'repeated_identical_flag_attempt', 'medium', 'Repeated identical invalid flag pattern.', 300);
        }

        if ($success) {
            $fastSuccessStmt = $pdo->prepare("SELECT COUNT(*) AS success_count FROM ctf_attempt_logs WHERE user_id = ? AND success = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 120 SECOND)");
            $fastSuccessStmt->execute([$user_id]);
            if ((int) $fastSuccessStmt->fetchColumn() >= 4) {
                record_proctor_event($user_id, 'unusually_fast_success_streak', 'medium', 'Rapid success streak detected.', 600);
            }
        }

        return true;
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('get_hint_progress')) {
function get_hint_progress($user_id, $challenge_id) {
    if (!ensure_advanced_ctf_tables()) {
        return 0;
    }
    $pdo = get_db();
    if (!$pdo) {
        return 0;
    }

    try {
        $stmt = $pdo->prepare("SELECT hints_used FROM ctf_hint_progress WHERE user_id = ? AND challenge_id = ? LIMIT 1");
        $stmt->execute([$user_id, $challenge_id]);
        $row = $stmt->fetch();
        return $row ? (int) $row['hints_used'] : 0;
    } catch (PDOException $e) {
        return 0;
    }
}
}

if (!function_exists('unlock_next_challenge_hint')) {
function unlock_next_challenge_hint($user_id, $challenge_id) {
    $catalog = get_ctf_hint_catalog();
    if (!isset($catalog[$challenge_id])) {
        return ['status' => 'error', 'message' => 'No hints available.'];
    }
    if (!ensure_advanced_ctf_tables()) {
        return ['status' => 'error', 'message' => 'Hint system unavailable.'];
    }

    $pdo = get_db();
    if (!$pdo) {
        return ['status' => 'error', 'message' => 'Hint system unavailable.'];
    }

    $hints = $catalog[$challenge_id];
    $used = get_hint_progress($user_id, $challenge_id);
    if ($used >= count($hints)) {
        return ['status' => 'exhausted', 'message' => 'All hints are unlocked.'];
    }

    $next_used = $used + 1;

    try {
        $stmt = $pdo->prepare("INSERT INTO ctf_hint_progress (user_id, challenge_id, hints_used, last_hint_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE hints_used = VALUES(hints_used), last_hint_at = NOW()");
        $stmt->execute([$user_id, $challenge_id, $next_used]);
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Could not unlock hint.'];
    }

    return [
        'status' => 'success',
        'hint' => $hints[$used],
        'tier' => $used + 1,
        'total' => count($hints),
    ];
}
}

if (!function_exists('get_hint_prompt_for_failures')) {
function get_hint_prompt_for_failures($user_id, $challenge_id) {
    if (!ensure_advanced_ctf_tables()) {
        return ['can_prompt' => false, 'fails' => 0, 'remaining' => 0];
    }
    $pdo = get_db();
    if (!$pdo) {
        return ['can_prompt' => false, 'fails' => 0, 'remaining' => 0];
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS fail_count FROM ctf_attempt_logs WHERE user_id = ? AND challenge_id = ? AND success = 0");
        $stmt->execute([$user_id, $challenge_id]);
        $fails = (int) $stmt->fetchColumn();

        $hints = get_ctf_hint_catalog();
        $total = isset($hints[$challenge_id]) ? count($hints[$challenge_id]) : 0;
        $used = get_hint_progress($user_id, $challenge_id);
        $remaining = max(0, $total - $used);

        return [
            'can_prompt' => $fails >= 3 && $remaining > 0,
            'fails' => $fails,
            'remaining' => $remaining,
        ];
    } catch (PDOException $e) {
        return ['can_prompt' => false, 'fails' => 0, 'remaining' => 0];
    }
}
}

if (!function_exists('get_user_proctoring_summary')) {
function get_user_proctoring_summary($user_id, $days = 7) {
    if (!ensure_advanced_ctf_tables()) {
        return ['low' => 0, 'medium' => 0, 'high' => 0, 'recent' => []];
    }
    $pdo = get_db();
    if (!$pdo) {
        return ['low' => 0, 'medium' => 0, 'high' => 0, 'recent' => []];
    }

    $cutoff = date('Y-m-d H:i:s', time() - (max(1, (int) $days) * 86400));
    $result = ['low' => 0, 'medium' => 0, 'high' => 0, 'recent' => []];

    try {
        $countStmt = $pdo->prepare("SELECT severity, COUNT(*) AS total FROM ctf_proctor_events WHERE user_id = ? AND created_at >= ? GROUP BY severity");
        $countStmt->execute([$user_id, $cutoff]);
        foreach ($countStmt->fetchAll() as $row) {
            $severity = $row['severity'];
            if (isset($result[$severity])) {
                $result[$severity] = (int) $row['total'];
            }
        }

        $recentStmt = $pdo->prepare("SELECT event_type, severity, details, created_at FROM ctf_proctor_events WHERE user_id = ? ORDER BY id DESC LIMIT 5");
        $recentStmt->execute([$user_id]);
        $result['recent'] = $recentStmt->fetchAll();
    } catch (PDOException $e) {
        // Keep default result
    }

    return $result;
}
}
if (!function_exists('add_war_room_note')) {
function add_war_room_note($user_id, $note, $priority = 'normal') {
    if (!ensure_advanced_ctf_tables()) {
        return false;
    }
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    $note = trim((string) $note);
    if ($note === '') {
        return false;
    }

    $priority = in_array($priority, ['low', 'normal', 'high', 'critical'], true) ? $priority : 'normal';

    try {
        $stmt = $pdo->prepare("INSERT INTO ctf_war_room_notes (user_id, note, priority, status) VALUES (?, ?, ?, 'open')");
        return $stmt->execute([$user_id, substr($note, 0, 500), $priority]);
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('update_war_room_note_status')) {
function update_war_room_note_status($user_id, $note_id, $status) {
    if (!ensure_advanced_ctf_tables()) {
        return false;
    }
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    $status = in_array($status, ['open', 'in_progress', 'done'], true) ? $status : 'open';

    try {
        $stmt = $pdo->prepare("UPDATE ctf_war_room_notes SET status = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$status, (int) $note_id, $user_id]);
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('get_war_room_notes')) {
function get_war_room_notes($user_id, $limit = 30) {
    if (!ensure_advanced_ctf_tables()) {
        return [];
    }
    $pdo = get_db();
    if (!$pdo) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("SELECT id, note, priority, status, created_at, updated_at FROM ctf_war_room_notes WHERE user_id = ? ORDER BY FIELD(status, 'open', 'in_progress', 'done'), FIELD(priority, 'critical', 'high', 'normal', 'low'), id DESC LIMIT ?");
        $stmt->execute([$user_id, max(1, (int) $limit)]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
}

if (!function_exists('record_simulation_run')) {
function record_simulation_run($user_id, $scenario, $score, $status, $duration_seconds) {
    if (!ensure_advanced_ctf_tables()) {
        return false;
    }
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    $status = ($status === 'passed') ? 'passed' : 'failed';

    try {
        $stmt = $pdo->prepare("INSERT INTO ctf_simulation_runs (user_id, scenario, score, status, duration_seconds) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, substr((string) $scenario, 0, 120), max(0, (int) $score), $status, max(0, (int) $duration_seconds)]);
    } catch (PDOException $e) {
        return false;
    }
}
}

if (!function_exists('get_simulation_runs')) {
function get_simulation_runs($user_id, $limit = 10) {
    if (!ensure_advanced_ctf_tables()) {
        return [];
    }
    $pdo = get_db();
    if (!$pdo) {
        return [];
    }

    try {
        $stmt = $pdo->prepare("SELECT scenario, score, status, duration_seconds, created_at FROM ctf_simulation_runs WHERE user_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->execute([$user_id, max(1, (int) $limit)]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
}

if (!function_exists('get_threat_intel_feed')) {
function get_threat_intel_feed() {
    return [
        ['id' => 'TI-1001', 'severity' => 'Critical', 'title' => 'Credential Stuffing Targeting Training Auth Flows', 'source' => 'Platform Telemetry', 'vector' => 'Auth Abuse', 'updated_at' => '2026-02-28 18:20:00', 'summary' => 'Burst login attempts from rotating endpoints indicate bot-driven account abuse.'],
        ['id' => 'TI-1002', 'severity' => 'High', 'title' => 'Reflected XSS Payload Variants Increasing', 'source' => 'Challenge Intelligence', 'vector' => 'Web Exploitation', 'updated_at' => '2026-02-27 11:45:00', 'summary' => 'Short event-handler payloads are bypassing naive blacklists in test apps.'],
        ['id' => 'TI-1003', 'severity' => 'Medium', 'title' => 'Open Redirect Chains Used for Token Theft', 'source' => 'SOC Digest', 'vector' => 'Session Hijack', 'updated_at' => '2026-02-26 09:10:00', 'summary' => 'Trusted-domain redirect chains are being weaponized for phishing.'],
        ['id' => 'TI-1004', 'severity' => 'Medium', 'title' => 'Weak Hash Reuse in Legacy Stores', 'source' => 'Password Audits', 'vector' => 'Credential Compromise', 'updated_at' => '2026-02-25 16:05:00', 'summary' => 'MD5/SHA1 credential artifacts remain quickly crackable with dictionary attacks.'],
        ['id' => 'TI-1005', 'severity' => 'Low', 'title' => 'Recon Traffic Up on Staging Hosts', 'source' => 'Network Sensors', 'vector' => 'Reconnaissance', 'updated_at' => '2026-02-24 07:40:00', 'summary' => 'Increased scanning on common service ports detected before targeted exploitation attempts.'],
    ];
}
}

if (!function_exists('run_sandbox_command')) {
function run_sandbox_command($user_id, $command_text) {
    if (!ensure_advanced_ctf_tables()) {
        return ['status' => 'blocked', 'output' => 'Sandbox unavailable.'];
    }

    $raw = trim((string) $command_text);
    $cmd = strtolower($raw);
    $aliases = [
        'hrlp' => 'help',
        'halp' => 'help',
        'whoai' => 'whoami',
        'threat' => 'show-threats',
        'threats' => 'show-threats',
        'intel' => 'show-threats',
        'challenges' => 'list-challenges',
        'list' => 'list-challenges',
        'points' => 'score',
        'my-score' => 'score',
    ];
    if (isset($aliases[$cmd])) {
        $cmd = $aliases[$cmd];
    }

    $status = 'allowed';
    $output = '';
    $allowed_commands = ['help', 'whoami', 'list-challenges', 'show-threats', 'score'];

    if ($cmd === '' || $cmd === 'help') {
        $output = "Allowed commands: help, whoami, list-challenges, show-threats, score.\nAliases: threats, threat, whoai, hrlp, points.";
    } elseif ($cmd === 'whoami') {
        $output = 'sandbox-user: ' . ($_SESSION['username'] ?? 'agent');
    } elseif ($cmd === 'list-challenges') {
        $lines = [];
        foreach (get_ctf_challenge_catalog() as $id => $meta) {
            $lines[] = $id . ' | ' . ucfirst($meta['difficulty']) . ' | ' . $meta['base_points'] . ' pts';
        }
        $output = implode("\n", $lines);
    } elseif ($cmd === 'show-threats') {
        $lines = [];
        foreach (get_threat_intel_feed() as $item) {
            $lines[] = '[' . $item['severity'] . '] ' . $item['id'] . ' - ' . $item['title'];
        }
        $output = implode("\n", $lines);
    } elseif ($cmd === 'score') {
        $user = get_current_user_data();
        $output = 'current_points=' . (is_array($user) ? (int) ($user['points'] ?? 0) : 0);
    } else {
        $status = 'blocked';
        $suggested = null;
        $min_distance = 99;
        foreach ($allowed_commands as $known) {
            $distance = levenshtein($cmd, $known);
            if ($distance < $min_distance) {
                $min_distance = $distance;
                $suggested = $known;
            }
        }
        if ($suggested !== null && $min_distance <= 5) {
            $output = 'Command blocked by sandbox policy. Did you mean: ' . $suggested . '?';
        } else {
            $output = 'Command blocked by sandbox policy. Try: help';
        }
    }

    $pdo = get_db();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO ctf_sandbox_logs (user_id, command_text, status, output_excerpt) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, substr($raw, 0, 255), $status, substr($output, 0, 500)]);
        } catch (PDOException $e) {
            // Ignore log failure
        }
    }

    return ['status' => $status, 'output' => $output];
}
}

if (!function_exists('get_matchmaking_candidates')) {
function get_matchmaking_candidates($user_id, $limit = 6) {
    $pdo = get_db();
    if (!$pdo) {
        return [];
    }

    try {
        $baseStmt = $pdo->prepare("SELECT u.id, u.username, u.points, COUNT(uc.id) AS solved FROM users u LEFT JOIN user_challenges uc ON uc.user_id = u.id WHERE u.id = ? GROUP BY u.id LIMIT 1");
        $baseStmt->execute([$user_id]);
        $current = $baseStmt->fetch();
        if (!$current) {
            return [];
        }

        $all = $pdo->query("SELECT u.id, u.username, u.points, COUNT(uc.id) AS solved FROM users u LEFT JOIN user_challenges uc ON uc.user_id = u.id GROUP BY u.id ORDER BY u.points DESC LIMIT 200")->fetchAll();
        $candidates = [];
        foreach ($all as $row) {
            if ((int) $row['id'] === (int) $user_id) {
                continue;
            }
            $points_gap = abs((int) $current['points'] - (int) $row['points']);
            $solved_gap = abs((int) $current['solved'] - (int) $row['solved']);
            $compatibility = max(0, 100 - ($points_gap * 0.6) - ($solved_gap * 8));
            $candidates[] = [
                'username' => $row['username'],
                'points' => (int) $row['points'],
                'solved' => (int) $row['solved'],
                'compatibility' => (int) round($compatibility),
                'reason' => $points_gap <= 20 ? 'Very close points profile.' : 'Adjacent skill progression level.',
            ];
        }

        usort($candidates, function($a, $b) {
            if ($a['compatibility'] === $b['compatibility']) {
                return strcmp($a['username'], $b['username']);
            }
            return $b['compatibility'] <=> $a['compatibility'];
        });

        return array_slice($candidates, 0, max(1, (int) $limit));
    } catch (PDOException $e) {
        return [];
    }
}
}

if (!function_exists('get_user_performance_analytics')) {
function get_user_performance_analytics($user_id) {
    $analytics = [
        'mastery' => get_user_mastery_profile($user_id),
        'attempt_total' => 0,
        'success_total' => 0,
        'accuracy' => 0.0,
        'hints_used' => 0,
        'attempt_trend' => [],
        'simulations' => ['runs' => 0, 'avg_score' => 0],
    ];

    if (!ensure_advanced_ctf_tables()) {
        return $analytics;
    }

    $pdo = get_db();
    if (!$pdo) {
        return $analytics;
    }

    try {
        $attemptStmt = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) AS success_total FROM ctf_attempt_logs WHERE user_id = ?");
        $attemptStmt->execute([$user_id]);
        $attempt = $attemptStmt->fetch();
        $analytics['attempt_total'] = isset($attempt['total']) ? (int) $attempt['total'] : 0;
        $analytics['success_total'] = isset($attempt['success_total']) ? (int) $attempt['success_total'] : 0;
        $analytics['accuracy'] = $analytics['attempt_total'] > 0 ? round(($analytics['success_total'] / $analytics['attempt_total']) * 100, 1) : 0.0;

        $hintStmt = $pdo->prepare("SELECT COALESCE(SUM(hints_used), 0) FROM ctf_hint_progress WHERE user_id = ?");
        $hintStmt->execute([$user_id]);
        $analytics['hints_used'] = (int) $hintStmt->fetchColumn();

        $trendStmt = $pdo->prepare("SELECT DATE(created_at) AS day_key, COUNT(*) AS total FROM ctf_attempt_logs WHERE user_id = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY day_key ASC");
        $trendStmt->execute([$user_id]);
        $map = [];
        foreach ($trendStmt->fetchAll() as $row) {
            $map[$row['day_key']] = (int) $row['total'];
        }
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' day'));
            $analytics['attempt_trend'][] = ['day' => $day, 'total' => $map[$day] ?? 0];
        }

        $simStmt = $pdo->prepare("SELECT COUNT(*) AS runs, COALESCE(AVG(score), 0) AS avg_score FROM ctf_simulation_runs WHERE user_id = ?");
        $simStmt->execute([$user_id]);
        $sim = $simStmt->fetch();
        $analytics['simulations'] = [
            'runs' => isset($sim['runs']) ? (int) $sim['runs'] : 0,
            'avg_score' => isset($sim['avg_score']) ? (int) round($sim['avg_score']) : 0,
        ];
    } catch (PDOException $e) {
        // Keep fallback values
    }

    return $analytics;
}
}
?>
