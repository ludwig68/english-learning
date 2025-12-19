<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

// Đã đăng nhập hay chưa
$isLoggedIn = !empty($_SESSION['user_id']);

// Lấy từ khóa tìm kiếm, nếu có
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Chuẩn bị biến
$levels = [];
$vocabResults = [];

// Thống kê cho trang chủ
$totalLevels = (int)$pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$totalVocab  = (int)$pdo->query("SELECT COUNT(*) FROM vocabularies WHERE deleted_at IS NULL")->fetchColumn();
$totalUsers  = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

if ($q !== '') {
    // Tìm Level theo tên / mô tả
    $stmt = $pdo->prepare("
        SELECT *
        FROM levels
        WHERE name LIKE :kw OR description LIKE :kw
        ORDER BY id
    ");
    $stmt->execute([':kw' => '%' . $q . '%']);
    $levels = $stmt->fetchAll();

    // Tìm vocab theo word / meaning / ví dụ
    $stmtV = $pdo->prepare("
        SELECT v.*, l.name AS level_name
        FROM vocabularies v
        JOIN levels l ON v.level_id = l.id
        WHERE v.deleted_at IS NULL
          AND (
              v.word LIKE :kw
              OR v.meaning LIKE :kw
              OR v.example_sentence LIKE :kw
          )
        ORDER BY v.id
        LIMIT 30
    ");
    $stmtV->execute([':kw' => '%' . $q . '%']);
    $vocabResults = $stmtV->fetchAll();
} else {
    // Không search: load tất cả level để hiển thị trang chủ
    $levels = $pdo->query("SELECT * FROM levels ORDER BY id")->fetchAll();
}
?>

<?php if ($q === ''): ?>
    <!-- TRANG CHỦ BÌNH THƯỜNG -->
    <section class="mb-10">
        <!-- Hero -->
        <div class="text-center max-w-2xl mx-auto mb-8">
            <span class="level-chip mb-3 inline-flex items-center gap-1">
                <i class="fa-solid fa-graduation-cap"></i>
                Free English Learning
            </span>
            <h1 class="text-3xl sm:text-4xl font-bold mb-3">
                Xây nền tảng tiếng Anh của bạn với
                <span class="text-[#7AE582]">flashcard</span> &amp; bài luyện tập.
            </h1>
            <p class="text-slate-500 text-sm sm:text-base">
                Học từ vựng theo level, luyện nghe – nhìn – điền từ với giao diện sáng, nhẹ,
                phù hợp cho người mới bắt đầu đến nâng cao.
            </p>

            <div class="flex flex-wrap justify-center gap-3 mt-5">git pull origin main

                <?php if (!$isLoggedIn): ?>
                    <!-- Chưa đăng nhập: nút đăng ký + đăng nhập -->
                    <a href="/auth/register.php"
                       class="px-4 py-2.5 rounded-full bg-[#7AE582] text-slate-900 text-sm font-semibold hover:bg-emerald-300 transition">
                        Bắt đầu học miễn phí
                    </a>
                    <a href="/auth/login.php"
                       class="px-4 py-2.5 rounded-full border border-slate-300 text-slate-700 text-sm hover:border-[#7AE582] hover:text-[#7AE582] transition">
                        Tôi đã có tài khoản
                    </a>
                <?php else: ?>
                    <!-- Đã đăng nhập: chỉ còn nút vào học ngay -->
                    <a href="/user/dashboard.php"
                       class="px-6 py-2.5 rounded-full bg-[#7AE582] text-slate-900 text-sm font-semibold hover:bg-emerald-300 transition inline-flex items-center gap-2">
                        Vào học ngay
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hàng thống kê -->
        <div class="grid gap-3 sm:grid-cols-3 mb-8">
            <div class="card-glass px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Số Level</p>
                    <p class="text-lg font-semibold text-slate-800"><?= $totalLevels ?></p>
                </div>
            </div>
            <div class="card-glass px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-sky-50 flex items-center justify-center text-sky-500">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Từ vựng đang có</p>
                    <p class="text-lg font-semibold text-slate-800"><?= $totalVocab ?></p>
                </div>
            </div>
            <div class="card-glass px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Tài khoản đã đăng ký</p>
                    <p class="text-lg font-semibold text-slate-800"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>

        <!-- Danh sách Level -->
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                        <i class="fa-solid fa-layer-group text-xs"></i>
                    </span>
                    Lộ trình theo Level
                </h2>
                <span class="text-xs text-slate-400">
                    Chọn một Level để bắt đầu học
                </span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($levels as $lv): ?>
                    <?php
                    // URL cho mỗi level: nếu đã login thì vào thẳng learn.php, nếu chưa thì sang login
                    $levelUrl = $isLoggedIn
                        ? '/user/learn.php?level_id=' . (int)$lv['id']
                        : '/auth/login.php';
                    ?>
                    <div class="card-glass p-4 flex flex-col animate__animated animate__fadeInUp">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-base">
                                <?= htmlspecialchars($lv['name']) ?>
                            </h3>
                            <span class="text-[0.7rem] px-2 py-0.5 rounded-full border border-slate-200 text-slate-500">
                                Level #<?= (int)$lv['id'] ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">
                            <?= htmlspecialchars(mb_strimwidth($lv['description'], 0, 110, '...')) ?>
                        </p>
                        <div class="mt-auto flex justify-between items-center">
                            <span class="text-[0.7rem] text-slate-400">
                                Từ vựng &amp; bài tập tương tác
                            </span>
                            <a href="<?= $levelUrl ?>"
                               class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-full bg-[rgba(122,229,130,0.08)] text-[#16a34a] border border-[rgba(122,229,130,0.6)] hover:bg-[rgba(122,229,130,0.16)] transition">
                                Học Level này
                                <i class="fa-solid fa-arrow-right text-[0.6rem]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$levels): ?>
                    <p class="text-slate-500 text-sm">Chưa có Level nào. Hãy thêm Level trong database.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Các section dưới giữ nguyên -->
        <!-- Cách hệ thống hoạt động -->
        <div class="mb-10">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                    <i class="fa-solid fa-bolt text-xs"></i>
                </span>
                Cách sử dụng hệ thống 
            </h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="card-glass p-4">
                    <p class="text-xs font-semibold text-[#16a34a] mb-1">Bước 1</p>
                    <h3 class="text-sm font-semibold mb-1">Chọn Level phù hợp</h3>
                    <p class="text-xs text-slate-500">
                        Bắt đầu từ level cơ bản (Pre, Junior, ...) hoặc level luyện thi (IELTS, TOEIC)
                        để nội dung vừa sức với bạn.
                    </p>
                </div>
                <div class="card-glass p-4">
                    <p class="text-xs font-semibold text-[#16a34a] mb-1">Bước 2</p>
                    <h3 class="text-sm font-semibold mb-1">Học bằng Flashcard</h3>
                    <p class="text-xs text-slate-500">
                        Lật thẻ để ghi nhớ từ vựng, xem hình minh họa, nghe phát âm và đọc câu ví dụ
                        giúp nhớ lâu hơn.
                    </p>
                </div>
                <div class="card-glass p-4">
                    <p class="text-xs font-semibold text-[#16a34a] mb-1">Bước 3</p>
                    <h3 class="text-sm font-semibold mb-1">Luyện Practice</h3>
                    <p class="text-xs text-slate-500">
                        Làm bài trắc nghiệm, nhìn hình đoán từ, nghe audio điền từ. Hệ thống chấm điểm
                        và phản hồi ngay bằng popup.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bạn sẽ học được gì -->
        <div class="mb-4">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                    <i class="fa-solid fa-headphones text-xs"></i>
                </span>
                Bạn sẽ học được gì?
            </h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="card-glass p-4 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-volume-high text-[#16a34a]"></i>
                        <h3 class="text-sm font-semibold">Luyện nghe &amp; phát âm</h3>
                    </div>
                    <p class="text-xs text-slate-500">
                        Các từ vựng có file audio giúp bạn nghe nhiều lần, bắt chước phát âm đúng chuẩn.
                    </p>
                </div>
                <div class="card-glass p-4 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-image text-[#16a34a]"></i>
                        <h3 class="text-sm font-semibold">Nhớ từ qua hình ảnh</h3>
                    </div>
                    <p class="text-xs text-slate-500">
                        Flashcard có hình minh họa giúp bạn liên tưởng tốt hơn, đặc biệt hiệu quả cho người mới.
                    </p>
                </div>
                <div class="card-glass p-4 flex flex-col gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-[#16a34a]"></i>
                        <h3 class="text-sm font-semibold">Từ vựng trong ngữ cảnh</h3>
                    </div>
                    <p class="text-xs text-slate-500">
                        Mỗi từ vựng đi kèm câu ví dụ, giúp bạn hiểu cách dùng trong câu chứ không chỉ học “chay”.
                    </p>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- TRANG KẾT QUẢ TÌM KIẾM -->
    <section class="mb-8">
        <div class="mb-6">
            <p class="text-xs text-slate-500 mb-1">
                Kết quả tìm kiếm cho:
            </p>
            <h1 class="text-xl sm:text-2xl font-semibold">
                “<span class="text-[#16a34a]"><?= htmlspecialchars($q) ?></span>”
            </h1>
        </div>

        <!-- Level phù hợp -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                        <i class="fa-solid fa-layer-group text-xs"></i>
                    </span>
                    Level phù hợp
                </h2>
                <span class="text-xs text-slate-400">
                    <?= count($levels) ?> level tìm được
                </span>
            </div>

            <?php if ($levels): ?>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($levels as $lv): ?>
                        <?php
                        $levelUrl = $isLoggedIn
                            ? '/user/learn.php?level_id=' . (int)$lv['id']
                            : '/auth/login.php';
                        ?>
                        <div class="card-glass p-4 flex flex-col">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-base">
                                    <?= htmlspecialchars($lv['name']) ?>
                                </h3>
                                <span class="text-[0.7rem] px-2 py-0.5 rounded-full border border-slate-200 text-slate-500">
                                    Level #<?= (int)$lv['id'] ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mb-3">
                                <?= htmlspecialchars(mb_strimwidth($lv['description'], 0, 120, '...')) ?>
                            </p>
                            <a href="<?= $levelUrl ?>"
                               class="mt-auto inline-flex items-center gap-2 text-xs px-3 py-1.5 rounded-md bg-[#7AE582] text-slate-900 font-semibold hover:bg-emerald-300 transition">
                                Vào học Level này
                                <i class="fa-solid fa-arrow-right text-[0.6rem]"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-500">
                    Không tìm thấy Level nào khớp với từ khóa.
                </p>
            <?php endif; ?>
        </div>

        <!-- Từ vựng khớp -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                        <i class="fa-solid fa-book text-xs"></i>
                    </span>
                    Từ vựng khớp
                </h2>
                <span class="text-xs text-slate-400">
                    <?= count($vocabResults) ?> từ vựng tìm được
                </span>
            </div>

            <?php if ($vocabResults): ?>
                <div class="space-y-3">
                    <?php foreach ($vocabResults as $v): ?>
                        <div class="card-glass p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-slate-800">
                                        <?= htmlspecialchars($v['word']) ?>
                                    </span>
                                    <span class="text-[0.65rem] px-2 py-0.5 rounded-full bg-emerald-50 text-[#15803d] border border-emerald-200">
                                        <?= htmlspecialchars($v['level_name']) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600">
                                    <span class="font-medium text-slate-700">Nghĩa:</span>
                                    <?= htmlspecialchars($v['meaning']) ?>
                                </p>
                                <?php if (!empty($v['example_sentence'])): ?>
                                    <p class="mt-1 text-[0.7rem] text-slate-500">
                                        <span class="font-medium text-slate-600">Ví dụ:</span>
                                        <?= htmlspecialchars(mb_strimwidth($v['example_sentence'], 0, 90, '...')) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <?php if (!empty($v['audio_path'])): ?>
                                    <button
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-white border border-slate-300 hover:border-[#7AE582] text-slate-700"
                                        onclick="new Audio('/<?= htmlspecialchars($v['audio_path']) ?>').play();">
                                        <i class="fa-solid fa-volume-high"></i> Nghe
                                    </button>
                                <?php endif; ?>
                                <a href="<?= $isLoggedIn ? '/user/flashcard.php?level_id=' . (int)$v['level_id'] : '/auth/login.php' ?>"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-300 text-slate-700 hover:border-[#7AE582] hover:text-[#16a34a]">
                                    <i class="fa-regular fa-clone"></i> Xem flashcard
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-slate-500">
                    Không tìm thấy từ vựng nào khớp với từ khóa.
                </p>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
