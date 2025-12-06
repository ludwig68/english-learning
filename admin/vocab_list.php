<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stmt = $pdo->query("
    SELECT v.*, l.name AS level_name
    FROM vocabularies v
    JOIN levels l ON v.level_id = l.id
    WHERE v.deleted_at IS NULL
    ORDER BY l.id, v.id
");
$vocabList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Từ vựng | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark navbar-lightblue">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="/admin/index.php" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="/auth/logout.php" class="nav-link">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </a>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/admin/index.php" class="brand-link">
            <span class="brand-text font-weight-light">English Admin</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="/admin/index.php" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/vocab_list.php" class="nav-link active">
                            <i class="nav-icon fa-solid fa-book"></i><p>Từ vựng</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Danh sách từ vựng</h3>
                        <a href="/admin/vocab_form.php" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-plus"></i> Thêm từ vựng
                        </a>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Level</th>
                                <th>Word</th>
                                <th>Meaning</th>
                                <th>Type</th>
                                <th>Example</th>
                                <th>Image</th>
                                <th>Audio</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($vocabList as $v): ?>
                                <tr>
                                    <td><?= $v['id'] ?></td>
                                    <td><?= htmlspecialchars($v['level_name']) ?></td>
                                    <td><?= htmlspecialchars($v['word']) ?></td>
                                    <td><?= htmlspecialchars($v['meaning']) ?></td>
                                    <td><?= htmlspecialchars($v['type']) ?></td>
                                    <td><?= htmlspecialchars(mb_strimwidth($v['example_sentence'], 0, 40, '...')) ?></td>
                                    <td>
                                        <?php if ($v['image_path']): ?>
                                            <img src="/<?= htmlspecialchars($v['image_path']) ?>" alt=""
                                                 style="height:35px;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($v['audio_path']): ?>
                                            <i class="fa-solid fa-volume-high text-success"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/admin/vocab_form.php?id=<?= $v['id'] ?>"
                                           class="btn btn-warning btn-xs">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="/admin/vocab_delete.php?id=<?= $v['id'] ?>"
                                           onclick="return confirm('Xóa từ này?')"
                                           class="btn btn-danger btn-xs">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$vocabList): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Chưa có từ vựng.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer text-sm">
        <strong>&copy; <?= date('Y') ?> English Learning.</strong>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
