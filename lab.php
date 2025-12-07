<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

/**

 * - Nếu chưa có ảnh, sẽ hiển thị chữ cái "H" làm placeholder
 */
$profileImageWebPath  = '/uploads/images/z6585190311694_dd360cc1176faf703d256ad8dc9b8426.jpg';
$profileImageRealPath = __DIR__ . $profileImageWebPath;
$hasProfileImage      = file_exists($profileImageRealPath);
?>

<section class="bg-gradient-to-br from-emerald-600 via-primary to-emerald-700 text-white py-12">
    <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <!-- Avatar / Ảnh sinh viên -->
        <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white/10 backdrop-blur-md border border-white/30 shadow-lg overflow-hidden flex items-center justify-center">
            <?php if ($hasProfileImage): ?>
                <img src="<?= htmlspecialchars($profileImageWebPath) ?>"
                    alt="Ảnh sinh viên"
                    class="w-full h-full object-cover">
            <?php else: ?>
                <!-- Placeholder nếu chưa có ảnh -->
                <span class="text-3xl sm:text-4xl font-bold">
                    T
                </span>
            <?php endif; ?>
        </div>

        <!-- Thông tin sinh viên -->
        <div class="text-center sm:text-left">
            <p class="text-emerald-100 font-medium text-sm mb-1 uppercase tracking-wide">
                Thông Tin Sinh Viên
            </p>
            <h1 class="text-3xl sm:text-4xl font-bold">
                Hoàng Nhật Trường
            </h1>
            <p class="mt-1 text-sm text-emerald-50/90">
                Khoa Công Nghệ Thông Tin – Thực hành Lập trình Web
            </p>
            <div class="flex flex-wrap justify-center sm:justify-start gap-3 mt-4 text-xs sm:text-sm text-white font-semibold">
                <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                    <i class="fa-regular fa-id-card text-[0.7rem]"></i>
                    <span>DH52201675</span>
                </span>
                <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                    <i class="fa-solid fa-layer-group text-[0.7rem]"></i>
                    <span>D22_TH07</span>
                </span>
                <span class="flex items-center gap-1 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                    <i class="fa-solid fa-code-branch text-[0.7rem]"></i>
                    <span>TH Lập Trình Web – Nhóm 19 Thứ 4 Ca 3</span>
                </span>
            </div>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 -mt-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cột trái: danh sách lab + chỗ trống hình ảnh project -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Danh sách Lab -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 flex items-center justify-center rounded-full bg-primary/10 text-primary">
                        <i class="fa-solid fa-flask text-sm"></i>
                    </span>
                    <span>Danh sách Lab thực hành</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <a href="lab<?php echo $i; ?>.php"
                            class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-primary/60 hover:bg-primary/5 transition-colors group">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500 group-hover:bg-white group-hover:text-primary shadow-sm">
                                <span class="font-bold text-sm"><?php echo $i; ?></span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-700 group-hover:text-primary">
                                    Bài tập Tuần <?php echo $i; ?>
                                </p>
                                <p class="text-xs text-slate-400 group-hover:text-slate-500">
                                    Xem chi tiết &rarr;
                                </p>
                            </div>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Cột phải: tài khoản demo + social -->
        <div class="lg:col-span-1 space-y-4">
            <!-- Tài khoản demo -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 bg-primary/5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-key text-primary text-xs"></i>
                        <span>Tài khoản dùng thử</span>
                    </h3>
                    <span class="text-[0.65rem] uppercase tracking-wider text-primary font-bold border border-primary/30 px-2 py-0.5 rounded-full bg-primary/5">
                        DEV
                    </span>
                </div>

                <div class="p-4 grid grid-cols-1 gap-4">
                    <!-- Admin -->
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-primary/20 bg-primary/5">
                        <div class="mt-1 w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <i class="fa-solid fa-user-shield text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-700 mb-1">Quản trị viên (Admin)</p>
                            <div class="space-y-1 text-xs text-slate-600">
                                <div class="flex items-center justify-between group cursor-pointer"
                                    onclick="copyToClipboard('admin')">
                                    <span>User: <strong class="font-mono text-primary">admin</strong></span>
                                    <i class="fa-regular fa-copy text-slate-300 group-hover:text-primary transition-colors" title="Copy"></i>
                                </div>
                                <div class="flex items-center justify-between group cursor-pointer"
                                    onclick="copyToClipboard('123456')">
                                    <span>Pass: <strong class="font-mono text-primary">123456</strong></span>
                                    <i class="fa-regular fa-copy text-slate-300 group-hover:text-primary transition-colors" title="Copy"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User -->
                    <div class="flex items-start gap-3 p-3 rounded-lg border border-emerald-100 bg-emerald-50/60">
                        <div class="mt-1 w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="fa-solid fa-user text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-700 mb-1">Khách hàng (User)</p>
                            <div class="space-y-1 text-xs text-slate-600">
                                <div class="flex items-center justify-between group cursor-pointer"
                                    onclick="copyToClipboard('user')">
                                    <span>User: <strong class="font-mono text-emerald-700">user</strong></span>
                                    <i class="fa-regular fa-copy text-slate-300 group-hover:text-emerald-500 transition-colors" title="Copy"></i>
                                </div>
                                <div class="flex items-center justify-between group cursor-pointer"
                                    onclick="copyToClipboard('123456')">
                                    <span>Pass: <strong class="font-mono text-emerald-700">123456</strong></span>
                                    <i class="fa-regular fa-copy text-slate-300 group-hover:text-emerald-500 transition-colors" title="Copy"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social links -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-share-nodes text-primary text-xs"></i>
                        <span>Kết nối &amp; liên hệ</span>
                    </h3>
                </div>
                <div class="p-4 space-y-2 text-xs text-slate-600">
                    <a href="https://github.com/ludwig68" target="_blank"
                        class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-brands fa-github text-slate-800"></i>
                            <span>GitHub</span>
                        </span>
                        <span class="text-[0.7rem] text-slate-400">/ludwig68</span>
                    </a>

                    <a href="https://www.facebook.com/ludwig68/" target="_blank"
                        class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-brands fa-facebook text-[#1877F2]"></i>
                            <span>Facebook</span>
                        </span>
                        <span class="text-[0.7rem] text-slate-400">/ludwig68</span>
                    </a>

                    <a href="https://www.instagram.com/wtr.g_/" target="_blank"
                        class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-brands fa-instagram"></i>
                            <span>Instagram</span>
                        </span>
                        <span class="text-[0.7rem] text-slate-400">@wtr.g_</span>
                    </a>

                    <a href="https://www.tiktok.com/@wtr.g_" target="_blank"
                        class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2">
                            <i class="fa-brands fa-tiktok"></i>
                            <span>TikTok</span>
                        </span>
                        <span class="text-[0.7rem] text-slate-400">@wtr.g_</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Đã sao chép: ' + text,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                alert('Đã sao chép: ' + text);
            }
        }).catch(err => {
            console.error('Không thể sao chép: ', err);
        });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>