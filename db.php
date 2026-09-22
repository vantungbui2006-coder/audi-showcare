<?php
/**
 * db.php — Kết nối database dùng chung cho toàn bộ site.
 * Mọi trang PHP cần dữ liệu chỉ cần: require_once 'includes/db.php';
 * rồi dùng biến $pdo.
 *
 * Dùng PDO thay vì mysqli vì: hỗ trợ prepared statements dễ dàng
 * (chống SQL Injection), API nhất quán hơn, dễ đổi sang DB khác sau này.
 */

$host   = 'localhost';
$dbname = 'audi_showcase';
$user   = 'root';      // mặc định XAMPP/WAMP là 'root'
$pass   = '';           // mặc định XAMPP không có mật khẩu

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Trong đồ án: hiển thị lỗi trực tiếp để dễ debug.
    // Nếu triển khai thật: KHÔNG bao giờ echo lỗi chi tiết ra ngoài,
    // vì nó có thể lộ thông tin cấu trúc database cho kẻ tấn công.
    die("Lỗi kết nối database: " . $e->getMessage());
}
