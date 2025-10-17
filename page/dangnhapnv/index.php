<?php
$obj = new database();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $matkhau = trim($_POST['matkhau']);

    if (empty($email) || empty($matkhau)) {
        $error = "Vui lòng nhập đầy đủ Email và Mật khẩu.";
    } else {
        // Lấy thông tin nhân viên theo email
        $sql = "SELECT * FROM nhanvien WHERE Email='$email' LIMIT 1";
        $nhanvien = $obj->xuatdulieu($sql);

        if ($nhanvien && count($nhanvien) > 0) {
            $nv = $nhanvien[0];
            // Nếu mật khẩu lưu ở DB là dạng hash thì dùng password_verify
            if (password_verify($matkhau, $nv['MatKhau']) || $matkhau === $nv['MatKhau']) {
                $_SESSION['idnv'] = $nv['IDNV'];
                $_SESSION['tennv'] = $nv['TenNV'];
                $_SESSION['vaitro'] = $nv['VaiTro'];

                // Chuyển hướng theo vai trò
                switch ($nv['VaiTro']) {
                    case 'Nhân viên kho':
                        header("Location: index.php?page=nhanvienkho");
                        break;
                    case 'Thủ kho':
                        header("Location: index.php?page=thukho");
                        break;
                    case 'Quản trị viên':
                        header("Location: index.php?page=admin");
                        break;
                    case 'Quản lý kinh doanh':
                        header("Location: index.php?page=quanlykinhdoanh");
                        break;
                    default:
                        $error = "Vai trò không hợp lệ.";
                        session_destroy();
                        break;
                }
                exit();
            } else {
                $error = "Mật khẩu không chính xác.";
            }
        } else {
            $error = "Email không tồn tại.";
        }
    }
}
?>

<main class="container my-5" style="max-width: 450px;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <h2 class="text-center mb-4 fw-bold text-primary">
                👩‍💼 Đăng nhập Nhân Viên
            </h2>

            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="Nhập email công ty">
                </div>

                <div class="mb-3">
                    <label for="matkhau" class="form-label fw-semibold">Mật khẩu</label>
                    <input type="password" name="matkhau" id="matkhau" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Dành cho nhân viên công ty The Dream.<br>
                    Liên hệ quản trị nếu chưa có tài khoản.
                </small>
            </div>
        </div>
    </div>
</main>
