<?php
/**
 * admin-orders.php — Xem danh sách đơn đặt mua demo.
 * ⚠️ Cùng cảnh báo bảo mật như admin-inquiries.php: chưa có đăng nhập,
 * chỉ dùng nội bộ trên localhost khi làm đồ án.
 *
 * Số CCCD hiển thị CHE BỚT (chỉ 4 số cuối) ngay cả ở trang admin —
 * nguyên tắc bảo mật tốt: hạn chế lộ dữ liệu nhạy cảm ở mức tối đa
 * có thể, kể cả với người có quyền xem nội bộ.
 */
require_once 'includes/db.php';

$stmt = $pdo->query(
    "SELECT orders.*, cars.model_name,
            customer_documents.id_card_number, customer_documents.id_card_image,
            customer_documents.license_number, customer_documents.license_image
     FROM orders
     JOIN cars ON orders.car_id = cars.id
     LEFT JOIN customer_documents ON customer_documents.order_id = orders.id
     ORDER BY orders.created_at DESC"
);
$orders = $stmt->fetchAll();

function maskIdNumber($number) {
    if (!$number) return '—';
    $len = strlen($number);
    if ($len <= 4) return $number;
    return str_repeat('•', $len - 4) . substr($number, -4);
}

function formatPrice($vnd) {
    if ($vnd >= 1000000000) {
        return number_format($vnd / 1000000000, 2) . ' Tỷ';
    }
    return number_format($vnd / 1000000) . ' Triệu';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Danh sách đơn hàng</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-wrap { padding: 40px 60px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--color-border); }
        th { text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; color: var(--color-text-muted); }
        .status-pending { color: #f0b429; font-weight: 600; }
        .status-confirmed { color: #6fdd8c; font-weight: 600; }
        .status-cancelled { color: #ff6b7d; font-weight: 600; }
        .empty-state { padding: 60px; text-align: center; color: var(--color-text-muted); }
        .doc-badge { font-size: 11px; padding: 3px 8px; border-radius: 10px; background: rgba(255,255,255,0.08); }
    </style>
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="logo">AUDI SHOWCASE — ADMIN</a>
        <nav><ul>
            <li><a href="admin-inquiries.php">Liên hệ</a></li>
            <li><a href="index.php">← Về trang chủ</a></li>
        </ul></nav>
    </header>

    <div class="admin-wrap">
        <h1 class="heading" style="margin-bottom: 24px;">
            Danh sách đơn hàng (<?= count($orders) ?>)
        </h1>

        <?php if (empty($orders)): ?>
            <p class="empty-state">Chưa có đơn hàng nào.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Thời gian</th>
                    <th>Khách hàng</th>
                    <th>SĐT</th>
                    <th>Xe</th>
                    <th>Giá trị</th>
                    <th>Thanh toán</th>
                    <th>Số CCCD (che)</th>
                    <th>Giấy tờ</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td>DH<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($o['created_at']))) ?></td>
                    <td><?= htmlspecialchars($o['customer_name']) ?></td>
                    <td><?= htmlspecialchars($o['phone']) ?></td>
                    <td><?= htmlspecialchars($o['model_name']) ?></td>
                    <td><?= formatPrice($o['amount_vnd']) ?></td>
                    <td><?= $o['payment_method'] === 'qr' ? 'QR (demo)' : 'Tiền mặt' ?></td>
                    <td><?= htmlspecialchars(maskIdNumber($o['id_card_number'] ?? '')) ?></td>
                    <td>
                        <?php if ($o['id_card_image']): ?><span class="doc-badge">✓ CCCD</span><?php endif; ?>
                        <?php if ($o['license_image']): ?><span class="doc-badge">✓ GPLX</span><?php endif; ?>
                        <?php if (!$o['id_card_image'] && !$o['license_image']): ?>—<?php endif; ?>
                    </td>
                    <td class="status-<?= htmlspecialchars($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
