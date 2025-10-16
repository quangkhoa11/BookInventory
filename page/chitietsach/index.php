<?php
$obj = new database();

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// --- Xử lý thêm vào giỏ hàng ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
    $idDauSach = isset($_POST['idds']) ? (int)$_POST['idds'] : 0;
    $soluong = isset($_POST['soluong']) ? (int)$_POST['soluong'] : 1;

    if ($idDauSach > 0 && $soluong > 0) {
        $sql = "SELECT TenDauSach, Gia, HinhAnh FROM dausach WHERE IDDauSach=$idDauSach";
        $data = $obj->xuatdulieu($sql);
        if ($data) {
            $book = $data[0];
            if (isset($_SESSION['cart'][$idDauSach])) {
                $_SESSION['cart'][$idDauSach]['soluong'] += $soluong;
            } else {
                $_SESSION['cart'][$idDauSach] = [
                    'ten' => $book['TenDauSach'],
                    'gia' => $book['Gia'],
                    'hinh' => $book['HinhAnh'],
                    'soluong' => $soluong
                ];
            }
            $message = "✅ Đã thêm vào giỏ hàng!";
        } else {
            $message = "❌ Sách không tồn tại!";
        }
    } else {
        $message = "❌ Số lượng không hợp lệ!";
    }
}

// --- Lấy chi tiết sách ---
$idDauSach = isset($_GET['idds']) ? (int)$_GET['idds'] : 0;
if ($idDauSach <= 0) {
    echo "<div class='container py-5 text-center text-danger fw-bold'>Sách không tồn tại!</div>";
    exit;
}

$sql = "SELECT ds.*, tl.TenTheLoai 
        FROM dausach ds
        JOIN theloai tl ON ds.IDTheLoai = tl.IDTheLoai
        WHERE ds.IDDauSach = $idDauSach";
$data = $obj->xuatdulieu($sql);
if (!$data) {
    echo "<div class='container py-5 text-center text-danger fw-bold'>Không tìm thấy sách!</div>";
    exit;
}
$sach = $data[0];

$sqlSL = "SELECT SoLuong FROM sach WHERE IDDauSach = $idDauSach LIMIT 1";
$rs = $obj->xuatdulieu($sqlSL);
$soLuongCon = $rs ? $rs[0]['SoLuong'] : 0;

$idTheLoai = $sach['IDTheLoai'];
$sqlDeXuat = "SELECT IDDauSach, TenDauSach, HinhAnh, Gia, TacGia 
              FROM dausach 
              WHERE IDTheLoai = $idTheLoai AND IDDauSach != $idDauSach
              LIMIT 4";
$deXuat = $obj->xuatdulieu($sqlDeXuat);
?>

<main class="container my-5">

<?php if(isset($message)): ?>
    <div class="alert alert-info text-center fw-semibold"><?= $message ?></div>
<?php endif; ?>

<div class="book-detail bg-white shadow rounded-4 p-4">
  <div class="row align-items-center">
    <div class="col-md-4 mb-3 mb-md-0 text-center">
      <img src="assets/images/<?= htmlspecialchars($sach['HinhAnh']) ?>" alt="<?= htmlspecialchars($sach['TenDauSach']) ?>" class="img-fluid rounded-4 shadow-sm">
    </div>
    <div class="col-md-8">
      <h2 class="fw-bold text-primary mb-3"><?= htmlspecialchars($sach['TenDauSach']) ?></h2>
      <p><strong>Tác giả:</strong> <?= htmlspecialchars($sach['TacGia']) ?></p>
      <p><strong>NXB:</strong> <?= htmlspecialchars($sach['NXB']) ?></p>
      <p><strong>Năm XB:</strong> <?= htmlspecialchars($sach['NamXB']) ?></p>
      <p><strong>Thể loại:</strong> <?= htmlspecialchars($sach['TenTheLoai']) ?></p>
      <p class="text-danger fs-4 fw-bold mt-3"><?= number_format($sach['Gia'],0,',','.') ?>₫</p>
      <p class="text-success fw-semibold">Còn lại: <?= $soLuongCon ?> quyển</p>

      <!-- Form thêm giỏ hàng -->
      <form method="post">
        <div class="input-group mb-3" style="max-width:180px;">
            <button type="button" class="btn btn-outline-secondary" onclick="if(qty.value>1) qty.value--">−</button>
            <input type="number" name="soluong" id="qty" class="form-control text-center" min="1" max="<?= $soLuongCon ?>" value="1">
            <button type="button" class="btn btn-outline-secondary" onclick="if(qty.value<<?= $soLuongCon ?>) qty.value++">+</button>
        </div>
        <input type="hidden" name="idds" value="<?= $idDauSach ?>">
        <button type="submit" name="add_cart" class="btn btn-primary px-4 py-2 rounded-pill mt-2">
            <i class="fa fa-cart-plus me-2"></i> Thêm vào giỏ hàng
        </button>
      </form>
    </div>
  </div>

  <div class="mt-5">
    <h4 class="fw-bold text-secondary mb-3">Giới thiệu sách</h4>
    <p class="text-muted" style="line-height:1.7;"><?= nl2br(htmlspecialchars($sach['MoTa'])) ?></p>
  </div>
</div>

<!-- Sách đề xuất -->
<div class="suggest-section mt-5">
<h4 class="fw-bold text-primary mb-4 text-center">Sách đề xuất cùng thể loại</h4>
<div class="row g-4">
<?php if(!empty($deXuat) && is_array($deXuat)): ?>
<?php foreach($deXuat as $row): ?>
<div class="col-lg-3 col-md-4 col-sm-6">
  <div class="card h-100 border-0 shadow-sm suggest-card text-center">
    <img src="assets/images/<?= htmlspecialchars($row['HinhAnh']) ?>" class="img-fluid mb-2" alt="<?= htmlspecialchars($row['TenDauSach']) ?>">
    <h6 class="fw-semibold text-dark"><?= htmlspecialchars($row['TenDauSach']) ?></h6>
    <p class="text-muted small mb-1"><?= htmlspecialchars($row['TacGia']) ?></p>
    <p class="fw-bold text-danger mb-2"><?= number_format($row['Gia'],0,',','.') ?>₫</p>
    <a href="?page=chitietsach&idds=<?= $row['IDDauSach'] ?>" class="btn btn-outline-primary btn-sm rounded-pill">Xem chi tiết</a>
  </div>
</div>
<?php endforeach; ?>
<?php else: ?>
    <p class="text-center text-muted">Không có sách đề xuất</p>
<?php endif; ?>
</div>
</div>

</main>