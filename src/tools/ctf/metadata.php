<?php
$challenge_flag = get_ctf_flag_map()['meta'] ?? '';
?>
<!-- Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>

<div class="max-w-6xl mx-auto animate-fade-in-up">
    
    <!-- Header -->
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Metadata Analyzer</h1>
        <p class="text-neutral-400">Extract hidden EXIF, GPS, and camera data from digital assets.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Mission Brief -->
        <div class="lg:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    Agents recovered a file from a compromised server. The flag is believed to be hidden within the file's properties (EXIF tags).
                </p>
                <div class="p-4 bg-neutral-900 border border-neutral-800 rounded-lg mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-neutral-800 rounded flex items-center justify-center text-neutral-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm text-white font-medium">evidence_04.jpg</div>
                            <div class="text-xs text-neutral-500">142 KB • JPG Image</div>
                        </div>
                    </div>
                </div>
                <a href="/assets/evidence_04.jpeg" download="evidence_04.jpeg"
                   class="block w-full text-center border border-neutral-700 text-neutral-300 py-2 rounded text-xs hover:text-white hover:border-white hover:bg-neutral-900 transition-colors">
                    ⬇ Download evidence_04.jpeg
                </a>
                <p class="text-[10px] text-neutral-600 mt-2">* Download the file, then upload it above to extract EXIF tags.</p>
            </div>

            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Found the flag?</h3>
                <form id="metaFlagForm" class="flex gap-2" onsubmit="checkMetaFlag(event)">
                    <input type="text" id="metaFlagInput" placeholder="flag{...}" class="w-full bg-black border border-neutral-800 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-neutral-600 transition-colors">
                    <button type="submit" class="bg-white text-black px-4 py-2 rounded text-sm font-bold hover:bg-neutral-200 shrink-0">Submit</button>
                </form>
                <div id="metaFlagError" class="hidden mt-2 text-xs text-red-400 animate-pulse">❌ Incorrect flag. Check the EXIF tags.</div>
                <div id="metaFlagReveal" class="hidden mt-3 border border-green-900/50 bg-green-900/10 rounded-lg p-3">
                    <div class="text-green-400 font-bold text-xs mb-1">✓ Correct Flag</div>
                    <div class="font-mono text-green-300 text-xs select-all"><?= htmlspecialchars($challenge_flag) ?></div>
                    <div class="text-neutral-500 text-[10px] mt-1">Submit this at Mission Control to record your solve.</div>
                </div>
            </div>
        </div>

        <!-- Right: Analyzer Tool -->
        <div class="lg:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6 min-h-[500px] flex flex-col">
                
                <!-- Dropzone -->
                <div id="dropzone" class="border-2 border-dashed border-neutral-800 rounded-xl p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-neutral-900/50 hover:border-neutral-600 transition-all">
                    <input type="file" id="fileInput" accept="image/jpeg,image/jpg" class="hidden">
                    <div class="w-16 h-16 bg-neutral-900 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Drop Image to Analyze</h3>
                    <p class="text-sm text-neutral-500 mt-1">Supports JPG/JPEG files with EXIF data</p>
                </div>

                <!-- Results Table -->
                <div id="resultsArea" class="hidden mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-white">Extraction Results</h3>
                        <button onclick="resetTool()" class="text-xs text-red-500 hover:underline">Clear</button>
                    </div>
                    
                    <div class="overflow-hidden border border-neutral-800 rounded-lg">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-neutral-900 text-neutral-500 font-mono text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Tag Name</th>
                                    <th class="px-4 py-3 font-medium">Value</th>
                                </tr>
                            </thead>
                            <tbody id="tagsBody" class="divide-y divide-neutral-800 bg-black text-neutral-300 font-mono">
                                <!-- JS Injects Rows Here -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const resultsArea = document.getElementById('resultsArea');
    const tagsBody = document.getElementById('tagsBody');

    // Drag & Drop Handlers
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-white'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-white'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-white');
        handleFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));

    function handleFile(file) {
        if (!file) return;

        // Reset UI
        tagsBody.innerHTML = '';
        dropzone.classList.add('hidden');
        resultsArea.classList.remove('hidden');

        // Basic File Info
        addRow('File Name', file.name);
        addRow('File Size', (file.size / 1024).toFixed(2) + ' KB');
        addRow('File Type', file.type);

        // EXIF Extraction
        EXIF.getData(file, function() {
            const allTags = EXIF.getAllTags(this);
            
            if (Object.keys(allTags).length === 0) {
                addRow('EXIF Data', 'No standard EXIF tags found.');
            } else {
                for (let tag in allTags) {
                    // Filter out thumbnail data which is huge text blob
                    if (tag !== 'thumbnail') {
                        let val = allTags[tag];
                        if (typeof val === 'object') val = JSON.stringify(val);
                        addRow(tag, val);
                    }
                }
            }
        });
    }

    function addRow(key, value) {
        const row = `
            <tr class="hover:bg-neutral-900/50 transition-colors">
                <td class="px-4 py-3 text-neutral-500">${key}</td>
                <td class="px-4 py-3 text-blue-400 break-all">${value}</td>
            </tr>
        `;
        tagsBody.innerHTML += row;
    }

    function resetTool() {
        fileInput.value = '';
        resultsArea.classList.add('hidden');
        dropzone.classList.remove('hidden');
    }

    function checkMetaFlag(e) {
        e.preventDefault();
        const input   = document.getElementById('metaFlagInput');
        const error   = document.getElementById('metaFlagError');
        const reveal  = document.getElementById('metaFlagReveal');
        const correct = '<?= addslashes($challenge_flag) ?>';

        if (input.value.trim().toLowerCase() === correct.toLowerCase()) {
            error.classList.add('hidden');
            input.classList.remove('border-red-600');
            input.classList.add('border-green-600');
            reveal.classList.remove('hidden');
        } else {
            reveal.classList.add('hidden');
            error.classList.remove('hidden');
            input.classList.add('border-red-600');
            setTimeout(() => {
                input.classList.remove('border-red-600');
                error.classList.add('hidden');
            }, 1500);
        }
    }
</script>