<?php
// includes/user_sidebar.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo có $pdo (nếu file này được include trước khi require db)
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/db.php';
}

// Lấy id user từ session
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$currentUser = [
    'full_name' => null,
    'avatar'    => null,
];

if ($userId > 0) {
    $stmt = $pdo->prepare("SELECT full_name, avatar FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $currentUser = $row;
    }
}

$current = basename($_SERVER['SCRIPT_NAME']); // vd: dashboard.php, learn.php,...

// Tên user ưu tiên full_name, nếu không có thì lấy username từ session
$userNameSidebar = !empty($currentUser['full_name'])
    ? $currentUser['full_name']
    : ($_SESSION['username'] ?? 'User');

function user_sidebar_class(string $file, string $current): string
{
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
            <a href="/user/profile.php" class="block">
                <?php
                $avatarSrc = '';
                if (!empty($currentUser['avatar'])) {
                    $avatarSrc = $currentUser['avatar'];
                    // Nếu lưu dạng relative path thì thêm / phía trước
                    if (!preg_match('~^https?://~', $avatarSrc)) {
                        $avatarSrc = '/' . ltrim($avatarSrc, '/');
                    }
                }
                ?>

                <?php if (!empty($avatarSrc)): ?>
                    <img src="<?= htmlspecialchars($avatarSrc) ?>"
                         alt="<?= htmlspecialchars($currentUser['full_name'] ?? 'User') ?>"
                         class="w-10 h-10 rounded-full object-cover border border-slate-200">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                        <i class="fa-solid fa-user text-sm" style="color:#7AE582;"></i>
                    </div>
                <?php endif; ?>
            </a>

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

            <!-- TODO: cập nhật đúng file history sau này -->
            <a href="/user/history.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('history.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-regular fa-clock text-xs"></i>
                    <span>Lịch sử học</span>
                </span>
                <i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
            </a>

            <!-- TODO: cập nhật đúng file report sau này -->
            <a href="/user/report.php"
               class="flex items-center justify-between px-3 py-2 rounded-xl <?= user_sidebar_class('report.php', $current) ?>">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-question text-xs"></i>
                    <span>Báo cáo tiến độ</span>
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
