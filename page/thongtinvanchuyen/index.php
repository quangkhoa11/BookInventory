<?php
$obj = new database();

// Kiểm tra đăng nhập
if(!isset($_SESSION['idkh'])){
    header("Location: index.php?page=dangnhap");
    exit;
}

// Kiểm tra giỏ hàng
if(empty($_SESSION['cart'])){
    echo '<div class="alert alert-warning text-center mt-5">Giỏ hàng trống!</div>';
    exit;
}

$idkh = $_SESSION['idkh'];
$tenkh = $_SESSION['tenkh'];
$loaikh = $_SESSION['loaikh'] ?? 'Cá nhân';
$error = "";

// Tính tổng tiền và chiết khấu
$tongtien = 0;
$chietkhau = 0;

foreach($_SESSION['cart'] as $item){
    $thanhtien = $item['gia'] * $item['soluong'];
    $tongtien += $thanhtien;

    if($loaikh === 'Tổ chức' && $item['soluong'] >= 80){
        $chietkhau += $thanhtien * 0.2;
    }
}

$tongtien -= $chietkhau;

// Xử lý form
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $tennguoinhan = trim($_POST['tennguoinhan'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');

    if($tennguoinhan === '' || $sdt === '' || $diachi === ''){
        $error = "Vui lòng điền đầy đủ thông tin giao hàng!";
    } else {
        // ✅ Lấy mã IDDonBan mới (DB001, DB002, ...)
        $res = $obj->xuatdulieu("SELECT IDDonBan FROM donban ORDER BY IDDonBan DESC LIMIT 1");
        if($res && count($res) > 0){
            $lastID = $res[0]['IDDonBan']; 
            $number = (int)substr($lastID, 2);
            $newNumber = $number + 1;
            $newID = 'DB' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $newID = 'DB001';
        }

        // ✅ Thêm đơn hàng
        $ngaydat = date('Y-m-d H:i:s');
        $trangthai = 'Chờ xác nhận';

        $sqlDonBan = "INSERT INTO donban (IDDonBan, IDKH, TenNguoiNhan, SDT, DiaChi, NgayDat, TongTien, TrangThai)
                      VALUES ('$newID', $idkh, '$tennguoinhan', '$sdt', '$diachi', '$ngaydat', $tongtien, '$trangthai')";
        $obj->themxoasua($sqlDonBan);

        // ✅ Thêm chi tiết đơn
        // ✅ Thêm chi tiết đơn
// ✅ Thêm chi tiết đơn
foreach($_SESSION['cart'] as $idDauSach => $item){
    $soluong = (int)$item['soluong'];

    // 🔍 Lấy giá chính xác từ bảng dausach để tránh sai lệch
    $sqlGia = "SELECT Gia FROM dausach WHERE IDDauSach = '$idDauSach'";
    $resultGia = $obj->xuatdulieu($sqlGia);

    if($resultGia && count($resultGia) > 0){
        $dongia = (float)$resultGia[0]['Gia'];
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Không tìm thấy giá cho đầu sách ID: $idDauSach</div>";
        continue;
    }

    // ✅ Thêm chi tiết đơn vào bảng chitietdonban
    $sqlChiTiet = "INSERT INTO chitietdonban (IDDonBan, IDDauSach, SoLuong, DonGia)
                   VALUES ('$newID', '$idDauSach', $soluong, $dongia)";
    $obj->themxoasua($sqlChiTiet);
}



        // ✅ Lưu IDDonBan vào session
        $_SESSION['iddonban'] = $newID;

        // ✅ Chuyển sang trang thanh toán
        header("Location: index.php?page=thanhtoan");
        exit;
    }
}
?>



<main class="container my-5">
  <h2 class="text-center mb-5">🚚 Thông tin giao hàng & Chi tiết đơn hàng</h2>

  <?php if($error !== ""): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="row g-4">
      <!-- Cột trái -->
      <div class="col-12 col-md-6">
        <div class="p-4 bg-white rounded shadow-sm">
          <h4 class="mb-4 text-primary">Thông tin người nhận</h4>

          <div class="mb-3">
            <label class="form-label">Tên người nhận</label>
            <input type="text" name="tennguoinhan" class="form-control form-control-lg" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="sdt" class="form-control form-control-lg" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Địa chỉ giao hàng</label>
            <textarea name="diachi" class="form-control form-control-lg" rows="4" required></textarea>
          </div>
        </div>
      </div>

      <!-- Cột phải -->
      <div class="col-12 col-md-6">
        <div class="p-4 bg-white rounded shadow-sm">
          <h4 class="mb-4 text-primary">📦 Chi tiết đơn hàng</h4>
          <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-primary">
                <tr>
                  <th>Ảnh</th>
                  <th>Tên sách</th>
                  <th>Giá</th>
                  <th>Số lượng</th>
                  <th>Thành tiền</th>
                  <th>Chiết khấu</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($_SESSION['cart'] as $item): 
                    $thanhtien = $item['gia'] * $item['soluong'];
                    $ck = ($loaikh === 'Tổ chức' && $item['soluong'] >= 80) ? $thanhtien * 0.2 : 0;
                ?>
                <tr>
                  <td><img src="./assets/images/<?= htmlspecialchars($item['hinh']) ?>" width="50"></td>
                  <td><?= htmlspecialchars($item['ten']) ?></td>
                  <td><?= number_format($item['gia'],0,',','.') ?> VND</td>
                  <td><?= $item['soluong'] ?></td>
                  <td><?= number_format($thanhtien,0,',','.') ?> VND</td>
                  <td><?= $ck > 0 ? number_format($ck,0,',','.') : '0' ?> VND</td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="text-end mt-3">
            <p><strong>Tổng tiền: <?= number_format($tongtien,0,',','.') ?> VND</strong></p>
            <button type="submit" class="btn btn-success btn-lg w-100">Thanh toán</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</main>

<!-- CSS -->
<style>
body {
  background-color: #f8f9fa;
  font-family: 'Segoe UI', sans-serif;
}

h2.text-center {
  color: #0d6efd;
}

.table img {
  border-radius: 6px;
}

table th, table td {
  vertical-align: middle !important;
}

table tbody tr:nth-child(even) {
  background-color: #f1f3f5;
}

button.btn-success:hover {
  background-color: #146c43;
}

@media (max-width: 768px) {
  .row.g-4 > div {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
</style>
