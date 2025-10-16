<?php
$obj = new database();

// Kiểm tra đăng nhập
if(!isset($_SESSION['idkh'])){
    echo "<div class='alert alert-warning text-center mt-5'>⚠️ Vui lòng đăng nhập để xem đơn hàng!</div>";
    return;
}

$idkh = $_SESSION['idkh'];

// Lấy đơn hàng mới nhất của khách
$don = $obj->xuatdulieu("SELECT * FROM donban WHERE IDKH = $idkh ORDER BY NgayDat DESC LIMIT 1");
if(!$don || count($don) == 0){
    echo "<div class='alert alert-warning text-center mt-5'>⚠️ Không tìm thấy đơn hàng nào. Vui lòng đặt hàng trước.</div>";
    return;
}
$don = $don[0];
$idDonBan = $don['IDDonBan'];

// Lấy chi tiết đơn hàng
$chitiet = $obj->xuatdulieu("
    SELECT c.*, d.TenDauSach 
    FROM chitietdonban c 
    JOIN dausach d ON c.IDDauSach = d.IDDauSach
    WHERE c.IDDonBan = '$idDonBan'
");

$showQR = false;
$qrURL = null;
$tongtien = $don['TongTien'];
$noidungCK = "THANHTOAN_DON_" . $idDonBan;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $phuongthuc = $_POST['phuongthuc'] ?? '';
    $ngayThanhToan = date('Y-m-d H:i:s');

    // Thanh toán tiền mặt
    if($phuongthuc === 'tienmat'){
        $obj->themxoasua("INSERT INTO thanhtoankh (IDKH, IDDonBan, NgayThanhToan, TongTien, PhuongThuc, GhiChu) 
                          VALUES ('$idkh', '$idDonBan', '$ngayThanhToan', '$tongtien', 'Tiền mặt', 'Khách thanh toán tiền mặt')");
        $obj->themxoasua("UPDATE donban SET TrangThai = 'Chờ xác nhận' WHERE IDDonBan = '$idDonBan'");
        unset($_SESSION['cart']);
        echo "<script>alert('✅ Đơn hàng đang chờ xác nhận'); window.location='index.php?page=danhmucsach';</script>";
        exit;
    }
    // Thanh toán chuyển khoản: hiển thị QR
    elseif($phuongthuc === 'chuyenkhoan'){
        $bank = "Vietinbank";
        $account = "106874938508";
        $tenTK = "LE QUANG KHOA";

        $qrURL = "https://img.vietqr.io/image/{$bank}-{$account}-compact2.png?amount={$tongtien}&addInfo={$noidungCK}&accountName=" . urlencode($tenTK);
        $showQR = true;
    }

    // Lưu chuyển khoản khi bấm "Quay lại"
    if(isset($_POST['luu_ck'])){
        $obj->themxoasua("INSERT INTO thanhtoankh (IDKH, IDDonBan, NgayThanhToan, TongTien, PhuongThuc, GhiChu) 
                          VALUES ('$idkh', '$idDonBan', '$ngayThanhToan','$tongtien', 'Chuyển khoản', 'Khách chọn chuyển khoản')");
        $obj->themxoasua("UPDATE donban SET TrangThai = 'Chờ xác nhận' WHERE IDDonBan = '$idDonBan'");
        unset($_SESSION['cart']);
        echo "<script>alert('✅ Giao dịch thành công'); window.location='index.php?page=danhmucsach';</script>";
        exit;
    }
}
?>

<style>
.container-tt { max-width: 800px; margin: 50px auto; background: #fff; border-radius: 12px; padding: 30px 35px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); font-family: 'Segoe UI', sans-serif; }
h3 { color: #2c3e50; font-weight: 700; }
h5 { color: #34495e; font-weight: 600; }
.book-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
.book-item:last-child { border-bottom: none; }
.book-title { font-weight: 600; font-size: 1rem; }
.book-qty { font-size: 0.9rem; color: #555; }
.total { font-size: 1.3rem; font-weight: 700; color: #e74c3c; text-align: right; margin-top: 15px; }
.form-check-label { font-size: 1rem; }
.btn-confirm { width: 100%; padding: 12px; font-size: 1.1rem; background: linear-gradient(90deg, #3498db, #2980b9); border: none; color: #fff; border-radius: 8px; transition: 0.3s; }
.btn-confirm:hover { background: linear-gradient(90deg, #2980b9, #3498db); }
.qr-box { text-align: center; margin-top: 30px; padding: 20px; border: 1px dashed #ccc; border-radius: 10px; background: #f9f9f9; }
.qr-box img { margin-top: 15px; border-radius: 10px; }
.alert-info { margin-top: 15px; }
.btn-back { margin-top: 15px; padding: 10px 20px; background: #3498db; color: #fff; border: none; border-radius: 6px; cursor: pointer; transition: 0.3s; }
.btn-back:hover { background: #2980b9; }
</style>

<div class="container-tt">
    <h3 class="text-center mb-4">💳 Thanh Toán Đơn Hàng #<?= $idDonBan ?></h3>

    <h5>📚 Thông tin sách</h5>
    <?php if($chitiet && count($chitiet) > 0): ?>
        <?php foreach($chitiet as $item): ?>
            <div class="book-item">
                <div>
                    <div class="book-title"><?= htmlspecialchars($item['TenDauSach']) ?></div>
                    <div class="book-qty">Số lượng: <?= $item['SoLuong'] ?></div>
                </div>
                <div><?= number_format($item['DonGia']) ?>₫</div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Không có sản phẩm nào trong đơn hàng.</p>
    <?php endif; ?>

    <div class="total">Tổng tiền: <?= number_format($don['TongTien']) ?>₫</div>

    <?php if(!$showQR): ?>
    <form method="POST" class="mt-4">
        <h5>🪙 Chọn phương thức thanh toán</h5>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="phuongthuc" id="tm" value="tienmat" required>
            <label class="form-check-label" for="tm">Thanh toán khi nhận hàng</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="phuongthuc" id="ck" value="chuyenkhoan" required>
            <label class="form-check-label" for="ck">Chuyển khoản qua ngân hàng (VietQR)</label>
        </div>
        <button type="submit" class="btn btn-confirm mt-3">Xác nhận thanh toán</button>
    </form>
    <?php endif; ?>

    <?php if($showQR && $qrURL): ?>
        <div class="qr-box">
            <h5>🔍 Quét mã QR để thanh toán</h5>
            <img src="<?= $qrURL ?>" width="250" height="250" alt="QR Thanh toán">
            <p class="mt-2">
                <strong>Số tiền:</strong> <?= number_format($tongtien) ?>₫<br>
                <strong>Nội dung CK:</strong> <?= $noidungCK ?>
            </p>
            <div class="alert alert-info">
                💡 Quét mã thanh toán để hoàn tất giao dịch.
            </div>
            <form method="POST">
                <button type="submit" name="luu_ck" class="btn btn-back">Quay lại</button>
            </form>
        </div>
    <?php endif; ?>
</div>
