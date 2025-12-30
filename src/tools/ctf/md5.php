<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Rainbow Table Attack</h1>
        <p class="text-neutral-400">Reverse MD5 hashes using pre-computed chains.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Hash Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Target Hash</h3>
                <div class="p-3 bg-black border border-neutral-800 rounded font-mono text-xs text-orange-500 break-all mb-4">
                    d8578edf8458ce06fbc5bb76a58c5ca4
                </div>
                <button onclick="document.getElementById('lookupInput').value = 'd8578edf8458ce06fbc5bb76a58c5ca4'" class="w-full border border-neutral-700 text-neutral-300 text-xs py-2 rounded hover:bg-neutral-800">
                    Auto-fill Hash
                </button>
            </div>
        </div>

        <!-- Lookup Tool -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Database Lookup</h3>
                
                <div class="flex gap-2 mb-6">
                    <input type="text" id="lookupInput" placeholder="Enter MD5 Hash..." class="flex-grow bg-black border border-neutral-800 rounded-lg px-4 py-3 text-white font-mono text-sm focus:outline-none focus:border-white">
                    <button onclick="lookupHash()" class="bg-white text-black font-bold px-6 py-3 rounded-lg hover:bg-neutral-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Search
                    </button>
                </div>

                <!-- Result Terminal -->
                <div id="terminal" class="bg-black border border-neutral-800 rounded-lg p-4 font-mono text-xs h-40 overflow-y-auto">
                    <span class="text-neutral-600">// Ready for query...</span>
                </div>

                <!-- Hidden Flag -->
                <div id="flagReveal" class="hidden mt-6 border-t border-neutral-800 pt-6 text-center">
                    <p class="text-green-500 font-bold mb-2">Hash Reversed Successfully</p>
                    <div class="inline-block bg-neutral-900 border border-neutral-800 rounded px-4 py-2 font-mono text-white text-sm">
                        flag{plexaur_md5_qwerty_cracked}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function lookupHash() {
        const hash = document.getElementById('lookupInput').value.trim();
        const term = document.getElementById('terminal');
        
        term.innerHTML = `<span class="text-blue-400">[*] Connecting to Rainbow DB...</span>`;
        
        setTimeout(() => {
            term.innerHTML += `\n<span class="text-neutral-400">[*] Querying: ${hash}</span>`;
            
            setTimeout(() => {
                if (hash === 'd8578edf8458ce06fbc5bb76a58c5ca4') {
                    term.innerHTML += `\n<span class="text-green-500">[+] Match Found: "qwerty"</span>`;
                    document.getElementById('flagReveal').classList.remove('hidden');
                } else {
                    term.innerHTML += `\n<span class="text-red-500">[-] No match found in database.</span>`;
                    document.getElementById('flagReveal').classList.add('hidden');
                }
            }, 800);
        }, 600);
    }
</script>