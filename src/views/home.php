<!-- Hero Section -->
<div class="text-center max-w-4xl mx-auto mb-32 mt-12">
    <h1 class="text-5xl md:text-8xl font-bold pb-2 tracking-tighter mb-8 bg-clip-text text-transparent bg-gradient-to-b from-white to-neutral-500">
        Let's begin with<br />Capturing the Flag.
    </h1>
    <p class="text-xl text-neutral-400 mb-10">
        Advanced cryptography, steganography, and network analysis tools.
        <a href="https://plexaur.com" class="text-white underline hover:text-neutral-300 transition-colors decoration-neutral-700 underline-offset-4 ml-2">- Plexaur</a>
    </p>
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="#tools" class="px-8 py-3 bg-white text-black font-semibold rounded-full hover:bg-neutral-200 transition-all">
            Get Started
        </a>
        <a href="/ctf" class="px-8 py-3 border border-neutral-700 text-neutral-300 font-medium rounded-full hover:bg-neutral-900 transition-all">
            View Challenges
        </a>
    </div>
</div>

<!-- Tools Grid -->
<section id="tools" class="mb-32">
    <div class="flex items-center justify-between mb-12">
        <h2 class="text-3xl font-bold text-white tracking-tight">Tools we provide</h2>
        <div class="h-px bg-neutral-800 flex-grow ml-8"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Base64 Card -->
        <a href="/base64" class="group relative p-6 border border-neutral-800 bg-neutral-950 rounded-xl hover:border-neutral-500 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-neutral-900 rounded-full flex items-center justify-center text-white font-mono text-sm border border-neutral-800">B64</div>
                <svg class="w-5 h-5 text-neutral-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Base64</h3>
            <p class="text-neutral-500 text-sm">Encode and decode data formats instantly.</p>
        </a>

        <!-- Steg Card -->
        <a href="/steg" class="group relative p-6 border border-neutral-800 bg-neutral-950 rounded-xl hover:border-neutral-500 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-neutral-900 rounded-full flex items-center justify-center text-white font-mono text-sm border border-neutral-800">Img</div>
                <svg class="w-5 h-5 text-neutral-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Steganography</h3>
            <p class="text-neutral-500 text-sm">Hide secret data inside image files.</p>
        </a>

        <!-- Invert Card -->
        <a href="/invert" class="group relative p-6 border border-neutral-800 bg-neutral-950 rounded-xl hover:border-neutral-500 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-neutral-900 rounded-full flex items-center justify-center text-white font-mono text-sm border border-neutral-800">Inv</div>
                <svg class="w-5 h-5 text-neutral-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Color Invert</h3>
            <p class="text-neutral-500 text-sm">Simple image manipulation tools.</p>
        </a>

        <!-- CTF Card -->
        <a href="/ctf" class="group relative p-6 border border-neutral-800 bg-neutral-950 rounded-xl hover:border-neutral-500 transition-all duration-300 hover:-translate-y-1">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-neutral-900 rounded-full flex items-center justify-center text-white font-mono text-sm border border-neutral-800">CTF</div>
                <svg class="w-5 h-5 text-neutral-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">CTF Hub</h3>
            <p class="text-neutral-500 text-sm">Access the latest challenges.</p>
        </a>

    </div>
</section>

<!-- Subscribe Section -->
<section id="subscribe" class="max-w-2xl mx-auto text-center py-20 border border-neutral-900 rounded-2xl bg-neutral-950/50">
    <h2 class="text-2xl font-bold text-white mb-2">Stay Updated</h2>
    <p class="text-neutral-400 mb-8">Get the latest tools and challenges sent to your inbox.</p>
    
    <form class="flex flex-col sm:flex-row gap-3 px-8">
        <input type="email" placeholder="Enter your email" class="flex-grow bg-black border border-neutral-800 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-white transition-colors placeholder-neutral-600">
        <button type="submit" class="bg-white text-black font-semibold px-6 py-3 rounded-lg hover:bg-neutral-200 transition-colors">
            Subscribe
        </button>
    </form>
</section>