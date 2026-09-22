-- =========================================================
-- CAR_PARTS_SEED.SQL — Dữ liệu mô tả bộ phận xe cho 12 mẫu Audi
-- Chạy SAU khi đã có database.sql (bảng cars phải có dữ liệu trước).
--
-- Dùng SELECT ... FROM cars WHERE model_slug = '...' thay vì hardcode
-- car_id = 1, 2, 3... vì: nếu sau này bạn xóa/thêm xe làm lệch thứ tự
-- auto-increment, hardcode ID sẽ gán NHẦM bộ phận cho xe khác mà
-- không có cảnh báo lỗi nào. Query theo slug luôn đúng dù ID đổi.
--
-- Cách chạy: phpMyAdmin -> chọn database audi_showcase -> tab SQL
-- -> dán toàn bộ -> Go
-- =========================================================

USE audi_showcase;

-- ---------- Audi A1 Sportback ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Thiết kế hatchback 5 cửa với lưới tản nhiệt Singleframe bát giác thu nhỏ, cụm đèn Full LED tiêu chuẩn, đường gân dập nổi dọc thân tạo cảm giác thể thao.', 1 FROM cars WHERE model_slug = 'a1-sportback'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Nền tảng MQB A0 chia sẻ với Volkswagen Polo, treo trước MacPherson, treo sau thanh xoắn, trọng lượng bản thân khoảng 1.2 tấn giúp xe linh hoạt trong đô thị.', 2 FROM cars WHERE model_slug = 'a1-sportback'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 1.5L TFSI 4 xi-lanh tăng áp, 116 mã lực, mô-men xoắn 200Nm, hộp số 7 cấp S tronic ly hợp kép.', 3 FROM cars WHERE model_slug = 'a1-sportback'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Màn hình Audi virtual cockpit 10.25 inch, hệ thống MMI touch, ghế bọc da/nỉ tùy chọn, không gian khoang lái tối giản tập trung vào người lái.', 4 FROM cars WHERE model_slug = 'a1-sportback';

-- ---------- Audi A3 Sedan ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Dáng sedan 4 cửa mui thấp, đèn hậu OLED tùy chọn, tỷ lệ thân xe cân đối tạo cảm giác thể thao hơn bản Sportback.', 1 FROM cars WHERE model_slug = 'a3-sedan'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Nền tảng MQB Evo, treo đa liên kết phía sau (bản quattro), hệ thống lái trợ lực điện tinh chỉnh cho phản hồi nhanh.', 2 FROM cars WHERE model_slug = 'a3-sedan'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI, 150 mã lực, mô-men xoắn 250Nm, hộp số 7 cấp S tronic.', 3 FROM cars WHERE model_slug = 'a3-sedan'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Bảng táp-lô hướng người lái đặc trưng Audi, hệ thống âm thanh Bang & Olufsen tùy chọn, sạc không dây tích hợp.', 4 FROM cars WHERE model_slug = 'a3-sedan';

-- ---------- Audi A4 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Thiết kế sedan doanh nhân với lưới Singleframe rộng bản, đường gân dập sắc sảo dọc hông xe, hệ số cản gió Cd 0.27.', 1 FROM cars WHERE model_slug = 'a4'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ dẫn động quattro ultra tự động ngắt cầu sau khi không cần thiết để tiết kiệm nhiên liệu, treo trước 5 thanh liên kết nhôm.', 2 FROM cars WHERE model_slug = 'a4'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI tăng áp, 190 mã lực, mô-men xoắn 320Nm, hộp số tự động 7 cấp.', 3 FROM cars WHERE model_slug = 'a4'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Ghế thể thao tùy chọn, hệ thống hỗ trợ lái Audi pre sense front, màn hình MMI 10.1 inch cảm ứng.', 4 FROM cars WHERE model_slug = 'a4';

-- ---------- Audi A6 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Lưới tản nhiệt Singleframe lớn mạ crôm, dải đèn LED matrix full, thân xe dài 4.94m tạo dáng vẻ uy nghi.', 1 FROM cars WHERE model_slug = 'a6'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ thống treo khí nén thích ứng tùy chọn, kết cấu thép-nhôm hỗn hợp giảm trọng lượng.', 2 FROM cars WHERE model_slug = 'a6'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI mild-hybrid 48V, 245 mã lực, hỗ trợ tăng tốc êm và tiết kiệm nhiên liệu khi nhả ga.', 3 FROM cars WHERE model_slug = 'a6'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Hai màn hình cảm ứng MMI touch response xếp chồng, đèn viền nội thất 30 màu, hệ thống âm thanh Bang & Olufsen 3D.', 4 FROM cars WHERE model_slug = 'a6';

-- ---------- Audi A8 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Sedan flagship dài 5.17m, thân xe nhôm ASF (Audi Space Frame) giúp giảm trọng lượng đáng kể so với thép truyền thống.', 1 FROM cars WHERE model_slug = 'a8'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ thống treo khí nén chủ động dự đoán địa hình qua camera, hệ dẫn động quattro tiêu chuẩn.', 2 FROM cars WHERE model_slug = 'a8'
UNION ALL SELECT id, 'Động cơ', 'Động cơ V6 3.0L TFSI tăng áp mild-hybrid, 340 mã lực, mô-men xoắn 500Nm, tăng tốc êm ái.', 3 FROM cars WHERE model_slug = 'a8'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Ghế sau massage & duỗi chân, màn hình MMI kép cảm ứng có phản hồi rung, hệ thống lọc không khí ion hóa.', 4 FROM cars WHERE model_slug = 'a8';

-- ---------- Audi Q3 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'SUV cỡ nhỏ với thiết kế vuông vức khỏe khoắn, khoảng sáng gầm xe 200mm phù hợp đa địa hình đô thị.', 1 FROM cars WHERE model_slug = 'q3'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Nền tảng MQB chia sẻ với Volkswagen Tiguan, hệ dẫn động quattro tùy chọn với khớp ly hợp đa đĩa.', 2 FROM cars WHERE model_slug = 'q3'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI, 184 mã lực, hộp số 7 cấp S tronic.', 3 FROM cars WHERE model_slug = 'q3'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Khoang hành lý linh hoạt 530-1525 lít khi gập ghế, màn hình virtual cockpit 10.25 inch tùy chọn.', 4 FROM cars WHERE model_slug = 'q3';

-- ---------- Audi Q5 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'SUV cỡ trung với tỷ lệ cân đối, lưới tản nhiệt hình bát giác lớn, khoảng sáng gầm 209mm.', 1 FROM cars WHERE model_slug = 'q5'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ dẫn động quattro với khớp ly hợp Haldex thế hệ 5, treo khí nén thích ứng tùy chọn.', 2 FROM cars WHERE model_slug = 'q5'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI mild-hybrid, 265 mã lực, mô-men xoắn 370Nm.', 3 FROM cars WHERE model_slug = 'q5'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Không gian 5 chỗ rộng rãi, cửa sổ trời toàn cảnh, hệ thống hỗ trợ đỗ xe 360 độ.', 4 FROM cars WHERE model_slug = 'q5';

-- ---------- Audi Q7 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'SUV 7 chỗ cỡ lớn, thân xe dài 5.06m, khung nhôm-thép hỗn hợp giảm trọng lượng 325kg so với thế hệ trước.', 1 FROM cars WHERE model_slug = 'q7'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ thống treo khí nén 5 chế độ, hệ dẫn động quattro với vi sai trung tâm tự khóa.', 2 FROM cars WHERE model_slug = 'q7'
UNION ALL SELECT id, 'Động cơ', 'Động cơ V6 3.0L TFSI mild-hybrid, 335 mã lực, mô-men xoắn 500Nm.', 3 FROM cars WHERE model_slug = 'q7'
UNION ALL SELECT id, 'Nội thất & Công nghệ', '3 hàng ghế linh hoạt gập điện, hệ thống giải trí hàng ghế sau, khoang hành lý tối đa 2050 lít.', 4 FROM cars WHERE model_slug = 'q7';

-- ---------- Audi Q8 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'SUV coupe với đường mái dốc thể thao, vòm bánh xe mở rộng, đèn hậu OLED dải liền mạch toàn chiều rộng.', 1 FROM cars WHERE model_slug = 'q8'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Chia sẻ nền tảng MLB Evo với Q7 nhưng chỉnh treo khí nén thể thao hơn, vệt bánh xe rộng hơn tăng độ bám cua.', 2 FROM cars WHERE model_slug = 'q8'
UNION ALL SELECT id, 'Động cơ', 'Động cơ V6 3.0L TFSI mild-hybrid, 340 mã lực, mô-men xoắn 500Nm.', 3 FROM cars WHERE model_slug = 'q8'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Khoang lái kết hợp 3 màn hình (đồng hồ kỹ thuật số, MMI, điều khiển điều hòa cảm ứng), ghế thể thao ốp da cao cấp.', 4 FROM cars WHERE model_slug = 'q8';

-- ---------- Audi TT Coupe ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Coupe 2+2 với thiết kế mui thấp mang tính biểu tượng, ít thay đổi thiết kế cốt lõi qua nhiều thế hệ, hệ số cản gió Cd 0.30.', 1 FROM cars WHERE model_slug = 'tt-coupe'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Nền tảng MQB, trọng lượng nhẹ nhờ các chi tiết vỏ xe bằng nhôm ở nắp capo và cửa xe.', 2 FROM cars WHERE model_slug = 'tt-coupe'
UNION ALL SELECT id, 'Động cơ', 'Động cơ 2.0L TFSI, 230 mã lực, mô-men xoắn 370Nm, tăng tốc 0-100km/h trong 6.0 giây.', 3 FROM cars WHERE model_slug = 'tt-coupe'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Khoang lái tối giản đặc trưng, cụm điều khiển điều hòa tích hợp trong núm vô-lăng, chỉ có màn hình virtual cockpit (không màn hình trung tâm riêng).', 4 FROM cars WHERE model_slug = 'tt-coupe';

-- ---------- Audi R8 V10 ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Siêu xe 2 chỗ với khung gầm ASF nhôm-carbon hỗn hợp, động cơ đặt giữa sau, tỷ lệ phân bổ trọng lượng gần 50:50.', 1 FROM cars WHERE model_slug = 'r8-v10'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Treo đa liên kết thể thao, hệ dẫn động quattro thiên hướng cầu sau, lốp hiệu suất cao chuyên dụng.', 2 FROM cars WHERE model_slug = 'r8-v10'
UNION ALL SELECT id, 'Động cơ', 'Động cơ V10 5.2L hút khí tự nhiên (không tăng áp), 570 mã lực, dải vòng tua lên tới 8700 vòng/phút, âm thanh động cơ đặc trưng.', 3 FROM cars WHERE model_slug = 'r8-v10'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Khoang lái phong cách đua xe với nút khởi động tích hợp trên vô-lăng, ghế đua ôm sát cơ thể, mọi thông tin hiển thị trên cụm đồng hồ ảo duy nhất.', 4 FROM cars WHERE model_slug = 'r8-v10';

-- ---------- Audi RS6 Avant ----------
INSERT INTO car_parts (car_id, part_name, part_desc, sort_order)
SELECT id, 'Thân xe & Ngoại thất', 'Thân xe station wagon (Avant) mở rộng vòm bánh 25mm mỗi bên so với A6 thường, ống xả kép hình oval đặc trưng RS.', 1 FROM cars WHERE model_slug = 'rs6-avant'
UNION ALL SELECT id, 'Khung gầm & Hệ thống treo', 'Hệ thống treo khí nén thể thao RS, hệ dẫn động quattro thiên hướng cầu sau, phanh gốm carbon tùy chọn.', 2 FROM cars WHERE model_slug = 'rs6-avant'
UNION ALL SELECT id, 'Động cơ', 'Động cơ V8 4.0L Twin-Turbo mild-hybrid, 600 mã lực, mô-men xoắn 800Nm, tăng tốc 0-100km/h chỉ 3.6 giây dù là xe 5 chỗ có khoang hành lý.', 3 FROM cars WHERE model_slug = 'rs6-avant'
UNION ALL SELECT id, 'Nội thất & Công nghệ', 'Ghế thể thao RS ốp da/Alcantara, logo RS dập nổi trên tựa đầu, hai chế độ lái RS1/RS2 tùy chỉnh riêng trên vô-lăng.', 4 FROM cars WHERE model_slug = 'rs6-avant';
