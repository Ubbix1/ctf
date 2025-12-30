<!-- Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Hash Buster</h1>
        <p class="text-neutral-400">Brute-force password hashes using client-side dictionary attacks.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Mission Info -->
        <div class="md:col-span-1">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Target Hash</h3>
                <div class="p-3 bg-black border border-neutral-800 rounded font-mono text-xs text-orange-500 break-all mb-4">
                    5f4dcc3b5aa765d61d8327deb882cf99
                </div>
                <p class="text-sm text-neutral-400">
                    <strong>Algorithm:</strong> MD5<br>
                    <strong>Leak Source:</strong> Admin DB<br>
                    <strong>Difficulty:</strong> Low
                </p>
                <button onclick="document.getElementById('hashInput').value = '5f4dcc3b5aa765d61d8327deb882cf99'" class="mt-4 w-full text-xs border border-neutral-700 text-neutral-300 py-2 rounded hover:bg-neutral-800 transition-colors">
                    Load Target Hash
                </button>
            </div>
        </div>

        <!-- Tool -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                
                <!-- Input -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Target Hash (MD5)</label>
                    <div class="flex gap-2">
                        <input type="text" id="hashInput" placeholder="e.g. 5f4dcc3b..." class="flex-grow bg-black border border-neutral-800 rounded-lg p-3 text-white font-mono focus:outline-none focus:border-white">
                        <button onclick="crackHash()" class="bg-white text-black font-bold px-6 rounded-lg hover:bg-neutral-200 transition-colors">
                            Crack
                        </button>
                    </div>
                </div>

                <!-- Wordlist Configuration -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Attack Mode</label>
                    <div class="bg-neutral-900 rounded-lg p-3 border border-neutral-800 flex gap-4 text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="mode" value="dict" checked class="accent-white">
                            <span class="text-neutral-300">Common Dictionary (100 Top)</span>
                        </label>
                        <!-- Future: Custom wordlist upload -->
                    </div>
                </div>

                <!-- Console Output -->
                <div class="relative">
                    <div class="absolute top-0 right-0 p-2">
                        <span id="statusBadge" class="hidden text-[10px] font-bold px-2 py-1 rounded bg-yellow-900 text-yellow-200">RUNNING</span>
                    </div>
                    <div id="console" class="w-full h-48 bg-black border border-neutral-800 rounded-lg p-4 font-mono text-xs text-neutral-500 overflow-y-auto">
                        <span class="text-neutral-600">// Console ready. Waiting for input...</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Common top passwords for the demo
    const dictionary = [
        "123456", "password", "12345678", "qwerty", "123456789", "12345", "111111", "1234567", "dragon",
        "sunshine", "princess", "admin", "welcome", "football", "monkey", "charlie", "jordan", "baseball",
        "letmein", "master", "shadow", "plexaur", "ctf", "hacker", "hunter2", "superman"
    ];

    async function crackHash() {
        const hash = document.getElementById('hashInput').value.trim().toLowerCase();
        const consoleDiv = document.getElementById('console');
        const badge = document.getElementById('statusBadge');
        
        if (!hash) {
            consoleDiv.innerHTML += `\n<span class="text-red-500">Error: No hash provided.</span>`;
            return;
        }

        // Reset UI
        consoleDiv.innerHTML = `<span class="text-blue-400">[*] Initializing Dictionary Attack...</span>\n<span class="text-neutral-500">[*] Target: ${hash}</span>\n<span class="text-neutral-500">[*] Wordlist size: ${dictionary.length}</span>`;
        badge.classList.remove('hidden');
        badge.innerText = "RUNNING";
        badge.className = "text-[10px] font-bold px-2 py-1 rounded bg-yellow-900 text-yellow-200 absolute top-2 right-2";

        // Simulate processing delay
        for (let i = 0; i < dictionary.length; i++) {
            const word = dictionary[i];
            const wordHash = CryptoJS.MD5(word).toString();

            // Artificial delay for effect
            await new Promise(r => setTimeout(r, 20)); // 20ms delay per word

            if (wordHash === hash) {
                consoleDiv.innerHTML += `\n<span class="text-green-500">[+] MATCH FOUND!</span>`;
                consoleDiv.innerHTML += `\n\n<span class="text-white font-bold bg-green-900/50 p-1">PASSWORD: ${word}</span>`;
                consoleDiv.scrollTop = consoleDiv.scrollHeight;
                
                badge.innerText = "CRACKED";
                badge.className = "text-[10px] font-bold px-2 py-1 rounded bg-green-900 text-green-200 absolute top-2 right-2";
                return;
            } else {
                // Show progress every few words
                if (i % 2 === 0) {
                     consoleDiv.innerHTML += `\n<span class="text-neutral-700">[-] Tried: ${word} (${wordHash.substr(0,8)}...)</span>`;
                     consoleDiv.scrollTop = consoleDiv.scrollHeight;
                }
            }
        }

        consoleDiv.innerHTML += `\n<span class="text-red-500">[-] Exhausted wordlist. No match found.</span>`;
        badge.innerText = "FAILED";
        badge.className = "text-[10px] font-bold px-2 py-1 rounded bg-red-900 text-red-200 absolute top-2 right-2";
    }
</script>