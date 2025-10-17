<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
  <script src="assets/js/jquery-3.7.1.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
  />
</head>

<body>
<?php
$homePage = 'index.php?page=trangchu';
if (isset($_SESSION['loaikh']) && $_SESSION['loaikh'] === 'Tổ chức') {
    $homePage = 'index.php?page=trangchutc';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center" href="<?= $homePage ?>">
      <img src="./assets/images/logo.png" alt="Logo" width="130" height="40" class="me-2">
    </a>

    <!-- Nút menu mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <?php if (!isset($_SESSION['idnv'])): ?>
      <!-- Menu KHÁCH HÀNG (ẩn khi nhân viên đăng nhập) -->
      <ul class="navbar-nav mx-auto">
        <li class="nav-item px-3">
          <a class="nav-link active fw-bold text-white" href="<?= $homePage ?>">Trang chủ</a>
        </li>
        <li class="nav-item px-3">
          <a class="nav-link text-white" href="index.php?page=danhmucsach">Sách</a>
        </li>
        <li class="nav-item px-3">
          <a class="nav-link text-white" href="index.php?page=giohang">Giỏ hàng</a>
        </li>
        <li class="nav-item px-3">
          <a class="nav-link text-white" href="index.php?page=lienhe">Liên hệ</a>
        </li>
      </ul>
      <?php endif; ?>

      <!-- Khu vực tài khoản -->
      <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['idkh'])): ?>
          <?php
            $profileLink = 'index.php?page=canhan';
            if(isset($_SESSION['loaikh']) && $_SESSION['loaikh'] === 'Tổ chức'){
                $profileLink = '#';
            }
          ?>
          <a href="<?= $profileLink ?>" class="text-white mr-3 fw-semibold">
            Chào, <?= htmlspecialchars($_SESSION['tenkh']) ?>
          </a>
          <a href="index.php?page=dangxuat" class="btn btn-outline-light">
            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
          </a>

        <?php elseif (isset($_SESSION['idncc'])): ?>
          <span class="text-white mr-3 fw-semibold">Chào, <?= htmlspecialchars($_SESSION['tenncc']) ?></span>
          <a href="index.php?page=dangxuat" class="btn btn-outline-light">
            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
          </a>

        <?php elseif (isset($_SESSION['idnv'])): ?>
          <!-- NHÂN VIÊN ĐĂNG NHẬP -->
          <span class="text-warning fw-semibold mr-3">
            👋 Chào, <?= htmlspecialchars($_SESSION['tennv']) ?> 
            <small class="text-white-50">[<?= htmlspecialchars($_SESSION['vaitro']) ?>]</small>
          </span>
          <a href="index.php?page=dangxuat" class="btn btn-outline-light">
            <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
          </a>

        <?php else: ?>
          <!-- Chưa đăng nhập -->
          <a href="index.php?page=dangnhap" class="btn btn-outline-light mr-2">
            <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
          </a>
          <a href="index.php?page=dangky" class="btn btn-warning px-3">
            <i class="fas fa-user-plus me-2"></i> Đăng ký
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
