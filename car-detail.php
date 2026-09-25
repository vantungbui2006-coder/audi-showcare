<?php
require_once 'includes/db.php';

// Lấy slug từ URL: car-detail.php?slug=r8-v10
$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM cars WHERE model_slug = ?");
$stmt->execute([$slug]);
$car = $stmt->fetch();

// Nếu không tìm thấy xe (slug sai hoặc xe đã bị xóa), dừng lại và báo lỗi
// rõ ràng thay vì để trang trắng hoặc lỗi PHP khó hiểu.
if (!$car) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Không tìm thấy xe</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body style="display:flex;align-items:center;justify-content:center;height:100vh;text-align:center;">
        <div>
            <h1 class="heading" style="margin-bottom:16px;">Không tìm thấy mẫu xe này</h1>
            <a href="index.php" class="btn btn-primary">← Quay lại danh sách</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Lấy danh sách bộ phận xe, sắp theo sort_order
$partsStmt = $pdo->prepare("SELECT * FROM car_parts WHERE car_id = ? ORDER BY sort_order ASC");
$partsStmt->execute([$car['id']]);
$parts = $partsStmt->fetchAll();

function formatPrice($vnd) {
    if ($vnd >= 1000000000) {
        return number_format($vnd / 1000000000, 2) . ' Tỷ';
    }
    return number_format($vnd / 1000000) . ' Triệu';
}

// Giá trị dùng cho include contact-form.php: chọn sẵn xe này trong dropdown,
// và quay về đúng trang chi tiết này sau khi khách gửi form.
$preselect_car_id = $car['id'];
$redirect_to       = 'car-detail.php?slug=' . urlencode($car['model_slug']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($car['model_name']) ?> — Audi Showcase</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/detail.css">
    <link rel="stylesheet" href="css/performance.css">
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>

    <?php $current_page = 'detail'; include 'includes/header.php'; ?>

    <a href="index.php" class="detail-back">← Quay lại danh sách</a>

    <div class="detail-hero" style="background: linear-gradient(135deg, <?= htmlspecialchars($car['main_color']) ?>33, #0a0a0a);">
        <?php if (!empty($car['thumbnail'])): ?>
            <img src="images/cars/<?= htmlspecialchars($car['thumbnail']) ?>" alt="<?= htmlspecialchars($car['model_name']) ?>">
        <?php else: ?>
            <span class="no-image-label">Chưa có ảnh — <?= htmlspecialchars($car['model_name']) ?></span>
        <?php endif; ?>
        <span class="detail-hero__badge"><?= htmlspecialchars($car['category']) ?></span>
    </div>

    <div class="detail-title">
        <h1 class="detail-title__name"><?= htmlspecialchars($car['model_name']) ?></h1>
        <div class="detail-title__year"><?= htmlspecialchars($car['generation_year']) ?></div>
        <p class="detail-title__desc"><?= htmlspecialchars($car['short_desc']) ?></p>
    </div>

    <div class="detail-specs">
        <div class="detail-specs__item">
            <div class="value"><?= $car['horsepower'] ?> hp</div>
            <div class="label">Công suất</div>
        </div>
        <div class="detail-specs__item">
            <div class="value"><?= $car['top_speed_kmh'] ?> km/h</div>
            <div class="label">Tốc độ tối đa</div>
        </div>
        <div class="detail-specs__item">
            <div class="value"><?= $car['accel_0_100'] ?>s</div>
            <div class="label">0-100 km/h</div>
        </div>
        <div class="detail-specs__item">
            <div class="value"><?= htmlspecialchars($car['engine_type']) ?></div>
            <div class="label">Động cơ</div>
        </div>
        <div class="detail-specs__item">
            <div class="value" style="color: var(--color-text-light);"><?= formatPrice($car['price_vnd']) ?></div>
            <div class="label">Giá tham khảo</div>
        </div>
    </div>

    <p class="detail-full-desc"><?= htmlspecialchars($car['full_desc']) ?></p>

    <div class="detail-cta" style="margin-top: 40px; display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
        <a href="order.php?slug=<?= urlencode($car['model_slug']) ?>" class="btn btn-primary">Đặt mua xe này</a>
        <a href="#contact" class="btn btn-outline">Liên hệ tư vấn</a>
    </div>

    <section class="performance-section"
              data-top-speed="<?= $car['top_speed_kmh'] ?>"
              data-horsepower="<?= $car['horsepower'] ?>"
              data-accel="<?= $car['accel_0_100'] ?>">
        <div class="performance-section__heading">
            <h2 class="heading">TRẢI NGHIỆM HIỆU SUẤT</h2>
            <p class="text-muted">
                Mô phỏng thông số vận hành của <?= htmlspecialchars($car['model_name']) ?> dựa trên
                số liệu công bố chính thức — bấm nút bên dưới để xem kim đồng hồ hoạt động.
            </p>
        </div>

        <div class="gauge-grid">
            <!-- Đồng hồ tốc độ tối đa -->
            <div class="gauge-card">
                <svg class="gauge" viewBox="0 0 240 140">
                    <path class="gauge-track" d="M 20 120 A 100 100 0 0 1 220 120" />
                    <path id="speed-arc" class="gauge-fill"
                          d="M 20 120 A 100 100 0 0 1 220 120"
                          stroke-dasharray="314" stroke-dashoffset="314" />
                    <line id="speed-needle" class="gauge-needle"
                          x1="120" y1="120" x2="120" y2="35"
                          transform="rotate(-90 120 120)" />
                    <circle class="gauge-pivot" cx="120" cy="120" r="6" />
                </svg>
                <div class="gauge-value"><span id="speed-value">0</span> <span class="gauge-unit">km/h</span></div>
                <div class="gauge-label">Tốc độ tối đa</div>
                <button id="btn-test-speed" type="button" class="btn btn-outline gauge-btn">Đo tốc độ tối đa</button>
            </div>

            <!-- Đồng hồ công suất động cơ -->
            <div class="gauge-card">
                <svg class="gauge" viewBox="0 0 240 140">
                    <path class="gauge-track" d="M 20 120 A 100 100 0 0 1 220 120" />
                    <path id="power-arc" class="gauge-fill"
                          d="M 20 120 A 100 100 0 0 1 220 120"
                          stroke-dasharray="314" stroke-dashoffset="314" />
                    <line id="power-needle" class="gauge-needle"
                          x1="120" y1="120" x2="120" y2="35"
                          transform="rotate(-90 120 120)" />
                    <circle class="gauge-pivot" cx="120" cy="120" r="6" />
                </svg>
                <div class="gauge-value"><span id="power-value">0</span> <span class="gauge-unit">hp</span></div>
                <div class="gauge-label">Công suất động cơ</div>
                <button id="btn-test-power" type="button" class="btn btn-outline gauge-btn">Đo công suất</button>
            </div>
        </div>

        <!-- Bài test tăng tốc 0-100km/h -->
        <div class="accel-tester">
            <div class="accel-tester__header">
                <h3 class="heading" style="font-size:16px;">KIỂM TRA TĂNG TỐC 0-100 KM/H</h3>
                <div id="accel-timer" class="accel-timer">0.0s</div>
            </div>
            <div class="accel-track">
                <div class="accel-track__line"></div>
                <div id="accel-car" class="accel-car">🏎️</div>
                <div class="accel-track__finish">100 km/h</div>
            </div>
            <div class="accel-tester__footer">
                <button id="btn-test-accel" type="button" class="btn btn-primary">Bắt đầu tăng tốc</button>
                <div id="accel-result" class="accel-result"></div>
            </div>
        </div>
    </section>

    <?php if (!empty($parts)): ?>
    <section class="parts-section">
        <div class="parts-section__heading">
            <h2 class="heading">BỘ PHẬN XE</h2>
            <p class="text-muted">Chi tiết cấu tạo và công nghệ trên <?= htmlspecialchars($car['model_name']) ?></p>
        </div>

        <div class="parts-grid">
            <?php foreach ($parts as $part): ?>
                <div class="part-card">
                    <div class="part-card__image">
                        <?php if (!empty($part['part_image'])): ?>
                            <img src="images/cars/<?= htmlspecialchars($part['part_image']) ?>" alt="<?= htmlspecialchars($part['part_name']) ?>">
                        <?php else: ?>
                            <span class="no-image-label">Chưa có ảnh</span>
                        <?php endif; ?>
                    </div>
                    <div class="part-card__body">
                        <h3 class="part-card__name"><?= htmlspecialchars($part['part_name']) ?></h3>
                        <p class="part-card__desc"><?= htmlspecialchars($part['part_desc']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'includes/contact-form.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <script src="js/performance.js" defer></script>
</body>
</html>
