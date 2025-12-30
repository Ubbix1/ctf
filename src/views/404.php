<div class="flex flex-col items-center justify-center min-h-[60vh] text-center animate-fade-in-up px-4">
    
    <!-- Background "Ghost" Text -->
    <h1 class="text-9xl font-bold text-neutral-900 select-none mb-[-50px] scale-150 opacity-50">404</h1>

    <!-- Main Message -->
    <div class="relative z-10">
        <h2 class="text-5xl md:text-6xl font-bold text-white mb-4 tracking-tight">Signal Lost</h2>
        <p class="text-neutral-400 text-lg max-w-md mx-auto">
            The requested path is unreachable or has been redacted by the system administrator.
        </p>
    </div>

    <!-- Terminal Simulation -->
    <div class="mt-12 w-full max-w-md bg-neutral-950 border border-neutral-800 rounded-xl p-6 font-mono text-xs text-left shadow-2xl relative group hover:border-neutral-700 transition-colors">
        
        <!-- Terminal Header -->
        <div class="flex gap-1.5 mb-4 border-b border-neutral-900 pb-2">
            <div class="w-2.5 h-2.5 rounded-full bg-red-500/20 group-hover:bg-red-500 transition-colors"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/20 group-hover:bg-yellow-500 transition-colors"></div>
            <div class="w-2.5 h-2.5 rounded-full bg-green-500/20 group-hover:bg-green-500 transition-colors"></div>
        </div>

        <!-- Terminal Body -->
        <div class="space-y-2">
            <p class="text-neutral-500">
                <span class="text-green-500">root@plexaur:~$</span> initiate_handshake --target="<?php echo htmlspecialchars($path ?? 'unknown'); ?>"
            </p>
            <p class="text-neutral-500">
                > resolving_host... <span class="text-white">done</span>
            </p>
            <p class="text-neutral-500">
                > verifying_path... 
            </p>
            <p class="text-red-500 font-bold">
                > FATAL_ERROR: 404_NOT_FOUND
            </p>
            <p class="text-neutral-600 italic">
                > The resource does not exist in the current directory.
            </p>
            <div class="flex items-center gap-1 mt-4">
                <span class="text-green-500">root@plexaur:~$</span>
                <span class="w-2 h-4 bg-green-500 animate-pulse"></span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-10 flex gap-4">
        <button onclick="history.back()" class="px-6 py-3 border border-neutral-700 text-neutral-300 font-medium rounded-full hover:bg-neutral-800 hover:text-white transition-all">
            Go Back
        </button>
        <a href="/" class="px-6 py-3 bg-white text-black font-bold rounded-full hover:bg-neutral-200 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Return Home
        </a>
    </div>

</div>