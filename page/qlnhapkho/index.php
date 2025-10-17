<?php
$obj = new database();
$IDNV = $_SESSION['IDNV'] ?? 1;

// ====== HÀM SINH MÃ PHIẾU NHẬP ======
function generateIDPhieuNhap($obj) {
    $last = $obj->xuatdulieu("SELECT IDPhieuNhap FROM phieunhap ORDER BY IDPhieuNhap DESC LIMIT 1");
    if (!empty($last)) {
        $lastID = $last[0]['IDPhieuNhap'];
        $num = (int)substr($lastID, 3);
        $newNum = $num + 1;
    } else {
        $newNum = 1;
    }
    return "PNK" . str_pad($newNum, 3, "0", STR_PAD_LEFT);
}

// ====== XỬ LÝ TẠO PHIẾU ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taoPhieuNhap'])) {
    $idDon = intval($_POST['IDDonDatNCC'] ?? 0);
    $ngayNhap = date('Y-m-d');
    $don = $obj->xuatdulieu("SELECT * FROM dondatncc WHERE IDDonDatNCC = $idDon AND TrangThai = 'Đã xuất kho'");
    if ($don && isset($don[0])) $don = $don[0];

    if (!empty($don)) {
        $idNCC = $don['IDNCC'];
        $tongtien = $don['TongTien'];
        $idPhieuNhap = generateIDPhieuNhap($obj);

        $obj->themxoasua("
            INSERT INTO phieunhap (IDPhieuNhap, IDNCC, NgayNhap, IDNV, TrangThai, TongTien)
            VALUES ('$idPhieuNhap', $idNCC, '$ngayNhap', $IDNV, 'Đang chờ duyệt', $tongtien)
        ");

        $chitiet = $obj->xuatdulieu("SELECT * FROM chitietdondatncc WHERE IDDonDatNCC = $idDon");
        foreach ($chitiet as $ct) {
            $idSach = $ct['IDDauSach'];
            $soLuong = $ct['SoLuong'];
            $donGia = $ct['Gia'];
            $obj->themxoasua("
                INSERT INTO chitietnhapkho (IDPhieuNhap, IDDauSach, SoLuong, DonGia)
                VALUES ('$idPhieuNhap', $idSach, $soLuong, $donGia)
            ");
        }

        echo "<script>alert('✅ Tạo phiếu nhập thành công! Mã: $idPhieuNhap');</script>";
    } else {
        echo "<script>alert('❌ Không tìm thấy đơn NCC hợp lệ!');</script>";
    }
}

// ====== DUYỆT PHIẾU ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duyetPhieu'])) {
    $id = $_POST['IDPhieuNhap'] ?? '';
    $obj->themxoasua("UPDATE phieunhap SET TrangThai = 'Đã duyệt' WHERE IDPhieuNhap = '$id'");
    $chitiet = $obj->xuatdulieu("SELECT IDDauSach, SoLuong FROM chitietnhapkho WHERE IDPhieuNhap = '$id'");

    foreach ($chitiet as $ct) {
        $idSach = $ct['IDDauSach'];
        $soLuongNhap = $ct['SoLuong'];
        $check = $obj->xuatdulieu("SELECT SoLuong FROM sach WHERE IDDauSach = $idSach");
        if (!empty($check)) {
            $obj->themxoasua("UPDATE sach SET SoLuong = SoLuong + $soLuongNhap WHERE IDDauSach = $idSach");
        } else {
            $obj->themxoasua("INSERT INTO sach (IDDauSach, SoLuong) VALUES ($idSach, $soLuongNhap)");
        }
    }

    echo "<script>alert('✅ Phiếu nhập $id đã được duyệt và cập nhật tồn kho!'); window.location.href = window.location.href;</script>";
}

// ====== DANH SÁCH ĐƠN NCC & PHIẾU NHẬP ======
$dsDonDaXuat = $obj->xuatdulieu("
    SELECT d.IDDonDatNCC, n.TenNCC, d.NgayDat, d.TongTien
    FROM dondatncc d
    JOIN nhacungcap n ON d.IDNCC = n.IDNCC
    WHERE d.TrangThai = 'Đã xuất kho'
    ORDER BY d.IDDonDatNCC DESC
");

$dsPhieu = $obj->xuatdulieu("
    SELECT p.*, n.TenNCC 
    FROM phieunhap p
    JOIN nhacungcap n ON p.IDNCC = n.IDNCC
    ORDER BY p.NgayNhap DESC
");

// ====== LẤY CHI TIẾT ĐƠN NẾU CHỌN ======
$idDonChon = $_POST['IDDonDatNCC'] ?? '';
$chitietDon = [];
if (!empty($idDonChon)) {
    $chitietDon = $obj->xuatdulieu("
        SELECT c.IDDauSach, s.TenDauSach, c.SoLuong, c.Gia
        FROM chitietdondatncc c
        JOIN dausach s ON c.IDDauSach = s.IDDauSach
        WHERE c.IDDonDatNCC = $idDonChon
    ");
}
?>

<style>
    h2, h3 {
        text-align: center;
        color: #0d6efd;
        margin-bottom: 15px;
    }
    form {
        text-align: center;
        margin-bottom: 25px;
    }
    select, button {
        padding: 8px 12px;
        font-size: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin: 5px;
    }
    select:focus {
        outline: none;
        border-color: #0d6efd;
    }
    button {
        cursor: pointer;
        border: none;
        transition: 0.3s;
    }
    button[name="taoPhieuNhap"] {
        background: #28a745;
        color: white;
    }
    button[name="taoPhieuNhap"]:hover {
        background: #218838;
    }
    button[name="duyetPhieu"] {
        background: #0d6efd;
        color: white;
        padding: 6px 12px;
    }
    button[name="duyetPhieu"]:hover {
        background: #0b5ed7;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    th {
        background: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
    }
    td {
        padding: 8px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    tr:hover {
        background: #f1f7ff;
    }
    .empty {
        color: #dc3545;
        font-weight: bold;
    }
    .footer {
        text-align: center;
        color: #777;
        margin-top: 30px;
        font-size: 13px;
    }
</style>

<h2>QUẢN LÝ PHIẾU NHẬP KHO</h2>

<!-- Form chọn đơn NCC -->
<form method="post" id="chonDonForm">
    <select name="IDDonDatNCC" onchange="document.getElementById('chonDonForm').submit();" required>
        <option value="">-- Chọn đơn NCC đã xuất kho --</option>
        <?php foreach ($dsDonDaXuat as $don): ?>
            <option value="<?= $don['IDDonDatNCC'] ?>" <?= ($don['IDDonDatNCC'] == $idDonChon) ? 'selected' : '' ?>>
                #<?= $don['IDDonDatNCC'] ?> - <?= htmlspecialchars($don['TenNCC']) ?> (<?= date('d/m/Y', strtotime($don['NgayDat'])) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($chitietDon)): ?>
        <button type="submit" name="taoPhieuNhap">Tạo phiếu nhập</button>
    <?php endif; ?>
</form>

<!-- Bảng chi tiết đơn -->
<?php if (!empty($chitietDon)): ?>
    <h3>Chi tiết đơn NCC #<?= htmlspecialchars($idDonChon) ?></h3>
    <table>
        <tr>
            <th>Mã đầu sách</th>
            <th>Tên đầu sách</th>
            <th>Số lượng</th>
            <th>Đơn giá</th>
            <th>Thành tiền</th>
        </tr>
        <?php $tong = 0; foreach ($chitietDon as $ct): 
            $thanhtien = $ct['SoLuong'] * $ct['Gia'];
            $tong += $thanhtien;
        ?>
        <tr>
            <td><?= $ct['IDDauSach'] ?></td>
            <td><?= htmlspecialchars($ct['TenDauSach']) ?></td>
            <td><?= $ct['SoLuong'] ?></td>
            <td><?= number_format($ct['Gia'], 0, ',', '.') ?> ₫</td>
            <td><?= number_format($thanhtien, 0, ',', '.') ?> ₫</td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:bold;background:#f8f9fa;">
            <td colspan="4" style="text-align:right;">Tổng cộng:</td>
            <td><?= number_format($tong, 0, ',', '.') ?> ₫</td>
        </tr>
    </table>
<?php endif; ?>

<!-- Danh sách phiếu nhập -->
<h3>Danh sách phiếu nhập</h3>
<table>
    <tr>
        <th>Mã phiếu</th>
        <th>Nhà cung cấp</th>
        <th>Ngày nhập</th>
        <th>Tổng tiền</th>
        <th>Nhân viên</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>
    <?php if (empty($dsPhieu)): ?>
        <tr><td colspan="7" class="empty">Chưa có phiếu nhập!</td></tr>
    <?php else: ?>
        <?php foreach ($dsPhieu as $p): ?>
        <tr>
            <td><?= $p['IDPhieuNhap'] ?></td>
            <td><?= htmlspecialchars($p['TenNCC']) ?></td>
            <td><?= date('d/m/Y', strtotime($p['NgayNhap'])) ?></td>
            <td><?= number_format($p['TongTien'], 0, ',', '.') ?> ₫</td>
            <td><?= $p['IDNV'] ?></td>
            <td><?= $p['TrangThai'] ?></td>
            <td>
                <?php if ($p['TrangThai'] == 'Đang chờ duyệt'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="IDPhieuNhap" value="<?= $p['IDPhieuNhap'] ?>">
                        <button type="submit" name="duyetPhieu">Duyệt</button>
                    </form>
                <?php else: ?>
                    <span style="color:gray;">Đã duyệt</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<div class="footer">© 2025 - Quản lý kho sách</div>
