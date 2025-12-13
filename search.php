<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';

$isLoggedIn = !empty($_SESSION['user_id']);

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = 'all'; // giữ lại biến cũ để tránh cảnh báo khi render phần kết quả
$levels = [];
$vocabResults = [];

if ($q !== '') {
    // Levels
    $stmt = $pdo->prepare("
        SELECT *
        FROM levels
        WHERE name LIKE :kw1 OR description LIKE :kw2
        ORDER BY id
    ");
    $stmt->execute([
        ':kw1' => '%' . $q . '%',
        ':kw2' => '%' . $q . '%',
    ]);
    $levels = $stmt->fetchAll();

    // Vocab
    $stmt = $pdo->prepare("
        SELECT v.*, l.name AS level_name
        FROM vocabularies v
        JOIN levels l ON v.level_id = l.id
        WHERE v.deleted_at IS NULL
          AND (
              v.word LIKE :kw1
              OR v.meaning LIKE :kw2
              OR v.example_sentence LIKE :kw3
          )
        ORDER BY v.id
        LIMIT 50
    ");
    $stmt->execute([
        ':kw1' => '%' . $q . '%',
        ':kw2' => '%' . $q . '%',
        ':kw3' => '%' . $q . '%',
    ]);
    $vocabResults = $stmt->fetchAll();
}
?>

<div class="flex items-center justify-between mb-5">
    <div>
        <h1 class="text-2xl font-semibold mb-1">Tìm kiếm level hoặc từ vựng</h1>
        <p class="text-xs text-slate-500">
            Nhập từ khóa, chọn loại dữ liệu cần lọc và xem kết quả trong từng khối riêng.
        </p>
    </div>
    <a href="/index.php" class="text-xs text-slate-500 hover:text-[#16a34a]">
        <i class="fa-solid fa-arrow-left mr-1"></i> Quay về trang chủ
    </a>
</div>

<form action="/search.php" method="get"
      class="bg-white border border-slate-200 rounded-2xl px-3 sm:px-4 py-3 flex items-center gap-3 shadow-sm">
    <div class="flex-1 flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
        <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
        <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($q) ?>"
            placeholder="Nhập từ vựng, nghĩa hoặc tên level và nhấn Enter..."
            class="flex-1 bg-transparent border-none outline-none text-sm text-slate-700 placeholder:text-slate-400"
        >
    </div>
</form>

<?php if ($q === ''): ?>
    <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-5 text-sm text-slate-500">
        Nhập từ khóa và chọn loại lọc để xem kết quả level hoặc từ vựng trong cơ sở dữ liệu.
    </div>
<?php else: ?>
    <div class="mt-6 space-y-8">
        <?php if ($type === 'all' || $type === 'levels'): ?>
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                            <i class="fa-solid fa-layer-group text-xs"></i>
                        </span>
                        Level phù hợp
                    </h2>
                    <span class="text-xs text-slate-400">
                        <?= count($levels) ?> level tìm thấy
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
                        Không tìm thấy level nào khớp với từ khóa.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($type === 'all' || $type === 'vocab'): ?>
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-emerald-50 flex items-center justify-center text-[#16a34a]">
                            <i class="fa-solid fa-book text-xs"></i>
                        </span>
                        Từ vựng khớp
                    </h2>
                    <span class="text-xs text-slate-400">
                        <?= count($vocabResults) ?> từ vựng tìm thấy
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
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
