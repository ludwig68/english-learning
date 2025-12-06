<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>English Learning System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#7AE582' }
                }
            }
        }
    </script>

    <!-- Animate.css -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

<!-- Navbar sáng -->
<nav class="w-full border-b border-slate-200 bg-white/90 backdrop-blur sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
        <!-- Logo / Brand -->
        <div class="flex items-center gap-6">
            <a href="/index.php" class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-[#7AE582] shadow-[0_0_10px_#7AE582]"></span>
                <span class="font-semibold tracking-wide text-sm sm:text-base text-slate-800">
                    English Learning
                </span>
            </a>

            <!-- Menu chính (desktop) -->
            <div class="hidden md:flex items-center gap-4 text-xs text-slate-600">
                <a href="/about.php" class="hover:text-[#16a34a]">
                    Giới thiệu
                </a>
                <a href="/news.php" class="hover:text-[#16a34a]">
                    Tin tức
                </a>
                <a href="/support.php" class="hover:text-[#16a34a]">
                    Hỗ trợ
                </a>
                <a href="/contact.php" class="hover:text-[#16a34a]">
                    Liên hệ
                </a>
                <a href="/lab.php" class="hover:text-[#16a34a]">
                    Lab
                </a>
            </div>
        </div>

        <!-- Search + Auth -->
        <div class="flex items-center gap-3 text-sm">
            <!-- Search (ẩn trên màn hình rất nhỏ) -->
            <form action="/index.php" method="get"
                  class="hidden sm:flex items-center bg-slate-100 border border-slate-300 rounded-full px-3 py-1.5 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-2"></i>
                <input
                    type="text"
                    name="q"
                    placeholder="Tìm level hoặc từ vựng..."
                    class="bg-transparent border-none outline-none text-xs text-slate-700 placeholder:text-slate-400 w-full"
                >
            </form>

            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/admin/index.php"
                       class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-300 text-slate-700 hover:border-[#7AE582] hover:text-[#7AE582] transition">
                        <i class="fa-solid fa-gauge-high text-xs"></i> Admin
                    </a>
                <?php endif; ?>
                <span class="hidden sm:inline text-xs text-slate-500">
                    <?= htmlspecialchars($_SESSION['username']) ?>
                </span>
                <a href="/auth/logout.php"
                   class="px-3 py-1.5 rounded-full border border-slate-300 text-xs text-slate-700 hover:bg-red-50 hover:border-red-400 hover:text-red-500 transition">
                    Đăng xuất
                </a>
            <?php else: ?>
                <a href="/auth/login.php" class="text-slate-600 hover:text-slate-900 text-xs sm:text-sm">
                    Đăng nhập
                </a>
                <a href="/auth/register.php"
                   class="px-3 py-1.5 rounded-full bg-[#7AE582] text-slate-900 text-xs sm:text-sm font-semibold hover:bg-emerald-300 transition">
                    Đăng ký
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="flex-1 w-full">
    <div class="max-w-6xl mx-auto px-4 py-6 sm:py-10">
