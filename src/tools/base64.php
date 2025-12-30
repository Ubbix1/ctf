<?php
// Handle AJAX Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Clean the Buffer
    // This removes the <head>, <nav>, etc. that layout.php has already generated.
    if (ob_get_length()) ob_clean(); 
    
    // 2. Logic
    $inputText = isset($_POST['inputText']) ? trim($_POST['inputText']) : '';
    $action = $_POST['action'] ?? '';

    if ($inputText === '') {
        echo 'Please enter some text.';
    } elseif ($action === 'encode') {
        echo base64_encode($inputText);
    } elseif ($action === 'decode') {
        echo base64_decode($inputText);
    } else {
        echo 'Invalid action.';
    }

    // 3. Stop Execution
    // This prevents the rest of the HTML (Footer, etc.) from loading.
    exit; 
}
?>

<!-- UI Content -->
<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <!-- Header -->
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-white mb-3">Base64 Converter</h1>
        <p class="text-neutral-400">Encode and decode data formats instantly with zero latency.</p>
    </div>

    <!-- Main Card -->
    <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6 md:p-8 shadow-2xl">
        
        <!-- Input Section -->
        <div class="mb-6">
            <label for="inputText" class="block text-sm font-medium text-neutral-300 mb-2">Input</label>
            <textarea 
                id="inputText" 
                rows="6" 
                placeholder="Paste your content here..."
                class="w-full bg-black border border-neutral-800 rounded-lg p-4 text-neutral-200 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all font-mono text-sm resize-y"
            ></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
            <button 
                onclick="performAction('encode')" 
                class="flex-1 bg-white text-black font-semibold py-3 px-6 rounded-lg hover:bg-neutral-200 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-white"
            >
                Encode
            </button>
            <button 
                onclick="performAction('decode')" 
                class="flex-1 border border-neutral-700 text-neutral-300 font-semibold py-3 px-6 rounded-lg hover:bg-neutral-900 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-offset-black focus:ring-neutral-600"
            >
                Decode
            </button>
        </div>

        <!-- Output Section -->
        <div class="relative">
            <div class="flex justify-between items-end mb-2">
                <label class="block text-sm font-medium text-neutral-300">Result</label>
                <button 
                    onclick="copyToClipboard()" 
                    class="text-xs text-neutral-500 hover:text-white flex items-center gap-1 transition-colors"
                    id="copyBtn"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    Copy
                </button>
            </div>
            
            <div 
                id="outputText" 
                class="min-h-[100px] w-full bg-neutral-900/50 border border-neutral-800 rounded-lg p-4 text-neutral-400 font-mono text-sm break-all"
            >
                <!-- Result will appear here -->
            </div>
        </div>
    </div>
</div>

<!-- Modern Toast Notification -->
<div id="toast" class="fixed bottom-8 right-8 bg-white text-black px-6 py-3 rounded-full shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 font-medium text-sm flex items-center gap-2">
    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    Copied to clipboard
</div>

<!-- Pure JavaScript Logic (No jQuery needed) -->
<script>
    async function performAction(action) {
        const input = document.getElementById('inputText').value;
        const outputDiv = document.getElementById('outputText');
        
        if (!input.trim()) {
            outputDiv.innerHTML = '<span class="text-red-500">Please enter some text first.</span>';
            return;
        }

        outputDiv.innerHTML = '<span class="animate-pulse">Processing...</span>';

        try {
            const formData = new FormData();
            formData.append('inputText', input);
            formData.append('action', action);

            // Post to current URL (Router handles it)
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const result = await response.text();
            outputDiv.textContent = result;
            outputDiv.classList.add('text-white');
            outputDiv.classList.remove('text-neutral-400');
        } catch (error) {
            outputDiv.innerHTML = '<span class="text-red-500">Error processing request.</span>';
        }
    }

    function copyToClipboard() {
        const text = document.getElementById('outputText').innerText;
        if (!text) return;

        navigator.clipboard.writeText(text).then(() => {
            showToast();
        });
    }

    function showToast() {
        const toast = document.getElementById('toast');
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2000);
    }
</script>