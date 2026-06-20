<?php
$challenge_flag = get_ctf_flag_map()['c2'] ?? '';
?>
<div class="max-w-6xl mx-auto animate-fade-in-up">

    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Memory Forensics — C2 Beacon Hunt</h1>
        <p class="text-neutral-400">Correlate Volatility3 process and network data to identify the active C2 implant.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Brief + Answer Form -->
        <div class="lg:col-span-1 space-y-5">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    A memory image was captured from a compromised workstation. Analyse the Volatility3 output to identify the active C2 beacon process.
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed">
                    Correlate the <strong class="text-white">process list</strong> with the <strong class="text-white">network connections</strong> to find which process is beaconing out to an external C2 server.
                </p>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Submit Answer</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-neutral-500 mb-1">Malicious Process Name</label>
                        <input type="text" id="ansProcess" placeholder="e.g. notepad.exe"
                            class="w-full bg-black border border-neutral-800 rounded-lg px-3 py-2 text-white font-mono text-xs focus:outline-none focus:border-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs text-neutral-500 mb-1">Process PID</label>
                        <input type="text" id="ansPID" placeholder="e.g. 1234"
                            class="w-full bg-black border border-neutral-800 rounded-lg px-3 py-2 text-white font-mono text-xs focus:outline-none focus:border-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs text-neutral-500 mb-1">C2 Domain / IP</label>
                        <input type="text" id="ansC2" placeholder="e.g. evil.example.com"
                            class="w-full bg-black border border-neutral-800 rounded-lg px-3 py-2 text-white font-mono text-xs focus:outline-none focus:border-white transition-colors">
                    </div>
                    <button onclick="submitAnswer()"
                        class="w-full py-2.5 bg-white text-black font-bold rounded-xl hover:bg-neutral-200 transition-colors text-sm">
                        Submit Analysis
                    </button>
                    <div id="errorMsg" class="hidden text-xs text-red-400 text-center animate-pulse">❌ Incorrect. Review the output carefully.</div>
                </div>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-3">Analysis Tips</h3>
                <ul class="space-y-2 text-xs text-neutral-400">
                    <li class="flex gap-2"><span class="text-white shrink-0">→</span>Cross-reference PID across both tables</li>
                    <li class="flex gap-2"><span class="text-white shrink-0">→</span>Look for unusual parent processes (PPID)</li>
                    <li class="flex gap-2"><span class="text-white shrink-0">→</span>C2 connections go to non-standard domains on port 443</li>
                    <li class="flex gap-2"><span class="text-white shrink-0">→</span>Beacons connect repeatedly — look for established state</li>
                    <li class="flex gap-2"><span class="text-white shrink-0">→</span>Flag format: <code class="text-neutral-300">flag{process_beacon_domain_pid}</code></li>
                </ul>
            </div>
        </div>

        <!-- Volatility Output -->
        <div class="lg:col-span-2 space-y-5">

            <!-- pslist -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center gap-3">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">vol.py -f memory.raw windows.pslist</span>
                    <span class="ml-auto text-[10px] px-2 py-0.5 rounded border border-neutral-700 text-neutral-500">30 processes</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-mono text-[11px]">
                        <thead class="bg-neutral-900 text-neutral-500 border-b border-neutral-800">
                            <tr>
                                <th class="px-3 py-2 font-medium">PID</th>
                                <th class="px-3 py-2 font-medium">PPID</th>
                                <th class="px-3 py-2 font-medium">ImageFileName</th>
                                <th class="px-3 py-2 font-medium">Threads</th>
                                <th class="px-3 py-2 font-medium">CreateTime</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-900 text-neutral-400 bg-black">
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">4</td><td class="px-3 py-1.5">0</td><td class="px-3 py-1.5">System</td><td class="px-3 py-1.5">148</td><td class="px-3 py-1.5">2026-06-20 03:01:02</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">352</td><td class="px-3 py-1.5">4</td><td class="px-3 py-1.5">smss.exe</td><td class="px-3 py-1.5">3</td><td class="px-3 py-1.5">2026-06-20 03:01:04</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">508</td><td class="px-3 py-1.5">500</td><td class="px-3 py-1.5">csrss.exe</td><td class="px-3 py-1.5">12</td><td class="px-3 py-1.5">2026-06-20 03:01:06</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">572</td><td class="px-3 py-1.5">564</td><td class="px-3 py-1.5">wininit.exe</td><td class="px-3 py-1.5">3</td><td class="px-3 py-1.5">2026-06-20 03:01:07</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">572</td><td class="px-3 py-1.5">services.exe</td><td class="px-3 py-1.5">9</td><td class="px-3 py-1.5">2026-06-20 03:01:08</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">680</td><td class="px-3 py-1.5">572</td><td class="px-3 py-1.5">lsass.exe</td><td class="px-3 py-1.5">11</td><td class="px-3 py-1.5">2026-06-20 03:01:08</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">824</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">svchost.exe</td><td class="px-3 py-1.5">22</td><td class="px-3 py-1.5">2026-06-20 03:01:10</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">936</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">svchost.exe</td><td class="px-3 py-1.5">17</td><td class="px-3 py-1.5">2026-06-20 03:01:11</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">1088</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">svchost.exe</td><td class="px-3 py-1.5">25</td><td class="px-3 py-1.5">2026-06-20 03:01:12</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">1216</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">spoolsv.exe</td><td class="px-3 py-1.5">14</td><td class="px-3 py-1.5">2026-06-20 03:01:14</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">1440</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">msdtc.exe</td><td class="px-3 py-1.5">14</td><td class="px-3 py-1.5">2026-06-20 03:01:16</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">1640</td><td class="px-3 py-1.5">explorer.exe</td><td class="px-3 py-1.5">55</td><td class="px-3 py-1.5">2026-06-20 03:02:01</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2104</td><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">chrome.exe</td><td class="px-3 py-1.5">42</td><td class="px-3 py-1.5">2026-06-20 03:05:33</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2388</td><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">notepad.exe</td><td class="px-3 py-1.5">3</td><td class="px-3 py-1.5">2026-06-20 03:08:14</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2740</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">SearchIndexer</td><td class="px-3 py-1.5">16</td><td class="px-3 py-1.5">2026-06-20 03:10:22</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">3108</td><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">OneDrive.exe</td><td class="px-3 py-1.5">19</td><td class="px-3 py-1.5">2026-06-20 04:00:02</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">3504</td><td class="px-3 py-1.5">668</td><td class="px-3 py-1.5">WmiPrvSE.exe</td><td class="px-3 py-1.5">8</td><td class="px-3 py-1.5">2026-06-20 04:15:07</td></tr>
                            <tr class="bg-red-950/20 hover:bg-red-950/40 border-l-2 border-red-700">
                                <td class="px-3 py-1.5 text-red-300 font-bold">4312</td>
                                <td class="px-3 py-1.5 text-red-300">3504</td>
                                <td class="px-3 py-1.5 text-red-300 font-bold">powershell.exe</td>
                                <td class="px-3 py-1.5 text-red-300">7</td>
                                <td class="px-3 py-1.5 text-red-300">2026-06-20 04:15:09</td>
                            </tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">4688</td><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">Teams.exe</td><td class="px-3 py-1.5">38</td><td class="px-3 py-1.5">2026-06-20 06:30:01</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">5020</td><td class="px-3 py-1.5">1836</td><td class="px-3 py-1.5">msedge.exe</td><td class="px-3 py-1.5">29</td><td class="px-3 py-1.5">2026-06-20 07:44:20</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- netscan -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-xl">
                <div class="bg-neutral-900 border-b border-neutral-800 px-4 py-2 flex items-center gap-3">
                    <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">vol.py -f memory.raw windows.netscan</span>
                    <span class="ml-auto text-[10px] px-2 py-0.5 rounded border border-neutral-700 text-neutral-500">12 connections</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-mono text-[11px]">
                        <thead class="bg-neutral-900 text-neutral-500 border-b border-neutral-800">
                            <tr>
                                <th class="px-3 py-2 font-medium">PID</th>
                                <th class="px-3 py-2 font-medium">Owner</th>
                                <th class="px-3 py-2 font-medium">Local Addr</th>
                                <th class="px-3 py-2 font-medium">Foreign Addr</th>
                                <th class="px-3 py-2 font-medium">State</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-900 text-neutral-400 bg-black">
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">824</td><td class="px-3 py-1.5">svchost.exe</td><td class="px-3 py-1.5">0.0.0.0:135</td><td class="px-3 py-1.5">0.0.0.0:0</td><td class="px-3 py-1.5 text-neutral-600">LISTENING</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">680</td><td class="px-3 py-1.5">lsass.exe</td><td class="px-3 py-1.5">0.0.0.0:49664</td><td class="px-3 py-1.5">0.0.0.0:0</td><td class="px-3 py-1.5 text-neutral-600">LISTENING</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2104</td><td class="px-3 py-1.5">chrome.exe</td><td class="px-3 py-1.5">192.168.1.45:52114</td><td class="px-3 py-1.5">142.250.185.46:443</td><td class="px-3 py-1.5 text-green-600">ESTABLISHED</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2104</td><td class="px-3 py-1.5">chrome.exe</td><td class="px-3 py-1.5">192.168.1.45:52201</td><td class="px-3 py-1.5">172.217.164.68:443</td><td class="px-3 py-1.5 text-green-600">ESTABLISHED</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">3108</td><td class="px-3 py-1.5">OneDrive.exe</td><td class="px-3 py-1.5">192.168.1.45:54312</td><td class="px-3 py-1.5">52.113.194.132:443</td><td class="px-3 py-1.5 text-green-600">ESTABLISHED</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">4688</td><td class="px-3 py-1.5">Teams.exe</td><td class="px-3 py-1.5">192.168.1.45:55008</td><td class="px-3 py-1.5">52.114.132.74:443</td><td class="px-3 py-1.5 text-green-600">ESTABLISHED</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">5020</td><td class="px-3 py-1.5">msedge.exe</td><td class="px-3 py-1.5">192.168.1.45:55120</td><td class="px-3 py-1.5">204.79.197.200:443</td><td class="px-3 py-1.5 text-green-600">ESTABLISHED</td></tr>
                            <tr class="bg-red-950/20 hover:bg-red-950/40 border-l-2 border-red-700">
                                <td class="px-3 py-1.5 text-red-300 font-bold">4312</td>
                                <td class="px-3 py-1.5 text-red-300 font-bold">powershell.exe</td>
                                <td class="px-3 py-1.5 text-red-300">192.168.1.45:55443</td>
                                <td class="px-3 py-1.5 text-red-300 font-bold">c2corp.net:443</td>
                                <td class="px-3 py-1.5 text-red-300">ESTABLISHED</td>
                            </tr>
                            <tr class="bg-red-950/10 hover:bg-red-950/30"><td class="px-3 py-1.5 text-red-400">4312</td><td class="px-3 py-1.5 text-red-400">powershell.exe</td><td class="px-3 py-1.5 text-red-400">192.168.1.45:55780</td><td class="px-3 py-1.5 text-red-400">c2corp.net:443</td><td class="px-3 py-1.5 text-red-400">TIME_WAIT</td></tr>
                            <tr class="bg-red-950/10 hover:bg-red-950/30"><td class="px-3 py-1.5 text-red-400">4312</td><td class="px-3 py-1.5 text-red-400">powershell.exe</td><td class="px-3 py-1.5 text-red-400">192.168.1.45:55621</td><td class="px-3 py-1.5 text-red-400">c2corp.net:443</td><td class="px-3 py-1.5 text-red-400">CLOSE_WAIT</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2740</td><td class="px-3 py-1.5">SearchIndexer</td><td class="px-3 py-1.5">192.168.1.45:56001</td><td class="px-3 py-1.5">10.0.0.1:80</td><td class="px-3 py-1.5 text-neutral-600">CLOSE_WAIT</td></tr>
                            <tr class="hover:bg-neutral-900/40"><td class="px-3 py-1.5">2388</td><td class="px-3 py-1.5">notepad.exe</td><td class="px-3 py-1.5">192.168.1.45:57120</td><td class="px-3 py-1.5">0.0.0.0:0</td><td class="px-3 py-1.5 text-neutral-600">LISTENING</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Flag Reveal -->
            <div id="flagReveal" class="hidden border border-green-900/50 bg-green-900/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center text-green-400 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-green-400 font-bold text-sm">C2 Beacon Identified — Analysis Complete</div>
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
function submitAnswer() {
    const proc = document.getElementById('ansProcess').value.trim().toLowerCase().replace(/\.exe$/, '');
    const pid  = document.getElementById('ansPID').value.trim();
    const c2   = document.getElementById('ansC2').value.trim().toLowerCase().replace(/^https?:\/\//, '').replace(/\/$/, '');
    const err  = document.getElementById('errorMsg');

    const correctProc = proc === 'powershell';
    const correctPID  = pid  === '4312';
    const correctC2   = c2   === 'c2corp.net';

    if (correctProc && correctPID && correctC2) {
        err.classList.add('hidden');
        document.getElementById('flagReveal').classList.remove('hidden');
        document.getElementById('flagReveal').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        err.classList.remove('hidden');
        // Briefly highlight incorrect fields
        ['ansProcess','ansPID','ansC2'].forEach((id, i) => {
            const correct = [correctProc, correctPID, correctC2][i];
            const el = document.getElementById(id);
            if (!correct) {
                el.classList.add('border-red-600');
                setTimeout(() => el.classList.remove('border-red-600'), 1200);
            }
        });
    }
}
</script>
