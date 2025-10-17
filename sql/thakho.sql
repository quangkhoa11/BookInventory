-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 17, 2025 lúc 08:44 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sachmoi`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonban`
--

CREATE TABLE `chitietdonban` (
  `IDDonBan` varchar(10) NOT NULL,
  `IDDauSach` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdondatncc`
--

CREATE TABLE `chitietdondatncc` (
  `IDDonDatNCC` int(11) NOT NULL,
  `IDDauSach` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `Gia` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietnhapkho`
--

CREATE TABLE `chitietnhapkho` (
  `IDPhieuNhap` varchar(10) NOT NULL,
  `IDDauSach` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietxuatkho`
--

CREATE TABLE `chitietxuatkho` (
  `IDPhieuXuat` varchar(10) NOT NULL,
  `IDDauSach` int(11) NOT NULL,
  `SoLuongYeuCau` int(11) NOT NULL,
  `SoLuongThucTe` int(11) NOT NULL,
  `DonGia` decimal(10,0) NOT NULL,
  `ThanhTien` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dausach`
--

CREATE TABLE `dausach` (
  `IDDauSach` int(11) NOT NULL,
  `TenDauSach` varchar(100) NOT NULL,
  `TacGia` varchar(100) NOT NULL,
  `NXB` varchar(100) NOT NULL,
  `NamXB` int(11) NOT NULL,
  `HinhAnh` varchar(100) NOT NULL,
  `MoTa` text NOT NULL,
  `Gia` decimal(10,0) NOT NULL,
  `ISBN` varchar(100) NOT NULL,
  `IDTheLoai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donban`
--

CREATE TABLE `donban` (
  `IDDonBan` varchar(10) NOT NULL,
  `IDKH` int(11) NOT NULL,
  `TenNguoiNhan` varchar(100) NOT NULL,
  `SDT` varchar(15) NOT NULL,
  `DiaChi` text NOT NULL,
  `NgayDat` datetime NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `TrangThai` enum('Chờ xác nhận','Đang soạn hàng','Đã xuất kho','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dondatncc`
--

CREATE TABLE `dondatncc` (
  `IDDonDatNCC` int(11) NOT NULL,
  `IDNCC` int(11) NOT NULL,
  `NgayDat` datetime NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `TrangThai` enum('Chờ thanh toán','Đã thanh toán','Đã xuất kho','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `IDKH` int(11) NOT NULL,
  `TenKH` varchar(100) NOT NULL,
  `DiaChi` varchar(100) NOT NULL,
  `SDT` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhau` int(100) NOT NULL,
  `LoaiKH` enum('Cá nhân','Tổ chức') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `IDNCC` int(11) NOT NULL,
  `TenNCC` varchar(100) NOT NULL,
  `DiaChi` varchar(100) NOT NULL,
  `SDT` varchar(40) NOT NULL,
  `Email` varchar(40) NOT NULL,
  `MatKhau` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `IDNV` int(11) NOT NULL,
  `TenNV` varchar(100) NOT NULL,
  `DiaChi` text NOT NULL,
  `SDT` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhau` varchar(40) NOT NULL,
  `VaiTro` enum('Nhân viên kho','Thủ kho','Quản lý kinh doanh','Quản trị viên') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

CREATE TABLE `phieunhap` (
  `IDPhieuNhap` varchar(10) NOT NULL,
  `IDNCC` int(11) DEFAULT NULL,
  `NgayNhap` datetime NOT NULL,
  `IDNV` int(11) DEFAULT NULL,
  `TrangThai` enum('Đang chờ duyệt','Đã duyệt','','') NOT NULL,
  `TongTien` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuxuat`
--

CREATE TABLE `phieuxuat` (
  `IDPhieuXuat` varchar(10) NOT NULL,
  `IDPhieuNhap` varchar(10) DEFAULT NULL,
  `IDDonBan` varchar(10) DEFAULT NULL,
  `IDNV` int(11) NOT NULL,
  `NgayXuat` datetime NOT NULL,
  `LoaiXuat` enum('Xuất bán hàng','Xuất trả hàng','','') NOT NULL,
  `GhiChu` text DEFAULT NULL,
  `TrangThai` enum('Đang chờ duyệt','Đã duyệt','Từ chối','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `IDSach` int(11) NOT NULL,
  `IDDauSach` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `NgayCapNhat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoankh`
--

CREATE TABLE `thanhtoankh` (
  `IDThanhToan` int(11) NOT NULL,
  `IDKH` int(11) NOT NULL,
  `IDDonBan` varchar(10) NOT NULL,
  `NgayThanhToan` datetime NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `PhuongThuc` enum('Tiền mặt','Chuyển khoản','','') NOT NULL,
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoanncc`
--

CREATE TABLE `thanhtoanncc` (
  `IDThanhToanNCC` int(11) NOT NULL,
  `IDNCC` int(11) NOT NULL,
  `IDDonDatNCC` int(11) NOT NULL,
  `NgayThanhToan` datetime NOT NULL,
  `TongTien` decimal(10,0) NOT NULL,
  `GhiChu` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theloai`
--

CREATE TABLE `theloai` (
  `IDTheLoai` int(11) NOT NULL,
  `TenTheLoai` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vitri`
--

CREATE TABLE `vitri` (
  `IDViTri` int(11) NOT NULL,
  `MaViTri` varchar(40) NOT NULL,
  `SucChua` int(11) NOT NULL,
  `GhiChu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD KEY `IDDonBan` (`IDDonBan`),
  ADD KEY `IDDauSach` (`IDDauSach`);

--
-- Chỉ mục cho bảng `chitietdondatncc`
--
ALTER TABLE `chitietdondatncc`
  ADD KEY `IDDauSach` (`IDDauSach`),
  ADD KEY `IDDonDatNCC` (`IDDonDatNCC`);

--
-- Chỉ mục cho bảng `chitietnhapkho`
--
ALTER TABLE `chitietnhapkho`
  ADD KEY `IDDauSach` (`IDDauSach`),
  ADD KEY `IDPhieuNhap` (`IDPhieuNhap`);

--
-- Chỉ mục cho bảng `chitietxuatkho`
--
ALTER TABLE `chitietxuatkho`
  ADD KEY `IDPhieuXuat` (`IDPhieuXuat`),
  ADD KEY `IDDauSach` (`IDDauSach`);

--
-- Chỉ mục cho bảng `dausach`
--
ALTER TABLE `dausach`
  ADD PRIMARY KEY (`IDDauSach`),
  ADD KEY `IDTheLoai` (`IDTheLoai`);

--
-- Chỉ mục cho bảng `donban`
--
ALTER TABLE `donban`
  ADD PRIMARY KEY (`IDDonBan`),
  ADD KEY `IDKH` (`IDKH`);

--
-- Chỉ mục cho bảng `dondatncc`
--
ALTER TABLE `dondatncc`
  ADD PRIMARY KEY (`IDDonDatNCC`),
  ADD KEY `IDNCC` (`IDNCC`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`IDKH`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`IDNCC`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`IDNV`);

--
-- Chỉ mục cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`IDPhieuNhap`),
  ADD KEY `IDNCC` (`IDNCC`),
  ADD KEY `IDNV` (`IDNV`);

--
-- Chỉ mục cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD PRIMARY KEY (`IDPhieuXuat`),
  ADD KEY `IDNV` (`IDNV`),
  ADD KEY `IDDonBan` (`IDDonBan`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`IDSach`),
  ADD KEY `IDDauSach` (`IDDauSach`);

--
-- Chỉ mục cho bảng `thanhtoankh`
--
ALTER TABLE `thanhtoankh`
  ADD PRIMARY KEY (`IDThanhToan`),
  ADD KEY `IDDonBan` (`IDDonBan`),
  ADD KEY `IDKH` (`IDKH`);

--
-- Chỉ mục cho bảng `thanhtoanncc`
--
ALTER TABLE `thanhtoanncc`
  ADD PRIMARY KEY (`IDThanhToanNCC`),
  ADD KEY `IDDonDatNCC` (`IDDonDatNCC`),
  ADD KEY `IDNCC` (`IDNCC`);

--
-- Chỉ mục cho bảng `theloai`
--
ALTER TABLE `theloai`
  ADD PRIMARY KEY (`IDTheLoai`);

--
-- Chỉ mục cho bảng `vitri`
--
ALTER TABLE `vitri`
  ADD PRIMARY KEY (`IDViTri`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `dausach`
--
ALTER TABLE `dausach`
  MODIFY `IDDauSach` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dondatncc`
--
ALTER TABLE `dondatncc`
  MODIFY `IDDonDatNCC` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `IDKH` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  MODIFY `IDNCC` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `IDNV` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sach`
--
ALTER TABLE `sach`
  MODIFY `IDSach` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanhtoankh`
--
ALTER TABLE `thanhtoankh`
  MODIFY `IDThanhToan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `thanhtoanncc`
--
ALTER TABLE `thanhtoanncc`
  MODIFY `IDThanhToanNCC` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `theloai`
--
ALTER TABLE `theloai`
  MODIFY `IDTheLoai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `vitri`
--
ALTER TABLE `vitri`
  MODIFY `IDViTri` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD CONSTRAINT `chitietdonban_ibfk_1` FOREIGN KEY (`IDDauSach`) REFERENCES `dausach` (`IDDauSach`),
  ADD CONSTRAINT `chitietdonban_ibfk_2` FOREIGN KEY (`IDDonBan`) REFERENCES `donban` (`IDDonBan`);

--
-- Các ràng buộc cho bảng `chitietdondatncc`
--
ALTER TABLE `chitietdondatncc`
  ADD CONSTRAINT `chitietdondatncc_ibfk_1` FOREIGN KEY (`IDDauSach`) REFERENCES `dausach` (`IDDauSach`),
  ADD CONSTRAINT `chitietdondatncc_ibfk_2` FOREIGN KEY (`IDDonDatNCC`) REFERENCES `dondatncc` (`IDDonDatNCC`);

--
-- Các ràng buộc cho bảng `chitietnhapkho`
--
ALTER TABLE `chitietnhapkho`
  ADD CONSTRAINT `chitietnhapkho_ibfk_1` FOREIGN KEY (`IDDauSach`) REFERENCES `dausach` (`IDDauSach`),
  ADD CONSTRAINT `chitietnhapkho_ibfk_2` FOREIGN KEY (`IDPhieuNhap`) REFERENCES `phieunhap` (`IDPhieuNhap`);

--
-- Các ràng buộc cho bảng `chitietxuatkho`
--
ALTER TABLE `chitietxuatkho`
  ADD CONSTRAINT `chitietxuatkho_ibfk_1` FOREIGN KEY (`IDPhieuXuat`) REFERENCES `phieuxuat` (`IDPhieuXuat`),
  ADD CONSTRAINT `chitietxuatkho_ibfk_2` FOREIGN KEY (`IDDauSach`) REFERENCES `dausach` (`IDDauSach`);

--
-- Các ràng buộc cho bảng `dausach`
--
ALTER TABLE `dausach`
  ADD CONSTRAINT `dausach_ibfk_1` FOREIGN KEY (`IDTheLoai`) REFERENCES `theloai` (`IDTheLoai`);

--
-- Các ràng buộc cho bảng `donban`
--
ALTER TABLE `donban`
  ADD CONSTRAINT `donban_ibfk_1` FOREIGN KEY (`IDKH`) REFERENCES `khachhang` (`IDKH`);

--
-- Các ràng buộc cho bảng `dondatncc`
--
ALTER TABLE `dondatncc`
  ADD CONSTRAINT `dondatncc_ibfk_1` FOREIGN KEY (`IDNCC`) REFERENCES `nhacungcap` (`IDNCC`);

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `phieunhap_ibfk_1` FOREIGN KEY (`IDNCC`) REFERENCES `nhacungcap` (`IDNCC`),
  ADD CONSTRAINT `phieunhap_ibfk_2` FOREIGN KEY (`IDNV`) REFERENCES `nhanvien` (`IDNV`);

--
-- Các ràng buộc cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD CONSTRAINT `phieuxuat_ibfk_1` FOREIGN KEY (`IDNV`) REFERENCES `nhanvien` (`IDNV`),
  ADD CONSTRAINT `phieuxuat_ibfk_2` FOREIGN KEY (`IDDonBan`) REFERENCES `donban` (`IDDonBan`);

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`IDDauSach`) REFERENCES `dausach` (`IDDauSach`);

--
-- Các ràng buộc cho bảng `thanhtoankh`
--
ALTER TABLE `thanhtoankh`
  ADD CONSTRAINT `thanhtoankh_ibfk_1` FOREIGN KEY (`IDDonBan`) REFERENCES `donban` (`IDDonBan`),
  ADD CONSTRAINT `thanhtoankh_ibfk_2` FOREIGN KEY (`IDKH`) REFERENCES `khachhang` (`IDKH`);

--
-- Các ràng buộc cho bảng `thanhtoanncc`
--
ALTER TABLE `thanhtoanncc`
  ADD CONSTRAINT `thanhtoanncc_ibfk_1` FOREIGN KEY (`IDDonDatNCC`) REFERENCES `dondatncc` (`IDDonDatNCC`),
  ADD CONSTRAINT `thanhtoanncc_ibfk_2` FOREIGN KEY (`IDNCC`) REFERENCES `nhacungcap` (`IDNCC`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
