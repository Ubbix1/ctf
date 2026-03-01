<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

if (!is_logged_in()) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'run_cmd') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $command = $_POST['command'] ?? '';
    $result = $user_id ? run_sandbox_command($user_id, $command) : ['status' => 'blocked', 'output' => 'No user context'];

    echo json_encode([
        'status' => $result['status'] ?? 'blocked',
        'output' => $result['output'] ?? 'No output',
    ]);
    exit;
}
?>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    <div class="mb-8">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm inline-flex items-center gap-1">&larr; Back to Mission Control</a>
        <h1 class="text-3xl font-bold text-white mt-3">Sandbox Command Console</h1>
        <p class="text-neutral-400 mt-2">Controlled command console with strict allowlist policy and audit logging.</p>
    </div>

    <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg text-white font-bold">Sandbox Terminal</h2>
            <span class="text-xs px-2 py-1 rounded bg-blue-900/30 text-blue-300 border border-blue-800">Policy: Allowlist Only</span>
        </div>

        <div id="terminal" class="h-72 overflow-y-auto bg-black border border-neutral-800 rounded-xl p-4 font-mono text-xs text-neutral-400 mb-4">
            [sandbox] Type <span class="text-white">help</span> to view allowed commands.
        </div>

        <form id="cmdForm" class="flex gap-2">
            <input id="cmdInput" type="text" autocomplete="off" placeholder="help" class="flex-grow bg-black border border-neutral-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-white">
            <button type="submit" class="px-4 py-2 bg-white text-black rounded-lg font-semibold hover:bg-neutral-200">Run</button>
        </form>
    </div>
</div>

<script>
    const terminal = document.getElementById('terminal');
    const form = document.getElementById('cmdForm');
    const input = document.getElementById('cmdInput');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const cmd = input.value.trim();
        if (!cmd) return;

        terminal.innerHTML += `\n\n$ ${cmd}`;
        terminal.scrollTop = terminal.scrollHeight;

        const formData = new FormData();
        formData.append('action', 'run_cmd');
        formData.append('command', cmd);

        try {
            const res = await fetch(window.location.href, { method: 'POST', body: formData });
            const data = await res.json();
            const cls = data.status === 'allowed' ? 'text-green-300' : 'text-red-300';
            terminal.innerHTML += `\n<span class="${cls}">${String(data.output || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}</span>`;
            terminal.scrollTop = terminal.scrollHeight;
        } catch (err) {
            terminal.innerHTML += '\n<span class="text-red-300">Execution failed.</span>';
        }

        input.value = '';
    });
</script>

