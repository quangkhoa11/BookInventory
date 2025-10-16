<?php

$obj = new database();
$error = "";

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $matkhau = trim($_POST['matkhau']);
    $loai = $_POST['loai'] ?? 'khachhang'; // mặc định khách hàng

    if ($email !== "" && $matkhau !== "") {
        if ($loai === "khachhang") {
            $user = $obj->xuatdulieu("SELECT * FROM khachhang WHERE Email='$email' AND MatKhau='$matkhau'");
            if ($user && count($user) > 0) {
                $_SESSION['idkh'] = $user[0]['IDKH'];
                $_SESSION['tenkh'] = $user[0]['TenKH'];
                $_SESSION['loaikh'] = $user[0]['LoaiKH'];

                // Chuyển hướng dựa trên LoaiKH
                if ($user[0]['LoaiKH'] === 'Cá nhân') {
                    header("Location: index.php?page=trangchu");
                } else { // Tổ chức
                    header("Location: index.php?page=trangchutc");
                }
                exit();
            } else {
                $error = "Email hoặc mật khẩu khách hàng không đúng!";
            }
        } elseif ($loai === "nhacungcap") {
            $ncc = $obj->xuatdulieu("SELECT * FROM nhacungcap WHERE Email='$email' AND MatKhau='$matkhau'");
            if ($ncc && count($ncc) > 0) {
                $_SESSION['idncc'] = $ncc[0]['IDNCC'];
                $_SESSION['tenncc'] = $ncc[0]['TenNCC'];
                header("Location: ../../index.php?page=quanlyncc");
                exit();
            } else {
                $error = "Email hoặc mật khẩu nhà cung cấp không đúng!";
            }
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>


<main class="container my-5">
  <h2 class="text-center mb-4">🔑 Đăng nhập</h2>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <?php if ($error != ""): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <!-- Chọn loại tài khoản -->
        <div class="form-group mb-3 text-center">
          <label class="me-3">
            <input type="radio" name="loai" value="khachhang" checked> Khách hàng
          </label>
          <label>
            <input type="radio" name="loai" value="nhacungcap"> Nhà cung cấp
          </label>
        </div>

        <div class="form-group mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required placeholder="Nhập email...">
        </div>
        <div class="form-group mb-3">
          <label>Mật khẩu</label>
          <input type="password" name="matkhau" class="form-control" required placeholder="Nhập mật khẩu...">
        </div>
        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
      </form>

      <div class="mt-3 text-center">
        <p>Chưa có tài khoản? <a href="index.php?page=dangky">Đăng ký ngay</a></p>
      </div>
    </div>
  </div>
</main>
