<?php
// includes/user_navbar.php

$current = basename($_SERVER['SCRIPT_NAME']); // ví dụ: dashboard.php, learn.php, ...

function user_nav_class($file, $current) {
    if ($file === $current) {
        return 'bg-[#7AE582] text-slate-900';
    }
    return 'bg-slate-100 text-slate-600 hover:bg-slate-200';
}
?>

<div class="mb-5">
    <div class="card-glass p-2 flex flex-wrap gap-2 text-xs">
        <a href="/user/dashboard.php"
           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-200 <?= user_nav_class('dashboard.php', $current) ?>">
            <i class="fa-solid fa-gauge text-[0.7rem]"></i>
            Tổng quan
        </a>

        <a href="/user/learn.php"
           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-200 <?= user_nav_class('learn.php', $current) ?>">
            <i class="fa-solid fa-route text-[0.7rem]"></i>
            Học theo Level
        </a>
    </div>
</div>
