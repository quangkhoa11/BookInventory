<?php
session_destroy(); // Xóa session đăng nhập
header("Location: index.php?page=trangchu"); // Quay về trang chủ
exit();
?>
