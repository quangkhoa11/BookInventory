<?php
$obj = new database();

// Lấy IDPhieuXuat từ GET
$IDPhieuXuat = $_GET['IDPhieuXuat'] ?? '';
if (!$IDPhieuXuat) {
    echo "<div class='alert alert-danger'>Không tìm thấy phiếu xuất!</div>";
    return;
}

// Lấy thông tin phiếu xuất
$phieuXuat = $obj->xuatdulieu("
    SELECT px.IDPhieuXuat, px.IDDonBan, px.IDNV, px.NgayXuat, px.LoaiXuat, px.GhiChu, px.TrangThai,
           db.TenNguoiNhan
    FROM phieuxuat px
    LEFT JOIN donban db ON db.IDDonBan = px.IDDonBan
    WHERE px.IDPhieuXuat='$IDPhieuXuat'
")[0] ?? null;

if (!$phieuXuat) {
    echo "<div class='alert alert-danger'>Phiếu xuất không tồn tại!</div>";
    return;
}

// Lấy chi tiết sách trong phiếu xuất
$chiTiet = $obj->xuatdulieu("
    SELECT ctx.IDDauSach, ds.TenDauSach, ctx.SoLuongYeuCau, ctx.SoLuongThucTe, ctx.DonGia, ctx.ThanhTien
    FROM chitietxuatkho ctx
    JOIN dausach ds ON ds.IDDauSach = ctx.IDDauSach
    WHERE ctx.IDPhieuXuat='$IDPhieuXuat'
");
?>

<div class="container my-5">
    <div class="card shadow-sm p-4">
        <h2 class="text-primary mb-4">📦 Chi tiết Phiếu Xuất: <?= htmlspecialchars($phieuXuat['IDPhieuXuat']) ?></h2>

        <div class="row mb-3">
            <div class="col-md-4"><strong>ID Đơn bán:</strong> <?= htmlspecialchars($phieuXuat['IDDonBan']) ?></div>
            <div class="col-md-4"><strong>Người nhận:</strong> <?= htmlspecialchars($phieuXuat['TenNguoiNhan']) ?></div>
            <div class="col-md-4"><strong>Ngày xuất:</strong> <?= date('d/m/Y H:i', strtotime($phieuXuat['NgayXuat'])) ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4"><strong>Loại xuất:</strong> <?= htmlspecialchars($phieuXuat['LoaiXuat']) ?></div>
            <div class="col-md-4"><strong>Trạng thái:</strong> <?= htmlspecialchars($phieuXuat['TrangThai']) ?></div>
            <div class="col-md-4"><strong>Ghi chú:</strong> <?= htmlspecialchars($phieuXuat['GhiChu']) ?></div>
        </div>

        <h4 class="mt-4 mb-3">📋 Chi tiết sách</h4>
        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Đầu sách</th>
                    <th>Tên sách</th>
                    <th>Số lượng yêu cầu</th>
                    <th>Số lượng thực tế</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $tongTien = 0;
                foreach($chiTiet as $ct): 
                    $tongTien += $ct['ThanhTien'];
                ?>
                <tr>
                    <td><?= $ct['IDDauSach'] ?></td>
                    <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
                    <td><?= $ct['SoLuongYeuCau'] ?></td>
                    <td><?= $ct['SoLuongThucTe'] ?></td>
                    <td><?= number_format($ct['DonGia']) ?></td>
                    <td><?= number_format($ct['ThanhTien']) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-secondary fw-bold">
                    <td colspan="5">Tổng tiền</td>
                    <td><?= number_format($tongTien) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <a href="index.php?page=phieuxuat" class="btn btn-secondary">🔙 Quay lại</a>
        </div>
    </div>
</div>

<style>
.card { border-radius: 10px; }
.table td, .table th { vertical-align: middle; }
</style>
