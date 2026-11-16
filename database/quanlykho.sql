-- =====================================================================
-- CƠ SỞ DỮ LIỆU CHUẨN DỰ ÁN QUẢN LÝ KHO NHÀ MÁY BÁNH QUY - BISCUITWMS
-- Tên Database : quanlykho_vuotqua
-- Quy mô       : ĐẦY ĐỦ 37 BẢNG CHUẨN THEO BÁO CÁO THỰC HÀNH TUẦN 9
-- Hệ quản trị  : MySQL 8.0+ / MariaDB 10.4+ (UTF-8mb4 unicode ci)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS quanlykho_vuotqua;
CREATE DATABASE quanlykho_vuotqua
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE quanlykho_vuotqua;

-- =====================================================================
-- PHẦN 1: HỆ THỐNG NGƯỜI DÙNG & PHÂN QUYỀN (3 BẢNG: 01 -> 03)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 01: vaitro (Trần Thanh Toàn - UC 2.5)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS vaitro;
CREATE TABLE vaitro (
  maVaiTro VARCHAR(20) NOT NULL,
  tenVaiTro VARCHAR(100) NOT NULL,
  moTa VARCHAR(255) DEFAULT NULL,
  ngayTao DATE DEFAULT NULL,
  PRIMARY KEY (maVaiTro),
  UNIQUE KEY uq_vaitro_ten (tenVaiTro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO vaitro (maVaiTro, tenVaiTro, moTa, ngayTao) VALUES
('VT01', 'Admin', 'Quản trị hệ thống: quản lý tài khoản, vai trò, danh mục master data', '2026-10-01'),
('VT02', 'QuanLyKho', 'Phê duyệt yêu cầu nhập/xuất, ban hành lệnh điều phối, xem báo cáo tồn kho', '2026-10-01'),
('VT03', 'NhanVienKho', 'Thực hiện nhập/xuất thực tế, lập phiếu nhập kho, phiếu xuất kho, sắp xếp vị trí kệ', '2026-10-01'),
('VT04', 'DieuPhoiVien', 'Lập lệnh điều phối nhập/xuất theo nguyên tắc FEFO', '2026-10-01'),
('VT05', 'KiemKe', 'Thực hiện kiểm đếm thực tế, lập biên bản kiểm kê và ghi nhận chênh lệch', '2026-10-01'),
('VT06', 'QC', 'Kiểm định chất lượng lô nguyên liệu/thành phẩm, lập biên bản kiểm định', '2026-10-01'),
('VT07', 'BanGiamDoc', 'Xem báo cáo thống kê toàn hệ thống, phê duyệt xử lý chênh lệch và tiêu hủy hàng hỏng', '2026-10-01');

-- ---------------------------------------------------------------------
-- BẢNG 02: nhanvien (Trần Thanh Toàn - UC 2.5)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS nhanvien;
CREATE TABLE nhanvien (
  maNV VARCHAR(20) NOT NULL,
  hoTen VARCHAR(100) NOT NULL,
  vaiTro VARCHAR(20) NOT NULL,
  soDienThoai VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maNV),
  UNIQUE KEY uq_nhanvien_sdt (soDienThoai),
  UNIQUE KEY uq_nhanvien_email (email),
  KEY fk_nhanvien_vaitro (vaiTro),
  CONSTRAINT fk_nhanvien_vaitro FOREIGN KEY (vaiTro) REFERENCES vaitro (maVaiTro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO nhanvien (maNV, hoTen, vaiTro, soDienThoai, email, trangThai) VALUES
('NV001', 'Lê Minh Thiện Phúc', 'VT06', '0901000001', 'phuc@iuh.edu.vn', 'Hoạt động'),
('NV002', 'Võ Thị Tuyết Ngân', 'VT05', '0901000002', 'ngan@iuh.edu.vn', 'Hoạt động'),
('NV003', 'Trần Bích Thuận', 'VT02', '0901000003', 'thuan@iuh.edu.vn', 'Hoạt động'),
('NV004', 'Trần Thanh Toàn', 'VT02', '0901000004', 'toan@iuh.edu.vn', 'Hoạt động'),
('NV005', 'Trần Văn Sang', 'VT03', '0901000005', 'sang@iuh.edu.vn', 'Hoạt động'),
('NV006', 'Nguyễn Hữu Lộc', 'VT03', '0901000006', 'loc@iuh.edu.vn', 'Hoạt động'),
('NV007', 'Lý Minh Khang', 'VT04', '0901000007', 'khang.dp@kho.vn', 'Hoạt động'),
('NV010', 'Đỗ Thành Đạt', 'VT01', '0909999000', 'admin@kho.vn', 'Hoạt động'),
('NV011', 'Nguyễn Văn An', 'VT02', '0909999001', 'quanly@kho.vn', 'Hoạt động'),
('NV012', 'Trần Thị Bình', 'VT03', '0909999002', 'nhanvienkho@kho.vn', 'Hoạt động'),
('NV013', 'Lê Hoàng Cường', 'VT06', '0909999003', 'qc@kho.vn', 'Hoạt động'),
('NV014', 'Phạm Minh Dũng', 'VT05', '0909999004', 'kiemke@kho.vn', 'Hoạt động'),
('NV015', 'Hoàng Văn Giang', 'VT07', '0909999005', 'giamdoc@kho.vn', 'Hoạt động'),
('NV016', 'Vũ Thị Hạnh', 'VT03', '0909999006', 'hanh.kho@kho.vn', 'Ngừng hoạt động');

-- ---------------------------------------------------------------------
-- BẢNG 03: taikhoan (Nguyễn Hữu Lộc - UC 1.5)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS taikhoan;
CREATE TABLE taikhoan (
  maTaiKhoan VARCHAR(20) NOT NULL,
  maNV VARCHAR(20) NOT NULL,
  maVaiTro VARCHAR(20) NOT NULL,
  tenDangNhap VARCHAR(50) NOT NULL,
  matKhau VARCHAR(255) NOT NULL,
  ngayTao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maTaiKhoan),
  UNIQUE KEY uq_taikhoan_manv (maNV),
  UNIQUE KEY uq_taikhoan_tendangnhap (tenDangNhap),
  KEY fk_taikhoan_vaitro (maVaiTro),
  CONSTRAINT fk_taikhoan_nhanvien FOREIGN KEY (maNV) REFERENCES nhanvien (maNV),
  CONSTRAINT fk_taikhoan_vaitro FOREIGN KEY (maVaiTro) REFERENCES vaitro (maVaiTro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO taikhoan (maTaiKhoan, maNV, maVaiTro, tenDangNhap, matKhau, ngayTao, trangThai) VALUES
('TK001', 'NV001', 'VT06', 'phuc_qc', '123456', '2026-10-01 08:00:00', 'Hoạt động'),
('TK002', 'NV002', 'VT05', 'ngan_kiemke', '123456', '2026-10-01 08:05:00', 'Hoạt động'),
('TK003', 'NV003', 'VT02', 'thuan_qlk', '123456', '2026-10-01 08:10:00', 'Hoạt động'),
('TK004', 'NV004', 'VT02', 'toan_qlk', '123456', '2026-10-01 08:15:00', 'Hoạt động'),
('TK005', 'NV005', 'VT03', 'sang_kho', '123456', '2026-10-01 08:20:00', 'Hoạt động'),
('TK006', 'NV006', 'VT03', 'loc_kho', '123456', '2026-10-01 08:25:00', 'Hoạt động'),
('TK007', 'NV007', 'VT04', 'khang_dp', '123456', '2026-10-02 09:00:00', 'Hoạt động'),
('TK010', 'NV010', 'VT01', 'admin', '123456', '2026-10-01 07:30:00', 'Hoạt động'),
('TK011', 'NV011', 'VT02', 'quanly', '123456', '2026-10-01 07:35:00', 'Hoạt động'),
('TK012', 'NV012', 'VT03', 'nhanvienkho', '123456', '2026-10-01 07:40:00', 'Hoạt động'),
('TK013', 'NV013', 'VT06', 'qc', '123456', '2026-10-01 07:45:00', 'Hoạt động'),
('TK014', 'NV014', 'VT05', 'kiemke', '123456', '2026-10-01 07:50:00', 'Hoạt động'),
('TK015', 'NV015', 'VT07', 'giamdoc', '123456', '2026-10-01 07:55:00', 'Hoạt động'),
('TK016', 'NV016', 'VT03', 'hanh_kho', '123456', '2026-10-05 10:00:00', 'Khóa');


-- =====================================================================
-- PHẦN 2: DANH MỤC MASTER DATA & KHO BÃI (7 BẢNG: 04 -> 10)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 04: nhacungcap (Trần Văn Sang - UC 1.1, 1.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS nhacungcap;
CREATE TABLE nhacungcap (
  maNCC VARCHAR(20) NOT NULL,
  tenNCC VARCHAR(150) NOT NULL,
  soDienThoai VARCHAR(15) DEFAULT NULL,
  diaChi VARCHAR(255) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (maNCC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO nhacungcap (maNCC, tenNCC, soDienThoai, diaChi, email) VALUES
('NCC01', 'Công ty CP Xay Lúa Mì Việt Nam (Flour Mill)', '02838123456', 'KCN Tân Bình, Tân Phú, TP.HCM', 'sales@luami.vn'),
('NCC02', 'Fonterra Brands Vietnam Co., Ltd', '02839998888', 'Tòa nhà Bitexco, Q.1, TP.HCM', 'orders@fonterra.com'),
('NCC03', 'Công ty CP Đường Biên Hòa (SBT)', '02513836123', 'KCN Biên Hòa 1, Đồng Nai', 'contact@ttcsugar.com.vn'),
('NCC04', 'Công ty TNHH Ba Huân (Trứng gà sạch)', '02838555666', 'Huyện Cần Giuộc, Long An', 'kinhdoanh@bahuan.vn'),
('NCC05', 'Puratos Grand-Place Vietnam (Socola & Phụ gia)', '02743782999', 'KCN VSIP 1, Thuận An, Bình Dương', 'info@puratos.vn'),
('NCC06', 'Công ty TNHH Mauri Lafrancaise (Men bánh mì)', '02513891444', 'KCN Amata, Biên Hòa, Đồng Nai', 'sales@abmauri.com.vn'),
('NCC07', 'Công ty TNHH Thực Phẩm Alsa Pháp', '02837772233', 'KCN Cát Lái, TP. Thủ Đức, TP.HCM', 'support@alsa.com.vn'),
('NCC08', 'Công ty Muối Tinh Bạc Liêu (Bacosalt)', '02913822111', 'Phường 2, TP. Bạc Liêu', 'sales@bacosalt.com.vn'),
('NCC09', 'Công ty TNHH Chế Biến Dừa Bến Tre (Betrimex)', '02753822333', 'KCN Giao Long, Châu Thành, Bến Tre', 'sales@betrimex.com.vn');

-- ---------------------------------------------------------------------
-- BẢNG 05: nguyenlieu (Trần Văn Sang - UC 1.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS nguyenlieu;
CREATE TABLE nguyenlieu (
  maNL VARCHAR(20) NOT NULL,
  tenNL VARCHAR(150) NOT NULL,
  donViTinh VARCHAR(20) NOT NULL,
  loaiKhoBaoQuan VARCHAR(50) NOT NULL DEFAULT 'Kho khô',
  nhietDoTieuChuan FLOAT DEFAULT NULL,
  doAmTieuChuan FLOAT DEFAULT NULL,
  dinhMucToiThieu DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  soLuongTon DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maNL),
  UNIQUE KEY uq_nguyenlieu_ten (tenNL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO nguyenlieu (maNL, tenNL, donViTinh, loaiKhoBaoQuan, nhietDoTieuChuan, doAmTieuChuan, dinhMucToiThieu, soLuongTon, trangThai) VALUES
('NL001', 'Bột mì đa dụng thượng hạng số 8', 'Kg', 'Kho khô', 25.0, 60.0, 500.00, 4500.00, 'Hoạt động'),
('NL002', 'Bột mì chuyên dụng làm bánh quy số 11', 'Kg', 'Kho khô', 25.0, 60.0, 400.00, 2200.00, 'Hoạt động'),
('NL003', 'Bơ lạt Anchor New Zealand (Thùng 25kg)', 'Kg', 'Kho lạnh', 4.0, 55.0, 200.00, 1200.00, 'Hoạt động'),
('NL004', 'Đường tinh luyện Biên Hòa Pure', 'Kg', 'Kho khô', 26.0, 55.0, 600.00, 5500.00, 'Hoạt động'),
('NL005', 'Sữa bột béo nguyên kem Fonterra', 'Kg', 'Kho khô', 24.0, 58.0, 150.00, 1400.00, 'Hoạt động'),
('NL006', 'Trứng gà tươi công nghiệp Ba Huân', 'Quả', 'Kho lạnh', 5.0, 60.0, 2000.00, 5500.00, 'Hoạt động'),
('NL007', 'Socola hạt chip đen nguyên chất Hershey', 'Kg', 'Kho mát', 18.0, 55.0, 100.00, 600.00, 'Hoạt động'),
('NL008', 'Bột nở làm bánh Alsa Pháp cao cấp', 'Kg', 'Kho khô', 25.0, 55.0, 50.00, 350.00, 'Hoạt động'),
('NL009', 'Men khô ngọt Mauri Instant', 'Kg', 'Kho mát', 16.0, 55.0, 50.00, 280.00, 'Hoạt động'),
('NL010', 'Hương vani tự nhiên dạng lỏng Rayner', 'Lít', 'Kho mát', 18.0, 60.0, 20.00, 15.00, 'Hoạt động'), -- Tồn dưới định mức tối thiểu (15 < 20) để test cảnh báo đỏ
('NL011', 'Bột dừa sấy khô béo Bến Tre', 'Kg', 'Kho khô', 25.0, 60.0, 100.00, 800.00, 'Hoạt động'),
('NL012', 'Muối tinh sấy I-ốt Bạc Liêu', 'Kg', 'Kho khô', 26.0, 55.0, 80.00, 650.00, 'Hoạt động'),
('NL013', 'Hạt mè trắng rang thơm thượng hạng', 'Kg', 'Kho khô', 24.0, 58.0, 60.0, 420.00, 'Hoạt động'),
('NL014', 'Bột ca cao nguyên chất Đắk Lắk', 'Kg', 'Kho khô', 24.0, 58.0, 100.00, 750.00, 'Hoạt động'),
('NL015', 'Hương bơ nhân tạo tổng hợp (Mẫu cũ)', 'Lít', 'Kho khô', 25.0, 60.0, 10.00, 0.00, 'Ngừng sử dụng');

-- ---------------------------------------------------------------------
-- BẢNG 06: nhomthanhpham (Nguyễn Hữu Lộc - UC 3.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS nhomthanhpham;
CREATE TABLE nhomthanhpham (
  maNhom VARCHAR(20) NOT NULL,
  tenNhom VARCHAR(100) NOT NULL,
  moTa VARCHAR(255) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maNhom),
  UNIQUE KEY uq_nhomthanhpham_ten (tenNhom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO nhomthanhpham (maNhom, tenNhom, moTa, trangThai) VALUES
('NTP01', 'Bánh quy bơ cao cấp', 'Dòng bánh quy nướng bơ truyền thống hộp thiếc sang trọng quà biếu', 'Hoạt động'),
('NTP02', 'Bánh quy giòn dinh dưỡng', 'Dòng bánh quy mè, lúa mì dinh dưỡng ít ngọt cho gia đình', 'Hoạt động'),
('NTP03', 'Bánh quy socola kẹp kem', 'Dòng bánh quy đen kẹp kem vani và socola giới trẻ ưa chuộng', 'Hoạt động'),
('NTP04', 'Bánh quy dừa & hương vị tự nhiên', 'Dòng bánh quy nướng đặc sản dừa nướng, gừng quế ấm cúng', 'Hoạt động');

-- ---------------------------------------------------------------------
-- BẢNG 07: thanhpham (Nguyễn Hữu Lộc - UC 3.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS thanhpham;
CREATE TABLE thanhpham (
  maTP VARCHAR(20) NOT NULL,
  tenTP VARCHAR(150) NOT NULL,
  maNhom VARCHAR(20) NOT NULL,
  donViTinh VARCHAR(20) NOT NULL,
  quyCachDongGoi VARCHAR(100) DEFAULT NULL,
  moTa VARCHAR(500) DEFAULT NULL,
  soLuongToiThieu INT NOT NULL DEFAULT 100,
  soLuongToiDa INT NOT NULL DEFAULT 10000,
  soLuongTon DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maTP),
  UNIQUE KEY uq_thanhpham_ten (tenTP),
  KEY fk_thanhpham_nhom (maNhom),
  CONSTRAINT fk_thanhpham_nhom FOREIGN KEY (maNhom) REFERENCES nhomthanhpham (maNhom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO thanhpham (maTP, tenTP, maNhom, donViTinh, quyCachDongGoi, moTa, soLuongToiThieu, soLuongToiDa, soLuongTon, trangThai) VALUES
('TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 'NTP01', 'Hộp', '24 hộp/thùng', 'Bảo quản nơi khô ráo thoáng mát (< 25°C), tránh va đập móp hộp thiếc.', 200, 5000, 2450.00, 'Hoạt động'),
('TP002', 'Bánh quy bơ Danisa Hộp thiếc 454g', 'NTP01', 'Hộp', '12 hộp/thùng', 'Hộp thiếc cao cấp quà biếu, xếp tối đa 5 thùng carton/pallet.', 150, 4000, 1300.00, 'Hoạt động'),
('TP003', 'Bánh quy bơ Danisa Đại Hộp thiếc 681g', 'NTP01', 'Hộp', '6 hộp/thùng', 'Hộp thiếc cỡ đại 681g, lưu trữ kệ tầng thấp Dãy B1 Kho Thành Phẩm 1.', 100, 2500, 950.00, 'Hoạt động'),
('TP004', 'Bánh quy Cosy Marie Kinh Đô 300g', 'NTP02', 'Gói', '20 gói/thùng', 'Tránh đè nén vật nặng lên thùng carton làm vỡ bánh giòn bên trong.', 300, 6000, 2700.00, 'Hoạt động'),
('TP005', 'Bánh quy Cosy Mè dinh dưỡng 250g', 'NTP02', 'Gói', '24 gói/thùng', 'Dòng bánh quy mè dinh dưỡng ít ngọt, độ ẩm kho < 65%.', 250, 5000, 1800.00, 'Hoạt động'),
('TP006', 'Bánh quy Socola Oreo Vani 133g', 'NTP03', 'Thanh', '24 thanh/thùng', 'Nhiệt độ kho bảo quản 18°C – 24°C để tránh chảy lớp nhân kem vani.', 400, 8000, 4500.00, 'Hoạt động'),
('TP007', 'Bánh quy kem Socola Oreo 256.5g', 'NTP03', 'Hộp', '12 hộp/thùng', 'Tránh ánh nắng trực tiếp và nguồn nhiệt cao gây biến dạng lớp kem.', 200, 4000, 1600.00, 'Hoạt động'),
('TP008', 'Bánh cracker AFC Lúa mì dinh dưỡng 200g', 'NTP02', 'Hộp', '16 hộp/thùng', 'Bảo quản nơi khô thoáng, cách tường tối thiểu 20cm và đặt trên pallet.', 250, 5000, 1100.00, 'Hoạt động'),
('TP009', 'Bánh cracker AFC Tảo biển xanh 200g', 'NTP02', 'Hộp', '16 hộp/thùng', 'Kiểm tra độ kín màng co trước khi xếp lên kệ Kho Thành Phẩm 2.', 200, 4500, 1250.00, 'Hoạt động'),
('TP010', 'Bánh quy dừa nướng giòn Bến Tre 180g', 'NTP04', 'Gói', '24 gói/thùng', 'Dòng bánh quy nướng đặc sản dừa nướng giòn, ưu tiên xuất kho theo FEFO.', 150, 3500, 950.00, 'Hoạt động'),
('TP011', 'Bánh quy gừng quế Noel ấm cúng 220g', 'NTP04', 'Hộp', '12 hộp/thùng', 'Sản phẩm mùa vụ Giáng Sinh, lưu trữ khu vực khô ráo riêng biệt.', 100, 2000, 750.00, 'Hoạt động'),
('TP012', 'Bánh quy Choreo chip socola giòn 150g', 'NTP03', 'Hộp', '20 hộp/thùng', 'Hạt chip socola dễ mềm ở nhiệt độ trên 28°C, bảo quản ngăn mát tiêu chuẩn.', 200, 4000, 1400.00, 'Hoạt động'),
('TP013', 'Bánh quy bơ Hạt Điều Bình Phước 250g (Mẫu thử)', 'NTP01', 'Hộp', '12 hộp/thùng', 'Mặt hàng đã xuất hết tồn kho (Tồn = 0), dùng để test luồng Ngừng hoạt động.', 50, 1000, 0.00, 'Hoạt động');

-- ---------------------------------------------------------------------
-- BẢNG 08: kehoachsanxuat (Trần Văn Sang - UC 1.1, 2.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS kehoachsanxuat;
CREATE TABLE kehoachsanxuat (
  maKeHoach VARCHAR(20) NOT NULL,
  maPhanXuong VARCHAR(20) NOT NULL,
  ngayBatDau DATE NOT NULL,
  ngayKetThuc DATE NOT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Lập kế hoạch',
  PRIMARY KEY (maKeHoach)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kehoachsanxuat (maKeHoach, maPhanXuong, ngayBatDau, ngayKetThuc, trangThai) VALUES
('KHSX-2026-Q4-01', 'PX-NUONG-01', '2026-10-01', '2026-10-31', 'Đang sản xuất'),
('KHSX-2026-Q4-02', 'PX-DONGGOI-02', '2026-10-10', '2026-11-10', 'Đang sản xuất'),
('KHSX-2026-Q4-03', 'PX-KEPKEM-03', '2026-10-15', '2026-11-15', 'Đang sản xuất'),
('KHSX-2026-Q4-04', 'PX-DACSAN-04', '2026-10-20', '2026-11-20', 'Đang sản xuất'),
('KHSX-2026-Q4-05', 'PX-NUONG-01', '2026-11-01', '2026-11-30', 'Lập kế hoạch');

-- ---------------------------------------------------------------------
-- BẢNG 09: kho (Võ Thị Tuyết Ngân - UC 5.1)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS kho;
CREATE TABLE kho (
  maKho VARCHAR(20) NOT NULL,
  tenKho VARCHAR(100) NOT NULL,
  loaiKho VARCHAR(50) NOT NULL,
  dieuKienBaoQuan VARCHAR(100) DEFAULT 'Kho khô',
  diaChi VARCHAR(200) DEFAULT NULL,
  sucChuaToiDa INT NOT NULL DEFAULT 1000,
  sucChuaHienTai INT NOT NULL DEFAULT 0,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoạt động',
  PRIMARY KEY (maKho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kho (maKho, tenKho, loaiKho, dieuKienBaoQuan, diaChi, sucChuaToiDa, sucChuaHienTai, trangThai) VALUES
('KHO_NVL_01', 'Kho Nguyên Liệu Khô 1', 'Kho NVL', 'Kho khô thoáng gió (< 60% ẩm)', 'Khu A1 - Nhà máy Bánh Quy', 15000, 5600, 'Hoạt động'),
('KHO_NVL_02', 'Kho Nguyên Liệu Lạnh & Mát', 'Kho NVL', 'Kho lạnh 2-8°C & Kho mát 16-20°C', 'Khu A2 - Nhà máy Bánh Quy', 10000, 6700, 'Hoạt động'),
('KHO_TP_01', 'Kho Thành Phẩm Bánh Quy 1', 'Kho Thành phẩm', 'Kho thoáng mát 20-25°C', 'Khu B1 - Nhà máy Bánh Quy', 20000, 8150, 'Hoạt động'),
('KHO_TP_02', 'Kho Thành Phẩm Bánh Quy 2', 'Kho Thành phẩm', 'Kho thoáng mát 20-25°C', 'Khu B2 - Nhà máy Bánh Quy', 15000, 2900, 'Hoạt động'),
('KHO_LOI_01', 'Kho Hàng Lỗi & Cách Ly', 'Kho Hàng lỗi', 'Kho cách ly tiêu chuẩn chờ xử lý', 'Khu C - Nhà máy Bánh Quy', 3000, 250, 'Hoạt động'),
('KHO_TP_03', 'Kho Thành Phẩm Dự Phòng 3', 'Kho Thành phẩm', 'Kho thoáng mát 22-26°C', 'Khu B3 - Nhà máy Bánh Quy', 8000, 0, 'Tạm đóng');

-- ---------------------------------------------------------------------
-- BẢNG 10: vitrikho (Võ Thị Tuyết Ngân - UC 5.1, Lê Minh Thiện Phúc - UC 6.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS vitrikho;
CREATE TABLE vitrikho (
  maViTri VARCHAR(20) NOT NULL,
  maKho VARCHAR(20) NOT NULL,
  day VARCHAR(10) NOT NULL,
  ke VARCHAR(10) NOT NULL,
  tang VARCHAR(10) NOT NULL,
  sucChuaKhaDung INT NOT NULL DEFAULT 500,
  trangThaiSuDung VARCHAR(50) NOT NULL DEFAULT 'Trống',
  trangThaiChuaHang VARCHAR(50) NOT NULL DEFAULT 'Khả dụng',
  PRIMARY KEY (maViTri),
  KEY fk_vitrikho_kho (maKho),
  CONSTRAINT fk_vitrikho_kho FOREIGN KEY (maKho) REFERENCES kho (maKho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO vitrikho (maViTri, maKho, day, ke, tang, sucChuaKhaDung, trangThaiSuDung, trangThaiChuaHang) VALUES
-- Kho Nguyên Liệu Khô 1 (KHO_NVL_01 - Dãy A1, A2)
('A1-K01-T01', 'KHO_NVL_01', 'A1', 'K01', 'T01', 600, 'Đang chứa', 'Khả dụng'),
('A1-K01-T02', 'KHO_NVL_01', 'A1', 'K01', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A1-K01-T03', 'KHO_NVL_01', 'A1', 'K01', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K02-T01', 'KHO_NVL_01', 'A1', 'K02', 'T01', 600, 'Đầy', 'Khả dụng'),
('A1-K02-T02', 'KHO_NVL_01', 'A1', 'K02', 'T02', 600, 'Đang chứa', 'Khả dụng'),
('A1-K02-T03', 'KHO_NVL_01', 'A1', 'K02', 'T03', 600, 'Đang chứa', 'Khả dụng'),
('A1-K03-T01', 'KHO_NVL_01', 'A1', 'K03', 'T01', 600, 'Đầy', 'Khả dụng'),
('A1-K03-T02', 'KHO_NVL_01', 'A1', 'K03', 'T02', 600, 'Đầy', 'Khả dụng'),
('A1-K03-T03', 'KHO_NVL_01', 'A1', 'K03', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K04-T01', 'KHO_NVL_01', 'A1', 'K04', 'T01', 600, 'Trống', 'Khả dụng'),
('A1-K04-T02', 'KHO_NVL_01', 'A1', 'K04', 'T02', 600, 'Trống', 'Khả dụng'),
('A1-K04-T03', 'KHO_NVL_01', 'A1', 'K04', 'T03', 600, 'Trống', 'Khả dụng'),
('A1-K05-T01', 'KHO_NVL_01', 'A1', 'K05', 'T01', 600, 'Trống', 'Khả dụng'),
('A1-K05-T02', 'KHO_NVL_01', 'A1', 'K05', 'T02', 600, 'Trống', 'Khả dụng'),
('A1-K05-T03', 'KHO_NVL_01', 'A1', 'K05', 'T03', 600, 'Trống', 'Bảo trì'),
('A2-K01-T01', 'KHO_NVL_01', 'A2', 'K01', 'T01', 600, 'Đầy', 'Khả dụng'),
('A2-K01-T02', 'KHO_NVL_01', 'A2', 'K01', 'T02', 600, 'Đầy', 'Khả dụng'),
('A2-K01-T03', 'KHO_NVL_01', 'A2', 'K01', 'T03', 600, 'Trống', 'Khả dụng'),
('A2-K02-T01', 'KHO_NVL_01', 'A2', 'K02', 'T01', 600, 'Đầy', 'Khả dụng'),
('A2-K02-T02', 'KHO_NVL_01', 'A2', 'K02', 'T02', 600, 'Đầy', 'Khả dụng'),
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
-- Kho Nguyên Liệu Lạnh & Mát (KHO_NVL_02 - Dãy L1)
('L1-K01-T01', 'KHO_NVL_02', 'L1', 'K01', 'T01', 400, 'Đầy', 'Khả dụng'),
('L1-K01-T02', 'KHO_NVL_02', 'L1', 'K01', 'T02', 400, 'Đầy', 'Khả dụng'),
('L1-K01-T03', 'KHO_NVL_02', 'L1', 'K01', 'T03', 400, 'Trống', 'Khả dụng'),
('L1-K02-T01', 'KHO_NVL_02', 'L1', 'K02', 'T01', 400, 'Đầy', 'Khả dụng'),
('L1-K02-T02', 'KHO_NVL_02', 'L1', 'K02', 'T02', 400, 'Trống', 'Khả dụng'),
('L1-K02-T03', 'KHO_NVL_02', 'L1', 'K02', 'T03', 400, 'Trống', 'Khả dụng'),
('L1-K03-T01', 'KHO_NVL_02', 'L1', 'K03', 'T01', 3000, 'Đang chứa', 'Khả dụng'),
('L1-K03-T02', 'KHO_NVL_02', 'L1', 'K03', 'T02', 3000, 'Trống', 'Khả dụng'),
('L1-K03-T03', 'KHO_NVL_02', 'L1', 'K03', 'T03', 3000, 'Trống', 'Khả dụng'),
('L1-K04-T01', 'KHO_NVL_02', 'L1', 'K04', 'T01', 3000, 'Đầy', 'Khả dụng'),
('L1-K04-T02', 'KHO_NVL_02', 'L1', 'K04', 'T02', 3000, 'Trống', 'Khả dụng'),
('L1-K04-T03', 'KHO_NVL_02', 'L1', 'K04', 'T03', 3000, 'Trống', 'Khả dụng'),
-- Kho Thành Phẩm Bánh Quy 1 (KHO_TP_01 - Dãy B1, B2)
('B1-K01-T01', 'KHO_TP_01', 'B1', 'K01', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K01-T02', 'KHO_TP_01', 'B1', 'K01', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B1-K01-T03', 'KHO_TP_01', 'B1', 'K01', 'T03', 800, 'Đang chứa', 'Khả dụng'),
('B1-K02-T01', 'KHO_TP_01', 'B1', 'K02', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K02-T02', 'KHO_TP_01', 'B1', 'K02', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B1-K02-T03', 'KHO_TP_01', 'B1', 'K02', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K03-T01', 'KHO_TP_01', 'B1', 'K03', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K03-T02', 'KHO_TP_01', 'B1', 'K03', 'T02', 800, 'Trống', 'Khả dụng'),
('B1-K03-T03', 'KHO_TP_01', 'B1', 'K03', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K04-T01', 'KHO_TP_01', 'B1', 'K04', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B1-K04-T02', 'KHO_TP_01', 'B1', 'K04', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B1-K04-T03', 'KHO_TP_01', 'B1', 'K04', 'T03', 800, 'Trống', 'Khả dụng'),
('B1-K05-T01', 'KHO_TP_01', 'B1', 'K05', 'T01', 800, 'Trống', 'Khả dụng'),
('B1-K05-T02', 'KHO_TP_01', 'B1', 'K05', 'T02', 800, 'Trống', 'Khả dụng'),
('B1-K05-T03', 'KHO_TP_01', 'B1', 'K05', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K01-T01', 'KHO_TP_01', 'B2', 'K01', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B2-K01-T02', 'KHO_TP_01', 'B2', 'K01', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B2-K01-T03', 'KHO_TP_01', 'B2', 'K01', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K02-T01', 'KHO_TP_01', 'B2', 'K02', 'T01', 800, 'Đầy', 'Khả dụng'),
('B2-K02-T02', 'KHO_TP_01', 'B2', 'K02', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B2-K02-T03', 'KHO_TP_01', 'B2', 'K02', 'T03', 800, 'Đang chứa', 'Khả dụng'),
('B2-K03-T01', 'KHO_TP_01', 'B2', 'K03', 'T01', 800, 'Đang chứa', 'Khả dụng'),
('B2-K03-T02', 'KHO_TP_01', 'B2', 'K03', 'T02', 800, 'Đang chứa', 'Khả dụng'),
('B2-K03-T03', 'KHO_TP_01', 'B2', 'K03', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K04-T01', 'KHO_TP_01', 'B2', 'K04', 'T01', 800, 'Trống', 'Khả dụng'),
('B2-K04-T02', 'KHO_TP_01', 'B2', 'K04', 'T02', 800, 'Trống', 'Khả dụng'),
('B2-K04-T03', 'KHO_TP_01', 'B2', 'K04', 'T03', 800, 'Trống', 'Khả dụng'),
('B2-K05-T01', 'KHO_TP_01', 'B2', 'K05', 'T01', 800, 'Trống', 'Khả dụng'),
('B2-K05-T02', 'KHO_TP_01', 'B2', 'K05', 'T02', 800, 'Trống', 'Khả dụng'),
('B2-K05-T03', 'KHO_TP_01', 'B2', 'K05', 'T03', 800, 'Trống', 'Khả dụng'),
-- Kho Thành Phẩm Bánh Quy 2 (KHO_TP_02 - Dãy C1)
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
-- Kho Hàng Lỗi & Cách Ly (KHO_LOI_01 - Dãy X1)
('X1-K01-T01', 'KHO_LOI_01', 'X1', 'K01', 'T01', 300, 'Đang chứa', 'Khả dụng'),
('X1-K01-T02', 'KHO_LOI_01', 'X1', 'K01', 'T02', 300, 'Trống', 'Khả dụng'),
('X1-K01-T03', 'KHO_LOI_01', 'X1', 'K01', 'T03', 300, 'Trống', 'Khả dụng'),
('X1-K02-T01', 'KHO_LOI_01', 'X1', 'K02', 'T01', 300, 'Trống', 'Khả dụng'),
('X1-K02-T02', 'KHO_LOI_01', 'X1', 'K02', 'T02', 300, 'Trống', 'Khả dụng'),
('X1-K02-T03', 'KHO_LOI_01', 'X1', 'K02', 'T03', 300, 'Trống', 'Khả dụng');


-- =====================================================================
-- PHẦN 3: QUẢN LÝ LÔ HÀNG & TỒN KHO THỜI GIAN THỰC (3 BẢNG: 11 -> 13)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 11: longuyenlieu (Trần Văn Sang - UC 1.1, 1.3, 1.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS longuyenlieu;
CREATE TABLE longuyenlieu (
  maLoNL VARCHAR(30) NOT NULL,
  maNL VARCHAR(20) NOT NULL,
  maNCC VARCHAR(20) DEFAULT NULL,
  ngaySanXuat DATE NOT NULL,
  hanSuDung DATE NOT NULL,
  soLuongLo DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  soLuongTon DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  trangThaiKiemDinh VARCHAR(50) NOT NULL DEFAULT 'Chờ kiểm định',
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ điều phối',
  PRIMARY KEY (maLoNL),
  KEY fk_longuyenlieu_nguyenlieu (maNL),
  KEY fk_longuyenlieu_ncc (maNCC),
  CONSTRAINT fk_longuyenlieu_nguyenlieu FOREIGN KEY (maNL) REFERENCES nguyenlieu (maNL),
  CONSTRAINT fk_longuyenlieu_ncc FOREIGN KEY (maNCC) REFERENCES nhacungcap (maNCC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO longuyenlieu (maLoNL, maNL, maNCC, ngaySanXuat, hanSuDung, soLuongLo, soLuongTon, trangThaiKiemDinh, trangThai) VALUES
-- 1. Các lô ĐÃ LƯU KHO (Dùng cho FEFO xuất NVL, báo cáo cận hạn, kiểm kê & tiêu hủy)
('LONL_20261001_001', 'NL001', 'NCC01', '2026-09-01', '2026-11-25', 1000.00, 1000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261001_002', 'NL001', 'NCC01', '2026-09-15', '2026-12-10', 1500.00, 1500.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261001_003', 'NL001', 'NCC01', '2026-10-01', '2027-03-15', 2000.00, 2000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261005_001', 'NL003', 'NCC02', '2026-09-10', '2026-11-28', 800.00, 800.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261005_002', 'NL003', 'NCC02', '2026-10-01', '2027-01-15', 1200.00, 400.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261008_001', 'NL004', 'NCC03', '2026-09-05', '2026-12-05', 2500.00, 1200.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261008_002', 'NL004', 'NCC03', '2026-10-05', '2027-04-10', 3000.00, 1200.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261010_001', 'NL006', 'NCC04', '2026-10-08', '2026-11-22', 5000.00, 2500.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261010_002', 'NL006', 'NCC04', '2026-10-18', '2026-12-01', 6000.00, 3000.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261012_001', 'NL007', 'NCC05', '2026-09-15', '2026-12-20', 600.00, 600.00, 'Đạt', 'Đã lưu kho'),
-- 2. Các lô ĐẠT QC - CHỜ ĐIỀU PHỐI (Dành cho Sang test UC 1.3 Điều phối nhập nguyên liệu)
('LONL_20261021_001', 'NL001', 'NCC01', '2026-10-18', '2027-04-18', 1800.00, 0.00, 'Đạt', 'Chờ điều phối'),
('LONL_20261021_002', 'NL003', 'NCC02', '2026-10-19', '2027-02-19', 700.00, 0.00, 'Đạt', 'Chờ điều phối'),
-- 3. Các lô ĐẠT QC - CHỜ NHẬP KHO (Dành cho Sang test UC 1.4 Lập phiếu nhập nguyên liệu)
('LONL_20261022_001', 'NL001', 'NCC01', '2026-10-15', '2027-04-15', 2000.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261022_002', 'NL002', 'NCC01', '2026-10-16', '2027-04-16', 1500.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261023_001', 'NL003', 'NCC02', '2026-10-18', '2027-02-18', 900.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261023_002', 'NL004', 'NCC03', '2026-10-20', '2027-04-20', 2200.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261024_001', 'NL005', 'NCC02', '2026-10-19', '2027-04-19', 1100.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261024_002', 'NL008', 'NCC07', '2026-10-10', '2027-10-10', 300.00, 0.00, 'Đạt', 'Chờ nhập kho'),
('LONL_20261025_001', 'NL009', 'NCC06', '2026-10-12', '2027-04-12', 250.00, 0.00, 'Đạt', 'Chờ nhập kho'),
-- 4. Các lô CHỜ KIỂM ĐỊNH / IMPORT TỪ EXCEL BỔ SUNG UC 1.1 (Dành cho Sang UC 1.1 & Phúc QC UC 6.1)
('LONL_20261026_001', 'NL001', 'NCC01', '2026-10-24', '2027-04-24', 2500.00, 0.00, 'Chờ kiểm định QC', 'Chờ điều phối'),
('LONL_20261026_002', 'NL002', 'NCC01', '2026-10-25', '2027-04-25', 1800.00, 0.00, 'Chờ kiểm định QC', 'Chờ điều phối'),
('LONL_20261026_003', 'NL003', 'NCC02', '2026-10-23', '2027-02-23', 1200.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261026_004', 'NL004', 'NCC03', '2026-10-25', '2027-04-25', 3000.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261026_005', 'NL005', 'NCC02', '2026-10-22', '2027-04-22', 800.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261026_006', 'NL006', 'NCC04', '2026-10-26', '2026-11-26', 4000.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261026_007', 'NL007', 'NCC05', '2026-10-20', '2027-01-20', 700.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261026_008', 'NL011', 'NCC09', '2026-10-22', '2027-04-22', 600.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
-- 5. Lô KHÔNG ĐẠT QC (Test quy trình lập biên bản không đạt / trả NCC)
('LONL_20261025_099', 'NL001', 'NCC01', '2026-10-10', '2027-04-10', 500.00, 0.00, 'Không đạt', 'Khóa');

-- ---------------------------------------------------------------------
-- BẢNG 12: lothanhpham (Nguyễn Hữu Lộc - UC 3.1, 3.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS lothanhpham;
CREATE TABLE lothanhpham (
  maLoTP VARCHAR(30) NOT NULL,
  maTP VARCHAR(20) NOT NULL,
  phanXuong VARCHAR(100) DEFAULT NULL,
  ngaySanXuat DATE NOT NULL,
  hanSuDung DATE NOT NULL,
  soLuongLo DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  soLuongTon DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  trangThaiKiemDinh VARCHAR(50) NOT NULL DEFAULT 'Chờ kiểm định',
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ điều phối',
  PRIMARY KEY (maLoTP),
  KEY fk_lothanhpham_thanhpham (maTP),
  CONSTRAINT fk_lothanhpham_thanhpham FOREIGN KEY (maTP) REFERENCES thanhpham (maTP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO lothanhpham (maLoTP, maTP, phanXuong, ngaySanXuat, hanSuDung, soLuongLo, soLuongTon, trangThaiKiemDinh, trangThai) VALUES
-- 1. Lô ĐÃ LƯU KHO (Dùng cho Thuận FEFO xuất TP, báo cáo cận hạn & kiểm kê)
('LOTP_20261002_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-02', '2026-11-26', 600.00, 600.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261005_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-05', '2026-12-15', 850.00, 850.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261015_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-15', '2027-04-15', 1000.00, 1000.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261003_001', 'TP002', 'Phân xưởng nướng số 1', '2026-10-03', '2026-11-28', 500.00, 500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261012_001', 'TP002', 'Phân xưởng nướng số 1', '2026-10-12', '2027-03-20', 800.00, 800.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261004_001', 'TP004', 'Phân xưởng đóng gói số 2', '2026-10-04', '2026-11-24', 1200.00, 1200.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261014_001', 'TP004', 'Phân xưởng đóng gói số 2', '2026-10-14', '2027-02-14', 1500.00, 1500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261006_001', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-06', '2026-12-02', 2000.00, 2000.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261016_001', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-16', '2027-03-30', 2500.00, 2500.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261008_001', 'TP008', 'Phân xưởng bánh nướng số 2', '2026-10-08', '2026-12-08', 1100.00, 1100.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261009_001', 'TP010', 'Phân xưởng nướng đặc sản 4', '2026-10-09', '2027-02-09', 700.00, 700.00, 'Đạt', 'Đã lưu kho'),
-- 2. Lô ĐÃ KIỂM ĐỊNH (ĐẠT & KHÔNG ĐẠT) - CHỜ ĐIỀU PHỐI (Dành cho Lộc test UC 3.1 Điều phối nhập TP)
('LOTP_20261025_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-25', '2027-04-25', 900.00, 0.00, 'Đạt', 'Chờ điều phối'),
('LOTP_20261025_002', 'TP003', 'Phân xưởng nướng số 1', '2026-10-25', '2027-04-25', 250.00, 0.00, 'Không đạt', 'Chờ điều phối'), -- Test Luồng 5.1 điều phối về Kho Hàng Lỗi
-- 3. Lô ĐẠT QC - CHỜ NHẬP KHO (Dành cho Lộc test UC 3.2 Lập phiếu nhập thành phẩm)
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
-- 4. Lô CHỜ KIỂM ĐỊNH (Dành cho Phúc test UC 6.2 Lập biên bản kiểm định thành phẩm)
('LOTP_20261027_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-27', '2027-04-27', 1000.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối'),
('LOTP_20261027_002', 'TP006', 'Phân xưởng bánh kẹp kem số 3', '2026-10-27', '2027-03-27', 1500.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối');

-- ---------------------------------------------------------------------
-- BẢNG 13: tonkho (Võ Thị Tuyết Ngân - UC 5.2, 6.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS tonkho;
CREATE TABLE tonkho (
  maTonKho VARCHAR(20) NOT NULL,
  loaiHang VARCHAR(20) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) NOT NULL,
  slTon DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  soLuongTamGiu DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  ngayCapNhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Khả dụng',
  PRIMARY KEY (maTonKho),
  UNIQUE KEY uq_tonkho_lo_vitri (maLo, maViTri),
  KEY fk_tonkho_vitri (maViTri),
  CONSTRAINT fk_tonkho_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tonkho (maTonKho, loaiHang, maLo, maViTri, slTon, soLuongTamGiu, ngayCapNhat, trangThai) VALUES
-- Tồn kho nguyên liệu khô (Kho NVL 01)
('TK_NL_001', 'NL', 'LONL_20261001_001', 'A1-K01-T01', 500.00, 0.00, '2026-10-20 08:30:00', 'Khả dụng'),
('TK_NL_002', 'NL', 'LONL_20261001_001', 'A1-K01-T02', 500.00, 0.00, '2026-10-20 08:30:00', 'Khả dụng'),
('TK_NL_003', 'NL', 'LONL_20261001_002', 'A1-K02-T01', 600.00, 20.00, '2026-10-21 09:00:00', 'Đang giữ'),
('TK_NL_004', 'NL', 'LONL_20261001_002', 'A1-K02-T02', 500.00, 0.00, '2026-10-21 09:00:00', 'Khả dụng'),
('TK_NL_005', 'NL', 'LONL_20261001_002', 'A1-K02-T03', 400.00, 0.00, '2026-10-21 09:00:00', 'Khả dụng'),
('TK_NL_006', 'NL', 'LONL_20261001_003', 'A1-K03-T01', 600.00, 0.00, '2026-10-22 10:15:00', 'Khả dụng'),
('TK_NL_007', 'NL', 'LONL_20261001_003', 'A1-K03-T02', 600.00, 0.00, '2026-10-22 10:15:00', 'Khả dụng'),
('TK_NL_008', 'NL', 'LONL_20261008_001', 'A2-K01-T01', 600.00, 0.00, '2026-10-22 11:00:00', 'Khả dụng'),
('TK_NL_009', 'NL', 'LONL_20261008_001', 'A2-K01-T02', 600.00, 0.00, '2026-10-22 11:00:00', 'Khả dụng'),
('TK_NL_010', 'NL', 'LONL_20261008_002', 'A2-K02-T01', 600.00, 0.00, '2026-10-23 14:00:00', 'Khả dụng'),
('TK_NL_011', 'NL', 'LONL_20261008_002', 'A2-K02-T02', 600.00, 0.00, '2026-10-23 14:00:00', 'Khả dụng'),
-- Tồn kho nguyên liệu lạnh (Kho NVL 02)
('TK_NL_012', 'NL', 'LONL_20261005_001', 'L1-K01-T01', 400.00, 0.00, '2026-10-20 15:30:00', 'Khả dụng'),
('TK_NL_013', 'NL', 'LONL_20261005_001', 'L1-K01-T02', 400.00, 0.00, '2026-10-20 15:30:00', 'Khả dụng'),
('TK_NL_014', 'NL', 'LONL_20261005_002', 'L1-K02-T01', 400.00, 0.00, '2026-10-22 16:00:00', 'Khả dụng'),
('TK_NL_015', 'NL', 'LONL_20261010_001', 'L1-K03-T01', 2500.00, 40.00, '2026-10-24 08:00:00', 'Chờ hủy'),
('TK_NL_016', 'NL', 'LONL_20261010_002', 'L1-K04-T01', 3000.00, 0.00, '2026-10-25 09:00:00', 'Khả dụng'),
-- Tồn kho thành phẩm (Kho TP 01 & Kho TP 02 & Kho Lỗi)
('TK_TP_001', 'TP', 'LOTP_20261002_001', 'B1-K01-T01', 600.00, 0.00, '2026-10-20 10:00:00', 'Khả dụng'),
('TK_TP_002', 'TP', 'LOTP_20261005_001', 'B1-K01-T02', 500.00, 15.00, '2026-10-21 11:30:00', 'Chờ hủy'),
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
('TK_TP_015', 'TP', 'LOTP_20261008_001', 'B2-K03-T02', 500.00, 0.00, '2026-10-23 10:45:00', 'Khả dụng'),
('TK_TP_016', 'TP', 'LOTP_20261009_001', 'C1-K01-T01', 700.00, 0.00, '2026-10-23 14:20:00', 'Khả dụng'),
('TK_TP_017', 'TP', 'LOTP_20261005_001', 'X1-K01-T01', 15.00, 15.00, '2026-10-26 15:40:00', 'Chờ hủy');


-- =====================================================================
-- PHẦN 4: KIỂM ĐỊNH CHẤT LƯỢNG QC (3 BẢNG: 14 -> 16)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 14: tieuchikiemdinh (Lê Minh Thiện Phúc - UC 6.1, 6.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS tieuchikiemdinh;
CREATE TABLE tieuchikiemdinh (
  maTieuChi VARCHAR(20) NOT NULL,
  tenTieuChi VARCHAR(100) NOT NULL,
  quyChuan VARCHAR(500) NOT NULL,
  loaiHang VARCHAR(50) NOT NULL DEFAULT 'Nguyên liệu',
  maNL VARCHAR(20) DEFAULT NULL,
  maTP VARCHAR(20) DEFAULT NULL,
  batBuoc TINYINT(1) NOT NULL DEFAULT 1,
  trangThai TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (maTieuChi),
  KEY fk_tckd_nl (maNL),
  KEY fk_tckd_tp (maTP),
  CONSTRAINT fk_tckd_nl FOREIGN KEY (maNL) REFERENCES nguyenlieu (maNL),
  CONSTRAINT fk_tckd_tp FOREIGN KEY (maTP) REFERENCES thanhpham (maTP)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tieuchikiemdinh (maTieuChi, tenTieuChi, quyChuan, loaiHang, maNL, maTP, batBuoc, trangThai) VALUES
('TC000001', 'Độ ẩm bột mì tiêu chuẩn', 'Độ ẩm ≤ 14.0%, bột tơi mịn không vón cục', 'Nguyên liệu', 'NL001', NULL, 1, 1),
('TC000002', 'Chỉ số Gluten ướt', 'Hàm lượng Gluten ướt từ 9.5% đến 11.5%', 'Nguyên liệu', 'NL001', NULL, 1, 1),
('TC000003', 'Cảm quan màu sắc & tạp chất bột mì', 'Trắng ngà tự nhiên, không sâu mọt, không mùi lạ', 'Nguyên liệu', 'NL001', NULL, 1, 1),
('TC000004', 'Hàm lượng chất béo bơ lạt', 'Chất béo sữa ≥ 82.0%, cấu trúc đồng nhất', 'Nguyên liệu', 'NL003', NULL, 1, 1),
('TC000005', 'Nhiệt độ giao nhận xe lạnh', 'Nhiệt độ tâm sản phẩm từ 2°C đến 6°C', 'Nguyên liệu', 'NL003', NULL, 1, 1),
('TC000006', 'Độ tinh khiết đường Saccharose', 'Độ Pol ≥ 99.7%, tinh thể trắng sáng khô ráo', 'Nguyên liệu', 'NL004', NULL, 1, 1),
('TC000007', 'Độ tươi trứng gà (chỉ số Haugh)', 'Haugh unit ≥ 72 (Hạng AA), vỏ sạch không nứt vỡ', 'Nguyên liệu', 'NL006', NULL, 1, 1),
('TC000008', 'Cảm quan độ giòn và mùi vị bánh', 'Bánh nướng vàng đều, giòn xốp, thơm bơ tự nhiên', 'Thành phẩm', NULL, 'TP001', 1, 1),
('TC000009', 'Độ ẩm bánh quy thành phẩm', 'Độ ẩm bánh ≤ 3.5% sau khi làm nguội', 'Thành phẩm', NULL, 'TP001', 1, 1),
('TC000010', 'Quy cách đóng gói & độ kín hộp thiếc', 'Hộp thiếc không móp méo, màng co niêm phong kín khí', 'Thành phẩm', NULL, 'TP001', 1, 1),
('TC000011', 'Cảm quan vỏ bánh socola & nhân kem', 'Vỏ socola giòn đắng đặc trưng, nhân kem vani mịn', 'Thành phẩm', NULL, 'TP006', 1, 1),
('TC000012', 'Kiểm tra ngoại quan thùng carton vận chuyển', 'Thùng carton nguyên vẹn, in rõ NSX/HSD', 'Thành phẩm', NULL, 'TP001', 0, 1);

-- ---------------------------------------------------------------------
-- BẢNG 15: bienbankiemdinh (Lê Minh Thiện Phúc - UC 6.1, 6.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bienbankiemdinh;
CREATE TABLE bienbankiemdinh (
  maBienBan VARCHAR(20) NOT NULL,
  loaiKiemDinh TINYINT NOT NULL,
  maLoNL VARCHAR(30) DEFAULT NULL,
  maLoTP VARCHAR(30) DEFAULT NULL,
  ngayKiemDinh DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiKiemDinh VARCHAR(20) NOT NULL,
  ketQuaTongThe TINYINT(1) DEFAULT NULL,
  lyDoKhongDat VARCHAR(500) DEFAULT NULL,
  ghiChu VARCHAR(500) DEFAULT NULL,
  trangThaiBienBan TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (maBienBan),
  KEY fk_bbkd_lonl (maLoNL),
  KEY fk_bbkd_lotp (maLoTP),
  KEY fk_bbkd_nv (nguoiKiemDinh),
  CONSTRAINT fk_bbkd_lonl FOREIGN KEY (maLoNL) REFERENCES longuyenlieu (maLoNL),
  CONSTRAINT fk_bbkd_lotp FOREIGN KEY (maLoTP) REFERENCES lothanhpham (maLoTP),
  CONSTRAINT fk_bbkd_nv FOREIGN KEY (nguoiKiemDinh) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bienbankiemdinh (maBienBan, loaiKiemDinh, maLoNL, maLoTP, ngayKiemDinh, nguoiKiemDinh, ketQuaTongThe, lyDoKhongDat, ghiChu, trangThaiBienBan) VALUES
('KD000001', 1, 'LONL_20261001_001', NULL, '2026-09-02 08:30:00', 'NV001', 1, NULL, 'Lô bột mì số 8 đạt chất lượng cao, đủ điều kiện nhập kho', 1),
('KD000002', 1, 'LONL_20261005_001', NULL, '2026-09-12 09:15:00', 'NV001', 1, NULL, 'Bơ lạt Anchor giữ lạnh 4°C đạt chuẩn', 1),
('KD000003', 1, 'LONL_20261008_001', NULL, '2026-09-06 14:00:00', 'NV013', 1, NULL, 'Đường Biên Hòa nguyên bao bì, khô tơi', 1),
('KD000004', 1, 'LONL_20261010_001', NULL, '2026-10-09 10:20:00', 'NV001', 1, NULL, 'Trứng gà Ba Huân đạt chuẩn AA', 1),
('KD000005', 1, 'LONL_20261025_099', NULL, '2026-10-25 14:30:00', 'NV001', 0, 'Độ ẩm bột mì đo được 16.4% vượt ngưỡng 14.0%, có dấu hiệu vón cục ẩm mốc', 'Lập biên bản trả lại nhà cung cấp NCC01', 1),
('KD000006', 2, NULL, 'LOTP_20261002_001', '2026-10-02 09:00:00', 'NV001', 1, NULL, 'Bánh quy Danisa 200g vàng giòn đạt chuẩn xuất xưởng', 1),
('KD000007', 2, NULL, 'LOTP_20261006_001', '2026-10-06 10:15:00', 'NV013', 1, NULL, 'Bánh quy Oreo Vani đạt chuẩn cảm quan & bao bì', 1),
('KD000008', 2, NULL, 'LOTP_20261025_002', '2026-10-25 16:00:00', 'NV001', 0, 'Bánh nướng quá lửa 15% mẻ hàng, hộp thiếc hở mép không đạt độ kín khí', 'Chuyển điều phối về Kho Hàng Lỗi cách ly', 1),
('KD000009', 1, 'LONL_20261026_001', NULL, '2026-10-27 08:15:00', 'NV001', NULL, NULL, 'Biên bản nháp đang đo chỉ tiêu mẫu bột mì số 8', 0),
('KD000010', 2, NULL, 'LOTP_20261027_001', '2026-10-27 09:00:00', 'NV013', NULL, NULL, 'Biên bản nháp đang kiểm tra mẻ bánh quy Danisa mới ra lò', 0);

-- ---------------------------------------------------------------------
-- BẢNG 16: chitietkiemdinh (Lê Minh Thiện Phúc - Bổ sung theo feedback GVHD)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietkiemdinh;
CREATE TABLE chitietkiemdinh (
  maChiTiet VARCHAR(20) NOT NULL,
  maBienBan VARCHAR(20) NOT NULL,
  maTieuChi VARCHAR(20) NOT NULL,
  giaTriDoThucTe VARCHAR(100) DEFAULT NULL,
  danhGia TINYINT(1) DEFAULT NULL,
  ghiChu VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (maChiTiet),
  UNIQUE KEY uq_ctkd_bb_tc (maBienBan, maTieuChi),
  KEY fk_ctkd_tc (maTieuChi),
  CONSTRAINT fk_ctkd_bb FOREIGN KEY (maBienBan) REFERENCES bienbankiemdinh (maBienBan) ON DELETE CASCADE,
  CONSTRAINT fk_ctkd_tc FOREIGN KEY (maTieuChi) REFERENCES tieuchikiemdinh (maTieuChi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietkiemdinh (maChiTiet, maBienBan, maTieuChi, giaTriDoThucTe, danhGia, ghiChu) VALUES
('CTKD000001', 'KD000001', 'TC000001', '13.1%', 1, 'Đạt độ ẩm tiêu chuẩn'),
('CTKD000002', 'KD000001', 'TC000002', '10.5%', 1, 'Hàm lượng Gluten tốt'),
('CTKD000003', 'KD000001', 'TC000003', 'Trắng ngà mịn', 1, 'Không tạp chất'),
('CTKD000004', 'KD000002', 'TC000004', '82.6%', 1, 'Đạt độ béo sữa'),
('CTKD000005', 'KD000002', 'TC000005', '3.8°C', 1, 'Xe lạnh duy trì nhiệt tốt'),
('CTKD000006', 'KD000003', 'TC000006', '99.8%', 1, 'Đường trắng tinh khiết'),
('CTKD000007', 'KD000004', 'TC000007', 'Haugh 76', 1, 'Trứng tươi đạt chuẩn AA'),
('CTKD000008', 'KD000005', 'TC000001', '16.4%', 0, 'Vượt ngưỡng độ ẩm cho phép (≤ 14.0%)'),
('CTKD000009', 'KD000005', 'TC000003', 'Vón cục nhẹ', 0, 'Không đạt cảm quan'),
('CTKD000010', 'KD000006', 'TC000008', 'Vàng giòn thơm bơ', 1, 'Đạt chuẩn cảm quan'),
('CTKD000011', 'KD000006', 'TC000009', '2.8%', 1, 'Độ ẩm bánh đạt chuẩn'),
('CTKD000012', 'KD000006', 'TC000010', 'Kín khí 100%', 1, 'Hộp thiếc nguyên vẹn'),
('CTKD000013', 'KD000007', 'TC000011', 'Giòn thơm nhân mịn', 1, 'Đạt chuẩn'),
('CTKD000014', 'KD000008', 'TC000008', 'Màu sẫm cháy cạnh', 0, 'Bánh nướng quá nhiệt'),
('CTKD000015', 'KD000008', 'TC000010', 'Hở nắp 12 hộp', 0, 'Không đạt kín khí'),
('CTKD000016', 'KD000009', 'TC000001', '13.4%', 1, 'Đã đo xong độ ẩm'),
('CTKD000017', 'KD000009', 'TC000002', NULL, NULL, 'Đang chờ kết quả phòng Lab'),
('CTKD000018', 'KD000010', 'TC000008', 'Vàng đều thơm bơ', 1, 'Đã kiểm cảm quan');


-- =====================================================================
-- PHẦN 5: YÊU CẦU NHẬP, IMPORT EXCEL BỔ SUNG (UC 1.1), ĐIỀU PHỐI & NHẬP KHO (4 BẢNG: 17 -> 20)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 17: phieuyeucaunhap (Trần Văn Sang - UC 1.1: Hỗ trợ Import Excel Nhập bổ sung)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS phieuyeucaunhap;
CREATE TABLE phieuyeucaunhap (
  maPhieuYeuCau VARCHAR(30) NOT NULL,
  ngayYeuCau DATE NOT NULL,
  ngayGiaoDuKien DATE DEFAULT NULL,
  nguoiYeuCau VARCHAR(100) NOT NULL,
  loaiYeuCau VARCHAR(50) NOT NULL DEFAULT 'Nhập nguyên liệu từ NCC',
  maNCC VARCHAR(20) DEFAULT NULL,
  maChungTuGoc VARCHAR(50) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ kiểm định',
  PRIMARY KEY (maPhieuYeuCau),
  KEY fk_pycn_ncc (maNCC),
  CONSTRAINT fk_pycn_ncc FOREIGN KEY (maNCC) REFERENCES nhacungcap (maNCC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phieuyeucaunhap (maPhieuYeuCau, ngayYeuCau, ngayGiaoDuKien, nguoiYeuCau, loaiYeuCau, maNCC, maChungTuGoc, trangThai) VALUES
-- Phiếu nhập nguyên liệu định kỳ từ Nhà cung cấp
('PYCN20261021001', '2026-10-21', '2026-10-23', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'NCC01', 'PO-LMVN-2026-088', 'Đã điều phối'),
('PYCN20261022001', '2026-10-22', '2026-10-25', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'NCC02', 'PO-FONT-2026-091', 'Đã điều phối'),
('PYCN20261025001', '2026-10-25', '2026-10-27', 'Trần Thanh Toàn', 'Nhập nguyên liệu từ NCC', 'NCC03', 'PO-SBT-2026-102', 'Chờ kiểm định'),
-- Phiếu NHẬP BỔ SUNG đầu vào từ Import File Excel thông qua Use Case 1.1
('PYCN20261027001', '2026-10-27', '2026-10-28', 'Trần Văn Sang', 'Nhập bổ sung', 'NCC01', 'EXCEL-IMPORT-20261027-01', 'Chờ kiểm định'),
('PYCN20261027002', '2026-10-27', '2026-10-29', 'Trần Thanh Toàn', 'Nhập bổ sung', 'NCC04', 'EXCEL-IMPORT-20261027-02', 'Chờ kiểm định'),
-- Phiếu yêu cầu nhập thành phẩm từ phân xưởng sản xuất (cho Lộc test UC 3.1 Điều phối nhập TP)
('PYCN20261025002', '2026-10-25', '2026-10-26', 'Nguyễn Hữu Lộc', 'Nhập thành phẩm từ phân xưởng', NULL, 'KHSX-2026-Q4-01', 'Đã kiểm định - Đạt'),
('PYCN20261026001', '2026-10-26', '2026-10-27', 'Nguyễn Hữu Lộc', 'Nhập thành phẩm từ phân xưởng', NULL, 'KHSX-2026-Q4-02', 'Đã điều phối');

-- ---------------------------------------------------------------------
-- BẢNG 18: chitietyeucaunhap (Trần Văn Sang - UC 1.1 Chi tiết dòng Import Excel & Yêu cầu nhập)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietyeucaunhap;
CREATE TABLE chitietyeucaunhap (
  maChiTiet VARCHAR(30) NOT NULL,
  maPhieuYeuCau VARCHAR(30) NOT NULL,
  maMatHang VARCHAR(20) NOT NULL,
  soLuongYeuCau DECIMAL(12,2) NOT NULL,
  donViTinh VARCHAR(20) NOT NULL,
  quyCachDongGoi VARCHAR(100) DEFAULT NULL,
  maLo VARCHAR(30) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ xử lý',
  PRIMARY KEY (maChiTiet),
  KEY fk_ctycn_phieu (maPhieuYeuCau),
  CONSTRAINT fk_ctycn_phieu FOREIGN KEY (maPhieuYeuCau) REFERENCES phieuyeucaunhap (maPhieuYeuCau) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietyeucaunhap (maChiTiet, maPhieuYeuCau, maMatHang, soLuongYeuCau, donViTinh, quyCachDongGoi, maLo, trangThai) VALUES
-- Chi tiết phiếu nhập từ NCC
('CTYCN000001', 'PYCN20261021001', 'NL001', 2000.00, 'Kg', 'Bao PP 25kg (80 bao)', 'LONL_20261022_001', 'Đã điều phối'),
('CTYCN000002', 'PYCN20261021001', 'NL002', 1500.00, 'Kg', 'Bao PP 25kg (60 bao)', 'LONL_20261022_002', 'Đã điều phối'),
('CTYCN000003', 'PYCN20261022001', 'NL003', 900.00, 'Kg', 'Thùng carton lạnh 25kg', 'LONL_20261023_001', 'Đã điều phối'),
('CTYCN000004', 'PYCN20261025001', 'NL004', 3000.00, 'Kg', 'Bao tải 50kg (60 bao)', 'LONL_20261026_004', 'Chờ xử lý'),
-- Chi tiết dòng hàng NHẬP BỔ SUNG được Import từ Excel (Use Case 1.1)
('CTYCN000005', 'PYCN20261027001', 'NL001', 2500.00, 'Kg', 'Bao 25kg - Bổ sung ca nướng tăng cường', 'LONL_20261026_001', 'Chờ xử lý'),
('CTYCN000006', 'PYCN20261027001', 'NL002', 1800.00, 'Kg', 'Bao 25kg - Bổ sung nguyên liệu Q4', 'LONL_20261026_002', 'Chờ xử lý'),
('CTYCN000007', 'PYCN20261027002', 'NL006', 4000.00, 'Quả', 'Khay nhựa 30 quả - Nhập bổ sung khẩn', 'LONL_20261026_006', 'Chờ xử lý'),
('CTYCN000008', 'PYCN20261027002', 'NL011', 600.00, 'Kg', 'Bao hút chân không 20kg', 'LONL_20261026_008', 'Chờ xử lý'),
-- Chi tiết phiếu nhập thành phẩm từ phân xưởng (Cho Lộc test UC 3.1 Điều phối nhập TP)
('CTYCN000009', 'PYCN20261025002', 'TP001', 900.00, 'Hộp', '24 hộp/thùng (37.5 thùng)', 'LOTP_20261025_001', 'Chờ xử lý'),
('CTYCN000010', 'PYCN20261025002', 'TP003', 250.00, 'Hộp', '6 hộp/thùng (Lô lỗi QC cách ly)', 'LOTP_20261025_002', 'Chờ xử lý'),
('CTYCN000011', 'PYCN20261026001', 'TP001', 1200.00, 'Hộp', '24 hộp/thùng (50 thùng)', 'LOTP_20261026_001', 'Đã điều phối'),
('CTYCN000012', 'PYCN20261026001', 'TP004', 1500.00, 'Gói', '20 gói/thùng (75 thùng)', 'LOTP_20261026_003', 'Đã điều phối');

-- ---------------------------------------------------------------------
-- BẢNG 19: lenhdieuphoinhap (Trần Văn Sang - UC 1.3 & Nguyễn Hữu Lộc - UC 3.1)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS lenhdieuphoinhap;
CREATE TABLE lenhdieuphoinhap (
  maLenhDieuPhoi VARCHAR(30) NOT NULL,
  maPhieuYeuCau VARCHAR(30) DEFAULT NULL,
  loaiHang VARCHAR(20) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  maKhoTiepNhan VARCHAR(20) NOT NULL,
  soLuongPhanBo DECIMAL(12,2) NOT NULL,
  ngayDieuPhoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiDieuPhoi VARCHAR(100) NOT NULL,
  lyDoGiaiTrinh VARCHAR(255) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ nhập kho',
  PRIMARY KEY (maLenhDieuPhoi),
  KEY fk_lenhdieuphoinhap_kho (maKhoTiepNhan),
  KEY fk_lenhdieuphoinhap_pycn (maPhieuYeuCau),
  CONSTRAINT fk_lenhdieuphoinhap_kho FOREIGN KEY (maKhoTiepNhan) REFERENCES kho (maKho),
  CONSTRAINT fk_lenhdieuphoinhap_pycn FOREIGN KEY (maPhieuYeuCau) REFERENCES phieuyeucaunhap (maPhieuYeuCau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO lenhdieuphoinhap (maLenhDieuPhoi, maPhieuYeuCau, loaiHang, maLo, maKhoTiepNhan, soLuongPhanBo, ngayDieuPhoi, nguoiDieuPhoi, lyDoGiaiTrinh, trangThai) VALUES
-- 1. Lệnh nhập THÀNH PHẨM (Dành cho Lộc test UC 3.2 Lập phiếu nhập TP)
('LDPN_TP_001', 'PYCN20261026001', 'TP', 'LOTP_20261026_001', 'KHO_TP_01', 1200.00, '2026-10-26 08:30:00', 'NV003', 'Tiếp nhận mẻ bánh quy bơ Danisa 200g từ PX1', 'Chờ nhập kho'),
('LDPN_TP_002', 'PYCN20261026001', 'TP', 'LOTP_20261026_002', 'KHO_TP_01', 800.00, '2026-10-26 09:15:00', 'NV003', 'Tiếp nhận mẻ bánh quy bơ Danisa Hộp thiếc 454g', 'Chờ nhập kho'),
('LDPN_TP_003', 'PYCN20261026001', 'TP', 'LOTP_20261026_003', 'KHO_TP_01', 1500.00, '2026-10-26 10:00:00', 'NV004', 'Nhập kho bánh quy Cosy Marie Kinh Đô 300g đóng thùng', 'Chờ nhập kho'),
('LDPN_TP_004', 'PYCN20261026001', 'TP', 'LOTP_20261026_004', 'KHO_TP_01', 1000.00, '2026-10-26 11:20:00', 'NV004', 'Tiếp nhận bánh quy Cosy Mè dinh dưỡng từ PX2', 'Chờ nhập kho'),
('LDPN_TP_005', 'PYCN20261026001', 'TP', 'LOTP_20261026_005', 'KHO_TP_01', 2000.00, '2026-10-26 13:45:00', 'NV003', 'Nhập kho lô bánh quy Oreo Vani từ xưởng kẹp kem PX3', 'Chờ nhập kho'),
('LDPN_TP_006', 'PYCN20261026001', 'TP', 'LOTP_20261026_006', 'KHO_TP_01', 1400.00, '2026-10-26 14:30:00', 'NV004', 'Tiếp nhận bánh cracker AFC Lúa mì dinh dưỡng', 'Chờ nhập kho'),
('LDPN_TP_007', 'PYCN20261026001', 'TP', 'LOTP_20261026_007', 'KHO_TP_02', 1100.00, '2026-10-26 15:10:00', 'NV003', 'Tiếp nhận mẻ bánh cracker AFC Tảo biển xanh', 'Chờ nhập kho'),
('LDPN_TP_008', 'PYCN20261026001', 'TP', 'LOTP_20261026_008', 'KHO_TP_02', 900.00, '2026-10-26 15:50:00', 'NV004', 'Nhập kho bánh quy dừa nướng Bến Tre thơm giòn', 'Chờ nhập kho'),
('LDPN_TP_009', 'PYCN20261026001', 'TP', 'LOTP_20261026_009', 'KHO_TP_02', 600.00, '2026-10-26 16:30:00', 'NV003', 'Nhập mẻ bánh gừng quế mùa vụ Giáng Sinh', 'Chờ nhập kho'),
('LDPN_TP_010', 'PYCN20261026001', 'TP', 'LOTP_20261026_010', 'KHO_TP_01', 1250.00, '2026-10-26 17:00:00', 'NV004', 'Tiếp nhận bánh quy socola chip Choreo giòn tan', 'Chờ nhập kho'),
('LDPN_TP_DONE_01', 'PYCN20261025002', 'TP', 'LOTP_20261002_001', 'KHO_TP_01', 600.00, '2026-10-02 08:30:00', 'NV003', 'Đã nhập xong mẻ Danisa 200g đầu tháng', 'Hoàn tất nhập kho'),
('LDPN_TP_DONE_02', 'PYCN20261025002', 'TP', 'LOTP_20261006_001', 'KHO_TP_01', 2000.00, '2026-10-06 09:00:00', 'NV004', 'Đã nhập xong lô bánh Oreo kem vani', 'Hoàn tất nhập kho (Hao hụt)'),
-- 2. Lệnh nhập NGUYÊN LIỆU (Dành cho Sang test UC 1.4 Lập phiếu nhập NL)
('LDPN_NL_001', 'PYCN20261021001', 'NL', 'LONL_20261022_001', 'KHO_NVL_01', 2000.00, '2026-10-22 09:00:00', 'NV004', 'Điều phối nhập kho 2 tấn bột mì số 8 từ NCC Lúa Mì VN', 'Chờ nhập kho'),
('LDPN_NL_002', 'PYCN20261021001', 'NL', 'LONL_20261022_002', 'KHO_NVL_01', 1500.00, '2026-10-22 10:30:00', 'NV004', 'Điều phối 1.5 tấn bột mì số 11 làm bánh Cosy', 'Chờ nhập kho'),
('LDPN_NL_003', 'PYCN20261022001', 'NL', 'LONL_20261023_001', 'KHO_NVL_02', 900.00, '2026-10-23 11:15:00', 'NV003', 'Nhập kho lạnh 900kg bơ lạt Anchor giao bằng xe lạnh', 'Chờ nhập kho'),
('LDPN_NL_004', 'PYCN20261022001', 'NL', 'LONL_20261023_002', 'KHO_NVL_01', 2200.00, '2026-10-23 14:00:00', 'NV004', 'Điều phối nhập đường tinh luyện Biên Hòa Pure', 'Chờ nhập kho'),
('LDPN_NL_005', 'PYCN20261022001', 'NL', 'LONL_20261024_001', 'KHO_NVL_01', 1100.00, '2026-10-24 15:30:00', 'NV003', 'Nhập sữa bột béo nguyên kem Fonterra New Zealand', 'Chờ nhập kho'),
('LDPN_NL_006', 'PYCN20261022001', 'NL', 'LONL_20261024_002', 'KHO_NVL_01', 300.00, '2026-10-24 16:15:00', 'NV004', 'Nhập bột nở Alsa Pháp phục vụ dây chuyền nướng', 'Chờ nhập kho'),
('LDPN_NL_DONE_01', 'PYCN20261021001', 'NL', 'LONL_20261001_001', 'KHO_NVL_01', 1000.00, '2026-10-01 08:30:00', 'NV004', 'Đã nhập xong bột mì số 8 lô 001', 'Hoàn tất nhập kho'),
('LDPN_NL_DONE_02', 'PYCN20261022001', 'NL', 'LONL_20261005_001', 'KHO_NVL_02', 800.00, '2026-10-05 09:45:00', 'NV003', 'Đã nhập xong bơ lạt Anchor lô 001', 'Hoàn tất nhập kho');

-- ---------------------------------------------------------------------
-- BẢNG 20: phieunhapkho (Nguyễn Hữu Lộc - UC 3.2 & Trần Văn Sang - UC 1.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS phieunhapkho;
CREATE TABLE phieunhapkho (
  maPNK VARCHAR(30) NOT NULL,
  maLenhDieuPhoi VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) NOT NULL,
  ngayNhapThucTe DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiLap VARCHAR(100) NOT NULL,
  slThucNhap DECIMAL(12,2) NOT NULL,
  soLuongChenhLech DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  lyDoChenhLech VARCHAR(255) DEFAULT NULL,
  ghiChu VARCHAR(500) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Hoàn tất',
  PRIMARY KEY (maPNK),
  KEY fk_phieunhapkho_lenh (maLenhDieuPhoi),
  KEY fk_phieunhapkho_vitri (maViTri),
  CONSTRAINT fk_phieunhapkho_lenh FOREIGN KEY (maLenhDieuPhoi) REFERENCES lenhdieuphoinhap (maLenhDieuPhoi),
  CONSTRAINT fk_phieunhapkho_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phieunhapkho (maPNK, maLenhDieuPhoi, maViTri, ngayNhapThucTe, nguoiLap, slThucNhap, soLuongChenhLech, lyDoChenhLech, ghiChu, trangThai) VALUES
('PNK_TP_20261002_001', 'LDPN_TP_DONE_01', 'B1-K01-T01', '2026-10-02 09:30:00', 'NV006', 600.00, 0.00, NULL, 'Bao bì nguyên vẹn, xếp ngay ngắn lên kệ B1-K01-T01', 'Hoàn tất'),
('PNK_TP_20261006_001', 'LDPN_TP_DONE_02', 'B2-K02-T01', '2026-10-06 10:45:00', 'NV006', 1980.00, 20.00, 'Hao hụt 20 thanh do móp méo va đập trong khay xe nâng', 'Đã lập biên bản hao hụt khi nhập kho', 'Hoàn tất'),
('PNK_NL_20261001_001', 'LDPN_NL_DONE_01', 'A1-K01-T01', '2026-10-01 09:15:00', 'NV005', 1000.00, 0.00, NULL, 'Đủ 40 bao 25kg bột mì số 8, kê pallet gỗ sạch sẽ', 'Hoàn tất'),
('PNK_NL_20261005_001', 'LDPN_NL_DONE_02', 'L1-K01-T01', '2026-10-05 10:30:00', 'NV005', 800.00, 0.00, NULL, 'Bơ lạt Anchor đưa thẳng vào kho mát 4 độ C', 'Hoàn tất');


-- =====================================================================
-- PHẦN 6: YÊU CẦU XUẤT, ĐIỀU PHỐI FEFO, XUẤT KHO & XUẤT BỔ SUNG (7 BẢNG: 21 -> 27)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 21: phieuyeucauxuat (Trần Thanh Toàn - UC 2.4 & Trần Bích Thuận - UC 4.1)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS phieuyeucauxuat;
CREATE TABLE phieuyeucauxuat (
  maPhieuYeuCau VARCHAR(30) NOT NULL,
  loaiYeuCau VARCHAR(20) NOT NULL,
  maKeHoach VARCHAR(20) DEFAULT NULL,
  xuongYeuCau VARCHAR(150) NOT NULL,
  ngayYeuCau DATE NOT NULL,
  nguoiYeuCau VARCHAR(100) NOT NULL,
  lyDoXuat VARCHAR(255) DEFAULT NULL,
  tongSoLuong DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ điều phối',
  PRIMARY KEY (maPhieuYeuCau),
  KEY fk_pycx_khsx (maKeHoach),
  CONSTRAINT fk_pycx_khsx FOREIGN KEY (maKeHoach) REFERENCES kehoachsanxuat (maKeHoach)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phieuyeucauxuat (maPhieuYeuCau, loaiYeuCau, maKeHoach, xuongYeuCau, ngayYeuCau, nguoiYeuCau, lyDoXuat, tongSoLuong, trangThai) VALUES
-- 1. Yêu cầu xuất NGUYÊN LIỆU cho sản xuất (Trần Thanh Toàn - UC 2.4)
('PYCX_NL_20261027_001', 'NL', 'KHSX-2026-Q4-01', 'Phân xưởng nướng bánh số 1', '2026-10-27', 'Quản đốc Nguyễn Văn Hùng', 'Cấp bột mì số 8 và bơ lạt cho mẻ nướng Danisa ca sáng', 1500.00, 'Chờ điều phối'),
('PYCX_NL_20261027_002', 'NL', 'KHSX-2026-Q4-02', 'Phân xưởng đóng gói số 2', '2026-10-27', 'Tổ trưởng Trần Thị Mai', 'Xuất bột mì số 8 và đường tinh luyện cho bánh Cosy Marie', 2000.00, 'Chờ điều phối'),
('PYCX_NL_20261027_003', 'NL', 'KHSX-2026-Q4-03', 'Phân xưởng bánh kẹp kem số 3', '2026-10-27', 'Kỹ thuật viên Lê Quốc Bảo', 'Cấp bột mì, trứng gà tươi và socola chip làm bánh Oreo', 2500.00, 'Chờ điều phối'),
('PYCX_NL_20261027_004', 'NL', 'KHSX-2026-Q4-04', 'Phân xưởng nướng đặc sản 4', '2026-10-27', 'Trưởng ca Đặng Thanh Tâm', 'Cấp đường tinh luyện làm bánh quy dừa Bến Tre', 300.00, 'Chờ điều phối'),
('PYCX_NL_20261026_001', 'NL', 'KHSX-2026-Q4-01', 'Phân xưởng nướng bánh số 1', '2026-10-26', 'Quản đốc Nguyễn Văn Hùng', 'Cấp nguyên liệu định kỳ mẻ nướng đầu tuần', 1000.00, 'Hoàn tất'),
-- 2. Yêu cầu xuất THÀNH PHẨM cho đơn hàng đại lý/siêu thị (Trần Bích Thuận - UC 4.1)
('PYCX_TP_20261027_001', 'TP', NULL, 'Hệ thống Siêu thị Co.opmart Miền Nam', '2026-10-27', 'Trưởng phòng KD Phạm Thu Hà', 'Giao đơn hàng theo hợp đồng tuần 44 - Danisa & Cosy', 1300.00, 'Chờ điều phối'),
('PYCX_TP_20261027_002', 'TP', NULL, 'Chuỗi Cửa hàng Bách Hóa Xanh TP.HCM', '2026-10-27', 'Chuyên viên mua hàng Đỗ Quốc Nam', 'Xuất bánh quy Oreo kem vani và cracker AFC', 1800.00, 'Chờ điều phối'),
('PYCX_TP_20261027_003', 'TP', NULL, 'Hệ thống Siêu thị WinMart Toàn Quốc', '2026-10-27', 'Giám đốc thu mua Vũ Hồng Quân', 'Xuất đơn Danisa hộp thiếc 454g', 700.00, 'Chờ điều phối'),
('PYCX_TP_20261027_004', 'TP', NULL, 'Nhà phân phối Bánh kẹo Miền Đông (Đồng Nai)', '2026-10-27', 'Đại diện NPP Huỳnh Tấn Đạt', 'Xuất bán sỉ bánh quy Danisa 200g', 900.00, 'Chờ điều phối'),
('PYCX_TP_20261026_001', 'TP', NULL, 'Hệ thống Siêu thị Co.opmart TP.HCM', '2026-10-26', 'Trưởng phòng KD Phạm Thu Hà', 'Đơn giao gấp tuần 43', 600.00, 'Hoàn tất');

-- ---------------------------------------------------------------------
-- BẢNG 22: chitietyeucauxuat (Trần Thanh Toàn - UC 2.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietyeucauxuat;
CREATE TABLE chitietyeucauxuat (
  maChiTiet VARCHAR(30) NOT NULL,
  maPhieuYeuCau VARCHAR(30) NOT NULL,
  maMatHang VARCHAR(20) NOT NULL,
  tenMatHang VARCHAR(150) NOT NULL,
  soLuongYeuCau DECIMAL(12,2) NOT NULL,
  donViTinh VARCHAR(20) NOT NULL,
  PRIMARY KEY (maChiTiet),
  UNIQUE KEY uq_ctycx_phieu_mh (maPhieuYeuCau, maMatHang),
  CONSTRAINT fk_ctycx_phieu FOREIGN KEY (maPhieuYeuCau) REFERENCES phieuyeucauxuat (maPhieuYeuCau) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietyeucauxuat (maChiTiet, maPhieuYeuCau, maMatHang, tenMatHang, soLuongYeuCau, donViTinh) VALUES
('CTYCX_NL_001', 'PYCX_NL_20261027_001', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1000.00, 'Kg'),
('CTYCX_NL_002', 'PYCX_NL_20261027_001', 'NL003', 'Bơ lạt Anchor New Zealand (Thùng 25kg)', 500.00, 'Kg'),
('CTYCX_NL_003', 'PYCX_NL_20261027_002', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1200.00, 'Kg'),
('CTYCX_NL_004', 'PYCX_NL_20261027_002', 'NL004', 'Đường tinh luyện Biên Hòa Pure', 800.00, 'Kg'),
('CTYCX_NL_005', 'PYCX_NL_20261027_003', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1500.00, 'Kg'),
('CTYCX_NL_006', 'PYCX_NL_20261027_003', 'NL007', 'Socola hạt chip đen nguyên chất Hershey', 600.00, 'Kg'),
('CTYCX_NL_007', 'PYCX_NL_20261027_003', 'NL006', 'Trứng gà tươi công nghiệp Ba Huân', 400.00, 'Quả'),
('CTYCX_NL_008', 'PYCX_NL_20261027_004', 'NL004', 'Đường tinh luyện Biên Hòa Pure', 300.00, 'Kg'),
('CTYCX_NL_009', 'PYCX_NL_20261026_001', 'NL001', 'Bột mì đa dụng thượng hạng số 8', 1000.00, 'Kg'),
('CTYCX_TP_001', 'PYCX_TP_20261027_001', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 800.00, 'Hộp'),
('CTYCX_TP_002', 'PYCX_TP_20261027_001', 'TP004', 'Bánh quy Cosy Marie Kinh Đô 300g', 500.00, 'Gói'),
('CTYCX_TP_003', 'PYCX_TP_20261027_002', 'TP006', 'Bánh quy Socola Oreo Vani 133g', 1200.00, 'Thanh'),
('CTYCX_TP_004', 'PYCX_TP_20261027_002', 'TP008', 'Bánh cracker AFC Lúa mì dinh dưỡng 200g', 600.00, 'Hộp'),
('CTYCX_TP_005', 'PYCX_TP_20261027_003', 'TP002', 'Bánh quy bơ Danisa Hộp thiếc 454g', 700.00, 'Hộp'),
('CTYCX_TP_006', 'PYCX_TP_20261027_004', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 900.00, 'Hộp'),
('CTYCX_TP_007', 'PYCX_TP_20261026_001', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 600.00, 'Hộp');

-- ---------------------------------------------------------------------
-- BẢNG 23: lenhdieuphoixuat (Trần Thanh Toàn - UC 2.3, 2.4 & Trần Bích Thuận - UC 4.1, 4.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS lenhdieuphoixuat;
CREATE TABLE lenhdieuphoixuat (
  maLenh VARCHAR(30) NOT NULL,
  maPhieuYeuCau VARCHAR(30) NOT NULL,
  maKho VARCHAR(20) NOT NULL,
  ngayDieuPhoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiDieuPhoi VARCHAR(100) NOT NULL,
  ghiChu VARCHAR(255) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Chờ xuất kho',
  PRIMARY KEY (maLenh),
  KEY fk_lenhdieuphoixuat_phieu (maPhieuYeuCau),
  KEY fk_lenhdieuphoixuat_kho (maKho),
  CONSTRAINT fk_lenhdieuphoixuat_kho FOREIGN KEY (maKho) REFERENCES kho (maKho),
  CONSTRAINT fk_lenhdieuphoixuat_phieu FOREIGN KEY (maPhieuYeuCau) REFERENCES phieuyeucauxuat (maPhieuYeuCau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO lenhdieuphoixuat (maLenh, maPhieuYeuCau, maKho, ngayDieuPhoi, nguoiDieuPhoi, ghiChu, trangThai) VALUES
('LDPX_NL_DONE_01', 'PYCX_NL_20261026_001', 'KHO_NVL_01', '2026-10-26 10:00:00', 'NV004', 'Thuật toán FEFO: Lấy lô LONL_20261001_001 hạn sớm nhất', 'Hoàn tất xuất kho'),
('LDPX_TP_DONE_01', 'PYCX_TP_20261026_001', 'KHO_TP_01', '2026-10-26 14:30:00', 'NV003', 'FEFO: Lấy lô Danisa LOTP_20261002_001 xuất cho Coopmart', 'Hoàn tất xuất kho (Thiếu hàng)'),
-- Lệnh điều phối đang CHỜ XUẤT KHO (Để Toàn test UC 2.3 & Thuận test UC 4.2 Lập phiếu xuất)
('LDPX_NL_WAIT_01', 'PYCX_NL_20261027_004', 'KHO_NVL_01', '2026-10-27 08:30:00', 'NV004', 'FEFO xuất 300kg đường tinh luyện cho PX4', 'Chờ xuất kho'),
('LDPX_TP_WAIT_01', 'PYCX_TP_20261027_003', 'KHO_TP_01', '2026-10-27 09:15:00', 'NV003', 'FEFO xuất 700 hộp Danisa 454g cho WinMart', 'Chờ xuất kho');

-- ---------------------------------------------------------------------
-- BẢNG 24: chitietdieuphoixuat (Trần Thanh Toàn - UC 2.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietdieuphoixuat;
CREATE TABLE chitietdieuphoixuat (
  maChiTiet VARCHAR(30) NOT NULL,
  maLenh VARCHAR(30) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) NOT NULL,
  soLuongPhanBo DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (maChiTiet),
  UNIQUE KEY uq_ctdpx_lenh_lo_vt (maLenh, maLo, maViTri),
  KEY fk_chitietdpx_vitri (maViTri),
  CONSTRAINT fk_chitietdpx_lenh FOREIGN KEY (maLenh) REFERENCES lenhdieuphoixuat (maLenh) ON DELETE CASCADE,
  CONSTRAINT fk_chitietdpx_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietdieuphoixuat (maChiTiet, maLenh, maLo, maViTri, soLuongPhanBo) VALUES
('CTDPX_000001', 'LDPX_NL_DONE_01', 'LONL_20261001_001', 'A1-K01-T01', 500.00),
('CTDPX_000002', 'LDPX_NL_DONE_01', 'LONL_20261001_001', 'A1-K01-T02', 500.00),
('CTDPX_000003', 'LDPX_TP_DONE_01', 'LOTP_20261002_001', 'B1-K01-T01', 600.00),
('CTDPX_000004', 'LDPX_NL_WAIT_01', 'LONL_20261008_001', 'A2-K01-T01', 300.00),
('CTDPX_000005', 'LDPX_TP_WAIT_01', 'LOTP_20261003_001', 'B1-K03-T01', 500.00),
('CTDPX_000006', 'LDPX_TP_WAIT_01', 'LOTP_20261012_001', 'B1-K04-T01', 200.00);

-- ---------------------------------------------------------------------
-- BẢNG 25: phieuxuatkho (Trần Bích Thuận - UC 4.2 & Trần Thanh Toàn - UC 2.3)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS phieuxuatkho;
CREATE TABLE phieuxuatkho (
  maPXK VARCHAR(30) NOT NULL,
  maLenh VARCHAR(30) NOT NULL,
  maKho VARCHAR(20) NOT NULL,
  donViNhan VARCHAR(150) NOT NULL,
  ngayXuat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiLap VARCHAR(20) NOT NULL,
  tongSoLuong DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  lyDoChenhLech TEXT DEFAULT NULL,
  ghiChu VARCHAR(255) DEFAULT NULL,
  trangThai VARCHAR(30) NOT NULL DEFAULT 'Hoàn tất',
  PRIMARY KEY (maPXK),
  KEY fk_phieuxuatkho_lenh (maLenh),
  KEY fk_phieuxuatkho_kho (maKho),
  KEY fk_phieuxuatkho_nv (nguoiLap),
  CONSTRAINT fk_phieuxuatkho_lenh FOREIGN KEY (maLenh) REFERENCES lenhdieuphoixuat (maLenh),
  CONSTRAINT fk_phieuxuatkho_kho FOREIGN KEY (maKho) REFERENCES kho (maKho),
  CONSTRAINT fk_phieuxuatkho_nv FOREIGN KEY (nguoiLap) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phieuxuatkho (maPXK, maLenh, maKho, donViNhan, ngayXuat, nguoiLap, tongSoLuong, lyDoChenhLech, ghiChu, trangThai) VALUES
('PXK_20261026_001', 'LDPX_NL_DONE_01', 'KHO_NVL_01', 'Phân xưởng nướng bánh số 1', '2026-10-26 11:00:00', 'NV005', 1000.00, NULL, 'Xuất đủ 1 tấn bột mì số 8 cho PX1 nướng bánh Danisa', 'Hoàn tất'),
('PXK_20261026_002', 'LDPX_TP_DONE_01', 'KHO_TP_01', 'Hệ thống Siêu thị Co.opmart TP.HCM', '2026-10-26 16:00:00', 'NV006', 595.00, 'Thiếu 5 hộp do móp méo khi bốc xếp lên xe tải', 'Đã lập biên bản thiếu hàng BBTH_20261026_001 và yêu cầu xuất bổ sung', 'Hoàn tất');

-- ---------------------------------------------------------------------
-- BẢNG 26: chitietphieuxuat (Trần Bích Thuận - UC 4.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietphieuxuat;
CREATE TABLE chitietphieuxuat (
  maChiTiet VARCHAR(20) NOT NULL,
  maPXK VARCHAR(30) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) DEFAULT NULL,
  soLuongDuKien DECIMAL(12,2) NOT NULL,
  soLuongThucXuat DECIMAL(12,2) NOT NULL,
  soLuongPhanBo DECIMAL(12,2) NOT NULL,
  lyDoChenhLech TEXT DEFAULT NULL,
  PRIMARY KEY (maChiTiet),
  KEY fk_ctpx_pxk (maPXK),
  KEY fk_ctpx_vitri (maViTri),
  CONSTRAINT fk_ctpx_pxk FOREIGN KEY (maPXK) REFERENCES phieuxuatkho (maPXK) ON DELETE CASCADE,
  CONSTRAINT fk_ctpx_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietphieuxuat (maChiTiet, maPXK, maLo, maViTri, soLuongDuKien, soLuongThucXuat, soLuongPhanBo, lyDoChenhLech) VALUES
('CTPX_00001', 'PXK_20261026_001', 'LONL_20261001_001', 'A1-K01-T01', 500.00, 500.00, 500.00, NULL),
('CTPX_00002', 'PXK_20261026_001', 'LONL_20261001_001', 'A1-K01-T02', 500.00, 500.00, 500.00, NULL),
('CTPX_00003', 'PXK_20261026_002', 'LOTP_20261002_001', 'B1-K01-T01', 600.00, 595.00, 600.00, 'Phát hiện 5 hộp thiếc móp nắp tại cuối kiện hàng');

-- ---------------------------------------------------------------------
-- BẢNG 27: yeucauxuatbosung (Trần Bích Thuận - UC 4.3 Phê duyệt yêu cầu xuất bổ sung)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS yeucauxuatbosung;
CREATE TABLE yeucauxuatbosung (
  maYeuCau VARCHAR(20) NOT NULL,
  maPXKGoc VARCHAR(30) NOT NULL,
  ngayYeuCau DATE NOT NULL,
  nguoiYeuCau VARCHAR(20) NOT NULL,
  soLuongXuatBoSung DECIMAL(12,2) NOT NULL,
  lyDoYeuCau TEXT NOT NULL,
  minhChung TEXT DEFAULT NULL,
  trangThai VARCHAR(30) NOT NULL DEFAULT 'Chờ phê duyệt',
  PRIMARY KEY (maYeuCau),
  KEY fk_ycxbs_pxk (maPXKGoc),
  KEY fk_ycxbs_nv (nguoiYeuCau),
  CONSTRAINT fk_ycxbs_pxk FOREIGN KEY (maPXKGoc) REFERENCES phieuxuatkho (maPXK),
  CONSTRAINT fk_ycxbs_nv FOREIGN KEY (nguoiYeuCau) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO yeucauxuatbosung (maYeuCau, maPXKGoc, ngayYeuCau, nguoiYeuCau, soLuongXuatBoSung, lyDoYeuCau, minhChung, trangThai) VALUES
('YCBS_20261026_001', 'PXK_20261026_002', '2026-10-26', 'NV006', 5.00, 'Cấp bù 5 hộp bánh quy bơ Danisa 200g bị thiếu do móp méo ở chuyến giao Coopmart', 'bb_thieu_hang_20261026.pdf', 'Chờ phê duyệt'),
('YCBS_20261027_001', 'PXK_20261026_002', '2026-10-27', 'NV006', 10.00, 'Đề nghị xuất bổ sung thêm 10 hộp mẫu trưng bày theo phụ lục hợp đồng Coopmart', 'phuluc_hd_coopmart.pdf', 'Chờ phê duyệt');


-- =====================================================================
-- PHẦN 7: KIỂM KÊ KHO, XỬ LÝ CHÊNH LỆCH, HÀNG HỎNG & PHÊ DUYỆT (10 BẢNG: 28 -> 37)
-- =====================================================================

-- ---------------------------------------------------------------------
-- BẢNG 28: dotkiemke (Võ Thị Tuyết Ngân - UC 5.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS dotkiemke;
CREATE TABLE dotkiemke (
  maDotKiemKe VARCHAR(30) NOT NULL,
  tenDotKiemKe VARCHAR(150) NOT NULL,
  ngayBatDau DATE NOT NULL,
  ngayKetThuc DATE DEFAULT NULL,
  maKho VARCHAR(20) DEFAULT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Đang diễn ra',
  PRIMARY KEY (maDotKiemKe),
  KEY fk_dotkiemke_kho (maKho),
  CONSTRAINT fk_dotkiemke_kho FOREIGN KEY (maKho) REFERENCES kho (maKho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO dotkiemke (maDotKiemKe, tenDotKiemKe, ngayBatDau, ngayKetThuc, maKho, trangThai) VALUES
('KK_2026_T10_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Khô 1 (Tháng 10/2026)', '2026-10-25', '2026-10-28', 'KHO_NVL_01', 'Đang diễn ra'),
('KK_2026_T10_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Bánh Quy 1 (Tháng 10/2026)', '2026-10-26', '2026-10-29', 'KHO_TP_01', 'Đang diễn ra'),
('KK_2026_T10_LANH', 'Kiểm kê đột xuất Kho Nguyên Liệu Lạnh & Mát', '2026-10-27', '2026-10-28', 'KHO_NVL_02', 'Đang diễn ra'),
('KK_2026_T09_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 09/2026', '2026-09-28', '2026-09-30', 'KHO_TP_01', 'Hoàn tất'),
('KK_2026_T09_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Tháng 09/2026', '2026-09-25', '2026-09-27', 'KHO_NVL_01', 'Hoàn tất');

-- ---------------------------------------------------------------------
-- BẢNG 29: bienbankiemke (Võ Thị Tuyết Ngân - UC 5.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bienbankiemke;
CREATE TABLE bienbankiemke (
  maBienBan VARCHAR(20) NOT NULL,
  maDotKiemKe VARCHAR(30) NOT NULL,
  tenDotKiemKe VARCHAR(150) NOT NULL,
  loaiKiemKe VARCHAR(50) NOT NULL,
  maKho VARCHAR(20) NOT NULL,
  ngayKiemKe DATE NOT NULL,
  nguoiKiemKe VARCHAR(20) NOT NULL,
  trangThai VARCHAR(50) NOT NULL DEFAULT 'Đang đếm',
  PRIMARY KEY (maBienBan),
  KEY fk_bbkk_dot (maDotKiemKe),
  KEY fk_bbkk_kho (maKho),
  KEY fk_bbkk_nv (nguoiKiemKe),
  CONSTRAINT fk_bbkk_dot FOREIGN KEY (maDotKiemKe) REFERENCES dotkiemke (maDotKiemKe),
  CONSTRAINT fk_bbkk_kho FOREIGN KEY (maKho) REFERENCES kho (maKho),
  CONSTRAINT fk_bbkk_nv FOREIGN KEY (nguoiKiemKe) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bienbankiemke (maBienBan, maDotKiemKe, tenDotKiemKe, loaiKiemKe, maKho, ngayKiemKe, nguoiKiemKe, trangThai) VALUES
('BBKK_20261025_01', 'KK_2026_T10_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Bánh Quy 1 (Tháng 10/2026)', 'Kiểm kê thành phẩm', 'KHO_TP_01', '2026-10-26', 'NV002', 'Đang đếm'),
('BBKK_20261026_01', 'KK_2026_T10_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Khô 1 (Tháng 10/2026)', 'Kiểm kê nguyên liệu', 'KHO_NVL_01', '2026-10-26', 'NV002', 'Chờ duyệt'),
('BBKK_20261027_01', 'KK_2026_T10_LANH', 'Kiểm kê đột xuất Kho Nguyên Liệu Lạnh & Mát', 'Kiểm kê nguyên liệu', 'KHO_NVL_02', '2026-10-27', 'NV014', 'Đang đếm'),
('BBKK_20260928_01', 'KK_2026_T09_TP01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 09/2026', 'Kiểm kê thành phẩm', 'KHO_TP_01', '2026-09-30', 'NV002', 'Đã duyệt'),
('BBKK_20260925_01', 'KK_2026_T09_NVL01', 'Kiểm kê định kỳ Kho Nguyên Liệu Tháng 09/2026', 'Kiểm kê nguyên liệu', 'KHO_NVL_01', '2026-09-27', 'NV002', 'Đã duyệt');

-- ---------------------------------------------------------------------
-- BẢNG 30: chitietkiemke (Võ Thị Tuyết Ngân - UC 5.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietkiemke;
CREATE TABLE chitietkiemke (
  maChiTiet VARCHAR(20) NOT NULL,
  maBienBan VARCHAR(20) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) NOT NULL,
  soLuongTheoSoSach DECIMAL(18,2) NOT NULL,
  soLuongTotThucTe DECIMAL(18,2) NOT NULL,
  soLuongHongThucTe DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  soLuongThucTe DECIMAL(18,2) NOT NULL,
  soLuongChenhLech DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  tinhTrang VARCHAR(255) DEFAULT 'Bình thường',
  PRIMARY KEY (maChiTiet),
  KEY fk_chitietkiemke_bb (maBienBan),
  KEY fk_chitietkiemke_vitri (maViTri),
  CONSTRAINT fk_chitietkiemke_bb FOREIGN KEY (maBienBan) REFERENCES bienbankiemke (maBienBan) ON DELETE CASCADE,
  CONSTRAINT fk_chitietkiemke_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietkiemke (maChiTiet, maBienBan, maLo, maViTri, soLuongTheoSoSach, soLuongTotThucTe, soLuongHongThucTe, soLuongThucTe, soLuongChenhLech, tinhTrang) VALUES
-- Chi tiết kiểm kê Thành phẩm Kho TP 01 (BBKK_20261025_01)
('CTKK_20261026_001', 'BBKK_20261025_01', 'LOTP_20261002_001', 'B1-K01-T01', 600.00, 600.00, 0.00, 600.00, 0.00, 'Khớp 100%, hộp thiếc nguyên vẹn'),
('CTKK_20261026_002', 'BBKK_20261025_01', 'LOTP_20261005_001', 'B1-K01-T02', 500.00, 485.00, 15.00, 500.00, 0.00, 'Tổng 500 hộp, phát hiện 15 hộp móp nắp do va chạm xe nâng'),
('CTKK_20261026_003', 'BBKK_20261025_01', 'LOTP_20261006_001', 'B2-K02-T01', 800.00, 800.00, 0.00, 800.00, 0.00, 'Khớp 100%'),
-- Chi tiết kiểm kê Nguyên liệu Kho NVL 01 (BBKK_20261026_01)
('CTKK_20261026_004', 'BBKK_20261026_01', 'LONL_20261001_001', 'A1-K01-T01', 500.00, 500.00, 0.00, 500.00, 0.00, 'Khớp 100%, bột mì khô ráo'),
('CTKK_20261026_005', 'BBKK_20261026_01', 'LONL_20261001_002', 'A1-K02-T01', 600.00, 575.00, 20.00, 595.00, -5.00, 'Thiếu 5kg rơi vãi + 20kg ẩm ướt góc bao'),
('CTKK_20261026_006', 'BBKK_20261026_01', 'LONL_20261008_001', 'A2-K01-T01', 600.00, 600.00, 0.00, 600.00, 0.00, 'Khớp 100%, đường tinh luyện nguyên vẹn'),
-- Chi tiết kiểm kê Kho lạnh NVL 02 (BBKK_20261027_01)
('CTKK_20261027_001', 'BBKK_20261027_01', 'LONL_20261005_001', 'L1-K01-T01', 400.00, 400.00, 0.00, 400.00, 0.00, 'Khớp 100%, nhiệt độ kho lạnh đạt 4°C'),
('CTKK_20261027_002', 'BBKK_20261027_01', 'LONL_20261010_001', 'L1-K03-T01', 2500.00, 2460.00, 40.00, 2500.00, 0.00, 'Bể 40 quả trứng trong quá trình di chuyển khay');

-- ---------------------------------------------------------------------
-- BẢNG 31: bienbanxulychenhlech (Lê Minh Thiện Phúc - Tuần 9)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bienbanxulychenhlech;
CREATE TABLE bienbanxulychenhlech (
  maBienBanXuLy VARCHAR(20) NOT NULL,
  maBienBan VARCHAR(20) NOT NULL,
  ngayXuLy DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiLap VARCHAR(20) NOT NULL,
  nguyenNhan VARCHAR(500) NOT NULL,
  phuongAnXuLy VARCHAR(500) NOT NULL,
  trangThai TINYINT NOT NULL DEFAULT 0,
  lanTrinh INT NOT NULL DEFAULT 1,
  PRIMARY KEY (maBienBanXuLy),
  UNIQUE KEY uq_bbxlcl_bbkk (maBienBan),
  KEY fk_bbxlcl_nv (nguoiLap),
  CONSTRAINT fk_bbxlcl_bbkk FOREIGN KEY (maBienBan) REFERENCES bienbankiemke (maBienBan),
  CONSTRAINT fk_bbxlcl_nv FOREIGN KEY (nguoiLap) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bienbanxulychenhlech (maBienBanXuLy, maBienBan, ngayXuLy, nguoiLap, nguyenNhan, phuongAnXuLy, trangThai, lanTrinh) VALUES
('XL000001', 'BBKK_20261026_01', '2026-10-26 17:30:00', 'NV004', 'Rách bao PP 25kg bột mì số 8 ở kệ A1-K02-T01 gây rơi vãi hao hụt 5kg và ẩm 20kg', 'Điều chỉnh giảm tồn sổ sách 5kg hao hụt tự nhiên và lập đề nghị tiêu hủy 20kg bột ẩm', 1, 1),
('XL000002', 'BBKK_20260928_01', '2026-09-30 16:00:00', 'NV003', 'Nhầm lẫn mã lô khi xuất giao ca cuối tháng 09 giữa 2 kệ B1-K01-T01 và B1-K01-T02', 'Cân bằng lại số liệu tồn kho giữa 2 vị trí kệ theo biên bản kiểm kê đã duyệt', 2, 1),
('XL000003', 'BBKK_20260925_01', '2026-09-27 15:00:00', 'NV004', 'Ghi nhận sai đơn vị quy đổi bao 25kg sang Kg trong kỳ kiểm kê tháng 09', 'Bổ sung chứng từ đối chiếu phiếu nhập kho PNK và trình Ban giám đốc phê duyệt lại', 3, 1);

-- ---------------------------------------------------------------------
-- BẢNG 32: chitietxulychenhlech (Lê Minh Thiện Phúc - Tuần 9)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietxulychenhlech;
CREATE TABLE chitietxulychenhlech (
  maChiTiet VARCHAR(20) NOT NULL,
  maBienBanXuLy VARCHAR(20) NOT NULL,
  maTonKho VARCHAR(20) NOT NULL,
  soLuongTheoHeThong DECIMAL(18,3) NOT NULL,
  soLuongThucTe DECIMAL(18,3) NOT NULL,
  soLuongChenhLech DECIMAL(19,3) GENERATED ALWAYS AS (soLuongThucTe - soLuongTheoHeThong) STORED,
  phienBanTonChot VARCHAR(32) NOT NULL DEFAULT '0x0000000000000001',
  ghiChu VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (maChiTiet),
  KEY fk_ctxlcl_bb (maBienBanXuLy),
  KEY fk_ctxlcl_tk (maTonKho),
  CONSTRAINT fk_ctxlcl_bb FOREIGN KEY (maBienBanXuLy) REFERENCES bienbanxulychenhlech (maBienBanXuLy) ON DELETE CASCADE,
  CONSTRAINT fk_ctxlcl_tk FOREIGN KEY (maTonKho) REFERENCES tonkho (maTonKho)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietxulychenhlech (maChiTiet, maBienBanXuLy, maTonKho, soLuongTheoHeThong, soLuongThucTe, phienBanTonChot, ghiChu) VALUES
('CTXL000001', 'XL000001', 'TK_NL_003', 600.000, 595.000, '0x0000000000000103', 'Gồm 575kg bột tốt + 20kg bột ẩm chờ tiêu hủy, hao hụt 5kg'),
('CTXL000002', 'XL000002', 'TK_TP_001', 600.000, 600.000, '0x0000000000000201', 'Đã đối soát khớp số lượng sau điều chuyển kệ'),
('CTXL000003', 'XL000003', 'TK_NL_001', 525.000, 500.000, '0x0000000000000101', 'Hồ sơ bị từ chối lần 1 do thiếu bản sao phiếu cân kho');

-- ---------------------------------------------------------------------
-- BẢNG 33: bienbanthieuhang (Trần Bích Thuận - UC 4.2)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bienbanthieuhang;
CREATE TABLE bienbanthieuhang (
  maBienBan VARCHAR(30) NOT NULL,
  maPXK VARCHAR(30) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  ngayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiLap VARCHAR(20) NOT NULL,
  noiDung TEXT NOT NULL,
  soLuongThieu DECIMAL(12,2) NOT NULL,
  lyDoThieu TEXT NOT NULL,
  PRIMARY KEY (maBienBan),
  KEY fk_bbth_pxk (maPXK),
  KEY fk_bbth_nv (nguoiLap),
  CONSTRAINT fk_bbth_pxk FOREIGN KEY (maPXK) REFERENCES phieuxuatkho (maPXK),
  CONSTRAINT fk_bbth_nv FOREIGN KEY (nguoiLap) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bienbanthieuhang (maBienBan, maPXK, maLo, ngayLap, nguoiLap, noiDung, soLuongThieu, lyDoThieu) VALUES
('BBTH_20261026_001', 'PXK_20261026_002', 'LOTP_20261002_001', '2026-10-26 16:30:00', 'NV006', 'Đối chiếu thực xuất tại cửa kho TP01 cho xe giao hàng Coopmart thiếu 5 hộp Danisa 200g', 5.00, 'Tài xế và thủ kho phát hiện 5 hộp bị móp nắp ở đáy kiện cuối nên giữ lại không xuất giao');

-- ---------------------------------------------------------------------
-- BẢNG 34: bienbanxulyhanghong (Trần Bích Thuận - UC 4.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS bienbanxulyhanghong;
CREATE TABLE bienbanxulyhanghong (
  maBienBan VARCHAR(30) NOT NULL,
  maPXK VARCHAR(30) DEFAULT NULL,
  ngayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiLap VARCHAR(20) NOT NULL,
  soLuongXuLy DECIMAL(12,2) NOT NULL,
  lyDo TEXT NOT NULL,
  phuongAnXuLy TEXT NOT NULL,
  minhChung TEXT DEFAULT NULL,
  trangThai VARCHAR(30) NOT NULL DEFAULT 'Chờ xử lý',
  PRIMARY KEY (maBienBan),
  KEY fk_bbhh_pxk (maPXK),
  KEY fk_bbhh_nv (nguoiLap),
  CONSTRAINT fk_bbhh_pxk FOREIGN KEY (maPXK) REFERENCES phieuxuatkho (maPXK),
  CONSTRAINT fk_bbhh_nv FOREIGN KEY (nguoiLap) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO bienbanxulyhanghong (maBienBan, maPXK, ngayLap, nguoiLap, soLuongXuLy, lyDo, phuongAnXuLy, minhChung, trangThai) VALUES
('BBHH_20261018_001', NULL, '2026-10-18 10:00:00', 'NV004', 20.00, 'Bao bột mì số 8 bị rách góc và thấm ẩm trong quá trình lưu kho', 'Khóa tồn kho và lập đề nghị tiêu hủy rác thải công nghiệp', 'hinh_bot_mi_am_20261018.jpg', 'Đã lập đề nghị tiêu hủy'),
('BBHH_20261026_001', 'PXK_20261026_002', '2026-10-26 15:30:00', 'NV003', 15.00, 'Va chạm càng xe nâng làm móp vỡ 15 hộp thiếc bánh quy Danisa 200g', 'Điều chuyển cách ly sang Kho Hàng Lỗi X1-K01-T01 và trình Ban giám đốc tiêu hủy', 'anh_hop_danisa_mop_20261026.jpg', 'Đã lập đề nghị tiêu hủy'),
('BBHH_20261027_001', NULL, '2026-10-27 08:45:00', 'NV003', 40.00, 'Vỡ nứt 40 quả trứng gà tươi trong khay bảo quản kho lạnh L1-K03-T01', 'Khóa 40 quả trứng hỏng tại vị trí L1-K03-T01, chuyển tiêu hủy sinh học trong ngày', 'bienban_trung_vo_20261027.jpg', 'Đã khóa hàng hỏng');

-- ---------------------------------------------------------------------
-- BẢNG 35: chitietxulyhanghong (Trần Bích Thuận - UC 4.4)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS chitietxulyhanghong;
CREATE TABLE chitietxulyhanghong (
  maChiTiet VARCHAR(20) NOT NULL,
  maBienBan VARCHAR(30) NOT NULL,
  maViTri VARCHAR(20) NOT NULL,
  maLo VARCHAR(30) NOT NULL,
  loaiHang VARCHAR(20) NOT NULL DEFAULT 'TP',
  soLuongHong DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (maChiTiet),
  KEY fk_cthh_bb (maBienBan),
  KEY fk_cthh_vitri (maViTri),
  CONSTRAINT fk_cthh_bb FOREIGN KEY (maBienBan) REFERENCES bienbanxulyhanghong (maBienBan) ON DELETE CASCADE,
  CONSTRAINT fk_cthh_vitri FOREIGN KEY (maViTri) REFERENCES vitrikho (maViTri)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chitietxulyhanghong (maChiTiet, maBienBan, maViTri, maLo, loaiHang, soLuongHong) VALUES
('CTHH_00001', 'BBHH_20261018_001', 'A1-K02-T01', 'LONL_20261001_002', 'NL', 20.00),
('CTHH_00002', 'BBHH_20261026_001', 'X1-K01-T01', 'LOTP_20261005_001', 'TP', 15.00),
('CTHH_00003', 'BBHH_20261027_001', 'L1-K03-T01', 'LONL_20261010_001', 'NL', 40.00);

-- ---------------------------------------------------------------------
-- BẢNG 36: denghitieuhuy (Lê Minh Thiện Phúc - Tuần 9)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS denghitieuhuy;
CREATE TABLE denghitieuhuy (
  maDeNghi VARCHAR(20) NOT NULL,
  ngayDeNghi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiDeNghi VARCHAR(20) NOT NULL,
  maTonKho VARCHAR(20) NOT NULL,
  soLuong DECIMAL(18,3) NOT NULL,
  lyDo VARCHAR(500) NOT NULL,
  maBienBanXuLy VARCHAR(20) DEFAULT NULL,
  minhChung VARCHAR(500) DEFAULT NULL,
  trangThai TINYINT NOT NULL DEFAULT 0,
  lanTrinh INT NOT NULL DEFAULT 1,
  maPXK VARCHAR(30) DEFAULT NULL,
  PRIMARY KEY (maDeNghi),
  KEY fk_dnth_nv (nguoiDeNghi),
  KEY fk_dnth_tk (maTonKho),
  KEY fk_dnth_xlcl (maBienBanXuLy),
  KEY fk_dnth_pxk (maPXK),
  CONSTRAINT fk_dnth_nv FOREIGN KEY (nguoiDeNghi) REFERENCES nhanvien (maNV),
  CONSTRAINT fk_dnth_tk FOREIGN KEY (maTonKho) REFERENCES tonkho (maTonKho),
  CONSTRAINT fk_dnth_xlcl FOREIGN KEY (maBienBanXuLy) REFERENCES bienbanxulychenhlech (maBienBanXuLy),
  CONSTRAINT fk_dnth_pxk FOREIGN KEY (maPXK) REFERENCES phieuxuatkho (maPXK)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO denghitieuhuy (maDeNghi, ngayDeNghi, nguoiDeNghi, maTonKho, soLuong, lyDo, maBienBanXuLy, minhChung, trangThai, lanTrinh, maPXK) VALUES
('TH000001', '2026-10-18 11:00:00', 'NV004', 'TK_NL_003', 20.000, 'Bột mì số 8 rách bao bị ẩm mốc cục bộ, không đạt chỉ tiêu vi sinh an toàn thực phẩm', 'XL000001', 'minhchung_botmi_am_TH01.pdf', 2, 1, NULL),
('TH000002', '2026-10-26 16:15:00', 'NV003', 'TK_TP_017', 15.000, 'Bánh quy Danisa 200g móp vỡ hộp thiếc nặng trong kho cách ly X1-K01-T01, không thể kinh doanh', NULL, 'minhchung_danisa_mop_TH02.pdf', 1, 1, NULL),
('TH000003', '2026-10-27 09:30:00', 'NV003', 'TK_NL_015', 40.000, 'Trứng gà tươi bị vỡ nứt trong quá trình bảo quản kho lạnh L1-K03-T01', NULL, 'minhchung_trung_vo_TH03.pdf', 1, 1, NULL),
('TH000004', '2026-10-27 10:00:00', 'NV004', 'TK_TP_002', 5.000, 'Bản nháp đề nghị tiêu hủy 5 hộp Danisa móp nắp phát hiện khi xuất giao', NULL, NULL, 0, 1, NULL);

-- ---------------------------------------------------------------------
-- BẢNG 37: phieupheduyet (Lê Minh Thiện Phúc - Tuần 9)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS phieupheduyet;
CREATE TABLE phieupheduyet (
  maPheDuyet VARCHAR(20) NOT NULL,
  loaiPheDuyet TINYINT NOT NULL,
  maBienBanXuLy VARCHAR(20) DEFAULT NULL,
  maDeNghi VARCHAR(20) DEFAULT NULL,
  lanTrinh INT NOT NULL DEFAULT 1,
  ngayPheDuyet DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoiPheDuyet VARCHAR(20) NOT NULL,
  ketQua TINYINT(1) NOT NULL,
  lyDoTuChoi VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (maPheDuyet),
  KEY fk_ppd_xlcl (maBienBanXuLy),
  KEY fk_ppd_dnth (maDeNghi),
  KEY fk_ppd_nv (nguoiPheDuyet),
  CONSTRAINT fk_ppd_xlcl FOREIGN KEY (maBienBanXuLy) REFERENCES bienbanxulychenhlech (maBienBanXuLy),
  CONSTRAINT fk_ppd_dnth FOREIGN KEY (maDeNghi) REFERENCES denghitieuhuy (maDeNghi),
  CONSTRAINT fk_ppd_nv FOREIGN KEY (nguoiPheDuyet) REFERENCES nhanvien (maNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phieupheduyet (maPheDuyet, loaiPheDuyet, maBienBanXuLy, maDeNghi, lanTrinh, ngayPheDuyet, nguoiPheDuyet, ketQua, lyDoTuChoi) VALUES
('PD000001', 1, 'XL000002', NULL, 1, '2026-10-01 09:00:00', 'NV015', 1, NULL),
('PD000002', 1, 'XL000003', NULL, 1, '2026-09-28 14:20:00', 'NV015', 0, 'Yêu cầu đính kèm phiếu cân điện tử thực tế và chữ ký xác nhận của Tổ trưởng kiểm kê trước khi duyệt'),
('PD000003', 2, NULL, 'TH000001', 1, '2026-10-19 08:30:00', 'NV015', 1, NULL);


-- =====================================================================
-- PHẦN MỞ RỘNG: CÁC VIEW HỖ TRỢ TRUY VẤN & BÁO CÁO CHUẨN TUẦN 9
-- =====================================================================

-- 1. View cảnh báo nguyên liệu cận hạn sử dụng (FEFO)
CREATE OR REPLACE VIEW v_NguyenLieuTonKhoCanHan AS
SELECT
  lo.maLoNL,
  nl.maNL,
  nl.tenNL,
  k.tenKho,
  tk.maViTri,
  tk.slTon,
  lo.ngaySanXuat,
  lo.hanSuDung,
  DATEDIFF(lo.hanSuDung, CURDATE()) AS soNgayConLai,
  CASE
    WHEN DATEDIFF(lo.hanSuDung, CURDATE()) < 15 THEN 'Khẩn cấp'
    WHEN DATEDIFF(lo.hanSuDung, CURDATE()) <= 30 THEN 'Cận hạn'
    ELSE 'An toàn'
  END AS mucDoCanhBao
FROM tonkho tk
JOIN longuyenlieu lo ON tk.maLo = lo.maLoNL
JOIN nguyenlieu nl ON lo.maNL = nl.maNL
JOIN vitrikho vt ON tk.maViTri = vt.maViTri
JOIN kho k ON vt.maKho = k.maKho
WHERE tk.loaiHang = 'NL' AND tk.slTon > 0;

-- 2. View cảnh báo thành phẩm cận hạn sử dụng (FEFO)
CREATE OR REPLACE VIEW v_ThanhPhamTonKhoCanHan AS
SELECT
  lo.maLoTP,
  tp.maTP,
  tp.tenTP,
  k.tenKho,
  tk.maViTri,
  tk.slTon,
  lo.ngaySanXuat,
  lo.hanSuDung,
  DATEDIFF(lo.hanSuDung, CURDATE()) AS soNgayConLai,
  CASE
    WHEN DATEDIFF(lo.hanSuDung, CURDATE()) < 15 THEN 'Khẩn cấp'
    WHEN DATEDIFF(lo.hanSuDung, CURDATE()) <= 30 THEN 'Cận hạn'
    ELSE 'An toàn'
  END AS mucDoCanhBao
FROM tonkho tk
JOIN lothanhpham lo ON tk.maLo = lo.maLoTP
JOIN thanhpham tp ON lo.maTP = tp.maTP
JOIN vitrikho vt ON tk.maViTri = vt.maViTri
JOIN kho k ON vt.maKho = k.maKho
WHERE tk.loaiHang = 'TP' AND tk.slTon > 0;

-- 3. View tổng hợp hồ sơ chờ Ban Giám Đốc phê duyệt (Chênh lệch & Tiêu hủy)
CREATE OR REPLACE VIEW vw_HoSoChoPheDuyet AS
SELECT
  1 AS loaiHoSo,
  xl.maBienBanXuLy AS maHoSo,
  xl.ngayXuLy AS ngayLap,
  xl.nguoiLap AS maNguoiLap,
  nv.hoTen AS hoTenNguoiLap,
  xl.lanTrinh,
  xl.nguyenNhan AS noiDung,
  NULL AS soLuongDeNghi
FROM bienbanxulychenhlech xl
JOIN nhanvien nv ON xl.nguoiLap = nv.maNV
WHERE xl.trangThai = 1
UNION ALL
SELECT
  2 AS loaiHoSo,
  th.maDeNghi AS maHoSo,
  th.ngayDeNghi AS ngayLap,
  th.nguoiDeNghi AS maNguoiLap,
  nv.hoTen AS hoTenNguoiLap,
  th.lanTrinh,
  th.lyDo AS noiDung,
  th.soLuong AS soLuongDeNghi
FROM denghitieuhuy th
JOIN nhanvien nv ON th.nguoiDeNghi = nv.maNV
WHERE th.trangThai = 1;

SET FOREIGN_KEY_CHECKS = 1;
-- =====================================================================
-- HOÀN TẤT KHỞI TẠO CSDL quanlykho_vuotqua (37 BẢNG + 3 VIEWS CHUẨN)
-- =====================================================================