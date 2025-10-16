<?php
// Kiểm tra giỏ hàng
if (empty($_SESSION['cart'])) {
  echo '<div class="alert alert-warning text-center mt-5">Giỏ hàng trống!</div>';
  exit;
}

$loaikh = $_SESSION['loaikh'] ?? 'Cá nhân';

$tongtien = 0;
$tongsl = 0;
?>

<main class="container my-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold text-primary">📦 Chi tiết đơn hàng</h2>
    <p class="text-muted">Vui lòng kiểm tra kỹ trước khi xác nhận thanh toán.</p>
  </div>

  <div class="table-responsive shadow-sm mb-4">
    <table class="table table-hover align-middle text-center">
      <thead class="table-success">
        <tr>
          <th>Ảnh</th>
          <th>Tên sách</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <?php if ($loaikh === 'Tổ chức'): ?>
            <th>Chiết khấu</th>
          <?php endif; ?>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_SESSION['cart'] as $id => $item): 
          $sl = $item['soluong'];
          $gia = $item['gia'];
          $tongsl += $sl;
          $thanhtien = $gia * $sl;
          $chietkhau = 0;

          // Tính chiết khấu từng sản phẩm (Tổ chức >=80 cuốn)
          if ($loaikh === 'Tổ chức' && $sl >= 80) {
              $chietkhau = $thanhtien * 0.2;
          }

          $thanhtienSauGiam = $thanhtien - $chietkhau;
          $tongtien += $thanhtienSauGiam;
        ?>
        <tr>
          <td><img src="./assets/images/<?= htmlspecialchars($item['hinh']) ?>" width="70" class="rounded shadow-sm"></td>
          <td class="text-start"><?= htmlspecialchars($item['ten']) ?></td>
          <td><?= number_format($gia, 0, ',', '.') ?> VND</td>
          <td><?= $sl ?></td>
          <?php if ($loaikh === 'Tổ chức'): ?>
            <td>
              <?php if ($chietkhau > 0): ?>
                <span class="text-success">-<?= number_format($chietkhau, 0, ',', '.') ?> VND</span>
              <?php else: ?>
                <span class="text-muted">Không</span>
              <?php endif; ?>
            </td>
          <?php endif; ?>
          <td class="fw-bold text-danger"><?= number_format($thanhtienSauGiam, 0, ',', '.') ?> VND</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="bg-light p-4 rounded-3 shadow-sm">
    <h5 class="fw-bold mb-3">📊 Thông tin thanh toán</h5>
    <p>Tổng số lượng: <strong><?= $tongsl ?></strong> quyển</p>
    <p>Tổng tiền: <strong><?= number_format($tongtien, 0, ',', '.') ?> VND</strong></p>

    <div class="mt-4 text-end">
      <a href="index.php?page=thongtinvanchuyen" class="btn btn-primary px-4">
        <i class="fa-solid fa-credit-card me-2"></i>Tiếp tục
      </a>
      <a href="index.php?page=giohang" class="btn btn-outline-secondary px-4 ms-2">
        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại giỏ hàng
      </a>
    </div>
  </div>
</main>
