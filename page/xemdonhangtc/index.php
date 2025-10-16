<?php
// Kiểm tra đăng nhập và loại khách hàng
if (!isset($_SESSION['idkh']) || ($_SESSION['loaikh'] ?? '') !== 'Tổ chức') {
    header("Location: index.php?page=dangnhap");
    exit();
}

$obj = new database();
$IDKH = $_SESSION['idkh'];
$IDDonBan = $_GET['id'] ?? '';

if ($IDDonBan === '') {
    echo "<div class='alert alert-danger'>Mã đơn hàng không hợp lệ.</div>";
    echo "<a href='index.php?page=donhangtochuc' class='btn btn-secondary mt-3'>Quay lại</a>";
    exit;
}

// Kiểm tra đơn hàng có thuộc tổ chức hiện tại không
$donban = $obj->xuatdulieu("SELECT * FROM donban WHERE IDDonBan='$IDDonBan' AND IDKH='$IDKH'");
if (!$donban) {
    echo "<div class='alert alert-danger'>Đơn hàng không tồn tại hoặc không thuộc tổ chức của bạn.</div>";
    echo "<a href='index.php?page=donhangtochuc' class='btn btn-secondary mt-3'>Quay lại</a>";
    exit;
}
$donban = $donban[0];

// Lấy chi tiết sản phẩm trong đơn
$chitiet = $obj->xuatdulieu("
    SELECT c.*, ds.TenDauSach, ds.TacGia, ds.Gia
    FROM chitietdonban c
    JOIN dausach ds ON c.IDDauSach = ds.IDDauSach
    WHERE c.IDDonBan = '$IDDonBan'
");
?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">
            <i class="fas fa-file-invoice-dollar me-2"></i> Chi Tiết Đơn Hàng
        </h2>
        <p class="text-muted">Mã đơn: <strong><?= htmlspecialchars($donban['IDDonBan']) ?></strong></p>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <h5 class="fw-bold text-secondary mb-3">📦 Thông tin đơn hàng</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($donban['TenNguoiNhan']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donban['DiaChi']) ?></p>
                    <p><strong>SĐT:</strong> <?= htmlspecialchars($donban['SDT']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($donban['NgayDat'])) ?></p>
                    <p><strong>Tổng tiền:</strong> <span class="text-danger fw-bold"><?= number_format($donban['TongTien'], 0, ",", ".") ?>₫</span></p>
                    <p><strong>Trạng thái:</strong>
                        <?php
                            $status = $donban['TrangThai'];
                            $badge = "bg-secondary";
                            if ($status === "Đang xử lý") $badge = "bg-warning text-dark";
                            elseif ($status === "Đang giao") $badge = "bg-info text-dark";
                            elseif ($status === "Đã giao") $badge = "bg-success";
                            elseif ($status === "Đã hủy") $badge = "bg-danger";
                        ?>
                        <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold text-secondary mb-3">📚 Danh sách sản phẩm</h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tên đầu sách</th>
                            <th>Tác giả</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($chitiet && count($chitiet) > 0): ?>
                            <?php foreach ($chitiet as $i => $ct): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
                                    <td><?= htmlspecialchars($ct['TacGia']) ?></td>
                                    <td><?= $ct['SoLuong'] ?></td>
                                    <td><?= number_format($ct['DonGia'], 0, ",", ".") ?>₫</td>
                                    <td class="text-danger fw-semibold"><?= number_format($ct['SoLuong'] * $ct['DonGia'], 0, ",", ".") ?>₫</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">Không có sản phẩm trong đơn hàng này.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="index.php?page=donhangtochuc" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại Danh sách đơn hàng
        </a>
    </div>
</main>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
