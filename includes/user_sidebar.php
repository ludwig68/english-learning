<?php
// includes/user_sidebar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['SCRIPT_NAME']); // vd: dashboard.php, learn.php,...

// Tên user
$userNameSidebar = $userName ?? ($_SESSION['username'] ?? 'User');

function user_sidebar_class(string $file, string $current): string {
    if ($file === $current) {
        // Active item: dùng màu chủ đạo
        return 'bg-[#7AE582] text-white shadow-sm';
    }
    return 'text-slate-600 hover:bg-slate-100';
}
?>

<aside class="w-full md:w-60 md:flex-shrink-0 md:pr-2 lg:pr-4">
    <div class="rounded-2xl p-4 bg-white/90 border border-slate-200 shadow-sm">
        <!-- Avatar + tên -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                <i class="fa-solid fa-user text-sm" style="color:#7AE582;"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-[0.7rem] text-slate-500">Học viên</span>
                <span class="text-sm font-semibold text-slate-800 truncate">
                    <?= htmlspecialchars($userNameSidebar) ?>
                </span>
            </div>
        </div>

        <!-- Menu sidebar -->
        <nav class="space-y-1 text-sm">
            <a href="/user/dashboard.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('dashboard.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-xs"></i>
                    <span>Dashboard</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
            </a>

            <a href="/user/learn.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('learn.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>Lộ trình Level</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
            </a>

            <a href="/user/flashcard.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('flashcard.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-clone text-xs"></i>
                    <span>Flashcard</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
            </a>

            <a href="/user/practice.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('practice.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-question text-xs"></i>
                    <span>Luyện tập</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
            </a>

            <hr class="my-3 border-slate-200">

            <a href="/index.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl text-[0.75rem] text-slate-500 hover:text-slate-700 hover:bg-slate-50">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span>Về trang giới thiệu</span>
                </span>
            </a>
        </nav>
    </div>
</aside>
