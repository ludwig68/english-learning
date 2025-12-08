<?php
// user/dashboard.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Ẩn navbar ngoài, dùng layout full-width
$hideMainNavbar = true;
$pageTitle = 'Dashboard | English Learning System';

require_once __DIR__ . '/../includes/header.php';

$userName = $_SESSION['username'] ?? 'User';

// Lấy thống kê (nếu lỗi thì cho 0 để không bị gãy trang)
try {
    $totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
} catch (Throwable $e) {
    $totalLevels = 0;
}

try {
    $totalVocab = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
} catch (Throwable $e) {
    $totalVocab = 0;
}
?>

<div class="flex flex-col md:flex-row gap-4 lg:gap-6">
    <!-- Sidebar trái (sát mép trái màn hình vì layout full width) -->
    <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>

    <!-- Nội dung chính -->
    <section class="flex-1">
        <!-- Header dashboard -->
        <div class="mb-6">
            <p class="text-xs text-slate-500 mb-1">
                Xin chào,
            </p>
            <h1 class="text-2xl font-semibold flex items-center flex-wrap gap-2">
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#7AE582]/10 border border-[#7AE582]/40 text-xs text-slate-800">
                    <i class="fa-solid fa-user text-[0.7rem]" style="color:#7AE582;"></i>
                    <?= htmlspecialchars($userName) ?>
                </span>
                
            </h1>
            <p class="text-xs text-slate-500 mt-2 max-w-xl">
                Bắt đầu với lộ trình phù hợp, ôn lại từ vựng bằng flashcard và củng cố kiến thức qua bài luyện tập.
            </p>
        </div>

        <!-- Thống kê nhanh -->
        <div class="grid gap-3 sm:grid-cols-2 mb-6">
            <div class="px-4 py-3 rounded-2xl bg-white border border-slate-200 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-sm" style="color:#7AE582;"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Số Level hiện có</p>
                    <p class="text-lg font-semibold text-slate-800"><?= $totalLevels ?></p>
                </div>
            </div>

            <div class="px-4 py-3 rounded-2xl bg-white border border-slate-200 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                    <i class="fa-solid fa-book text-sm" style="color:#7AE582;"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Từ vựng trong hệ thống</p>
                    <p class="text-lg font-semibold text-slate-800"><?= $totalVocab ?></p>
                </div>
            </div>
        </div>

        <!-- Hành động nhanh -->
        <section class="mb-8">
            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                Bắt đầu học ngay hôm nay
            </h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <!-- Lộ trình Level -->
                <a href="/user/learn.php"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#7AE582] hover:bg-[#7AE582]/5 transition flex flex-col shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                            <i class="fa-solid fa-road text-sm" style="color:#7AE582;"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">
                            Lộ trình Level
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">
                        Xem toàn bộ Level và chọn lộ trình phù hợp với trình độ hiện tại của bạn.
                    </p>
                    <span class="mt-auto text-[0.7rem] text-slate-700 flex items-center gap-1">
                        Vào lộ trình
                        <i class="fa-solid fa-arrow-right text-[0.6rem]" style="color:#7AE582;"></i>
                    </span>
                </a>

                <!-- Flashcard -->
                <a href="/user/flashcard.php?level_id=1"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#7AE582] hover:bg-[#7AE582]/5 transition flex flex-col shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                            <i class="fa-regular fa-clone text-sm" style="color:#7AE582;"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">
                            Flashcard
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">
                        Lật thẻ, ghi nhớ từ vựng qua hình ảnh và ví dụ. Có thể đổi Level trực tiếp trong trang flashcard.
                    </p>
                    <span class="mt-auto text-[0.7rem] text-slate-700 flex items-center gap-1">
                        Mở flashcard
                        <i class="fa-solid fa-arrow-right text-[0.6rem]" style="color:#7AE582;"></i>
                    </span>
                </a>

                <!-- Luyện tập -->
                <a href="/user/practice.php?level_id=1"
                   class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#7AE582] hover:bg-[#7AE582]/5 transition flex flex-col shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[#7AE582]/15 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-question text-sm" style="color:#7AE582;"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-slate-800">
                            Luyện tập
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">
                        Làm bài trắc nghiệm, điền từ, nghe – chép lại, xem kết quả bằng popup trực quan.
                    </p>
                    <span class="mt-auto text-[0.7rem] text-slate-700 flex items-center gap-1">
                        Vào luyện tập
                        <i class="fa-solid fa-arrow-right text-[0.6rem]" style="color:#7AE582;"></i>
                    </span>
                </a>
            </div>
        </section>
    </section>
</div>


