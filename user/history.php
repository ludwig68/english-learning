<?php
// user/history.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Lấy ID user từ session
$userId = (int)($_SESSION['user_id'] ?? 0);

// Lấy thông tin user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Không tìm thấy tài khoản hoặc tài khoản đã bị ẩn.";
    exit;
}

// ------------ LẤY DỮ LIỆU LỊCH SỬ ------------
$logs = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            h.*,
            l.name AS level_name
        FROM learning_history h
        LEFT JOIN levels l ON h.level_id = l.id
        WHERE h.user_id = ?
        ORDER BY h.created_at DESC, h.id DESC
        LIMIT 200
    ");
    $stmt->execute([$userId]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Nếu chưa tạo bảng learning_history thì tránh chết site
    if ($e->getCode() !== '42S02') { // 42S02: table not found
        throw $e;
    }
    $logs = [];
}

// Lấy chi tiết câu hỏi cho tất cả history_id đã load
$detailsByHistory = [];
if ($logs) {
    $ids = array_column($logs, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM learning_history_items
            WHERE history_id IN ($placeholders)
            ORDER BY id
        ");
        $stmt->execute($ids);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $hid = (int)$item['history_id'];
            if (!isset($detailsByHistory[$hid])) {
                $detailsByHistory[$hid] = [];
            }
            $detailsByHistory[$hid][] = $item;
        }
    } catch (PDOException $e) {
        // Nếu chưa tạo bảng chi tiết thì bỏ qua phần chi tiết
        if ($e->getCode() !== '42S02') {
            throw $e;
        }
        $detailsByHistory = [];
    }
}

// --------- Tổng quan thống kê ----------
$totalSessions   = count($logs);
$totalFlashcard  = 0;
$totalPractice   = 0;
$totalQuiz       = 0;
$sumAccuracy     = 0;
$accuracyCount   = 0;
$lastActivityStr = '';

foreach ($logs as $log) {
    $mode = $log['mode'] ?? 'flashcard';
    if ($mode === 'flashcard') $totalFlashcard++;
    elseif ($mode === 'practice') $totalPractice++;
    elseif ($mode === 'quiz') $totalQuiz++;

    $totalQ = (int)($log['total_questions'] ?? 0);
    $correct = (int)($log['correct_count'] ?? 0);
    if ($totalQ > 0) {
        $acc = $correct * 100 / $totalQ;
        $sumAccuracy += $acc;
        $accuracyCount++;
    }

    if ($lastActivityStr === '' && !empty($log['created_at'])) {
        $lastActivityStr = date('d/m/Y H:i', strtotime($log['created_at']));
    }
}
$avgAccuracy = $accuracyCount > 0 ? round($sumAccuracy / $accuracyCount) : null;

// Gom nhóm theo ngày cho đẹp
$grouped = [];
foreach ($logs as $log) {
    $dateKey = !empty($log['created_at'])
        ? date('Y-m-d', strtotime($log['created_at']))
        : 'unknown';
    $grouped[$dateKey][] = $log;
}

// ------- HTML / UI -------
$pageTitle = 'Lịch sử học tập';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto py-6 px-3 md:px-0">
    <div class="grid md:grid-cols-[260px,1fr] gap-4 lg:gap-6 items-start">
        <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>

        <div class="space-y-4">
            <!-- Tiêu đề -->
            <div class="mb-1">
                <h1 class="text-lg font-semibold text-slate-900">Lịch sử học tập</h1>
                <p class="text-[0.8rem] text-slate-500 mt-0.5">
                    Theo dõi quá trình học Flashcard, làm bài luyện tập và quiz của bạn.
                </p>
            </div>

            <?php if (empty($logs)): ?>
                <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-6 text-center">
                    <p class="text-sm font-medium text-slate-700 mb-1">
                        Chưa có lịch sử học tập
                    </p>
                    <p class="text-[0.8rem] text-slate-500 mb-3">
                        Hãy bắt đầu học ở mục <b>Lộ trình Level</b> hoặc <b>Flashcard / Practice</b> để xem lịch sử tại đây.
                    </p>
                </div>
            <?php else: ?>

                <!-- Cards thống kê -->
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs text-slate-500">Tổng phiên học</p>
                            <span class="w-7 h-7 rounded-full bg-[#7AE582]/20 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left text-[0.75rem] text-[#16a34a]"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalSessions ?></p>
                        <p class="text-[0.7rem] text-slate-400">Trong tối đa 200 phiên gần nhất</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs text-slate-500">Flashcard</p>
                            <span class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center">
                                <i class="fa-regular fa-clone text-[0.75rem] text-sky-600"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1"><?= $totalFlashcard ?></p>
                        <p class="text-[0.7rem] text-slate-400">Phiên ôn từ dạng flashcard</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs text-slate-500">Practice & Quiz</p>
                            <span class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center">
                                <i class="fa-solid fa-clipboard-question text-[0.75rem] text-amber-700"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1">
                            <?= $totalPractice + $totalQuiz ?>
                        </p>
                        <p class="text-[0.7rem] text-slate-400">Làm bài luyện tập / kiểm tra</p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs text-slate-500">Hiệu quả trung bình</p>
                            <span class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-[0.75rem] text-emerald-700"></i>
                            </span>
                        </div>
                        <p class="text-2xl font-semibold text-slate-900 mb-1">
                            <?= $avgAccuracy !== null ? $avgAccuracy . '%' : '--' ?>
                        </p>
                        <p class="text-[0.7rem] text-slate-400">
                            Tỉ lệ trả lời đúng trung bình
                        </p>
                    </div>
                </div>

                <!-- Lịch sử chi tiết + câu đúng/sai -->
                <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-[#7AE582]/20 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left text-[0.75rem] text-[#16a34a]"></i>
                            </span>
                            Chi tiết lịch sử học
                        </h2>
                        <?php if ($lastActivityStr): ?>
                            <span class="text-[0.7rem] text-slate-400">
                                Lần hoạt động gần nhất: <?= $lastActivityStr ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-4">
                        <?php
                        krsort($grouped); // sắp xếp ngày mới -> cũ
                        foreach ($grouped as $dateKey => $items):
                            $dateLabel = $dateKey !== 'unknown'
                                ? date('d/m/Y', strtotime($dateKey))
                                : 'Không rõ ngày';
                        ?>
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[0.75rem] font-semibold text-slate-700">
                                        <?= $dateLabel ?>
                                    </span>
                                    <span class="text-[0.7rem] text-slate-400">
                                        (<?= count($items) ?> phiên)
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <?php foreach ($items as $log):
                                        $mode = $log['mode'] ?? 'flashcard';
                                        $modeLabel = $mode === 'practice'
                                            ? 'Practice'
                                            : ($mode === 'quiz' ? 'Quiz' : 'Flashcard');
                                        $modeClass = $mode === 'practice'
                                            ? 'bg-sky-100 text-sky-700'
                                            : ($mode === 'quiz'
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-[#7AE582] text-emerald-950');

                                        $createdAt = !empty($log['created_at'])
                                            ? date('H:i', strtotime($log['created_at']))
                                            : '';
                                        $levelName = $log['level_name'] ?? null;
                                        $vocabCount = (int)($log['vocab_count'] ?? 0);
                                        $totalQ = (int)($log['total_questions'] ?? 0);
                                        $correct = (int)($log['correct_count'] ?? 0);

                                        $accStr = '';
                                        if ($totalQ > 0) {
                                            $acc = round($correct * 100 / $totalQ);
                                            $accStr = $acc . '% đúng';
                                        }

                                        $note = $log['note'] ?? '';
                                        $hid  = (int)$log['id'];
                                        $details = $detailsByHistory[$hid] ?? [];
                                    ?>
                                        <div class="rounded-2xl bg-slate-50 px-3 sm:px-4 py-3">
                                            <div class="flex flex-wrap items-start gap-3">
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        <span class="px-2 py-0.5 rounded-full text-[0.65rem] <?= $modeClass ?>">
                                                            <?= $modeLabel ?>
                                                        </span>

                                                        <?php if ($levelName): ?>
                                                            <span class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-[0.65rem] text-slate-500">
                                                                Level: <?= htmlspecialchars($levelName) ?>
                                                            </span>
                                                        <?php endif; ?>

                                                        <?php if ($createdAt): ?>
                                                            <span class="text-[0.65rem] text-slate-400">
                                                                <i class="fa-solid fa-clock text-[0.65rem] mr-1"></i><?= $createdAt ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="text-[0.75rem] text-slate-600 space-y-0.5">
                                                        <?php if ($vocabCount > 0): ?>
                                                            <p>
                                                                <i class="fa-solid fa-book-open text-[0.7rem] mr-1 text-slate-400"></i>
                                                                Ôn <?= $vocabCount ?> từ vựng
                                                            </p>
                                                        <?php endif; ?>

                                                        <?php if ($totalQ > 0): ?>
                                                            <p>
                                                                <i class="fa-solid fa-list-check text-[0.7rem] mr-1 text-slate-400"></i>
                                                                Đúng <?= $correct ?>/<?= $totalQ ?>
                                                                <?php if ($accStr): ?>
                                                                    (<span class="font-semibold"><?= $accStr ?></span>)
                                                                <?php endif; ?>
                                                            </p>
                                                        <?php endif; ?>

                                                        <?php if ($note): ?>
                                                            <p class="text-[0.7rem] text-slate-500">
                                                                <i class="fa-solid fa-note-sticky text-[0.65rem] mr-1"></i>
                                                                <?= htmlspecialchars($note) ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Nút xem chi tiết -->
                                                <?php if ($details): ?>
                                                    <button
                                                        type="button"
                                                        class="text-[0.7rem] px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-600 hover:border-[#7AE582] hover:text-[#16a34a] h-fit"
                                                        data-toggle-detail="<?= $hid ?>">
                                                        <i class="fa-solid fa-chevron-down text-[0.6rem] mr-1"></i>
                                                        Xem chi tiết
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Danh sách câu hỏi: ẩn/hiện -->
                                            <?php if ($details): ?>
                                                <div class="mt-3 hidden" data-detail-panel="<?= $hid ?>">
                                                    <div class="rounded-2xl bg-white border border-slate-200 p-3 max-h-64 overflow-y-auto">
                                                        <p class="text-[0.7rem] text-slate-500 mb-2">
                                                            Danh sách câu hỏi trong phiên này:
                                                        </p>
                                                        <ul class="space-y-1.5 text-[0.75rem]">
                                                            <?php foreach ($details as $idx => $item):
                                                                $isCorrect = (int)$item['is_correct'] === 1;
                                                                $qText  = $item['question_text'] ?? '';
                                                                $uAns   = $item['user_answer'] ?? '';
                                                                $cAns   = $item['correct_answer'] ?? '';
                                                            ?>
                                                                <li class="flex items-start gap-2">
                                                                    <span class="mt-[3px] w-4 flex justify-center">
                                                                        <?php if ($isCorrect): ?>
                                                                            <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
                                                                        <?php else: ?>
                                                                            <i class="fa-solid fa-circle-xmark text-red-500 text-xs"></i>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                    <div>
                                                                        <p class="text-slate-700">
                                                                            <span class="font-semibold">Câu <?= $idx + 1 ?>:</span>
                                                                            <?= htmlspecialchars($qText) ?>
                                                                        </p>
                                                                        <p class="text-[0.7rem] text-slate-500">
                                                                            <span class="font-semibold <?= $isCorrect ? 'text-emerald-600' : 'text-red-600' ?>">
                                                                                Trả lời:
                                                                            </span>
                                                                            <?= htmlspecialchars($uAns) ?>
                                                                        </p>
                                                                        <?php if (!$isCorrect): ?>
                                                                            <p class="text-[0.7rem] text-emerald-600">
                                                                                <span class="font-semibold">Đáp án đúng:</span>
                                                                                <?= htmlspecialchars($cAns) ?>
                                                                            </p>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Toggle xem chi tiết câu hỏi
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-toggle-detail]');
    if (!btn) return;

    const id = btn.getAttribute('data-toggle-detail');
    const panel = document.querySelector('[data-detail-panel="' + id + '"]');
    if (!panel) return;

    const isHidden = panel.classList.contains('hidden');
    if (isHidden) {
        panel.classList.remove('hidden');
        btn.innerHTML = '<i class="fa-solid fa-chevron-up text-[0.6rem] mr-1"></i> Ẩn chi tiết';
    } else {
        panel.classList.add('hidden');
        btn.innerHTML = '<i class="fa-solid fa-chevron-down text-[0.6rem] mr-1"></i> Xem chi tiết';
    }
});
</script>

<?php
$hideFooter = true;
require_once __DIR__ . '/../includes/footer.php';
