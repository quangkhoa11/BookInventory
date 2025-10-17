<?php
$obj = new database();

// Lấy IDDonBan từ URL và bảo vệ
$IDDonBan = $_GET['IDDonBan'] ?? '';
if (!$IDDonBan) {
    echo "<div class='alert alert-danger'>Không có đơn hàng được chọn!</div>";
    exit();
}

// Lấy thông tin đơn hàng
$donhangArr = $obj->xuatdulieu("SELECT * FROM donban WHERE IDDonBan='$IDDonBan'");
if (empty($donhangArr)) {
    echo "<div class='alert alert-danger'>Đơn hàng không tồn tại!</div>";
    exit();
}
$donhang = $donhangArr[0];

// Lấy thông tin khách hàng
$khachhangArr = $obj->xuatdulieu("SELECT * FROM khachhang WHERE IDKH='".$donhang['IDKH']."'");
$khachhang = !empty($khachhangArr) ? $khachhangArr[0] : null;

// Lấy thông tin thanh toán
$thanhtoanArr = $obj->xuatdulieu("SELECT * FROM thanhtoankh WHERE IDDonBan='$IDDonBan' ORDER BY NgayThanhToan DESC");
$thanhtoan = !empty($thanhtoanArr) ? $thanhtoanArr[0] : null;

// Lấy chi tiết sản phẩm trong đơn
$chitiet = $obj->xuatdulieu("
    SELECT ctdb.IDDauSach, ds.TenDauSach, ctdb.SoLuong
    FROM chitietdonban ctdb
    JOIN dausach ds ON ds.IDDauSach = ctdb.IDDauSach
    WHERE ctdb.IDDonBan='$IDDonBan'
");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📄 Chi tiết đơn hàng #<?= htmlspecialchars($IDDonBan) ?></h2>

    <!-- Thông tin giao hàng (donban) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white fw-bold">Thông tin giao hàng</div>
        <div class="card-body">
            <p><strong>Mã đơn:</strong> <?= htmlspecialchars($donhang['IDDonBan']) ?></p>
            <p><strong>Người nhận:</strong> <?= htmlspecialchars($donhang['TenNguoiNhan']) ?></p>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($donhang['SDT']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donhang['DiaChi']) ?></p>
            <p><strong>Ngày đặt:</strong> <?= !empty($donhang['NgayDat']) ? date('d/m/Y', strtotime($donhang['NgayDat'])) : '-' ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($donhang['TongTien'], 0, ',', '.') ?> VNĐ</p>
            <p><strong>Trạng thái:</strong> <?= htmlspecialchars($donhang['TrangThai']) ?></p>
        </div>
    </div>

    <!-- Thông tin khách hàng -->
    <?php if($khachhang): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">Thông tin khách hàng</div>
        <div class="card-body">
            <p><strong>Mã KH:</strong> <?= htmlspecialchars($khachhang['IDKH']) ?></p>
            <p><strong>Tên KH:</strong> <?= htmlspecialchars($khachhang['TenKH']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($khachhang['DiaChi']) ?></p>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($khachhang['SDT']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($khachhang['Email']) ?></p>
            <p><strong>Loại KH:</strong> <?= htmlspecialchars($khachhang['LoaiKH']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Thông tin thanh toán -->
    <?php if($thanhtoan): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-bold">Thông tin thanh toán</div>
        <div class="card-body">
            <p><strong>Mã thanh toán:</strong> <?= htmlspecialchars($thanhtoan['IDThanhToan']) ?></p>
            <p><strong>Ngày thanh toán:</strong> <?= !empty($thanhtoan['NgayThanhToan']) ? date('d/m/Y H:i', strtotime($thanhtoan['NgayThanhToan'])) : '-' ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($thanhtoan['TongTien'], 0, ',', '.') ?> VNĐ</p>
            <p><strong>Phương thức:</strong> <?= htmlspecialchars($thanhtoan['PhuongThuc']) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($thanhtoan['GhiChu']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi tiết sản phẩm -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white fw-bold">Chi tiết sản phẩm</div>
        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Mã đầu sách</th>
                        <th>Tên đầu sách</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($chitiet)): ?>
                        <?php foreach($chitiet as $ct): ?>
                            <tr>
                                <td><?= htmlspecialchars($ct['IDDauSach']) ?></td>
                                <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
                                <td><?= intval($ct['SoLuong']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Không có sản phẩm trong đơn này.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="index.php?page=xemdonhang_nv" class="btn btn-secondary">Quay lại</a>
    </div>
</div>

<style>
.card { border-radius: 10px; }
.table td, .table th { vertical-align: middle; }
</style>
