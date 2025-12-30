<div class="max-w-4xl mx-auto animate-fade-in-up">
    
    <div class="mb-10">
        <a href="/ctf" class="text-neutral-500 hover:text-white text-sm mb-4 inline-block flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Mission Control
        </a>
        <h1 class="text-3xl font-bold text-white mb-2">Cross-Site Scripting (XSS)</h1>
        <p class="text-neutral-400">Identify and exploit unsanitized inputs.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Code Analysis -->
        <div class="md:col-span-1 space-y-6">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl p-6">
                <h3 class="text-xs font-bold text-neutral-500 uppercase tracking-wider mb-4">Source Code Leak</h3>
                <div class="bg-black border border-neutral-800 rounded p-3 overflow-x-auto">
<pre class="text-xs font-mono text-blue-300">
&lt;form&gt;
  &lt;input 
    type="text" 
    name="user" 
    value="<span class="text-yellow-500">&lt;?php echo $_GET['user']; ?&gt;</span>"
  &gt;
&lt;/form&gt;
</pre>
                </div>
                <p class="text-xs text-neutral-400 mt-4 leading-relaxed">
                    The developer is echoing input directly into the `value` attribute without sanitization.
                </p>
                <p class="text-xs text-white mt-4 font-bold">
                    Objective: Inject a &lt;script&gt; tag or an event handler to trigger an alert.
                </p>
            </div>
        </div>

        <!-- Vulnerable App Simulator -->
        <div class="md:col-span-2">
            <div class="border border-neutral-800 bg-neutral-950 rounded-xl overflow-hidden shadow-2xl">
                <!-- Browser Header -->
                <div class="bg-neutral-900 border-b border-neutral-800 p-3 flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500/50"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                    </div>
                    <div class="bg-black text-neutral-500 text-xs px-3 py-1 rounded-full flex-grow text-center font-mono">
                        vulnerable-app.com/profile
                    </div>
                </div>

                <!-- Web Page -->
                <div class="bg-white p-8 h-64 relative">
                    <h2 class="text-2xl font-bold text-black mb-4">User Profile</h2>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Display Name</label>
                        <input type="text" id="xssInput" placeholder="Enter your name" class="w-full border-2 border-gray-300 rounded p-2 text-black focus:border-blue-500 outline-none transition-colors">
                    </div>
                    
                    <button onclick="submitPayload()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm hover:bg-blue-700">
                        Update Profile
                    </button>

                    <!-- Fake Alert Overlay -->
                    <div id="fakeAlert" class="hidden absolute inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-10">
                        <div class="bg-white rounded-lg shadow-2xl w-64 overflow-hidden animate-bounce-in">
                            <div class="bg-gray-100 px-4 py-2 border-b flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-700">vulnerable-app.com says</span>
                            </div>
                            <div class="p-6 text-center">
                                <p class="text-black font-medium">1</p>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 flex justify-center border-t">
                                <button onclick="closeAlert()" class="bg-blue-600 text-white px-6 py-1 rounded text-sm hover:bg-blue-700">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div id="successMsg" class="hidden mt-6 bg-green-900/20 border border-green-900 rounded-xl p-4 text-center animate-fade-in-up">
                <span class="text-green-400 font-bold">Exploit Successful!</span>
                <p class="text-green-300 text-sm mt-1">Flag: <span class="font-mono bg-green-900/50 px-2 py-0.5 rounded">flag{plexaur_xss_caught}</span></p>
            </div>
        </div>
    </div>
</div>

<script>
    function submitPayload() {
        const val = document.getElementById('xssInput').value;
        
        // Simple regex to check for script injection attempts
        // Looks for <script> OR event handlers like " onclick="
        const xssPattern = /<script\b[^>]*>([\s\S]*?)<\/script>|on\w+="[^"]*"/i;

        if (xssPattern.test(val) || val.includes('alert(')) {
            // Trigger Simulation
            document.getElementById('fakeAlert').classList.remove('hidden');
        } else {
            // Normal behavior
            const input = document.getElementById('xssInput');
            input.value = "Updated: " + val;
            setTimeout(() => input.value = val, 1000);
        }
    }

    function closeAlert() {
        document.getElementById('fakeAlert').classList.add('hidden');
        document.getElementById('successMsg').classList.remove('hidden');
    }
</script>