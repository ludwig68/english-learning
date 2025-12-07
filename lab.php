<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="mb-8">
    <div class="mb-6">
        <p class="text-xs text-slate-500 mb-1">
            Khu vực thử nghiệm
        </p>
        <h1 class="text-2xl font-semibold">
            Lab
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Đây là khu vực để bạn thử nghiệm các tính năng mới: mini game, bài test đặc biệt, playground từ vựng,...
            Hiện tại Lab đang để trống để bạn tự sáng tạo sau.
        </p>
    </div>

    <div class="card-glass p-6 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
            <i class="fa-solid fa-flask text-slate-500"></i>
        </div>
        <h2 class="text-sm font-semibold text-slate-800 mb-2">
            Lab đang trong giai đoạn xây dựng
        </h2>
        <p class="text-xs text-slate-500 mb-3 max-w-md">
            Bạn có thể dùng trang này để triển khai các ý tưởng liên quan đến tiếng Anh:
            ví dụ: trò chơi đoán từ, quiz nhanh, kiểm tra từ vựng theo chủ đề,...
        </p>
        <p class="text-[0.7rem] text-slate-400 italic">
            Gợi ý: tạo file riêng trong thư mục <code>/lab</code> và include vào đây.
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
