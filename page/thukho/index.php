<?php
session_start();
?>

<div class="container my-5">
    <h2 class="text-success fw-bold text-center mb-5">🧰 Trang Thủ kho</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">

        <!-- Quản lý đơn nhập -->
        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📝</div>
                        <h4 class="fw-bold">Đơn nhập hàng</h4>
                        <p class="text-muted">Xem và cập nhật trạng thái đơn nhập từ nhà cung cấp.</p>
                    </div>
                    <a href="index.php?page=xemdonhang_tk" class="btn btn-success btn-lg fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>

        <!-- Tồn kho -->
        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📦</div>
                        <h4 class="fw-bold">Tồn kho</h4>
                        <p class="text-muted">Xem số lượng tồn kho thực tế của từng đầu sách.</p>
                    </div>
                    <a href="index.php?page=tonkho_tk" class="btn btn-info btn-lg text-white fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>

        <!-- Kiểm kê -->
        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📊</div>
                        <h4 class="fw-bold">Kiểm kê kho</h4>
                        <p class="text-muted">Cập nhật số lượng thực tế và tình trạng kho định kỳ.</p>
                    </div>
                    <a href="index.php?page=kiemke_tk" class="btn btn-warning btn-lg fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>

        <!-- Quản phiếu -->
        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📊</div>
                        <h4 class="fw-bold">Quản lý xuất kho</h4>
                        <p class="text-muted">Quản lý duyệt phiếu xuất kho tạo phiếu từ đơn bán.</p>
                    </div>
                    <a href="index.php?page=qlxuatkho" class="btn btn-warning btn-lg fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📊</div>
                        <h4 class="fw-bold">Quản lý nhập kho</h4>
                        <p class="text-muted">Quản lý duyệt phiếu nhập kho tạo phiếu.</p>
                    </div>
                    <a href="index.php?page=qlnhapkho" class="btn btn-warning btn-lg fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.dashboard-card {
    border-radius: 20px;
    transition: all 0.3s;
    padding: 20px;
}
.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}
.icon-card { font-size: 3rem; }
.card p { font-size: 0.95rem; }
.btn { border-radius: 50px; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
