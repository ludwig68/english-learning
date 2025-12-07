<?php
// user/profile.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Lấy ID user từ session
$userId = (int)($_SESSION['user_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Không tìm thấy tài khoản hoặc tài khoản đã bị ẩn.";
    exit;
}

// Biến cho form
$full_name = $user['full_name'] ?? '';
$email     = $user['email']     ?? '';
$phone     = $user['phone']     ?? '';
$avatar    = $user['avatar']    ?? '';

$profileErrors   = [];
$passwordErrors  = [];
$profileSuccess  = '';
$passwordSuccess = '';

// Thư mục lưu avatar
$avatarDir = __DIR__ . '/../uploads/avatars/';
if (!is_dir($avatarDir)) {
    @mkdir($avatarDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    // ================== CẬP NHẬT THÔNG TIN CÁ NHÂN ==================
    if ($formType === 'profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email']     ?? '');
        $phone     = trim($_POST['phone']     ?? '');

        // Validate cơ bản
        if ($full_name === '') {
            $profileErrors[] = 'Vui lòng nhập Họ tên.';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $profileErrors[] = 'Email không hợp lệ.';
        }

        $currentAvatar = $avatar;

        // Upload avatar từ file
        if (!empty($_FILES['avatar_file']['name'])) {
            $file = $_FILES['avatar_file'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $allow)) {
                    $profileErrors[] = 'Avatar chỉ hỗ trợ: ' . implode(', ', $allow);
                } else {
                    $newName = 'avatar_' . $userId . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                    if (move_uploaded_file($file['tmp_name'], $avatarDir . $newName)) {
                        $currentAvatar = 'uploads/avatars/' . $newName;
                    } else {
                        $profileErrors[] = 'Không lưu được file avatar.';
                    }
                }
            } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                $profileErrors[] = 'Lỗi upload avatar.';
            }
        }

        // Nếu tick xóa avatar
        if (!empty($_POST['remove_avatar'])) {
            $currentAvatar = '';
        }

        if (!$profileErrors) {
            $stmt = $pdo->prepare("
                UPDATE users
                SET full_name = ?, email = ?, phone = ?, avatar = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $full_name,
                $email,
                $phone,
                $currentAvatar,
                $userId
            ]);

            $profileSuccess = 'Đã cập nhật thông tin cá nhân.';
            $avatar = $currentAvatar;

            // Reload user để dùng dữ liệu mới
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    // ================== ĐỔI MẬT KHẨU ==================
    if ($formType === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $passwordErrors[] = 'Vui lòng nhập đầy đủ các trường mật khẩu.';
        }

        // Kiểm tra mật khẩu hiện tại (cột password trong DB)
        if (!password_verify($currentPassword, $user['password'])) {
            $passwordErrors[] = 'Mật khẩu hiện tại không đúng.';
        }

        if (strlen($newPassword) < 6) {
            $passwordErrors[] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
        }

        if ($newPassword !== $confirmPassword) {
            $passwordErrors[] = 'Mật khẩu mới và Nhập lại mật khẩu không khớp.';
        }

        if (!$passwordErrors) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$newHash, $userId]);

            $passwordSuccess = 'Đã đổi mật khẩu thành công.';

            // Reload user để dùng hash mới
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}

// ------- HTML / UI -------
$pageTitle = 'Hồ sơ cá nhân';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto py-6 px-3 md:px-0">
    <div class="grid md:grid-cols-[260px,1fr] gap-4 lg:gap-6 items-start">
        <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>

        <div class="space-y-4">
            <!-- Tiêu đề -->
            <div class="mb-1">
                <h1 class="text-lg font-semibold text-slate-900">Hồ sơ cá nhân</h1>
                <p class="text-[0.8rem] text-slate-500 mt-0.5">
                    Cập nhật thông tin cá nhân và thay đổi mật khẩu tài khoản học viên.
                </p>
            </div>

            <!-- Thông báo -->
            <?php if ($profileSuccess): ?>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                    <i class="fa-solid fa-circle-check mr-1"></i>
                    <?= htmlspecialchars($profileSuccess) ?>
                </div>
            <?php endif; ?>

            <?php if ($passwordSuccess): ?>
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                    <i class="fa-solid fa-circle-check mr-1"></i>
                    <?= htmlspecialchars($passwordSuccess) ?>
                </div>
            <?php endif; ?>

            <!-- FORM THÔNG TIN CÁ NHÂN -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-[#7AE582]/20 flex items-center justify-center">
                        <i class="fa-solid fa-id-card text-[0.75rem] text-[#16a34a]"></i>
                    </span>
                    Thông tin cá nhân
                </h2>

                <?php if ($profileErrors): ?>
                    <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[0.75rem] text-red-700">
                        <ul class="list-disc pl-4">
                            <?php foreach ($profileErrors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="form_type" value="profile">

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Avatar -->
                        <div class="sm:w-40 flex flex-col items-center text-center gap-2">
                            <?php
                            $avatarSrc = '';
                            if (!empty($avatar)) {
                                $avatarSrc = $avatar;
                                if (!preg_match('~^https?://~', $avatarSrc)) {
                                    $avatarSrc = '/' . ltrim($avatarSrc, '/');
                                }
                            }
                            ?>

                            <?php if ($avatarSrc): ?>
                                <img id="avatarPreview"
                                     src="<?= htmlspecialchars($avatarSrc) ?>"
                                     alt="Avatar"
                                     class="w-20 h-20 rounded-full object-cover border border-slate-200">
                            <?php else: ?>
                                <img id="avatarPreview"
                                     src="/assets/images/avatar-default.png"
                                     alt="Avatar"
                                     class="w-20 h-20 rounded-full object-cover border border-slate-200">
                            <?php endif; ?>

                            <label
                                for="avatar_file"
                                class="mt-1 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 text-[0.75rem] text-slate-700 cursor-pointer hover:border-[#7AE582] hover:text-[#16a34a]">
                                <i class="fa-solid fa-camera text-[0.7rem]"></i>
                                <span>Chọn ảnh...</span>
                            </label>
                            <input type="file"
                                   id="avatar_file"
                                   name="avatar_file"
                                   class="hidden"
                                   accept="image/*">

                            <?php if ($avatarSrc && $avatar !== ''): ?>
                                <label class="mt-1 flex items-center gap-1 text-[0.7rem] text-slate-500 cursor-pointer">
                                    <input type="checkbox" name="remove_avatar"
                                           class="w-3 h-3 rounded border-slate-300 text-[#7AE582]">
                                    <span>Xóa avatar, dùng mặc định</span>
                                </label>
                            <?php endif; ?>

                            <p class="mt-1 text-[0.7rem] text-slate-400">
                                JPG, PNG, GIF, WEBP. Kích thước nhỏ gọn để tải nhanh.
                            </p>
                        </div>

                        <!-- Thông tin -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Họ và tên
                                </label>
                                <input type="text" name="full_name"
                                       value="<?= htmlspecialchars($full_name) ?>"
                                       class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                            </div>

                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Email
                                </label>
                                <input type="email" name="email"
                                       value="<?= htmlspecialchars($email) ?>"
                                       class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                            </div>

                            <div>
                                <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                    Số điện thoại
                                </label>
                                <input type="text" name="phone"
                                       value="<?= htmlspecialchars($phone) ?>"
                                       class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                            </div>

                            <div class="sm:col-span-2">
                                <p class="text-[0.7rem] text-slate-400 mt-1">
                                    Thông tin này chỉ dùng trong hệ thống, không hiển thị công khai.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-[#7AE582] text-slate-900 text-[0.8rem] font-semibold hover:bg-emerald-300">
                            <i class="fa-solid fa-floppy-disk text-[0.75rem]"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            <!-- ĐỔI MẬT KHẨU -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                        <i class="fa-solid fa-lock text-[0.75rem] text-slate-600"></i>
                    </span>
                    Đổi mật khẩu
                </h2>

                <?php if ($passwordErrors): ?>
                    <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[0.75rem] text-red-700">
                        <ul class="list-disc pl-4">
                            <?php foreach ($passwordErrors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" class="mt-3 max-w-3xl space-y-4">
                    <input type="hidden" name="form_type" value="password">

                    <!-- Lưới 2 cột, ô hiện tại chiếm full hàng -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                Mật khẩu hiện tại
                            </label>
                            <input type="password" name="current_password"
                                   class="w-full h-10 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                        </div>

                        <div>
                            <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                Mật khẩu mới
                            </label>
                            <input type="password" name="new_password"
                                   class="w-full h-10 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                        </div>

                        <div>
                            <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                Nhập lại mật khẩu mới
                            </label>
                            <input type="password" name="confirm_password"
                                   class="w-full h-10 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#7AE582]">
                        </div>
                    </div>

                    <p class="text-[0.7rem] text-slate-400">
                        Mật khẩu nên có ít nhất 6 ký tự, gồm chữ hoa, chữ thường, số hoặc ký tự đặc biệt.
                    </p>

                    <div class="flex justify-end pt-1">
                        <button
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-900 text-white text-[0.8rem] font-semibold hover:bg-slate-800">
                            <i class="fa-solid fa-key text-[0.75rem]"></i>
                            Đổi mật khẩu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- LIVE PREVIEW AVATAR -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput  = document.getElementById('avatar_file');
    const previewImg = document.getElementById('avatarPreview');

    if (!fileInput || !previewImg) return;

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh hợp lệ.');
            fileInput.value = '';
            return;
        }

        const url = URL.createObjectURL(file);
        previewImg.src = url;
    });
});
</script>

<?php
$hideFooter = true;
require_once __DIR__ . '/../includes/footer.php';
