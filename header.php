<?php
/**
 * includes/header.php — Header + nav dùng chung.
 * Trang gọi cần khai báo $current_page trước khi include, VD:
 *   $current_page = 'home';  // hoặc 'detail'
 *   include 'includes/header.php';
 */
$current_page = $current_page ?? '';
?>
<header class="site-header">
    <a href="index.php" class="logo">AUDI SHOWCASE</a>
    <nav>
        <ul>
            <li><a href="index.php" class="<?= $current_page === 'home' ? 'active' : '' ?>">Bộ sưu tập</a></li>
            <li><a href="index.php#contact">Ưu đãi</a></li>
            <li><a href="index.php#contact">Liên hệ</a></li>
        </ul>
    </nav>
</header>
