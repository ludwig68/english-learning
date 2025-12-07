<?php
// user/save_history.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);

// Lấy dữ liệu POST (gửi từ JS)
$level_id       = (int)($_POST['level_id'] ?? 0);
$mode           = trim($_POST['mode'] ?? 'practice'); // 'practice' | 'flashcard'
$vocab_count    = (int)($_POST['vocab_count'] ?? 0);
$correct_count  = (int)($_POST['correct_count'] ?? 0);
$total_questions= (int)($_POST['total_questions'] ?? 0);
$note           = trim($_POST['note'] ?? '');

// questions sẽ là JSON string -> decode ra mảng
$questions_json = $_POST['questions'] ?? '[]';
$questions      = json_decode($questions_json, true);

if (!$userId || !$level_id || !$total_questions) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu cần thiết']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Insert vào learning_history
    $stmt = $pdo->prepare("
        INSERT INTO learning_history
            (user_id, level_id, mode, vocab_count, correct_count, total_questions, note, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $userId,
        $level_id,
        $mode,
        $vocab_count,
        $correct_count,
        $total_questions,
        $note
    ]);

    $historyId = (int)$pdo->lastInsertId();

    // 2. Insert chi tiết từng câu (nếu có gửi lên)
    if (is_array($questions)) {
        $stmtItem = $pdo->prepare("
            INSERT INTO learning_history_items
                (history_id, question_text, user_answer, correct_answer, is_correct, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        foreach ($questions as $q) {
            $question_text = $q['question_text'] ?? '';
            $user_answer   = $q['user_answer']   ?? '';
            $correct_answer= $q['correct_answer']?? '';
            $is_correct    = !empty($q['is_correct']) ? 1 : 0;

            $stmtItem->execute([
                $historyId,
                $question_text,
                $user_answer,
                $correct_answer,
                $is_correct
            ]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu lịch sử học',
        'history_id' => $historyId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi lưu lịch sử: ' . $e->getMessage()
    ]);
}
