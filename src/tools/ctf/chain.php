<?php
$challenge_flag = get_ctf_flag_map()['chain'] ?? '';
?>
<div class="max-w-6xl mx-auto animate-fade-in-up">

    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Full Attack Chain — Privilege Escalation</h1>
        <p class="text-neutral-400">Enumerate the host, find the escalation vector, and reach root.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Mission Brief -->
        <div class="lg:col-span-1 space-y-5">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    You have a low-privilege shell as user <code class="text-yellow-400">operator</code> on a Linux host. Enumerate, find the escalation path, and read <code class="text-red-400">/root/final_flag.txt</code>.
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed">
                    Think systematically: <strong class="text-white">Who am I? What can I run? How do I escalate?</strong>
                </p>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Useful Commands</h3>
                <div class="space-y-1 font-mono text-xs">
                    <div class="text-neutral-500 hover:text-neutral-300 cursor-pointer transition-colors" onclick="fillCmd('id')">$ id</div>
                    <div class="text-neutral-500 hover:text-neutral-300 cursor-pointer transition-colors" onclick="fillCmd('whoami')">$ whoami</div>
                    <div class="text-neutral-500 hover:text-neutral-300 cursor-pointer transition-colors" onclick="fillCmd('find / -perm -4000 2>/dev/null')">$ find / -perm -4000 2>/dev/null</div>
                    <div class="text-neutral-500 hover:text-neutral-300 cursor-pointer transition-colors" onclick="fillCmd('docker images')">$ docker images</div>
                    <div class="text-neutral-500 hover:text-neutral-300 cursor-pointer transition-colors" onclick="fillCmd('cat /etc/group | grep docker')">$ cat /etc/group | grep docker</div>
                </div>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Progress</h3>
                <div id="progressSteps" class="space-y-2 text-xs">
                    <div id="step1" class="flex items-center gap-2 text-neutral-600">
                        <div class="w-2 h-2 rounded-full bg-neutral-700 shrink-0"></div>Identify current user + groups
                    </div>
                    <div id="step2" class="flex items-center gap-2 text-neutral-600">
                        <div class="w-2 h-2 rounded-full bg-neutral-700 shrink-0"></div>Discover escalation vector
                    </div>
                    <div id="step3" class="flex items-center gap-2 text-neutral-600">
                        <div class="w-2 h-2 rounded-full bg-neutral-700 shrink-0"></div>Escalate to root
                    </div>
                    <div id="step4" class="flex items-center gap-2 text-neutral-600">
                        <div class="w-2 h-2 rounded-full bg-neutral-700 shrink-0"></div>Read /root/final_flag.txt
                    </div>
                </div>
            </div>
        </div>

        <!-- Terminal -->
        <div class="lg:col-span-2 space-y-4">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-2xl">

                <!-- Terminal Header -->
                <div class="bg-neutral-900 border-b border-neutral-800 p-2 flex items-center gap-2">
                    <div class="flex gap-1.5 ml-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                    </div>
                    <div class="flex-grow text-center text-xs text-neutral-500 font-mono">operator@plexaur-target:~$</div>
                </div>

                <!-- Terminal Body -->
                <div id="termBody" class="flex-grow bg-black p-4 font-mono text-xs text-neutral-300 overflow-y-auto h-96 cursor-text" onclick="document.getElementById('termInput').focus()">
                    <div class="text-neutral-500 mb-2">Debian GNU/Linux 11  |  Kernel 5.15.0  |  Last login: Fri Jun 20 07:32:01 2026</div>
                    <div class="text-green-400 mb-1">operator@plexaur-target:~$  <span class="text-neutral-500">Type 'help' for tips</span></div>
                    <div id="termHistory"></div>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-green-400">operator@plexaur-target:~$</span>
                        <input type="text" id="termInput" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                            class="bg-transparent border-none outline-none text-white flex-grow font-mono text-xs ml-1"
                            onkeydown="handleKey(event)" placeholder="">
                    </div>
                </div>
            </div>

            <!-- Flag Reveal -->
            <div id="flagReveal" class="hidden border border-green-900/50 bg-green-900/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center text-green-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-green-400 font-bold text-sm">Root Reached — Attack Chain Complete</div>
                        <div class="text-neutral-400 text-xs mt-0.5">Submit the flag at Mission Control to record your solve.</div>
                    </div>
                </div>
                <div class="bg-black border border-green-900/50 rounded-lg px-4 py-3 font-mono text-green-400 text-sm tracking-wide select-all">
                    <?= htmlspecialchars($challenge_flag) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const FLAG = '<?= addslashes(htmlspecialchars($challenge_flag)) ?>';
let steps = { s1: false, s2: false, s3: false };
let cmdHistory = [];
let histIdx = -1;

const CMD_MAP = {
    'help': () => `Available: id, whoami, ls, uname -a, find / -perm -4000 2>/dev/null, cat /etc/group | grep docker, docker images, docker ps, docker run ...`,
    'whoami': () => `operator`,
    'id': () => {
        markStep('step1', 1);
        return `uid=1000(operator) gid=1000(operator) groups=1000(operator),<span class="text-yellow-400 font-bold">999(docker)</span>`;
    },
    'uname -a': () => `Linux plexaur-target 5.15.0-58-generic #64-Ubuntu SMP x86_64 GNU/Linux`,
    'ls': () => `Desktop  Documents  notes.txt`,
    'cat notes.txt': () => `TODO: remove operator from docker group before prod`,
    'cat /etc/group | grep docker': () => {
        markStep('step2', 2);
        return `docker:x:999:operator`;
    },
    'find / -perm -4000 2>/dev/null': () => {
        markStep('step1', 1);
        return `/usr/bin/mount\n/usr/bin/su\n/usr/bin/newgrp\n/usr/bin/python3\n/usr/bin/umount\n/usr/bin/chfn\n/usr/bin/chsh`;
    },
    'docker images': () => {
        markStep('step2', 2);
        return `REPOSITORY   TAG       IMAGE ID       CREATED        SIZE\n<span class="text-green-400">alpine       latest    49176f190c7e   3 weeks ago    7.34MB</span>\nubuntu       22.04     b1d9df8ab815   4 weeks ago    69.2MB`;
    },
    'docker ps': () => `CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES\n<span class="text-neutral-500">(no running containers)</span>`,
    'docker ps -a': () => `CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES\n<span class="text-neutral-500">(no containers)</span>`,
};

function handleKey(e) {
    const input = document.getElementById('termInput');
    if (e.key === 'Enter') {
        const cmd = input.value.trim();
        if (!cmd) return;
        cmdHistory.unshift(cmd);
        histIdx = -1;
        input.value = '';
        processCmd(cmd);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (histIdx < cmdHistory.length - 1) histIdx++;
        input.value = cmdHistory[histIdx] ?? '';
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (histIdx > 0) histIdx--;
        else histIdx = -1;
        input.value = histIdx >= 0 ? cmdHistory[histIdx] : '';
    }
}

function processCmd(cmd) {
    const hist = document.getElementById('termHistory');
    const term = document.getElementById('termBody');

    hist.innerHTML += `<div class="flex items-center gap-1 mt-1 mb-0.5"><span class="text-green-400">operator@plexaur-target:~$</span><span class="text-white ml-1">${escHtml(cmd)}</span></div>`;

    let out = '';

    if (cmd === 'clear') { hist.innerHTML = ''; return; }

    // Docker escalation commands
    const isDockerEscape = /docker\s+run\b/.test(cmd) && /\/mnt/.test(cmd) && /(final_flag\.txt|root)/.test(cmd);
    const isPythonSUID   = /python3?\s+-c/.test(cmd) && /(setuid|os\.system|flag)/.test(cmd);

    if (isDockerEscape) {
        markStep('step3', 3);
        out = `<span class="text-neutral-400">Unable to find image 'alpine:latest' locally — using cached\nalpine: Pulling from library/alpine\nDigest: sha256:c5c5fda71656f28e49ac9c5416b3643eaa6a108a8093151d6d1afc9463be8e33\nStatus: Image is up to date for alpine:latest</span>`;
        setTimeout(() => {
            appendOutput(`<span class="text-green-400 font-bold">🚩 root@plexaur-target:/# cat /mnt/root/final_flag.txt</span>`, true);
            appendOutput(`<span class="text-green-300">${escHtml(FLAG)}</span>`, true);
            markStep('step4', 4);
            document.getElementById('flagReveal').classList.remove('hidden');
            scrollTerm();
        }, 800);
    } else if (isPythonSUID) {
        markStep('step3', 3);
        out = `<span class="text-neutral-400">root@plexaur-target:~#</span>`;
        setTimeout(() => {
            appendOutput(`<span class="text-green-300">${escHtml(FLAG)}</span>`, true);
            markStep('step4', 4);
            document.getElementById('flagReveal').classList.remove('hidden');
            scrollTerm();
        }, 500);
    } else if (CMD_MAP[cmd] !== undefined) {
        out = CMD_MAP[cmd]();
    } else if (cmd.startsWith('docker run') && !isDockerEscape) {
        out = `<span class="text-neutral-400">Usage tip: Mount the host root filesystem to /mnt and read /root/final_flag.txt\nExample: docker run --rm -v /:/mnt alpine cat /mnt/root/final_flag.txt</span>`;
    } else {
        out = `<span class="text-red-400">bash: ${escHtml(cmd.split(' ')[0])}: command not found</span>`;
    }

    if (out) {
        hist.innerHTML += `<div class="text-neutral-300 mb-1 whitespace-pre-wrap">${out}</div>`;
    }
    scrollTerm();
}

function appendOutput(html, isFlagged) {
    document.getElementById('termHistory').innerHTML += `<div class="whitespace-pre-wrap mb-1">${html}</div>`;
}

function markStep(id, num) {
    const el = document.getElementById(id);
    if (!el || el.dataset.done) return;
    el.dataset.done = '1';
    el.className = 'flex items-center gap-2 text-green-400 text-xs';
    el.querySelector('div').className = 'w-2 h-2 rounded-full bg-green-500 shrink-0';
}

function fillCmd(cmd) {
    const input = document.getElementById('termInput');
    input.value = cmd;
    input.focus();
}

function scrollTerm() {
    const t = document.getElementById('termBody');
    t.scrollTop = t.scrollHeight;
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
</script>
