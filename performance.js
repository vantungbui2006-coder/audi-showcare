/**
 * js/performance.js — Điều khiển animation cho section "TRẢI NGHIỆM HIỆU SUẤT"
 * trong car-detail.php. Dùng chung cho mọi xe: đọc thông số riêng của từng xe
 * qua thuộc tính data-* gắn trên <section class="performance-section">,
 * không hardcode số liệu trong file JS này.
 */

document.addEventListener('DOMContentLoaded', function () {
    var section = document.querySelector('.performance-section');
    if (!section) return; // Trang không có section này thì bỏ qua, không lỗi

    var topSpeed  = parseFloat(section.dataset.topSpeed);
    var horsepower = parseFloat(section.dataset.horsepower);
    var accel      = parseFloat(section.dataset.accel);

    // Thang đo cố định cho MỌI xe (không đổi theo từng xe) để kim đồng hồ
    // có ý nghĩa so sánh trực quan: xe yếu hơn thì kim quay ít hơn, xe mạnh
    // hơn thì kim quay gần hết mặt đồng hồ.
    var SPEED_SCALE_MAX = 350; // km/h — cao hơn xe nhanh nhất (R8: 324km/h)
    var HP_SCALE_MAX    = 700; // hp   — cao hơn xe mạnh nhất (RS6: 600hp)

    setupGauge({
        btn: document.getElementById('btn-test-speed'),
        needle: document.getElementById('speed-needle'),
        arc: document.getElementById('speed-arc'),
        valueEl: document.getElementById('speed-value'),
        target: topSpeed,
        max: SPEED_SCALE_MAX,
        withEngineSound: true,
    });

    setupGauge({
        btn: document.getElementById('btn-test-power'),
        needle: document.getElementById('power-needle'),
        arc: document.getElementById('power-arc'),
        valueEl: document.getElementById('power-value'),
        target: horsepower,
        max: HP_SCALE_MAX,
        withEngineSound: false,
    });

    setupAccelTester({
        btn: document.getElementById('btn-test-accel'),
        car: document.getElementById('accel-car'),
        timerEl: document.getElementById('accel-timer'),
        resultEl: document.getElementById('accel-result'),
        duration: accel,
    });
});

/**
 * setupGauge — Gắn sự kiện click cho 1 đồng hồ (dùng chung cho đồng hồ
 * tốc độ và đồng hồ công suất, chỉ khác tham số truyền vào).
 */
function setupGauge(opts) {
    var ARC_LENGTH = 314; // Chu vi cung tròn = π * bán kính(100), khớp với stroke-dasharray trong CSS
    var audioCtx, oscillator, gainNode;

    opts.btn.addEventListener('click', function () {
        opts.btn.disabled = true;
        var durationMs = 2200;
        var startTime = performance.now();

        if (opts.withEngineSound) {
            startEngineSound();
        }

        function frame(now) {
            var elapsed = now - startTime;
            var t = Math.min(elapsed / durationMs, 1);
            // easeOutCubic: kim tăng tốc nhanh lúc đầu, chậm dần khi gần chạm giá trị đích —
            // mô phỏng cảm giác kim đồng hồ thật, không di chuyển đều đều máy móc.
            var eased = 1 - Math.pow(1 - t, 3);
            var currentValue = eased * opts.target;
            var percent = Math.min(currentValue / opts.max, 1);

            var angle = (percent * 180) - 90; // -90deg (trái) -> +90deg (phải)
            opts.needle.setAttribute('transform', 'rotate(' + angle + ' 120 120)');
            opts.arc.style.strokeDashoffset = ARC_LENGTH * (1 - percent);
            opts.valueEl.textContent = Math.round(currentValue);

            if (opts.withEngineSound && oscillator) {
                oscillator.frequency.setValueAtTime(80 + percent * 500, audioCtx.currentTime);
            }

            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                opts.btn.disabled = false;
                if (opts.withEngineSound) stopEngineSound();
            }
        }
        requestAnimationFrame(frame);

        function startEngineSound() {
            try {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                oscillator = audioCtx.createOscillator();
                gainNode = audioCtx.createGain();
                oscillator.type = 'sawtooth';
                oscillator.frequency.value = 80;
                gainNode.gain.value = 0.04; // âm lượng nhỏ, tránh giật mình người dùng
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                oscillator.start();
            } catch (e) {
                // Trình duyệt cũ không hỗ trợ Web Audio API — bỏ qua âm thanh,
                // animation kim đồng hồ vẫn chạy bình thường.
            }
        }

        function stopEngineSound() {
            if (oscillator && gainNode) {
                gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.3);
                oscillator.stop(audioCtx.currentTime + 0.3);
            }
        }
    });
}

/**
 * setupAccelTester — Bài test tăng tốc 0-100km/h.
 * QUAN TRỌNG: animation chạy đúng bằng thời gian thật (duration giây)
 * lấy từ database, không phải con số giả định cố định — xe accel 3.4s
 * sẽ chạy animation trong đúng 3.4 giây thực tế.
 */
function setupAccelTester(opts) {
    opts.btn.addEventListener('click', function () {
        opts.btn.disabled = true;
        opts.resultEl.classList.remove('show');
        opts.car.style.left = '0%';
        opts.timerEl.textContent = '0.0s';

        var durationMs = opts.duration * 1000;
        var startTime = performance.now();

        function frame(now) {
            var elapsed = now - startTime;
            var t = Math.min(elapsed / durationMs, 1);
            opts.car.style.left = (t * 96) + '%'; // 96% để icon xe không tràn ra ngoài khung
            opts.timerEl.textContent = (t * opts.duration).toFixed(1) + 's';

            if (t < 1) {
                requestAnimationFrame(frame);
            } else {
                opts.timerEl.textContent = opts.duration.toFixed(1) + 's';
                opts.resultEl.textContent = '✓ Kết quả: 0-100 km/h trong ' + opts.duration.toFixed(1) + ' giây';
                opts.resultEl.classList.add('show');
                opts.btn.disabled = false;
            }
        }
        requestAnimationFrame(frame);
    });
}
