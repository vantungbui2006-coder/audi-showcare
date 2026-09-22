<?php
require_once 'includes/db.php';

/*
 * Filter theo category (Sedan/SUV/Coupe/Sports/Hatchback) qua query string:
 *   index.php?category=SUV
 * Dùng prepared statement để chống SQL Injection dù input chỉ từ URL.
 */
$category = $_GET['category'] ?? 'all';
$categories = ['all', 'Sedan', 'SUV', 'Coupe', 'Sports', 'Hatchback'];

if ($category !== 'all' && in_array($category, $categories, true)) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE category = ? ORDER BY price_vnd DESC");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM cars ORDER BY price_vnd DESC");
}
$cars = $stmt->fetchAll();

// Hàm format giá VNĐ dạng "9,50 Tỷ" cho gọn, dùng lại nhiều nơi
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
    <title>Audi Showcase — Bộ sưu tập xe Audi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>

    <?php $current_page = 'home'; include 'includes/header.php'; ?>

    <section class="hero">
        <h1>VƯỢT NGƯỠNG <span>GIỚI HẠN</span></h1>
        <p class="text-muted">
            Khám phá toàn bộ dải sản phẩm Audi — từ những mẫu xe đô thị tinh gọn
            đến những cỗ máy hiệu suất cao mang tinh thần thể thao thuần khiết.
        </p>
    </section>

    <nav class="filter-bar">
        <?php foreach ($categories as $cat): ?>
            <a href="?category=<?= urlencode($cat) ?>"
               class="<?= $category === $cat ? 'active' : '' ?>">
                <?= $cat === 'all' ? 'Tất cả' : htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <main class="car-grid">
        <?php foreach ($cars as $car): ?>
            <article class="car-card">
                <div class="car-card__image" style="background: linear-gradient(135deg, <?= htmlspecialchars($car['main_color']) ?>22, #0a0a0a);">
                    <?php if ($car['thumbnail']): ?>
                        <img src="images/cars/<?= htmlspecialchars($car['thumbnail']) ?>" alt="<?= htmlspecialchars($car['model_name']) ?>">
                    <?php else: ?>
                        <span class="no-image-label">Chưa có ảnh — <?= htmlspecialchars($car['model_name']) ?></span>
                    <?php endif; ?>
                    <span class="car-card__badge"><?= htmlspecialchars($car['category']) ?></span>
                </div>

                <div class="car-card__body">
                    <h2 class="car-card__name"><?= htmlspecialchars($car['model_name']) ?></h2>
                    <div class="car-card__year"><?= htmlspecialchars($car['generation_year']) ?></div>
                    <p class="car-card__desc"><?= htmlspecialchars($car['short_desc']) ?></p>

                    <div class="car-card__specs">
                        <div class="car-card__spec">
                            <div class="value"><?= $car['horsepower'] ?> hp</div>
                            <div class="label">Công suất</div>
                        </div>
                        <div class="car-card__spec">
                            <div class="value"><?= $car['top_speed_kmh'] ?> km/h</div>
                            <div class="label">Tốc độ tối đa</div>
                        </div>
                        <div class="car-card__spec">
                            <div class="value"><?= $car['accel_0_100'] ?>s</div>
                            <div class="label">0-100km/h</div>
                        </div>
                    </div>

                    <div class="car-card__price"><?= formatPrice($car['price_vnd']) ?></div>

                    <div class="car-card__footer">
                        <a href="car-detail.php?slug=<?= urlencode($car['model_slug']) ?>" class="btn btn-primary">Xem chi tiết</a>
                        <a href="?car=<?= $car['id'] ?>#contact" class="btn btn-outline">Liên hệ</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (empty($cars)): ?>
            <p class="text-muted">Không tìm thấy mẫu xe nào trong danh mục này.</p>
        <?php endif; ?>
    </main>

    <?php require 'includes/contact-form.php'; ?>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
