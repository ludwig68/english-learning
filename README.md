# English Learning System

Ứng dụng web học tiếng Anh bằng flashcard và bài tập trắc nghiệm, có phân quyền người dùng và trang quản trị.

## Tính năng chính
- Trang landing với thống kê, danh sách level và CTA đăng ký/đăng nhập (`index.php`).
- Tìm kiếm có lọc theo Level hoặc Từ vựng, trả kết quả theo khối riêng (`search.php`).
- Đăng ký/đăng nhập, bảo vệ route bằng session (`includes/auth.php`, `auth/*.php`).
- Khu vực học viên: dashboard, chọn lộ trình (`user/learn.php`), học flashcard (`user/flashcard.php`), practice trắc nghiệm (`user/practice.php`), lịch sử (`user/history.php`), hồ sơ cá nhân (`user/profile.php`).
- Quản trị: quản lý Level, Từ vựng, Người dùng, Thống kê (`admin/*.php`).
- Lưu tiến trình và lịch sử làm bài qua các bảng `learning_history`, `learning_history_items`, `user_level_progress`.

## Công nghệ
- PHP thuần (PHP 8.x), PDO MySQL.
- MySQL/MariaDB (dump: `database/viakingv_englishlearning.sql`).
- Tailwind CDN, FontAwesome, SweetAlert2, Animate.css, CSS/JS tùy chỉnh (`assets/`).

## Cấu trúc thư mục (chính)
- `config/` – cấu hình DB (`db.php`, ưu tiên `db.local.php` nếu có).
- `includes/` – header/footer, auth helpers, navbar/sidebar.
- `auth/` – đăng nhập, đăng ký, logout.
- `user/` – màn hình học viên (dashboard, learn, flashcard, practice, history, profile).
- `admin/` – màn hình quản trị levels, vocab, users, stats.
- `assets/` – CSS/JS tĩnh; `uploads/` – ảnh/audio upload.
- `.github/workflows/vietnix-ftp-deploy.yml` – CI deploy FTP lên VietNix.

## Cài đặt nhanh (local WAMP/LAMP)
1. Clone mã nguồn vào webroot (ví dụ `C:\wamp64\www\english-learning`).
2. Tạo DB MySQL, import `database/viakingv_englishlearning.sql`.
3. Tạo file `config/db.local.php` để ghi đè thông tin kết nối:
   ```php
   <?php
   $host = '127.0.0.1';
   $db   = 'viakingv_englishlearning';
   $user = 'root';
   $pass = '';
   $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
   $pdo = new PDO($dsn, $user, $pass, [
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       PDO::ATTR_EMULATE_PREPARES => false,
   ]);
   ```
4. Đảm bảo thư mục `uploads/` có quyền ghi để lưu avatar/audio/hình ảnh.
5. Trỏ trình duyệt tới `http://localhost/english-learning`.

## Tài khoản mẫu
- Trong dump có sẵn user `admin` (role `admin`) và `user` (role `user`) với mật khẩu băm. Bạn có thể:
  - Đăng ký tài khoản mới tại `/auth/register.php`, hoặc
  - Cập nhật mật khẩu admin trong DB bằng `UPDATE users SET password = PASSWORD_HASH` (bcrypt) nếu cần đăng nhập ngay, rồi gán `role='admin'`.

## Sử dụng nhanh
- Duyệt levels hoặc tìm kiếm từ thanh search trên navbar (chọn “Tất cả / Level / Từ vựng”).
- Sau khi đăng nhập:
  - Vào Dashboard (`/user/dashboard.php`) để xem thống kê và lối tắt.
  - Chọn lộ trình ở `/user/learn.php`, sau đó học Flashcard hoặc Practice.
  - Xem lịch sử học ở `/user/history.php`, chỉnh sửa hồ sơ ở `/user/profile.php`.
- Quản trị: đăng nhập bằng tài khoản admin và truy cập `/admin/index.php` để quản lý dữ liệu.

## Triển khai (CI/CD)
- Workflow `.github/workflows/vietnix-ftp-deploy.yml` deploy qua FTP với action `SamKirkland/FTP-Deploy-Action@v4.3.4`.
- Cần thiết lập secrets: `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `FTP_PATH`.

## Ghi chú
- Dùng chuẩn UTF-8 (utf8mb4) cho DB.
- Nếu cấu hình host khác VietNix, chỉ cần chỉnh `db.local.php`; file `config/db.php` giữ cấu hình mặc định cho môi trường production. 
