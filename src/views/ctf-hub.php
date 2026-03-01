<?php
// src/views/ctf-hub.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!isset($current_user) || !is_array($current_user)) {
    $current_user = get_current_user_data();
}
$current_user_id = is_array($current_user) ? ($current_user['id'] ?? null) : null;

$duration = $_SESSION['ctf_duration'] ?? 3600;
if (!isset($_SESSION['ctf_active'])) {
    $_SESSION['ctf_active'] = false;
}

if (!function_exists('stop_active_ctf_timer_as_completed')) {
function stop_active_ctf_timer_as_completed() {
    $_SESSION['ctf_active'] = false;
    $_SESSION['ctf_completed_at'] = time();
    $_SESSION['ctf_end'] = time();

    if (isset($_SESSION['ctf_session_id'])) {
        if (function_exists('complete_ctf_session')) {
            complete_ctf_session($_SESSION['ctf_session_id']);
        } else {
            expire_ctf_session($_SESSION['ctf_session_id']);
        }
        unset($_SESSION['ctf_session_id']);
    }
}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_ctf_progress') {
    if ($current_user_id === null) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Please log in to reset your labs.',
        ];
        header('Location: /login');
        exit;
    }

    $reset_ok = reset_user_ctf_progress($current_user_id);

    $_SESSION['ctf_active'] = false;
    $_SESSION['ctf_solved'] = [];
    unset($_SESSION['ctf_start'], $_SESSION['ctf_end'], $_SESSION['ctf_completed_at'], $_SESSION['ctf_expired_at'], $_SESSION['ctf_session_id']);

    $_SESSION['flash'] = [
        'type' => $reset_ok ? 'success' : 'error',
        'message' => $reset_ok
            ? 'CTF progress reset. You can start again from zero.'
            : 'Could not reset progress right now. Please try again.',
    ];
    header('Location: /ctf');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start_ctf') {
    if ($current_user_id !== null) {
        $already_solved = get_user_challenges($current_user_id);
        $catalog_size = count(get_ctf_challenge_catalog());
        if ($catalog_size > 0 && is_array($already_solved) && count($already_solved) >= $catalog_size) {
            $_SESSION['ctf_solved'] = $already_solved;
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'All labs already completed. Timer remains stopped.',
            ];
            header('Location: /ctf');
            exit;
        }
    }

    $_SESSION['ctf_active'] = true;
    $_SESSION['ctf_start'] = time();
    $_SESSION['ctf_end'] = $_SESSION['ctf_start'] + $duration;

    if ($current_user_id !== null) {
        $session_id = start_ctf_session($current_user_id, $duration);
        if ($session_id) {
            $_SESSION['ctf_session_id'] = $session_id;
        }
        $_SESSION['ctf_solved'] = get_user_challenges($current_user_id);
    }

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'CTF started. Your timer is running.',
    ];
    header('Location: /ctf');
    exit;
}

$remaining = 0;
if (!empty($_SESSION['ctf_active']) && isset($_SESSION['ctf_end'])) {
    $remaining = max(0, $_SESSION['ctf_end'] - time());
    if ($remaining <= 0) {
        $_SESSION['ctf_active'] = false;
        $_SESSION['ctf_expired_at'] = time();
        if (isset($_SESSION['ctf_session_id'])) {
            expire_ctf_session($_SESSION['ctf_session_id']);
            unset($_SESSION['ctf_session_id']);
        }
    }
}

if (!isset($_SESSION['ctf_solved'])) $_SESSION['ctf_solved'] = [];
$challenge_ids = $_SESSION['ctf_solved'];
if ($current_user_id !== null) {
    $challenge_ids = get_user_challenges($current_user_id);
    $_SESSION['ctf_solved'] = $challenge_ids;
}

$catalog = get_ctf_challenge_catalog();
$total_challenges = count($catalog);
$solved_count = is_array($challenge_ids) ? count($challenge_ids) : 0;
$progress = $total_challenges > 0 ? (($solved_count / $total_challenges) * 100) : 0;
$all_labs_completed = $total_challenges > 0 && $solved_count >= $total_challenges;

if ($all_labs_completed && !empty($_SESSION['ctf_active'])) {
    stop_active_ctf_timer_as_completed();
    $remaining = 0;
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'All labs completed. Timer stopped.',
    ];
}

$mastery = $current_user_id !== null ? get_user_mastery_profile($current_user_id) : ['level' => 'Novice', 'mastery_score' => 0, 'accuracy' => 0];
$adaptive_pathway = $current_user_id !== null ? get_adaptive_challenge_pathway($current_user_id, 3) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_hint') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if (empty($_SESSION['ctf_active'])) {
        echo json_encode(['status' => 'error', 'msg' => 'CTF is not active.']);
        exit;
    }
    if ($current_user_id === null) {
        echo json_encode(['status' => 'error', 'msg' => 'Please log in first.']);
        exit;
    }

    $challenge_id = normalize_ctf_challenge_id($_POST['id'] ?? '');
    $hint = unlock_next_challenge_hint($current_user_id, $challenge_id);

    if (($hint['status'] ?? '') === 'success') {
        echo json_encode([
            'status' => 'success',
            'msg' => 'Hint unlocked.',
            'hint' => $hint['hint'],
            'tier' => $hint['tier'],
            'total' => $hint['total'],
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'msg' => $hint['message'] ?? 'Could not unlock hint.',
        ]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_flag') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    if (empty($_SESSION['ctf_active'])) {
        echo json_encode(['status' => 'error', 'msg' => 'CTF is not active. Click "Start CTF" to begin.']);
        exit;
    }
    if ($current_user_id === null) {
        echo json_encode(['status' => 'error', 'msg' => 'Please log in to submit flags.']);
        exit;
    }

    $challenge_id = normalize_ctf_challenge_id($_POST['id'] ?? '');
    $flag = strtolower(trim($_POST['flag'] ?? ''));

    $flag_check = validate_ctf_flag_submission($challenge_id, $flag);
    $challenge_id = $flag_check['challenge_id'];
    $is_valid = !empty($flag_check['valid']);
    log_ctf_attempt($current_user_id, $challenge_id, $flag, $is_valid);

    if ($is_valid) {
        $points = get_dynamic_challenge_points($current_user_id, $challenge_id);
        $recorded = record_challenge_completion($current_user_id, $challenge_id, $points);
        if ($recorded && !in_array($challenge_id, $_SESSION['ctf_solved'])) {
            $_SESSION['ctf_solved'][] = $challenge_id;
        }
        $latest_solved = get_user_challenges($current_user_id);
        if (is_array($latest_solved)) {
            $_SESSION['ctf_solved'] = $latest_solved;
        }

        $latest_count = is_array($latest_solved) ? count($latest_solved) : (is_array($_SESSION['ctf_solved']) ? count($_SESSION['ctf_solved']) : 0);
        $all_completed_now = $total_challenges > 0 && $latest_count >= $total_challenges;
        if ($all_completed_now && !empty($_SESSION['ctf_active'])) {
            stop_active_ctf_timer_as_completed();
        }

        $success_msg = $recorded ? ('Correct! +' . $points . ' points awarded.') : 'Already solved. No extra points.';
        if ($all_completed_now) {
            $success_msg .= ' All labs completed. Timer stopped.';
        }

        echo json_encode([
            'status' => 'success',
            'msg' => $success_msg,
            'all_completed' => $all_completed_now,
        ]);
    } else {
        $hint_prompt = get_hint_prompt_for_failures($current_user_id, $challenge_id);
        echo json_encode([
            'status' => 'error',
            'msg' => 'Access Denied: Invalid Flag',
            'hint_prompt' => $hint_prompt['can_prompt'],
            'hint_remaining' => $hint_prompt['remaining'],
            'fail_count' => $hint_prompt['fails'],
        ]);
    }
    exit;
}

function renderCard($id, $title, $desc, $link, $diff, $points, $ctf_active, $current_user_id) {
    $is_solved = in_array($id, $_SESSION['ctf_solved']);
    $status_color = $is_solved ? 'border-green-900/50 bg-green-900/10' : 'border-neutral-800 bg-neutral-950';
    $icon_color = $is_solved ? 'text-green-500' : 'text-neutral-600';
    $disabled = !$ctf_active;
    $hint_used = $current_user_id !== null ? get_hint_progress($current_user_id, $id) : 0;

    echo "
    <div class='group relative border $status_color rounded-xl p-6 transition-all duration-300 flex flex-col " . ($disabled ? "opacity-50" : "hover:border-neutral-600") . "'>
        <div class='flex justify-between items-start mb-4'>
            <div class='p-3 bg-neutral-900 rounded-lg $icon_color'>
                <svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'></path></svg>
            </div>
            <span class='text-xs font-mono px-2 py-1 rounded bg-neutral-900 text-neutral-400'>$diff</span>
        </div>

        <h3 class='text-lg font-bold text-white mb-2'>$title</h3>
        <p class='text-neutral-400 text-sm mb-2 flex-grow'>$desc</p>
        <div class='text-xs text-neutral-500 mb-5'>Dynamic Reward: <span class='text-white font-semibold'>$points pts</span> · Hints used: $hint_used</div>

        " . ($is_solved ? "
            <div class='mt-auto py-3 text-center text-green-500 font-bold bg-green-900/20 rounded-lg border border-green-900/30'>
                ACCESS GRANTED
            </div>
        " : ($disabled ? "
            <div class='mt-auto py-3 text-center text-neutral-500 font-bold bg-neutral-900/40 rounded-lg border border-neutral-800'>
                LAB CLOSED
            </div>
        " : "
            <div class='mt-auto space-y-3'>
                <a href='$link' class='block w-full text-center py-2 border border-neutral-700 rounded-lg text-sm text-white hover:bg-neutral-800 transition-colors'>
                    Launch Tool
                </a>
                <form onsubmit='submitFlag(event, \"$id\")' class='relative'>
                    <input type='text' name='flag' placeholder='Enter flag...' class='w-full bg-black border border-neutral-800 rounded-lg py-2 pl-3 pr-10 text-xs text-white focus:border-white focus:outline-none'>
                    <button type='submit' class='absolute right-2 top-2 text-neutral-500 hover:text-white'>
                        <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 5l7 7-7 7M5 5l7 7-7 7'></path></svg>
                    </button>
                </form>
                <button type='button' onclick='requestHint(\"$id\")' class='w-full text-xs py-2 rounded-lg border border-neutral-700 text-neutral-300 hover:text-white hover:border-neutral-500 transition-colors'>
                    Request Hint
                </button>
                <div id='hint-$id' class='hidden text-xs border border-blue-800 bg-blue-900/20 text-blue-200 rounded-lg p-3'></div>
            </div>
        ")) . "
    </div>";
}
?>

<div class="max-w-7xl mx-auto animate-fade-in-up">
    <div id="systemMessage" class="hidden mb-6 p-4 rounded-lg text-sm"></div>

    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Operation: Plexaur</h1>
            <p class="text-neutral-400">Infiltrate the system. Capture the flags. Leave no trace.</p>
        </div>
        <div class="flex gap-4 bg-neutral-900 border border-neutral-800 p-2 rounded-xl">
            <div class="px-4 py-2 border-r border-neutral-800">
                <div class="text-[10px] text-neutral-500 uppercase tracking-widest">Time Remaining</div>
                <div id="countdown" class="text-xl font-mono text-white font-bold">00:00:00</div>
            </div>
            <div class="px-4 py-2 border-r border-neutral-800">
                <div class="text-[10px] text-neutral-500 uppercase tracking-widest">Mastery Level</div>
                <div class="text-xl font-mono text-white font-bold"><?= htmlspecialchars($mastery['level']) ?></div>
            </div>
            <div class="px-4 py-2">
                <div class="text-[10px] text-neutral-500 uppercase tracking-widest">Access</div>
                <div class="text-xl font-mono text-white font-bold"><?= $solved_count ?>/<?= $total_challenges ?></div>
            </div>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
        <div class="mb-8 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-900/20 border border-green-800 text-green-400' : 'bg-red-900/20 border border-red-800 text-red-400' ?>">
            <p class="text-sm"><?= htmlspecialchars($flash['message']) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($solved_count > 0): ?>
        <div class="mb-8 border border-neutral-800 bg-neutral-950 rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-white mb-1">Need a Fresh Run?</h2>
                <p class="text-neutral-400 text-sm">Reset will clear completed labs and set your points to 0 so you can replay from scratch.</p>
            </div>
            <form method="POST" action="/ctf" class="shrink-0" onsubmit="return confirm('Reset all completed labs and points to 0?');">
                <input type="hidden" name="action" value="reset_ctf_progress">
                <button type="submit" class="px-5 py-3 rounded-full border border-red-700 text-red-300 hover:bg-red-900/20 transition-colors font-semibold">
                    Reset Labs
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['ctf_active'])): ?>
        <div class="mb-10 border border-neutral-800 bg-neutral-950 rounded-2xl p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <?php if ($all_labs_completed): ?>
                    <h2 class="text-xl font-bold text-white mb-1">Mission Complete</h2>
                    <p class="text-neutral-400 text-sm">All labs solved. Timer has been stopped automatically.</p>
                <?php else: ?>
                    <h2 class="text-xl font-bold text-white mb-1">CTF Lab Closed</h2>
                    <p class="text-neutral-400 text-sm">Click "Start CTF" to open the lab and begin the timer.</p>
                <?php endif; ?>
            </div>
            <?php if (!$all_labs_completed): ?>
                <form method="POST" action="/ctf" class="shrink-0">
                    <input type="hidden" name="action" value="start_ctf">
                    <button type="submit" class="px-6 py-3 bg-white text-black rounded-full font-semibold hover:bg-neutral-200 transition-colors">Start CTF</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <div class="flex justify-between text-xs text-neutral-500 mb-2 uppercase tracking-widest">
                <span>Infiltration Progress</span>
                <span><?= number_format($progress, 0) ?>%</span>
            </div>
            <div class="h-2 bg-neutral-900 rounded-full overflow-hidden mb-6">
                <div class="h-full bg-white transition-all duration-1000 ease-out" style="width: <?= $progress ?>%"></div>
            </div>

            <h3 class="text-lg font-bold text-white mb-4">Adaptive Pathway</h3>
            <div class="space-y-3">
                <?php if (!empty($adaptive_pathway)): ?>
                    <?php foreach ($adaptive_pathway as $next): ?>
                        <a href="<?= htmlspecialchars($next['url']) ?>" class="block border border-neutral-800 rounded-xl p-4 hover:border-neutral-600 transition-colors">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <div class="text-white font-semibold"><?= htmlspecialchars($next['title']) ?></div>
                                    <div class="text-xs text-neutral-500 mt-1"><?= htmlspecialchars($next['reason']) ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-neutral-400"><?= htmlspecialchars($next['difficulty']) ?></div>
                                    <div class="text-sm text-white font-semibold"><?= (int) $next['dynamic_points'] ?> pts</div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-neutral-500">No unsolved challenges left. Great run.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Advanced Modules</h3>
            <div class="space-y-2 text-sm">
                <a href="/ctf/matchmaking" class="block text-neutral-300 hover:text-white">Skill Match Queue</a>
                <a href="/ctf/sandbox" class="block text-neutral-300 hover:text-white">Sandbox Command Console</a>
            </div>
            <div class="mt-4 text-xs text-neutral-500">Mastery Score: <?= number_format((float) ($mastery['mastery_score'] ?? 0), 1) ?> · Accuracy: <?= number_format((float) ($mastery['accuracy'] ?? 0), 1) ?>%</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $ctf_active = !empty($_SESSION['ctf_active']);
        renderCard('caesar', 'Caesar Cipher', 'Intercepted comms detected. Decrypt the message shift by shift.', '/ctf/caesar', 'Easy', get_dynamic_challenge_points($current_user_id, 'caesar'), $ctf_active, $current_user_id);
        renderCard('meta', 'Metadata Analysis', 'Hidden data lies within the files. Extract the EXIF tags.', '/ctf/meta', 'Easy', get_dynamic_challenge_points($current_user_id, 'meta'), $ctf_active, $current_user_id);
        renderCard('base64', 'Base64 Decode', 'Standard encoding used for obfuscation. Decode the payload.', '/ctf/base64', 'Easy', get_dynamic_challenge_points($current_user_id, 'base64'), $ctf_active, $current_user_id);
        renderCard('redirect', 'Open Redirect', 'The login page redirects to an unsafe location. Exploit it.', '/ctf/redirect', 'Medium', get_dynamic_challenge_points($current_user_id, 'redirect'), $ctf_active, $current_user_id);
        renderCard('pass', 'Password Crack', 'Hash leaked. Brute force the password from the wordlist.', '/ctf/password', 'Medium', get_dynamic_challenge_points($current_user_id, 'pass'), $ctf_active, $current_user_id);
        renderCard('ports', 'Open Ports', 'Scan the target IP. Identify the vulnerable service.', '/ctf/ports', 'Medium', get_dynamic_challenge_points($current_user_id, 'ports'), $ctf_active, $current_user_id);
        renderCard('xss', 'XSS Injection', 'Inject malicious script into the feedback form.', '/ctf/xss', 'Hard', get_dynamic_challenge_points($current_user_id, 'xss'), $ctf_active, $current_user_id);
        renderCard('md5', 'MD5 Cracking', 'Reverse the hash. Find the original string.', '/ctf/md5', 'Hard', get_dynamic_challenge_points($current_user_id, 'md5'), $ctf_active, $current_user_id);
        renderCard('desync', 'desync', 'HTTP request smuggling through proxy/backend desync to internal admin flag endpoint.', '/ctf/desync', 'Extreme', get_dynamic_challenge_points($current_user_id, 'desync'), $ctf_active, $current_user_id);
        renderCard('blind', 'blind', 'Blind command injection with out-of-band exfiltration and no direct output.', '/ctf/blind', 'Extreme', get_dynamic_challenge_points($current_user_id, 'blind'), $ctf_active, $current_user_id);
        renderCard('chain', 'chain', 'Foothold to root through a full Linux privilege escalation attack path.', '/ctf/chain', 'Extreme', get_dynamic_challenge_points($current_user_id, 'chain'), $ctf_active, $current_user_id);
        renderCard('pickle', 'pickle', 'Exploit Python pickle deserialization in session cookie workflow.', '/ctf/pickle', 'Extreme', get_dynamic_challenge_points($current_user_id, 'pickle'), $ctf_active, $current_user_id);
        renderCard('c2', 'c2', 'Memory forensics beacon hunt with process-network correlation.', '/ctf/c2', 'Extreme', get_dynamic_challenge_points($current_user_id, 'c2'), $ctf_active, $current_user_id);
        ?>
    </div>
</div>

<script>
    let timeLeft = <?= (int) $remaining ?>;
    const timerEl = document.getElementById('countdown');

    function renderCountdown() {
        if (!timerEl) return;
        const safeTime = Math.max(0, timeLeft);
        const h = Math.floor(safeTime / 3600).toString().padStart(2, '0');
        const m = Math.floor((safeTime % 3600) / 60).toString().padStart(2, '0');
        const s = (safeTime % 60).toString().padStart(2, '0');
        timerEl.innerText = `${h}:${m}:${s}`;
    }

    renderCountdown();

    setInterval(() => {
        if (!timerEl) return;
        if (timeLeft <= 0) return;
        timeLeft--;
        renderCountdown();
    }, 1000);

    function showSystemMessage(msg, type = 'error') {
        const el = document.getElementById('systemMessage');
        if (!el) return;
        el.classList.remove('hidden', 'bg-red-900/20', 'border-red-800', 'text-red-300', 'bg-green-900/20', 'border-green-800', 'text-green-300', 'bg-blue-900/20', 'border-blue-800', 'text-blue-300');
        if (type === 'success') {
            el.classList.add('bg-green-900/20', 'border', 'border-green-800', 'text-green-300');
        } else if (type === 'info') {
            el.classList.add('bg-blue-900/20', 'border', 'border-blue-800', 'text-blue-300');
        } else {
            el.classList.add('bg-red-900/20', 'border', 'border-red-800', 'text-red-300');
        }
        el.textContent = msg;
    }

    async function submitFlag(e, id) {
        e.preventDefault();
        const form = e.target;
        const input = form.querySelector('input');
        const btn = form.querySelector('button');
        const originalBtnHTML = btn.innerHTML;

        const flag = input.value;
        if (!flag) return;

        btn.innerHTML = '<div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

        try {
            const formData = new FormData();
            formData.append('action', 'submit_flag');
            formData.append('id', id);
            formData.append('flag', flag);

            const res = await fetch(window.location.href, { method: 'POST', body: formData });
            const data = await res.json();

            if (data.status === 'success') {
                input.classList.add('border-green-500', 'text-green-500');
                showSystemMessage(data.msg || 'Correct flag submitted.', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                form.classList.add('animate-shake');
                input.classList.add('border-red-500', 'text-red-500');
                showSystemMessage(data.msg || 'Invalid flag.');
                if (data.hint_prompt) {
                    showSystemMessage(`Invalid flag. Hint available (${data.hint_remaining} remaining).`, 'info');
                }
                setTimeout(() => {
                    form.classList.remove('animate-shake');
                    input.classList.remove('border-red-500', 'text-red-500');
                }, 500);
            }
        } catch (err) {
            console.error(err);
            showSystemMessage('Submission failed. Try again.');
        } finally {
            btn.innerHTML = originalBtnHTML;
        }
    }

    async function requestHint(id) {
        try {
            const formData = new FormData();
            formData.append('action', 'request_hint');
            formData.append('id', id);

            const res = await fetch(window.location.href, { method: 'POST', body: formData });
            const data = await res.json();

            if (data.status !== 'success') {
                showSystemMessage(data.msg || 'No hint available right now.');
                return;
            }

            const hintBox = document.getElementById(`hint-${id}`);
            if (hintBox) {
                hintBox.classList.remove('hidden');
                hintBox.innerHTML = `<span class="font-semibold">Hint ${data.tier}/${data.total}:</span> ${data.hint}`;
            }
            showSystemMessage('Hint unlocked.', 'info');
        } catch (err) {
            console.error(err);
            showSystemMessage('Hint request failed.');
        }
    }
</script>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
</style>




