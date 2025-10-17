<?php
$obj = new database();

// === Cập nhật trạng thái đơn hàng ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTrangThai'])) {
    $IDDonBan = $_POST['IDDonBan'];
    $TrangThai = $_POST['TrangThai'];

    $trangThaiHopLe = ['Chờ xác nhận', 'Đang soạn hàng', 'Đã xuất kho'];
    if (in_array($TrangThai, $trangThaiHopLe)) {
        $obj->xuatdulieu("UPDATE donban SET TrangThai='$TrangThai' WHERE IDDonBan='$IDDonBan'");
        $_SESSION['msg'] = "Cập nhật trạng thái đơn hàng thành công!";
    }
    header("Location: index.php?page=xemdonhang_nv");
    exit();
}

// === Lấy danh sách đơn hàng ===
$donhangs = $obj->xuatdulieu("SELECT IDDonBan, TrangThai FROM donban ORDER BY IDDonBan DESC");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📄 Quản lý đơn hàng</h2>
    

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <!-- Nút tạo phiếu xuất -->
    <div class="mb-3 text-end">
        <a href="index.php?page=phieuxuat" class="btn btn-success fw-bold">
            📝 Tạo phiếu xuất
        </a>
    </div>

    <table class="table table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($donhangs as $dh): ?>
            <tr>
                <td><?= $dh['IDDonBan'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="IDDonBan" value="<?= $dh['IDDonBan'] ?>">
                        <select name="TrangThai" class="form-select form-select-sm">
                            <option value="Chờ xác nhận" <?= $dh['TrangThai']=='Chờ xác nhận'?'selected':'' ?>>Chờ xác nhận</option>
                            <option value="Đang soạn hàng" <?= $dh['TrangThai']=='Đang soạn hàng'?'selected':'' ?>>Đang soạn hàng</option>
                            <option value="Đã xuất kho" <?= $dh['TrangThai']=='Đã xuất kho'?'selected':'' ?>>Đã xuất kho</option>
                        </select>
                        <button type="submit" name="updateTrangThai" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                    </form>
                </td>
                <td>
                    <a href="index.php?page=xemdonct&IDDonBan=<?= $dh['IDDonBan'] ?>" class="btn btn-sm btn-info">Xem chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-3">
        <a href="index.php?page=nhanvienkho" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
