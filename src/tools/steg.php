<div class="max-w-6xl mx-auto animate-fade-in-up">
    
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-white mb-3">Steganography Studio</h1>
        <p class="text-neutral-400">Hide secret text messages inside PNG images securely in your browser.</p>
    </div>

    <!-- Mode Switcher -->
    <div class="flex justify-center mb-10">
        <div class="bg-neutral-900 p-1 rounded-lg border border-neutral-800 flex relative">
            <!-- Sliding Background (Visual only, simple implementation) -->
            <button onclick="setMode('encode')" id="tab-encode" class="relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all bg-white text-black shadow">
                Hide Data
            </button>
            <button onclick="setMode('decode')" id="tab-decode" class="relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all text-neutral-400 hover:text-white">
                Reveal Data
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Left Column: Input -->
        <div class="space-y-6">
            <!-- Image Upload -->
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">1. Source Image</h3>
                
                <div class="relative group">
                    <input type="file" id="imageInput" accept="image/png" class="hidden">
                    <div id="dropzone" onclick="document.getElementById('imageInput').click()" class="border-2 border-dashed border-neutral-800 rounded-lg h-64 flex flex-col items-center justify-center cursor-pointer hover:bg-neutral-900/50 hover:border-neutral-600 transition-all overflow-hidden relative">
                        
                        <!-- Empty State -->
                        <div id="emptyState" class="text-center">
                            <div class="w-12 h-12 bg-neutral-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm text-neutral-300 font-medium">Click to upload PNG</p>
                            <p class="text-xs text-neutral-500 mt-1">or drag and drop</p>
                        </div>

                        <!-- Preview -->
                        <img id="previewImg" class="absolute inset-0 w-full h-full object-contain hidden bg-black p-2">
                    </div>
                </div>
            </div>

            <!-- Encode: Text Input -->
            <div id="encodeInputArea" class="border border-neutral-800 bg-neutral-950 rounded-xl p-6 transition-all">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">2. Secret Message</h3>
                <textarea id="secretMessage" class="w-full h-32 bg-black border border-neutral-800 rounded-lg p-4 text-white text-sm focus:outline-none focus:border-white transition-colors resize-none" placeholder="Enter the text you want to hide..."></textarea>
                
                <div class="mt-4 flex justify-between items-center">
                    <span id="capacity" class="text-xs text-neutral-500">Capacity: 0 chars</span>
                    <button onclick="encode()" class="bg-white text-black px-6 py-2.5 rounded-lg font-bold text-sm hover:bg-neutral-200 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Encrypt & Save
                    </button>
                </div>
            </div>

             <!-- Decode: Action Area (Hidden by default) -->
             <div id="decodeInputArea" class="hidden border border-neutral-800 bg-neutral-950 rounded-xl p-6 transition-all">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">2. Extraction</h3>
                <p class="text-sm text-neutral-400 mb-6">Upload the image containing hidden data to reveal the contents.</p>
                <button onclick="decode()" class="w-full bg-white text-black px-6 py-3 rounded-lg font-bold text-sm hover:bg-neutral-200 transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Scan Image
                </button>
            </div>
        </div>

        <!-- Right Column: Result -->
        <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6 flex flex-col h-full min-h-[400px]">
            <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Console Output</h3>
            
            <div id="console" class="flex-grow bg-black rounded-lg border border-neutral-900 p-4 font-mono text-xs overflow-y-auto space-y-2">
                <div class="text-neutral-500">// System ready. Waiting for image...</div>
            </div>
        </div>

    </div>
</div>

<script>
    // --- State ---
    let currentMode = 'encode';
    let sourceImage = null;
    let canvas = document.createElement('canvas');
    let ctx = canvas.getContext('2d', { willReadFrequently: true });

    // --- UI Logic ---
    function setMode(mode) {
        currentMode = mode;
        const encodeTab = document.getElementById('tab-encode');
        const decodeTab = document.getElementById('tab-decode');
        const encodeArea = document.getElementById('encodeInputArea');
        const decodeArea = document.getElementById('decodeInputArea');

        if(mode === 'encode') {
            encodeTab.className = "relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all bg-white text-black shadow";
            decodeTab.className = "relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all text-neutral-400 hover:text-white";
            encodeArea.classList.remove('hidden');
            decodeArea.classList.add('hidden');
            log("Switched to ENCODE mode.");
        } else {
            encodeTab.className = "relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all text-neutral-400 hover:text-white";
            decodeTab.className = "relative z-10 px-8 py-2 rounded-md text-sm font-bold transition-all bg-white text-black shadow";
            encodeArea.classList.add('hidden');
            decodeArea.classList.remove('hidden');
            log("Switched to DECODE mode.");
        }
    }

    // --- File Handling ---
    const fileInput = document.getElementById('imageInput');
    const preview = document.getElementById('previewImg');
    const emptyState = document.getElementById('emptyState');

    fileInput.addEventListener('change', e => handleFile(e.target.files[0]));

    function handleFile(file) {
        if(!file) return;
        if(file.type !== "image/png") {
            log("Error: Only PNG images are supported for lossless data hiding.", "error");
            alert("Please use a PNG image.");
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            sourceImage = new Image();
            sourceImage.onload = function() {
                // Init Canvas
                canvas.width = sourceImage.width;
                canvas.height = sourceImage.height;
                ctx.drawImage(sourceImage, 0, 0);
                
                // UI Updates
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                emptyState.classList.add('hidden');
                
                // Capacity Calc (3 bits per pixel)
                const totalPixels = sourceImage.width * sourceImage.height;
                const maxChars = Math.floor((totalPixels * 3) / 8); 
                document.getElementById('capacity').innerText = `Capacity: ~${(maxChars/1024).toFixed(1)} KB`;
                
                log(`Image loaded: ${file.name} (${sourceImage.width}x${sourceImage.height})`);
            }
            sourceImage.src = event.target.result;
        }
        reader.readAsDataURL(file);
    }

    // --- LSB Encoding Logic ---
    function encode() {
        if(!sourceImage) return log("No image loaded.", "error");
        
        const text = document.getElementById('secretMessage').value;
        if(!text) return log("Message is empty.", "error");

        log("Starting encryption...");

        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imgData.data;

        // 1. Convert text to binary + NULL Terminator (00000000)
        let binary = "";
        for (let i = 0; i < text.length; i++) {
            let bin = text.charCodeAt(i).toString(2);
            binary += "00000000".substr(bin.length) + bin;
        }
        binary += "00000000"; // Terminator

        if(binary.length > data.length * 0.75) {
            return log("Message too long for this image.", "error");
        }

        // 2. Embed Bits
        let binIdx = 0;
        for (let i = 0; i < data.length; i += 4) {
            // R, G, B channels (skip Alpha i+3)
            for (let j = 0; j < 3; j++) {
                if (binIdx < binary.length) {
                    // Clear LSB and add new bit
                    data[i+j] = (data[i+j] & 0xFE) | parseInt(binary[binIdx]);
                    binIdx++;
                }
            }
            if(binIdx >= binary.length) break;
        }

        // 3. Update Canvas & Download
        ctx.putImageData(imgData, 0, 0);
        
        log("Encryption complete. Downloading...");
        
        const link = document.createElement('a');
        link.download = "secret_image.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
    }

    // --- LSB Decoding Logic ---
    function decode() {
        if(!sourceImage) return log("No image loaded.", "error");

        log("Scanning image for hidden data...");

        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const data = imgData.data;
        let binary = "";
        let char = "";

        // Extract Bits
        for (let i = 0; i < data.length; i += 4) {
            for (let j = 0; j < 3; j++) {
                const bit = (data[i+j] & 1); // Get LSB
                char += bit;
                
                if (char.length === 8) {
                    if (parseInt(char, 2) === 0) {
                        // Null terminator found
                        printResult(binary);
                        return;
                    }
                    // Valid char found, add to binary string 
                    // (We store binary first to be safe, then convert at end)
                    binary += String.fromCharCode(parseInt(char, 2));
                    char = "";
                }
            }
        }
        
        log("No null terminator found. Data might be corrupted or image has no secret.", "warning");
    }

    // --- Utilities ---
    function printResult(msg) {
        log("Message found!", "success");
        const consoleEl = document.getElementById('console');
        consoleEl.innerHTML += `
            <div class="mt-2 p-3 bg-neutral-900 border border-neutral-800 rounded text-green-400 break-all select-all font-mono">
                ${escapeHtml(msg)}
            </div>
        `;
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function log(msg, type="info") {
        const consoleEl = document.getElementById('console');
        let color = "text-neutral-400";
        if(type === "error") color = "text-red-500";
        if(type === "success") color = "text-green-500";
        if(type === "warning") color = "text-yellow-500";

        consoleEl.innerHTML += `<div class="${color}">> ${msg}</div>`;
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>