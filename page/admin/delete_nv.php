<?php
include('./class/classdb.php'); // đường dẫn tới file class database

$obj = new database();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM nhanvien WHERE IDNV = '$id'";
    $obj->themxoasua($sql);
    echo 'success';
} else {
    echo 'error';
}
?>
