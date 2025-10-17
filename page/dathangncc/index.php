<?php
$obj = new database();

// Lấy danh sách nhà cung cấp
$nccs = $obj->xuatdulieu("SELECT * FROM nhacungcap ORDER BY TenNCC ASC");

// Lấy danh sách đầu sách
$dausachs = $obj->xuatdulieu("SELECT * FROM dausach ORDER BY TenDauSach ASC");

// Xử lý khi bấm "Tạo đơn đặt"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taoDonDat'])) {
    $IDNCC = $_POST['IDNCC'] ?? '';
    $NgayDat = date('Y-m-d');
    $chitiet = $_POST['chitiet'] ?? [];

    if (!$IDNCC || empty($chitiet)) {
        $_SESSION['msg'] = "⚠️ Vui lòng chọn nhà cung cấp và thêm ít nhất 1 đầu sách!";
        header("Location: index.php?page=dathangncc");
        exit();
    }

    // Tính tổng tiền
    $TongTien = 0;
    foreach ($chitiet as $ct) {
        $SoLuong = isset($ct['SoLuong']) ? intval($ct['SoLuong']) : 0;
        $Gia = isset($ct['Gia']) ? floatval($ct['Gia']) : 0;
        if ($SoLuong > 0) $TongTien += $SoLuong * $Gia;
    }

    // ➕ Thêm đơn đặt NCC
    $sql_don = "
        INSERT INTO dondatncc (IDNCC, NgayDat, TongTien, TrangThai)
        VALUES ('$IDNCC', '$NgayDat', $TongTien, 'Chờ thanh toán')
    ";
    $IDDonDatNCC = $obj->themxoasua_layid($sql_don);

    if ($IDDonDatNCC > 0) {
        foreach ($chitiet as $ct) {
            $IDDauSach = $ct['IDDauSach'] ?? null;
            $SoLuong = isset($ct['SoLuong']) ? intval($ct['SoLuong']) : 0;
            $Gia = isset($ct['Gia']) ? floatval($ct['Gia']) : 0;

            if ($IDDauSach && $SoLuong > 0) {
                $obj->themxoasua("
                    INSERT INTO chitietdondatncc (IDDonDatNCC, IDDauSach, SoLuong, Gia)
                    VALUES ($IDDonDatNCC, '$IDDauSach', $SoLuong, $Gia)
                ");
            }
        }

        // Chuyển sang trang thanh toán NCC
        header("Location: index.php?page=thanhtoanncc&iddon=$IDDonDatNCC");
        exit();
    } else {
        $_SESSION['msg'] = "❌ Lỗi khi tạo đơn đặt NCC!";
        header("Location: index.php?page=dathangncc");
        exit();
    }
}
?>

<div class="container my-5 shadow-lg p-4 bg-white rounded-4">
    <h2 class="text-center text-primary fw-bold mb-4">
        <i class="fa-solid fa-cart-plus me-2"></i>Đặt sách từ nhà cung cấp
    </h2>
    <div class="text-end mt-3 pb-3" style="float: right;">
        <a href="index.php?page=quanlykinhdoanh" class="btn btn-secondary">🔙 Quay về</a>
    </div>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-info text-center fw-semibold shadow-sm rounded-3"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <form method="post" id="formDonDat">
        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary">Chọn nhà cung cấp</label>
            <select name="IDNCC" class="form-select border-primary-subtle" required>
                <option value="">-- Chọn nhà cung cấp --</option>
                <?php foreach ($nccs as $ncc): ?>
                    <option value="<?= $ncc['IDNCC'] ?>"><?= htmlspecialchars($ncc['TenNCC']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr class="text-primary opacity-50">

        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-book-open me-1"></i>Danh sách sách đặt</h5>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center" id="tableSach">
                <thead class="table-primary">
                    <tr>
                        <th>Tên sách</th>
                        <th width="120">Số lượng</th>
                        <th width="140">Giá</th>
                        <th width="160">Thành tiền</th>
                        <th width="100">Hành động</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="row g-2 mt-3 align-items-center">
            <div class="col-md-8">
                <select id="selectSach" class="form-select">
                    <option value="">-- Chọn sách thêm --</option>
                    <?php foreach ($dausachs as $ds): ?>
                        <option value="<?= $ds['IDDauSach'] ?>" data-gia="<?= $ds['Gia'] ?>"><?= htmlspecialchars($ds['TenDauSach']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 text-md-start text-center">
                <button type="button" id="btnAddSach" class="btn btn-outline-primary w-100 fw-semibold">
                    <i class="fa-solid fa-plus me-1"></i>Thêm sách
                </button>
            </div>
        </div>

        <div class="text-end mt-4">
            <h5 class="fw-bold text-success">Tổng tiền: <span id="TongTien">0</span> ₫</h5>
        </div>

        <div class="text-end mt-3">
            <button type="submit" name="taoDonDat" class="btn btn-success fw-bold px-4 py-2 shadow-sm rounded-pill">
                <i class="fa-solid fa-check-circle me-2"></i>Tạo đơn đặt
            </button>
        </div>
    </form>
</div>

<script>
const tableBody = document.querySelector('#tableSach tbody');
const tongTienEl = document.getElementById('TongTien');

function updateTongTien(){
    let tong = 0;
    tableBody.querySelectorAll('tr').forEach(tr=>{
        let thanhTien = parseFloat(tr.querySelector('.thanhTien').innerText.replace(/,/g,'')) || 0;
        tong += thanhTien;
    });
    tongTienEl.innerText = new Intl.NumberFormat().format(tong);
}

document.getElementById('btnAddSach').addEventListener('click',()=>{
    const select = document.getElementById('selectSach');
    const IDDauSach = select.value;
    if(!IDDauSach) return;

    const TenSach = select.options[select.selectedIndex].text;
    const Gia = parseFloat(select.options[select.selectedIndex].dataset.gia) || 0;

    if(tableBody.querySelector(`tr[data-id="${IDDauSach}"]`)) return;

    const tr = document.createElement('tr');
    tr.dataset.id = IDDauSach;
    tr.innerHTML = `
        <td>
            ${TenSach}
            <input type="hidden" name="chitiet[${IDDauSach}][IDDauSach]" value="${IDDauSach}">
        </td>
        <td><input type="number" name="chitiet[${IDDauSach}][SoLuong]" class="form-control soLuongInput text-center border-primary-subtle" value="1" min="1"></td>
        <td><input type="number" name="chitiet[${IDDauSach}][Gia]" class="form-control giaInput text-center border-primary-subtle" value="${Gia}" min="0"></td>
        <td class="thanhTien text-success fw-semibold">${Gia.toLocaleString()}</td>
        <td><button type="button" class="btn btn-outline-danger btn-sm btnRemove"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tableBody.appendChild(tr);
    updateTongTien();
    select.value = '';
});

tableBody.addEventListener('click', e=>{
    if(e.target.closest('.btnRemove')){
        e.target.closest('tr').remove();
        updateTongTien();
    }
});

tableBody.addEventListener('input', e=>{
    const tr = e.target.closest('tr');
    if(!tr) return;
    const soLuong = parseFloat(tr.querySelector('.soLuongInput').value) || 0;
    const gia = parseFloat(tr.querySelector('.giaInput').value) || 0;
    const thanhTien = soLuong * gia;
    tr.querySelector('.thanhTien').innerText = new Intl.NumberFormat().format(thanhTien);
    updateTongTien();
});
</script>

<style>
.table th, .table td { vertical-align: middle; }
.table tr:hover { background: #f9fbff; }
.btn-outline-primary:hover { background: #0d6efd; color: #fff; }
.btn-outline-danger:hover { background: #dc3545; color: #fff; }
</style>

<script src="https://kit.fontawesome.com/a2e0b6f9f6.js" crossorigin="anonymous"></script>
