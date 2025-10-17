<?php
$obj = new database();

// Lấy danh sách phiếu xuất đang chờ duyệt
$phieuxuat = $obj->xuatdulieu("
    SELECT px.IDPhieuXuat, px.IDDonBan, px.IDNV, px.NgayXuat, px.LoaiXuat, px.GhiChu, px.TrangThai, db.TenNguoiNhan
    FROM phieuxuat px
    LEFT JOIN donban db ON db.IDDonBan = px.IDDonBan
    WHERE px.TrangThai='Đang chờ duyệt'
    ORDER BY px.NgayXuat DESC
");

// Xử lý duyệt / từ chối phiếu xuất
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duyetPhieu'])) {
    $IDPhieuXuat = $_POST['IDPhieuXuat'];
    $TrangThaiMoi = $_POST['TrangThaiMoi']; // 'Đã duyệt' hoặc 'Từ chối'
    $GhiChu = $_POST['GhiChuDuyet'] ?? '';

    // Cập nhật trạng thái phiếu
    $obj->xuatdulieu("
        UPDATE phieuxuat 
        SET TrangThai='$TrangThaiMoi', GhiChu=CONCAT(IFNULL(GhiChu,''), ' ', '$GhiChu') 
        WHERE IDPhieuXuat='$IDPhieuXuat'
    ");

    // Nếu duyệt, giảm tồn kho
    if ($TrangThaiMoi === 'Đã duyệt') {
    // 1. Giảm tồn kho sách
    $chiTiet = $obj->xuatdulieu("
        SELECT IDDauSach, SoLuongThucTe 
        FROM chitietxuatkho 
        WHERE IDPhieuXuat='$IDPhieuXuat'
    ");
    foreach ($chiTiet as $ct) {
        $IDDauSach = $ct['IDDauSach'];
        $soLuongThucTe = $ct['SoLuongThucTe'];
        $obj->xuatdulieu("
            UPDATE sach 
            SET SoLuong = SoLuong - $soLuongThucTe, NgayCapNhat = NOW() 
            WHERE IDDauSach='$IDDauSach'
        ");
    }

    // 2. Cập nhật trạng thái đơn bán nếu phiếu xuất là "Xuất bán hàng"
    $donbanID = $obj->xuatdulieu("SELECT IDDonBan FROM phieuxuat WHERE IDPhieuXuat='$IDPhieuXuat' AND LoaiXuat='Xuất bán hàng'");
    if (!empty($donbanID)) {
        $IDDonBan = $donbanID[0]['IDDonBan'];
        $obj->xuatdulieu("
            UPDATE donban 
            SET TrangThai='Đã xuất kho' 
            WHERE IDDonBan='$IDDonBan'
        ");
    }
}


    $_SESSION['msg'] = "Phiếu xuất $IDPhieuXuat đã được cập nhật: $TrangThaiMoi";
    header("Location: index.php?page=qlxuatkho");
    exit();
}
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">🗂 Quản lý phiếu xuất kho</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3 text-end">
        <a href="index.php?page=taophieuxuat_tk" class="btn btn-success fw-bold">➕ Tạo phiếu xuất mới</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-bordered text-center align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID Phiếu</th>
                        <th>ID Đơn bán</th>
                        <th>Người nhận</th>
                        <th>Loại xuất</th>
                        <th>Ngày xuất</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
<?php if(!empty($phieuxuat)): ?>
    <?php foreach($phieuxuat as $px): ?>
    <tr>
        <td><?= $px['IDPhieuXuat'] ?></td>
        <td><?= $px['IDDonBan'] ?></td>
        <td><?= htmlspecialchars($px['TenNguoiNhan']) ?></td>
        <td><?= $px['LoaiXuat'] ?></td>
        <td><?= date('d/m/Y H:i', strtotime($px['NgayXuat'])) ?></td>
        <td><?= $px['TrangThai'] ?></td>
        <td><?= htmlspecialchars($px['GhiChu']) ?></td>
        <td class="d-flex justify-content-center gap-1">
            <a href="index.php?page=xemphieuxuattt&IDPhieuXuat=<?= $px['IDPhieuXuat'] ?>" class="btn btn-sm btn-info">Xem</a>
            <form method="post" class="d-inline">
                <input type="hidden" name="IDPhieuXuat" value="<?= $px['IDPhieuXuat'] ?>">
                <input type="hidden" name="TrangThaiMoi" value="Đã duyệt">
                <button type="submit" name="duyetPhieu" class="btn btn-sm btn-success">Duyệt</button>
            </form>
            <form method="post" class="d-inline">
                <input type="hidden" name="IDPhieuXuat" value="<?= $px['IDPhieuXuat'] ?>">
                <input type="hidden" name="TrangThaiMoi" value="Từ chối">
                <button type="submit" name="duyetPhieu" class="btn btn-sm btn-danger">Từ chối</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-secondary py-4">
            Không có phiếu xuất nào đang chờ duyệt.<br>
            Bạn có thể <a href="index.php?page=taophieuxuat_tk">tạo phiếu xuất mới</a> để bắt đầu.
        </td>
    </tr>
<?php endif; ?>
</tbody>

            </table>
        </div>
    </div>
</div>

<style>
.table td, .table th { vertical-align: middle; }
.btn { border-radius: 5px; padding: 4px 10px; font-size: 0.875rem; }
.card { border-radius: 10px; overflow: hidden; }
.alert { font-size: 0.95rem; }
</style>

