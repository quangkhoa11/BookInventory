<?php
$obj = new database();

// === Xóa nhân viên ===
if (isset($_POST['xoa'])) {
    $id = $_POST['IDNV'];
    $sql = "DELETE FROM nhanvien WHERE IDNV = '$id'";
    $obj->themxoasua($sql);
}

// === Thêm nhân viên ===
if (isset($_POST['them'])) {
    $ten = trim($_POST['TenNV']);
    $diachi = trim($_POST['DiaChi']);
    $sdt = trim($_POST['SDT']);
    $email = trim($_POST['Email']);
    $matkhau = trim($_POST['MatKhau']);
    $vaitro = $_POST['VaiTro'];

    if ($ten != '' && $email != '' && $matkhau != '') {
        $sql = "INSERT INTO nhanvien (TenNV, DiaChi, SDT, Email, MatKhau, VaiTro)
                VALUES ('$ten', '$diachi', '$sdt', '$email', '$matkhau', '$vaitro')";
        $obj->themxoasua($sql);
    }
}

// === Sửa nhân viên ===
if (isset($_POST['sua'])) {
    $id = $_POST['IDNV'];
    $ten = $_POST['TenNV'];
    $diachi = $_POST['DiaChi'];
    $sdt = $_POST['SDT'];
    $email = $_POST['Email'];
    $vaitro = $_POST['VaiTro'];

    $sql = "UPDATE nhanvien 
            SET TenNV='$ten', DiaChi='$diachi', SDT='$sdt', Email='$email', VaiTro='$vaitro' 
            WHERE IDNV='$id'";
    $obj->themxoasua($sql);
}

// === Lấy danh sách nhân viên ===
$dsnv = $obj->xuatdulieu("SELECT * FROM nhanvien");
?>

<main class="container my-5">
    <h2 class="text-primary fw-bold mb-4 text-center">👥 Quản lý tài khoản nhân viên</h2>

    <!-- Form thêm nhân viên -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-primary text-white fw-bold fs-5">
            ➕ Thêm nhân viên mới
        </div>
        <div class="card-body bg-light">
            <form method="post" class="row g-4 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Tên nhân viên</label>
                    <input type="text" name="TenNV" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Email</label>
                    <input type="email" name="Email" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Mật khẩu</label>
                    <input type="password" name="MatKhau" class="form-control shadow-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">SĐT</label>
                    <input type="text" name="SDT" class="form-control shadow-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Địa chỉ</label>
                    <input type="text" name="DiaChi" class="form-control shadow-sm">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Vai trò</label>
                    <select name="VaiTro" class="form-select shadow-sm custom-select">
                        <option value="">-- Chọn vai trò --</option>
                        <option value="Nhân viên kho">Nhân viên kho</option>
                        <option value="Thủ kho">Thủ kho</option>
                        <option value="Quản lý kinh doanh">Quản lý kinh doanh</option>
                        <option value="Quản trị viên">Quản trị viên</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-3">
                    <button type="submit" name="them" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm">
                        <i class="fa fa-plus-circle me-1"></i> Thêm nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách nhân viên -->
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white fw-bold fs-5">
            📋 Danh sách nhân viên
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>IDNV</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Vai trò</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dsnv): ?>
                        <?php foreach ($dsnv as $nv): ?>
                            <tr>
                                <form method="post">
                                    <td><?= $nv['IDNV'] ?></td>
                                    <td><input type="text" name="TenNV" value="<?= $nv['TenNV'] ?>" class="form-control form-control-sm"></td>
                                    <td><input type="email" name="Email" value="<?= $nv['Email'] ?>" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="SDT" value="<?= $nv['SDT'] ?>" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="DiaChi" value="<?= $nv['DiaChi'] ?>" class="form-control form-control-sm"></td>
                                    <td>
                                        <select name="VaiTro" class="form-select form-select-sm">
                                            <option <?= $nv['VaiTro']=='Nhân viên kho'?'selected':'' ?>>Nhân viên kho</option>
                                            <option <?= $nv['VaiTro']=='Thủ kho'?'selected':'' ?>>Thủ kho</option>
                                            <option <?= $nv['VaiTro']=='Quản lý kinh doanh'?'selected':'' ?>>Quản lý kinh doanh</option>
                                            <option <?= $nv['VaiTro']=='Quản trị viên'?'selected':'' ?>>Quản trị viên</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="IDNV" value="<?= $nv['IDNV'] ?>">
                                        <button type="submit" name="sua" class="btn btn-sm btn-success">
                                            <i class="fa fa-save"></i> Sửa
                                        </button>
                                        <button type="submit" name="xoa" class="btn btn-sm btn-danger" onclick="return confirm('Xóa nhân viên này?')">
                                            <i class="fa fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">Không có nhân viên nào!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<style>
/* Toàn trang */
.card {
    border-radius: 15px;
    overflow: hidden;
}
.card-header {
    font-size: 1.1rem;
    letter-spacing: 0.3px;
}

/* Ô nhập và select */
.form-control, .form-select {
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
}
.custom-select {
    background-color: #fff;
    font-weight: 500;
}

/* Nút bấm */
.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #3a8ef6);
    border: none;
    font-weight: 600;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0056d2, #2575fc);
    transform: translateY(-1px);
}
.btn-success {
    font-weight: 500;
    border-radius: 8px;
}
.btn-danger {
    font-weight: 500;
    border-radius: 8px;
}
.table th {
    background-color: #f8f9fa;
}
.table td, .table th {
    vertical-align: middle;
}
</style>
