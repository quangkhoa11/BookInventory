<?php
$obj = new database();

// ✅ Lấy danh sách thể loại
$theloai = $obj->xuatdulieu("SELECT * FROM theloai");

// ✅ Lấy id thể loại và từ khóa tìm kiếm
$idTheLoai = isset($_GET['idtl']) ? (int)$_GET['idtl'] : 0;
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";

// ✅ Truy vấn sách
$sql = "SELECT ds.IDDauSach, ds.TenDauSach, ds.TacGia, ds.HinhAnh, ds.Gia, tl.TenTheLoai
        FROM dausach ds
        JOIN theloai tl ON ds.IDTheLoai = tl.IDTheLoai
        WHERE 1";

if ($idTheLoai > 0) {
    $sql .= " AND ds.IDTheLoai = $idTheLoai";
}
if ($keyword != "") {
    $sql .= " AND ds.TenDauSach LIKE '%$keyword%'";
}

$data = $obj->xuatdulieu($sql);
?>

<main class="container my-5">
  <div class="row">
    <!-- 🧭 Danh mục thể loại -->
    <aside class="col-md-3 mb-4">
      <div class="card shadow-sm border-0 sticky-top" style="top: 80px;">
        <div class="card-header bg-primary text-white fw-bold text-center">
          Thể loại sách
        </div>
        <ul class="list-group list-group-flush">
          <a href="?page=danhmucsach" 
             class="list-group-item list-group-item-action <?php if($idTheLoai==0) echo 'active'; ?>">
             📚 Tất cả
          </a>
          <?php foreach ($theloai as $tl): ?>
            <a href="?page=danhmucsach&idtl=<?php echo $tl['IDTheLoai']; ?>" 
               class="list-group-item list-group-item-action <?php if($idTheLoai==$tl['IDTheLoai']) echo 'active'; ?>">
               <?php echo htmlspecialchars($tl['TenTheLoai']); ?>
            </a>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <!-- 📚 Danh sách sách -->
    <section class="col-md-9">
      <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h3 class="fw-bold text-primary mb-3 mb-md-0">
          <?php 
            if ($idTheLoai > 0) {
                $tenTL = $obj->xuatdulieu("SELECT TenTheLoai FROM theloai WHERE IDTheLoai=$idTheLoai")[0]['TenTheLoai'];
                echo "Thể loại: " . htmlspecialchars($tenTL);
            } else {
                echo "Tất cả sách";
            }
          ?>
        </h3>

        <!-- 🔍 Ô tìm kiếm -->
        <form class="d-flex" method="get">
          <input type="hidden" name="page" value="danhmucsach">
          <?php if ($idTheLoai > 0): ?>
            <input type="hidden" name="idtl" value="<?php echo $idTheLoai; ?>">
          <?php endif; ?>
          <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm sách..." 
                 value="<?php echo htmlspecialchars($keyword); ?>" style="width:220px;">
          <button class="btn btn-outline-primary" type="submit">Tìm</button>
        </form>
      </div>

      <?php if ($data != 0): ?>
        <div class="row g-4">
          <?php foreach ($data as $row): ?>
            <div class="col-lg-4 col-md-6">
              <div class="card book-card shadow-sm border-0 h-100">
                <div class="book-img">
                  <img src="assets/images/<?php echo htmlspecialchars($row['HinhAnh']); ?>" 
                       alt="<?php echo htmlspecialchars($row['TenDauSach']); ?>">
                </div>
                <div class="card-body d-flex flex-column text-center">
                  <h6 class="fw-bold text-dark mb-2 flex-grow-1 text-truncate" title="<?php echo htmlspecialchars($row['TenDauSach']); ?>">
                    <?php echo htmlspecialchars($row['TenDauSach']); ?>
                  </h6>
                  <p class="text-muted small mb-1">✍️ <?php echo htmlspecialchars($row['TacGia']); ?></p>
                  <p class="text-secondary small mb-1"><i><?php echo htmlspecialchars($row['TenTheLoai']); ?></i></p>
                  <p class="fw-bold text-danger mb-3 fs-6"><?php echo number_format($row['Gia'], 0, ',', '.'); ?>₫</p>
                  <a href="?page=chitietsach&idds=<?php echo $row['IDDauSach']; ?>" 
                     class="btn btn-primary btn-sm rounded-pill mt-auto">
                    Xem chi tiết
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-center text-muted mt-5">Không tìm thấy sách nào phù hợp.</p>
      <?php endif; ?>
    </section>
  </div>
</main>
<link rel="stylesheet" href="assets/css/danhmucsach.css">


