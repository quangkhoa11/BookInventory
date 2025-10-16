<?php
$obj = new database();
$IDKH = $_SESSION['idkh'] ?? 0;

// Thông tin cá nhân
$kh = $obj->xuatdulieu("SELECT * FROM khachhang WHERE IDKH='$IDKH'");
$kh = $kh ? $kh[0] : null;

// Danh sách đơn hàng
$donban = $obj->xuatdulieu("SELECT * FROM donban WHERE IDKH='$IDKH' ORDER BY NgayDat DESC");
?>

<style>
    .profile-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 25px;
    }
    .profile-card p {
        font-size: 16px;
        margin-bottom: 8px;
    }
    .profile-card strong {
        color: #333;
    }

    .table-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 20px;
    }

    table.table {
        border-radius: 10px;
        overflow: hidden;
    }
    table thead {
        background-color: #343a40 !important;
        color: #fff;
        text-align: center;
    }
    table tbody tr:hover {
        background-color: #f5f5f5;
        transition: 0.2s;
    }
    .btn-info {
        background-color: #0d6efd;
        border: none;
        transition: 0.3s;
    }
    .btn-info:hover {
        background-color: #084298;
    }

    h2 {
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }

    body {
        background: #f1f3f6;
    }
</style>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4"><i class="fa-solid fa-user-circle me-2"></i>Thông tin cá nhân</h2>

    <?php if($kh): ?>
    <div class="profile-card mb-5">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tên:</strong> <?= htmlspecialchars($kh['TenKH']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($kh['DiaChi']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($kh['SDT']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Email:</strong> <?= htmlspecialchars($kh['Email']) ?></p>
                <p><strong>Loại KH:</strong> <?= htmlspecialchars($kh['LoaiKH']) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <h2 class="text-center mb-4"><i class="fa-solid fa-box-open me-2"></i>Danh sách đơn hàng</h2>
    <div class="table-container">
        <?php if($donban && count($donban)>0): ?>
        <table class="table table-bordered align-middle text-center">
            <thead>
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
            <?php foreach($donban as $i => $d): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($d['IDDonBan']) ?></td>
                    <td><?= htmlspecialchars($d['TenNguoiNhan']) ?></td>
                    <td><?= date('d/m/Y', strtotime($d['NgayDat'])) ?></td>
                    <td><?= number_format($d['TongTien'],0,",",".") ?>₫</td>
                    <td>
                        <?php
                            $status = htmlspecialchars($d['TrangThai']);
                            $badgeClass = match($status) {
                                'Chờ duyệt' => 'bg-warning text-dark',
                                'Đang giao' => 'bg-info text-dark',
                                'Hoàn tất' => 'bg-success',
                                'Đã hủy' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                    </td>
                    <td>
                        <a href="index.php?page=xemdonban&id=<?= urlencode($d['IDDonBan']) ?>"
                           class="btn btn-sm btn-info">
                           <i class="fa-solid fa-eye me-1"></i> Xem
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info text-center p-4">Bạn chưa có đơn hàng nào.</div>
        <?php endif; ?>
    </div>
</div>
