<?php
$obj = new database();
$IDKH = $_SESSION['idkh'] ?? '';
$IDDonBan = $_GET['id'] ?? '';

if ($IDDonBan === '') {
    echo "<div class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm'>ID đơn hàng không hợp lệ.</div>
            <a href='index.php?page=canhan' class='btn btn-outline-secondary mt-3'>Quay lại</a>
          </div>";
    exit;
}

// Kiểm tra đơn hàng có thuộc khách hàng hiện tại
$donban = $obj->xuatdulieu("SELECT * FROM donban WHERE IDDonBan='$IDDonBan' AND IDKH='$IDKH'");
if (!$donban) {
    echo "<div class='container mt-5 text-center'>
            <div class='alert alert-danger shadow-sm'>Đơn hàng không tồn tại hoặc không thuộc về bạn.</div>
            <a href='index.php?page=canhan' class='btn btn-outline-secondary mt-3'>Quay lại</a>
          </div>";
    exit;
}
$donban = $donban[0];

// Lấy chi tiết đơn hàng
$ct = $obj->xuatdulieu("SELECT c.*, ds.TenDauSach 
                        FROM chitietdonban c
                        JOIN dausach ds ON c.IDDauSach = ds.IDDauSach
                        WHERE c.IDDonBan = '$IDDonBan'");
?>

<style>
    .order-container {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 30px;
        transition: 0.3s;
    }
    .order-container:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .order-header {
        border-bottom: 2px solid #007bff;
        margin-bottom: 25px;
    }
    .table th {
        background-color: #007bff;
        color: #fff;
    }
    .table tbody tr:hover {
        background-color: #f9f9f9;
    }
    .info-label {
        font-weight: 600;
        color: #555;
    }
    .btn-secondary {
        border-radius: 25px;
        padding: 8px 20px;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="order-container">
        <div class="order-header mb-4">
            <h3 class="text-primary mb-0">
                <i class="fa-solid fa-file-invoice me-2"></i>
                Chi tiết đơn hàng #<?= htmlspecialchars($donban['IDDonBan']) ?>
            </h3>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <p><span class="info-label">Người nhận:</span> <?= htmlspecialchars($donban['TenNguoiNhan']) ?></p>
                <p><span class="info-label">SĐT:</span> <?= htmlspecialchars($donban['SDT']) ?></p>
                <p><span class="info-label">Địa chỉ:</span> <?= htmlspecialchars($donban['DiaChi']) ?></p>
            </div>
            <div class="col-md-6">
                <p><span class="info-label">Ngày đặt:</span> <?= date('d/m/Y', strtotime($donban['NgayDat'])) ?></p>
                <p><span class="info-label">Tổng tiền:</span> 
                    <span class="text-danger fw-bold"><?= number_format($donban['TongTien'], 0, ",", ".") ?>₫</span>
                </p>
                <p><span class="info-label">Trạng thái:</span> 
                    <span class="badge 
                        <?php 
                            if($donban['TrangThai'] === 'Đang xử lý') echo 'bg-warning text-dark';
                            elseif($donban['TrangThai'] === 'Đã giao') echo 'bg-success';
                            else echo 'bg-secondary';
                        ?>">
                        <?= htmlspecialchars($donban['TrangThai']) ?>
                    </span>
                </p>
            </div>
        </div>

        <h5 class="text-secondary mt-4 mb-3"><i class="fa-solid fa-book-open me-2"></i>Danh sách sản phẩm</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sách</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ct && count($ct) > 0): ?>
                        <?php foreach ($ct as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="text-start"><?= htmlspecialchars($item['TenDauSach']) ?></td>
                                <td><?= $item['SoLuong'] ?></td>
                                <td><?= number_format($item['DonGia'], 0, ",", ".") ?>₫</td>
                                <td class="text-danger fw-bold"><?= number_format($item['SoLuong'] * $item['DonGia'], 0, ",", ".") ?>₫</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">Không có chi tiết sản phẩm nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="index.php?page=canhan" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
</div>
