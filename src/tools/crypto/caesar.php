<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Caesar Cipher Decryptor</h1>
        <p class="text-neutral-400">Shift the alphabets to reveal the hidden message.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Left: Challenge Context -->
        <div class="md:col-span-1">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    We intercepted this encrypted string from the enemy server. 
                    Intelligence suggests a simple shift substitution was used.
                </p>
                <div class="bg-black border border-neutral-800 p-3 rounded font-mono text-yellow-500 text-sm break-all mb-4">
                    Vkrqh lv dq dgydqfhg irup ri qrwklqj.
                </div>
                <p class="text-xs text-neutral-500">Hint: Try shifting by -3.</p>
            </div>
        </div>

        <!-- Right: Tool -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                
                <!-- Input -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Cipher Text</label>
                    <textarea id="cipherInput" class="w-full bg-black border border-neutral-800 rounded-lg p-3 text-white font-mono focus:outline-none focus:border-white h-24" placeholder="Enter text to shift...">Vkrqh lv dq dgydqfhg irup ri qrwklqj.</textarea>
                </div>

                <!-- Slider Control -->
                <div class="mb-8 bg-neutral-900 rounded-lg p-4 border border-neutral-800">
                    <div class="flex justify-between text-xs text-neutral-400 mb-2">
                        <span>Shift Key</span>
                        <span id="shiftValue" class="text-white font-bold">0</span>
                    </div>
                    <input type="range" id="shiftRange" min="0" max="25" value="0" class="w-full h-2 bg-neutral-800 rounded-lg appearance-none cursor-pointer accent-white">
                    <div class="flex justify-between text-[10px] text-neutral-600 mt-1">
                        <span>0</span>
                        <span>25</span>
                    </div>
                </div>

                <!-- Output -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Decrypted Result</label>
                    <div id="cipherOutput" class="w-full bg-neutral-900 border border-neutral-800 rounded-lg p-3 text-green-400 font-mono min-h-[60px] break-all">
                        Vkrqh lv dq dgydqfhg irup ri qrwklqj.
                    </div>
                </div>

                <div class="flex justify-end">
                    <button onclick="bruteForce()" class="text-xs text-neutral-400 hover:text-white underline">Show All 25 Shifts</button>
                </div>

            </div>

            <!-- Brute Force Results -->
            <div id="bruteForceArea" class="mt-6 hidden space-y-2">
                <!-- Injected via JS -->
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('cipherInput');
    const output = document.getElementById('cipherOutput');
    const slider = document.getElementById('shiftRange');
    const valueDisplay = document.getElementById('shiftValue');

    // 1. Shift Logic
    function caesarShift(str, amount) {
        // Wrap amount
        if (amount < 0) return caesarShift(str, amount + 26);
        
        let output = "";
        for (let i = 0; i < str.length; i++) {
            let c = str[i];
            if (c.match(/[a-z]/i)) {
                const code = str.charCodeAt(i);
                // ASCII: A=65, Z=90, a=97, z=122
                if (code >= 65 && code <= 90) {
                    c = String.fromCharCode(((code - 65 - amount + 26) % 26) + 65);
                } else if (code >= 97 && code <= 122) {
                    c = String.fromCharCode(((code - 97 - amount + 26) % 26) + 97);
                }
            }
            output += c;
        }
        return output;
    }

    // 2. Event Listeners
    slider.addEventListener('input', (e) => {
        const shift = parseInt(e.target.value);
        valueDisplay.innerText = shift;
        output.innerText = caesarShift(input.value, shift);
    });

    input.addEventListener('input', () => {
        const shift = parseInt(slider.value);
        output.innerText = caesarShift(input.value, shift);
    });

    // 3. Brute Force
    function bruteForce() {
        const container = document.getElementById('bruteForceArea');
        container.innerHTML = '<h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Brute Force Analysis</h3>';
        container.classList.remove('hidden');
        
        const text = input.value;
        for(let i=1; i<=25; i++) {
            const result = caesarShift(text, i);
            container.innerHTML += `
                <div class="flex items-center gap-4 p-2 bg-neutral-900 border border-neutral-800 rounded hover:bg-neutral-800 transition-colors cursor-pointer" onclick="setShift(${i})">
                    <span class="text-xs text-neutral-500 w-8">-${i}</span>
                    <span class="text-sm font-mono text-neutral-300 truncate">${result}</span>
                </div>
            `;
        }
    }

    function setShift(n) {
        slider.value = n;
        valueDisplay.innerText = n;
        output.innerText = caesarShift(input.value, n);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>