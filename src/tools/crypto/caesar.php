<?php
$challenge_flag = get_ctf_flag_map()['caesar'] ?? '';
?>

<div class="max-w-4xl mx-auto animate-fade-in-up">

    <!-- Header -->
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Mission Control
        </a>

        <h1 class="text-3xl font-bold mb-2">Caesar Cipher Decryptor</h1>
        <p class="text-neutral-400">Shift the alphabets to reveal the hidden message.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Mission Brief -->
        <div class="md:col-span-1">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase mb-4">Mission Brief</h3>

                <p class="text-sm text-neutral-300 mb-4">
                    We intercepted this encrypted string from the enemy server.
                    Intelligence suggests a simple shift substitution.
                </p>

                <div class="bg-black border border-neutral-800 p-3 rounded font-mono text-yellow-500 text-sm mb-4 break-all">
                    Vkrqh lv dq dgydqfhg irup ri qrwklqj.
                </div>

                <p class="text-xs text-neutral-500">Hint: Try shifting by 3.</p>
            </div>
        </div>

        <!-- Tool -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">

                <!-- Cipher Input -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">
                        Cipher Text
                    </label>
                    <textarea
                        id="cipherInput"
                        class="w-full bg-black border border-neutral-800 rounded-lg p-3 font-mono h-24 focus:outline-none focus:border-white"
                    >Vkrqh lv dq dgydqfhg irup ri qrwklqj.</textarea>
                </div>

                <!-- Slider -->
                <div class="mb-8 bg-neutral-900 rounded-lg p-4 border border-neutral-800">
                    <div class="flex justify-between text-xs mb-2">
                        <span class="text-neutral-400">Shift Key</span>
                        <span id="shiftValue" class="font-bold">0</span>
                    </div>

                    <input
                        type="range"
                        id="shiftRange"
                        min="0"
                        max="25"
                        value="0"
                        class="w-full h-2 bg-neutral-800 rounded-lg cursor-pointer accent-white"
                    >

                    <div class="flex justify-between text-[10px] text-neutral-600 mt-1">
                        <span>0</span>
                        <span>25</span>
                    </div>
                </div>

                <!-- Output -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">
                        Decrypted Result
                    </label>

                    <div
                        id="cipherOutput"
                        class="w-full bg-neutral-900 border border-neutral-800 rounded-lg p-3 text-green-400 font-mono min-h-[60px] break-all"
                    >
                        Vkrqh lv dq dgydqfhg irup ri qrwklqj.
                    </div>
                </div>

                <!-- Answer Verification -->
                <div class="mt-6">
                    <label class="block text-xs font-bold text-neutral-500 uppercase mb-2">
                        Final Answer
                    </label>

                    <input
                        id="answerInput"
                        type="text"
                        placeholder="Enter decrypted message..."
                        class="w-full bg-black border border-neutral-800 rounded-lg p-3 font-mono focus:outline-none focus:border-white"
                    >

                    <button
                        onclick="checkAnswer()"
                        class="mt-3 px-4 py-2 bg-white text-black text-xs font-bold rounded hover:bg-neutral-200"
                    >
                        Submit Answer
                    </button>
                </div>

                <!-- Flag -->
                <div
                    id="flagBox"
                    class="hidden mt-6 p-4 border border-green-500 bg-green-900/20 rounded font-mono text-green-400"
                >
                    🚩 Submission: <span class="font-bold"><?= htmlspecialchars($challenge_flag) ?></span>
                </div>

                <!-- Error -->
                <div
                    id="errorBox"
                    class="hidden mt-4 p-3 border border-red-500 bg-red-900/20 rounded text-red-400 text-sm"
                >
                    ❌ Incorrect answer. Try again.
                </div>

                <div class="flex justify-end mt-6">
                    <button onclick="bruteForce()" class="text-xs text-neutral-400 hover:text-white underline">
                        Show All 25 Shifts
                    </button>
                </div>
            </div>

            <!-- Brute Force -->
            <div id="bruteForceArea" class="mt-6 hidden space-y-2"></div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('cipherInput');
    const output = document.getElementById('cipherOutput');
    const slider = document.getElementById('shiftRange');
    const valueDisplay = document.getElementById('shiftValue');

    function caesarShift(str, amount) {
        let result = "";
        for (let i = 0; i < str.length; i++) {
            let c = str[i];
            if (/[a-z]/i.test(c)) {
                const code = str.charCodeAt(i);
                if (code >= 65 && code <= 90) {
                    c = String.fromCharCode(((code - 65 - amount + 26) % 26) + 65);
                } else if (code >= 97 && code <= 122) {
                    c = String.fromCharCode(((code - 97 - amount + 26) % 26) + 97);
                }
            }
            result += c;
        }
        return result;
    }

    slider.addEventListener('input', e => {
        const shift = parseInt(e.target.value);
        valueDisplay.innerText = shift;
        output.innerText = caesarShift(input.value, shift);
    });

    input.addEventListener('input', () => {
        output.innerText = caesarShift(input.value, parseInt(slider.value));
    });

    function bruteForce() {
        const container = document.getElementById('bruteForceArea');
        container.innerHTML = '<h3 class="text-xs font-bold text-neutral-500 uppercase mb-2">Brute Force Analysis</h3>';
        container.classList.remove('hidden');

        for (let i = 1; i <= 25; i++) {
            const res = caesarShift(input.value, i);
            container.innerHTML += `
                <div onclick="setShift(${i})"
                     class="p-2 bg-neutral-900 border border-neutral-800 rounded cursor-pointer hover:bg-neutral-800">
                    <span class="text-xs text-neutral-500">-${i}</span>
                    <div class="font-mono text-sm text-neutral-300 truncate">${res}</div>
                </div>`;
        }
    }

    function setShift(n) {
        slider.value = n;
        valueDisplay.innerText = n;
        output.innerText = caesarShift(input.value, n);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function checkAnswer() {
        const userAnswer = document.getElementById('answerInput').value.trim().toLowerCase();
        const correctAnswer = "shone is an advanced form of nothing.";

        document.getElementById('flagBox').classList.toggle('hidden', userAnswer !== correctAnswer);
        document.getElementById('errorBox').classList.toggle('hidden', userAnswer === correctAnswer);
    }
</script>





