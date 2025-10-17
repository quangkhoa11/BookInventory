

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-5">👷‍♂️ Trang Quản lý kinh doanh</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">

        <!-- Đơn hàng -->
        <div class="col-md-4 col-sm-6">
            <div class="dashboard-card card text-center shadow">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-3">
                        <div class="icon-card mb-3">📄</div>
                        <h4 class="fw-bold">Đặt hàng nhà cung cấp</h4>
                        <p class="text-muted">Đặt hàng từ nhà cung cấp</p>
                    </div>
                    <a href="index.php?page=dathangncc" class="btn btn-primary btn-lg fw-bold">Vào chức năng</a>
                </div>
            </div>
        </div>



    </div>
</div>

<style>
body { background: #f1f3f6; }
.dashboard-card {
    border-radius: 20px;
    transition: all 0.3s;
    padding: 20px;
}
.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}
.icon-card {
    font-size: 3rem;
}
.card p {
    font-size: 0.95rem;
}
.btn {
    border-radius: 50px;
}
</style>
