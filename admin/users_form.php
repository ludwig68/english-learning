<?php
// admin/user_form.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$userId = (int)$_SESSION['user_id'];

// ===== Stats cho sidebar =====
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
$totalUsers  = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn();

// Thư mục lưu avatar
$avatarDir = __DIR__ . '/../uploads/avatars/';
if (!is_dir($avatarDir)) {
    @mkdir($avatarDir, 0777, true);
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;
$errors = [];

// Giá trị mặc định
$username   = '';
$full_name  = '';
$email      = '';
$phone      = '';
$avatar     = '';
$role       = 'user';
$status     = 'active';
$created_at = null;

// Nếu sửa -> load dữ liệu
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('Không tìm thấy tài khoản.');
    }

    $username   = $user['username'];
    $full_name  = $user['full_name'];
    $email      = $user['email'];
    $phone      = $user['phone'];
    $avatar     = $user['avatar'];
    $role       = $user['role'];
    $status     = $user['status'];
    $created_at = $user['created_at'];
}

// Xử lý submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $role      = $_POST['role'] ?? 'user';
    $status    = $_POST['status'] ?? 'active';

    // avatar hiện tại
    $avatar = $_POST['current_avatar'] ?? '';

    // URL avatar (nếu có)
    $avatarUrl = trim($_POST['avatar_url'] ?? '');
    if ($avatarUrl !== '') {
        $avatar = $avatarUrl;
    }

    // Mật khẩu
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // ==== Validate cơ bản ====
    if ($username === '') {
        $errors[] = 'Vui lòng nhập Username.';
    }

    if (!$isEdit && $password === '') {
        $errors[] = 'Vui lòng nhập mật khẩu cho tài khoản mới.';
    }

    if ($password !== '' && $password !== $passwordConfirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }

    // Check trùng username
    if ($username !== '') {
        if ($isEdit) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1");
            $stmt->execute([$username, $id]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
        }
        if ($stmt->fetch()) {
            $errors[] = 'Username đã tồn tại, vui lòng chọn tên khác.';
        }
    }

    // ==== Upload avatar (nếu có file) ====
    if (!empty($_FILES['avatar_file']['name'])) {
        $file = $_FILES['avatar_file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $allow)) {
                $errors[] = 'Ảnh đại diện chỉ hỗ trợ: ' . implode(', ', $allow);
            } else {
                $newName = 'avatar_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $avatarDir . $newName)) {
                    // Lưu đường dẫn tương đối
                    $avatar = 'uploads/avatars/' . $newName;
                } else {
                    $errors[] = 'Không lưu được file ảnh đại diện.';
                }
            }
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Lỗi upload ảnh đại diện.';
        }
    }

    // Nếu không có lỗi -> lưu DB
    if (!$errors) {
        if ($isEdit) {
            // Update
            if ($password !== '') {
                // Đổi mật khẩu
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET username = ?, full_name = ?, email = ?, phone = ?, avatar = ?, role = ?, status = ?
                      , password = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $username,
                    $full_name,
                    $email,
                    $phone,
                    $avatar,
                    $role,
                    $status,
                    $hash,
                    $id
                ]);
            } else {
                // Không đổi mật khẩu
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET username = ?, full_name = ?, email = ?, phone = ?, avatar = ?, role = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $username,
                    $full_name,
                    $email,
                    $phone,
                    $avatar,
                    $role,
                    $status,
                    $id
                ]);
            }

            header('Location: /admin/users.php?success=Cập nhật tài khoản thành công');
            exit;
        } else {
            // Insert
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("
                INSERT INTO users (username, full_name, email, phone, avatar, password, role, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $username,
                $full_name,
                $email,
                $phone,
                $avatar,
                $hash,
                $role,
                $status
            ]);

            header('Location: /admin/users.php?success=Thêm tài khoản mới thành công');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? 'Sửa' : 'Thêm' ?> người dùng | English Learning</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7AE582',
                        'primary-dark': '#16a34a'
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.7);
            border-radius: 999px;
        }
    </style>
</head>

<body class="bg-slate-50">
    <div class="h-screen flex overflow-hidden">

        <!-- SIDEBAR (giống các trang admin khác) -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0">
            <!-- Logo -->
            <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100">
                <div
                    class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-primary-dark to-primary flex items-center justify-center text-white">
                    <i class="fa-solid fa-chalkboard-user text-sm"></i>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-[0.7rem] text-slate-400">English Learning System</span>
                </div>
            </div>

            <!-- Admin info -->
            <div class="px-5 pt-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-primary/80 flex items-center justify-center text-sm font-semibold text-emerald-950">
                        A
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-800">
                            Administrator
                        </span>
                        <span class="text-[0.7rem] text-slate-400">
                            Quản trị viên
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-center text-[0.7rem]">
                    <div class="rounded-xl bg-primary/20 text-emerald-900 py-2">
                        <p class="font-semibold mb-0.5"><?= $totalLevels ?></p>
                        <p class="text-[0.65rem]">Levels</p>
                    </div>
                    <div class="rounded-xl bg-primary/20 text-emerald-900 py-2">
                        <p class="font-semibold mb-0.5"><?= $totalUsers ?></p>
                        <p class="text-[0.65rem]">Người dùng</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 text-sm">
                <p class="px-2 mb-2 text-[0.7rem] uppercase tracking-wide text-slate-400">
                    Điều hướng
                </p>

                <a href="/admin/index.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-gauge text-xs"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/levels.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                    <span>Quản lý Levels</span>
                </a>

                <a href="/admin/vocab_list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-book-open text-xs"></i>
                    <span>Quản lý Từ vựng</span>
                </a>

                <a href="/admin/users.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-primary text-emerald-950 font-semibold mb-1">
                    <i class="fa-solid fa-users text-xs"></i>
                    <span>Người dùng</span>
                </a>

                <a href="/admin/stats.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-2xl text-slate-600 hover:bg-slate-100 mb-1">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                    <span>Thống kê</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="px-4 py-4 border-t border-slate-100">
                <a href="/index.php"
                    class="flex items-center justify-center gap-2 text-xs text-slate-500 hover:text-primary-dark mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Về trang học viên
                </a>
                <a href="/auth/logout.php"
                    class="w-full flex items-center justify-center gap-2 text-xs font-semibold px-3 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-red-50 hover:text-red-500">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col ml-64">
            <!-- Top bar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">
                        <?= $isEdit ? 'Sửa người dùng' : 'Thêm người dùng' ?>
                    </h1>
                    <p class="text-[0.75rem] text-slate-400 mt-0.5">
                        Điền thông tin tài khoản, vai trò và trạng thái hoạt động.
                    </p>
                </div>
                <div class="hidden sm:flex items-center gap-3 text-xs">
                    <a href="/admin/users.php"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200">
                        <i class="fa-solid fa-arrow-left text-[0.75rem]"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </header>

            <!-- Content -->
            <section class="flex-1 p-6 overflow-x-hidden overflow-y-auto scrollbar-thin">
                <div class="max-w-4xl mx-auto space-y-4">

                    <!-- Errors -->
                    <?php if ($errors): ?>
                        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">
                            <p class="font-semibold mb-1">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                Có lỗi xảy ra:
                            </p>
                            <ul class="list-disc pl-5 space-y-0.5">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Card form -->
                    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-6">
                        <form method="post" enctype="multipart/form-data" class="space-y-6">

                            <!-- Hàng 1: username + fullname -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Username <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="username"
                                        value="<?= htmlspecialchars($username) ?>"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="tên đăng nhập...">
                                </div>
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Họ tên
                                    </label>
                                    <input type="text" name="full_name"
                                        value="<?= htmlspecialchars($full_name) ?>"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="Họ tên người dùng...">
                                </div>
                            </div>

                            <!-- Hàng 2: email + phone -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Email
                                    </label>
                                    <input type="email" name="email"
                                        value="<?= htmlspecialchars($email) ?>"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="example@gmail.com">
                                </div>
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Số điện thoại
                                    </label>
                                    <input type="text" name="phone"
                                        value="<?= htmlspecialchars($phone) ?>"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="VD: 0903 xxx xxx">
                                </div>
                            </div>

                            <!-- Hàng 3: Avatar -->
                            <div class="grid gap-4 md:grid-cols-[auto,1fr]">
                                <!-- Preview -->
                                <div class="flex flex-col items-center gap-2">
                                    <p class="text-[0.75rem] font-medium text-slate-700">Ảnh đại diện</p>

                                    <?php
                                    $hasAvatar = !empty($avatar);
                                    $avatarSrc = '';
                                    if ($hasAvatar) {
                                        $avatarSrc = $avatar;
                                        if (!preg_match('~^https?://~', $avatarSrc)) {
                                            $avatarSrc = '/' . ltrim($avatarSrc, '/');
                                        }
                                    }
                                    ?>
                                    <div class="relative">
                                        <!-- Ảnh preview (ẩn nếu chưa có gì) -->
                                        <img id="avatarPreview"
                                            src="<?= $hasAvatar ? htmlspecialchars($avatarSrc) : '' ?>"
                                            class="w-16 h-16 rounded-full object-cover border border-slate-200 <?= $hasAvatar ? '' : 'hidden' ?>">

                                        <!-- Placeholder khi chưa có avatar -->
                                        <div id="avatarPlaceholder"
                                            class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xs <?= $hasAvatar ? 'hidden' : '' ?>">
                                            No avatar
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload + URL -->
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                            Upload file ảnh
                                        </label>
                                        <input type="file" name="avatar_file" id="avatar_file"
                                            class="block w-full text-[0.75rem] text-slate-600">
                                        <p class="text-[0.7rem] text-slate-400 mt-1">
                                            Hỗ trợ: jpg, jpeg, png, gif, webp. Nếu chọn file, hệ thống sẽ ưu tiên file này.
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                            Hoặc URL ảnh
                                        </label>
                                        <input type="text" name="avatar_url" id="avatar_url"
                                            value="<?= preg_match('~^https?://~', (string)$avatar) ? htmlspecialchars($avatar) : '' ?>"
                                            class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                            placeholder="https://example.com/avatar.png">
                                        <p class="text-[0.7rem] text-slate-400 mt-1">
                                            Nếu nhập URL ảnh (và không chọn file), hệ thống sẽ dùng URL này làm avatar.
                                        </p>
                                    </div>

                                    <input type="hidden" name="current_avatar"
                                        value="<?= htmlspecialchars($avatar) ?>">
                                </div>
                            </div>


                            <!-- Hàng 4: Role + Status -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Vai trò
                                    </label>
                                    <select name="role"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3">
                                        <option value="user" <?= $role === 'user'  ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <p class="text-[0.7rem] text-slate-400 mt-1">
                                        <b>Admin</b> có quyền truy cập trang quản trị, quản lý nội dung và người dùng.
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Trạng thái
                                    </label>
                                    <select name="status"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3">
                                        <option value="active" <?= $status === 'active'  ? 'selected' : '' ?>>Đang hoạt động</option>
                                        <option value="blocked" <?= $status === 'blocked' ? 'selected' : '' ?>>Bị khóa</option>
                                    </select>
                                    <p class="text-[0.7rem] text-slate-400 mt-1">
                                        Tài khoản <b>bị khóa</b> sẽ không đăng nhập được nhưng vẫn giữ dữ liệu.
                                    </p>
                                </div>
                            </div>

                            <!-- Hàng 5: Password -->
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Mật khẩu <?= $isEdit ? '(để trống nếu không đổi)' : '<span class="text-red-500">*</span>' ?>
                                    </label>
                                    <input type="password" name="password"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="<?= $isEdit ? 'Nhập mật khẩu mới (nếu muốn đổi)...' : 'Ít nhất 6 ký tự...' ?>">
                                </div>
                                <div>
                                    <label class="block text-[0.75rem] font-medium text-slate-700 mb-1">
                                        Xác nhận mật khẩu
                                    </label>
                                    <input type="password" name="password_confirm"
                                        class="w-full h-9 rounded-2xl border border-slate-200 bg-slate-50 text-[0.8rem] px-3 focus:bg-white"
                                        placeholder="Nhập lại mật khẩu...">
                                </div>
                            </div>

                            <?php if ($isEdit && $created_at): ?>
                                <p class="text-[0.7rem] text-slate-400">
                                    <i class="fa-solid fa-clock text-[0.7rem] mr-1"></i>
                                    Tạo lúc: <?= date('d/m/Y H:i', strtotime($created_at)) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Buttons -->
                            <div class="flex items-center justify-end gap-2 pt-2">
                                <a href="/admin/users.php"
                                    class="inline-flex items-center gap-1 px-3 py-2 rounded-2xl bg-slate-100 text-slate-600 text-[0.8rem] hover:bg-slate-200">
                                    <i class="fa-solid fa-arrow-left text-[0.75rem]"></i>
                                    Hủy
                                </a>
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-4 py-2 rounded-2xl bg-primary text-emerald-950 text-[0.8rem] font-semibold hover:bg-primary-dark hover:text-emerald-50">
                                    <i class="fa-solid fa-floppy-disk text-[0.75rem]"></i>
                                    Lưu
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('avatar_file');
            const urlInput = document.getElementById('avatar_url');
            const previewImg = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');

            function showPreview(src) {
                if (!previewImg || !placeholder) return;
                if (src) {
                    previewImg.src = src;
                    previewImg.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                } else {
                    previewImg.src = '';
                    previewImg.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            }

            // Chọn file từ máy
            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (!file) return;
                    const url = URL.createObjectURL(file);
                    showPreview(url);

                    // Nếu chọn file thì xóa URL để tránh nhầm
                    if (urlInput) urlInput.value = '';
                });
            }

            // Nhập URL avatar
            if (urlInput) {
                urlInput.addEventListener('change', (e) => {
                    const url = e.target.value.trim();
                    if (url) {
                        showPreview(url);
                        // Nếu dùng URL thì bỏ file cũ
                        if (fileInput) fileInput.value = '';
                    } else {
                        // Xóa URL và không có file -> quay về placeholder
                        if (!fileInput || !fileInput.files.length) {
                            showPreview('');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>