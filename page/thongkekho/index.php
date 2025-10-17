<?php
$obj = new database();

// Role và IDNV
$role = $_SESSION['role'] ?? 'thukho';
$IDNV = $_SESSION['IDNV'] ?? 1;

// 1️⃣ Thống kê tồn kho
$tonKho = $obj->xuatdulieu("
    SELECT ds.IDDauSach, ds.TenDauSach, SUM(s.SoLuong) AS TongTon, MAX(s.NgayCapNhat) AS NgayCapNhat
    FROM sach s
    JOIN dausach ds ON ds.IDDauSach = s.IDDauSach
    GROUP BY ds.IDDauSach
    ORDER BY ds.TenDauSach ASC
");

// 2️⃣ Báo cáo xuất kho tháng
$thangHienTai = date('Y-m');
// Lấy số liệu xuất kho thực tế (dao động theo ngày)
// Báo cáo xuất kho theo ngày thực tế (có giao động)
$baoCaoXuat = $obj->xuatdulieu("
    SELECT ds.IDDauSach, ds.TenDauSach, 
           px.NgayXuat,
           SUM(ctx.SoLuongThucTe) AS SoLuongXuatNgay
    FROM chitietxuatkho ctx
    JOIN phieuxuat px ON px.IDPhieuXuat = ctx.IDPhieuXuat
    JOIN dausach ds ON ds.IDDauSach = ctx.IDDauSach
    WHERE px.NgayXuat LIKE '$thangHienTai%'
    GROUP BY ds.IDDauSach, DATE(px.NgayXuat)
    ORDER BY ds.TenDauSach ASC, px.NgayXuat ASC
");


?>

<div class="container my-5">
    <h2 class="text-primary fw-bold text-center mb-4">📊 Thống kê kho</h2>

    <!-- Nút điều hướng -->
    <div class="mb-4 d-flex gap-2">
         <a href="index.php?page=thukho" class="btn btn-danger">Quản lý phiếu</a>
        <a href="index.php?page=taophieuxuat" class="btn btn-primary">Tạo phiếu xuất</a>
        <a href="index.php?page=taophieunhap" class="btn btn-success">Tạo phiếu nhập</a>
        <a href="index.php?page=baocao" class="btn btn-warning">Báo cáo</a>
        <a href="index.php?page=thongkekho" class="btn btn-info">Thống kê tồn kho</a>
    </div>

    <!-- Thống kê tồn kho -->
    <h4 class="mb-3">📦 Tồn kho hiện tại</h4>
    <table class="table table-bordered table-striped text-center">
        <thead class="table-dark">
            <tr>
                <th>ID Đầu sách</th>
                <th>Tên sách</th>
                <th>Tổng tồn</th>
                <th>Ngày cập nhật</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tonKho as $tk): ?>
            <tr>
                <td><?= $tk['IDDauSach'] ?></td>
                <td><?= htmlspecialchars($tk['TenDauSach']) ?></td>
                <td><?= $tk['TongTon'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($tk['NgayCapNhat'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Báo cáo xuất kho tháng -->
   <h4 class="mt-5 mb-3">📈 Báo cáo xuất kho tháng <?= date('m/Y') ?> (giao động theo ngày)</h4>
<table class="table table-bordered table-striped text-center">
    <thead class="table-dark">
        <tr>
            <th>Tên sách</th>
            <th>Ngày xuất</th>
            <th>Số lượng xuất</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($baoCaoXuat as $bc): ?>
        <tr>
            <td><?= htmlspecialchars($bc['TenDauSach']) ?></td>
            <td><?= date('d/m/Y', strtotime($bc['NgayXuat'])) ?></td>
            <td><?= $bc['SoLuongXuatNgay'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


</div>

<style>
.table td, .table th { vertical-align: middle; }
.btn { border-radius: 6px; padding: 5px 12px; }
</style>
