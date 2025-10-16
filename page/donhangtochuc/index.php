<?php
// Kiểm tra đăng nhập và loại khách hàng
if (!isset($_SESSION['idkh']) || ($_SESSION['loaikh'] ?? '') !== 'Tổ chức') {
    header("Location: index.php?page=dangnhap");
    exit();
}

$obj = new database();
$IDKH = $_SESSION['idkh'];

// Lấy danh sách đơn hàng của tổ chức
$donhang = $obj->xuatdulieu("SELECT * FROM donban WHERE IDKH='$IDKH' ORDER BY NgayDat DESC");
?>

<main class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary mb-3">
            <i class="fas fa-file-invoice-dollar me-2"></i> Quản Lý Đơn Hàng
        </h2>
        <p class="text-muted">Xem danh sách đơn hàng, tình trạng và chi tiết đơn hàng của tổ chức bạn.</p>
    </div>

    <?php if ($donhang && count($donhang) > 0): ?>
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-bordered align-middle text-center mb-4">
                <thead class="table-primary">
                    <tr>
                        <th>#</th>
                        <th>Mã đơn</th>
                        <th>Người nhận</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donhang as $i => $d): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold text-primary"><?= htmlspecialchars($d['IDDonBan']) ?></td>
                            <td><?= htmlspecialchars($d['TenNguoiNhan']) ?></td>
                            <td><?= date('d/m/Y', strtotime($d['NgayDat'])) ?></td>
                            <td class="text-danger fw-bold"><?= number_format($d['TongTien'], 0, ",", ".") ?>₫</td>
                            <td>
                                <?php
                                    $status = $d['TrangThai'];
                                    $badge = "bg-secondary";
                                    if ($status === "Đang xử lý") $badge = "bg-warning text-dark";
                                    elseif ($status === "Đang giao") $badge = "bg-info text-dark";
                                    elseif ($status === "Đã giao") $badge = "bg-success";
                                    elseif ($status === "Đã hủy") $badge = "bg-danger";
                                ?>
                                <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td>
                                <a href="index.php?page=xemdonhangtc&id=<?= urlencode($d['IDDonBan']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center shadow-sm">
            <i class="fas fa-info-circle me-2"></i> Hiện chưa có đơn hàng nào được đặt.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="index.php?page=trangchutc" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại Trang Chủ
        </a>
    </div>
</main>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
