<?php
// src/views/ctf-hub.php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Session & Timer Logic
$duration = 3600; // 1 Hour
if (!isset($_SESSION['ctf_start'])) $_SESSION['ctf_start'] = time();
$elapsed = time() - $_SESSION['ctf_start'];
$remaining = max(0, $duration - $elapsed);

if ($remaining <= 0) {
    // Session Expired Logic (Reset or Lock)
    // For now, let's just reset for infinite play
    $_SESSION['ctf_start'] = time();
    $remaining = $duration;
}

// 2. Solved State Tracking
if (!isset($_SESSION['ctf_solved'])) $_SESSION['ctf_solved'] = [];
$total_challenges = 8;
$solved_count = count($_SESSION['ctf_solved']);
$progress = ($solved_count / $total_challenges) * 100;

// 3. Handle AJAX Flag Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_flag') {
    // Clear buffer if any
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $challenge_id = $_POST['id'] ?? '';
    $flag = strtolower(trim($_POST['flag'] ?? ''));

    // Master Key (In production, move to DB/Env)
    $flags = [
        'caesar'    => 'flag{plexaur_caesar}',
        'meta'      => 'flag{plexaur_meta_data}',
        'base64'    => 'flag{plexaur_decoded_successfully}',
        'redirect'  => 'flag{plexaur_redirect_caught}',
        'pass'      => 'flag{plexaur_password_found}',
        'ports'     => 'flag{plexaur_ports_opened}',
        'xss'       => 'flag{plexaur_xss_caught}',
        'md5'       => 'flag{plexaur_md5_qwerty_cracked}'
    ];

    if (isset($flags[$challenge_id]) && $flag === $flags[$challenge_id]) {
        if (!in_array($challenge_id, $_SESSION['ctf_solved'])) {
            $_SESSION['ctf_solved'][] = $challenge_id;
        }
        echo json_encode(['status' => 'success', 'msg' => 'Correct! System unlocked.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Access Denied: Invalid Flag']);
    }
    exit;
}
?>

<div class="max-w-7xl mx-auto animate-fade-in-up">
    
    <!-- Mission Header -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white mb-2">Operation: Plexaur</h1>
            <p class="text-neutral-400">Infiltrate the system. Capture the flags. Leave no trace.</p>
        </div>
        
        <!-- Stats Board -->
        <div class="flex gap-4 bg-neutral-900 border border-neutral-800 p-2 rounded-xl">
            <div class="px-4 py-2 border-r border-neutral-800">
                <div class="text-[10px] text-neutral-500 uppercase tracking-widest">Time Remaining</div>
                <div id="countdown" class="text-xl font-mono text-white font-bold">00:00:00</div>
            </div>
            <div class="px-4 py-2">
                <div class="text-[10px] text-neutral-500 uppercase tracking-widest">System Access</div>
                <div class="text-xl font-mono text-white font-bold"><?= $solved_count ?>/<?= $total_challenges ?></div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-12">
        <div class="flex justify-between text-xs text-neutral-500 mb-2 uppercase tracking-widest">
            <span>Infiltration Progress</span>
            <span><?= number_format($progress, 0) ?>%</span>
        </div>
        <div class="h-2 bg-neutral-900 rounded-full overflow-hidden">
            <div class="h-full bg-white transition-all duration-1000 ease-out" style="width: <?= $progress ?>%"></div>
        </div>
    </div>

    <!-- Challenge Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <?php
        // Helper to render card
        function renderCard($id, $title, $desc, $link, $diff = 'Easy') {
            $is_solved = in_array($id, $_SESSION['ctf_solved']);
            $status_color = $is_solved ? 'border-green-900/50 bg-green-900/10' : 'border-neutral-800 bg-neutral-950';
            $icon_color = $is_solved ? 'text-green-500' : 'text-neutral-600';
            
            echo "
            <div class='group relative border $status_color rounded-xl p-6 hover:border-neutral-600 transition-all duration-300 flex flex-col'>
                <div class='flex justify-between items-start mb-4'>
                    <div class='p-3 bg-neutral-900 rounded-lg $icon_color'>
                        <svg class='w-6 h-6' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'></path></svg>
                    </div>
                    <span class='text-xs font-mono px-2 py-1 rounded bg-neutral-900 text-neutral-400'>$diff</span>
                </div>
                
                <h3 class='text-lg font-bold text-white mb-2'>$title</h3>
                <p class='text-neutral-400 text-sm mb-6 flex-grow'>$desc</p>
                
                " . ($is_solved ? "
                    <div class='mt-auto py-3 text-center text-green-500 font-bold bg-green-900/20 rounded-lg border border-green-900/30'>
                        ACCESS GRANTED
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
                    </div>
                ") . "
            </div>";
        }

        renderCard('caesar', 'Caesar Cipher', 'Intercepted comms detected. Decrypt the message shift by shift.', '/ctf/caesar', 'Easy');
        renderCard('meta', 'Metadata Analysis', 'Hidden data lies within the files. Extract the EXIF tags.', '/ctf/meta', 'Easy');
        renderCard('base64', 'Base64 Decode', 'Standard encoding used for obfuscation. Decode the payload.', '/ctf/base64', 'Easy');
        renderCard('redirect', 'Open Redirect', 'The login page redirects to an unsafe location. Exploit it.', '/ctf/redirect', 'Medium');
        renderCard('pass', 'Password Crack', 'Hash leaked. Brute force the password from the wordlist.', '/ctf/password', 'Medium');
        renderCard('ports', 'Open Ports', 'Scan the target IP. Identify the vulnerable service.', '/ctf/ports', 'Medium');
        renderCard('xss', 'XSS Injection', 'Inject malicious script into the feedback form.', '/ctf/xss', 'Hard');
        renderCard('md5', 'MD5 Cracking', 'Reverse the hash. Find the original string.', '/ctf/md5', 'Hard');
        ?>
        
    </div>
</div>

<script>
    // 1. Timer Logic
    let timeLeft = <?= $remaining ?>;
    const timerEl = document.getElementById('countdown');
    
    setInterval(() => {
        if(timeLeft <= 0) return;
        timeLeft--;
        
        const h = Math.floor(timeLeft / 3600).toString().padStart(2, '0');
        const m = Math.floor((timeLeft % 3600) / 60).toString().padStart(2, '0');
        const s = (timeLeft % 60).toString().padStart(2, '0');
        
        timerEl.innerText = `${h}:${m}:${s}`;
    }, 1000);

    // 2. Flag Submission
    async function submitFlag(e, id) {
        e.preventDefault();
        const form = e.target;
        const input = form.querySelector('input');
        const btn = form.querySelector('button');
        const originalBtnHTML = btn.innerHTML;
        
        const flag = input.value;
        if(!flag) return;

        // Loading state
        btn.innerHTML = '<div class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
        
        try {
            const formData = new FormData();
            formData.append('action', 'submit_flag');
            formData.append('id', id);
            formData.append('flag', flag);

            const res = await fetch(window.location.href, { method: 'POST', body: formData });
            const data = await res.json();

            if(data.status === 'success') {
                // Flash Green and Reload
                input.classList.add('border-green-500', 'text-green-500');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                // Shake Animation
                form.classList.add('animate-shake');
                input.classList.add('border-red-500', 'text-red-500');
                setTimeout(() => {
                    form.classList.remove('animate-shake');
                    input.classList.remove('border-red-500', 'text-red-500');
                }, 500);
            }
        } catch(err) {
            console.error(err);
        } finally {
            btn.innerHTML = originalBtnHTML;
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