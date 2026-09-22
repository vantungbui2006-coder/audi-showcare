-- =========================================================
-- MIGRATE.SQL — chạy file này nếu bạn ĐÃ import database.sql trước đó
-- Mục đích: thêm 2 cột address + referral_source vào bảng inquiries
-- mà KHÔNG xóa dữ liệu đang có.
--
-- Cách chạy: phpMyAdmin -> chọn database audi_showcase -> tab "SQL"
-- -> dán đoạn dưới -> bấm Go
-- =========================================================

USE audi_showcase;

ALTER TABLE inquiries
    ADD COLUMN address VARCHAR(255) DEFAULT NULL AFTER email,
    ADD COLUMN referral_source VARCHAR(100) DEFAULT NULL AFTER address;

-- Sửa luôn lỗi ký tự lạ trong mô tả Audi A8 (nếu bạn đã import bản cũ)
UPDATE cars
SET short_desc = 'Sedan chủ lực hạng sang — đỉnh cao công nghệ và sự tĩnh lặng.'
WHERE model_slug = 'a8';
