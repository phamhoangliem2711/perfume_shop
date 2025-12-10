<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();
$pageTitle = 'Lab Thực Hành - Perfume Shop';
include __DIR__ . '/header.php';

// Lấy đường dẫn đến thư mục lab_thuchanh
$lab_path = __DIR__ . '/assets/lab_thuchanh';

// Lấy tham số tuần từ URL (nếu có)
$selected_tuan = isset($_GET['tuan']) ? $_GET['tuan'] : null;

// Lấy danh sách các thư mục tuần
$tuan_dirs = [];
if (is_dir($lab_path)) {
    $items = scandir($lab_path);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir($lab_path . '/' . $item) && preg_match('/^tuan\d+$/', $item)) {
            $tuan_dirs[] = $item;
        }
    }
    sort($tuan_dirs);
}

// Nếu chưa chọn tuần, chọn tuần đầu tiên
if (!$selected_tuan && !empty($tuan_dirs)) {
    $selected_tuan = $tuan_dirs[0];
}

// Lấy danh sách files trong tuần được chọn
$files = [];
if ($selected_tuan) {
    $tuan_path = $lab_path . '/' . $selected_tuan;
    if (is_dir($tuan_path)) {
        $items = scandir($tuan_path);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && !is_dir($tuan_path . '/' . $item)) {
                // Lấy phần mở rộng
                $ext = pathinfo($item, PATHINFO_EXTENSION);
                // Chỉ hiển thị file php, html, css, js
                if (in_array(strtolower($ext), ['php', 'html', 'css', 'js'])) {
                    $files[] = $item;
                }
            }
        }
        sort($files);
    }
}
?>

<style>
    body { background: #f8f9fa; }
    .container h1 { font-size: 1.6rem; margin-bottom: 2rem; }
    .tuan-btn { margin-right: 0.5rem; margin-bottom: 0.5rem; }
    .tuan-btn.active { 
        background-color: #0d6efd !important; 
        color: white !important;
        border-color: #0d6efd !important;
    }
    .file-list {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .file-item {
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    .file-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
    }
    .file-icon {
        font-size: 1.2rem;
        margin-right: 0.75rem;
    }
    .file-name {
        flex-grow: 1;
        font-weight: 500;
    }
    .file-actions {
        display: flex;
        gap: 0.5rem;
    }
    .empty-message {
        text-align: center;
        color: #6c757d;
        padding: 2rem;
    }
</style>

<div class="container" style="max-width: 900px; margin-bottom: 3rem;">
    <h1>📚 Lab Thực Hành</h1>
    
    <!-- Chọn tuần -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Chọn tuần học:</h5>
            <div>
                <?php foreach ($tuan_dirs as $tuan): ?>
                    <a href="?tuan=<?= urlencode($tuan) ?>" 
                       class="btn btn-outline-primary tuan-btn <?= $tuan === $selected_tuan ? 'active' : '' ?>">
                        <?= htmlspecialchars(ucfirst($tuan)) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Danh sách file -->
    <?php if ($selected_tuan): ?>
        <div class="file-list">
            <h5 class="mb-3">
                📁 Các file trong <?= htmlspecialchars(ucfirst($selected_tuan)) ?>
            </h5>
            
            <?php if (empty($files)): ?>
                <div class="empty-message">
                    <p>Không có file nào trong tuần này hoặc đang chuẩn bị.</p>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($files as $file): ?>
                        <?php
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $icons = [
                                'php' => '🐘',
                                'html' => '🌐',
                                'css' => '🎨',
                                'js' => '⚙️'
                            ];
                            $icon = $icons[strtolower($ext)] ?? '📄';
                            $file_url = base_url('/public/assets/lab_thuchanh/' . $selected_tuan . '/' . urlencode($file));
                        ?>
                        <div class="file-item">
                            <span>
                                <span class="file-icon"><?= $icon ?></span>
                                <span class="file-name"><?= htmlspecialchars($file) ?></span>
                            </span>
                            <div class="file-actions">
                                <a href="<?= $file_url ?>" 
                                   class="btn btn-sm btn-info" 
                                   target="_blank"
                                   title="Xem file">
                                   👁️ Xem
                                </a>
                                <?php if (strtolower($ext) === 'php'): ?>
                                    <a href="<?= $file_url ?>" 
                                       class="btn btn-sm btn-success" 
                                       target="_blank"
                                       title="Chạy file">
                                       ▶️ Chạy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/footer.php'; ?>
