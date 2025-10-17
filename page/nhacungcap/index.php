<?php
$obj = new database();

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['capNhatTrangThai'])) {
    $id = $_POST['IDDonDatNCC'] ?? 0;
    $trangthai = $_POST['TrangThaiMoi'] ?? '';
    if ($id && $trangthai) {
        $obj->themxoasua("UPDATE dondatncc SET TrangThai = '$trangthai' WHERE IDDonDatNCC = $id");
        echo "<script>alert('✅ Cập nhật trạng thái thành công!'); window.location.href='index.php?page=nhacungcap';</script>";
        exit();
    }
}

// Lấy danh sách đơn hàng + tên đầu sách + tổng số lượng
$dsdon = $obj->xuatdulieu("
    SELECT d.IDDonDatNCC, d.NgayDat, d.TongTien, d.TrangThai,
           GROUP_CONCAT(CONCAT(s.TenDauSach, ' (', c.SoLuong, ')') SEPARATOR ', ') AS SachDat
    FROM dondatncc d
    JOIN chitietdondatncc c ON d.IDDonDatNCC = c.IDDonDatNCC
    JOIN dausach s ON c.IDDauSach = s.IDDauSach
    GROUP BY d.IDDonDatNCC
    ORDER BY d.IDDonDatNCC DESC
");
?>

<div class="container my-5 p-4 bg-white shadow-lg rounded-4" style="max-width: 1100px;">
    <h2 class="text-center text-primary fw-bold mb-4">
        <i class="fa-solid fa-book-open-reader me-2"></i>Quản lý đơn đặt đầu sách từ Nhà cung cấp
    </h2>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th width="70">Mã đơn</th>
                    <th width="350">Đầu sách đặt (Số lượng)</th>
                    <th width="130">Ngày đặt</th>
                    <th width="150">Tổng tiền (₫)</th>
                    <th width="150">Trạng thái</th>
                    <th width="180">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($dsdon)): ?>
                    <tr><td colspan="6" class="text-center text-danger fw-bold">Chưa có đơn đặt nào!</td></tr>
                <?php else: ?>
                    <?php foreach ($dsdon as $item): ?>
                        <tr>
                            <td class="fw-semibold">#<?= $item['IDDonDatNCC'] ?></td>
                            <td class="text-start">
                                <?= htmlspecialchars($item['SachDat']) ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($item['NgayDat'])) ?></td>
                            <td class="fw-bold text-success"><?= number_format($item['TongTien']) ?></td>
                            <td>
                                <?php
                                $badge = "secondary";
                                if ($item['TrangThai'] == "Đã thanh toán") $badge = "info";
                                if ($item['TrangThai'] == "Đã xuất kho") $badge = "success";
                                if ($item['TrangThai'] == "Chưa thanh toán") $badge = "warning";
                                ?>
                                <span class="badge bg-<?= $badge ?> px-3 py-2"><?= $item['TrangThai'] ?></span>
                            </td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="IDDonDatNCC" value="<?= $item['IDDonDatNCC'] ?>">
                                    <select name="TrangThaiMoi" class="form-select form-select-sm d-inline w-auto me-2" required>
                                        <option value="">-- Trạng thái --</option>
                                        <option value="Chưa thanh toán">Chưa thanh toán</option>
                                        <option value="Đã thanh toán">Đã thanh toán</option>
                                        <option value="Đã xuất kho">Đã xuất kho</option>
                                    </select>
                                    <button type="submit" name="capNhatTrangThai" class="btn btn-sm btn-success rounded-pill">
                                        <i class="fa-solid fa-rotate me-1"></i> Cập nhật
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.table th, .table td { vertical-align: middle; }
.table tr:hover { background: #f9fbff; transition: background 0.2s; }
select.form-select-sm { border-radius: 20px; font-size: 0.9rem; }
.btn-success { transition: all 0.2s ease; }
.btn-success:hover { background-color: #157347; }
.badge { font-size: 0.85rem; padding: 6px 10px; }
td.text-start { text-align: left !important; }
</style>

<script src="https://kit.fontawesome.com/a2e0b6f9f6.js" crossorigin="anonymous"></script>
