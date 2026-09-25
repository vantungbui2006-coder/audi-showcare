<?php
require_once 'includes/db.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM cars WHERE model_slug = ?");
$stmt->execute([$slug]);
$car = $stmt->fetch();

if (!$car) {
    header('Location: index.php');
    exit;
}

function formatPrice($vnd) {
    if ($vnd >= 1000000000) {
        return number_format($vnd / 1000000000, 2) . ' Tỷ';
    }
    return number_format($vnd / 1000000) . ' Triệu';
}

// Nội dung QR chỉ mang tính minh họa — KHÔNG phải chuẩn VietQR ngân hàng thật.
// Dùng API tạo QR công khai api.qrserver.com để vẽ ảnh QR từ chuỗi văn bản demo.
$qr_content = "AUDI SHOWCASE - DEMO PAYMENT\n"
    . "Xe: {$car['model_name']}\n"
    . "So tien: " . number_format($car['price_vnd']) . " VND\n"
    . "*** DAY LA MA QR DEMO, KHONG DUNG DE THANH TOAN THAT ***";
$qr_image_url = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($qr_content);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt mua <?= htmlspecialchars($car['model_name']) ?> — Audi Showcase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/order.css">
</head>
<body>

    <?php $current_page = 'order'; include 'includes/header.php'; ?>

    <div class="order-wrap">
        <a href="car-detail.php?slug=<?= urlencode($car['model_slug']) ?>" class="detail-back" style="margin:0 0 20px;display:inline-flex;">
            ← Quay lại chi tiết xe
        </a>

        <div class="demo-badge">⚠ Giao dịch demo — đồ án học tập</div>

        <div class="order-summary">
            <div>
                <h2><?= htmlspecialchars($car['model_name']) ?></h2>
                <div class="text-muted" style="font-size:13px;"><?= htmlspecialchars($car['generation_year']) ?></div>
            </div>
            <div class="price"><?= formatPrice($car['price_vnd']) ?></div>
        </div>

        <?php if (($_GET['order'] ?? '') === 'error'): ?>
            <div class="alert alert-error">
                ✗ <?= htmlspecialchars($_GET['msg'] ?? 'Có lỗi xảy ra, vui lòng kiểm tra lại thông tin.') ?>
            </div>
        <?php endif; ?>

        <form action="submit-order.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

            <div class="order-section-title">Thông tin khách hàng</div>

            <div class="form-group" style="margin-bottom:16px;">
                <label>Họ và tên <span class="required">*</span></label>
                <input type="text" name="customer_name" required placeholder="Nguyễn Văn A">
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label>Số điện thoại <span class="required">*</span></label>
                <input type="tel" name="phone" required placeholder="09xx xxx xxx">
            </div>

            <div class="order-section-title">Phương thức thanh toán</div>

            <div class="payment-methods">
                <label class="payment-method" data-panel="panel-qr">
                    <input type="radio" name="payment_method" value="qr" checked>
                    <div class="title">💳 Quét mã QR</div>
                    <div class="desc">Thanh toán demo qua mã QR minh họa</div>
                </label>
                <label class="payment-method" data-panel="panel-cash">
                    <input type="radio" name="payment_method" value="cash">
                    <div class="title">💵 Tiền mặt</div>
                    <div class="desc">Thanh toán trực tiếp tại đại lý</div>
                </label>
            </div>

            <div id="panel-qr" class="payment-panel active">
                <img class="qr-image" src="<?= htmlspecialchars($qr_image_url) ?>" alt="Mã QR thanh toán demo">
                <p class="text-muted" style="font-size:13px;">Quét mã để xem nội dung minh họa giao dịch.</p>
                <p class="qr-warning">⚠ Đây là mã QR DEMO cho mục đích học tập — không phải mã thanh toán ngân hàng thật, không dùng để chuyển tiền thật.</p>
            </div>

            <div id="panel-cash" class="payment-panel">
                <p class="cash-info">
                    Vui lòng thanh toán trực tiếp bằng tiền mặt tại đại lý khi đến nhận xe.
                    Mang theo CCCD/GPLX bản gốc để đối chiếu.
                </p>
            </div>

            <div class="order-section-title">Giấy tờ tùy thân (demo)</div>
            <p class="file-hint" style="margin-bottom:16px;">
                ⚠ Đây là đồ án học tập — vui lòng dùng số CCCD/GPLX GIẢ ĐỊNH (VD: 000000000000),
                KHÔNG nhập số giấy tờ thật của bất kỳ ai.
            </p>

            <div class="form-group" style="margin-bottom:16px;">
                <label>Số CCCD (demo) <span class="required">*</span></label>
                <input type="text" name="id_card_number" required placeholder="000000000000" pattern="[0-9]{9,12}">
            </div>

            <div class="form-group file-upload-group" style="margin-bottom:20px;">
                <label>Ảnh CCCD (demo)</label>
                <input type="file" name="id_card_image" accept=".jpg,.jpeg,.png,.pdf">
                <div class="file-hint">Chấp nhận JPG, PNG, PDF — tối đa 5MB. Có thể dùng ảnh bất kỳ để test chức năng upload.</div>
            </div>

            <div class="form-group" style="margin-bottom:16px;">
                <label>Số GPLX (demo)</label>
                <input type="text" name="license_number" placeholder="000000000000">
            </div>

            <div class="form-group file-upload-group" style="margin-bottom:20px;">
                <label>Ảnh GPLX (demo)</label>
                <input type="file" name="license_image" accept=".jpg,.jpeg,.png,.pdf">
                <div class="file-hint">Không bắt buộc.</div>
            </div>

            <div class="consent-group">
                <input type="checkbox" name="consent" id="consent" required>
                <label for="consent">
                    Tôi xác nhận đây là giao dịch <strong>demo phục vụ mục đích học tập</strong>,
                    không phải giao dịch mua bán thật, và tôi không nhập thông tin giấy tờ tùy thân thật của bất kỳ ai.
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:16px;">
                Xác nhận đặt mua (Demo)
            </button>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/order.js" defer></script>
</body>
</html>
