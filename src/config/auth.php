<?php
// src/config/auth.php - Authentication Functions

// Prevent multiple inclusions
if (defined('AUTH_FUNCTIONS_LOADED')) {
    return;
}
define('AUTH_FUNCTIONS_LOADED', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lazy DB connection to avoid slowing down public pages
if (!function_exists('get_db')) {
function get_db() {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    require __DIR__ . '/db.php';
    return (isset($pdo) && $pdo instanceof PDO) ? $pdo : null;
}
}

/**
 * Register a new user
 */
if (!function_exists('register_user')) {
function register_user($username, $email, $password, $confirm_password) {
    $pdo = get_db();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database unavailable'];
    }

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }

    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['success' => false, 'message' => 'Username must be between 3-50 characters'];
    }

    if ($password !== $confirm_password) {
        return ['success' => false, 'message' => 'Passwords do not match'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password must be at least 6 characters'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email or username already registered'];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, points) VALUES (?, ?, ?, 0)");
        $stmt->execute([$username, $email, $hashed_password]);

        $user_id = $pdo->lastInsertId();

        // Auto-login after registration
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

        return ['success' => true, 'message' => 'Registration successful! Welcome!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
} // End register_user conditional

/**
 * Login user
 */
if (!function_exists('login_user')) {
function login_user($identifier, $password) {
    $pdo = get_db();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database unavailable'];
    }

    $identifier = trim((string)$identifier);

    if ($identifier === '' || empty($password)) {
        return ['success' => false, 'message' => 'Username/email and password are required'];
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid username/email or password'];
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        return ['success' => true, 'message' => 'Login successful!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
} // End login_user conditional

/**
 * Logout user
 */
if (!function_exists('logout_user')) {
function logout_user() {
    session_destroy();
    return true;
}
} // End logout_user conditional

/**
 * Check if user is logged in
 */
if (!function_exists('is_logged_in')) {
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}
} // End is_logged_in conditional

/**
 * Get current user data
 */
if (!function_exists('get_current_user_data')) {
function get_current_user_data() {
    if (!is_logged_in()) {
        return null;
    }

    $pdo = get_db();
    if (!$pdo) {
        return null;
    }
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, points FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
} // End get_current_user_data conditional

/**
 * Get leaderboard (top users by points)
 */
if (!function_exists('get_leaderboard')) {
function get_leaderboard($limit = 20) {
    $pdo = get_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.points, COUNT(uc.id) as challenges_solved
            FROM users u
            LEFT JOIN user_challenges uc ON u.id = uc.user_id
            GROUP BY u.id
            ORDER BY u.points DESC, u.username ASC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
} // End get_leaderboard conditional

/**
 * Add points to user
 */
if (!function_exists('add_user_points')) {
function add_user_points($user_id, $points) {
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }
    try {
        $stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE id = ?");
        return $stmt->execute([$points, $user_id]);
    } catch (PDOException $e) {
        return false;
    }
}
} // End add_user_points conditional

/**
 * Record challenge completion
 */
if (!function_exists('record_challenge_completion')) {
function record_challenge_completion($user_id, $challenge_id, $points) {
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }
    try {
        // Check if already completed
        $stmt = $pdo->prepare("SELECT id FROM user_challenges WHERE user_id = ? AND challenge_id = ?");
        $stmt->execute([$user_id, $challenge_id]);
        
        if ($stmt->fetch()) {
            return false; // Already completed
        }

        // Record completion
        $stmt = $pdo->prepare("INSERT INTO user_challenges (user_id, challenge_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $challenge_id]);

        // Add points
        add_user_points($user_id, $points);

        return true;
    } catch (PDOException $e) {
        return false;
    }
}
} // End record_challenge_completion conditional

/**
 * Get user's challenge progress
 */
if (!function_exists('get_user_challenges')) {
function get_user_challenges($user_id) {
    $pdo = get_db();
    if (!$pdo) {
        return [];
    }
    try {
        $stmt = $pdo->prepare("SELECT challenge_id FROM user_challenges WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $challenges = $stmt->fetchAll();
        return array_column($challenges, 'challenge_id');
    } catch (PDOException $e) {
        return [];
    }
}
} // End get_user_challenges conditional

/**
 * Reset user's CTF progress so labs can be replayed from scratch
 */
if (!function_exists('reset_user_ctf_progress')) {
function reset_user_ctf_progress($user_id) {
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM user_challenges WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $stmt = $pdo->prepare("UPDATE users SET points = 0 WHERE id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();

        // Optional: close active DB-tracked CTF sessions if that table exists.
        try {
            $stmt = $pdo->prepare("
                UPDATE ctf_sessions
                SET status = 'expired', completed_at = NOW()
                WHERE user_id = ? AND status = 'active'
            ");
            $stmt->execute([$user_id]);
        } catch (PDOException $e) {
            // Some deployments do not include ctf_sessions; ignore safely.
        }

        return true;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}
} // End reset_user_ctf_progress conditional

/**
 * Start a CTF session for a user
 */
if (!function_exists('start_ctf_session')) {
function start_ctf_session($user_id, $duration_seconds = 3600) {
    $pdo = get_db();
    if (!$pdo) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO ctf_sessions (user_id, duration_seconds, status, started_at, ends_at)
            VALUES (?, ?, 'active', NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))
        ");
        $stmt->execute([$user_id, $duration_seconds, $duration_seconds]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return null;
    }
}
} // End start_ctf_session conditional

/**
 * Fetch the latest active CTF session for a user
 */
if (!function_exists('get_active_ctf_session')) {
function get_active_ctf_session($user_id) {
    $pdo = get_db();
    if (!$pdo) {
        return null;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM ctf_sessions
            WHERE user_id = ? AND status = 'active'
            ORDER BY started_at DESC
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}
} // End get_active_ctf_session conditional

/**
 * Expire a CTF session
 */
if (!function_exists('expire_ctf_session')) {
function expire_ctf_session($session_id) {
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE ctf_sessions
            SET status = 'expired', completed_at = NOW()
            WHERE id = ? AND status = 'active'
        ");
        return $stmt->execute([$session_id]);
    } catch (PDOException $e) {
        return false;
    }
}
} // End expire_ctf_session conditional

/**
 * Complete a CTF session when user solves all labs
 */
if (!function_exists('complete_ctf_session')) {
function complete_ctf_session($session_id) {
    $pdo = get_db();
    if (!$pdo) {
        return false;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE ctf_sessions
            SET status = 'completed', completed_at = NOW()
            WHERE id = ? AND status = 'active'
        ");
        return $stmt->execute([$session_id]);
    } catch (PDOException $e) {
        return false;
    }
}
} // End complete_ctf_session conditional

// Advanced CTF platform extensions.
require_once __DIR__ . '/advanced.php';
?>
