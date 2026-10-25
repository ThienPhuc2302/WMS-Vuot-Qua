-- =========================================================================
-- DATABASE: HỆ THỐNG QUẢN LÝ KHO - NHÓM VƯỢT QUA (17 BẢNG CỐT LÕI)
-- Tương thích: MySQL / MariaDB (phpMyAdmin - XAMPP / Laragon)
-- =========================================================================

CREATE DATABASE IF NOT EXISTS `quanlykho_vuotqua` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quanlykho_vuotqua`;

SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================================
-- NHÓM 1: DANH MỤC CƠ BẢN & NGƯỜI DÙNG (5 BẢNG)
-- =========================================================================

-- 1. Bảng NhanVien (Tích hợp thông tin đăng nhập & phân quyền cho 6 thành viên)
DROP TABLE IF EXISTS `NhanVien`;
CREATE TABLE `NhanVien` (
  `maNV` varchar(20) NOT NULL,
  `hoTen` varchar(100) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL UNIQUE,
  `matKhau` varchar(255) NOT NULL,
  `vaiTro` varchar(50) NOT NULL COMMENT 'QuanLyKho, NhanVienKho, QC, KiemKe, BanGiamDoc, Admin',
  `soDienThoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Kho
DROP TABLE IF EXISTS `Kho`;
CREATE TABLE `Kho` (
  `maKho` varchar(20) NOT NULL,
  `tenKho` varchar(100) NOT NULL,
  `loaiKho` varchar(50) NOT NULL COMMENT 'Kho NVL, Kho Thành phẩm, Kho Hàng lỗi',
  `dieuKienBaoQuan` varchar(50) DEFAULT 'Kho khô' COMMENT 'Kho khô, Kho mát, Kho lạnh',
  `diaChi` varchar(200) DEFAULT NULL,
  `sucChuaToiDa` int(11) DEFAULT 1000,
  `sucChuaHienTai` int(11) DEFAULT 0,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maKho`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng ViTriKho (Ô/Kệ lưu trữ 3 chiều: Dãy - Kệ - Tầng)
DROP TABLE IF EXISTS `ViTriKho`;
CREATE TABLE `ViTriKho` (
  `maViTri` varchar(20) NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `day` varchar(10) NOT NULL,
  `ke` varchar(10) NOT NULL,
  `tang` varchar(10) NOT NULL,
  `sucChuaKhaDung` int(11) DEFAULT 500,
  `trangThaiSuDung` varchar(50) DEFAULT 'Trống' COMMENT 'Trống, Đang chứa, Đầy',
  `trangThaiChuaHang` varchar(50) DEFAULT 'Khả dụng' COMMENT 'Khả dụng, Khóa, Bảo trì',
  PRIMARY KEY (`maViTri`),
  KEY `fk_vitri_kho` (`maKho`),
  CONSTRAINT `fk_vitri_kho` FOREIGN KEY (`maKho`) REFERENCES `Kho` (`maKho`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng NguyenLieu
DROP TABLE IF EXISTS `NguyenLieu`;
CREATE TABLE `NguyenLieu` (
  `maNL` varchar(20) NOT NULL,
  `tenNL` varchar(150) NOT NULL,
  `donViTinh` varchar(20) NOT NULL,
  `loaiKhoBaoQuan` varchar(50) DEFAULT 'Kho khô',
  `dinhMucToiThieu` decimal(12,2) DEFAULT 0.00,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maNL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng ThanhPham
DROP TABLE IF EXISTS `ThanhPham`;
CREATE TABLE `ThanhPham` (
  `maTP` varchar(20) NOT NULL,
  `tenTP` varchar(150) NOT NULL,
  `tenNhom` varchar(100) DEFAULT 'Bánh quy bơ',
  `donViTinh` varchar(20) NOT NULL,
  `quyCachDongGoi` varchar(100) DEFAULT NULL,
  `soLuongToiThieu` int(11) DEFAULT 0,
  `soLuongToiDa` int(11) DEFAULT 2000,
  `soLuongTon` decimal(12,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  PRIMARY KEY (`maTP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- NHÓM 2: TRỌNG TÂM TỒN KHO & LÔ HÀNG (3 BẢNG)
-- =========================================================================

-- 6. Bảng LoNguyenLieu
DROP TABLE IF EXISTS `LoNguyenLieu`;
CREATE TABLE `LoNguyenLieu` (
  `maLoNL` varchar(30) NOT NULL,
  `maNL` varchar(20) NOT NULL,
  `nhaCungCap` varchar(150) DEFAULT 'Công ty Bột mì Đại Phong',
  `ngaySanXuat` date NOT NULL,
  `hanSuDung` date NOT NULL,
  `soLuongLo` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Số lượng dự kiến ban đầu của lô',
  `soLuongTon` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Số lượng thực tồn hiện tại',
  `trangThaiKiemDinh` varchar(50) DEFAULT 'Chờ kiểm định' COMMENT 'Chờ kiểm định, Đạt, Không đạt',
  `trangThai` varchar(50) DEFAULT 'Chờ điều phối' COMMENT 'Chờ điều phối, Chờ nhập kho, Đã lưu kho, Đã xuất hết, Khóa',
  PRIMARY KEY (`maLoNL`),
  KEY `fk_lonl_nl` (`maNL`),
  CONSTRAINT `fk_lonl_nl` FOREIGN KEY (`maNL`) REFERENCES `NguyenLieu` (`maNL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng LoThanhPham
DROP TABLE IF EXISTS `LoThanhPham`;
CREATE TABLE `LoThanhPham` (
  `maLoTP` varchar(30) NOT NULL,
  `maTP` varchar(20) NOT NULL,
  `phanXuong` varchar(100) DEFAULT 'Phân xưởng nướng số 1',
  `ngaySanXuat` date NOT NULL,
  `hanSuDung` date NOT NULL,
  `soLuongLo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongTon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `trangThaiKiemDinh` varchar(50) DEFAULT 'Chờ kiểm định' COMMENT 'Chờ kiểm định, Đạt, Không đạt',
  `trangThai` varchar(50) DEFAULT 'Chờ điều phối' COMMENT 'Chờ điều phối, Chờ nhập kho, Đã lưu kho, Đã xuất hết, Khóa',
  PRIMARY KEY (`maLoTP`),
  KEY `fk_lotp_tp` (`maTP`),
  CONSTRAINT `fk_lotp_tp` FOREIGN KEY (`maTP`) REFERENCES `ThanhPham` (`maTP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng TonKho (Trái tim hệ thống: Lưu số lượng tồn thực tế và tạm giữ FEFO tại từng kệ)
DROP TABLE IF EXISTS `TonKho`;
CREATE TABLE `TonKho` (
  `maTonKho` varchar(20) NOT NULL,
  `loaiHang` varchar(20) NOT NULL DEFAULT 'NL' COMMENT 'NL (Nguyên liệu) hoặc TP (Thành phẩm)',
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc Mã lô TP',
  `maViTri` varchar(20) NOT NULL,
  `slTon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongTamGiu` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Số lượng đang giữ cho lệnh điều phối chờ xuất',
  `ngayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trangThai` varchar(50) DEFAULT 'Khả dụng' COMMENT 'Khả dụng, Đang giữ, Khóa',
  PRIMARY KEY (`maTonKho`),
  UNIQUE KEY `uq_lo_vitri` (`maLo`, `maViTri`),
  KEY `fk_tonkho_vitri` (`maViTri`),
  CONSTRAINT `fk_tonkho_vitri` FOREIGN KEY (`maViTri`) REFERENCES `ViTriKho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- NHÓM 3: PHÂN HỆ KIỂM ĐỊNH CHẤT LƯỢNG QC - PHÚC (1 BẢNG)
-- =========================================================================

-- 9. Bảng BienBanKiemDinh (Phục vụ Module 6 của Phúc: UC 6.1 & 6.2)
DROP TABLE IF EXISTS `BienBanKiemDinh`;
CREATE TABLE `BienBanKiemDinh` (
  `maBienBan` varchar(20) NOT NULL,
  `loaiKiemDinh` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: Nguyên liệu, 2: Thành phẩm',
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc Mã lô TP được kiểm định',
  `ngayKiemDinh` datetime DEFAULT current_timestamp(),
  `nguoiKiemDinh` varchar(20) NOT NULL,
  `tieuChiDanhGia` text DEFAULT NULL COMMENT 'Ghi nhận kết quả các tiêu chí: Độ ẩm, mùi vị, bao bì...',
  `ketQuaTongThe` varchar(20) NOT NULL COMMENT 'Đạt, Không đạt',
  `lyDoKhongDat` varchar(500) DEFAULT NULL,
  `ghiChu` varchar(500) DEFAULT NULL,
  `trangThaiBienBan` varchar(50) DEFAULT 'Đã xác nhận',
  PRIMARY KEY (`maBienBan`),
  KEY `fk_bbkd_nv` (`nguoiKiemDinh`),
  CONSTRAINT `fk_bbkd_nv` FOREIGN KEY (`nguoiKiemDinh`) REFERENCES `NhanVien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- NHÓM 4: PHÂN HỆ NHẬP KHO - SANG & LỘC (2 BẢNG)
-- =========================================================================

-- 10. Bảng LenhDieuPhoiNhap (Dùng cho UC 1.3, 1.4 của Sang & UC 3.1, 3.2 của Lộc)
DROP TABLE IF EXISTS `LenhDieuPhoiNhap`;
CREATE TABLE `LenhDieuPhoiNhap` (
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `loaiHang` varchar(20) NOT NULL DEFAULT 'NL' COMMENT 'NL hoặc TP',
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc Mã lô TP',
  `maKhoTiepNhan` varchar(20) NOT NULL,
  `soLuongPhanBo` decimal(12,2) NOT NULL,
  `ngayDieuPhoi` datetime DEFAULT current_timestamp(),
  `nguoiDieuPhoi` varchar(20) NOT NULL,
  `lyDoGiaiTrinh` varchar(255) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Chờ nhập kho' COMMENT 'Chờ nhập kho, Hoàn tất nhập kho, Hoàn tất nhập kho (Hao hụt)',
  PRIMARY KEY (`maLenhDieuPhoi`),
  KEY `fk_ldpn_kho` (`maKhoTiepNhan`),
  KEY `fk_ldpn_nv` (`nguoiDieuPhoi`),
  CONSTRAINT `fk_ldpn_kho` FOREIGN KEY (`maKhoTiepNhan`) REFERENCES `Kho` (`maKho`),
  CONSTRAINT `fk_ldpn_nv` FOREIGN KEY (`nguoiDieuPhoi`) REFERENCES `NhanVien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Bảng PhieuNhapKho (Chứng từ thực nhập lên kệ - Tăng tồn kho của Sang & Lộc)
DROP TABLE IF EXISTS `PhieuNhapKho`;
CREATE TABLE `PhieuNhapKho` (
  `maPNK` varchar(30) NOT NULL,
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `ngayNhapThucTe` datetime DEFAULT current_timestamp(),
  `nguoiLap` varchar(20) NOT NULL,
  `slThucNhap` decimal(12,2) NOT NULL,
  `soLuongChenhLech` decimal(12,2) DEFAULT 0.00 COMMENT 'Hao hụt = soLuongPhanBo - slThucNhap',
  `lyDoChenhLech` varchar(255) DEFAULT NULL COMMENT 'Bắt buộc nhập khi có hao hụt bánh vỡ/rách bao',
  `ghiChu` varchar(500) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoàn tất',
  PRIMARY KEY (`maPNK`),
  KEY `fk_pnk_lenh` (`maLenhDieuPhoi`),
  KEY `fk_pnk_vitri` (`maViTri`),
  KEY `fk_pnk_nv` (`nguoiLap`),
  CONSTRAINT `fk_pnk_lenh` FOREIGN KEY (`maLenhDieuPhoi`) REFERENCES `LenhDieuPhoiNhap` (`maLenhDieuPhoi`),
  CONSTRAINT `fk_pnk_vitri` FOREIGN KEY (`maViTri`) REFERENCES `ViTriKho` (`maViTri`),
  CONSTRAINT `fk_pnk_nv` FOREIGN KEY (`nguoiLap`) REFERENCES `NhanVien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- NHÓM 5: PHÂN HỆ XUẤT KHO FEFO - TOÀN & THUẬN (4 BẢNG)
-- =========================================================================

-- 12. Bảng PhieuYeuCauXuat (Yêu cầu xuất NVL cho xưởng hoặc xuất TP cho đại lý)
DROP TABLE IF EXISTS `PhieuYeuCauXuat`;
CREATE TABLE `PhieuYeuCauXuat` (
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `loaiYeuCau` varchar(20) NOT NULL DEFAULT 'NL' COMMENT 'NL (Xuất nguyên liệu - Toàn) hoặc TP (Xuất thành phẩm - Thuận)',
  `donViYeuCau` varchar(150) NOT NULL COMMENT 'Tên phân xưởng hoặc Tên đại lý/Đơn hàng',
  `ngayYeuCau` date DEFAULT curdate(),
  `nguoiYeuCau` varchar(100) NOT NULL,
  `lyDoXuat` varchar(255) DEFAULT NULL,
  `tongSoLuong` decimal(14,2) DEFAULT 0.00,
  `trangThai` varchar(50) DEFAULT 'Chờ xử lý' COMMENT 'Chờ xử lý, Đã điều phối, Hoàn tất',
  PRIMARY KEY (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Bảng ChiTietYeuCauXuat
DROP TABLE IF EXISTS `ChiTietYeuCauXuat`;
CREATE TABLE `ChiTietYeuCauXuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `maMatHang` varchar(20) NOT NULL COMMENT 'Lưu maNL hoặc maTP',
  `tenMatHang` varchar(150) DEFAULT NULL,
  `soLuongYeuCau` decimal(12,2) NOT NULL,
  `donViTinh` varchar(20) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_ctycx_pycx` (`maPhieuYeuCau`),
  CONSTRAINT `fk_ctycx_pycx` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `PhieuYeuCauXuat` (`maPhieuYeuCau`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Bảng LenhDieuPhoiXuat (Header lệnh điều phối xuất của Toàn & Thuận)
DROP TABLE IF EXISTS `LenhDieuPhoiXuat`;
CREATE TABLE `LenhDieuPhoiXuat` (
  `maLenh` varchar(30) NOT NULL,
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `ngayDieuPhoi` datetime DEFAULT current_timestamp(),
  `nguoiDieuPhoi` varchar(20) NOT NULL,
  `ghiChu` varchar(255) DEFAULT NULL COMMENT 'Ghi lý do nếu cố tình chọn lô bỏ qua FEFO',
  `trangThai` varchar(50) DEFAULT 'Chờ xuất' COMMENT 'Chờ xuất, Đã xuất',
  PRIMARY KEY (`maLenh`),
  KEY `fk_ldpx_pycx` (`maPhieuYeuCau`),
  KEY `fk_ldpx_kho` (`maKho`),
  KEY `fk_ldpx_nv` (`nguoiDieuPhoi`),
  CONSTRAINT `fk_ldpx_pycx` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `PhieuYeuCauXuat` (`maPhieuYeuCau`),
  CONSTRAINT `fk_ldpx_kho` FOREIGN KEY (`maKho`) REFERENCES `Kho` (`maKho`),
  CONSTRAINT `fk_ldpx_nv` FOREIGN KEY (`nguoiDieuPhoi`) REFERENCES `NhanVien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Bảng ChiTietDieuPhoiXuat (Chi tiết phân bổ từng Lô - Vị trí theo thuật toán FEFO)
DROP TABLE IF EXISTS `ChiTietDieuPhoiXuat`;
CREATE TABLE `ChiTietDieuPhoiXuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maLenh` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc TP được chọn theo FEFO',
  `maViTri` varchar(20) NOT NULL,
  `soLuongPhanBo` decimal(12,2) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_ctdpx_lenh` (`maLenh`),
  KEY `fk_ctdpx_vitri` (`maViTri`),
  CONSTRAINT `fk_ctdpx_lenh` FOREIGN KEY (`maLenh`) REFERENCES `LenhDieuPhoiXuat` (`maLenh`) ON DELETE CASCADE,
  CONSTRAINT `fk_ctdpx_vitri` FOREIGN KEY (`maViTri`) REFERENCES `ViTriKho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================================
-- NHÓM 6: PHÂN HỆ KIỂM KÊ KHO - NGÂN (2 BẢNG)
-- =========================================================================

-- 16. Bảng BienBanKiemKe (Phục vụ Module 5 của Ngân: UC 5.2)
DROP TABLE IF EXISTS `BienBanKiemKe`;
CREATE TABLE `BienBanKiemKe` (
  `maBienBan` varchar(20) NOT NULL,
  `tenDotKiemKe` varchar(150) NOT NULL DEFAULT 'Kiểm kê định kỳ Tháng 10/2026',
  `loaiKiemKe` varchar(50) NOT NULL DEFAULT 'Kiểm kê nguyên liệu' COMMENT 'Kiểm kê nguyên liệu, Kiểm kê thành phẩm',
  `maKho` varchar(20) NOT NULL,
  `ngayKiemKe` date DEFAULT curdate(),
  `nguoiKiemKe` varchar(20) NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Đã hoàn tất' COMMENT 'Đang đếm, Đã hoàn tất',
  PRIMARY KEY (`maBienBan`),
  KEY `fk_bbkk_kho` (`maKho`),
  KEY `fk_bbkk_nv` (`nguoiKiemKe`),
  CONSTRAINT `fk_bbkk_kho` FOREIGN KEY (`maKho`) REFERENCES `Kho` (`maKho`),
  CONSTRAINT `fk_bbkk_nv` FOREIGN KEY (`nguoiKiemKe`) REFERENCES `NhanVien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Bảng ChiTietKiemKe (Lưu số lượng sổ sách vs thực tế và chênh lệch)
DROP TABLE IF EXISTS `ChiTietKiemKe`;
CREATE TABLE `ChiTietKiemKe` (
  `maChiTiet` varchar(20) NOT NULL,
  `maBienBan` varchar(20) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `maViTri` varchar(20) NOT NULL,
  `soLuongTheoSoSach` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongTotThucTe` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongHongThucTe` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongChenhLech` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '(Tốt + Hỏng) - Sổ sách',
  `tinhTrang` varchar(255) DEFAULT 'Bao bì nguyên vẹn',
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_ctkk_bb` (`maBienBan`),
  KEY `fk_ctkk_vitri` (`maViTri`),
  CONSTRAINT `fk_ctkk_bb` FOREIGN KEY (`maBienBan`) REFERENCES `BienBanKiemKe` (`maBienBan`) ON DELETE CASCADE,
  CONSTRAINT `fk_ctkk_vitri` FOREIGN KEY (`maViTri`) REFERENCES `ViTriKho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================================
-- INSERT DỮ LIỆU MẪU (CHUẨN BỊ SẴN KỊCH BẢN DEMO CHO 6 THÀNH VIÊN)
-- =========================================================================

-- 1. Tài khoản 6 thành viên nhóm Vượt Qua (Mật khẩu chung: 123456)
INSERT INTO `NhanVien` (`maNV`, `hoTen`, `tenDangNhap`, `matKhau`, `vaiTro`, `soDienThoai`, `email`) VALUES
('NV001', 'Lê Minh Thiện Phúc', 'phuc_qc', '123456', 'QC', '0901000001', 'phuc@iuh.edu.vn'),
('NV002', 'Võ Thị Tuyết Ngân', 'ngan_kiemke', '123456', 'KiemKe', '0901000002', 'ngan@iuh.edu.vn'),
('NV003', 'Trần Bích Thuận', 'thuan_qlk', '123456', 'QuanLyKho', '0901000003', 'thuan@iuh.edu.vn'),
('NV004', 'Trần Thanh Toàn', 'toan_qlk', '123456', 'QuanLyKho', '0901000004', 'toan@iuh.edu.vn'),
('NV005', 'Trần Văn Sang', 'sang_kho', '123456', 'NhanVienKho', '0901000005', 'sang@iuh.edu.vn'),
('NV006', 'Nguyễn Hữu Lộc', 'loc_kho', '123456', 'NhanVienKho', '0901000006', 'loc@iuh.edu.vn');

-- 2. Danh mục Kho
INSERT INTO `Kho` (`maKho`, `tenKho`, `loaiKho`, `dieuKienBaoQuan`, `diaChi`, `sucChuaToiDa`, `sucChuaHienTai`) VALUES
('KHO_NVL_01', 'Kho Nguyên Liệu Khô 1', 'Kho NVL', 'Kho khô', 'Khu A - Nhà máy Bánh Quy', 2000, 500),
('KHO_NVL_02', 'Kho Nguyên Liệu Lạnh 2', 'Kho NVL', 'Kho lạnh', 'Khu A - Nhà máy Bánh Quy', 1000, 200),
('KHO_TP_01', 'Kho Thành Phẩm Bánh Quy 1', 'Kho Thành phẩm', 'Kho khô', 'Khu B - Nhà máy Bánh Quy', 3000, 600),
('KHO_LOI_01', 'Kho Hàng Lỗi & Cách Ly', 'Kho Hàng lỗi', 'Kho khô', 'Khu C - Nhà máy Bánh Quy', 500, 0);

-- 3. Danh mục Vị trí Ô Kệ (Có kệ đang chứa và kệ trống để Sang & Lộc chọn khi nhập kho)
INSERT INTO `ViTriKho` (`maViTri`, `maKho`, `day`, `ke`, `tang`, `sucChuaKhaDung`, `trangThaiSuDung`, `trangThaiChuaHang`) VALUES
('A1-K01-T01', 'KHO_NVL_01', 'A1', 'K01', 'T01', 300, 'Đang chứa', 'Khả dụng'),
('A1-K01-T02', 'KHO_NVL_01', 'A1', 'K01', 'T02', 300, 'Đang chứa', 'Khả dụng'),
('A1-K02-T01', 'KHO_NVL_01', 'A1', 'K02', 'T01', 500, 'Trống', 'Khả dụng'),
('L1-K01-T01', 'KHO_NVL_02', 'L1', 'K01', 'T01', 400, 'Trống', 'Khả dụng'),
('B1-K01-T01', 'KHO_TP_01', 'B1', 'K01', 'T01', 400, 'Đang chứa', 'Khả dụng'),
('B1-K01-T02', 'KHO_TP_01', 'B1', 'K01', 'T02', 400, 'Đang chứa', 'Khả dụng'),
('B1-K02-T01', 'KHO_TP_01', 'B1', 'K02', 'T01', 500, 'Trống', 'Khả dụng'),
('C1-K01-T01', 'KHO_LOI_01', 'C1', 'K01', 'T01', 500, 'Trống', 'Khả dụng');

-- 4. Danh mục Nguyên liệu & Thành phẩm
INSERT INTO `NguyenLieu` (`maNL`, `tenNL`, `donViTinh`, `loaiKhoBaoQuan`, `dinhMucToiThieu`, `soLuongTon`) VALUES
('NL001', 'Bột mì đa dụng số 8', 'Kg', 'Kho khô', 200.00, 500.00),
('NL002', 'Bơ lạt Anchor New Zealand', 'Kg', 'Kho lạnh', 50.00, 0.00),
('NL003', 'Đường tinh luyện Biên Hòa', 'Kg', 'Kho khô', 100.00, 0.00);

INSERT INTO `ThanhPham` (`maTP`, `tenTP`, `tenNhom`, `donViTinh`, `quyCachDongGoi`, `soLuongToiThieu`, `soLuongToiDa`, `soLuongTon`) VALUES
('TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 'Bánh quy bơ', 'Hộp', '24 hộp/thùng', 100, 2000, 600.00),
('TP002', 'Bánh quy kẹp kem Socola Oreo 133g', 'Bánh quy kẹp kem', 'Hộp', '36 hộp/thùng', 100, 2000, 0.00);

-- 5. Dữ liệu Lô Nguyên Liệu (Phục vụ Phúc QC, Sang Nhập kho, Toàn Xuất FEFO)
INSERT INTO `LoNguyenLieu` (`maLoNL`, `maNL`, `nhaCungCap`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
-- 2 lô đã lưu kho có HSD khác nhau để Toàn chạy thuật toán FEFO:
('LONL_20261001_001', 'NL001', 'Công ty Bột mì Đại Phong', '2026-09-01', '2026-11-05', 200.00, 200.00, 'Đạt', 'Đã lưu kho'),
('LONL_20261010_002', 'NL001', 'Công ty Bột mì Đại Phong', '2026-10-01', '2027-03-01', 300.00, 300.00, 'Đạt', 'Đã lưu kho'),
-- 1 lô đã Đạt QC và đang chờ Sang lập phiếu nhập kho (UC 1.4):
('LONL_20261022_003', 'NL001', 'Công ty Bột mì Đại Phong', '2026-10-20', '2027-04-20', 150.00, 0.00, 'Đạt', 'Chờ nhập kho'),
-- 1 lô mới tinh đang chờ Phúc lập biên bản kiểm định QC (UC 6.1):
('LONL_20261024_004', 'NL002', 'Công ty Sữa & Bơ Quốc Tế', '2026-10-22', '2027-02-22', 100.00, 0.00, 'Chờ kiểm định', 'Chờ điều phối');

-- 6. Dữ liệu Lô Thành Phẩm (Phục vụ Lộc Nhập kho TP, Thuận Xuất kho TP)
INSERT INTO `LoThanhPham` (`maLoTP`, `maTP`, `phanXuong`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
-- 2 lô bánh quy đã lưu kho có HSD khác nhau để Thuận điều phối xuất TP theo FEFO (UC 4.1):
('LOTP_20261005_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-05', '2026-11-10', 250.00, 250.00, 'Đạt', 'Đã lưu kho'),
('LOTP_20261015_002', 'TP001', 'Phân xưởng nướng số 1', '2026-10-15', '2027-05-15', 350.00, 350.00, 'Đạt', 'Đã lưu kho'),
-- 1 lô bánh quy vừa ra lò Đạt QC, đang chờ Lộc lập phiếu nhập thành phẩm (UC 3.2):
('LOTP_20261023_003', 'TP001', 'Phân xưởng nướng số 2', '2026-10-23', '2027-06-23', 200.00, 0.00, 'Đạt', 'Chờ nhập kho');

-- 7. Dữ liệu Tồn Kho tại từng Vị Trí Kệ (Khớp với các lô 'Đã lưu kho' ở trên)
INSERT INTO `TonKho` (`maTonKho`, `loaiHang`, `maLo`, `maViTri`, `slTon`, `soLuongTamGiu`, `trangThai`) VALUES
('TK_20261001_001', 'NL', 'LONL_20261001_001', 'A1-K01-T01', 200.00, 0.00, 'Khả dụng'),
('TK_20261010_002', 'NL', 'LONL_20261010_002', 'A1-K01-T02', 300.00, 0.00, 'Khả dụng'),
('TK_20261005_003', 'TP', 'LOTP_20261005_001', 'B1-K01-T01', 250.00, 0.00, 'Khả dụng'),
('TK_20261015_004', 'TP', 'LOTP_20261015_002', 'B1-K01-T02', 350.00, 0.00, 'Khả dụng');

-- 8. Lệnh Điều Phối Nhập Kho chờ sẵn cho Sang (UC 1.4) và Lộc (UC 3.2) bấm lập phiếu nhập
INSERT INTO `LenhDieuPhoiNhap` (`maLenhDieuPhoi`, `loaiHang`, `maLo`, `maKhoTiepNhan`, `soLuongPhanBo`, `nguoiDieuPhoi`, `lyDoGiaiTrinh`, `trangThai`) VALUES
('LDPN_20261022_001', 'NL', 'LONL_20261022_003', 'KHO_NVL_01', 150.00, 'NV004', 'Nhập bột mì đạt chuẩn vào Kho Khô 1', 'Chờ nhập kho'),
('LDPN_20261023_002', 'TP', 'LOTP_20261023_003', 'KHO_TP_01', 200.00, 'NV003', 'Nhập bánh quy ca sáng vào Kho Thành Phẩm 1', 'Chờ nhập kho');

-- 9. Phiếu Yêu Cầu Xuất chờ sẵn cho Toàn (UC 2.4) và Thuận (UC 4.1) bấm Điều phối FEFO
INSERT INTO `PhieuYeuCauXuat` (`maPhieuYeuCau`, `loaiYeuCau`, `donViYeuCau`, `ngayYeuCau`, `nguoiYeuCau`, `lyDoXuat`, `tongSoLuong`, `trangThai`) VALUES
('PYCX_20261024_001', 'NL', 'Phân xưởng nướng số 1', '2026-10-24', 'Trưởng xưởng Lê Văn A', 'Cấp bột mì làm bánh ca sáng (cần 250kg -> FEFO sẽ lấy 200kg lô cận hạn + 50kg lô mới)', 250.00, 'Chờ xử lý'),
('PYCX_20261024_002', 'TP', 'Đại lý Siêu thị Co.opmart Gò Vấp', '2026-10-24', 'Phòng Kinh Doanh', 'Xuất giao đơn hàng bánh quy Danisa (cần 300 hộp -> FEFO lấy 250 hộp lô cận hạn + 50 hộp lô mới)', 300.00, 'Chờ xử lý');

INSERT INTO `ChiTietYeuCauXuat` (`maChiTiet`, `maPhieuYeuCau`, `maMatHang`, `tenMatHang`, `soLuongYeuCau`, `donViTinh`) VALUES
('CTYCX_000001', 'PYCX_20261024_001', 'NL001', 'Bột mì đa dụng số 8', 250.00, 'Kg'),
('CTYCX_000002', 'PYCX_20261024_002', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 300.00, 'Hộp');

SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- CÁC BẢNG BỔ SUNG ĐẦY ĐỦ THEO BÁO CÁO TUẦN 9 NHÓM VƯỢT QUÁ
-- ========================================================

-- 1. Bảng VaiTro (Phân quyền người dùng)
CREATE TABLE IF NOT EXISTS `VaiTro` (
  `maVaiTro` VARCHAR(20) NOT NULL PRIMARY KEY,
  `tenVaiTro` VARCHAR(100) NOT NULL UNIQUE,
  `moTa` VARCHAR(255) NULL,
  `ngayTao` DATE DEFAULT CURRENT_DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `VaiTro` (`maVaiTro`, `tenVaiTro`, `moTa`) VALUES
('VT01', 'Admin', 'Quản trị hệ thống: quản lý tài khoản, vai trò, danh mục master data'),
('VT02', 'Quản lý kho', 'Phê duyệt yêu cầu nhập/xuất, ban hành lệnh điều phối, xem báo cáo tồn kho'),
('VT03', 'Thủ kho', 'Thực hiện nhập/xuất thực tế, lập phiếu nhập kho, phiếu xuất kho, sắp xếp vị trí kệ'),
('VT04', 'Điều phối viên', 'Lập lệnh điều phối nhập/xuất theo nguyên tắc FEFO và tối ưu vị trí kệ'),
('VT05', 'Nhân viên kiểm kê', 'Thực hiện kiểm đếm thực tế, lập biên bản kiểm kê và ghi nhận chênh lệch'),
('VT06', 'KCS/QC', 'Kiểm định chất lượng lô nguyên liệu/thành phẩm, lập biên bản kiểm định'),
('VT07', 'Ban giám đốc', 'Xem báo cáo thống kê toàn hệ thống, phê duyệt đề nghị tiêu hủy hàng hỏng');

-- Cập nhật bổ sung cột maVaiTro vào NhanVien nếu chưa có
-- (Bảng NhanVien đã có sẵn vaiTro text, liên kết thêm maVaiTro)
ALTER TABLE `NhanVien` ADD COLUMN IF NOT EXISTS `maVaiTro` VARCHAR(20) DEFAULT 'VT03';

UPDATE `NhanVien` SET `maVaiTro` = 'VT01' WHERE `maNV` = 'NV001';
UPDATE `NhanVien` SET `maVaiTro` = 'VT02' WHERE `maNV` = 'NV002';
UPDATE `NhanVien` SET `maVaiTro` = 'VT03' WHERE `maNV` = 'NV003';
UPDATE `NhanVien` SET `maVaiTro` = 'VT04' WHERE `maNV` = 'NV004';
UPDATE `NhanVien` SET `maVaiTro` = 'VT05' WHERE `maNV` = 'NV005';
UPDATE `NhanVien` SET `maVaiTro` = 'VT06' WHERE `maNV` = 'NV006';
UPDATE `NhanVien` SET `maVaiTro` = 'VT07' WHERE `maNV` = 'NV007';

-- 2. Bảng TaiKhoan (Tài khoản bảo mật độc lập)
CREATE TABLE IF NOT EXISTS `TaiKhoan` (
  `maTaiKhoan` VARCHAR(20) NOT NULL PRIMARY KEY,
  `maNV` VARCHAR(20) NOT NULL UNIQUE,
  `maVaiTro` VARCHAR(20) NOT NULL,
  `tenDangNhap` VARCHAR(50) NOT NULL UNIQUE,
  `matKhau` VARCHAR(255) NOT NULL,
  `trangThai` VARCHAR(50) DEFAULT 'Hoạt động',
  `ngayTao` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `TaiKhoan` (`maTaiKhoan`, `maNV`, `maVaiTro`, `tenDangNhap`, `matKhau`, `trangThai`) VALUES
('TK001', 'NV001', 'VT01', 'admin', '123456', 'Hoạt động'),
('TK002', 'NV002', 'VT02', 'quanlykho', '123456', 'Hoạt động'),
('TK003', 'NV003', 'VT03', 'thukho', '123456', 'Hoạt động'),
('TK004', 'NV004', 'VT04', 'dieuphoi', '123456', 'Hoạt động'),
('TK005', 'NV005', 'VT05', 'kiemke', '123456', 'Hoạt động'),
('TK006', 'NV006', 'VT06', 'kcs_qc', '123456', 'Hoạt động'),
('TK007', 'NV007', 'VT07', 'giamdoc', '123456', 'Hoạt động');

-- 3. Bảng NhaCungCap (Nhà cung ứng nguyên liệu)
CREATE TABLE IF NOT EXISTS `NhaCungCap` (
  `maNCC` VARCHAR(20) NOT NULL PRIMARY KEY,
  `tenNCC` VARCHAR(150) NOT NULL,
  `soDienThoai` VARCHAR(15) NULL,
  `diaChi` VARCHAR(255) NULL,
  `email` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `NhaCungCap` (`maNCC`, `tenNCC`, `soDienThoai`, `diaChi`, `email`) VALUES
('NCC01', 'Công ty CP Xay Lúa Mì Việt Nam', '02838123456', 'KCN Tân Bình, Tân Phú, TP.HCM', 'sales@luami.vn'),
('NCC02', 'Fonterra Brands Vietnam (Anchor)', '02839988776', 'Tòa nhà Landmark 81, Bình Thạnh, TP.HCM', 'contact@fonterra.com'),
('NCC03', 'Công ty CP Đường Biên Hòa', '02513836171', 'KCN Biên Hòa 1, Đồng Nai', 'info@bh sugar.com'),
('NCC04', 'Công ty TNHH Ba Huân (Trứng tươi)', '02837550123', 'Bình Chánh, TP.HCM', 'kinhdoanh@bahuan.vn');

-- 4. Bảng KeHoachSanXuat (Kế hoạch sản xuất từ Ban giám đốc)
CREATE TABLE IF NOT EXISTS `KeHoachSanXuat` (
  `maKeHoach` VARCHAR(20) NOT NULL PRIMARY KEY,
  `maPhanXuong` VARCHAR(20) NOT NULL,
  `ngayBatDau` DATE NOT NULL,
  `ngayKetThuc` DATE NOT NULL,
  `trangThai` VARCHAR(50) DEFAULT 'Đang sản xuất'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `KeHoachSanXuat` (`maKeHoach`, `maPhanXuong`, `ngayBatDau`, `ngayKetThuc`, `trangThai`) VALUES
('KHSX-2026-Q4-01', 'PX-BANH-01', '2026-10-01', '2026-10-31', 'Đang sản xuất'),
('KHSX-2026-Q4-02', 'PX-BANH-02', '2026-10-15', '2026-11-15', 'Lập kế hoạch');

-- 5. Bảng PhieuYeuCauNhap (Phiếu yêu cầu nhập nguyên liệu từ NCC)
CREATE TABLE IF NOT EXISTS `PhieuYeuCauNhap` (
  `maPhieuYeuCau` VARCHAR(30) NOT NULL PRIMARY KEY,
  `ngayYeuCau` DATE NOT NULL,
  `ngayGiaoDuKien` DATE NULL,
  `nguoiYeuCau` VARCHAR(100) NOT NULL,
  `loaiYeuCau` VARCHAR(50) DEFAULT 'Nhập nguyên liệu từ NCC',
  `trangThai` VARCHAR(50) DEFAULT 'Đã phê duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `PhieuYeuCauNhap` (`maPhieuYeuCau`, `ngayYeuCau`, `ngayGiaoDuKien`, `nguoiYeuCau`, `loaiYeuCau`, `trangThai`) VALUES
('PYCN-2026-089', '2026-10-18', '2026-10-21', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Đã tiếp nhận'),
('PYCN-2026-092', '2026-10-19', '2026-10-22', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Đã tiếp nhận'),
('PYCN-2026-095', '2026-10-20', '2026-10-24', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Chờ kiểm định');

-- 6. Bảng NhomThanhPham (Danh mục nhóm bánh thành phẩm)
CREATE TABLE IF NOT EXISTS `NhomThanhPham` (
  `maNhom` VARCHAR(20) NOT NULL PRIMARY KEY,
  `tenNhom` VARCHAR(100) NOT NULL,
  `moTa` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `NhomThanhPham` (`maNhom`, `tenNhom`, `moTa`) VALUES
('NTP01', 'Bánh quy bơ cao cấp', 'Dòng bánh quy nướng hương bơ truyền thống hộp thiếc sang trọng'),
('NTP02', 'Bánh quy kẹp kem', 'Bánh quy giòn nhân kem vani và socola ngọt dịu'),
('NTP03', 'Bánh quy dinh dưỡng hạt', 'Bánh quy ngũ cốc yến mạch tốt cho sức khỏe');

-- 7. Bảng TieuChiKiemDinh & ChiTietKiemDinh
CREATE TABLE IF NOT EXISTS `TieuChiKiemDinh` (
  `maTieuChi` VARCHAR(20) NOT NULL PRIMARY KEY,
  `tenTieuChi` VARCHAR(150) NOT NULL,
  `quyChuan` VARCHAR(150) NOT NULL,
  `loaiHang` VARCHAR(50) DEFAULT 'Nguyên liệu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `TieuChiKiemDinh` (`maTieuChi`, `tenTieuChi`, `quyChuan`, `loaiHang`) VALUES
('TC-BM01', 'Độ ẩm bột tiêu chuẩn', '≤ 14.0 %', 'Nguyên liệu'),
('TC-BM02', 'Hàm lượng Protein (Gluten)', '9.5 % – 11.5 %', 'Nguyên liệu'),
('TC-BM03', 'Màu sắc, mùi vị & tạp chất', 'Trắng ngà, không ẩm mốc', 'Nguyên liệu'),
('TC-BO01', 'Hàm lượng chất béo bơ', '≥ 82.0 %', 'Nguyên liệu'),
('TC-BO02', 'Nhiệt độ bảo quản xe lạnh', '≤ 4.0 °C', 'Nguyên liệu');

-- 8. Bảng DotKiemKe (Lập kế hoạch đợt kiểm kê định kỳ)
CREATE TABLE IF NOT EXISTS `DotKiemKe` (
  `maDotKiemKe` VARCHAR(30) NOT NULL PRIMARY KEY,
  `tenDotKiemKe` VARCHAR(150) NOT NULL,
  `ngayBatDau` DATE NOT NULL,
  `ngayKetThuc` DATE NOT NULL,
  `maKho` VARCHAR(20) NOT NULL,
  `trangThai` VARCHAR(50) DEFAULT 'Đang thực hiện'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `DotKiemKe` (`maDotKiemKe`, `tenDotKiemKe`, `ngayBatDau`, `ngayKetThuc`, `maKho`, `trangThai`) VALUES
('KK-2026-Q4-01', 'Kiểm kê định kỳ tháng 10/2026 Kho Nguyên Liệu', '2026-10-21', '2026-10-22', 'KHO_NVL', 'Đang thực hiện'),
('KK-2026-Q4-02', 'Kiểm kê đột xuất Kho Thành Phẩm A', '2026-10-23', '2026-10-24', 'KHO_TPA', 'Lập kế hoạch');

-- 9. Bảng PhieuXuatKho (Phiếu xuất kho thực tế)
CREATE TABLE IF NOT EXISTS `PhieuXuatKho` (
  `maPXK` VARCHAR(30) NOT NULL PRIMARY KEY,
  `maLenhDieuPhoi` VARCHAR(30) NOT NULL,
  `ngayXuatThucTe` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `nguoiXuat` VARCHAR(100) NOT NULL,
  `ghiChu` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Bảng BienBanThieuHang & BienBanXuLyHangHong & DeNghiTieuHuy
CREATE TABLE IF NOT EXISTS `BienBanThieuHang` (
  `maBienBan` VARCHAR(30) NOT NULL PRIMARY KEY,
  `maPXK` VARCHAR(30) NOT NULL,
  `maLo` VARCHAR(30) NOT NULL,
  `soLuongThieu` DECIMAL(12,2) NOT NULL,
  `lyDo` VARCHAR(255) NULL,
  `ngayLap` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `BienBanXuLyHangHong` (
  `maBienBan` VARCHAR(30) NOT NULL PRIMARY KEY,
  `maLo` VARCHAR(30) NOT NULL,
  `loaiHang` VARCHAR(50) NOT NULL,
  `soLuongHong` DECIMAL(12,2) NOT NULL,
  `nguyenNhan` VARCHAR(255) NULL,
  `bienPhapXuLy` VARCHAR(255) NULL,
  `ngayLap` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `DeNghiTieuHuy` (
  `maDeNghi` VARCHAR(30) NOT NULL PRIMARY KEY,
  `maLo` VARCHAR(30) NOT NULL,
  `loaiHang` VARCHAR(50) NOT NULL,
  `soLuong` DECIMAL(12,2) NOT NULL,
  `lyDo` VARCHAR(255) NOT NULL,
  `nguoiDeNghi` VARCHAR(100) NOT NULL,
  `ngayDeNghi` DATE NOT NULL,
  `trangThai` VARCHAR(50) DEFAULT 'Chờ giám đốc duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
