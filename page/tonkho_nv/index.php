<?php
$obj = new database();

// Lấy dữ liệu tồn kho
$tonkho = $obj->xuatdulieu("
    SELECT ds.IDDauSach, ds.TenDauSach, SUM(s.SoLuong) AS TonKho
    FROM dausach ds
    LEFT JOIN sach s ON ds.IDDauSach = s.IDDauSach
    GROUP BY ds.IDDauSach, ds.TenDauSach
");
?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📦 Tồn kho đầu sách</h2>

    <table class="table table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>Mã đầu sách</th>
                <th>Tên đầu sách</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tonkho as $tk):
                $ton = intval($tk['TonKho']);
                if ($ton < 2) { $trangthai = 'Cần bổ sung'; $class='table-danger'; }
                elseif ($ton < 5) { $trangthai='Sắp hết'; $class='table-warning'; }
                else { $trangthai='Đủ'; $class=''; }
            ?>
            <tr class="<?= $class ?>">
                <td><?= $tk['IDDauSach'] ?></td>
                <td><?= htmlspecialchars($tk['TenDauSach']) ?></td>
                <td><?= $ton ?></td>
                <td><?= $trangthai ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-end mt-3">
        <a href="index.php?page=nhanvienkho" class="btn btn-secondary">🔙 Quay về</a>
    </div>
</div>
