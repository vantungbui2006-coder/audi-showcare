<?php
/**
 * includes/contact-form.php — Section "Để lại thông tin" dùng chung.
 * Yêu cầu: biến $pdo phải tồn tại (đã require db.php trước đó).
 * Tùy chọn: $preselect_car_id (int) — tự chọn sẵn xe trong dropdown,
 *           $redirect_to (string) — trang sẽ quay về sau khi gửi form
 *           (mặc định 'index.php' nếu không truyền).
 *
 * Tự truy vấn danh sách xe bên trong (không phụ thuộc trang gọi phải
 * chuẩn bị sẵn $cars) để dùng được độc lập ở bất kỳ trang nào.
 */
$preselect_car_id = $preselect_car_id ?? ($_GET['car'] ?? null);
$redirect_to       = $redirect_to ?? 'index.php';

$all_cars_stmt = $pdo->query("SELECT id, model_name FROM cars ORDER BY model_name ASC");
$all_cars = $all_cars_stmt->fetchAll();
?>
<section class="contact-section" id="contact">
    <div class="contact-section__inner">
        <h2>ĐỂ LẠI THÔNG TIN</h2>
        <p class="text-muted">
            Để lại thông tin, đội ngũ tư vấn Audi Showcase sẽ liên hệ trong thời gian sớm nhất.
        </p>

        <?php if (($_GET['inquiry'] ?? '') === 'success'): ?>
            <div class="alert alert-success">
                ✓ Gửi thông tin thành công! Chúng tôi sẽ liên hệ lại với bạn sớm nhất.
            </div>
        <?php elseif (($_GET['inquiry'] ?? '') === 'error'): ?>
            <div class="alert alert-error">
                ✗ <?= htmlspecialchars($_GET['msg'] ?? 'Có lỗi xảy ra, vui lòng thử lại.') ?>
            </div>
        <?php endif; ?>

        <form class="contact-form" action="submit-inquiry.php" method="POST">
            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($redirect_to) ?>">

            <div class="form-group">
                <label>Họ và tên <span class="required">*</span></label>
                <input type="text" name="full_name" required placeholder="Nguyễn Văn A">
            </div>

            <div class="form-group">
                <label>Số điện thoại <span class="required">*</span></label>
                <input type="tel" name="phone" required placeholder="09xx xxx xxx">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="email@example.com">
            </div>

            <div class="form-group">
                <label>Xe quan tâm</label>
                <select name="car_id">
                    <option value="">— Chưa xác định —</option>
                    <?php foreach ($all_cars as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int)$preselect_car_id === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['model_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group full-width">
                <label>Địa chỉ</label>
                <input type="text" name="address" placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố">
            </div>

            <div class="form-group full-width">
                <label>Người giới thiệu</label>
                <input type="text" name="referral_source" placeholder="Tên người giới thiệu bạn đến (nếu có)">
            </div>

            <div class="form-group full-width">
                <label>Lời nhắn</label>
                <textarea name="message" placeholder="Bạn cần tư vấn thêm điều gì?"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Gửi thông tin</button>
        </form>
    </div>
</section>
