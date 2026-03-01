<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!is_logged_in()) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'record_simulation') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $scenario = trim($_POST['scenario'] ?? 'Unknown Scenario');
    $score = (int) ($_POST['score'] ?? 0);
    $duration = (int) ($_POST['duration'] ?? 0);
    $status = $score >= 70 ? 'passed' : 'failed';

    $saved = $user_id ? record_simulation_run($user_id, $scenario, $score, $status, $duration) : false;
    echo json_encode([
        'status' => $saved ? 'success' : 'error',
        'msg' => $saved ? 'Simulation run recorded.' : 'Failed to record simulation run.',
    ]);
    exit;
}

$recent_runs = $user_id ? get_simulation_runs($user_id, 8) : [];
?>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm inline-flex items-center gap-1">&larr; Back to Mission Control</a>
        <h1 class="text-3xl font-bold text-white mt-3">Real-Time Attack Simulation Lab</h1>
        <p class="text-neutral-400 mt-2">Run timed incident simulations and build response speed.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl text-white font-bold">Simulation Console</h2>
                <span id="simStatus" class="text-xs px-2 py-1 rounded bg-neutral-800 text-neutral-300">Idle</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <button class="scenario-btn border border-neutral-700 rounded-xl p-3 text-left hover:border-white transition-colors" data-scenario="Credential Stuffing Response">
                    <div class="text-white font-semibold text-sm">Credential Stuffing Response</div>
                    <div class="text-xs text-neutral-500 mt-1">Contain auth abuse and block bot burst.</div>
                </button>
                <button class="scenario-btn border border-neutral-700 rounded-xl p-3 text-left hover:border-white transition-colors" data-scenario="XSS Incident Triage">
                    <div class="text-white font-semibold text-sm">XSS Incident Triage</div>
                    <div class="text-xs text-neutral-500 mt-1">Validate payload path and patch filter gaps.</div>
                </button>
                <button class="scenario-btn border border-neutral-700 rounded-xl p-3 text-left hover:border-white transition-colors" data-scenario="Open Redirect Containment">
                    <div class="text-white font-semibold text-sm">Open Redirect Containment</div>
                    <div class="text-xs text-neutral-500 mt-1">Lock redirect policies and monitor token leakage.</div>
                </button>
            </div>

            <div class="mb-4">
                <div class="flex justify-between text-xs text-neutral-500 mb-1">
                    <span>Execution Progress</span>
                    <span id="progressText">0%</span>
                </div>
                <div class="h-2 bg-neutral-900 rounded-full overflow-hidden">
                    <div id="simProgress" class="h-full bg-white" style="width:0%"></div>
                </div>
            </div>

            <div id="simOutput" class="h-64 overflow-y-auto bg-black border border-neutral-800 rounded-xl p-4 font-mono text-xs text-neutral-400">
                // Select a scenario and click Start Simulation.
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button id="startBtn" class="px-4 py-2 bg-white text-black rounded-full font-semibold hover:bg-neutral-200">Start Simulation</button>
                <button id="resetBtn" class="px-4 py-2 border border-neutral-700 rounded-full text-neutral-300 hover:text-white hover:border-neutral-500">Reset</button>
                <div id="resultBadge" class="text-sm text-neutral-400">No run yet.</div>
            </div>
        </div>

        <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <h2 class="text-lg text-white font-bold mb-4">Recent Runs</h2>
            <div class="space-y-3 text-sm">
                <?php foreach ($recent_runs as $run): ?>
                    <div class="border border-neutral-800 rounded-xl p-3">
                        <div class="flex justify-between items-center">
                            <div class="text-white font-semibold"><?= htmlspecialchars($run['scenario']) ?></div>
                            <div class="<?= $run['status'] === 'passed' ? 'text-green-400' : 'text-red-400' ?>"><?= strtoupper($run['status']) ?></div>
                        </div>
                        <div class="text-neutral-500 text-xs mt-1">Score <?= (int) $run['score'] ?> · <?= (int) $run['duration_seconds'] ?>s</div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent_runs)): ?>
                    <div class="text-neutral-500">No simulations recorded yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    let activeScenario = 'Credential Stuffing Response';
    let running = false;

    const scenarioBtns = document.querySelectorAll('.scenario-btn');
    const output = document.getElementById('simOutput');
    const progressBar = document.getElementById('simProgress');
    const progressText = document.getElementById('progressText');
    const status = document.getElementById('simStatus');
    const resultBadge = document.getElementById('resultBadge');

    scenarioBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            scenarioBtns.forEach((b) => b.classList.remove('border-white'));
            btn.classList.add('border-white');
            activeScenario = btn.dataset.scenario;
        });
    });
    if (scenarioBtns.length > 0) scenarioBtns[0].classList.add('border-white');

    document.getElementById('resetBtn').addEventListener('click', () => {
        running = false;
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
        status.textContent = 'Idle';
        status.className = 'text-xs px-2 py-1 rounded bg-neutral-800 text-neutral-300';
        output.textContent = '// Select a scenario and click Start Simulation.';
        resultBadge.textContent = 'No run yet.';
    });

    document.getElementById('startBtn').addEventListener('click', async () => {
        if (running) return;
        running = true;
        const startedAt = Date.now();

        status.textContent = 'Running';
        status.className = 'text-xs px-2 py-1 rounded bg-yellow-900/40 text-yellow-300';
        output.textContent = `[SIM] ${activeScenario}\n[SIM] Initializing environment...`;

        const logs = [
            'Collecting baseline telemetry...',
            'Detecting suspicious indicators...',
            'Correlating attack timeline...',
            'Deploying containment rules...',
            'Verifying remediation status...'
        ];

        for (let i = 0; i < logs.length; i++) {
            await new Promise((r) => setTimeout(r, 700));
            const pct = Math.round(((i + 1) / logs.length) * 100);
            progressBar.style.width = `${pct}%`;
            progressText.textContent = `${pct}%`;
            output.textContent += `\n[STEP ${i + 1}] ${logs[i]}`;
            output.scrollTop = output.scrollHeight;
        }

        const score = Math.floor(65 + Math.random() * 35);
        const duration = Math.round((Date.now() - startedAt) / 1000);
        const passed = score >= 70;

        output.textContent += `\n\n[RESULT] Score: ${score}`;
        output.textContent += passed ? '\n[RESULT] Mission passed.' : '\n[RESULT] Mission failed.';

        status.textContent = passed ? 'Passed' : 'Failed';
        status.className = passed
            ? 'text-xs px-2 py-1 rounded bg-green-900/40 text-green-300'
            : 'text-xs px-2 py-1 rounded bg-red-900/40 text-red-300';

        resultBadge.textContent = `Last run: ${score} points in ${duration}s`;

        const formData = new FormData();
        formData.append('action', 'record_simulation');
        formData.append('scenario', activeScenario);
        formData.append('score', String(score));
        formData.append('duration', String(duration));

        try {
            await fetch(window.location.href, { method: 'POST', body: formData });
        } catch (err) {
            console.error(err);
        }

        running = false;
    });
</script>
