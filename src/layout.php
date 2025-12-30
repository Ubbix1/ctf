<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTF Plexaur</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        neutral: { 850: '#1f1f1f', 900: '#171717', 950: '#0a0a0a' }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #000; color: #fff; }
        /* Custom Scrollbar for that hacker feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="flex flex-col min-h-screen antialiased selection:bg-white selection:text-black">

    <!-- MODERN NAVIGATION -->
    <nav class="fixed top-0 w-full z-50 border-b border-neutral-800 bg-black/60 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 group">
                <img src="https://plexaur.com/assets/svg/plexaur.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                <span class="font-bold tracking-tight text-lg">CTF Plexaur</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-neutral-400">
                <div class="relative group">
                    <button class="hover:text-white transition-colors py-2 flex items-center gap-1">
                        Tools 
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute top-full left-0 w-48 bg-neutral-900 border border-neutral-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0">
                        <a href="/base64" class="block px-4 py-2 hover:bg-neutral-800 hover:text-white rounded-t-lg">Base64</a>
                        <a href="/steg" class="block px-4 py-2 hover:bg-neutral-800 hover:text-white">Steganography</a>
                        <a href="/invert" class="block px-4 py-2 hover:bg-neutral-800 hover:text-white">Color Invert</a>
                        <a href="/pcap" class="block px-4 py-2 hover:bg-neutral-800 hover:text-white">PCAP Decoder</a>
                        <a href="/cap-reader" class="block px-4 py-2 hover:bg-neutral-800 hover:text-white rounded-b-lg">CAP Reader</a>
                    </div>
                </div>
                <a href="/ctf" class="hover:text-white transition-colors">CTF Challenges</a>
                <a href="https://plexaur.com/php/login" class="px-4 py-2 bg-white text-black rounded-full font-semibold hover:bg-neutral-200 transition-colors">Login</a>
            </div>

            <!-- Mobile Menu Button (Simple JS trigger) -->
            <button id="mobile-menu-btn" class="md:hidden text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu" class="hidden md:hidden absolute top-16 left-0 w-full bg-neutral-900 border-b border-neutral-800 p-4 space-y-4">
            <a href="/base64" class="block text-neutral-400 hover:text-white">Base64</a>
            <a href="/steg" class="block text-neutral-400 hover:text-white">Steganography</a>
            <a href="/ctf" class="block text-neutral-400 hover:text-white">CTF</a>
            <a href="https://plexaur.com/php/login" class="block text-white font-semibold">Login</a>
        </div>
    </nav>

    <!-- CONTENT INJECTION -->
    <!-- We add padding-top (pt-24) to account for the fixed header -->
    <main class="flex-grow pt-24 px-6 relative z-10">
        <div class="max-w-7xl mx-auto">
            <?php 
            // This is where the specific page content is loaded
            if (file_exists($content_view)) {
                include $content_view;
            } else {
                echo "<h1 class='text-red-500'>Error: View file not found ($content_view)</h1>";
            }
            ?>
        </div>
    </main>

    <!-- MODERN FOOTER -->
    <footer class="border-t border-neutral-800 bg-black mt-24 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1">
                <h4 class="text-xl font-bold text-white mb-4 flex flex-row gap-2">
                <span><img src="https://plexaur.com/assets/svg/plexaur.png" alt="Logo" width="28" height="28" class="d-inline-block align-text-top"></span>    
                CTF Plexaur</h4>
                <p class="text-neutral-500 text-sm leading-relaxed">
                    Advanced security tools for the modern web. Capture the flag, decode the matrix.
                </p>
            </div>
            
            <div>
                <h6 class="font-semibold text-white mb-4">Tools</h6>
                <ul class="space-y-2 text-sm text-neutral-500">
                    <li><a href="/base64" class="hover:text-neutral-300 transition-colors">Base 64</a></li>
                    <li><a href="/invert" class="hover:text-neutral-300 transition-colors">Color Invert</a></li>
                    <li><a href="/steg" class="hover:text-neutral-300 transition-colors">Steganography</a></li>
                </ul>
            </div>

            <div>
                <h6 class="font-semibold text-white mb-4">Network</h6>
                <ul class="space-y-2 text-sm text-neutral-500">
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Github</a></li>
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Twitter</a></li>
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Discord</a></li>
                </ul>
            </div>

            <div>
                <h6 class="font-semibold text-white mb-4">Plexaur Ecosystem</h6>
                <ul class="space-y-2 text-sm text-neutral-500">
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Survey</a></li>
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Project Spot</a></li>
                    <li><a href="#" class="hover:text-neutral-300 transition-colors">Demo</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center border-t border-neutral-900 mt-12 pt-8 text-neutral-600 text-sm">
            &copy; <?php echo date("Y"); ?> CTF Plexaur. All rights reserved.
        </div>
    </footer>

    <script>
        // Simple Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>