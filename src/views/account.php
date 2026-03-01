<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: /login');
    exit;
}

if (!isset($current_user) || !is_array($current_user)) {
    $current_user = get_current_user_data();
}
$current_user_id = is_array($current_user) ? ($current_user['id'] ?? null) : null;
$message = '';
$message_type = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');

    if ($new_username === '' || $new_email === '') {
        $message = 'Username and email are required';
        $message_type = 'error';
    } elseif (strlen($new_username) < 3 || strlen($new_username) > 50) {
        $message = 'Username must be between 3-50 characters';
        $message_type = 'error';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format';
        $message_type = 'error';
    } else {
        $pdo = get_db();
        if (!$pdo) {
            $message = 'Database unavailable';
            $message_type = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
                $stmt->execute([$new_email, $new_username, $current_user['id']]);
                if ($stmt->fetch()) {
                    $message = 'Email or username already in use';
                    $message_type = 'error';
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->execute([$new_username, $new_email, $current_user['id']]);
                    $_SESSION['username'] = $new_username;
                    $_SESSION['email'] = $new_email;
                    $current_user = get_current_user_data();
                    $message = 'Profile updated successfully!';
                    $message_type = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Error updating profile';
                $message_type = 'error';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $pdo = get_db();
    if (!$pdo) {
        $message = 'Database unavailable';
        $message_type = 'error';
    } else {

    // Verify old password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$current_user['id']]);
    $user_data = $stmt->fetch();

    if (!password_verify($old_password, $user_data['password'])) {
        $message = 'Current password is incorrect';
        $message_type = 'error';
    } elseif (strlen($new_password) < 6) {
        $message = 'New password must be at least 6 characters';
        $message_type = 'error';
    } elseif ($new_password !== $confirm_password) {
        $message = 'New passwords do not match';
        $message_type = 'error';
    } else {
        try {
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $current_user['id']]);
            $message = 'Password changed successfully!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Error updating password';
            $message_type = 'error';
        }
    }
    }
}

$challenge_ids = $current_user_id !== null ? get_user_challenges($current_user_id) : [];
$solved_count = is_array($challenge_ids) ? count($challenge_ids) : 0;
$is_hardened = ($solved_count >= 3) ? 'Strong' : 'Needs Work';
?>

<div class="max-w-6xl mx-auto animate-fade-in-up space-y-8">
    <div class="relative overflow-hidden rounded-3xl border border-neutral-800 bg-gradient-to-br from-neutral-950 via-black to-neutral-900 p-8 md:p-10">
        <div class="absolute -top-16 -right-16 h-44 w-44 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-neutral-500/10 blur-3xl"></div>

        <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="inline-flex items-center rounded-full border border-neutral-700 bg-neutral-900/70 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-300">
                    Account Center
                </span>
                <h1 class="mt-4 text-3xl md:text-4xl font-bold text-white">
                    <?= htmlspecialchars($current_user['username']) ?>
                </h1>
                <p class="mt-2 max-w-2xl text-neutral-400">
                    Update your profile details, strengthen account security, and track your CTF readiness in one place.
                </p>
            </div>
            <div class="grid w-full grid-cols-2 gap-3 md:w-auto">
                <div class="rounded-2xl border border-neutral-800 bg-black/50 px-4 py-3">
                    <div class="text-[10px] uppercase tracking-widest text-neutral-500">Points</div>
                    <div id="accountPoints" class="mt-1 text-2xl font-bold text-white"><?= (int) $current_user['points'] ?></div>
                </div>
                <div class="rounded-2xl border border-neutral-800 bg-black/50 px-4 py-3">
                    <div class="text-[10px] uppercase tracking-widest text-neutral-500">Solved</div>
                    <div class="mt-1 text-2xl font-bold text-white"><?= (int) $solved_count ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="rounded-2xl border px-4 py-3 text-sm <?= $message_type === 'success' ? 'border-green-700/60 bg-green-900/20 text-green-300' : 'border-red-700/60 bg-red-900/20 text-red-300' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <aside class="space-y-6">
            <div class="rounded-3xl border border-neutral-800 bg-neutral-950 p-7">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-white to-neutral-300 text-xl font-bold text-black">
                        <?= strtoupper(substr($current_user['username'], 0, 1)) ?>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-white"><?= htmlspecialchars($current_user['username']) ?></h2>
                        <p class="break-all text-xs text-neutral-400"><?= htmlspecialchars($current_user['email']) ?></p>
                    </div>
                </div>

                <div class="mt-6 space-y-3 border-t border-neutral-800 pt-5 text-sm">
                    <div class="flex items-center justify-between rounded-xl border border-neutral-800 bg-black/40 px-3 py-2">
                        <span class="text-neutral-400">Security State</span>
                        <span class="font-semibold <?= $is_hardened === 'Strong' ? 'text-green-400' : 'text-yellow-400' ?>"><?= $is_hardened ?></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-neutral-800 bg-black/40 px-3 py-2">
                        <span class="text-neutral-400">Challenges Solved</span>
                        <span class="font-semibold text-white"><?= (int) $solved_count ?></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-neutral-800 bg-black/40 px-3 py-2">
                        <span class="text-neutral-400">Session</span>
                        <span class="font-semibold text-neutral-200">Active</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="/ctf" class="inline-flex items-center justify-center rounded-xl border border-neutral-700 px-3 py-2 text-sm font-medium text-neutral-200 transition-colors hover:border-neutral-500 hover:text-white">
                        CTF Hub
                    </a>
                    <a href="/logout" class="inline-flex items-center justify-center rounded-xl border border-red-800/80 bg-red-900/20 px-3 py-2 text-sm font-medium text-red-300 transition-colors hover:bg-red-900/40">
                        Logout
                    </a>
                </div>
            </div>

            <div class="rounded-3xl border border-neutral-800 bg-neutral-950 p-7">
                <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-500">Security Tip</div>
                <h3 class="mt-3 text-base font-semibold text-white">Use a unique password with 12+ characters.</h3>
                <p class="mt-2 text-sm text-neutral-400">
                    Rotate your password after public CTF events and avoid reusing credentials across services.
                </p>
            </div>
        </aside>

        <section class="space-y-6 lg:col-span-2">
            <div class="rounded-3xl border border-neutral-800 bg-neutral-950 p-7 md:p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-white">Profile Details</h3>
                    <p class="mt-1 text-sm text-neutral-400">Keep your identity and contact information current.</p>
                </div>

                <form method="POST" action="/account" class="space-y-5">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">Username</label>
                            <input
                                type="text"
                                name="username"
                                value="<?= htmlspecialchars($current_user['username']) ?>"
                                required
                                minlength="3"
                                maxlength="50"
                                autocomplete="username"
                                class="w-full rounded-xl border border-neutral-800 bg-black px-4 py-3 text-sm text-white transition-all placeholder-neutral-700 focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                            >
                            <p class="mt-1 text-[10px] text-neutral-600">3-50 characters</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="<?= htmlspecialchars($current_user['email']) ?>"
                                required
                                autocomplete="email"
                                class="w-full rounded-xl border border-neutral-800 bg-black px-4 py-3 text-sm text-white transition-all placeholder-neutral-700 focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                            >
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-black transition-colors hover:bg-neutral-200">
                        Save Profile Changes
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-neutral-800 bg-neutral-950 p-7 md:p-8">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-white">Change Password</h3>
                    <p class="mt-1 text-sm text-neutral-400">Update credentials regularly to harden your account.</p>
                </div>

                <form method="POST" action="/account" class="space-y-5">
                    <input type="hidden" name="action" value="change_password">

                    <div>
                        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">Current Password</label>
                        <input
                            type="password"
                            name="old_password"
                            required
                            autocomplete="current-password"
                            placeholder="********"
                            class="w-full rounded-xl border border-neutral-800 bg-black px-4 py-3 text-sm text-white transition-all placeholder-neutral-700 focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                        >
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">New Password</label>
                            <input
                                type="password"
                                name="new_password"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                placeholder="********"
                                class="w-full rounded-xl border border-neutral-800 bg-black px-4 py-3 text-sm text-white transition-all placeholder-neutral-700 focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                            >
                            <p class="mt-1 text-[10px] text-neutral-600">Minimum 6 characters</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-500">Confirm Password</label>
                            <input
                                type="password"
                                name="confirm_password"
                                required
                                minlength="6"
                                autocomplete="new-password"
                                placeholder="********"
                                class="w-full rounded-xl border border-neutral-800 bg-black px-4 py-3 text-sm text-white transition-all placeholder-neutral-700 focus:border-white focus:outline-none focus:ring-1 focus:ring-white"
                            >
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-black transition-colors hover:bg-neutral-200">
                        Update Password
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
    const accountPointsEl = document.getElementById('accountPoints');
    const currentUserId = <?= $current_user_id !== null ? (int)$current_user_id : 'null' ?>;

    if (window.EventSource && currentUserId !== null) {
        const source = new EventSource('/ctf/scoreboard-stream');
        source.addEventListener('leaderboard', (event) => {
            try {
                const payload = JSON.parse(event.data);
                const users = Array.isArray(payload.leaderboard) ? payload.leaderboard : [];
                const me = users.find(u => Number(u.id) === Number(currentUserId));
                if (me && accountPointsEl) {
                    accountPointsEl.textContent = me.points ?? 0;
                }
            } catch (err) {
                console.error('Account scoreboard parse error', err);
            }
        });
    }
</script>
