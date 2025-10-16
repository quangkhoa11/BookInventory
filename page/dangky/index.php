<?php
$obj = new database();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $loaiKHEnum = $_POST['loaiKHHidden'] ?? '';

    // Lấy dữ liệu theo loại khách hàng
    if($loaiKHEnum === 'Cá nhân'){
        $tenKH = trim($_POST['tenKHCaNhan'] ?? '');
        $diaChi = trim($_POST['diaChiCaNhan'] ?? '');
        $sdt = trim($_POST['sdtCaNhan'] ?? '');
    } else {
        $tenKH = trim($_POST['tenKHTOChuc'] ?? '');
        $diaChi = trim($_POST['diaChiToChuc'] ?? '');
        $sdt = trim($_POST['sdtToChuc'] ?? '');
    }

    $email = trim($_POST['email'] ?? '');
    $matKhau = $_POST['matKhau'] ?? '';
    $matKhau2 = $_POST['matKhau2'] ?? '';

    // Kiểm tra dữ liệu bắt buộc
    if(empty($tenKH) || empty($diaChi) || empty($sdt) || empty($email) || empty($matKhau) || empty($matKhau2)){
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    }
    // Kiểm tra mật khẩu trùng khớp
    elseif($matKhau !== $matKhau2){
        echo "<script>alert('Mật khẩu nhập lại không khớp!');</script>";
    }
    else {
        // Kiểm tra email đã tồn tại
        $check = $obj->xuatdulieu("SELECT * FROM khachhang WHERE Email = '$email'");
        if($check && count($check) > 0){
            echo "<script>alert('Email đã tồn tại!');</script>";
        } else {

            // Lưu khách hàng
            $sql = "INSERT INTO khachhang (TenKH, DiaChi, SDT, Email, MatKhau, LoaiKH)
                    VALUES ('$tenKH', '$diaChi', '$sdt', '$email', '$matKhau', '$loaiKHEnum')";
            
            if($obj->themxoasua($sql)){
                echo "<script>alert('Đăng ký thành công!'); window.location='index.php?page=dangnhap';</script>";
                exit;
            } else {
                echo "<script>alert('Đăng ký thất bại! Vui lòng kiểm tra lại thông tin.');</script>";
            }
        }
    }
}
?>

<div class="register-container">
    <h2>Đăng ký tài khoản</h2>
    <div class="radio-group">
        <label><input type="radio" name="loaiKH" value="Cá nhân" onclick="showForm()" checked> Cá nhân</label>
        <label><input type="radio" name="loaiKH" value="Tổ chức" onclick="showForm()"> Tổ chức</label>
    </div>

    <form method="POST" class="register-form">
        <!-- Form cá nhân -->
        <div id="formCaNhan">
            <div class="form-group">
                <label>Tên khách hàng</label>
                <input type="text" name="tenKHCaNhan" required>
            </div>
            <div class="form-group">
                <label>Địa chỉ</label>
                <input type="text" name="diaChiCaNhan" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="sdtCaNhan" required>
            </div>
        </div>

        <!-- Form tổ chức -->
        <div id="formToChuc" style="display:none;">
            <div class="form-group">
                <label>Tên tổ chức</label>
                <input type="text" name="tenKHTOChuc" required>
            </div>
            <div class="form-group">
                <label>Địa chỉ trụ sở</label>
                <input type="text" name="diaChiToChuc" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại liên hệ</label>
                <input type="text" name="sdtToChuc" required>
            </div>
        </div>

        <!-- Thông tin chung -->
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="matKhau" required>
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="matKhau2" required>
        </div>

        <input type="hidden" name="loaiKHHidden" id="loaiKHHidden" value="Cá nhân">
        <button type="submit" class="btn-submit">Đăng ký</button>
    </form>
</div>

<script>
function showForm(){
    var loai = document.querySelector('input[name="loaiKH"]:checked').value;
    document.getElementById('loaiKHHidden').value = loai;

    var formCaNhan = document.getElementById('formCaNhan');
    var formToChuc = document.getElementById('formToChuc');

    if(loai === 'Cá nhân'){
        formCaNhan.style.display = 'block';
        formToChuc.style.display = 'none';

        // enable required cho Cá nhân
        formCaNhan.querySelectorAll('input').forEach(i => i.disabled = false);
        formToChuc.querySelectorAll('input').forEach(i => i.disabled = true);
    } else {
        formCaNhan.style.display = 'none';
        formToChuc.style.display = 'block';

        // enable required cho Tổ chức
        formToChuc.querySelectorAll('input').forEach(i => i.disabled = false);
        formCaNhan.querySelectorAll('input').forEach(i => i.disabled = true);
    }
}

// khởi tạo đúng khi load
showForm();

// đồng bộ radio
document.querySelectorAll('input[name="loaiKH"]').forEach(radio => {
    radio.addEventListener('change', showForm);
});

</script>

<style>
.register-container {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
}
.register-container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #2c3e50;
}
.radio-group {
    text-align: center;
    margin-bottom: 20px;
}
.radio-group label {
    margin-right: 20px;
    font-weight: 500;
    cursor: pointer;
}
.radio-group input[type="radio"] {
    margin-right: 6px;
}
.register-form .form-group {
    margin-bottom: 15px;
}
.register-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #34495e;
}
.register-form input[type="text"],
.register-form input[type="email"],
.register-form input[type="password"] {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 0.95rem;
    transition: border 0.3s;
}
.register-form input[type="text"]:focus,
.register-form input[type="email"]:focus,
.register-form input[type="password"]:focus {
    border-color: #3498db;
    outline: none;
}
.btn-submit {
    width: 100%;
    padding: 12px;
    background: linear-gradient(90deg, #3498db, #2980b9);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: 0.3s;
}
.btn-submit:hover {
    background: linear-gradient(90deg, #2980b9, #3498db);
}
#formCaNhan, #formToChuc {
    transition: all 0.3s ease-in-out;
}
@media (max-width: 600px) {
    .register-container {
        padding: 20px;
        margin: 30px 15px;
    }
    .radio-group label {
        display: block;
        margin-bottom: 10px;
    }
}
</style>
