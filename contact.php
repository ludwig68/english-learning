<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

// Đơn giản: xử lý POST hiển thị thông báo (không gửi mail)
$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg   = trim($_POST['message'] ?? '');

    if ($name && $email && $msg) {
        $successMsg = 'Cảm ơn bạn đã liên hệ! (Demo: hiện chưa gửi email thật)';
    }
}
?>

<section class="mb-8 max-w-3xl">
    <div class="mb-6">
        <p class="text-xs text-slate-500 mb-1">
            Kết nối với chúng tôi
        </p>
        <h1 class="text-2xl font-semibold">
            Liên hệ
        </h1>
        <p class="text-xs text-slate-500 mt-2">
            Nếu bạn có góp ý, bug, hoặc ý tưởng phát triển tính năng mới, hãy gửi tin nhắn cho chúng tôi.
        </p>
    </div>

    <?php if ($successMsg): ?>
        <div class="mb-4 text-xs text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md p-3">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <div class="card-glass p-5">
        <form method="post" class="space-y-3">
            <div>
                <label class="block text-xs text-slate-600 mb-1">Họ tên</label>
                <input type="text" name="name"
                       class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                       required>
            </div>
            <div>
                <label class="block text-xs text-slate-600 mb-1">Email</label>
                <input type="email" name="email"
                       class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                       required>
            </div>
            <div>
                <label class="block text-xs text-slate-600 mb-1">Nội dung</label>
                <textarea name="message" rows="4"
                          class="w-full rounded-md bg-white border border-slate-300 text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-[#7AE582]"
                          required></textarea>
            </div>
            <button
                class="mt-2 inline-flex items-center gap-2 text-xs px-4 py-2 rounded-md bg-[#7AE582] text-slate-900 font-semibold hover:bg-emerald-300 transition">
                <i class="fa-solid fa-paper-plane text-[0.7rem]"></i>
                Gửi liên hệ
            </button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
