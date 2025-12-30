<div class="max-w-7xl mx-auto animate-fade-in-up h-[calc(100vh-100px)] md:h-[calc(100vh-140px)] flex flex-col relative">
    
    <!-- 1. Header & Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 md:mb-6 gap-4 shrink-0">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Network Analyzer</h1>
            <p class="text-neutral-400 text-xs md:text-sm">Client-side PCAP/CAP inspector. Multi-file support enabled.</p>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <!-- Added 'multiple' attribute here -->
            <input type="file" id="pcapInput" accept=".pcap,.cap" class="hidden" multiple>
            
            <button onclick="document.getElementById('pcapInput').click()" class="flex-1 md:flex-none justify-center bg-white text-black px-4 py-2 rounded-md text-sm font-bold hover:bg-neutral-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Open Files</span>
            </button>
            <button onclick="clearData()" class="flex-none border border-neutral-700 text-neutral-300 px-4 py-2 rounded-md text-sm hover:bg-neutral-800 transition-colors">
                Clear
            </button>
        </div>
    </div>

    <!-- 2. Main Workspace -->
    <div id="workspace" class="hidden flex-grow flex flex-col gap-4 overflow-hidden relative">
        
        <!-- Packet List Pane -->
        <div class="flex-grow bg-neutral-950 border border-neutral-800 rounded-lg overflow-hidden flex flex-col shadow-xl">
            <!-- Desktop Table Header -->
            <div class="hidden md:flex bg-neutral-900 px-4 py-2 border-b border-neutral-800 text-xs font-mono text-neutral-400 uppercase tracking-wider">
                <div class="w-16">No.</div>
                <div class="w-24">Time</div>
                <div class="w-32">Source</div>
                <div class="w-32">Destination</div>
                <div class="w-16">Proto</div>
                <div class="w-16">Len</div>
                <div class="flex-grow">Info</div>
            </div>
            
            <!-- List Container -->
            <div id="packetList" class="overflow-y-auto flex-grow font-mono text-xs text-neutral-300 relative">
                <!-- Javascript injects rows here -->
            </div>
            
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="absolute inset-0 bg-neutral-900/80 hidden items-center justify-center z-20">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 border-2 border-white border-t-transparent rounded-full animate-spin mb-2"></div>
                    <span class="text-xs text-neutral-300">Merging & Sorting...</span>
                </div>
            </div>
        </div>

        <!-- Desktop Bottom Panes -->
        <div class="hidden md:flex h-1/3 gap-4 min-h-[200px]">
            <!-- Packet Details -->
            <div class="w-1/2 bg-neutral-950 border border-neutral-800 rounded-lg overflow-hidden flex flex-col">
                <div class="bg-neutral-900 px-4 py-2 border-b border-neutral-800 text-xs font-bold text-neutral-400 uppercase">Packet Details</div>
                <div id="desktopDetails" class="p-4 overflow-y-auto text-sm text-neutral-300 font-mono space-y-1">
                    <p class="text-neutral-600 italic">Select a packet to view details.</p>
                </div>
            </div>

            <!-- Hex Dump -->
            <div class="w-1/2 bg-neutral-950 border border-neutral-800 rounded-lg overflow-hidden flex flex-col">
                <div class="bg-neutral-900 px-4 py-2 border-b border-neutral-800 text-xs font-bold text-neutral-400 uppercase">Hex Dump</div>
                <div id="desktopHex" class="p-4 overflow-y-auto text-xs font-mono text-neutral-400 whitespace-pre"></div>
            </div>
        </div>
    </div>

    <!-- 3. Mobile Details Modal -->
    <div id="mobileModal" class="md:hidden fixed inset-0 z-50 translate-y-full transition-transform duration-300 ease-out">
        <div onclick="closeMobileModal()" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
        <div class="absolute bottom-0 left-0 right-0 h-[85vh] bg-neutral-900 rounded-t-2xl flex flex-col shadow-2xl border-t border-neutral-700">
            <div class="p-4 border-b border-neutral-800 flex justify-between items-center bg-neutral-800/50 rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <span id="mobileModalTitle" class="font-bold text-white">Packet #</span>
                    <span id="mobileModalProto" class="text-xs bg-blue-900 text-blue-200 px-2 py-0.5 rounded">TCP</span>
                </div>
                <button onclick="closeMobileModal()" class="text-neutral-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="overflow-y-auto p-4 flex-grow space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Details</h3>
                    <div id="mobileDetailsContent" class="text-sm font-mono text-neutral-300 bg-black/30 p-3 rounded border border-neutral-800"></div>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-2">Hex Dump</h3>
                    <div id="mobileHexContent" class="text-[10px] leading-relaxed font-mono text-neutral-400 bg-black p-3 rounded border border-neutral-800 whitespace-pre-wrap break-all"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Empty State -->
    <div id="emptyState" class="flex-grow flex flex-col items-center justify-center border-2 border-dashed border-neutral-800 rounded-xl bg-neutral-950/50 m-4 md:m-0">
        <div class="w-16 h-16 bg-neutral-900 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">Multi-File Analyzer</h3>
        <p class="text-neutral-400 mb-6 text-center max-w-sm px-4">Select multiple .pcap files to merge and analyze them in a single timeline.</p>
        <button onclick="document.getElementById('pcapInput').click()" class="text-white underline hover:text-neutral-300 cursor-pointer">Browse Files</button>
    </div>

</div>

<script>
    const fileInput = document.getElementById('pcapInput');
    const loadingOverlay = document.getElementById('loadingOverlay');
    let packets = [];

    // --- File Handling (Async Multi-Read) ---
    fileInput.addEventListener('change', async (e) => {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        // UI Reset
        clearData(); 
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('workspace').classList.remove('hidden');
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.classList.add('flex');

        try {
            // Read all files in parallel
            const promises = files.map(file => readFileAsync(file));
            const buffers = await Promise.all(promises);

            // Parse all buffers
            for (const buffer of buffers) {
                parsePCAPBuffer(buffer);
            }

            // SORT packets by timestamp (Important for multi-file merging)
            packets.sort((a, b) => a.ts_epoch - b.ts_epoch);

            // Re-index packet numbers after sort
            packets.forEach((p, index) => { p.num = index + 1; });

            renderPacketList();

        } catch (err) {
            console.error("Error reading files", err);
            alert("Error reading files: " + err);
        } finally {
            loadingOverlay.classList.add('hidden');
            loadingOverlay.classList.remove('flex');
        }
    });

    function readFileAsync(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = (e) => reject(e);
            reader.readAsArrayBuffer(file);
        });
    }

    function clearData() {
        packets = [];
        document.getElementById('packetList').innerHTML = '';
        document.getElementById('desktopDetails').innerHTML = '<p class="text-neutral-600 italic">Select a packet to view details.</p>';
        document.getElementById('desktopHex').innerHTML = '';
        document.getElementById('workspace').classList.add('hidden');
        document.getElementById('emptyState').classList.remove('hidden');
        fileInput.value = '';
        closeMobileModal();
    }

    // --- Parser Engine ---
    function parsePCAPBuffer(buffer) {
        const view = new DataView(buffer);
        let offset = 24; // Skip Global Header
        const le = true; // Assume Little Endian

        while (offset < buffer.byteLength) {
            try {
                if(offset + 16 > buffer.byteLength) break;
                
                const ts_sec = view.getUint32(offset, le);
                const ts_usec = view.getUint32(offset + 4, le);
                const incl_len = view.getUint32(offset + 8, le);
                
                offset += 16;
                if(offset + incl_len > buffer.byteLength) break;

                const packetData = buffer.slice(offset, offset + incl_len);
                
                // We don't set 'num' here, we set it after sorting
                packets.push(parsePacketData(packetData, ts_sec, ts_usec));

                offset += incl_len;
            } catch (e) {
                console.error("Parse Error at offset " + offset, e);
                break;
            }
        }
    }

    function parsePacketData(buffer, ts_sec, ts_usec) {
        const view = new DataView(buffer);
        
        // Calculate detailed timestamp for sorting
        // Standard PCAP is seconds + microseconds
        const ts_epoch = ts_sec + (ts_usec / 1000000); 
        const date = new Date(ts_sec * 1000);
        const timeStr = date.toLocaleTimeString([], { hour12: false }) + '.' + ts_usec.toString().padStart(6, '0').substring(0, 3);

        let info = {
            num: 0, // Placeholder
            ts_epoch: ts_epoch,
            time: timeStr,
            src: '?',
            dst: '?',
            proto: 'ETH',
            len: buffer.byteLength,
            summary: 'Raw Frame',
            raw: buffer
        };

        if (buffer.byteLength >= 14) {
            const ethType = view.getUint16(12, false); // Big Endian
            if (ethType === 0x0800 && buffer.byteLength >= 34) {
                // IPv4
                const ipOffset = 14;
                const proto = view.getUint8(ipOffset + 9);
                info.src = getIP(view, ipOffset + 12);
                info.dst = getIP(view, ipOffset + 16);
                info.proto = getProtoName(proto);
                info.summary = `IPv4 ${info.src} → ${info.dst}`;
            } else if (ethType === 0x0806) {
                // ARP
                info.proto = 'ARP';
                info.summary = 'Address Resolution Protocol';
            } else if (ethType === 0x86DD) {
                // IPv6
                info.proto = 'IPv6';
                info.summary = 'IPv6 Packet';
            }
        }
        return info;
    }

    function getIP(view, offset) {
        return `${view.getUint8(offset)}.${view.getUint8(offset+1)}.${view.getUint8(offset+2)}.${view.getUint8(offset+3)}`;
    }

    function getProtoName(p) {
        if (p === 6) return 'TCP';
        if (p === 17) return 'UDP';
        if (p === 1) return 'ICMP';
        return 'IP';
    }

    // --- Renderer ---
    function renderPacketList() {
        const list = document.getElementById('packetList');
        const limit = 2000; // Render limit to prevent DOM freeze
        const displayPackets = packets.slice(0, limit);
        
        list.innerHTML = displayPackets.map((p, i) => `
            <div onclick="selectPacket(${i})" class="group border-b border-neutral-900 cursor-pointer hover:bg-neutral-800 transition-colors p-3 md:p-0 md:px-4 md:py-1 md:flex md:items-center">
                <!-- Mobile -->
                <div class="flex justify-between items-center mb-1 md:hidden">
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-500 text-xs">#${p.num}</span>
                        <span class="bg-neutral-800 text-neutral-300 text-[10px] px-1.5 rounded">${p.proto}</span>
                    </div>
                    <span class="text-neutral-500 text-xs">${p.time}</span>
                </div>
                <!-- Desktop -->
                <div class="hidden md:block w-16 text-neutral-500">${p.num}</div>
                <div class="hidden md:block w-24 text-neutral-400">${p.time}</div>
                <div class="hidden md:block w-32 truncate text-blue-400">${p.src}</div>
                <div class="hidden md:block w-32 truncate text-orange-400">${p.dst}</div>
                <div class="hidden md:block w-16 text-neutral-300">${p.proto}</div>
                <div class="hidden md:block w-16 text-neutral-500">${p.len}</div>
                <!-- Summary -->
                <div class="flex-grow truncate text-neutral-400 md:text-neutral-500 group-hover:text-white text-sm md:text-xs">
                    <span class="md:hidden text-blue-400">${p.src}</span> 
                    <span class="md:hidden text-neutral-600">→</span> 
                    <span class="md:hidden text-orange-400">${p.dst}</span>
                    <span class="hidden md:inline">${p.summary}</span>
                </div>
            </div>
        `).join('');

        if(packets.length > limit) {
             list.innerHTML += `<div class="p-4 text-center text-xs text-neutral-500 italic">Showing first ${limit} of ${packets.length} packets</div>`;
        }
    }

    function selectPacket(index) {
        const p = packets[index];
        const hexHtml = generateHexDump(p.raw);
        const detailsHtml = `
            <div class="mb-2"><span class="text-neutral-500">Frame:</span> ${p.num} <span class="text-neutral-500">Length:</span> ${p.len} bytes</div>
            <div class="mb-2"><span class="text-neutral-500">Time:</span> ${p.time}</div>
            <div class="pl-3 border-l-2 border-neutral-800 mb-2">
                <div><span class="text-neutral-500">Source:</span> <span class="text-blue-400">${p.src}</span></div>
                <div><span class="text-neutral-500">Destination:</span> <span class="text-orange-400">${p.dst}</span></div>
            </div>
            <div><span class="text-neutral-500">Protocol:</span> ${p.proto}</div>
        `;

        if (window.innerWidth < 768) {
            document.getElementById('mobileModalTitle').innerText = `Packet #${p.num}`;
            document.getElementById('mobileModalProto').innerText = p.proto;
            document.getElementById('mobileDetailsContent').innerHTML = detailsHtml;
            document.getElementById('mobileHexContent').innerHTML = hexHtml;
            document.getElementById('mobileModal').classList.remove('translate-y-full');
        } else {
            document.getElementById('desktopDetails').innerHTML = detailsHtml;
            document.getElementById('desktopHex').innerHTML = hexHtml;
        }
    }

    function closeMobileModal() {
        document.getElementById('mobileModal').classList.add('translate-y-full');
    }

    function generateHexDump(buffer) {
        const bytes = new Uint8Array(buffer);
        let output = '';
        let ascii = '';
        for (let i = 0; i < bytes.length; i++) {
            if (i % 16 === 0) {
                if (i > 0) output += `  |${ascii}|\n`;
                output += i.toString(16).padStart(4, '0') + '  ';
                ascii = '';
            }
            const val = bytes[i];
            output += val.toString(16).padStart(2, '0') + ' ';
            ascii += (val >= 32 && val <= 126) ? String.fromCharCode(val) : '.';
        }
        const remainder = bytes.length % 16;
        if (remainder > 0) {
            for (let j = 0; j < (16 - remainder); j++) output += '   ';
        }
        output += `  |${ascii}|`;
        return `<span class="text-blue-300">${output}</span>`;
    }
</script>