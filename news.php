<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="mb-8">
    <div class="mb-6">
        <p class="text-xs text-slate-500 mb-1">
            Cập nhật mới nhất
        </p>
        <h1 class="text-2xl font-semibold">
            Tin tức &amp; thông báo
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Đây là trang tin tức demo. Sau này bạn có thể kết nối với bảng <strong>news</strong> trong database
            để load bài viết động.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <!-- Tin demo 1 -->
        <article class="card-glass p-4 flex flex-col gap-2">
            <div class="text-[0.7rem] text-slate-400 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-[#15803d] border border-emerald-200">
                    Cập nhật
                </span>
                <span>12/2025</span>
            </div>
            <h2 class="text-sm font-semibold text-slate-800">
                Ra mắt hệ thống English Learning phiên bản đầu tiên
            </h2>
            <p class="text-xs text-slate-500">
                Phiên bản demo hỗ trợ học từ vựng bằng flashcard, practice trắc nghiệm, upload hình ảnh
                và file audio...
            </p>
        </article>

        <!-- Tin demo 2 -->
        <article class="card-glass p-4 flex flex-col gap-2">
            <div class="text-[0.7rem] text-slate-400 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-600 border border-sky-200">
                    Sắp ra mắt
                </span>
                <span>Coming soon</span>
            </div>
            <h2 class="text-sm font-semibold text-slate-800">
                Thống kê tiến độ học &amp; bảng xếp hạng
            </h2>
            <p class="text-xs text-slate-500">
                Tính năng xem tiến độ học, số từ đã hoàn thành, điểm practice và bảng xếp hạng bạn bè
                đang được phát triển.
            </p>
        </article>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
