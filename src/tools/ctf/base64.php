<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Signal Interception</h1>
        <p class="text-neutral-400">Decode the obfuscated transmission.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Mission Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Intercepted Data</h3>
                <p class="text-sm text-neutral-300 mb-4">
                    Our sensors picked up a burst transmission. It appears to be standard Base64 encoding.
                </p>
                <div class="bg-black border border-neutral-800 rounded p-3 font-mono text-yellow-500 text-sm break-all">
                    U29sdmUgdGhpcyBjaGFsbGVuZ2U=
                </div>
                <p class="text-xs text-neutral-500 mt-4">
                    Tip: You can use the <a href="/base64" target="_blank" class="text-white underline">Base64 Tool</a> to solve this.
                </p>
            </div>
        </div>

        <!-- Decoder Interface -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Translation Console</h3>
                
                <div class="mb-6">
                    <label class="block text-xs text-neutral-400 mb-2">Enter Decoded Message</label>
                    <div class="flex gap-2">
                        <input type="text" id="answerInput" placeholder="Type answer here..." class="flex-grow bg-black border border-neutral-800 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-white">
                        <button onclick="checkAnswer()" class="bg-white text-black font-bold px-6 py-3 rounded-lg hover:bg-neutral-200">
                            Verify
                        </button>
                    </div>
                </div>

                <!-- Hidden Result -->
                <div id="resultArea" class="hidden animate-fade-in-up">
                    <div class="bg-green-900/20 border border-green-900 rounded-lg p-4 flex items-center gap-4">
                        <div class="bg-green-500/20 p-2 rounded-full text-green-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-green-400 font-bold text-sm">Transmission Decoded</h4>
                            <p class="text-green-300 text-xs mt-1">Flag: <span class="font-mono bg-green-900/50 px-1 rounded">flag{plexaur_decoded_successfully}</span></p>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-2 text-center">Copy the flag to Mission Control.</p>
                </div>

                <div id="errorArea" class="hidden mt-4 text-center">
                    <p class="text-red-500 text-sm animate-pulse">Translation Mismatch. Try again.</p>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function checkAnswer() {
        const input = document.getElementById('answerInput').value.trim().toLowerCase();
        if (input === 'solve this challenge') {
            document.getElementById('resultArea').classList.remove('hidden');
            document.getElementById('errorArea').classList.add('hidden');
            document.getElementById('answerInput').classList.add('border-green-500', 'text-green-500');
        } else {
            document.getElementById('errorArea').classList.remove('hidden');
            document.getElementById('answerInput').classList.add('border-red-500');
            setTimeout(() => {
                document.getElementById('answerInput').classList.remove('border-red-500');
            }, 500);
        }
    }
</script>