<?php
$obj = new database();

// === Cập nhật trạng thái đơn hàng ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTrangThai'])) {
    $IDDonBan = $_POST['IDDonBan']; // varchar
    $TrangThai = $_POST['TrangThai'];

    $trangThaiHopLe = ['Chờ xác nhận','Đang soạn hàng','Đã xuất kho',''];
    if (in_array($TrangThai, $trangThaiHopLe)) {
        $obj->xuatdulieu("UPDATE donban SET TrangThai='$TrangThai' WHERE IDDonBan='$IDDonBan'");
        $_SESSION['msg'] = "Cập nhật trạng thái đơn $IDDonBan thành công!";
    }

    header("Location: index.php?page=xemdonhang_tk");
    exit();
}

// === Lấy danh sách đơn hàng ===
$donhangs = $obj->xuatdulieu("
    SELECT IDDonBan, TenNguoiNhan, SDT, DiaChi, NgayDat, TongTien, TrangThai
    FROM donban
    ORDER BY NgayDat DESC
");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📄 Quản lý đơn hàng - Thủ kho</h2>
<div class="mt-3 pb-3" style="float: right;">
        <a href="index.php?page=thukho" class="btn btn-secondary">Quay lại</a>
    </div>
    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID Đơn</th>
                <th>Người nhận</th>
                <th>SDT</th>
                <th>Địa chỉ</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($donhangs as $dh): ?>
            <tr>
                <td><?= $dh['IDDonBan'] ?></td>
                <td><?= htmlspecialchars($dh['TenNguoiNhan']) ?></td>
                <td><?= $dh['SDT'] ?></td>
                <td><?= htmlspecialchars($dh['DiaChi']) ?></td>
                <td><?= date('d/m/Y', strtotime($dh['NgayDat'])) ?></td>
                <td><?= number_format($dh['TongTien'],0,',','.') ?> ₫</td>
                <td>
                    <form method="post">
                        <input type="hidden" name="IDDonBan" value="<?= $dh['IDDonBan'] ?>">
                        <select name="TrangThai" class="form-select form-select-sm">
                            <option value="Chờ xác nhận" <?= $dh['TrangThai']=='Chờ xác nhận'?'selected':'' ?>>Chờ xác nhận</option>
                            <option value="Đang soạn hàng" <?= $dh['TrangThai']=='Đang soạn hàng'?'selected':'' ?>>Đang soạn hàng</option>
                            <option value="Đã xuất kho" <?= $dh['TrangThai']=='Đã xuất kho'?'selected':'' ?>>Đã xuất kho</option>
                            <option value="" <?= $dh['TrangThai']==''?'selected':'' ?>>Trống</option>
                        </select>
                        <button type="submit" name="updateTrangThai" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <a href="index.php?page=xemdonct_tk&IDDonBan=<?= $dh['IDDonBan'] ?>" class="btn btn-sm btn-info">Xem chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.table td, .table th { vertical-align: middle; }
.btn { border-radius: 6px; padding: 5px 12px; }
</style>
