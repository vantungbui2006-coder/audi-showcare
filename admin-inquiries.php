<?php
/**
 * admin-inquiries.php — Xem danh sách khách hàng đã để lại thông tin.
 *
 * ⚠️ CẢNH BÁO BẢO MẬT: file này CHƯA có đăng nhập/xác thực.
 * Bất kỳ ai biết đường dẫn (localhost/audi-showcase/admin-inquiries.php)
 * đều xem được toàn bộ số điện thoại, địa chỉ khách hàng.
 * Với đồ án chạy trên máy cá nhân (localhost) thì không sao.
 * Nếu deploy lên domain công khai, BẮT BUỘC thêm hệ thống đăng nhập
 * (session + password) trước khi đưa file này lên — tôi sẽ hướng dẫn
 * ở giai đoạn làm hosting/deploy.
 */

require_once 'includes/db.php';

$stmt = $pdo->query(
    "SELECT inquiries.*, cars.model_name
     FROM inquiries
     LEFT JOIN cars ON inquiries.car_id = cars.id
     ORDER BY inquiries.created_at DESC"
);
$inquiries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Danh sách khách hàng liên hệ</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-wrap { padding: 40px 60px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid var(--color-border); }
        th { text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; color: var(--color-text-muted); }
        .status-new { color: #ff6b7d; font-weight: 600; }
        .empty-state { padding: 60px; text-align: center; color: var(--color-text-muted); }
    </style>
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="logo">AUDI SHOWCASE — ADMIN</a>
        <nav><ul><li><a href="index.php">← Về trang chủ</a></li></ul></nav>
    </header>

    <div class="admin-wrap">
        <h1 class="heading" style="margin-bottom: 24px;">
            Danh sách khách hàng liên hệ (<?= count($inquiries) ?>)
        </h1>

        <?php if (empty($inquiries)): ?>
            <p class="empty-state">Chưa có khách hàng nào để lại thông tin.</p>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th>Họ tên</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th>Người giới thiệu</th>
                    <th>Xe quan tâm</th>
                    <th>Lời nhắn</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiries as $row): ?>
                <tr>
                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['created_at']))) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['address'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['referral_source'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['model_name'] ?? 'Không chọn xe cụ thể') ?></td>
                    <td><?= htmlspecialchars($row['message'] ?? '—') ?></td>
                    <td class="status-<?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
