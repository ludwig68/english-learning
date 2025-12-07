<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="mb-8">
    <div class="mb-6">
        <p class="text-xs text-slate-500 mb-1">
            Câu hỏi thường gặp
        </p>
        <h1 class="text-2xl font-semibold">
            Hỗ trợ &amp; hướng dẫn
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Nếu bạn gặp vấn đề khi đăng nhập, học bài hoặc sử dụng hệ thống, hãy xem một số câu hỏi thường gặp bên dưới.
        </p>
    </div>

    <div class="space-y-3">
        <div class="card-glass p-4">
            <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-[#16a34a] text-xs"></i>
                Tôi quên mật khẩu thì làm sao?
            </h2>
            <p class="mt-2 text-xs text-slate-500">
                Phiên bản demo hiện chưa có chức năng quên mật khẩu. Bạn có thể tự tạo tài khoản mới
                (hoặc sau này bổ sung chức năng gửi email reset password).
            </p>
        </div>

        <div class="card-glass p-4">
            <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-[#16a34a] text-xs"></i>
                Vì sao tôi không thấy từ vựng trong Level?
            </h2>
            <p class="mt-2 text-xs text-slate-500">
                Hãy đăng nhập với tài khoản <strong>admin</strong>, vào trang <strong>Quản lý từ vựng</strong>
                để thêm từ mới cho từng Level. Sau đó quay lại trang học là sẽ thấy.
            </p>
        </div>

        <div class="card-glass p-4">
            <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-[#16a34a] text-xs"></i>
                Tôi upload ảnh/audio mà không thấy hiện?
            </h2>
            <p class="mt-2 text-xs text-slate-500">
                Kiểm tra lại phân quyền thư mục <code>uploads/images</code> và <code>uploads/audio</code> trên host
                (cấp quyền ghi cho webserver). Ngoài ra kiểm tra lại đường dẫn lưu trong database.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
