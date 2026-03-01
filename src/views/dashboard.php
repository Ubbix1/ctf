<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!isset($current_user) || !is_array($current_user)) {
    $current_user = get_current_user_data();
}

$leaderboard = get_leaderboard(50);
$display_name = is_array($current_user) ? ($current_user['username'] ?? 'Agent') : 'Agent';
$points = is_array($current_user) ? (int) ($current_user['points'] ?? 0) : 0;
$current_user_id = is_array($current_user) ? ($current_user['id'] ?? null) : null;

$rank = 1;
foreach ($leaderboard as $idx => $user) {
    if ($current_user_id !== null && (int) $user['id'] === (int) $current_user_id) {
        $rank = $idx + 1;
        break;
    }
}

$challenge_ids = $current_user_id !== null ? get_user_challenges($current_user_id) : [];
$solved_count = is_array($challenge_ids) ? count($challenge_ids) : 0;
$total_challenges = count(get_ctf_challenge_catalog());
$progress_pct = $total_challenges > 0 ? min(100, ($solved_count / $total_challenges) * 100) : 0;

$analytics = $current_user_id !== null ? get_user_performance_analytics($current_user_id) : [];
$mastery = $analytics['mastery'] ?? ['level' => 'Novice', 'mastery_score' => 0, 'accuracy' => 0];
$proctor = $current_user_id !== null ? get_user_proctoring_summary($current_user_id) : ['low' => 0, 'medium' => 0, 'high' => 0, 'recent' => []];
$adaptive = $current_user_id !== null ? get_adaptive_challenge_pathway($current_user_id, 2) : [];
$matchmaking = $current_user_id !== null ? get_matchmaking_candidates($current_user_id, 3) : [];

$attempt_trend = $analytics['attempt_trend'] ?? [];
$trend_max = 1;
foreach ($attempt_trend as $point) {
    if ((int) $point['total'] > $trend_max) {
        $trend_max = (int) $point['total'];
    }
}
?>

<div class="max-w-7xl mx-auto animate-fade-in-up">
    <div class="mb-10">
        <div class="bg-gradient-to-br from-neutral-950 via-black to-neutral-950 border border-neutral-800 rounded-2xl p-8 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 rounded-full bg-white/5 blur-3xl"></div>

            <div class="relative flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                <div>
                    <div class="text-xs text-neutral-500 uppercase tracking-widest mb-3">Advanced Platform</div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-3">Welcome back, <?= htmlspecialchars($display_name) ?></h1>
                    <p class="text-neutral-400 max-w-2xl">Dynamic difficulty, adaptive challenge paths, proctoring, and analytics are now active.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="/ctf" class="px-5 py-3 rounded-full bg-white text-black font-semibold hover:bg-neutral-200 transition-colors">Resume CTF</a>
                    <a href="/ctf/matchmaking" class="px-5 py-3 rounded-full border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-500 transition-colors">Skill Match Queue</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-12">
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <div class="text-[10px] text-neutral-500 uppercase tracking-widest mb-2">Your Points</div>
            <div class="text-3xl font-bold text-white"><?= $points ?></div>
        </div>
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <div class="text-[10px] text-neutral-500 uppercase tracking-widest mb-2">Global Rank</div>
            <div class="text-3xl font-bold text-white">#<?= $rank ?></div>
        </div>
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <div class="text-[10px] text-neutral-500 uppercase tracking-widest mb-2">Mastery Level</div>
            <div class="text-3xl font-bold text-white"><?= htmlspecialchars($mastery['level'] ?? 'Novice') ?></div>
        </div>
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <div class="text-[10px] text-neutral-500 uppercase tracking-widest mb-2">Accuracy</div>
            <div class="text-3xl font-bold text-white"><?= number_format((float) ($analytics['accuracy'] ?? 0), 1) ?>%</div>
        </div>
        <div class="bg-neutral-900 border border-neutral-800 rounded-2xl p-6">
            <div class="text-[10px] text-neutral-500 uppercase tracking-widest mb-2">Proctor Risk</div>
            <div class="text-3xl font-bold <?= (($proctor['high'] ?? 0) > 0) ? 'text-red-400' : 'text-green-400' ?>"><?= (($proctor['high'] ?? 0) > 0) ? 'High' : 'Low' ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
        <div class="lg:col-span-2 bg-neutral-950 border border-neutral-800 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">Performance Analytics</h2>
                <span class="text-xs text-neutral-500 uppercase tracking-widest">Mastery <?= number_format((float) ($mastery['mastery_score'] ?? 0), 1) ?></span>
            </div>
            <div class="h-2 bg-neutral-900 rounded-full overflow-hidden mb-6">
                <div class="h-full bg-white transition-all duration-700" style="width: <?= min(100, max(0, (float) ($mastery['mastery_score'] ?? 0))) ?>%"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="border border-neutral-800 rounded-xl p-4">
                    <div class="text-xs text-neutral-500 uppercase tracking-widest mb-2">Attempts</div>
                    <div class="text-white text-2xl font-bold"><?= (int) ($analytics['attempt_total'] ?? 0) ?></div>
                    <div class="text-xs text-neutral-500 mt-2">Successful: <?= (int) ($analytics['success_total'] ?? 0) ?></div>
                </div>
                <div class="border border-neutral-800 rounded-xl p-4">
                    <div class="text-xs text-neutral-500 uppercase tracking-widest mb-2">Hints Used</div>
                    <div class="text-white text-2xl font-bold"><?= (int) ($analytics['hints_used'] ?? 0) ?></div>
                    <div class="text-xs text-neutral-500 mt-2">Profile matches: <?= count($matchmaking) ?></div>
                </div>
            </div>

            <div>
                <div class="text-xs text-neutral-500 uppercase tracking-widest mb-3">7-Day Attempt Trend</div>
                <div class="grid grid-cols-7 gap-2 items-end h-24">
                    <?php foreach ($attempt_trend as $p):
                        $height = $trend_max > 0 ? max(8, (int) round(($p['total'] / $trend_max) * 90)) : 8;
                    ?>
                        <div class="flex flex-col items-center justify-end gap-1">
                            <div class="w-full bg-white/80 rounded" style="height: <?= $height ?>px"></div>
                            <div class="text-[10px] text-neutral-500"><?= substr($p['day'], 5) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Adaptive Next Moves</h2>
            <div class="space-y-3">
                <?php if (!empty($adaptive)): ?>
                    <?php foreach ($adaptive as $next): ?>
                        <a href="<?= htmlspecialchars($next['url']) ?>" class="block border border-neutral-800 rounded-xl p-3 hover:border-neutral-600 transition-colors">
                            <div class="text-white font-semibold"><?= htmlspecialchars($next['title']) ?></div>
                            <div class="text-xs text-neutral-500 mt-1"><?= htmlspecialchars($next['difficulty']) ?> · <?= (int) $next['dynamic_points'] ?> pts</div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-sm text-neutral-500">All challenges solved. Explore match queue and sandbox console modules.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Proctoring Events (7d)</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-neutral-400">High</span><span class="text-red-400 font-semibold"><?= (int) ($proctor['high'] ?? 0) ?></span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Medium</span><span class="text-yellow-400 font-semibold"><?= (int) ($proctor['medium'] ?? 0) ?></span></div>
                <div class="flex justify-between"><span class="text-neutral-400">Low</span><span class="text-blue-400 font-semibold"><?= (int) ($proctor['low'] ?? 0) ?></span></div>
            </div>
            <div class="mt-4 pt-4 border-t border-neutral-800 space-y-2 text-xs text-neutral-500">
                <?php foreach (($proctor['recent'] ?? []) as $event): ?>
                    <div>
                        <div class="text-neutral-300"><?= htmlspecialchars($event['event_type']) ?></div>
                        <div><?= htmlspecialchars($event['created_at']) ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($proctor['recent'])): ?>
                    <div>No recent proctoring events.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Match Queue Preview</h2>
            <div class="space-y-3">
                <?php foreach ($matchmaking as $match): ?>
                    <div class="border border-neutral-800 rounded-xl p-3">
                        <div class="flex justify-between items-center">
                            <div class="text-white font-semibold"><?= htmlspecialchars($match['username']) ?></div>
                            <div class="text-sm text-green-400"><?= (int) $match['compatibility'] ?>%</div>
                        </div>
                        <div class="text-xs text-neutral-500 mt-1"><?= (int) $match['points'] ?> pts · <?= (int) $match['solved'] ?> solved</div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($matchmaking)): ?>
                    <div class="text-sm text-neutral-500">No match candidates yet.</div>
                <?php endif; ?>
            </div>
            <a href="/ctf/matchmaking" class="inline-block mt-4 text-sm text-white border border-neutral-700 px-3 py-2 rounded-full hover:bg-neutral-900">Open Queue</a>
        </div>

        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Advanced Modules</h2>
            <div class="space-y-2 text-sm">
                <a href="/ctf/matchmaking" class="block text-neutral-300 hover:text-white">Skill Match Queue</a>
                <a href="/ctf/sandbox" class="block text-neutral-300 hover:text-white">Sandbox Command Console</a>
            </div>
        </div>
    </div>

    <div class="mb-12">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-white mb-2">Global Leaderboard</h2>
            <p class="text-neutral-400">Top performers across all CTF challenges</p>
        </div>

        <div class="bg-neutral-950 border border-neutral-800 rounded-2xl overflow-hidden">
            <div class="grid grid-cols-12 gap-4 bg-black border-b border-neutral-800 px-6 py-4 font-bold text-neutral-500 text-sm uppercase tracking-widest">
                <div class="col-span-1">Rank</div>
                <div class="col-span-4">Username</div>
                <div class="col-span-3">Solved</div>
                <div class="col-span-4">Points</div>
            </div>

            <div id="leaderboardBody">
            <?php foreach ($leaderboard as $idx => $user):
                $r = $idx + 1;
                $is_current = ($current_user_id !== null) && ((int) $user['id'] === (int) $current_user_id);
                $bg = $is_current ? 'bg-neutral-900/50 border-l-4 border-l-white' : 'hover:bg-neutral-900/20';
            ?>
                <div class="grid grid-cols-12 gap-4 <?= $bg ?> px-6 py-4 border-b border-neutral-800/50 items-center transition-colors last:border-b-0">
                    <div class="col-span-1"><span class="text-white font-semibold">#<?= $r ?></span></div>
                    <div class="col-span-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-white to-neutral-300 flex items-center justify-center text-xs font-bold text-black">
                                <?= strtoupper(substr($user['username'], 0, 1)) ?>
                            </div>
                            <span class="text-white font-medium <?= $is_current ? 'font-bold' : '' ?>">
                                <?= htmlspecialchars($user['username']) ?>
                                <?php if ($is_current): ?><span class="text-[10px] text-neutral-500">(You)</span><?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-span-3"><span class="text-white font-semibold"><?= (int) ($user['challenges_solved'] ?? 0) ?></span></div>
                    <div class="col-span-4"><span class="text-white font-bold text-lg"><?= (int) $user['points'] ?></span></div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const leaderboardBody = document.getElementById('leaderboardBody');

    function renderLeaderboard(payload) {
        if (!payload || !Array.isArray(payload.leaderboard) || !leaderboardBody) return;
        const currentUserId = payload.current_user_id;

        const rows = payload.leaderboard.map((user, idx) => {
            const rank = idx + 1;
            const isCurrent = currentUserId !== null && Number(user.id) === Number(currentUserId);
            const bgClass = isCurrent ? 'bg-neutral-900/50 border-l-4 border-l-white' : 'hover:bg-neutral-900/20';
            const solved = Number(user.challenges_solved || 0);

            return `
                <div class="grid grid-cols-12 gap-4 ${bgClass} px-6 py-4 border-b border-neutral-800/50 items-center transition-colors last:border-b-0">
                    <div class="col-span-1"><span class="text-white font-semibold">#${rank}</span></div>
                    <div class="col-span-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-white to-neutral-300 flex items-center justify-center text-xs font-bold text-black">
                                ${String(user.username || '?').slice(0, 1).toUpperCase()}
                            </div>
                            <span class="text-white font-medium ${isCurrent ? 'font-bold' : ''}">
                                ${user.username || 'Unknown'} ${isCurrent ? '<span class="text-[10px] text-neutral-500">(You)</span>' : ''}
                            </span>
                        </div>
                    </div>
                    <div class="col-span-3"><span class="text-white font-semibold">${solved}</span></div>
                    <div class="col-span-4"><span class="text-white font-bold text-lg">${Number(user.points || 0)}</span></div>
                </div>
            `;
        });

        leaderboardBody.innerHTML = rows.join('');
    }

    if (window.EventSource) {
        const source = new EventSource('/ctf/scoreboard-stream');
        source.addEventListener('leaderboard', (event) => {
            try {
                const payload = JSON.parse(event.data);
                renderLeaderboard(payload);
            } catch (err) {
                console.error('Scoreboard parse error', err);
            }
        });
    }
</script>

