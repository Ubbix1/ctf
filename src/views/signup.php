<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        $result = register_user(
            $_POST['username'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );
        
        if ($result['success']) {
            $message = $result['message'];
            $message_type = 'success';
            header('Refresh: 2; url=/ctf');
        } else {
            $message = $result['message'];
            $message_type = 'error';
        }
    }
}

// If already logged in, redirect to CTF hub
if (is_logged_in()) {
    header('Location: /ctf');
    exit;
}
?>

<div class="max-w-md mx-auto mt-12 md:mt-24 animate-fade-in-up px-4">
    
    <!-- Signup Card -->
    <div class="border border-neutral-800 bg-neutral-950 rounded-2xl p-8 shadow-2xl relative overflow-hidden group">
        
        <!-- Aesthetic Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-1 bg-white blur-[25px] opacity-10 group-hover:opacity-20 transition-opacity duration-500"></div>

        <!-- Message Alert -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $message_type === 'success' ? 'bg-green-900/20 border border-green-800 text-green-400' : 'bg-red-900/20 border border-red-800 text-red-400' ?>">
                <p class="text-sm"><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-white rounded-full mx-auto mb-5 flex items-center justify-center shadow-[0_0_15px_rgba(255,255,255,0.3)]">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2 tracking-tight">Join the CTF</h1>
            <p class="text-neutral-400 text-sm">Create your account to start playing challenges.</p>
        </div>

        <!-- Form -->
        <form method="POST" action="/signup" class="space-y-5">
            <input type="hidden" name="action" value="signup">
            
            <div>
                <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Username</label>
                <div class="relative">
                    <input type="text" name="username" required placeholder="your_username" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" required placeholder="user@plexaur.com" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••" minlength="6" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
                <p class="text-[10px] text-neutral-600 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-2">Confirm Password</label>
                <div class="relative">
                    <input type="password" name="confirm_password" required placeholder="••••••••" minlength="6" class="w-full bg-black border border-neutral-800 rounded-lg py-3 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition-all placeholder-neutral-700">
                    <div class="absolute left-3 top-3 text-neutral-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-white text-black font-bold py-3.5 rounded-lg hover:bg-neutral-200 transition-all active:scale-[0.98] flex justify-center items-center gap-2 mt-2">
                <span>Create Account</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-neutral-800"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-neutral-950 px-2 text-neutral-600">Already have an account?</span></div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-neutral-500">
            <a href="/login" class="text-white font-medium hover:underline">Sign In</a>
        </div>
    </div>
</div>
