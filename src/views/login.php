<div class="max-w-md mx-auto mt-12 md:mt-24 animate-fade-in-up px-4">
    
    <!-- Login Card -->
    <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-8 shadow-2xl relative overflow-hidden group">
        
        <!-- Aesthetic Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-1 bg-white blur-[25px] opacity-10 group-hover:opacity-20 transition-opacity duration-500"></div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-white rounded-full mx-auto mb-5 flex items-center justify-center shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2 tracking-tight">Welcome Back</h1>
            <p class="text-neutral-400 text-sm">Enter your credentials to access the terminal.</p>
        </div>

        <!-- Form -->
        <form onsubmit="handleLogin(event)" class="space-y-5">
            <div>
                <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Email Address</label>
                <div class="relative">
                    <input type="email" required placeholder="user@plexaur.com" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-2">
                    <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest">Password</label>
                    <a href="#" class="text-xs text-neutral-400 hover:text-white transition-colors">Forgot Password?</a>
                </div>
                <div class="relative">
                    <input type="password" required placeholder="••••••••" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
            </div>

            <button type="submit" id="loginBtn" class="w-full bg-white text-black font-bold py-3.5 rounded-lg hover:bg-neutral-200 transition-all active:scale-[0.98] flex justify-center items-center gap-2 mt-2">
                <span>Sign In</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-neutral-800"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-neutral-950 px-2 text-neutral-600">Or continue with</span></div>
        </div>

        <!-- Social (Visual Only) -->
        <div class="grid grid-cols-2 gap-4">
            <button class="flex justify-center items-center py-2.5 border border-neutral-800 rounded-lg hover:bg-neutral-900 transition-colors">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            </button>
            <button class="flex justify-center items-center py-2.5 border border-neutral-800 rounded-lg hover:bg-neutral-900 transition-colors">
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </button>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-neutral-500">
            Don't have an account? <a href="#" class="text-white font-medium hover:underline">Request Access</a>
        </div>
    </div>
</div>

<script>
    function handleLogin(e) {
        e.preventDefault();
        const btn = document.getElementById('loginBtn');
        const originalContent = btn.innerHTML;
        
        // Simulate Loading State
        btn.innerHTML = '<div class="w-5 h-5 border-2 border-black border-t-transparent rounded-full animate-spin"></div>';
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        btn.disabled = true;

        // Mock Login Delay (Redirects to CTF Hub after 1.5s)
        setTimeout(() => {
            window.location.href = '/ctf';
        }, 1500);
    }
</script>