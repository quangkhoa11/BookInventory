<?php
$obj = new database();

$IDDonDatNCC = $_GET['iddon'] ?? 0;
if (!$IDDonDatNCC) {
    echo "<div class='text-center text-danger fw-bold mt-5'>❌ Không tìm thấy đơn hàng!</div>";
    exit();
}

// Lấy thông tin đơn đặt + NCC
$don = $obj->xuatdulieu("
    SELECT d.*, n.TenNCC, n.SDT, n.DiaChi
    FROM dondatncc d
    JOIN nhacungcap n ON d.IDNCC = n.IDNCC
    WHERE d.IDDonDatNCC = $IDDonDatNCC
");
if (empty($don)) {
    echo "<div class='text-center text-danger fw-bold mt-5'>❌ Đơn đặt không tồn tại!</div>";
    exit();
}
$don = $don[0];

// Lấy chi tiết đơn hàng
$ct = $obj->xuatdulieu("
    SELECT c.*, s.TenDauSach
    FROM chitietdondatncc c
    JOIN dausach s ON c.IDDauSach = s.IDDauSach
    WHERE c.IDDonDatNCC = $IDDonDatNCC
");

// Tổng tiền thanh toán toàn bộ
$TongThanhToan = $don['TongTien'];

// Tạo QR thanh toán VietQR (thanh toán toàn bộ)
$bank_id = "Vietinbank";
$account_no = "106874938508";
$account_name = "THAKHO INVENTORY";
$noi_dung = "Thanh toan NCC Don #" . $IDDonDatNCC;
$vietqr_url = "https://img.vietqr.io/image/{$bank_id}-{$account_no}-compact2.jpg?amount={$TongThanhToan}&addInfo=" . urlencode($noi_dung) . "&accountName=" . urlencode($account_name);

// Nếu xác nhận thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['xacNhanThanhToan'])) {
    $NgayThanhToan = date('Y-m-d H:i:s');
    $IDNCC = $don['IDNCC'];
    $GhiChu = "Thanh toán toàn bộ đơn #" . $IDDonDatNCC;

    $sql_insert = "
        INSERT INTO thanhtoanncc (IDNCC, IDDonDatNCC, NgayThanhToan, TongTien, GhiChu)
        VALUES ('$IDNCC', '$IDDonDatNCC', '$NgayThanhToan', '$TongThanhToan', '$GhiChu')
    ";
    $obj->themxoasua($sql_insert);

    // Cập nhật trạng thái đơn hàng
    $obj->themxoasua("UPDATE dondatncc SET TrangThai = 'Đã thanh toán' WHERE IDDonDatNCC = $IDDonDatNCC");

    $_SESSION['msg'] = "✅ Thanh toán thành công cho đơn #$IDDonDatNCC!";
    header("Location: index.php?page=dathangncc");
    exit();
}
?>

<div class="container my-5 p-4 bg-white shadow-lg rounded-4" style="max-width: 850px;">
    <h2 class="text-center text-primary fw-bold mb-4">
        <i class="fa-solid fa-receipt me-2"></i>Thanh toán cho Nhà cung cấp
    </h2>

    <div class="mb-3">
        <h5 class="fw-bold text-secondary mb-2"><i class="fa-solid fa-store me-2"></i>Thông tin NCC</h5>
        <p><strong>Tên NCC:</strong> <?= htmlspecialchars($don['TenNCC']) ?></p>
        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($don['DiaChi']) ?></p>
        <p><strong>SĐT:</strong> <?= htmlspecialchars($don['SDT']) ?></p>
        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($don['NgayDat'])) ?></p>
    </div>

    <hr class="text-primary opacity-50">

    <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-book me-2"></i>Chi tiết đơn hàng</h5>
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Tên sách</th>
                    <th width="120">Số lượng</th>
                    <th width="150">Đơn giá (₫)</th>
                    <th width="150">Thành tiền (₫)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ct as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['TenDauSach']) ?></td>
                        <td><?= $item['SoLuong'] ?></td>
                        <td><?= number_format($item['Gia']) ?></td>
                        <td><?= number_format($item['SoLuong'] * $item['Gia']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="text-end mt-3">
        <h5 class="fw-bold text-success">Tổng tiền cần thanh toán: <?= number_format($TongThanhToan) ?> ₫</h5>
    </div>

    <div class="text-center my-4">
        <img src="<?= $vietqr_url ?>" alt="QR thanh toán" class="border rounded-3 shadow-sm" width="260">
        <p class="mt-2 text-secondary">Quét mã VietQR để thanh toán toàn bộ đơn hàng</p>
    </div>

    <form method="post" class="text-center">
        <button type="submit" name="xacNhanThanhToan" class="btn btn-success px-4 py-2 fw-bold rounded-pill shadow-sm me-2">
            <i class="fa-solid fa-check me-1"></i> Xác nhận thanh toán
        </button>
        <a href="index.php?page=dathangncc" class="btn btn-outline-secondary px-4 py-2 fw-semibold rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </form>
</div>

<style>
.table th, .table td { vertical-align: middle; }
.table tr:hover { background: #f9fbff; }
img { transition: transform .2s ease; }
img:hover { transform: scale(1.05); }
.btn-success:hover { background: #198754; }
</style>

<script src="https://kit.fontawesome.com/a2e0b6f9f6.js" crossorigin="anonymous"></script>
