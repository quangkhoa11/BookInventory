<?php

// Kiểm tra đăng nhập và loại khách hàng
if (!isset($_SESSION['idkh']) || ($_SESSION['loaikh'] ?? '') !== 'Tổ chức') {
    // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập hoặc không phải tổ chức
    header("Location: index.php?page=dangnhap");
    exit();
}
?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary mb-3">🏢 Trang Chủ Khách Hàng Tổ Chức</h1>
        <p class="text-muted">Chào mừng <strong><?= htmlspecialchars($_SESSION['tenkh']) ?></strong>! Quản lý đơn hàng số lượng lớn của bạn tại đây.</p>
    </div>

    <!-- Các tính năng chính -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title fw-bold">Đặt hàng số lượng lớn</h5>
                    <p class="card-text">Chọn sách và số lượng lớn với giá ưu đãi cho tổ chức.</p>
                    <a href="index.php?page=danhmucsach" class="btn btn-primary">Đặt ngay</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-invoice fa-3x mb-3 text-success"></i>
                    <h5 class="card-title fw-bold">Quản lý đơn hàng</h5>
                    <p class="card-text">Xem tất cả đơn hàng, trạng thái và lịch sử giao dịch của tổ chức.</p>
                    <a href="index.php?page=donhangtochuc" class="btn btn-success">Xem đơn hàng</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title fw-bold">Hợp tác & Ưu đãi</h5>
                    <p class="card-text">Xem các chính sách, ưu đãi đặc biệt cho khách hàng tổ chức.</p>
                    <a href="index.php?page=uutien" class="btn btn-warning">Tìm hiểu thêm</a>
                </div>
            </div>
        </div>
    </div>
</main>
