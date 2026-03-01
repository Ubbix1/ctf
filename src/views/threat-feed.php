<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

$feed = get_threat_intel_feed();
$severity_badge = [
    'Critical' => 'bg-red-900/30 text-red-300 border-red-800',
    'High' => 'bg-orange-900/30 text-orange-300 border-orange-800',
    'Medium' => 'bg-yellow-900/30 text-yellow-300 border-yellow-800',
    'Low' => 'bg-blue-900/30 text-blue-300 border-blue-800',
];
?>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm inline-flex items-center gap-1">&larr; Back to Mission Control</a>
        <h1 class="text-3xl font-bold text-white mt-3">Advanced Threat Intelligence Feed</h1>
        <p class="text-neutral-400 mt-2">Live-style intelligence snapshots to guide challenge strategy and detection mindset.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <?php foreach ($feed as $item):
            $badge = $severity_badge[$item['severity']] ?? 'bg-neutral-900 text-neutral-300 border-neutral-700';
        ?>
            <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-2 py-1 rounded border <?= $badge ?>"><?= htmlspecialchars($item['severity']) ?></span>
                        <span class="text-xs text-neutral-500 font-mono"><?= htmlspecialchars($item['id']) ?></span>
                    </div>
                    <div class="text-xs text-neutral-500">Updated: <?= htmlspecialchars($item['updated_at']) ?></div>
                </div>
                <h3 class="text-xl text-white font-bold mb-2"><?= htmlspecialchars($item['title']) ?></h3>
                <p class="text-neutral-400 mb-4"><?= htmlspecialchars($item['summary']) ?></p>
                <div class="flex flex-wrap gap-3 text-xs text-neutral-500">
                    <span>Source: <?= htmlspecialchars($item['source']) ?></span>
                    <span>Vector: <?= htmlspecialchars($item['vector']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
