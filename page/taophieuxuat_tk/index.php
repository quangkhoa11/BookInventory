<?php
session_start();
$obj = new database();

// Role và IDNV
$IDNV = $_SESSION['IDNV'] ?? 1;

// Lấy danh sách đơn bán đang ở trạng thái "Đang soạn hàng"
$donhangs = $obj->xuatdulieu("
    SELECT db.IDDonBan, db.IDKH, db.TenNguoiNhan, db.SDT, db.DiaChi, db.NgayDat, db.TongTien, db.TrangThai
    FROM donban db
    WHERE db.TrangThai='Đang soạn hàng'
    ORDER BY db.NgayDat DESC
");

// Hàm sinh IDPhieuXuat kiểu PXK001
function generateIDPhieuXuat($obj) {
    $last = $obj->xuatdulieu("SELECT IDPhieuXuat FROM phieuxuat ORDER BY IDPhieuXuat DESC LIMIT 1");
    if (!empty($last)) {
        $num = intval(substr($last[0]['IDPhieuXuat'], 3)) + 1;
    } else {
        $num = 1;
    }
    return 'PXK' . str_pad($num, 3, '0', STR_PAD_LEFT);
}

// Xử lý tạo phiếu xuất
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taoPhieuXuat'])) {
    $IDDonBan = $_POST['IDDonBan'];
    $LoaiXuat = $_POST['LoaiXuat'] ?? 'Xuất bán hàng';
    $GhiChu = $_POST['GhiChu'] ?? '';
    $IDPhieuXuat = generateIDPhieuXuat($obj);

    // Thêm phiếu xuất
    $obj->xuatdulieu("
        INSERT INTO phieuxuat (IDPhieuXuat, IDDonBan, IDNV, NgayXuat, LoaiXuat, GhiChu, TrangThai)
        VALUES ('$IDPhieuXuat', '$IDDonBan', '$IDNV', NOW(), '$LoaiXuat', '$GhiChu', 'Đang chờ duyệt')
    ");

    // Lấy chi tiết đơn bán
    $chiTietDon = $obj->xuatdulieu("SELECT * FROM chitietdonban WHERE IDDonBan='$IDDonBan'");

    foreach ($chiTietDon as $ct) {
    $IDDauSach = $ct['IDDauSach'];
    $SoLuongYeuCau = $ct['SoLuong']; // số lượng còn lại trong đơn

    // Số lượng thực tế nhập
    $SoLuongThucTe = intval($_POST['SoLuongThucTe'][$IDDauSach] ?? $SoLuongYeuCau);
    if ($SoLuongThucTe > $SoLuongYeuCau) $SoLuongThucTe = $SoLuongYeuCau;

    $ThanhTien = $SoLuongThucTe * $ct['DonGia'];

    // Thêm chi tiết phiếu xuất
    $obj->xuatdulieu("
        INSERT INTO chitietxuatkho (IDPhieuXuat, IDDauSach, SoLuongYeuCau, SoLuongThucTe, DonGia, ThanhTien)
        VALUES ('$IDPhieuXuat', '$IDDauSach', $SoLuongYeuCau, $SoLuongThucTe, {$ct['DonGia']}, $ThanhTien)
    ");

    // Trừ số lượng thực tế xuất ra khỏi chi tiết đơn bán
    $SoLuongMoi = $SoLuongYeuCau - $SoLuongThucTe;
    $obj->xuatdulieu("
        UPDATE chitietdonban SET SoLuong = $SoLuongMoi
        WHERE IDDonBan='$IDDonBan' AND IDDauSach='$IDDauSach'
    ");
}


    $_SESSION['msg'] = "Phiếu xuất $IDPhieuXuat đã được tạo thành công!";
    header("Location: index.php?page=taophieuxuat_tk");
    exit();
}
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">➕ Tạo phiếu xuất kho</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <?php if(!empty($donhangs)): ?>
        <?php foreach($donhangs as $dh): 
            $chiTiet = $obj->xuatdulieu("
                SELECT ctdb.*, ds.TenDauSach FROM chitietdonban ctdb
                LEFT JOIN dausach ds ON ds.IDDauSach=ctdb.IDDauSach
                WHERE ctdb.IDDonBan='{$dh['IDDonBan']}'
            ");
        ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                Đơn: <?= $dh['IDDonBan'] ?> | Khách: <?= htmlspecialchars($dh['TenNguoiNhan']) ?> | Ngày đặt: <?= date('d/m/Y', strtotime($dh['NgayDat'])) ?>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="IDDonBan" value="<?= $dh['IDDonBan'] ?>">
                    <input type="hidden" name="LoaiXuat" value="Xuất bán hàng">

                    <table class="table table-bordered text-center align-middle mb-2">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sách</th>
                                <th>Số lượng yêu cầu</th>
                                <th>Số lượng đã xuất</th>
                                <th>Số lượng còn lại</th>
                                <th>Số lượng thực tế xuất</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($chiTiet as $ct): 
                                $IDDauSach = $ct['IDDauSach'];
                                $daXuat = $obj->xuatdulieu("
                                    SELECT IFNULL(SUM(SoLuongThucTe),0) AS DaXuat
                                    FROM chitietxuatkho ctx
                                    JOIN phieuxuat px ON px.IDPhieuXuat=ctx.IDPhieuXuat
                                    WHERE ctx.IDDauSach='$IDDauSach' AND px.IDDonBan='{$dh['IDDonBan']}' AND px.TrangThai='Đã duyệt'
                                ")[0]['DaXuat'] ?? 0;
                                $SoLuongConLai = $ct['SoLuong'] - $daXuat;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
                                <td><?= $ct['SoLuong'] ?></td>
                                <td><?= $daXuat ?></td>
                                <td><?= $SoLuongConLai ?></td>
                                <td>
                                    <input type="number" 
                                           name="SoLuongThucTe[<?= $IDDauSach ?>]" 
                                           class="form-control form-control-sm soLuongInput" 
                                           data-dongia="<?= $ct['DonGia'] ?>" 
                                           data-thanhTienId="thanhTien_<?= $IDDauSach ?>"
                                           min="0" max="<?= $SoLuongConLai ?>" 
                                           value="<?= $SoLuongConLai ?>" required>
                                </td>
                                <td><?= number_format($ct['DonGia']) ?></td>
                                <td id="thanhTien_<?= $IDDauSach ?>"><?= number_format($SoLuongConLai * $ct['DonGia']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <textarea name="GhiChu" class="form-control mb-2" placeholder="Ghi chú phiếu xuất"></textarea>
                    <div class="text-end">
                        <button type="submit" name="taoPhieuXuat" class="btn btn-success fw-bold">Tạo phiếu xuất</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">Không có đơn hàng nào đang ở trạng thái "Đang soạn hàng".</div>
    <?php endif; ?>
</div>

<script>
// Cập nhật thành tiền ngay khi thay đổi số lượng
document.querySelectorAll('.soLuongInput').forEach(input => {
    input.addEventListener('input', function() {
        let soLuong = parseInt(this.value) || 0;
        let donGia = parseInt(this.dataset.dongia) || 0;
        let thanhTienId = this.dataset.thanhTienId;
        document.getElementById(thanhTienId).innerText = new Intl.NumberFormat().format(soLuong * donGia);
    });
});
</script>

<style>
.card { border-radius: 10px; }
.table th, .table td { vertical-align: middle; }
.btn { border-radius: 6px; }
</style>
