<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!is_logged_in()) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$candidates = $user_id ? get_matchmaking_candidates($user_id, 12) : [];
?>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm inline-flex items-center gap-1">&larr; Back to Mission Control</a>
        <h1 class="text-3xl font-bold text-white mt-3">Skill Match Queue</h1>
        <p class="text-neutral-400 mt-2">Get ranked peers by compatibility and queue focused practice matches.</p>
    </div>

    <div class="border border-neutral-800 bg-neutral-950 rounded-2xl overflow-hidden">
        <div class="grid grid-cols-12 gap-4 bg-black border-b border-neutral-800 px-6 py-4 font-bold text-neutral-500 text-sm uppercase tracking-widest">
            <div class="col-span-4">Agent</div>
            <div class="col-span-2">Compatibility</div>
            <div class="col-span-2">Points</div>
            <div class="col-span-2">Solved</div>
            <div class="col-span-2">Action</div>
        </div>

        <?php foreach ($candidates as $c): ?>
            <div class="grid grid-cols-12 gap-4 px-6 py-4 border-b border-neutral-800/50 items-center last:border-b-0 hover:bg-neutral-900/20">
                <div class="col-span-4">
                    <div class="text-white font-semibold"><?= htmlspecialchars($c['username']) ?></div>
                    <div class="text-xs text-neutral-500 mt-1"><?= htmlspecialchars($c['reason']) ?></div>
                </div>
                <div class="col-span-2">
                    <span class="text-green-400 font-bold"><?= (int) $c['compatibility'] ?>%</span>
                </div>
                <div class="col-span-2 text-white"><?= (int) $c['points'] ?></div>
                <div class="col-span-2 text-white"><?= (int) $c['solved'] ?></div>
                <div class="col-span-2">
                    <button type="button" class="px-3 py-2 text-xs border border-neutral-700 rounded-lg text-neutral-300 hover:text-white hover:border-neutral-500">Queue</button>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($candidates)): ?>
            <div class="p-6 text-neutral-500">No compatible players found yet.</div>
        <?php endif; ?>
    </div>
</div>

