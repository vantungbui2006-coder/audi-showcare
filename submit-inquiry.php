<?php
/**
 * submit-inquiry.php — Xử lý dữ liệu form liên hệ gửi từ index.php
 *
 * Dùng mẫu PRG (Post/Redirect/Get): sau khi lưu DB xong, redirect
 * người dùng về lại trang chủ kèm query string báo trạng thái,
 * thay vì echo trực tiếp HTML ở đây. Lý do: nếu không redirect,
 * khi khách F5 (refresh) trình duyệt sẽ hỏi "gửi lại dữ liệu form?"
 * và có thể vô tình tạo 2 bản ghi trùng trong database.
 */

require_once 'includes/db.php';
require_once 'includes/mailer.php';

// Chỉ chấp nhận POST — chặn truy cập trực tiếp qua URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Lấy dữ liệu, trim khoảng trắng thừa
$full_name = trim($_POST['full_name'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$email     = trim($_POST['email'] ?? '');
$address   = trim($_POST['address'] ?? '');
$referral  = trim($_POST['referral_source'] ?? '');
$message   = trim($_POST['message'] ?? '');
$car_id    = !empty($_POST['car_id']) ? (int) $_POST['car_id'] : null;

// ---------- Validate phía server ----------
// LUÔN validate ở server dù đã có validate ở client (HTML required),
// vì client-side validate có thể bị bỏ qua (tắt JS, gọi thẳng bằng Postman...).
$errors = [];

if ($full_name === '') {
    $errors[] = 'Vui lòng nhập họ tên.';
}
if ($phone === '' || !preg_match('/^[0-9+\s]{9,15}$/', $phone)) {
    $errors[] = 'Số điện thoại không hợp lệ.';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không hợp lệ.';
}

// ---------- Xác định trang quay về sau khi gửi (chống Open Redirect) ----------
// KHÔNG tin trực tiếp giá trị redirect_to từ POST, vì nếu không kiểm tra,
// kẻ xấu có thể sửa nó thành một URL bên ngoài (VD: http://trang-lua-dao.com)
// và lừa nạn nhân bị chuyển hướng sau khi gửi form trên site của mình.
// Giải pháp: chỉ chấp nhận đúng 2 dạng đường dẫn nội bộ đã biết trước.
$redirect_to = $_POST['redirect_to'] ?? 'index.php';
$is_valid_redirect =
    $redirect_to === 'index.php' ||
    preg_match('/^car-detail\.php\?slug=[a-z0-9\-]+$/', $redirect_to);

if (!$is_valid_redirect) {
    $redirect_to = 'index.php';
}

if (!empty($errors)) {
    // Gộp lỗi vào query string (đơn giản cho đồ án; dự án thật nên dùng session flash message)
    $msg = urlencode(implode(' ', $errors));
    $separator = str_contains($redirect_to, '?') ? '&' : '?';
    header("Location: $redirect_to{$separator}inquiry=error&msg=$msg#contact");
    exit;
}

// ---------- Lưu vào database bằng prepared statement ----------
// Prepared statement (?) tách biệt code SQL và dữ liệu người dùng nhập,
// nên dù khách gõ ký tự đặc biệt (', ", ;) vào form cũng KHÔNG thể
// chèn thêm câu lệnh SQL độc hại (chống SQL Injection).
$stmt = $pdo->prepare(
    "INSERT INTO inquiries (car_id, full_name, phone, email, address, referral_source, message)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->execute([
    $car_id,
    $full_name,
    $phone,
    $email ?: null,
    $address ?: null,
    $referral ?: null,
    $message ?: null,
]);

// ---------- Gửi email thông báo cho admin ----------
// Đặt SAU khi INSERT đã chạy thành công: nếu gửi email lỗi, dữ liệu khách
// hàng vẫn được lưu an toàn trong database — không phụ thuộc vào email.
$carName = 'Chưa xác định';
if ($car_id) {
    $carStmt = $pdo->prepare("SELECT model_name FROM cars WHERE id = ?");
    $carStmt->execute([$car_id]);
    $carName = $carStmt->fetchColumn() ?: 'Chưa xác định';
}

$emailBody = "
    <h2>Khách hàng mới để lại thông tin liên hệ</h2>
    <p><strong>Họ tên:</strong> " . htmlspecialchars($full_name) . "</p>
    <p><strong>Số điện thoại:</strong> " . htmlspecialchars($phone) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($email ?: '—') . "</p>
    <p><strong>Địa chỉ:</strong> " . htmlspecialchars($address ?: '—') . "</p>
    <p><strong>Người giới thiệu:</strong> " . htmlspecialchars($referral ?: '—') . "</p>
    <p><strong>Xe quan tâm:</strong> " . htmlspecialchars($carName) . "</p>
    <p><strong>Lời nhắn:</strong> " . htmlspecialchars($message ?: '—') . "</p>
    <hr>
    <p style='color:#888;font-size:12px;'>Email tự động từ website Audi Showcase (đồ án học tập).</p>
";
sendAdminNotification('🔔 Khách hàng mới liên hệ: ' . $full_name, $emailBody);

header("Location: $redirect_to{$separator}inquiry=success#contact");
exit;
