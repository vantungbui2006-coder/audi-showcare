<?php
require_once 'includes/db.php';

$order_id = (int) ($_GET['order_id'] ?? 0);

$stmt = $pdo->prepare(
    "SELECT orders.*, cars.model_name, cars.model_slug
     FROM orders
     JOIN cars ON orders.car_id = cars.id
     WHERE orders.id = ?"
);
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$orderCode = 'DH' . str_pad($order['id'], 6, '0', STR_PAD_LEFT);
$paymentLabel = $order['payment_method'] === 'qr' ? 'Quét mã QR (demo)' : 'Tiền mặt tại đại lý';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt mua thành công — Audi Showcase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/order.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="order-success">
        <div class="icon">✓</div>
        <h1 class="heading">Đặt mua thành công (Demo)</h1>
        <p class="text-muted">
            Cảm ơn <?= htmlspecialchars($order['customer_name']) ?> đã quan tâm đến
            <strong><?= htmlspecialchars($order['model_name']) ?></strong>.
        </p>
        <div class="order-code">Mã đơn hàng: <?= $orderCode ?></div>
        <p class="text-muted" style="font-size:14px;max-width:480px;margin:0 auto 30px;">
            Phương thức thanh toán: <?= htmlspecialchars($paymentLabel) ?><br>
            Đây là đơn hàng <strong>demo</strong> cho mục đích học tập — nhân viên tư vấn (giả lập)
            sẽ không thực sự liên hệ. Trạng thái đơn hàng có thể xem tại trang quản trị nội bộ.
        </p>
        <a href="index.php" class="btn btn-primary">← Về trang chủ</a>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
