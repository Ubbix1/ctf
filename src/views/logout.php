<?php
// src/views/logout.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/auth.php';

logout_user();

// Redirect after 1 second
header('Refresh: 1; url=/');
?>

<div class="max-w-md mx-auto mt-24 text-center">
    <div class="bg-neutral-950 border border-neutral-800 rounded-2xl p-8">
        <div class="w-12 h-12 bg-white rounded-full mx-auto mb-5 flex items-center justify-center shadow-[0_0_15px_rgba(255,255,255,0.3)]">
            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Goodbye!</h1>
        <p class="text-neutral-400 mb-6">You've been logged out successfully.</p>
        <p class="text-neutral-500 text-sm">Redirecting to home...</p>
    </div>
</div>
