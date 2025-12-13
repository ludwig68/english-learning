<?php
// user/report.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$userId = (int)($_SESSION['user_id'] ?? 0);

// Thông tin user
$stmtUser = $pdo->prepare("SELECT username, full_name FROM users WHERE id = ? AND deleted_at IS NULL");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Lấy tiến độ theo level
$stmtLevels = $pdo->prepare("
    SELECT 
        l.id,
        l.name,
        l.description,
        COALESCE(p.answered_questions, 0) AS answered_questions,
        COALESCE(p.total_questions, 0) AS total_questions,
        COALESCE(p.correct_answers, 0) AS correct_answers,
        p.updated_at,
        (
            SELECT COUNT(*) 
            FROM vocabularies v 
            WHERE v.level_id = l.id AND v.deleted_at IS NULL
        ) AS vocab_count
    FROM levels l
    LEFT JOIN user_level_progress p
        ON p.level_id = l.id
       AND p.user_id  = ?
    ORDER BY l.id
");
$stmtLevels->execute([$userId]);
$levels = $stmtLevels->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng quan
$totalLevels       = count($levels);
$startedLevels     = 0;
$completedLevels   = 0;
$totalAnswered     = 0;
$totalCorrect      = 0;
$lastUpdate        = null;

foreach ($levels as $lv) {
    $answered = (int)$lv['answered_questions'];
    $totalQ   = (int)$lv['total_questions'];
    $vocabCnt = (int)$lv['vocab_count'];

    if ($answered > 0) {
        $startedLevels++;
    }

    $completionBase = $totalQ > 0 ? $totalQ : $vocabCnt;
    if ($completionBase > 0 && $answered >= $completionBase) {
        $completedLevels++;
    }

    $totalAnswered += $answered;
    $totalCorrect  += (int)$lv['correct_answers'];

    if (!empty($lv['updated_at'])) {
        $ts = strtotime($lv['updated_at']);
        if ($ts && ($lastUpdate === null || $ts > $lastUpdate)) {
            $lastUpdate = $ts;
        }
    }
}
$overallAccuracy = $totalAnswered > 0 ? round($totalCorrect * 100 / $totalAnswered) : null;
$lastUpdateStr   = $lastUpdate ? date('d/m/Y H:i', $lastUpdate) : '--';

// Hoạt động gần đây
$recent = [];
try {
    $stmtRecent = $pdo->prepare("
        SELECT h.*, l.name AS level_name
        FROM learning_history h
        LEFT JOIN levels l ON h.level_id = l.id
        WHERE h.user_id = ?
        ORDER BY h.created_at DESC
        LIMIT 6
    ");
    $stmtRecent->execute([$userId]);
    $recent = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if ($e->getCode() !== '42S02') {
        throw $e;
    }
    $recent = [];
}

$pageTitle = 'Báo cáo học tập';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="max-w-6xl mx-auto py-6 px-3 md:px-0">
    <div class="grid md:grid-cols-[260px,1fr] gap-4 lg:gap-6 items-start">
        <?php require_once __DIR__ . '/../includes/user_sidebar.php'; ?>

        <div class="space-y-4">
            <div class="mb-1 flex items-center justify-between gap-2">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Báo cáo học tập</h1>
                    <p class="text-[0.8rem] text-slate-500 mt-0.5">
                        Tổng quan tiến độ, độ chính xác và hoạt động gần đây của bạn.
                    </p>
                </div>
                <span class="text-[0.75rem] text-slate-400">
                    Cập nhật: <?= htmlspecialchars($lastUpdateStr) ?>
                </span>
            </div>

            <!-- Thống kê nhanh -->
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                    <p class="text-xs text-slate-500">Level đã tham gia</p>
                    <div class="flex items-end gap-2 mt-1">
                        <p class="text-2xl font-semibold text-slate-900"><?= $startedLevels ?></p>
                        <span class="text-[0.7rem] text-slate-400">/ <?= $totalLevels ?></span>
                    </div>
                    <p class="text-[0.7rem] text-slate-400 mt-1">Đã có tiến độ</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                    <p class="text-xs text-slate-500">Level hoàn thành</p>
                    <div class="flex items-end gap-2 mt-1">
                        <p class="text-2xl font-semibold text-slate-900"><?= $completedLevels ?></p>
                        <span class="text-[0.7rem] text-slate-400">đã đủ câu</span>
                    </div>
                    <p class="text-[0.7rem] text-slate-400 mt-1">Dựa trên số câu/vocab trong level</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                    <p class="text-xs text-slate-500">Câu đã trả lời</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-1"><?= $totalAnswered ?></p>
                    <p class="text-[0.7rem] text-slate-400 mt-1">Trong practice/quiz</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
                    <p class="text-xs text-slate-500">Độ chính xác</p>
                    <p class="text-2xl font-semibold text-slate-900 mt-1">
                        <?= $overallAccuracy !== null ? $overallAccuracy . '%' : '--' ?>
                    </p>
                    <p class="text-[0.7rem] text-slate-400 mt-1">Đúng / đã trả lời</p>
                </div>
            </div>

            <!-- Tiến độ theo level -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-[#7AE582]/20 flex items-center justify-center">
                            <i class="fa-solid fa-layer-group text-[0.75rem] text-[#16a34a]"></i>
                        </span>
                        Tiến độ từng Level
                    </h2>
                    <span class="text-[0.75rem] text-slate-400">
                        Nhấp vào level để tiếp tục học
                    </span>
                </div>

                <?php if (!$levels): ?>
                    <p class="text-sm text-slate-500">Chưa có level nào. Hãy bắt đầu tại Lộ trình Level.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($levels as $lv): ?>
                            <?php
                                $vocabCount = (int)$lv['vocab_count'];
                                $totalQ     = (int)$lv['total_questions'];
                                $answered   = (int)$lv['answered_questions'];
                                $correct    = (int)$lv['correct_answers'];
                                $base       = $totalQ > 0 ? $totalQ : $vocabCount;
                                $percent    = ($base > 0) ? round(min($answered / $base, 1) * 100) : 0;
                                $acc        = ($answered > 0) ? round($correct * 100 / $answered) : null;
                                $updated    = $lv['updated_at'] ? date('d/m/Y H:i', strtotime($lv['updated_at'])) : '--';
                            ?>
                            <a href="/user/learn.php?level_id=<?= (int)$lv['id'] ?>"
                               class="block rounded-2xl border border-slate-200 bg-slate-50 hover:border-[#7AE582] transition p-3 sm:p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-xs text-slate-500">
                                            #<?= (int)$lv['id'] ?>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($lv['name']) ?></p>
                                            <p class="text-[0.75rem] text-slate-500">
                                                <?= htmlspecialchars(mb_strimwidth($lv['description'] ?? '', 0, 80, '...')) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-500">Cập nhật: <?= htmlspecialchars($updated) ?></p>
                                        <p class="text-xs text-slate-500">Đúng: <?= $correct ?> / TL: <?= $answered ?></p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full bg-white border border-slate-200 overflow-hidden">
                                        <div class="h-full bg-[#7AE582]" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                    <span class="text-xs text-slate-600 min-w-[52px] text-right">
                                        <?= $percent ?>%
                                    </span>
                                    <span class="text-xs text-slate-500">
                                        <?= $acc !== null ? $acc . '% đúng' : 'Chưa có dữ liệu' ?>
                                    </span>
                                </div>
                                <p class="mt-2 text-[0.75rem] text-slate-500">
                                    Vocab: <?= $vocabCount ?> |
                                    Tổng câu: <?= $totalQ ?: ($vocabCount ?: '0') ?> |
                                    Trả lời: <?= $answered ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Hoạt động gần đây -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center">
                            <i class="fa-solid fa-clock-rotate-left text-[0.75rem] text-sky-600"></i>
                        </span>
                        Hoạt động gần đây
                    </h2>
                    <a href="/user/history.php" class="text-xs text-[#16a34a] hover:underline">Xem lịch sử</a>
                </div>

                <?php if (!$recent): ?>
                    <p class="text-sm text-slate-500">Chưa có hoạt động gần đây.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($recent as $item): ?>
                            <?php
                                $mode      = $item['mode'] ?? 'flashcard';
                                $modeLabel = $mode === 'practice' ? 'Practice' : ($mode === 'quiz' ? 'Quiz' : 'Flashcard');
                                $created   = $item['created_at'] ? date('d/m/Y H:i', strtotime($item['created_at'])) : '--';
                                $totalQ    = (int)($item['total_questions'] ?? 0);
                                $correct   = (int)($item['correct_count'] ?? 0);
                                $acc       = $totalQ > 0 ? round($correct * 100 / $totalQ) : null;
                            ?>
                            <div class="flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-[0.7rem] bg-white border border-slate-200 text-slate-600">
                                        <?= htmlspecialchars($modeLabel) ?>
                                    </span>
                                    <p class="text-sm font-medium text-slate-800">
                                        <?= htmlspecialchars($item['level_name'] ?? 'Không rõ Level') ?>
                                    </p>
                                </div>
                                <div class="text-right text-[0.8rem] text-slate-500">
                                    <p><?= $created ?></p>
                                    <?php if ($totalQ > 0): ?>
                                        <p>Đúng <?= $correct ?>/<?= $totalQ ?> <?= $acc !== null ? '(' . $acc . '%)' : '' ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$hideFooter = true;
require_once __DIR__ . '/../includes/footer.php';
?>
