<?php
// BẮT ĐẦU: Xử lý tất cả action và redirect TRƯỚC KHI có output
require_once __DIR__ . '/../helpers.php';

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = db_connect();

// Lấy các tham số từ URL
$action = input('action');
$variant_id = intval(input('variant_id', 0));
$product_id = intval(input('product_id', 0));
$q = intval(input('q', 1));

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý product_id thành variant_id nếu cần
if ($product_id && !$variant_id) {
    $stmt = $db->prepare("SELECT id FROM variants WHERE product_id = ? ORDER BY gia ASC LIMIT 1");
    $stmt->execute([$product_id]);
    $variant_id = $stmt->fetchColumn();
}

// XỬ LÝ CÁC ACTION CẦN REDIRECT
$shouldRedirect = false;

if ($action === 'add' && $variant_id) {
    if (isset($_SESSION['cart'][$variant_id])) {
        $_SESSION['cart'][$variant_id] += $q;
    } else {
        $_SESSION['cart'][$variant_id] = $q;
    }
    $shouldRedirect = true;
}

if ($action === 'update' && $variant_id) {
    if ($q <= 0) {
        unset($_SESSION['cart'][$variant_id]);
    } else {
        $_SESSION['cart'][$variant_id] = $q;
    }
    $shouldRedirect = true;
}

if ($action === 'remove' && $variant_id) {
    unset($_SESSION['cart'][$variant_id]);
    $shouldRedirect = true;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    $shouldRedirect = true;
}

// Nếu cần redirect, thực hiện ngay và dừng script
if ($shouldRedirect) {
    header('Location: cart.php');
    exit;
}

// CHỈ KHI KHÔNG CÓ REDIRECT: tiếp tục hiển thị trang bình thường
$pageTitle = 'Giỏ hàng - Perfume Shop';

// Lấy thông tin sản phẩm trong giỏ hàng để hiển thị
$items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    
    // Sửa query để lấy hình ảnh chính xác hơn
    $stmt = $db->query("SELECT 
        v.id AS variant_id, 
        v.gia, 
        v.dung_tich, 
        p.id AS product_id, 
        p.ten, 
        (SELECT url FROM images WHERE product_id = p.id LIMIT 1) AS image_url 
    FROM variants v 
    JOIN products p ON p.id = v.product_id 
    WHERE v.id IN ($ids)");
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $qty = $_SESSION['cart'][$r['variant_id']];
        $subtotal = $r['gia'] * $qty;
        $items[] = [
            'variant' => $r, 
            'qty' => $qty, 
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
}

// BÂY GIỜ mới include header.php để hiển thị HTML
include __DIR__ . '/header.php';
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
}

.cart-item {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.product-image-placeholder {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 0.8rem;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 35px;
    height: 35px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.quantity-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.quantity-input {
    width: 60px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 5px;
}

.empty-cart {
    text-align: center;
    padding: 60px 20px;
}

.empty-cart-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 20px;
}

.price-highlight {
    color: #dc3545;
    font-weight: 600;
    font-size: 1.1em;
}

.total-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 25px;
    margin-top: 30px;
}

.cart-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-checkout {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
}

.btn-continue {
    background: #6c757d;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
}

.remove-btn {
    color: #dc3545;
    background: none;
    border: 1px solid #dc3545;
    padding: 8px 15px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.remove-btn:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .cart-item {
        padding: 15px;
    }
    
    .product-image, .product-image-placeholder {
        width: 60px;
        height: 60px;
    }
    
    .cart-actions {
        flex-direction: column;
    }
    
    .cart-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="cart-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">🛒 Giỏ hàng của bạn</h1>
        <?php if (!empty($items)): ?>
            <span class="badge bg-primary fs-6"><?= array_sum($_SESSION['cart']) ?> sản phẩm</span>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h3 class="text-muted mb-3">Giỏ hàng trống</h3>
            <p class="text-muted mb-4">Hãy thêm một số sản phẩm vào giỏ hàng để bắt đầu mua sắm!</p>
            <a href="<?= base_url('/index.php') ?>" class="btn btn-primary btn-lg">
                🛍️ Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php foreach ($items as $it): ?>
                    <div class="cart-item">
                        <div class="row align-items-center">
                                         <div class="col-md-2 col-3">
    <?php 
    // Lấy tên file ảnh từ Database
    $imgFile = $it['variant']['image_url'] ?? ''; 
    ?>

    <?php if (!empty($imgFile)): ?>
        <!-- TRƯỜNG HỢP CÓ ẢNH -->
        <!-- Logic: /public/assets/ + uploads/tenfile.jpg -->
        <img src="<?= base_url('/public/assets/' . $imgFile) ?>" 
             alt="<?= htmlspecialchars($it['variant']['ten']) ?>" 
             class="img-fluid rounded"
             style="width: 100%; height: auto; object-fit: cover;"
             onerror="this.src='<?= base_url('/public/assets/uploads/no-image.svg') ?>'">
    <?php else: ?>
        <!-- TRƯỜNG HỢP KHÔNG CÓ ẢNH TRONG DB -->
        <img src="<?= base_url('/public/assets/uploads/no-image.svg') ?>" 
             alt="Chưa có ảnh"
             class="img-fluid rounded"
             style="width: 100%; height: auto; opacity: 0.6;">
    <?php endif; ?>
</div>
                            
                            <div class="col-md-4 col-9">
                                <h5 class="mb-1"><?= htmlspecialchars($it['variant']['ten']) ?></h5>
                                <p class="text-muted mb-0 small">Dung tích: <?= $it['variant']['dung_tich'] ?>ml</p>
                                <p class="text-muted mb-0 small">Mã SP: <?= $it['variant']['variant_id'] ?></p>
                            </div>
                            
                            <div class="col-md-3 col-6 mt-3 mt-md-0">
                                <div class="quantity-control">
                                    <form method="get" action="cart.php" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="action" value="update" />
                                        <input type="hidden" name="variant_id" value="<?= $it['variant']['variant_id'] ?>" />
                                        <button type="button" class="quantity-btn minus-btn" 
                                                onclick="updateQuantity(<?= $it['variant']['variant_id'] ?>, -1)">-</button>
                                        <input type="number" name="q" value="<?= $it['qty'] ?>" min="1" 
                                               class="quantity-input" id="qty-<?= $it['variant']['variant_id'] ?>">
                                        <button type="button" class="quantity-btn plus-btn" 
                                                onclick="updateQuantity(<?= $it['variant']['variant_id'] ?>, 1)">+</button>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" style="display:none;" 
                                                id="submit-<?= $it['variant']['variant_id'] ?>">Cập nhật</button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-3 mt-3 mt-md-0 text-md-center">
                                <div class="price-highlight">
                                    <?= number_format($it['variant']['gia'], 0, ',', '.') ?>₫
                                </div>
                                <div class="text-muted small">Tổng: <?= number_format($it['subtotal'], 0, ',', '.') ?>₫</div>
                            </div>
                            
                            <div class="col-md-1 col-3 mt-3 mt-md-0 text-end">
                                <a href="cart.php?action=remove&variant_id=<?= $it['variant']['variant_id'] ?>" 
                                   class="btn remove-btn" 
                                   title="Xóa sản phẩm"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    🗑️
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="text-end mt-3">
                    <a href="cart.php?action=clear" class="btn btn-outline-danger" 
                       onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">
                        🗑️ Xóa toàn bộ giỏ hàng
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="total-section">
                    <h4 class="text-white mb-4">Tổng thanh toán</h4>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Tạm tính:</span>
                        <span><?= number_format($total, 0, ',', '.') ?>₫</span>
                    </div>
                    
                    
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <strong class="fs-5">Tổng cộng:</strong>
                        <strong class="fs-4"><?= number_format($total, 0, ',', '.') ?>₫</strong>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="checkout.php" class="btn btn-success btn-checkout flex-fill">
                            💳 Thanh toán ngay
                        </a>
                        <a href="<?= base_url('/index.php') ?>" class="btn btn-light btn-continue flex-fill">
                            🛒 Tiếp tục mua hàng
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQuantity(variantId, change) {
    const input = document.getElementById('qty-' + variantId);
    const submitBtn = document.getElementById('submit-' + variantId);
    
    let newValue = parseInt(input.value) + change;
    if (newValue < 1) newValue = 1;
    
    input.value = newValue;
    submitBtn.click(); // Tự động submit form
}

// Thêm hiệu ứng cho nút quantity
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>