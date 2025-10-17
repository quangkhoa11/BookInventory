<?php
$obj = new database();

// Cập nhật kiểm kê
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thucte'])) {
    $thucte = $_POST['thucte'];
    foreach ($thucte as $IDDauSach => $soluongThucTe) {
        $IDDauSach = intval($IDDauSach);
        $soluongThucTe = intval($soluongThucTe);

        $sachList = $obj->xuatdulieu("SELECT IDSach, SoLuong FROM sach WHERE IDDauSach=$IDDauSach");
        $tong = count($sachList);
        if ($tong <= 0) continue;

        $tongHienTai = array_sum(array_column($sachList, 'SoLuong'));
        if ($tongHienTai != $soluongThucTe) {
            $soLuongMoi = intdiv($soluongThucTe, $tong);
            $du = $soluongThucTe % $tong;
            $now = date('Y-m-d H:i:s');

            foreach ($sachList as $index => $sach) {
                $sl = $soLuongMoi; 
                if ($index === 0) $sl += $du;
                if ($sach['SoLuong'] != $sl)
                    $obj->xuatdulieu("UPDATE sach SET SoLuong=$sl, NgayCapNhat='$now' WHERE IDSach=".$sach['IDSach']);
            }
        }
    }
    $_SESSION['msg']="Cập nhật số lượng kiểm kê thành công!";
    header("Location: index.php?page=kiemke_nv"); // refresh trang
    exit();
}

// Lấy dữ liệu tồn kho để hiển thị trong kiểm kê
$tonkho = $obj->xuatdulieu("
    SELECT ds.IDDauSach, ds.TenDauSach, SUM(s.SoLuong) AS TonKho
    FROM dausach ds
    LEFT JOIN sach s ON ds.IDDauSach = s.IDDauSach
    GROUP BY ds.IDDauSach, ds.TenDauSach
");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📝 Kiểm kê số lượng thực tế</h2>

    <?php if(isset($_SESSION['msg'])): ?>
        <div class="alert alert-success"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <form method="post">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Mã đầu sách</th>
                    <th>Tên đầu sách</th>
                    <th>Số lượng hiện tại</th>
                    <th>Số lượng thực tế</th>
                    <th>Ngày cập nhật</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tonkho as $tk):
                    $ngayCapNhat = $obj->xuatdulieu("SELECT MAX(NgayCapNhat) AS NgayCapNhat FROM sach WHERE IDDauSach=".$tk['IDDauSach'])[0]['NgayCapNhat'] ?? '';
                ?>
                <tr>
                    <td><?= $tk['IDDauSach'] ?></td>
                    <td><?= htmlspecialchars($tk['TenDauSach']) ?></td>
                    <td><?= $tk['TonKho'] ?></td>
                    <td><input type="number" name="thucte[<?= $tk['IDDauSach'] ?>]" value="<?= $tk['TonKho'] ?>" class="form-control form-control-sm"></td>
                    <td><?= $ngayCapNhat ? date('d/m/Y', strtotime($ngayCapNhat)) : '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-end mt-3">
            <button type="submit" class="btn btn-warning">Cập nhật kiểm kê</button>
            <a href="index.php?page=nhanvienkho" class="btn btn-secondary">🔙 Quay về</a>
        </div>
    </form>
</div>
