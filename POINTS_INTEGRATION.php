<?php
// EXAMPLE: How to integrate points system with CTF challenges
// This shows exactly what changes to make

/**
 * LOCATION: src/views/ctf-hub.php
 * LINE: ~47 (where flags are checked)
 * 
 * FIND THIS:
 */

// BEFORE (Current code - no points)
if (isset($flags[$challenge_id]) && $flag === $flags[$challenge_id]) {
    if (!in_array($challenge_id, $_SESSION['ctf_solved'])) {
        $_SESSION['ctf_solved'][] = $challenge_id;
    }
    echo json_encode(['status' => 'success', 'msg' => 'Correct! System unlocked.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Access Denied: Invalid Flag']);
}

/**
 * REPLACE WITH THIS:
 */

// AFTER (With points system)
if (isset($flags[$challenge_id]) && $flag === $flags[$challenge_id]) {
    if (!in_array($challenge_id, $_SESSION['ctf_solved'])) {
        $_SESSION['ctf_solved'][] = $challenge_id;
        
        // ====== ADD THIS BLOCK ======
        // Award points if user is logged in
        if (is_logged_in()) {
            require_once __DIR__ . '/../config/auth.php';
            
            // Points configuration
            $challenge_points = [
                'caesar'    => 10,   // Easy
                'meta'      => 15,   // Medium
                'base64'    => 10,   // Easy
                'redirect'  => 20,   // Hard
                'pass'      => 25,   // Very Hard
                'ports'     => 15,   // Medium
                'xss'       => 20,   // Hard
                'md5'       => 10    // Easy
            ];
            
            $points = $challenge_points[$challenge_id] ?? 10;
            record_challenge_completion($_SESSION['user_id'], $challenge_id, $points);
        }
        // ====== END ADD BLOCK ======
    }
    
    // Show success message with points
    $points = $challenge_points[$challenge_id] ?? 10;
    echo json_encode([
        'status' => 'success', 
        'msg' => "Correct! System unlocked. +{$points} points! 🎯"
    ]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Access Denied: Invalid Flag']);
}

/**
 * ========================================
 * HOW IT WORKS:
 * ========================================
 * 
 * 1. When a user submits correct flag:
 *    - Challenge ID is checked
 * 
 * 2. If correct:
 *    - Challenge added to $_SESSION['ctf_solved']
 *    - If user is logged in, points are awarded
 * 
 * 3. Points are awarded:
 *    - User gets points (stored in database)
 *    - User's rank updates on leaderboard
 *    - Challenge marked as completed
 * 
 * 4. Success message shows:
 *    - "Correct! System unlocked. +10 points! 🎯"
 * 
 * ========================================
 * FUNCTIONS USED:
 * ========================================
 */

/**
 * is_logged_in()
 * - Returns true if user has session
 * - Prevents points for anonymous users
 */

/**
 * record_challenge_completion($user_id, $challenge_id, $points)
 * - Inserts into user_challenges table
 * - Updates user points
 * - Prevents duplicate entries
 * 
 * Location: src/config/auth.php
 */

/**
 * ========================================
 * CUSTOMIZING POINTS:
 * ========================================
 * 
 * Option 1: Static points per challenge
 * ----
 * $challenge_points = [
 *     'caesar'    => 10,
 *     'meta'      => 15,
 *     'base64'    => 10,
 * ];
 * 
 * Option 2: Dynamic based on difficulty
 * ----
 * if ($diff == 'Easy') $points = 10;
 * if ($diff == 'Medium') $points = 15;
 * if ($diff == 'Hard') $points = 20;
 * 
 * Option 3: Time-based (solve time × multiplier)
 * ----
 * $time_taken = time() - $_SESSION['ctf_start'];
 * $bonus = max(0, 100 - ($time_taken / 60)); // Less time = more points
 * 
 * ========================================
 * TESTING:
 * ========================================
 * 
 * 1. Create test account
 * 2. Go to /login
 * 3. Complete a CTF challenge
 * 4. Go to /dashboard
 * 5. Check if points increased
 * 6. Verify rank changed
 * 
 * ========================================
 * VIEWING POINTS IN DATABASE:
 * ========================================
 * 
 * In phpMyAdmin:
 * 
 * SELECT u.username, u.points, COUNT(uc.id) as challenges_solved
 * FROM users u
 * LEFT JOIN user_challenges uc ON u.id = uc.user_id
 * GROUP BY u.id
 * ORDER BY u.points DESC;
 * 
 * ========================================
 * RESETTING POINTS (if needed):
 * ========================================
 * 
 * UPDATE users SET points = 0;
 * DELETE FROM user_challenges;
 * 
 * ========================================
 */
?>
