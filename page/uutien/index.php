<?php
// Kiểm tra đăng nhập
if (!isset($_SESSION['idkh']) || ($_SESSION['loaikh'] ?? '') !== 'Tổ chức') {
    header("Location: index.php?page=dangnhap");
    exit();
}
?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-warning">🤝 Chính Sách Hợp Tác & Ưu Đãi Cho Tổ Chức</h1>
        <p class="text-muted fs-5">Cùng đồng hành để phát triển bền vững và mang tri thức đến mọi người.</p>
    </div>

    <!-- Ưu đãi chính -->
    <section class="mb-5">
        <h3 class="fw-bold text-primary mb-4 text-center">🎁 Các Ưu Đãi Dành Cho Đối Tác Tổ Chức</h3>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-center p-4">
                    <i class="fas fa-percent fa-3x text-success mb-3"></i>
                    <h5 class="fw-bold">Chiết Khấu Số Lượng Lớn</h5>
                    <p class="text-muted">
                        Giảm giá đặc biệt cho các đơn hàng có số lượng từ <strong>80 cuốn trở lên</strong>.
                        Càng mua nhiều, ưu đãi càng cao.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-center p-4">
                    <i class="fas fa-truck-fast fa-3x text-primary mb-3"></i>
                    <h5 class="fw-bold">Giao Hàng Nhanh & Miễn Phí</h5>
                    <p class="text-muted">
                        Miễn phí vận chuyển cho các đơn hàng trên <strong>5.000.000 VNĐ</strong> trong nội thành.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-center p-4">
                    <i class="fas fa-gift fa-3x text-warning mb-3"></i>
                    <h5 class="fw-bold">Chương Trình Tri Ân</h5>
                    <p class="text-muted">
                        Tặng quà hoặc voucher đặc biệt cho các đối tác thân thiết khi đạt doanh số hàng tháng.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cơ hội hợp tác -->
    <section class="mb-5">
        <h3 class="fw-bold text-primary mb-4 text-center">🏫 Cơ Hội Hợp Tác Dành Cho Tổ Chức</h3>
        <div class="card border-0 shadow-sm p-4">
            <ul class="list-unstyled fs-5">
                <li class="mb-3"><i class="fas fa-building text-primary me-2"></i>
                    Hợp tác cung cấp sách cho <strong>trường học, thư viện, doanh nghiệp</strong>.
                </li>
                <li class="mb-3"><i class="fas fa-handshake text-success me-2"></i>
                    Ký kết hợp đồng dài hạn để nhận chiết khấu ưu đãi và dịch vụ chăm sóc riêng biệt.
                </li>
                <li class="mb-3"><i class="fas fa-chart-line text-warning me-2"></i>
                    Cùng phát triển các chương trình <strong>khuyến học, tặng sách</strong> vì cộng đồng.
                </li>
            </ul>
        </div>
    </section>

    <!-- Liên hệ hợp tác -->
    <section class="text-center">
        <h3 class="fw-bold text-primary mb-4">📞 Liên Hệ Hợp Tác Ngay</h3>
        <p class="text-muted fs-5 mb-4">
            Chúng tôi luôn sẵn sàng đồng hành cùng tổ chức của bạn.  
            Vui lòng liên hệ qua các kênh sau để nhận tư vấn chi tiết.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="mailto:hotro@thakho.vn" class="btn btn-outline-primary">
                <i class="fas fa-envelope me-2"></i>Email: hotro@thakho.vn
            </a>
            <a href="tel:0123456789" class="btn btn-outline-success">
                <i class="fas fa-phone-alt me-2"></i>0123 456 789
            </a>
        </div>
    </section>
</main>
