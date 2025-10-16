<?php
$obj = new database();

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// =============== AJAX CẬP NHẬT SỐ LƯỢNG ===============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'update_qty') {
    header('Content-Type: application/json');
    $id = (int)$_POST['id'];
    $qty = (int)$_POST['qty'];

    $sql = "SELECT SoLuong FROM sach WHERE IDSach = $id";
    $res = $obj->xuatdulieu($sql);
    $tonkho = $res ? (int)$res[0]['SoLuong'] : 0;

    if ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id]['soluong'] = min($qty, $tonkho);
        $_SESSION['cart'][$id]['ton'] = $tonkho;
    }

    echo json_encode(['success' => true]);
    exit;
}

// =============== XÓA SẢN PHẨM ===============
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    unset($_SESSION['cart'][(int)$_GET['id']]);
    header("Location: index.php?page=giohang");
    exit;
}
?>

<main class="container my-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold text-primary">🛒 Giỏ hàng của bạn</h2>
    <p class="text-muted">Thay đổi số lượng sẽ tự động cập nhật – không cần nhấn nút!</p>
  </div>

  <?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-light border text-center py-5">
      <img src="./assets/images/empty-cart.png" width="150" class="mb-3">
      <h5>Giỏ hàng trống!</h5>
      <a href="index.php?page=danhmucsach" class="btn btn-warning mt-3">Mua sách ngay</a>
    </div>
  <?php else: ?>
    <div class="shadow-sm p-4 bg-white rounded-3">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>Ảnh</th>
            <th>Tên sách</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="cart-body">
        <?php 
          $tong = 0; 
          $tongsl = 0;
          $loaikh = $_SESSION['loaikh'] ?? 'Cá nhân';
          $giam = 0; $tile = 0;

          foreach ($_SESSION['cart'] as $id => $item): 
              $sqlTon = "SELECT SoLuong FROM sach WHERE IDSach = $id";
              $resultTon = $obj->xuatdulieu($sqlTon);
              $ton = $resultTon ? (int)$resultTon[0]['SoLuong'] : 0;
              $_SESSION['cart'][$id]['ton'] = $ton;

              $thanhtien = $item['gia'] * $item['soluong'];
              $tong += $thanhtien;
              $tongsl += $item['soluong'];

              // Tính chiết khấu từng sản phẩm nếu là Tổ chức và >=80 cuốn
              if ($loaikh === 'Tổ chức' && $item['soluong'] >= 80) {
                  $giam += $thanhtien * 0.2;
                  $tile = 20;
              }
        ?>
          <tr data-id="<?= $id ?>">
            <td><img src="./assets/images/<?= htmlspecialchars($item['hinh']) ?>" width="70"></td>
            <td><?= htmlspecialchars($item['ten']) ?></td>
            <td class="gia"><?= number_format($item['gia'], 0, ',', '.') ?></td>
            <td style="width:130px">
              <input type="number" 
                     class="form-control text-center qtyInput" 
                     min="1" 
                     max="<?= $ton ?>" 
                     value="<?= $item['soluong'] ?>">
              <small class="text-muted">Còn <?= $ton ?> cuốn</small>
            </td>
            <td class="text-danger fw-bold subtotal"><?= number_format($thanhtien, 0, ',', '.') ?></td>
            <td>
              <a href="index.php?page=giohang&action=remove&id=<?= $id ?>" 
                 class="btn btn-outline-danger btn-sm">Xóa</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <?php $tongtien = $tong - $giam; ?>

      <div class="text-end mt-4">
        <p class="mb-1">Tổng số lượng: <strong id="tongsl"><?= $tongsl ?></strong> quyển</p>
        <?php if ($loaikh === 'Tổ chức' && $giam > 0): ?>
          <p class="text-success mb-1" id="chietkhau">
            Chiết khấu (<?= $tile ?>%): -<?= number_format($giam, 0, ',', '.') ?> VND
          </p>
        <?php elseif ($loaikh === 'Tổ chức'): ?>
          <p class="text-muted mb-1" id="chietkhau">Chưa đạt mức chiết khấu (≥80 cuốn mỗi sản phẩm)</p>
        <?php else: ?>
          <p class="text-muted mb-1" id="chietkhau">Chiết khấu: Không áp dụng</p>
        <?php endif; ?>
        <h4 class="fw-bold">Tổng cộng: 
          <span class="text-danger" id="tongtien">
            <?= number_format($tongtien, 0, ',', '.') ?> VND
          </span>
        </h4>
      </div>

      <div class="text-end mt-3">
  <a href="index.php?page=danhmucsach" class="btn btn-secondary">⬅ Mua thêm</a>

  <?php if(isset($_SESSION['idkh'])): ?>
    <!-- Nếu đã đăng nhập khách hàng -->
    <form method="post" action="index.php?page=chitietdon" class="d-inline">
      <input type="hidden" name="tongtien" value="<?= $tongtien ?>">
      <input type="hidden" name="tongsl" value="<?= $tongsl ?>">
      <button type="submit" class="btn btn-success">Thanh toán</button>
    </form>
  <?php else: ?>
    <!-- Nếu chưa đăng nhập -->
    <a href="index.php?page=dangnhap" class="btn btn-warning">
      ⚠ Vui lòng đăng nhập để thanh toán
    </a>
  <?php endif; ?>
</div>

    </div>
  <?php endif; ?>

<div class="cart-extra mt-4">
  <h5>💡 Mẹo mua sắm thông minh</h5>
  <p>✅ Kiểm tra số lượng sách còn lại trước khi thêm vào giỏ.<br>
     ✅ Nhân đôi niềm vui với các sách cùng thể loại trong phần “Sách đề xuất”.<br>
     ✅ Thanh toán ngay để giữ giá ưu đãi và tránh hết hàng.</p>
  <a href="index.php?page=danhmucsach" class="btn btn-primary">
    <i class="fa-solid fa-book-open me-2"></i>Mua thêm sách
  </a>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputs = document.querySelectorAll('.qtyInput');

  inputs.forEach(input => {
    input.addEventListener('change', async function() {
      const tr = this.closest('tr');
      const id = tr.getAttribute('data-id');
      let qty = parseInt(this.value);
      const max = parseInt(this.getAttribute('max'));
      const priceEl = tr.querySelector('.gia');
      const subtotalEl = tr.querySelector('.subtotal');

      if (isNaN(qty) || qty <= 0) qty = 1;
      if (qty > max) {
        alert('Không đủ hàng trong kho (' + max + ' cuốn)');
        qty = max;
      }
      this.value = qty;

      // ===== AJAX cập nhật session =====
      try {
        const formData = new URLSearchParams({ ajax: 'update_qty', id, qty });
        const res = await fetch('index.php?page=giohang', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: formData
        });
        const data = await res.json();
        if (!data.success) return;
      } catch (err) { console.error(err); }

      // ===== Cập nhật subtotal =====
      const price = parseInt(priceEl.textContent.replace(/\D/g, '')) || 0;
      subtotalEl.textContent = (price * qty).toLocaleString('vi-VN');

      // ===== Tính tổng, chiết khấu =====
      let tong = 0, tongsl = 0, giam = 0, tile = 0;
      const loaikh = "<?= $_SESSION['loaikh'] ?? 'Cá nhân' ?>";

      document.querySelectorAll('tbody tr').forEach(row => {
        const sl = parseInt(row.querySelector('.qtyInput').value);
        const gia = parseInt(row.querySelector('.gia').textContent.replace(/\D/g, '')) || 0;
        tong += gia * sl;
        tongsl += sl;

        if (loaikh === 'Tổ chức' && sl >= 80) {
          giam += gia * sl * 0.2;
          tile = 20;
        }
      });

      const tongtien = tong - giam;
      document.getElementById('tongsl').textContent = tongsl.toLocaleString('vi-VN');
      document.getElementById('tongtien').textContent = tongtien.toLocaleString('vi-VN') + ' VND';

      if (loaikh === 'Tổ chức' && giam > 0) {
        document.getElementById('chietkhau').innerHTML = `Chiết khấu (<strong>${tile}%</strong>): -${giam.toLocaleString('vi-VN')} VND`;
        document.getElementById('chietkhau').classList.remove('text-muted');
        document.getElementById('chietkhau').classList.add('text-success');
      } else if (loaikh === 'Tổ chức') {
        document.getElementById('chietkhau').innerHTML = 'Chưa đạt mức chiết khấu (≥80 cuốn mỗi sản phẩm)';
        document.getElementById('chietkhau').classList.add('text-muted');
        document.getElementById('chietkhau').classList.remove('text-success');
      } else {
        document.getElementById('chietkhau').innerHTML = 'Chiết khấu: Không áp dụng';
        document.getElementById('chietkhau').classList.add('text-muted');
        document.getElementById('chietkhau').classList.remove('text-success');
      }
    });
  });
});
</script>

<link rel="stylesheet" href="assets/css/giohang.css">
