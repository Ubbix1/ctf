<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Network Reconnaissance</h1>
        <p class="text-neutral-400">Scan target infrastructure to identify vulnerable entry points.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Mission Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Target Intel</h3>
                <p class="text-sm text-neutral-300 mb-4">
                    Intelligence indicates an old database server was left exposed to the public internet.
                </p>
                <div class="bg-neutral-900 p-3 rounded border border-neutral-800 text-xs font-mono text-green-400 mb-4">
                    Target IP: 10.10.14.55
                </div>
                <p class="text-xs text-neutral-500">
                    Use the terminal to run a port scan. Identify the port typically associated with <span class="text-white">MySQL</span>.
                </p>
            </div>
        </div>

        <!-- Terminal Simulator -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-2xl flex flex-col h-[400px]">
                
                <!-- Terminal Header -->
                <div class="bg-neutral-900 border-b border-neutral-800 p-2 flex items-center gap-2">
                    <div class="flex gap-1.5 ml-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                    </div>
                    <div class="flex-grow text-center text-xs text-neutral-500 font-mono">root@plexaur-kali:~</div>
                </div>

                <!-- Terminal Body -->
                <div id="terminalBody" class="flex-grow bg-black p-4 font-mono text-xs text-neutral-300 overflow-y-auto cursor-text" onclick="document.getElementById('cmdInput').focus()">
                    <div class="mb-2">Kali GNU/Linux Rolling [Version 2024.1]</div>
                    <div id="history"></div>
                    
                    <div class="flex items-center gap-2 text-green-500">
                        <span>┌──(root㉿kali)-[~]</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-green-500">└─#</span>
                        <input type="text" id="cmdInput" class="bg-transparent border-none focus:outline-none text-white flex-grow" autocomplete="off" autofocus>
                    </div>
                </div>

            </div>

            <!-- Flag Input (Hidden until scan complete) -->
            <div id="flagSection" class="mt-6 hidden animate-fade-in-up">
                <div class="border border-green-900/30 bg-green-900/10 rounded-xl p-4 flex items-center justify-between">
                    <span class="text-sm text-green-400">Vulnerable Port Identified?</span>
                    <div class="flex gap-2">
                        <input type="text" id="portAnswer" placeholder="Port #" class="bg-black border border-green-900 rounded px-3 py-1 text-sm text-white w-24 text-center focus:outline-none focus:border-green-500">
                        <button onclick="checkPort()" class="bg-green-600 hover:bg-green-500 text-white text-xs font-bold px-4 rounded transition-colors">
                            Submit
                        </button>
                    </div>
                </div>
                <div id="flagResult" class="mt-2 text-center text-sm font-mono text-white hidden bg-neutral-900 p-2 rounded border border-neutral-800">
                    flag{plexaur_ports_opened}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('cmdInput');
    const historyDiv = document.getElementById('history');
    const flagSection = document.getElementById('flagSection');

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const cmd = input.value.trim();
            input.value = '';
            
            // Echo command
            historyDiv.innerHTML += `
                <div class="flex items-center gap-2 text-green-500 mt-2">
                    <span>┌──(root㉿kali)-[~]</span>
                </div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-green-500">└─#</span>
                    <span class="text-white">${cmd}</span>
                </div>
            `;

            processCommand(cmd);
            
            // Auto scroll
            const term = document.getElementById('terminalBody');
            term.scrollTop = term.scrollHeight;
        }
    });

    function processCommand(cmd) {
        if (cmd === 'clear') {
            historyDiv.innerHTML = '';
            return;
        }

        if (cmd === 'help') {
            printOutput("Available commands: nmap [target_ip], clear, help");
            return;
        }

        if (cmd.startsWith('nmap')) {
            // Simulate Scan
            printOutput(`Starting Nmap 7.94 ( https://nmap.org ) at ${new Date().toLocaleTimeString()}`);
            printOutput(`Nmap scan report for 10.10.14.55`);
            printOutput(`Host is up (0.00042s latency).`);
            printOutput(`Not shown: 997 closed tcp ports (reset)`);
            
            setTimeout(() => {
                historyDiv.innerHTML += `
                    <div class="text-neutral-300 mt-2">PORT     STATE SERVICE VERSION</div>
                    <div class="text-green-400">22/tcp   open  ssh     OpenSSH 8.4p1</div>
                    <div class="text-green-400">80/tcp   open  http    Apache httpd 2.4.48</div>
                    <div class="text-red-400 font-bold">3306/tcp open  mysql   MySQL 5.5.5-10.3.27-MariaDB</div>
                    <div class="text-neutral-400 mt-2">Nmap done: 1 IP address (1 host up) scanned in 1.24 seconds</div>
                `;
                const term = document.getElementById('terminalBody');
                term.scrollTop = term.scrollHeight;
                
                // Show flag input
                flagSection.classList.remove('hidden');
            }, 800);
            return;
        }

        printOutput(`zsh: command not found: ${cmd}`);
    }

    function printOutput(text) {
        historyDiv.innerHTML += `<div class="text-neutral-400 mb-1">${text}</div>`;
    }

    function checkPort() {
        const p = document.getElementById('portAnswer').value;
        if (p === '3306') {
            document.getElementById('flagResult').classList.remove('hidden');
            document.getElementById('portAnswer').classList.add('border-green-500', 'text-green-500');
        } else {
            document.getElementById('portAnswer').classList.add('border-red-500');
            setTimeout(() => document.getElementById('portAnswer').classList.remove('border-red-500'), 500);
        }
    }
</script>   