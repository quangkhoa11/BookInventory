<?php
$obj = new database();

// Role và IDNV
$role = $_SESSION['role'] ?? 'kho';
$IDNV = $_SESSION['IDNV'] ?? 1;

// Hàm sinh IDPhieuXuat
function generateIDPhieuXuat($obj) {
    $last = $obj->xuatdulieu("SELECT IDPhieuXuat FROM phieuxuat ORDER BY IDPhieuXuat DESC LIMIT 1");
    if (!empty($last)) {
        $num = intval(substr($last[0]['IDPhieuXuat'], 3)) + 1;
    } else {
        $num = 1;
    }
    return 'PXK' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// Nếu đã chọn đơn bán để xem chi tiết
$chiTietDon = [];
$IDDonBanChon = $_POST['IDDonBanChon'] ?? '';
if ($IDDonBanChon) {
    $chiTietDon = $obj->xuatdulieu("
        SELECT 
            ctdb.IDDauSach, 
            ds.TenDauSach, 
            ctdb.SoLuong AS SoLuongDat, 
            ctdb.DonGia,
            (ctdb.SoLuong - IFNULL(SUM(ctx.SoLuongThucTe),0)) AS SoLuongConLai
        FROM chitietdonban ctdb
        JOIN dausach ds ON ds.IDDauSach = ctdb.IDDauSach
        LEFT JOIN chitietxuatkho ctx ON ctx.IDDauSach = ctdb.IDDauSach 
            AND ctx.IDPhieuXuat IN (SELECT IDPhieuXuat FROM phieuxuat WHERE IDDonBan='$IDDonBanChon')
        WHERE ctdb.IDDonBan='$IDDonBanChon'
        GROUP BY ctdb.IDDauSach
    ");
}
PX00
// Tạo phiếu xuất
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taoPhieuXuat'])) {
    $IDDonBan = $_POST['IDDonBan'];
    $LoaiXuat = $_POST['LoaiXuat'];
    $GhiChu = $_POST['GhiChu'] ?? '';
    $NgayXuat = date('Y-m-d H:i:s');
    $TrangThai = $role == 'thukho' ? 'Đã duyệt' : 'Đang chờ duyệt';
    $IDPhieuXuat = generateIDPhieuXuat($obj);

    // Thêm phiếu xuất
    $obj->xuatdulieu("INSERT INTO phieuxuat 
        (IDPhieuXuat, IDDonBan, IDNV, NgayXuat, LoaiXuat, GhiChu, TrangThai)
        VALUES ('$IDPhieuXuat', '$IDDonBan', $IDNV, '$NgayXuat', '$LoaiXuat', '$GhiChu', '$TrangThai')");

    $slThucTeArr = $_POST['SoLuongThucTe'] ?? [];

    foreach($slThucTeArr as $IDDauSach => $SoLuongThucTe) {
        $IDDauSach = intval($IDDauSach);
        $SoLuongThucTe = intval($SoLuongThucTe);
        if ($SoLuongThucTe <= 0) continue;

        $DonGia = $obj->xuatdulieu("SELECT DonGia FROM chitietdonban WHERE IDDonBan='$IDDonBan' AND IDDauSach=$IDDauSach")[0]['DonGia'] ?? 0;
        $ThanhTien = $SoLuongThucTe * $DonGia;

        // Lấy số lượng còn lại chưa xuất
        $SoLuongYeuCau = $obj->xuatdulieu("
            SELECT ctdb.SoLuong - IFNULL(SUM(ctx.SoLuongThucTe),0) AS SoLuongConLai
            FROM chitietdonban ctdb
            LEFT JOIN chitietxuatkho ctx ON ctx.IDDauSach = ctdb.IDDauSach 
                AND ctx.IDPhieuXuat IN (SELECT IDPhieuXuat FROM phieuxuat WHERE IDDonBan='$IDDonBan')
            WHERE ctdb.IDDonBan='$IDDonBan' AND ctdb.IDDauSach=$IDDauSach
            GROUP BY ctdb.IDDauSach
        ")[0]['SoLuongConLai'] ?? $SoLuongThucTe;

        $obj->xuatdulieu("INSERT INTO chitietxuatkho
            (IDPhieuXuat, IDDauSach, SoLuongYeuCau, SoLuongThucTe, DonGia, ThanhTien)
            VALUES ('$IDPhieuXuat', $IDDauSach, $SoLuongYeuCau, $SoLuongThucTe, $DonGia, $ThanhTien)");
    }

    $_SESSION['msg'] = "Tạo phiếu xuất thành công: $IDPhieuXuat";
    header("Location: index.php?page=phieuxuat");
    exit();
}

// Danh sách đơn bán chưa tạo phiếu xuất hoặc còn số lượng chưa xuất
$donHangChuaXuat = $obj->xuatdulieu("
    SELECT db.IDDonBan, db.TenNguoiNhan, db.NgayDat,
        (SELECT SUM(ctdb.SoLuong) - IFNULL(SUM(ctx.SoLuongThucTe),0) 
         FROM chitietdonban ctdb 
         LEFT JOIN chitietxuatkho ctx ON ctx.IDDauSach=ctdb.IDDauSach 
             AND ctx.IDPhieuXuat IN (SELECT IDPhieuXuat FROM phieuxuat WHERE IDDonBan=db.IDDonBan)
         WHERE ctdb.IDDonBan=db.IDDonBan) AS SoLuongConLai
    FROM donban db
    HAVING SoLuongConLai>0
    ORDER BY db.NgayDat DESC
");

// Danh sách phiếu xuất
$phieuXuatList = $obj->xuatdulieu("
    SELECT px.IDPhieuXuat, px.IDDonBan, px.IDNV, px.NgayXuat, px.LoaiXuat, px.GhiChu, px.TrangThai, db.TenNguoiNhan
    FROM phieuxuat px
    LEFT JOIN donban db ON db.IDDonBan = px.IDDonBan
    ORDER BY px.NgayXuat DESC
");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📦 Phiếu Xuất Kho</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <!-- Chọn đơn bán -->
    <form method="post" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label>Chọn đơn bán:</label>
                <select name="IDDonBanChon" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Chọn đơn bán --</option>
                    <?php foreach($donHangChuaXuat as $dh): ?>
                        <option value="<?= $dh['IDDonBan'] ?>" <?= $IDDonBanChon==$dh['IDDonBan']?'selected':'' ?>>
                            <?= $dh['IDDonBan'] ?> - <?= htmlspecialchars($dh['TenNguoiNhan']) ?> (<?= date('d/m/Y', strtotime($dh['NgayDat'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
    

    <?php if(!empty($chiTietDon)): ?>
    <!-- Chi tiết đơn bán -->
    <form method="post">
        <input type="hidden" name="IDDonBan" value="<?= $IDDonBanChon ?>">
        <div class="row g-2 align-items-end mb-3">
            <div class="col-md-3">
                <label>Loại xuất:</label>
                <select name="LoaiXuat" class="form-select">
                    <option value="Xuất bán hàng">Xuất bán hàng</option>
                    <option value="Xuất trả hàng">Xuất trả hàng</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Ghi chú:</label>
                <input type="text" name="GhiChu" class="form-control">
            </div>
        </div>

        <table class="table table-bordered text-center">
            <thead>
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
                <?php foreach($chiTietDon as $ct): ?>
                <tr>
                    <td><?= $ct['IDDauSach'] ?></td>
                    <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
                    <td><?= $ct['SoLuongConLai'] ?></td>
                    <td><input type="number" name="SoLuongThucTe[<?= $ct['IDDauSach'] ?>]" 
                               value="<?= $ct['SoLuongConLai'] ?>" min="0" max="<?= $ct['SoLuongConLai'] ?>" 
                               class="form-control form-control-sm"></td>
                    <td><?= $ct['DonGia'] ?></td>
                    <td><?= $ct['SoLuongConLai'] * $ct['DonGia'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-end mt-2">
            <button type="submit" name="taoPhieuXuat" class="btn btn-primary">Tạo phiếu xuất</button>
        </div>
    </form>
    <?php endif; ?>
<div class="text-end mt-3" style="float: right;">
    <?php if($role === 'kho'): ?>
        <a href="index.php?page=xemdonhang_nv" class="btn btn-secondary">🔙 Quay lại</a>
    <?php elseif($role === 'thukho'): ?>
        <a href="index.php?page=thukho" class="btn btn-secondary">🔙 Quay lại</a>
    <?php else: ?>
        <a href="index.php" class="btn btn-secondary">🔙 Quay lại</a>
    <?php endif; ?>
</div>
    <!-- Danh sách phiếu xuất có sẵn -->
    <!-- Danh sách phiếu xuất có sẵn -->
<h4 class="mt-5 mb-3">📋 Danh sách phiếu xuất</h4>
<table class="table table-striped table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>ID Phiếu</th>
            <th>ID Đơn bán</th>
            <th>Người nhận</th>
            <th>Loại xuất</th>
            <th>Ngày xuất</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            <th>Xem</th> <!-- Thêm cột xem -->
        </tr>
    </thead>
    <tbody>
        <?php foreach($phieuXuatList as $px): ?>
        <tr>
            <td><?= $px['IDPhieuXuat'] ?></td>
            <td><?= $px['IDDonBan'] ?></td>
            <td><?= htmlspecialchars($px['TenNguoiNhan']) ?></td>
            <td><?= $px['LoaiXuat'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($px['NgayXuat'])) ?></td>
            <td><?= $px['TrangThai'] ?></td>
            <td><?= htmlspecialchars($px['GhiChu']) ?></td>
            <td>
                <a href="index.php?page=xemphieuxuat&IDPhieuXuat=<?= $px['IDPhieuXuat'] ?>" 
                   class="btn btn-sm btn-info">Xem</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>



<style>
.table td, .table th { vertical-align: middle; }
.btn-primary { border-radius: 6px; padding: 8px 16px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const qtyInput = row.querySelector('input[name^="SoLuongThucTe"]');
        if (!qtyInput) return;

        const donGia = parseFloat(row.cells[4].textContent) || 0;
        const thanhTienCell = row.cells[5];

        qtyInput.addEventListener('input', function() {
            let qty = parseInt(this.value) || 0;
            if (qty < 0) qty = 0;
            const maxQty = parseInt(this.max) || qty;
            if (qty > maxQty) qty = maxQty;
            this.value = qty;
            thanhTienCell.textContent = (qty * donGia).toLocaleString();
        });
    });
});
</script>
