<?php
/**
 * submit-order.php — Xử lý đơn đặt mua demo: validate, upload file an toàn,
 * lưu vào bảng orders + customer_documents trong 1 transaction.
 */
require_once 'includes/db.php';
require_once 'includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$car_id         = (int) ($_POST['car_id'] ?? 0);
$customer_name  = trim($_POST['customer_name'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$id_card_number = trim($_POST['id_card_number'] ?? '');
$license_number = trim($_POST['license_number'] ?? '');
$consent        = isset($_POST['consent']);

// ---------- Validate ----------
$errors = [];

$carStmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$carStmt->execute([$car_id]);
$car = $carStmt->fetch();

if (!$car) {
    $errors[] = 'Không tìm thấy xe.';
}
if ($customer_name === '') {
    $errors[] = 'Vui lòng nhập họ tên.';
}
if ($phone === '' || !preg_match('/^[0-9+\s]{9,15}$/', $phone)) {
    $errors[] = 'Số điện thoại không hợp lệ.';
}
if (!in_array($payment_method, ['qr', 'cash'], true)) {
    $errors[] = 'Vui lòng chọn phương thức thanh toán.';
}
if ($id_card_number === '' || !preg_match('/^[0-9]{9,12}$/', $id_card_number)) {
    $errors[] = 'Số CCCD (demo) không hợp lệ — chỉ nhập 9-12 chữ số.';
}
if (!$consent) {
    $errors[] = 'Vui lòng xác nhận đây là giao dịch demo trước khi tiếp tục.';
}

if (!empty($errors)) {
    $msg = urlencode(implode(' ', $errors));
    $slug = $car['model_slug'] ?? '';
    header("Location: order.php?slug=$slug&order=error&msg=$msg");
    exit;
}

/**
 * handleUpload() — Xử lý 1 file upload an toàn.
 * Trả về tên file đã lưu (hoặc null nếu không có file / có lỗi).
 *
 * Các bước bảo mật quan trọng, GIẢI THÍCH LÝ DO từng bước:
 * 1. Kiểm tra MIME type THẬT của file (finfo) thay vì chỉ tin đuôi file
 *    (.jpg) — vì kẻ xấu có thể đổi tên file .php thành .jpg để qua mặt
 *    kiểm tra đuôi file, nhưng finfo đọc nội dung byte thật của file
 *    nên khó bị đánh lừa hơn.
 * 2. Giới hạn dung lượng tối đa — tránh bị tấn công làm đầy ổ đĩa server
 *    bằng cách upload file khổng lồ liên tục.
 * 3. Đặt tên file MỚI ngẫu nhiên (không giữ tên gốc người dùng đặt) —
 *    tránh 2 vấn đề: (a) path traversal nếu tên file chứa "../",
 *    (b) đoán được URL file của người khác nếu tên file dễ đoán.
 */
function handleUpload($fieldName, $uploadDir) {
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // Không có file, không phải lỗi (vd: GPLX không bắt buộc)
    }
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null; // Có lỗi upload (VD: vượt kích thước) — bỏ qua, không chặn cả đơn hàng
    }

    $maxSizeBytes = 5 * 1024 * 1024; // 5MB
    if ($_FILES[$fieldName]['size'] > $maxSizeBytes) {
        return null;
    }

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'application/pdf' => 'pdf',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMimes[$realMime])) {
        return null; // File không đúng định dạng cho phép
    }

    $extension = $allowedMimes[$realMime];
    $newFilename = bin2hex(random_bytes(16)) . '.' . $extension;
    $destination = $uploadDir . '/' . $newFilename;

    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
        return $newFilename;
    }
    return null;
}

$uploadDir = __DIR__ . '/uploads/documents';
$id_card_image = handleUpload('id_card_image', $uploadDir);
$license_image = handleUpload('license_image', $uploadDir);

// ---------- Lưu vào database ----------
// Dùng transaction: nếu bước tạo đơn hàng thành công nhưng bước lưu giấy tờ
// thất bại giữa chừng, toàn bộ sẽ được rollback — tránh tạo ra đơn hàng
// "mồ côi" không có giấy tờ đính kèm.
try {
    $pdo->beginTransaction();

    $orderStmt = $pdo->prepare(
        "INSERT INTO orders (car_id, customer_name, phone, payment_method, amount_vnd, status)
         VALUES (?, ?, ?, ?, ?, 'pending')"
    );
    $orderStmt->execute([$car_id, $customer_name, $phone, $payment_method, $car['price_vnd']]);
    $orderId = $pdo->lastInsertId();

    $docStmt = $pdo->prepare(
        "INSERT INTO customer_documents (order_id, id_card_number, id_card_image, license_number, license_image)
         VALUES (?, ?, ?, ?, ?)"
    );
    $docStmt->execute([$orderId, $id_card_number, $id_card_image, $license_number ?: null, $license_image]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    header("Location: order.php?slug={$car['model_slug']}&order=error&msg=" . urlencode('Có lỗi hệ thống, vui lòng thử lại.'));
    exit;
}

// ---------- Gửi email thông báo cho admin ----------
// Đặt SAU khi commit() thành công: đơn hàng đã lưu chắc chắn vào database
// trước, việc gửi email chỉ là thông báo thêm, không ảnh hưởng đơn hàng.
$orderCode = 'DH' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
$paymentLabel = $payment_method === 'qr' ? 'Quét mã QR (demo)' : 'Tiền mặt tại đại lý';

$emailBody = "
    <h2>Đơn đặt mua xe mới (Demo)</h2>
    <p><strong>Mã đơn hàng:</strong> {$orderCode}</p>
    <p><strong>Xe:</strong> " . htmlspecialchars($car['model_name']) . "</p>
    <p><strong>Giá trị:</strong> " . number_format($car['price_vnd']) . " VNĐ</p>
    <p><strong>Khách hàng:</strong> " . htmlspecialchars($customer_name) . "</p>
    <p><strong>Số điện thoại:</strong> " . htmlspecialchars($phone) . "</p>
    <p><strong>Phương thức thanh toán:</strong> " . htmlspecialchars($paymentLabel) . "</p>
    <p><strong>Đã nộp giấy tờ:</strong> " . ($id_card_image ? 'CCCD ✓' : 'CCCD ✗') . ($license_image ? ', GPLX ✓' : '') . "</p>
    <hr>
    <p style='color:#888;font-size:12px;'>Xem chi tiết đầy đủ tại trang admin-orders.php trên localhost. Email tự động từ website Audi Showcase (đồ án học tập).</p>
";
sendAdminNotification('🚗 Đơn đặt mua mới: ' . $orderCode . ' — ' . $car['model_name'], $emailBody);

header("Location: order-success.php?order_id=$orderId");
exit;
