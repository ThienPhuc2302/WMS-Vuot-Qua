-- =====================================================================
-- CƠ SỞ DỮ LIỆU CHUẨN DỰ ÁN QUẢN LÝ KHO BISCUITWMS (quanlykho_vuotqua)
-- Đã bổ sung bộ dữ liệu test nghiệp vụ phong phú cho 6 chức năng nhóm
-- Mã hóa: UTF-8mb4 unicode ci
-- Ngày tạo: 2026-10-27
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `quanlykho_vuotqua` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quanlykho_vuotqua`;

-- -------------------------------------------------------------
-- Bảng `vaitro`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vaitro`;
CREATE TABLE `vaitro` (
  `maVaiTro` varchar(20) NOT NULL,
  `tenVaiTro` varchar(100) NOT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  `ngayTao` date DEFAULT NULL,
  PRIMARY KEY (`maVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vaitro` (`maVaiTro`, `tenVaiTro`, `moTa`, `ngayTao`) VALUES
('VT01', 'Admin', 'Quản trị hệ thống: quản lý tài khoản, vai trò, danh mục master data', '2026-10-26'),
('VT02', 'QuanLyKho', 'Phê duyệt yêu cầu nhập/xuất, ban hành lệnh điều phối, xem báo cáo tồn kho', '2026-10-26'),
('VT03', 'NhanVienKho', 'Thực hiện nhập/xuất thực tế, lập phiếu nhập kho, phiếu xuất kho, sắp xếp vị trí kệ', '2026-10-26'),
('VT05', 'KiemKe', 'Thực hiện kiểm đếm thực tế, lập biên bản kiểm kê và ghi nhận chênh lệch', '2026-10-26'),
('VT06', 'QC', 'Kiểm định chất lượng lô nguyên liệu/thành phẩm, lập biên bản kiểm định', '2026-10-26'),
('VT07', 'BanGiamDoc', 'Xem báo cáo thống kê toàn hệ thống, phê duyệt đề nghị tiêu hủy hàng hỏng', '2026-10-26');

-- -------------------------------------------------------------
-- Bảng `nhanvien`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE `nhanvien` (
  `maNV` varchar(20) NOT NULL,
  `hoTen` varchar(100) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `vaiTro` varchar(50) NOT NULL,
  `soDienThoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  `maVaiTro` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`maNV`),
  KEY `fk_nhanvien_vaitro` (`maVaiTro`),
  CONSTRAINT `fk_nhanvien_vaitro` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nhanvien` (`maNV`, `hoTen`, `tenDangNhap`, `matKhau`, `vaiTro`, `soDienThoai`, `email`, `trangThai`, `maVaiTro`) VALUES
('NV001', 'Lê Minh Thiện Phúc', 'phuc_qc', '123456', 'QC', '0901000001', 'phuc@iuh.edu.vn', 'Hoạt động', 'VT06'),
('NV002', 'Võ Thị Tuyết Ngân', 'ngan_kiemke', '123456', 'KiemKe', '0901000002', 'ngan@iuh.edu.vn', 'Hoạt động', 'VT05'),
('NV003', 'Trần Bích Thuận', 'thuan_qlk', '123456', 'QuanLyKho', '0901000003', 'thuan@iuh.edu.vn', 'Hoạt động', 'VT02'),
('NV004', 'Trần Thanh Toàn', 'toan_qlk', '123456', 'QuanLyKho', '0901000004', 'toan@iuh.edu.vn', 'Hoạt động', 'VT02'),
('NV005', 'Trần Văn Sang', 'sang_kho', '123456', 'NhanVienKho', '0901000005', 'sang@iuh.edu.vn', 'Hoạt động', 'VT03'),
('NV006', 'Nguyễn Hữu Lộc', 'loc_kho', '123456', 'NhanVienKho', '0901000006', 'loc@iuh.edu.vn', 'Hoạt động', 'VT03'),
('NV010', 'Đỗ Thành Đạt', 'admin', '123456', 'Admin', '0909999000', 'admin@kho.vn', 'Hoạt động', 'VT01'),
('NV011', 'Nguyễn Văn An', 'quanly', '123456', 'QuanLyKho', '0909999001', 'quanly@kho.vn', 'Hoạt động', 'VT02'),
('NV012', 'Trần Thị Bình', 'nhanvienkho', '123456', 'NhanVienKho', '0909999002', 'nhanvienkho@kho.vn', 'Hoạt động', 'VT03'),
('NV013', 'Lê Hoàng Cường', 'qc', '123456', 'QC', '0909999003', 'qc@kho.vn', 'Hoạt động', 'VT06'),
('NV014', 'Phạm Minh Dũng', 'kiemke', '123456', 'KiemKe', '0909999004', 'kiemke@kho.vn', 'Hoạt động', 'VT05'),
('NV015', 'Hoàng Văn Giang', 'giamdoc', '123456', 'BanGiamDoc', '0909999005', 'giamdoc@kho.vn', 'Hoạt động', 'VT07');

-- -------------------------------------------------------------
-- Bảng `taikhoan`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE `taikhoan` (
  `maTaiKhoan` varchar(20) NOT NULL,
  `maNV` varchar(20) NOT NULL,
  `maVaiTro` varchar(20) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  `ngayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maTaiKhoan`),
  UNIQUE KEY `tenDangNhap` (`tenDangNhap`),
  KEY `fk_taikhoan_nhanvien` (`maNV`),
  KEY `fk_taikhoan_vaitro` (`maVaiTro`),
  CONSTRAINT `fk_taikhoan_nhanvien` FOREIGN KEY (`maNV`) REFERENCES `nhanvien` (`maNV`),
  CONSTRAINT `fk_taikhoan_vaitro` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `taikhoan` (`maTaiKhoan`, `maNV`, `maVaiTro`, `tenDangNhap`, `matKhau`, `trangThai`, `ngayTao`) VALUES
('TK001', 'NV001', 'VT06', 'phuc_qc', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK002', 'NV002', 'VT05', 'ngan_kiemke', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK003', 'NV003', 'VT02', 'thuan_qlk', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK004', 'NV004', 'VT02', 'toan_qlk', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK005', 'NV005', 'VT03', 'sang_kho', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK006', 'NV006', 'VT03', 'loc_kho', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK010', 'NV010', 'VT01', 'admin', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK011', 'NV011', 'VT02', 'quanly', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK012', 'NV012', 'VT03', 'nhanvienkho', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK013', 'NV013', 'VT06', 'qc', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK014', 'NV014', 'VT05', 'kiemke', '123456', 'Hoạt động', '2026-10-26 17:44:14'),
('TK015', 'NV015', 'VT07', 'giamdoc', '123456', 'Hoạt động', '2026-10-26 17:44:14');

-- -------------------------------------------------------------
-- Bảng `nhacungcap`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nhacungcap`;
CREATE TABLE `nhacungcap` (
  `maNCC` varchar(20) NOT NULL,
  `tenNCC` varchar(150) NOT NULL,
  `soDienThoai` varchar(15) DEFAULT NULL,
  `diaChi` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`maNCC`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nhacungcap` (`maNCC`, `tenNCC`, `soDienThoai`, `diaChi`, `email`) VALUES
('NCC01', 'Công ty CP Xay Lúa Mì Việt Nam (Flour Mill)', '02838123456', 'KCN Tân Bình, Tân Phú, TP.HCM', 'sales@luami.vn'),
('NCC02', 'Fonterra Brands Vietnam Co., Ltd', '02839998888', 'Tòa nhà Bitexco, Q.1, TP.HCM', 'orders@fonterra.com'),
('NCC03', 'Công ty CP Đường Biên Hòa (SBT)', '02513836123', 'KCN Biên Hòa 1, Đồng Nai', 'contact@ttcsugar.com.vn'),
('NCC04', 'Công ty TNHH Ba Huân (Trứng gà sạch)', '02838555666', 'Huyện Cần Giuộc, Long An', 'kinhdoanh@bahuan.vn'),
('NCC05', 'Puratos Grand-Place Vietnam (Socola & Phụ gia)', '02743782999', 'KCN VSIP 1, Thuận An, Bình Dương', 'info@puratos.vn'),
('NCC06', 'Công ty TNHH Mauri Lafrancaise (Men bánh mì)', '02513891444', 'KCN Amata, Biên Hòa, Đồng Nai', 'sales@abmauri.com.vn'),
('NCC07', 'Công ty TNHH Thực Phẩm Alsa Pháp', '02837772233', 'KCN Cát Lái, TP. Thủ Đức, TP.HCM', 'support@alsa.com.vn'),
('NCC08', 'Công ty Muối Tinh Bạc Liêu (Bacosalt)', '02913822111', 'Phường 2, TP. Bạc Liêu', 'sales@bacosalt.com.vn');

-- -------------------------------------------------------------
-- Bảng `nguyenlieu`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nguyenlieu`;
CREATE TABLE `nguyenlieu` (
  `maNL` varchar(20) NOT NULL,
  `tenNL` varchar(150) NOT NULL,
  `donViTinh` varchar(20) NOT NULL,
  `loaiKhoBaoQuan` varchar(50) DEFAULT 'Kho khô',
  `dinhMucToiThieu` decimal(12,2) DEFAULT 0.00,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maNL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nguyenlieu` (`maNL`, `tenNL`, `donViTinh`, `loaiKhoBaoQuan`, `dinhMucToiThieu`, `soLuongTon`, `trangThai`) VALUES
('NL001', 'Bột mì đa dụng thượng hạng số 8', 'Kg', 'Kho khô', 500.00, 8500.00, 'Hoạt động'),
('NL002', 'Bột mì chuyên dụng làm bánh quy số 11', 'Kg', 'Kho khô', 400.00, 6200.00, 'Hoạt động'),
('NL003', 'Bơ lạt Anchor New Zealand (Thùng 25kg)', 'Kg', 'Kho lạnh', 200.00, 3100.00, 'Hoạt động'),
('NL004', 'Đường tinh luyện Biên Hòa Pure', 'Kg', 'Kho khô', 600.00, 9400.00, 'Hoạt động'),
('NL005', 'Sữa bột béo nguyên kem Fonterra', 'Kg', 'Kho khô', 150.00, 2400.00, 'Hoạt động'),
('NL006', 'Trứng gà tươi công nghiệp Ba Huân', 'Quả', 'Kho lạnh', 2000.00, 15000.00, 'Hoạt động'),
('NL007', 'Socola hạt chip đen nguyên chất Hershey', 'Kg', 'Kho mát', 100.00, 1850.00, 'Hoạt động'),
('NL008', 'Bột nở làm bánh Alsa Pháp cao cấp', 'Kg', 'Kho khô', 50.00, 650.00, 'Hoạt động'),
('NL009', 'Men khô ngọt Mauri Instant', 'Kg', 'Kho mát', 50.00, 480.00, 'Hoạt động'),
('NL010', 'Hương vani tự nhiên dạng lỏng Rayner', 'Lít', 'Kho mát', 20.00, 190.00, 'Hoạt động'),
('NL011', 'Bột dừa sấy khô béo Bến Tre', 'Kg', 'Kho khô', 100.00, 1200.00, 'Hoạt động'),
('NL012', 'Muối tinh sấy I-ốt Bạc Liêu', 'Kg', 'Kho khô', 80.00, 950.00, 'Hoạt động'),
('NL013', 'Hạt mè trắng rang thơm thượng hạng', 'Kg', 'Kho khô', 60.00, 820.00, 'Hoạt động'),
('NL014', 'Bột ca cao nguyên chất Đắk Lắk', 'Kg', 'Kho khô', 100.00, 1400.00, 'Hoạt động');

-- -------------------------------------------------------------
-- Bảng `nhomthanhpham`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nhomthanhpham`;
CREATE TABLE `nhomthanhpham` (
  `maNhom` varchar(20) NOT NULL,
  `tenNhom` varchar(100) NOT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`maNhom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nhomthanhpham` (`maNhom`, `tenNhom`, `moTa`) VALUES
('NTP01', 'Bánh quy bơ cao cấp', 'Dòng bánh quy nướng bơ truyền thống hộp thiếc sang trọng quà biếu'),
('NTP02', 'Bánh quy giòn dinh dưỡng', 'Dòng bánh quy mè, lúa mì dinh dưỡng ít ngọt cho gia đình'),
('NTP03', 'Bánh quy socola kẹp kem', 'Dòng bánh quy đen kẹp kem vani và socola giới trẻ ưa chuộng'),
('NTP04', 'Bánh quy dừa & hương vị tự nhiên', 'Dòng bánh quy nướng đặc sản dừa nướng, gừng quế ấm cúng');

-- -------------------------------------------------------------
-- Bảng `thanhpham`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `thanhpham`;
CREATE TABLE `thanhpham` (
  `maTP` varchar(20) NOT NULL,
  `tenTP` varchar(150) NOT NULL,
  `tenNhom` varchar(100) DEFAULT NULL,
  `donViTinh` varchar(20) NOT NULL,
  `quyCachDongGoi` varchar(100) DEFAULT NULL,
  `soLuongToiThieu` int(11) DEFAULT 100,
  `soLuongToiDa` int(11) DEFAULT 10000,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maTP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `thanhpham` (`maTP`, `tenTP`, `tenNhom`, `donViTinh`, `quyCachDongGoi`, `soLuongToiThieu`, `soLuongToiDa`, `soLuongTon`, `trangThai`) VALUES
('TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 'Bánh quy bơ cao cấp', 'Hộp', '24 hộp/thùng', 200, 5000, 2450.00, 'Hoạt động'),
('TP002', 'Bánh quy bơ Danisa Hộp thiếc 454g', 'Bánh quy bơ cao cấp', 'Hộp', '12 hộp/thùng', 150, 4000, 1850.00, 'Hoạt động'),
('TP003', 'Bánh quy bơ Danisa Đại Hộp thiếc 681g', 'Bánh quy bơ cao cấp', 'Hộp', '6 hộp/thùng', 100, 2500, 1120.00, 'Hoạt động'),
('TP004', 'Bánh quy Cosy Marie Kinh Đô 300g', 'Bánh quy giòn dinh dưỡng', 'Gói', '20 gói/thùng', 300, 6000, 3200.00, 'Hoạt động'),
('TP005', 'Bánh quy Cosy Mè dinh dưỡng 250g', 'Bánh quy giòn dinh dưỡng', 'Gói', '24 gói/thùng', 250, 5000, 2800.00, 'Hoạt động'),
('TP006', 'Bánh quy Socola Oreo Vani 133g', 'Bánh quy socola kẹp kem', 'Thanh', '24 thanh/thùng', 400, 8000, 4500.00, 'Hoạt động'),
('TP007', 'Bánh quy kem Socola Oreo 256.5g', 'Bánh quy socola kẹp kem', 'Hộp', '12 hộp/thùng', 200, 4000, 2100.00, 'Hoạt động'),
('TP008', 'Bánh cracker AFC Lúa mì dinh dưỡng 200g', 'Bánh quy giòn dinh dưỡng', 'Hộp', '16 hộp/thùng', 250, 5000, 2600.00, 'Hoạt động'),
('TP009', 'Bánh cracker AFC Tảo biển xanh 200g', 'Bánh quy giòn dinh dưỡng', 'Hộp', '16 hộp/thùng', 200, 4500, 1950.00, 'Hoạt động'),
('TP010', 'Bánh quy dừa nướng giòn Bến Tre 180g', 'Bánh quy dừa & hương vị tự nhiên', 'Gói', '24 gói/thùng', 150, 3500, 1600.00, 'Hoạt động'),
('TP011', 'Bánh quy gừng quế Noel ấm cúng 220g', 'Bánh quy dừa & hương vị tự nhiên', 'Hộp', '12 hộp/thùng', 100, 2000, 980.00, 'Hoạt động'),
('TP012', 'Bánh quy Choreo chip socola giòn 150g', 'Bánh quy socola kẹp kem', 'Hộp', '20 hộp/thùng', 200, 4000, 2250.00, 'Hoạt động');

-- -------------------------------------------------------------
-- Bảng `kho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `kho`;
CREATE TABLE `kho` (
  `maKho` varchar(20) NOT NULL,
  `tenKho` varchar(100) NOT NULL,
  `loaiKho` varchar(50) NOT NULL,
  `dieuKienBaoQuan` varchar(50) DEFAULT 'Kho khô',
  `diaChi` varchar(200) DEFAULT NULL,
  `sucChuaToiDa` int(11) DEFAULT 1000,
  `sucChuaHienTai` int(11) DEFAULT 0,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kho` (`maKho`, `tenKho`, `loaiKho`, `dieuKienBaoQuan`, `diaChi`, `sucChuaToiDa`, `sucChuaHienTai`, `trangThai`) VALUES
('KHO_NVL_01', 'Kho Nguyên Liệu Khô 1', 'Kho nguyên liệu', 'Kho khô thoáng gió', 'Khu A1 - Nhà máy Bánh Quy', 15000, 8500, 'Hoạt động'),
('KHO_NVL_02', 'Kho Nguyên Liệu Lạnh & Mát', 'Kho nguyên liệu', 'Nhiệt độ 2-8°C, ẩm <60%', 'Khu A2 - Nhà máy Bánh Quy', 8000, 3600, 'Hoạt động'),
('KHO_TP_01', 'Kho Thành Phẩm Bánh Quy 1', 'Kho thành phẩm', 'Kho thoáng mát 22-26°C', 'Khu B1 - Nhà máy Bánh Quy', 20000, 11500, 'Hoạt động'),
('KHO_TP_02', 'Kho Thành Phẩm Bánh Quy 2', 'Kho thành phẩm', 'Kho thoáng mát 22-26°C', 'Khu B2 - Nhà máy Bánh Quy', 15000, 6800, 'Hoạt động'),
('KHO_LOI_01', 'Kho Hàng Lỗi & Cách Ly', 'Kho Hàng lỗi', 'Kho cách ly tiêu chuẩn', 'Khu C - Nhà máy Bánh Quy', 3000, 450, 'Hoạt động');

-- -------------------------------------------------------------
-- Bảng `vitrikho` (Hệ thống kệ Kho: 90 vị trí trực quan)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vitrikho`;
CREATE TABLE `vitrikho` (
  `maViTri` varchar(20) NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `day` varchar(10) NOT NULL,
  `ke` varchar(10) NOT NULL,
  `tang` varchar(10) NOT NULL,
  `sucChuaKhaDung` int(11) DEFAULT 500,
  `trangThaiSuDung` varchar(50) DEFAULT 'Đang hoạt động',
  `trangThaiChuaHang` varchar(50) DEFAULT 'Khả dụng',
  PRIMARY KEY (`maViTri`),
  KEY `fk_vitrikho_kho` (`maKho`),
  CONSTRAINT `fk_vitrikho_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vitrikho` (`maViTri`, `maKho`, `day`, `ke`, `tang`, `sucChuaKhaDung`, `trangThaiSuDung`, `trangThaiChuaHang`) VALUES
('A1-K01-T01', 'KHO_NVL_01', 'A1', 'K01', 'T01', 600, 'Đang chứa', 'Khả dụng'),
('A1-K01-T02', 'KHO_NVL_01', 'A1', 'K01', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A1-K01-T03', 'KHO_NVL_01', 'A1', 'K01', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K02-T01', 'KHO_NVL_01', 'A1', 'K02', 'T01', 600, 'Đang chứa', 'Khả dụng'),
('A1-K02-T02', 'KHO_NVL_01', 'A1', 'K02', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A1-K02-T03', 'KHO_NVL_01', 'A1', 'K02', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K03-T01', 'KHO_NVL_01', 'A1', 'K03', 'T01', 600, 'Trống', 'Khả dụng'),
('A1-K03-T02', 'KHO_NVL_01', 'A1', 'K03', 'T02', 600, 'Trống', 'Khả dụng'),
('A1-K03-T03', 'KHO_NVL_01', 'A1', 'K03', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K04-T01', 'KHO_NVL_01', 'A1', 'K04', 'T01', 600, 'Trống', 'Khả dụng'),
('A1-K04-T02', 'KHO_NVL_01', 'A1', 'K04', 'T02', 600, 'Trống', 'Khả dụng'),
('A1-K04-T03', 'KHO_NVL_01', 'A1', 'K04', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K05-T01', 'KHO_NVL_01', 'A1', 'K05', 'T01', 600, 'Trống', 'Khả dụng'),
('A1-K05-T02', 'KHO_NVL_01', 'A1', 'K05', 'T02', 600, 'Trống', 'Khả dụng'),
('A1-K05-T03', 'KHO_NVL_01', 'A1', 'K05', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K01-T01', 'KHO_NVL_01', 'A2', 'K01', 'T01', 600, 'Đang chứa', 'Khả dụng'),
('A2-K01-T02', 'KHO_NVL_01', 'A2', 'K01', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A2-K01-T03', 'KHO_NVL_01', 'A2', 'K01', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K02-T01', 'KHO_NVL_01', 'A2', 'K02', 'T01', 600, 'Đang chứa', 'Khả dụng'),
('A2-K02-T02', 'KHO_NVL_01', 'A2', 'K02', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A2-K02-T03', 'KHO_NVL_01', 'A2', 'K02', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K03-T01', 'KHO_NVL_01', 'A2', 'K03', 'T01', 600, 'Trống', 'Khả dụng'),
('A2-K03-T02', 'KHO_NVL_01', 'A2', 'K03', 'T02', 600, 'Trống', 'Khả dụng'),
('A2-K03-T03', 'KHO_NVL_01', 'A2', 'K03', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K04-T01', 'KHO_NVL_01', 'A2', 'K04', 'T01', 600, 'Trống', 'Khả dụng'),
('A2-K04-T02', 'KHO_NVL_01', 'A2', 'K04', 'T02', 600, 'Trống', 'Khả dụng'),
('A2-K04-T03', 'KHO_NVL_01', 'A2', 'K04', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K05-T01', 'KHO_NVL_01', 'A2', 'K05', 'T01', 600, 'Trống', 'Khả dụng'),
('A2-K05-T02', 'KHO_NVL_01', 'A2', 'K05', 'T02', 600, 'Trống', 'Khả dụng'),
('A2-K05-T03', 'KHO_NVL_01', 'A2', 'K05', 'T03', 600, 'Trống', 'Khả dụng'),
('L1-K01-T01', 'KHO_NVL_02', 'L1', 'K01', 'T01', 400, 'Đang chứa', 'Khả dụng'),
('L1-K01-T02', 'KHO_NVL_02', 'L1', 'K01', 'T02', 400, 'Trống', 'Khả dụng'),
('L1-K01-T03', 'KHO_NVL_02', 'L1', 'K01', 'T03', 400, 'Trống', 'Khả dụng'),
('L1-K02-T01', 'KHO_NVL_02', 'L1', 'K02', 'T01', 400, 'Đang chứa', 'Khả dụng'),
('L1-K02-T02', 'KHO_NVL_02', 'L1', 'K02', 'T02', 400, 'Trống', 'Khả dụng'),
('L1-K02-T03', 'KHO_NVL_02', 'L1', 'K02', 'T03', 400, 'Trống', 'Khả dụng'),
('L1-K03-T01', 'KHO_NVL_02', 'L1', 'K03', 'T01', 400, 'Trống', 'Khả dụng'),
('L1-K03-T02', 'KHO_NVL_02', 'L1', 'K03', 'T02', 400, 'Trống', 'Khả dụng'),
('L1-K03-T03', 'KHO_NVL_02', 'L1', 'K03', 'T03', 400, 'Trống', 'Khả dụng'),
('L1-K04-T01', 'KHO_NVL_02', 'L1', 'K04', 'T01', 400, 'Trống', 'Khả dụng'),
('L1-K04-T02', 'KHO_NVL_02', 'L1', 'K04', 'T02', 400, 'Trống', 'Khả dụng'),
('L1-K04-T03', 'KHO_NVL_02', 'L1', 'K04', 'T03', 400, 'Trống', 'Khả dụng'),
('B1-K01-T01', 'KHO_TP_01', 'B1', 'K01', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K01-T02', 'KHO_TP_01', 'B1', 'K01', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B1-K01-T03', 'KHO_TP_01', 'B1', 'K01', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K02-T01', 'KHO_TP_01', 'B1', 'K02', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K02-T02', 'KHO_TP_01', 'B1', 'K02', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B1-K02-T03', 'KHO_TP_01', 'B1', 'K02', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K03-T01', 'KHO_TP_01', 'B1', 'K03', 'T01', 800, 'Trống', 'Khả dụng'),
('B1-K03-T02', 'KHO_TP_01', 'B1', 'K03', 'T02', 800, 'Trống', 'Khả dụng'),
('B1-K03-T03', 'KHO_TP_01', 'B1', 'K03', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K04-T01', 'KHO_TP_01', 'B1', 'K04', 'T01', 800, 'Trống', 'Khả dụng'),
('B1-K04-T02', 'KHO_TP_01', 'B1', 'K04', 'T02', 800, 'Trống', 'Khả dụng'),
('B1-K04-T03', 'KHO_TP_01', 'B1', 'K04', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K05-T01', 'KHO_TP_01', 'B1', 'K05', 'T01', 800, 'Trống', 'Khả dụng'),
('B1-K05-T02', 'KHO_TP_01', 'B1', 'K05', 'T02', 800, 'Trống', 'Khả dụng'),
('B1-K05-T03', 'KHO_TP_01', 'B1', 'K05', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K01-T01', 'KHO_TP_01', 'B2', 'K01', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B2-K01-T02', 'KHO_TP_01', 'B2', 'K01', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B2-K01-T03', 'KHO_TP_01', 'B2', 'K01', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K02-T01', 'KHO_TP_01', 'B2', 'K02', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B2-K02-T02', 'KHO_TP_01', 'B2', 'K02', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B2-K02-T03', 'KHO_TP_01', 'B2', 'K02', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K03-T01', 'KHO_TP_01', 'B2', 'K03', 'T01', 800, 'Trống', 'Khả dụng'),
('B2-K03-T02', 'KHO_TP_01', 'B2', 'K03', 'T02', 800, 'Trống', 'Khả dụng'),
('B2-K03-T03', 'KHO_TP_01', 'B2', 'K03', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K04-T01', 'KHO_TP_01', 'B2', 'K04', 'T01', 800, 'Trống', 'Khả dụng'),
('B2-K04-T02', 'KHO_TP_01', 'B2', 'K04', 'T02', 800, 'Trống', 'Khả dụng'),
('B2-K04-T03', 'KHO_TP_01', 'B2', 'K04', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K05-T01', 'KHO_TP_01', 'B2', 'K05', 'T01', 800, 'Trống', 'Khả dụng'),
('B2-K05-T02', 'KHO_TP_01', 'B2', 'K05', 'T02', 800, 'Trống', 'Khả dụng'),
('B2-K05-T03', 'KHO_TP_01', 'B2', 'K05', 'T03', 800, 'Trống', 'Khả dụng'),
('C1-K01-T01', 'KHO_TP_02', 'C1', 'K01', 'T01', 750, 'Đang chứa', 'Khả dụng'),
('C1-K01-T02', 'KHO_TP_02', 'C1', 'K01', 'T02', 750, 'Trống', 'Khả dụng'),
('C1-K01-T03', 'KHO_TP_02', 'C1', 'K01', 'T03', 750, 'Trống', 'Khả dụng'),
('C1-K02-T01', 'KHO_TP_02', 'C1', 'K02', 'T01', 750, 'Trống', 'Khả dụng'),
('C1-K02-T02', 'KHO_TP_02', 'C1', 'K02', 'T02', 750, 'Trống', 'Khả dụng'),
('C1-K02-T03', 'KHO_TP_02', 'C1', 'K02', 'T03', 750, 'Trống', 'Khả dụng'),
('C1-K03-T01', 'KHO_TP_02', 'C1', 'K03', 'T01', 750, 'Trống', 'Khả dụng'),
('C1-K03-T02', 'KHO_TP_02', 'C1', 'K03', 'T02', 750, 'Trống', 'Khả dụng'),
('C1-K03-T03', 'KHO_TP_02', 'C1', 'K03', 'T03', 750, 'Trống', 'Khả dụng'),
('C1-K04-T01', 'KHO_TP_02', 'C1', 'K04', 'T01', 750, 'Trống', 'Khả dụng'),
('C1-K04-T02', 'KHO_TP_02', 'C1', 'K04', 'T02', 750, 'Trống', 'Khả dụng'),
('C1-K04-T03', 'KHO_TP_02', 'C1', 'K04', 'T03', 750, 'Trống', 'Khả dụng'),
('X1-K01-T01', 'KHO_LOI_01', 'X1', 'K01', 'T01', 300, 'Đang chứa', 'Khả dụng'),
('X1-K01-T02', 'KHO_LOI_01', 'X1', 'K01', 'T02', 300, 'Trống', 'Khả dụng'),
('X1-K01-T03', 'KHO_LOI_01', 'X1', 'K01', 'T03', 300, 'Trống', 'Khả dụng'),
('X1-K02-T01', 'KHO_LOI_01', 'X1', 'K02', 'T01', 300, 'Trống', 'Khả dụng'),
('X1-K02-T02', 'KHO_LOI_01', 'X1', 'K02', 'T02', 300, 'Trống', 'Khả dụng'),
('X1-K02-T03', 'KHO_LOI_01', 'X1', 'K02', 'T03', 300, 'Trống', 'Khả dụng');

-- -------------------------------------------------------------
-- Bảng `longuyenlieu` (25 lô nguyên liệu thực tế)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `longuyenlieu`;
CREATE TABLE `longuyenlieu` (
  `maLoNL` varchar(30) NOT NULL,
  `maNL` varchar(20) NOT NULL,
  `nhaCungCap` varchar(150) DEFAULT NULL,
  `ngaySanXuat` date DEFAULT NULL,
  `hanSuDung` date DEFAULT NULL,
  `soLuongLo` decimal(12,2) DEFAULT 0.00,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThaiKiemDinh` varchar(50) DEFAULT 'Chờ kiểm định',
  `trangThai` varchar(50) DEFAULT 'Chờ kiểm định',
  PRIMARY KEY (`maLoNL`),
  KEY `fk_longuyenlieu_nguyenlieu` (`maNL`),
  CONSTRAINT `fk_longuyenlieu_nguyenlieu` FOREIGN KEY (`maNL`) REFERENCES `nguyenlieu` (`maNL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `longuyenlieu` (`maLoNL`, `maNL`, `nhaCungCap`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
-- 1. Các lô ĐÃ KIỂM ĐỊNH ĐẠT & ĐÃ LƯU KHO (Dành cho Toàn test FEFO xuất NVL: hạn sử dụng phân tầng rõ rệt)
('LONL_20261001_001', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-09-01', '2026-11-10', 1000.00, 1000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261001_002', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-09-15', '2026-12-05', 1500.00, 1500.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261001_003', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-01', '2027-03-15', 2000.00, 2000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261005_001', 'NL003', 'Fonterra Brands Vietnam Co., Ltd', '2026-09-10', '2026-11-20', 800.00, 800.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261005_002', 'NL003', 'Fonterra Brands Vietnam Co., Ltd', '2026-10-01', '2027-01-15', 1200.00, 1200.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261008_001', 'NL004', 'Công ty CP Đường Biên Hòa (SBT)', '2026-09-05', '2026-11-25', 2500.00, 2500.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261008_002', 'NL004', 'Công ty CP Đường Biên Hòa (SBT)', '2026-10-05', '2027-04-10', 3000.00, 3000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261010_001', 'NL006', 'Công ty TNHH Ba Huân', '2026-10-08', '2026-11-05', 5000.00, 5000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261010_002', 'NL006', 'Công ty TNHH Ba Huân', '2026-10-18', '2026-11-18', 6000.00, 6000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261012_001', 'NL007', 'Puratos Grand-Place Vietnam', '2026-09-15', '2026-12-20', 600.00, 600.00, 'Đạt', 'Đã lưu kho'),

-- 2. Các lô ĐÃ KIỂM ĐỊNH ĐẠT NHƯNG CHỜ NHẬP KHO (Dành cho Sang test Lập phiếu nhập NVL - vPhieuNhapNL)
('LONL_20261022_001', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-15', '2027-04-15', 2000.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261022_002', 'NL002', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-16', '2027-04-16', 1500.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261023_001', 'NL003', 'Fonterra Brands Vietnam Co., Ltd', '2026-10-18', '2027-02-18', 900.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261023_002', 'NL004', 'Công ty CP Đường Biên Hòa (SBT)', '2026-10-20', '2027-04-20', 2200.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261024_001', 'NL005', 'Fonterra Brands Vietnam Co., Ltd', '2026-10-19', '2027-04-19', 1100.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261024_002', 'NL008', 'Công ty TNHH Thực Phẩm Alsa Pháp', '2026-10-10', '2027-10-10', 300.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261025_001', 'NL009', 'Công ty TNHH Mauri Lafrancaise', '2026-10-12', '2027-04-12', 250.00, 0.00, 'Đạt', 'Chờ nhập kho'),

-- 3. Các lô CHỜ KIỂM ĐỊNH (Dành cho Phúc QC test Lập biên bản kiểm định NVL - vKiemDinhNL: cực nhiều để test thoải mái!)
('LONL_20261026_001', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-24', '2027-04-24', 2500.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_002', 'NL002', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-25', '2027-04-25', 1800.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_003', 'NL003', 'Fonterra Brands Vietnam Co., Ltd', '2026-10-23', '2027-02-23', 1200.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_004', 'NL004', 'Công ty CP Đường Biên Hòa (SBT)', '2026-10-25', '2027-04-25', 3000.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_005', 'NL005', 'Fonterra Brands Vietnam Co., Ltd', '2026-10-22', '2027-04-22', 800.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_006', 'NL006', 'Công ty TNHH Ba Huân', '2026-10-26', '2026-11-26', 4000.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_007', 'NL007', 'Puratos Grand-Place Vietnam', '2026-10-20', '2027-01-20', 700.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LONL_20261026_008', 'NL011', 'Công ty TNHH Chế Biến Dừa Bến Tre', '2026-10-22', '2027-04-22', 600.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định');

-- -------------------------------------------------------------
-- Bảng `lothanhpham` (25 lô bánh quy thành phẩm)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lothanhpham`;
CREATE TABLE `lothanhpham` (
  `maLoTP` varchar(30) NOT NULL,
  `maTP` varchar(20) NOT NULL,
  `phanXuong` varchar(100) DEFAULT NULL,
  `ngaySanXuat` date DEFAULT NULL,
  `hanSuDung` date DEFAULT NULL,
  `soLuongLo` decimal(12,2) DEFAULT 0.00,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThaiKiemDinh` varchar(50) DEFAULT 'Chờ kiểm định',
  `trangThai` varchar(50) DEFAULT 'Chờ kiểm định',
  PRIMARY KEY (`maLoTP`),
  KEY `fk_lothanhpham_thanhpham` (`maTP`),
  CONSTRAINT `fk_lothanhpham_thanhpham` FOREIGN KEY (`maTP`) REFERENCES `thanhpham` (`maTP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lothanhpham` (`maLoTP`, `maTP`, `phanXuong`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
-- 1. Lô ĐÃ LƯU KHO (Dành cho Thuận test FEFO Điều phối xuất TP - vDieuPhoiXuatTP: có lô cận hạn và hạn xa)
('LOTP_20261002_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-02', '2026-11-20', 600.00, 600.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261005_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-05', '2026-12-15', 850.00, 850.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261015_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-15', '2027-04-15', 1000.00, 1000.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261003_001', 'TP002', 'Phân xưởng nướng số 1', '2026-10-03', '2026-11-25', 500.00, 500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261012_001', 'TP002', 'Phân xưởng nướng số 1', '2026-10-12', '2027-03-20', 800.00, 800.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261004_001', 'TP004', 'Phân xưởng đóng gói số 2', '2026-10-04', '2026-11-15', 1200.00, 1200.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261014_001', 'TP004', 'Phân xưởng đóng gói số 2', '2026-10-14', '2027-02-14', 1500.00, 1500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261006_001', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-06', '2026-11-30', 2000.00, 2000.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261016_001', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-16', '2027-03-30', 2500.00, 2500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261008_001', 'TP008', 'Phân xưởng bánh nướng số 2', '2026-10-08', '2026-12-08', 1100.00, 1100.00, 'Đạt', 'Đã lưu kho'),

-- 2. Lô ĐẠT QC CHỜ NHẬP KHO (Dành riêng cho Lộc test Lập phiếu nhập TP - vPhieuNhapTP: test nhiều đơn, xử lý hao hụt bánh vỡ)
('LOTP_20261026_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-25', '2027-04-25', 1200.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_002', 'TP002', 'Phân xưởng nướng số 1', '2026-10-25', '2027-04-25', 800.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_003', 'TP004', 'Phân xưởng đóng gói số 2', '2026-10-26', '2027-04-26', 1500.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_004', 'TP005', 'Phân xưởng đóng gói số 2', '2026-10-26', '2027-04-26', 1000.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_005', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-25', '2027-03-25', 2000.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_006', 'TP008', 'Phân xưởng bánh nướng số 2', '2026-10-26', '2027-04-26', 1400.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_007', 'TP009', 'Phân xưởng bánh nướng số 2', '2026-10-26', '2027-04-26', 1100.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_008', 'TP010', 'Phân xưởng nướng đặc sản 4', '2026-10-24', '2027-02-24', 900.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_009', 'TP011', 'Phân xưởng nướng đặc sản 4', '2026-10-25', '2027-01-25', 600.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_010', 'TP012', 'Phân xưởng nướng số 1', '2026-10-26', '2027-04-26', 1250.00, 0.00, 'Đạt', 'Chờ nhập kho'),

-- 3. Lô CHỜ KIỂM ĐỊNH (QC)
('LOTP_20261027_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-27', '2027-04-27', 1000.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định'),
('LOTP_20261027_002', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-27', '2027-03-27', 1500.00, 0.00, 'Chờ kiểm định', 'Chờ kiểm định');

-- -------------------------------------------------------------
-- Bảng `tonkho` (30 bản ghi tồn kho thực tế gắn với kệ kho)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tonkho`;
CREATE TABLE `tonkho` (
  `maTonKho` varchar(20) NOT NULL,
  `loaiHang` varchar(20) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `slTon` decimal(12,2) DEFAULT 0.00,
  `soLuongTamGiu` decimal(12,2) DEFAULT 0.00,
  `ngayCapNhat` datetime DEFAULT current_timestamp(),
  `trangThai` varchar(50) DEFAULT 'Khả dụng',
  PRIMARY KEY (`maTonKho`),
  KEY `fk_tonkho_vitri` (`maViTri`),
  CONSTRAINT `fk_tonkho_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tonkho` (`maTonKho`, `loaiHang`, `maLo`, `maViTri`, `slTon`, `soLuongTamGiu`, `ngayCapNhat`, `trangThai`) VALUES
-- Tồn kho nguyên liệu (Kho NVL 01 - Dãy A1, A2)
('TK_NL_001', 'NL', 'LONL_20261001_001', 'A1-K01-T01', 500.00, 0.00, '2026-10-20 08:30:00', 'Khả dụng'),
('TK_NL_002', 'NL', 'LONL_20261001_001', 'A1-K01-T02', 500.00, 0.00, '2026-10-20 08:30:00', 'Khả dụng'),
('TK_NL_003', 'NL', 'LONL_20261001_002', 'A1-K02-T01', 600.00, 0.00, '2026-10-21 09:00:00', 'Khả dụng'),
('TK_NL_004', 'NL', 'LONL_20261001_002', 'A1-K02-T02', 500.00, 0.00, '2026-10-21 09:00:00', 'Khả dụng'),
('TK_NL_005', 'NL', 'LONL_20261001_002', 'A1-K02-T03', 400.00, 0.00, '2026-10-21 09:00:00', 'Khả dụng'),
('TK_NL_006', 'NL', 'LONL_20261001_003', 'A1-K03-T01', 600.00, 0.00, '2026-10-22 10:15:00', 'Khả dụng'),
('TK_NL_007', 'NL', 'LONL_20261001_003', 'A1-K03-T02', 600.00, 0.00, '2026-10-22 10:15:00', 'Khả dụng'),
('TK_NL_008', 'NL', 'LONL_20261008_001', 'A2-K01-T01', 600.00, 0.00, '2026-10-22 11:00:00', 'Khả dụng'),
('TK_NL_009', 'NL', 'LONL_20261008_001', 'A2-K01-T02', 600.00, 0.00, '2026-10-22 11:00:00', 'Khả dụng'),
('TK_NL_010', 'NL', 'LONL_20261008_002', 'A2-K02-T01', 600.00, 0.00, '2026-10-23 14:00:00', 'Khả dụng'),
('TK_NL_011', 'NL', 'LONL_20261008_002', 'A2-K02-T02', 600.00, 0.00, '2026-10-23 14:00:00', 'Khả dụng'),

-- Tồn kho nguyên liệu lạnh (Kho NVL 02 - Dãy L1)
('TK_NL_012', 'NL', 'LONL_20261005_001', 'L1-K01-T01', 400.00, 0.00, '2026-10-20 15:30:00', 'Khả dụng'),
('TK_NL_013', 'NL', 'LONL_20261005_001', 'L1-K01-T02', 400.00, 0.00, '2026-10-20 15:30:00', 'Khả dụng'),
('TK_NL_014', 'NL', 'LONL_20261005_002', 'L1-K02-T01', 400.00, 0.00, '2026-10-22 16:00:00', 'Khả dụng'),
('TK_NL_015', 'NL', 'LONL_20261010_001', 'L1-K03-T01', 2500.00, 0.00, '2026-10-24 08:00:00', 'Khả dụng'),
('TK_NL_016', 'NL', 'LONL_20261010_002', 'L1-K04-T01', 3000.00, 0.00, '2026-10-25 09:00:00', 'Khả dụng'),

-- Tồn kho thành phẩm (Kho TP 01 - Dãy B1, B2)
('TK_TP_001', 'TP', 'LOTP_20261002_001', 'B1-K01-T01', 600.00, 0.00, '2026-10-20 10:00:00', 'Khả dụng'),
('TK_TP_002', 'TP', 'LOTP_20261005_001', 'B1-K01-T02', 500.00, 0.00, '2026-10-21 11:30:00', 'Khả dụng'),
('TK_TP_003', 'TP', 'LOTP_20261005_001', 'B1-K01-T03', 350.00, 0.00, '2026-10-21 11:30:00', 'Khả dụng'),
('TK_TP_004', 'TP', 'LOTP_20261015_001', 'B1-K02-T01', 500.00, 0.00, '2026-10-23 13:00:00', 'Khả dụng'),
('TK_TP_005', 'TP', 'LOTP_20261015_001', 'B1-K02-T02', 500.00, 0.00, '2026-10-23 13:00:00', 'Khả dụng'),
('TK_TP_006', 'TP', 'LOTP_20261003_001', 'B1-K03-T01', 500.00, 0.00, '2026-10-20 14:00:00', 'Khả dụng'),
('TK_TP_007', 'TP', 'LOTP_20261012_001', 'B1-K04-T01', 400.00, 0.00, '2026-10-22 15:00:00', 'Khả dụng'),
('TK_TP_008', 'TP', 'LOTP_20261012_001', 'B1-K04-T02', 400.00, 0.00, '2026-10-22 15:00:00', 'Khả dụng'),
('TK_TP_009', 'TP', 'LOTP_20261004_001', 'B2-K01-T01', 600.00, 0.00, '2026-10-21 16:00:00', 'Khả dụng'),
('TK_TP_010', 'TP', 'LOTP_20261004_001', 'B2-K01-T02', 600.00, 0.00, '2026-10-21 16:00:00', 'Khả dụng'),
('TK_TP_011', 'TP', 'LOTP_20261006_001', 'B2-K02-T01', 800.00, 0.00, '2026-10-22 09:30:00', 'Khả dụng'),
('TK_TP_012', 'TP', 'LOTP_20261006_001', 'B2-K02-T02', 700.00, 0.00, '2026-10-22 09:30:00', 'Khả dụng'),
('TK_TP_013', 'TP', 'LOTP_20261006_001', 'B2-K02-T03', 500.00, 0.00, '2026-10-22 09:30:00', 'Khả dụng'),
('TK_TP_014', 'TP', 'LOTP_20261008_001', 'B2-K03-T01', 600.00, 0.00, '2026-10-23 10:45:00', 'Khả dụng'),
('TK_TP_015', 'TP', 'LOTP_20261008_001', 'B2-K03-T02', 500.00, 0.00, '2026-10-23 10:45:00', 'Khả dụng');

-- -------------------------------------------------------------
-- Bảng `tieuchikiemdinh`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tieuchikiemdinh`;
CREATE TABLE `tieuchikiemdinh` (
  `maTieuChi` varchar(20) NOT NULL,
  `tenTieuChi` varchar(150) NOT NULL,
  `quyChuan` varchar(150) NOT NULL,
  `loaiHang` varchar(50) DEFAULT 'Nguyên liệu',
  PRIMARY KEY (`maTieuChi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tieuchikiemdinh` (`maTieuChi`, `tenTieuChi`, `quyChuan`, `loaiHang`) VALUES
('TC-BM01', 'Độ ẩm bột mì', 'Độ ẩm ≤ 14.0%', 'Nguyên liệu'),
('TC-BM02', 'Chỉ số Gluten ướt', 'Gluten từ 9.5% đến 11.5%', 'Nguyên liệu'),
('TC-BL01', 'Hàm lượng chất béo bơ lạt', 'Chất béo sữa ≥ 82.0%', 'Nguyên liệu'),
('TC-DG01', 'Độ tinh khiết đường Saccharose', 'Độ Pol ≥ 99.7%', 'Nguyên liệu'),
('TC-TG01', 'Độ tươi trứng gà (chỉ số Haugh)', 'Haugh unit ≥ 72 (Hạng AA)', 'Nguyên liệu'),
('TC-TP01', 'Cảm quan độ giòn và mùi vị bánh', 'Bánh vàng đều, giòn xốp, thơm bơ tự nhiên', 'Thành phẩm'),
('TC-TP02', 'Độ ẩm bánh quy thành phẩm', 'Độ ẩm bánh ≤ 3.5%', 'Thành phẩm'),
('TC-TP03', 'Quy cách đóng gói & kín khí', 'Bao bì nguyên vẹn, hút chân không đạt chuẩn', 'Thành phẩm');

-- -------------------------------------------------------------
-- Bảng `bienbankiemdinh`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `bienbankiemdinh`;
CREATE TABLE `bienbankiemdinh` (
  `maBienBan` varchar(20) NOT NULL,
  `loaiKiemDinh` tinyint(4) DEFAULT 1,
  `maLo` varchar(30) NOT NULL,
  `ngayKiemDinh` datetime DEFAULT current_timestamp(),
  `nguoiKiemDinh` varchar(20) DEFAULT NULL,
  `tieuChiDanhGia` text DEFAULT NULL,
  `ketQuaTongThe` varchar(20) DEFAULT 'Đạt',
  `lyDoKhongDat` varchar(500) DEFAULT NULL,
  `ghiChu` varchar(500) DEFAULT NULL,
  `trangThaiBienBan` varchar(50) DEFAULT 'Đã hoàn tất',
  PRIMARY KEY (`maBienBan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bienbankiemdinh` (`maBienBan`, `loaiKiemDinh`, `maLo`, `ngayKiemDinh`, `nguoiKiemDinh`, `tieuChiDanhGia`, `ketQuaTongThe`, `lyDoKhongDat`, `ghiChu`, `trangThaiBienBan`) VALUES
('BBKD_NL_20261001_01', 1, 'LONL_20261001_001', '2026-09-02 08:30:00', 'NV001', 'Độ ẩm 13.2%, gluten 10.5%, màu trắng ngà, không sâu mọt', 'Đạt', NULL, 'Lô bột mì số 8 đạt chất lượng cao', 'Đã hoàn tất'),
('BBKD_NL_20261005_01', 1, 'LONL_20261005_001', '2026-09-12 09:15:00', 'NV001', 'Chất béo 82.5%, nhiệt độ kho lạnh giao nhận 4°C', 'Đạt', NULL, 'Bơ lạt Anchor giữ lạnh tốt', 'Đã hoàn tất'),
('BBKD_NL_20261008_01', 1, 'LONL_20261008_001', '2026-09-06 14:00:00', 'NV013', 'Độ tinh khiết 99.8%, hạt đường khô ráo, trắng sáng', 'Đạt', NULL, 'Đường Biên Hòa nguyên bao bì', 'Đã hoàn tất'),
('BBKD_NL_20261010_01', 1, 'LONL_20261010_001', '2026-10-09 10:20:00', 'NV001', 'Vỏ trứng sạch, lòng đỏ đặc, chỉ số Haugh 75', 'Đạt', NULL, 'Trứng gà Ba Huân đạt chuẩn AA', 'Đã hoàn tất'),
('BBKD_NL_20261012_01', 1, 'LONL_20261012_001', '2026-09-16 11:30:00', 'NV013', 'Socola chip đồng đều, không bị tan chảy', 'Đạt', NULL, 'Socola hạt chip Hershey đạt', 'Đã hoàn tất'),
('BBKD_TP_20261002_01', 2, 'LOTP_20261002_001', '2026-10-02 09:00:00', 'NV001', 'Bánh vàng giòn thơm, độ ẩm 2.8%, bao bì kín', 'Đạt', NULL, 'Danisa 200g đạt chuẩn xuất xưởng', 'Đã hoàn tất'),
('BBKD_TP_20261006_01', 2, 'LOTP_20261006_001', '2026-10-06 10:15:00', 'NV013', 'Vỏ socola giòn đắng, nhân kem vani mịn ngọt', 'Đạt', NULL, 'Oreo Vani đạt chuẩn', 'Đã hoàn tất');

-- -------------------------------------------------------------
-- Bảng `lenhdieuphoinhap` (20 lệnh điều phối tiếp nhận)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lenhdieuphoinhap`;
CREATE TABLE `lenhdieuphoinhap` (
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `loaiHang` varchar(20) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `maKhoTiepNhan` varchar(20) NOT NULL,
  `soLuongPhanBo` decimal(12,2) NOT NULL,
  `ngayDieuPhoi` datetime DEFAULT current_timestamp(),
  `nguoiDieuPhoi` varchar(20) DEFAULT NULL,
  `lyDoGiaiTrinh` varchar(255) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Chờ tiếp nhận',
  PRIMARY KEY (`maLenhDieuPhoi`),
  KEY `fk_lenhdieuphoinhap_kho` (`maKhoTiepNhan`),
  CONSTRAINT `fk_lenhdieuphoinhap_kho` FOREIGN KEY (`maKhoTiepNhan`) REFERENCES `kho` (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lenhdieuphoinhap` (`maLenhDieuPhoi`, `loaiHang`, `maLo`, `maKhoTiepNhan`, `soLuongPhanBo`, `ngayDieuPhoi`, `nguoiDieuPhoi`, `lyDoGiaiTrinh`, `trangThai`) VALUES
-- 1. Lệnh nhập THÀNH PHẨM (Dành riêng cho Lộc test vPhieuNhapTP - Lập phiếu nhập thành phẩm: Có 7 lệnh chờ nhập kho cực xịn)
('LDPN_TP_001', 'TP', 'LOTP_20261026_001', 'KHO_TP_01', 1200.00, '2026-10-26 08:30:00', 'NV003', 'Tiếp nhận mẻ bánh quy bơ Danisa 200g từ PX1', 'Chờ nhập kho'),
('LDPN_TP_002', 'TP', 'LOTP_20261026_002', 'KHO_TP_01', 800.00, '2026-10-26 09:15:00', 'NV003', 'Tiếp nhận mẻ bánh quy bơ Danisa Hộp thiếc 454g', 'Chờ tiếp nhận'),
('LDPN_TP_003', 'TP', 'LOTP_20261026_003', 'KHO_TP_01', 1500.00, '2026-10-26 10:00:00', 'NV004', 'Nhập kho bánh quy Cosy Marie Kinh Đô 300g đóng thùng', 'Chờ nhập kho'),
('LDPN_TP_004', 'TP', 'LOTP_20261026_004', 'KHO_TP_01', 1000.00, '2026-10-26 11:20:00', 'NV004', 'Tiếp nhận bánh quy Cosy Mè dinh dưỡng từ PX2', 'Chờ nhập kho'),
('LDPN_TP_005', 'TP', 'LOTP_20261026_005', 'KHO_TP_01', 2000.00, '2026-10-26 13:45:00', 'NV003', 'Nhập kho lô bánh quy Oreo Vani từ xưởng kẹp kem PX3', 'Chờ nhập kho'),
('LDPN_TP_006', 'TP', 'LOTP_20261026_006', 'KHO_TP_01', 1400.00, '2026-10-26 14:30:00', 'NV004', 'Tiếp nhận bánh cracker AFC Lúa mì dinh dưỡng', 'Chờ nhập kho'),
('LDPN_TP_007', 'TP', 'LOTP_20261026_007', 'KHO_TP_02', 1100.00, '2026-10-26 15:10:00', 'NV003', 'Tiếp nhận mẻ bánh cracker AFC Tảo biển xanh', 'Chờ nhập kho'),
('LDPN_TP_008', 'TP', 'LOTP_20261026_008', 'KHO_TP_02', 900.00, '2026-10-26 15:50:00', 'NV004', 'Nhập kho bánh quy dừa nướng Bến Tre thơm giòn', 'Chờ nhập kho'),
('LDPN_TP_009', 'TP', 'LOTP_20261026_009', 'KHO_TP_02', 600.00, '2026-10-26 16:30:00', 'NV003', 'Nhập mẻ bánh gừng quế mùa vụ Giáng Sinh', 'Chờ nhập kho'),
('LDPN_TP_010', 'TP', 'LOTP_20261026_010', 'KHO_TP_01', 1250.00, '2026-10-26 17:00:00', 'NV004', 'Tiếp nhận bánh quy socola chip Choreo giòn tan', 'Chờ tiếp nhận'),
('LDPN_TP_DONE_01', 'TP', 'LOTP_20261002_001', 'KHO_TP_01', 600.00, '2026-10-02 08:30:00', 'NV003', 'Đã nhập xong mẻ Danisa 200g đầu tháng', 'Hoàn tất nhập kho'),
('LDPN_TP_DONE_02', 'TP', 'LOTP_20261006_001', 'KHO_TP_01', 2000.00, '2026-10-06 09:00:00', 'NV004', 'Đã nhập xong lô bánh Oreo kem vani', 'Hoàn tất nhập kho'),

-- 2. Lệnh nhập NGUYÊN LIỆU (Dành cho Sang test vPhieuNhapNL - Lập phiếu nhập nguyên liệu: 6 lệnh chờ nhập)
('LDPN_NL_001', 'NL', 'LONL_20261022_001', 'KHO_NVL_01', 2000.00, '2026-10-22 09:00:00', 'NV004', 'Điều phối nhập kho 2 tấn bột mì số 8 từ NCC Lúa Mì VN', 'Chờ nhập kho'),
('LDPN_NL_002', 'NL', 'LONL_20261022_002', 'KHO_NVL_01', 1500.00, '2026-10-22 10:30:00', 'NV004', 'Điều phối 1.5 tấn bột mì số 11 làm bánh Cosy', 'Chờ nhập kho'),
('LDPN_NL_003', 'NL', 'LONL_20261023_001', 'KHO_NVL_02', 900.00, '2026-10-23 11:15:00', 'NV003', 'Nhập kho lạnh 900kg bơ lạt Anchor giao bằng xe lạnh', 'Chờ nhập kho'),
('LDPN_NL_004', 'NL', 'LONL_20261023_002', 'KHO_NVL_01', 2200.00, '2026-10-23 14:00:00', 'NV004', 'Điều phối nhập đường tinh luyện Biên Hòa Pure', 'Chờ nhập kho'),
('LDPN_NL_005', 'NL', 'LONL_20261024_001', 'KHO_NVL_01', 1100.00, '2026-10-24 15:30:00', 'NV003', 'Nhập sữa bột béo nguyên kem Fonterra New Zealand', 'Chờ nhập kho'),
('LDPN_NL_006', 'NL', 'LONL_20261024_002', 'KHO_NVL_01', 300.00, '2026-10-24 16:15:00', 'NV004', 'Nhập bột nở Alsa Pháp phục vụ dây chuyền nướng', 'Chờ tiếp nhận'),
('LDPN_NL_DONE_01', 'NL', 'LONL_20261001_001', 'KHO_NVL_01', 1000.00, '2026-10-01 08:30:00', 'NV004', 'Đã nhập xong bột mì số 8 lô 001', 'Hoàn tất nhập kho'),
('LDPN_NL_DONE_02', 'NL', 'LONL_20261005_001', 'KHO_NVL_02', 800.00, '2026-10-05 09:45:00', 'NV003', 'Đã nhập xong bơ lạt Anchor lô 001', 'Hoàn tất nhập kho');

-- -------------------------------------------------------------
-- Bảng `phieunhapkho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieunhapkho`;
CREATE TABLE `phieunhapkho` (
  `maPNK` varchar(30) NOT NULL,
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `ngayNhapThucTe` datetime DEFAULT current_timestamp(),
  `nguoiLap` varchar(20) DEFAULT NULL,
  `slThucNhap` decimal(12,2) NOT NULL,
  `soLuongChenhLech` decimal(12,2) DEFAULT 0.00,
  `lyDoChenhLech` varchar(255) DEFAULT NULL,
  `ghiChu` varchar(500) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoàn tất',
  PRIMARY KEY (`maPNK`),
  KEY `fk_phieunhapkho_lenh` (`maLenhDieuPhoi`),
  KEY `fk_phieunhapkho_vitri` (`maViTri`),
  CONSTRAINT `fk_phieunhapkho_lenh` FOREIGN KEY (`maLenhDieuPhoi`) REFERENCES `lenhdieuphoinhap` (`maLenhDieuPhoi`),
  CONSTRAINT `fk_phieunhapkho_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phieunhapkho` (`maPNK`, `maLenhDieuPhoi`, `maViTri`, `ngayNhapThucTe`, `nguoiLap`, `slThucNhap`, `soLuongChenhLech`, `lyDoChenhLech`, `ghiChu`, `trangThai`) VALUES
('PNK_TP_20261002_001', 'LDPN_TP_DONE_01', 'B1-K01-T01', '2026-10-02 09:30:00', 'NV006', 600.00, 0.00, NULL, 'Bao bì nguyên vẹn, xếp ngay ngắn lên kệ B1-K01-T01', 'Hoàn tất'),
('PNK_TP_20261006_001', 'LDPN_TP_DONE_02', 'B2-K02-T01', '2026-10-06 10:45:00', 'NV006', 1980.00, -20.00, 'Hao hụt 20 thanh do móp méo va đập trong khay xe nâng', 'Đã lập biên bản hao hụt', 'Hoàn tất'),
('PNK_NL_20261001_001', 'LDPN_NL_DONE_01', 'A1-K01-T01', '2026-10-01 09:15:00', 'NV005', 1000.00, 0.00, NULL, 'Đủ 40 bao 25kg bột mì số 8, kê pallet gỗ sạch sẽ', 'Hoàn tất'),
('PNK_NL_20261005_001', 'LDPN_NL_DONE_02', 'L1-K01-T01', '2026-10-05 10:30:00', 'NV005', 800.00, 0.00, NULL, 'Bơ lạt Anchor đưa thẳng vào kho mát 4 độ C', 'Hoàn tất');

-- -------------------------------------------------------------
-- Bảng `phieuyeucaunhap`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuyeucaunhap`;
CREATE TABLE `phieuyeucaunhap` (
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `ngayYeuCau` date NOT NULL,
  `ngayGiaoDuKien` date DEFAULT NULL,
  `nguoiYeuCau` varchar(100) DEFAULT NULL,
  `loaiYeuCau` varchar(50) DEFAULT 'Nhập nguyên liệu từ NCC',
  `trangThai` varchar(50) DEFAULT 'Chờ phê duyệt',
  PRIMARY KEY (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phieuyeucaunhap` (`maPhieuYeuCau`, `ngayYeuCau`, `ngayGiaoDuKien`, `nguoiYeuCau`, `loaiYeuCau`, `trangThai`) VALUES
('PYCN-2026-101', '2026-10-22', '2026-10-25', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Đã tiếp nhận'),
('PYCN-2026-102', '2026-10-24', '2026-10-28', 'Nguyễn Hữu Lộc', 'Nhập thành phẩm từ phân xưởng', 'Đã tiếp nhận'),
('PYCN-2026-103', '2026-10-26', '2026-10-30', 'Trần Bích Thuận', 'Nhập nguyên liệu bổ sung', 'Chờ phê duyệt');

-- -------------------------------------------------------------
-- Bảng `phieuyeucauxuat` (10 yêu cầu xuất cho xưởng & đại lý)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuyeucauxuat`;
CREATE TABLE `phieuyeucauxuat` (
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `loaiYeuCau` varchar(20) NOT NULL,
  `donViYeuCau` varchar(150) NOT NULL,
  `ngayYeuCau` date NOT NULL,
  `nguoiYeuCau` varchar(100) DEFAULT NULL,
  `lyDoXuat` varchar(255) DEFAULT NULL,
  `tongSoLuong` decimal(14,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Chờ điều phối',
  PRIMARY KEY (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phieuyeucauxuat` (`maPhieuYeuCau`, `loaiYeuCau`, `donViYeuCau`, `ngayYeuCau`, `nguoiYeuCau`, `lyDoXuat`, `tongSoLuong`, `trangThai`) VALUES
-- 1. Yêu cầu xuất NGUYÊN LIỆU (Dành cho Toàn test vDieuPhoiXuatNL - Điều phối xuất NVL FEFO: 5 phiếu chờ điều phối)
('PYCX_NL_20261027_001', 'NL', 'Phân xưởng nướng bánh số 1', '2026-10-27', 'Quản đốc Nguyễn Văn Hùng', 'Cấp bột mì số 8 và bơ lạt cho mẻ nướng Danisa ca sáng', 1500.00, 'Chờ điều phối'),
('PYCX_NL_20261027_002', 'NL', 'Phân xưởng đóng gói số 2', '2026-10-27', 'Tổ trưởng Trần Thị Mai', 'Xuất bột mì số 11 và đường tinh luyện cho bánh Cosy Marie', 2000.00, 'Chờ điều phối'),
('PYCX_NL_20261027_003', 'NL', 'Phân xưởng bánh kẹp kem số 3', '2026-10-27', 'Kỹ thuật viên Lê Quốc Bảo', 'Cấp bột mì, trứng gà tươi và socola chip làm bánh Oreo', 2500.00, 'Chờ điều phối'),
('PYCX_NL_20261027_004', 'NL', 'Phân xưởng nướng đặc sản 4', '2026-10-27', 'Trưởng ca Đặng Thanh Tâm', 'Cấp bột dừa sấy béo và đường làm bánh quy dừa Bến Tre', 800.00, 'Chờ điều phối'),
('PYCX_NL_20261027_005', 'NL', 'Phân xưởng nướng bánh số 1', '2026-10-27', 'Quản đốc Nguyễn Văn Hùng', 'Cấp sữa bột Fonterra và bơ lạt làm mẻ bánh Danisa đại', 900.00, 'Chờ điều phối'),

-- 2. Yêu cầu xuất THÀNH PHẨM (Dành cho Thuận test vDieuPhoiXuatTP - Điều phối xuất TP: 5 phiếu cho các đại lý/siêu thị)
('PYCX_TP_20261027_001', 'TP', 'Hệ thống Siêu thị Co.opmart Miền Nam', '2026-10-27', 'Trưởng phòng KD Phạm Thu Hà', 'Giao đơn hàng theo hợp đồng tuần 44 - Danisa & Cosy', 1300.00, 'Chờ điều phối'),
('PYCX_TP_20261027_002', 'TP', 'Chuỗi Cửa hàng Bách Hóa Xanh TP.HCM', '2026-10-27', 'Chuyên viên mua hàng Đỗ Quốc Nam', 'Xuất bánh quy Oreo kem vani và cracker AFC', 1800.00, 'Chờ điều phối'),
('PYCX_TP_20261027_003', 'TP', 'Hệ thống Siêu thị WinMart Toàn Quốc', '2026-10-27', 'Giám đốc thu mua Vũ Hồng Quân', 'Xuất đơn Danisa hộp thiếc 454g và Cosy mè dinh dưỡng', 1500.00, 'Chờ điều phối'),
('PYCX_TP_20261027_004', 'TP', 'Nhà phân phối Bánh kẹo Miền Đông (Đồng Nai)', '2026-10-27', 'Đại diện NPP Huỳnh Tấn Đạt', 'Xuất bán sỉ bánh quy Danisa 200g và Choreo chip', 1600.00, 'Chờ điều phối'),
('PYCX_TP_20261027_005', 'TP', 'Đại lý Bánh kẹo Xuất khẩu Tân Bình', '2026-10-27', 'Giám đốc đại lý Ngô Xuân Phúc', 'Xuất hàng bánh quy dừa Bến Tre và gừng quế mùa lễ hội', 900.00, 'Chờ điều phối');

-- -------------------------------------------------------------
-- Bảng `chitietyeucauxuat` (Chi tiết mặt hàng từng phiếu yêu cầu)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietyeucauxuat`;
CREATE TABLE `chitietyeucauxuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `maMatHang` varchar(20) NOT NULL,
  `tenMatHang` varchar(150) NOT NULL,
  `soLuongYeuCau` decimal(12,2) NOT NULL,
  `donViTinh` varchar(20) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_chitietyc_phieu` (`maPhieuYeuCau`),
  CONSTRAINT `fk_chitietyc_phieu` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `phieuyeucauxuat` (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chitietyeucauxuat` (`maChiTiet`, `maPhieuYeuCau`, `maMatHang`, `tenMatHang`, `soLuongYeuCau`, `donViTinh`) VALUES
-- Chi tiết yêu cầu xuất NVL
('CTYCX_NL_001', 'PYCX_NL_20261027_001', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1000.00, 'Kg'),
('CTYCX_NL_002', 'PYCX_NL_20261027_001', 'NL003', 'Bơ lạt Anchor New Zealand (Thùng 25kg)', 500.00, 'Kg'),
('CTYCX_NL_003', 'PYCX_NL_20261027_002', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1200.00, 'Kg'),
('CTYCX_NL_004', 'PYCX_NL_20261027_002', 'NL004', 'Đường tinh luyện Biên Hòa Pure', 800.00, 'Kg'),
('CTYCX_NL_005', 'PYCX_NL_20261027_003', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1500.00, 'Kg'),
('CTYCX_NL_006', 'PYCX_NL_20261027_003', 'NL007', 'Socola hạt chip đen nguyên chất Hershey', 600.00, 'Kg'),
('CTYCX_NL_007', 'PYCX_NL_20261027_003', 'NL006', 'Trứng gà tươi công nghiệp Ba Huân', 400.00, 'Quả'),
('CTYCX_NL_008', 'PYCX_NL_20261027_004', 'NL011', 'Bột dừa sấy khô béo Bến Tre', 500.00, 'Kg'),
('CTYCX_NL_009', 'PYCX_NL_20261027_004', 'NL004', 'Đường tinh luyện Biên Hòa Pure', 300.00, 'Kg'),
('CTYCX_NL_010', 'PYCX_NL_20261027_005', 'NL005', 'Sữa bột béo nguyên kem Fonterra', 500.00, 'Kg'),
('CTYCX_NL_011', 'PYCX_NL_20261027_005', 'NL003', 'Bơ lạt Anchor New Zealand (Thùng 25kg)', 400.00, 'Kg'),

-- Chi tiết yêu cầu xuất Thành Phẩm
('CTYCX_TP_001', 'PYCX_TP_20261027_001', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 800.00, 'Hộp'),
('CTYCX_TP_002', 'PYCX_TP_20261027_001', 'TP004', 'Bánh quy Cosy Marie Kinh Đô 300g', 500.00, 'Gói'),
('CTYCX_TP_003', 'PYCX_TP_20261027_002', 'TP006', 'Bánh quy Socola Oreo Vani 133g', 1200.00, 'Thanh'),
('CTYCX_TP_004', 'PYCX_TP_20261027_002', 'TP008', 'Bánh cracker AFC Lúa mì dinh dưỡng 200g', 600.00, 'Hộp'),
('CTYCX_TP_005', 'PYCX_TP_20261027_003', 'TP002', 'Bánh quy bơ Danisa Hộp thiếc 454g', 700.00, 'Hộp'),
('CTYCX_TP_006', 'PYCX_TP_20261027_003', 'TP005', 'Bánh quy Cosy Mè dinh dưỡng 250g', 800.00, 'Gói'),
('CTYCX_TP_007', 'PYCX_TP_20261027_004', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 900.00, 'Hộp'),
('CTYCX_TP_008', 'PYCX_TP_20261027_004', 'TP012', 'Bánh quy Choreo chip socola giòn 150g', 700.00, 'Hộp'),
('CTYCX_TP_009', 'PYCX_TP_20261027_005', 'TP010', 'Bánh quy dừa nướng giòn Bến Tre 180g', 500.00, 'Gói'),
('CTYCX_TP_010', 'PYCX_TP_20261027_005', 'TP011', 'Bánh quy gừng quế Noel ấm cúng 220g', 400.00, 'Hộp');

-- -------------------------------------------------------------
-- Bảng `lenhdieuphoixuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lenhdieuphoixuat`;
CREATE TABLE `lenhdieuphoixuat` (
  `maLenh` varchar(30) NOT NULL,
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `ngayDieuPhoi` datetime DEFAULT current_timestamp(),
  `nguoiDieuPhoi` varchar(20) DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Chờ xuất kho',
  PRIMARY KEY (`maLenh`),
  KEY `fk_lenhdieuphoixuat_phieu` (`maPhieuYeuCau`),
  KEY `fk_lenhdieuphoixuat_kho` (`maKho`),
  CONSTRAINT `fk_lenhdieuphoixuat_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`),
  CONSTRAINT `fk_lenhdieuphoixuat_phieu` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `phieuyeucauxuat` (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lenhdieuphoixuat` (`maLenh`, `maPhieuYeuCau`, `maKho`, `ngayDieuPhoi`, `nguoiDieuPhoi`, `ghiChu`, `trangThai`) VALUES
('LDPX_NL_DONE_01', 'PYCX_NL_20261027_001', 'KHO_NVL_01', '2026-10-26 10:00:00', 'NV004', 'Thuật toán FEFO: Lấy lô LONL_20261001_001 hạn sớm nhất', 'Đã xuất kho'),
('LDPX_TP_DONE_01', 'PYCX_TP_20261027_001', 'KHO_TP_01', '2026-10-26 14:30:00', 'NV003', 'FEFO: Lấy lô Danisa LOTP_20261002_001 xuất cho Coopmart', 'Đã xuất kho');

-- -------------------------------------------------------------
-- Bảng `chitietdieuphoixuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietdieuphoixuat`;
CREATE TABLE `chitietdieuphoixuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maLenh` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `soLuongPhanBo` decimal(12,2) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_chitietdpx_lenh` (`maLenh`),
  KEY `fk_chitietdpx_vitri` (`maViTri`),
  CONSTRAINT `fk_chitietdpx_lenh` FOREIGN KEY (`maLenh`) REFERENCES `lenhdieuphoixuat` (`maLenh`),
  CONSTRAINT `fk_chitietdpx_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chitietdieuphoixuat` (`maChiTiet`, `maLenh`, `maLo`, `maViTri`, `soLuongPhanBo`) VALUES
('CTDPX_001', 'LDPX_NL_DONE_01', 'LONL_20261001_001', 'A1-K01-T01', 500.00),
('CTDPX_002', 'LDPX_NL_DONE_01', 'LONL_20261001_001', 'A1-K01-T02', 500.00),
('CTDPX_003', 'LDPX_TP_DONE_01', 'LOTP_20261002_001', 'B1-K01-T01', 600.00);

-- -------------------------------------------------------------
-- Bảng `phieuxuatkho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuxuatkho`;
CREATE TABLE `phieuxuatkho` (
  `maPXK` varchar(30) NOT NULL,
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `ngayXuatThucTe` datetime DEFAULT current_timestamp(),
  `nguoiXuat` varchar(100) DEFAULT NULL,
  `ghiChu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`maPXK`),
  KEY `fk_phieuxuatkho_lenh` (`maLenhDieuPhoi`),
  CONSTRAINT `fk_phieuxuatkho_lenh` FOREIGN KEY (`maLenhDieuPhoi`) REFERENCES `lenhdieuphoixuat` (`maLenh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `phieuxuatkho` (`maPXK`, `maLenhDieuPhoi`, `ngayXuatThucTe`, `nguoiXuat`, `ghiChu`) VALUES
('PXK_NL_20261026_001', 'LDPX_NL_DONE_01', '2026-10-26 11:00:00', 'Trần Văn Sang', 'Xuất 1 tấn bột mì số 8 cho PX1 nướng bánh Danisa'),
('PXK_TP_20261026_001', 'LDPX_TP_DONE_01', '2026-10-26 16:00:00', 'Nguyễn Hữu Lộc', 'Xuất 600 hộp Danisa 200g cho xe tải Coopmart');

-- -------------------------------------------------------------
-- Bảng `dotkiemke` (5 đợt kiểm kê kho)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `dotkiemke`;
CREATE TABLE `dotkiemke` (
  `maDotKiemKe` varchar(30) NOT NULL,
  `tenDotKiemKe` varchar(150) NOT NULL,
  `ngayBatDau` date NOT NULL,
  `ngayKetThuc` date DEFAULT NULL,
  `maKho` varchar(20) NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Đang thực hiện',
  PRIMARY KEY (`maDotKiemKe`),
  KEY `fk_dotkiemke_kho` (`maKho`),
  CONSTRAINT `fk_dotkiemke_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dotkiemke` (`maDotKiemKe`, `tenDotKiemKe`, `ngayBatDau`, `ngayKetThuc`, `maKho`, `trangThai`) VALUES
('KK_2026_T10_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Khô 1 (Tháng 10/2026)', '2026-10-25', '2026-10-28', 'KHO_NVL_01', 'Đang thực hiện'),
('KK_2026_T10_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Bánh Quy 1 (Tháng 10/2026)', '2026-10-26', '2026-10-29', 'KHO_TP_01', 'Đang thực hiện'),
('KK_2026_T10_LANH', 'Kiểm kê đột xuất Kho Nguyên Liệu Lạnh & Mát', '2026-10-27', '2026-10-28', 'KHO_NVL_02', 'Đang thực hiện'),
('KK_2026_T09_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 09/2026', '2026-09-28', '2026-09-30', 'KHO_TP_01', 'Đã hoàn tất'),
('KK_2026_T09_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Tháng 09/2026', '2026-09-25', '2026-09-27', 'KHO_NVL_01', 'Đã hoàn tất');

-- -------------------------------------------------------------
-- Bảng `bienbankiemke`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `bienbankiemke`;
CREATE TABLE `bienbankiemke` (
  `maBienBan` varchar(20) NOT NULL,
  `tenDotKiemKe` varchar(150) NOT NULL,
  `loaiKiemKe` varchar(50) NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `ngayKiemKe` date DEFAULT NULL,
  `nguoiKiemKe` varchar(20) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Đang thực hiện',
  PRIMARY KEY (`maBienBan`),
  KEY `fk_bienbankiemke_kho` (`maKho`),
  CONSTRAINT `fk_bienbankiemke_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bienbankiemke` (`maBienBan`, `tenDotKiemKe`, `loaiKiemKe`, `maKho`, `ngayKiemKe`, `nguoiKiemKe`, `trangThai`) VALUES
('BBKK_20261025_01', 'Kiểm kê định kỳ Kho Thành Phẩm Bánh Quy 1 (Tháng 10/2026)', 'Kiểm kê thành phẩm', 'KHO_TP_01', '2026-10-26', 'NV002', 'Đang thực hiện'),
('BBKK_20261026_01', 'Kiểm kê định kỳ Kho Nguyên Liệu Khô 1 (Tháng 10/2026)', 'Kiểm kê nguyên liệu', 'KHO_NVL_01', '2026-10-26', 'NV002', 'Đang thực hiện'),
('BBKK_20261027_01', 'Kiểm kê đột xuất Kho Nguyên Liệu Lạnh & Mát', 'Kiểm kê nguyên liệu', 'KHO_NVL_02', '2026-10-27', 'NV014', 'Đang thực hiện'),
('BBKK_20260928_01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 09/2026', 'Kiểm kê thành phẩm', 'KHO_TP_01', '2026-09-30', 'NV002', 'Đã hoàn tất'),
('BBKK_20260925_01', 'Kiểm kê định kỳ Kho Nguyên Liệu Tháng 09/2026', 'Kiểm kê nguyên liệu', 'KHO_NVL_01', '2026-09-27', 'NV002', 'Đã hoàn tất');

-- -------------------------------------------------------------
-- Bảng `chitietkiemke` (Nhiều trường hợp khớp, thiếu, hỏng)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietkiemke`;
CREATE TABLE `chitietkiemke` (
  `maChiTiet` varchar(20) NOT NULL,
  `maBienBan` varchar(20) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `soLuongTheoSoSach` decimal(12,2) NOT NULL,
  `soLuongTotThucTe` decimal(12,2) NOT NULL,
  `soLuongHongThucTe` decimal(12,2) DEFAULT 0.00,
  `soLuongChenhLech` decimal(12,2) DEFAULT 0.00,
  `tinhTrang` varchar(255) DEFAULT 'Bình thường',
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_chitietkiemke_bb` (`maBienBan`),
  KEY `fk_chitietkiemke_vitri` (`maViTri`),
  CONSTRAINT `fk_chitietkiemke_bb` FOREIGN KEY (`maBienBan`) REFERENCES `bienbankiemke` (`maBienBan`),
  CONSTRAINT `fk_chitietkiemke_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `chitietkiemke` (`maChiTiet`, `maBienBan`, `maLo`, `maViTri`, `soLuongTheoSoSach`, `soLuongTotThucTe`, `soLuongHongThucTe`, `soLuongChenhLech`, `tinhTrang`) VALUES
-- Chi tiết kiểm kê Thành phẩm Kho TP 01 (BBKK_20261025_01)
('CTKK_001', 'BBKK_20261025_01', 'LOTP_20261002_001', 'B1-K01-T01', 600.00, 600.00, 0.00, 0.00, 'Khớp số sách 100%, bảo quản tốt'),
('CTKK_002', 'BBKK_20261025_01', 'LOTP_20261005_001', 'B1-K01-T02', 500.00, 485.00, 15.00, -15.00, 'Thiếu 15 hộp: 10 hộp bị móp méo va đập khi xe nâng xếp, 5 hộp thất thoát'),
('CTKK_003', 'BBKK_20261025_01', 'LOTP_20261015_001', 'B1-K02-T01', 500.00, 500.00, 0.00, 0.00, 'Khớp số sách 100%'),
('CTKK_004', 'BBKK_20261025_01', 'LOTP_20261003_001', 'B1-K03-T01', 500.00, 490.00, 10.00, -10.00, 'Hỏng 10 hộp do bao bì rách khi bốc xếp'),
('CTKK_005', 'BBKK_20261025_01', 'LOTP_20261006_001', 'B2-K02-T01', 800.00, 800.00, 0.00, 0.00, 'Khớp số sách 100%'),
('CTKK_006', 'BBKK_20261025_01', 'LOTP_20261008_001', 'B2-K03-T01', 600.00, 605.00, 0.00, 5.00, 'Thừa 5 hộp do nhầm lẫn kiểm đếm ca trước'),

-- Chi tiết kiểm kê Nguyên liệu Kho NVL 01 (BBKK_20261026_01)
('CTKK_007', 'BBKK_20261026_01', 'LONL_20261001_001', 'A1-K01-T01', 500.00, 500.00, 0.00, 0.00, 'Khớp 100%, bột mì khô ráo'),
('CTKK_008', 'BBKK_20261026_01', 'LONL_20261001_002', 'A1-K02-T01', 600.00, 580.00, 20.00, -20.00, 'Hao hụt 20kg: 1 bao 25kg bị rách bục góc'),
('CTKK_009', 'BBKK_20261026_01', 'LONL_20261008_001', 'A2-K01-T01', 600.00, 600.00, 0.00, 0.00, 'Khớp 100%, đường tinh luyện nguyên vẹn'),

-- Chi tiết kiểm kê Kho lạnh NVL 02 (BBKK_20261027_01)
('CTKK_010', 'BBKK_20261027_01', 'LONL_20261005_001', 'L1-K01-T01', 400.00, 400.00, 0.00, 0.00, 'Khớp 100%, nhiệt độ kho lạnh đạt 4°C'),
('CTKK_011', 'BBKK_20261027_01', 'LONL_20261010_001', 'L1-K03-T01', 2500.00, 2460.00, 40.00, -40.00, 'Bể 40 quả trứng trong quá trình di chuyển khay');

-- -------------------------------------------------------------
-- Các bảng xử lý chênh lệch & sự cố
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `denghitieuhuy`;
CREATE TABLE `denghitieuhuy` (
  `maDeNghi` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `loaiHang` varchar(50) DEFAULT 'Nguyên liệu',
  `soLuong` decimal(12,2) NOT NULL,
  `lyDo` varchar(255) DEFAULT NULL,
  `nguoiDeNghi` varchar(100) DEFAULT NULL,
  `ngayDeNghi` date DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Chờ phê duyệt',
  PRIMARY KEY (`maDeNghi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `denghitieuhuy` (`maDeNghi`, `maLo`, `loaiHang`, `soLuong`, `lyDo`, `nguoiDeNghi`, `ngayDeNghi`, `trangThai`) VALUES
('DNTH_20261018_01', 'LONL_20261001_002', 'Nguyên liệu', 20.00, 'Bột mì rách bao bị ẩm mốc cục bộ, không đạt vi sinh an toàn thực phẩm', 'Trần Thanh Toàn', '2026-10-18', 'Đã phê duyệt'),
('DNTH_20261026_01', 'LOTP_20261005_001', 'Thành phẩm', 15.00, 'Bánh quy Danisa móp vỡ hộp thiếc nặng trong kho, không thể kinh doanh', 'Trần Bích Thuận', '2026-10-26', 'Chờ phê duyệt'),
('DNTH_20261027_01', 'LONL_20261010_001', 'Nguyên liệu', 40.00, 'Trứng gà tươi bị vỡ nứt trong quá trình bảo quản kho lạnh', 'Trần Văn Sang', '2026-10-27', 'Chờ phê duyệt');

DROP TABLE IF EXISTS `bienbanthieuhang`;
CREATE TABLE `bienbanthieuhang` (
  `maBienBan` varchar(30) NOT NULL,
  `maPXK` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `soLuongThieu` decimal(12,2) NOT NULL,
  `lyDo` varchar(255) DEFAULT NULL,
  `ngayLap` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maBienBan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bienbanthieuhang` (`maBienBan`, `maPXK`, `maLo`, `soLuongThieu`, `lyDo`, `ngayLap`) VALUES
('BBTH_20261026_01', 'PXK_TP_20261026_001', 'LOTP_20261002_001', 5.00, 'Tài xế giao nhận kiểm đếm thiếu 5 hộp ở cuối kiện hàng', '2026-10-26 16:30:00');

DROP TABLE IF EXISTS `bienbanxulyhanghong`;
CREATE TABLE `bienbanxulyhanghong` (
  `maBienBan` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `loaiHang` varchar(50) DEFAULT 'Nguyên liệu',
  `soLuongHong` decimal(12,2) NOT NULL,
  `nguyenNhan` varchar(255) DEFAULT NULL,
  `bienPhapXuLy` varchar(255) DEFAULT NULL,
  `ngayLap` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maBienBan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bienbanxulyhanghong` (`maBienBan`, `maLo`, `loaiHang`, `soLuongHong`, `nguyenNhan`, `bienPhapXuLy`, `ngayLap`) VALUES
('BBHH_20261018_01', 'LONL_20261001_002', 'Nguyên liệu', 20.00, 'Thùng xe chở hàng bị dột nước mưa làm ướt bao bột mì góc ngoài', 'Tiêu hủy theo quy trình rác thải công nghiệp', '2026-10-18 10:00:00'),
('BBHH_20261026_01', 'LOTP_20261005_001', 'Thành phẩm', 15.00, 'Va chạm xe nâng khi nâng hạ kiện pallet', 'Chuyển về xưởng tái chế làm thức ăn chăn nuôi', '2026-10-26 15:30:00');

DROP TABLE IF EXISTS `kehoachsanxuat`;
CREATE TABLE `kehoachsanxuat` (
  `maKeHoach` varchar(20) NOT NULL,
  `maPhanXuong` varchar(20) NOT NULL,
  `ngayBatDau` date NOT NULL,
  `ngayKetThuc` date NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Đang sản xuất',
  PRIMARY KEY (`maKeHoach`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kehoachsanxuat` (`maKeHoach`, `maPhanXuong`, `ngayBatDau`, `ngayKetThuc`, `trangThai`) VALUES
('KHSX-2026-Q4-01', 'PX-BANH-01', '2026-10-01', '2026-10-31', 'Đang sản xuất'),
('KHSX-2026-Q4-02', 'PX-BANH-02', '2026-10-15', '2026-11-15', 'Đang sản xuất');

SET FOREIGN_KEY_CHECKS = 1;
