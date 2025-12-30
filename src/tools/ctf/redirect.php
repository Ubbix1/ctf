<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Open Redirect Exploit</h1>
        <p class="text-neutral-400">Manipulate trusted URLs to redirect users to malicious destinations.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Mission Brief -->
        <div class="md:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Mission Brief</h3>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    The target site uses a `return` parameter to send users back to the dashboard after login. 
                </p>
                <p class="text-sm text-neutral-300 leading-relaxed mb-4">
                    <strong>Objective:</strong> Modify the URL to redirect the user to <code class="text-red-400">http://evil.com</code> instead of the dashboard.
                </p>
                <div class="bg-neutral-900 p-3 rounded border border-neutral-800 text-xs font-mono text-neutral-400">
                    Target: https://plexaur.com/login?return=/dashboard
                </div>
            </div>
        </div>

        <!-- Browser Simulator -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-2xl">
                
                <!-- Browser Chrome -->
                <div class="bg-neutral-900 border-b border-neutral-800 p-3 flex items-center gap-4">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/50"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/50"></div>
                    </div>
                    <!-- Address Bar -->
                    <div class="flex-grow relative">
                        <input type="text" id="urlBar" 
                            value="https://plexaur.com/login?return=/dashboard" 
                            class="w-full bg-black border border-neutral-700 rounded-full py-1.5 px-4 text-sm text-white font-mono focus:border-blue-500 focus:outline-none transition-colors">
                        <button onclick="navigate()" class="absolute right-1 top-1 p-0.5 text-neutral-400 hover:text-white rounded-full hover:bg-neutral-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Browser Viewport -->
                <div id="viewport" class="h-64 bg-white flex items-center justify-center relative overflow-hidden">
                    
                    <!-- Scenario 1: Login Page -->
                    <div id="pageLogin" class="text-center w-full max-w-xs transition-all duration-500">
                        <div class="w-12 h-12 bg-black rounded-full mx-auto mb-4"></div>
                        <h2 class="text-xl font-bold text-black mb-6">Sign In</h2>
                        <input type="text" disabled placeholder="username" class="w-full bg-gray-100 border-none rounded mb-3 px-3 py-2 text-sm">
                        <input type="password" disabled placeholder="••••••" class="w-full bg-gray-100 border-none rounded mb-4 px-3 py-2 text-sm">
                        <button onclick="navigate()" class="w-full bg-black text-white py-2 rounded text-sm font-bold">Login</button>
                    </div>

                    <!-- Scenario 2: Evil Site (Success) -->
                    <div id="pageEvil" class="hidden absolute inset-0 bg-red-900 flex flex-col items-center justify-center text-white p-8 text-center">
                        <svg class="w-16 h-16 mb-4 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h2 class="text-2xl font-bold mb-2">Warning: Dangerous Site</h2>
                        <p class="text-red-200 mb-6">You successfully redirected the user to an external domain!</p>
                        <div class="bg-black/50 p-4 rounded border border-red-500/50">
                            <span class="font-mono text-green-400">flag{plexaur_redirect_caught}</span>
                        </div>
                    </div>

                    <!-- Scenario 3: Normal Dashboard (Fail) -->
                    <div id="pageDash" class="hidden absolute inset-0 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                        <p>Redirected to Internal Dashboard.</p>
                        <p class="text-xs mt-2">Try changing the URL to an external site.</p>
                        <button onclick="resetSim()" class="mt-4 text-blue-500 underline text-sm">Reset</button>
                    </div>

                </div>
            </div>
            
            <p class="text-center text-xs text-neutral-500 mt-4">
                Tip: Change the <code>return</code> parameter in the simulated address bar above.
            </p>
        </div>
    </div>
</div>

<script>
    function navigate() {
        const url = document.getElementById('urlBar').value;
        const viewport = document.getElementById('viewport');
        const login = document.getElementById('pageLogin');
        const evil = document.getElementById('pageEvil');
        const dash = document.getElementById('pageDash');

        // Check param
        const urlObj = new URL(url); // This might fail if protocol missing, but simulated here
        const params = new URLSearchParams(url.split('?')[1]);
        const redirect = params.get('return');

        if (redirect && (redirect.includes('http://') || redirect.includes('https://'))) {
            // SUCCESS: External redirect detected
            login.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                login.classList.add('hidden');
                evil.classList.remove('hidden');
                
                // Simulate loading
                setTimeout(() => {
                    evil.classList.remove('opacity-0'); 
                }, 100);
            }, 300);
        } else {
            // FAIL: Internal redirect
            login.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                login.classList.add('hidden');
                dash.classList.remove('hidden');
            }, 300);
        }
    }

    function resetSim() {
        document.getElementById('pageDash').classList.add('hidden');
        document.getElementById('pageEvil').classList.add('hidden');
        const login = document.getElementById('pageLogin');
        login.classList.remove('hidden', 'opacity-0', 'scale-95');
        document.getElementById('urlBar').value = "https://plexaur.com/login?return=/dashboard";
    }
</script>