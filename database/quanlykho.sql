-- =====================================================================
-- MariaDB / MySQL Database Dump
-- Hệ Thống Quản Lý Kho Vượt Quá (BiscuitWMS)
-- Database: quanlykho_vuotqua
-- Export Date: 2026-10-26 15:15:10
-- =====================================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `quanlykho_vuotqua` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `quanlykho_vuotqua`;

-- -------------------------------------------------------------
-- Cấu trúc bảng `bienbankiemdinh`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `bienbankiemdinh`;
CREATE TABLE `bienbankiemdinh` (
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
  CONSTRAINT `fk_bbkd_nv` FOREIGN KEY (`nguoiKiemDinh`) REFERENCES `nhanvien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `bienbankiemdinh` (5 bản ghi)
LOCK TABLES `bienbankiemdinh` WRITE;
/*!40000 ALTER TABLE `bienbankiemdinh` DISABLE KEYS */;
INSERT INTO `bienbankiemdinh` (`maBienBan`, `loaiKiemDinh`, `maLo`, `ngayKiemDinh`, `nguoiKiemDinh`, `tieuChiDanhGia`, `ketQuaTongThe`, `lyDoKhongDat`, `ghiChu`, `trangThaiBienBan`) VALUES
('BBKD_NL_20261001_01', 1, 'LONL_20261001_001', '2026-09-02 08:30:00', 'NV001', 'Độ ẩm 13.2%, gluten 10.5%, màu trắng ngà tự nhiên, không mối mọt', 'Đạt', NULL, 'Lô đạt chuẩn nhập kho sản xuất', 'Đã xác nhận'),
('BBKD_NL_20261005_02', 1, 'LONL_20261005_003', '2026-09-16 09:00:00', 'NV001', 'Hàm lượng chất béo bơ 82.5%, nhiệt độ thùng xe lạnh -2°C, mùi bơ thơm tự nhiên', 'Đạt', NULL, 'Đạt tiêu chuẩn bảo quản kho lạnh', 'Đã xác nhận'),
('BBKD_NL_20261020_03', 1, 'LONL_20261020_010', '2026-10-16 10:15:00', 'NV001', 'Phát hiện rách 4 bao hạt hạnh nhân, có dấu hiệu ẩm mốc cục bộ vượt ngưỡng cho phép', 'Không đạt', 'Bao bì rách rưới, độ ẩm hạnh nhân 18.5% (vượt mức tiêu chuẩn ≤ 6.0%)', 'Chuyển kho cách ly KHO_LOI_01 chờ lập biên bản tiêu hủy', 'Đã xác nhận'),
('BBKD_TP_20261002_01', 2, 'LOTP_20261002_001', '2026-10-02 08:00:00', 'NV001', 'Bánh vàng giòn đều, vị bơ ngậy đặc trưng, bao thiếc hút chân không đạt chuẩn', 'Đạt', NULL, 'Đạt chuẩn xuất xưởng lưu kho', 'Đã xác nhận'),
('BBKD_TP_20261020_02', 2, 'LOTP_20261020_012', '2026-10-20 11:00:00', 'NV001', 'Bánh bị nướng quá lửa nhiệt độ 220°C, mặt bánh cháy xém đắng nghét', 'Không đạt', 'Lỗi cảm quan: 100% mẫu cháy đen mặt dưới, không đạt tiêu chuẩn an toàn thực phẩm', 'Khóa lô, lập đề nghị tiêu hủy', 'Đã xác nhận');
/*!40000 ALTER TABLE `bienbankiemdinh` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `bienbankiemke`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `bienbankiemke`;
CREATE TABLE `bienbankiemke` (
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
  CONSTRAINT `fk_bbkk_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`),
  CONSTRAINT `fk_bbkk_nv` FOREIGN KEY (`nguoiKiemKe`) REFERENCES `nhanvien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `bienbankiemke` (2 bản ghi)
LOCK TABLES `bienbankiemke` WRITE;
/*!40000 ALTER TABLE `bienbankiemke` DISABLE KEYS */;
INSERT INTO `bienbankiemke` (`maBienBan`, `tenDotKiemKe`, `loaiKiemKe`, `maKho`, `ngayKiemKe`, `nguoiKiemKe`, `trangThai`) VALUES
('BBKK_20261020_02', 'Kiểm kê đột xuất Kho Nguyên Liệu Khô 1', 'Kiểm kê nguyên liệu', 'KHO_NVL_01', '2026-10-20', 'NV002', 'Đã hoàn tất'),
('BBKK_20261025_01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 10/2026', 'Kiểm kê thành phẩm', 'KHO_TP_01', '2026-10-25', 'NV002', 'Đang đếm');
/*!40000 ALTER TABLE `bienbankiemke` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `bienbanthieuhang`
-- -------------------------------------------------------------
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

-- Dữ liệu bảng `bienbanthieuhang` (1 bản ghi)
LOCK TABLES `bienbanthieuhang` WRITE;
/*!40000 ALTER TABLE `bienbanthieuhang` DISABLE KEYS */;
INSERT INTO `bienbanthieuhang` (`maBienBan`, `maPXK`, `maLo`, `soLuongThieu`, `lyDo`, `ngayLap`) VALUES
('BBTH_20261020_01', 'PXK_TP_20261020_001', 'LOTP_20261012_007', '5.00', 'Tài xế giao nhận kiểm đếm thiếu 5 hộp ở cuối kiện hàng pallet, đại lý ký xác nhận', '2026-10-20 17:00:00');
/*!40000 ALTER TABLE `bienbanthieuhang` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `bienbanxulyhanghong`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `bienbanxulyhanghong`;
CREATE TABLE `bienbanxulyhanghong` (
  `maBienBan` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `loaiHang` varchar(50) NOT NULL,
  `soLuongHong` decimal(12,2) NOT NULL,
  `nguyenNhan` varchar(255) DEFAULT NULL,
  `bienPhapXuLy` varchar(255) DEFAULT NULL,
  `ngayLap` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maBienBan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `bienbanxulyhanghong` (2 bản ghi)
LOCK TABLES `bienbanxulyhanghong` WRITE;
/*!40000 ALTER TABLE `bienbanxulyhanghong` DISABLE KEYS */;
INSERT INTO `bienbanxulyhanghong` (`maBienBan`, `maLo`, `loaiHang`, `soLuongHong`, `nguyenNhan`, `bienPhapXuLy`, `ngayLap`) VALUES
('BBHH_20261018_02', 'LONL_20261020_010', 'Nguyên liệu', '100.00', 'Thùng container đường biển bị rò rỉ nước mưa làm ướt 4 bao bì hạt hạnh nhân', 'Đã lập biên bản bồi thường bảo hiểm vận chuyển, tiêu hủy theo quy định an toàn vệ sinh', '2026-10-18 11:20:00'),
('BBHH_20261020_01', 'LOTP_20261020_012', 'Thành phẩm', '120.00', 'Sự cố cảm biến nhiệt lò nướng số 2 của PX1 khiến nhiệt độ tăng đột biến 235°C', 'Chuyển sang bãi phế liệu nhà máy, chờ xe ép rác công nghiệp tiêu hủy', '2026-10-20 15:30:00');
/*!40000 ALTER TABLE `bienbanxulyhanghong` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `chitietdieuphoixuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietdieuphoixuat`;
CREATE TABLE `chitietdieuphoixuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maLenh` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc TP được chọn theo FEFO',
  `maViTri` varchar(20) NOT NULL,
  `soLuongPhanBo` decimal(12,2) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_ctdpx_lenh` (`maLenh`),
  KEY `fk_ctdpx_vitri` (`maViTri`),
  CONSTRAINT `fk_ctdpx_lenh` FOREIGN KEY (`maLenh`) REFERENCES `lenhdieuphoixuat` (`maLenh`) ON DELETE CASCADE,
  CONSTRAINT `fk_ctdpx_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `chitietdieuphoixuat` (5 bản ghi)
LOCK TABLES `chitietdieuphoixuat` WRITE;
/*!40000 ALTER TABLE `chitietdieuphoixuat` DISABLE KEYS */;
INSERT INTO `chitietdieuphoixuat` (`maChiTiet`, `maLenh`, `maLo`, `maViTri`, `soLuongPhanBo`) VALUES
('CTDPX_001', 'LDPX_NL_20261026_01', 'LONL_20261001_001', 'A1-K01-T01', '800.00'),
('CTDPX_002', 'LDPX_NL_20261026_01', 'LONL_20261010_002', 'A1-K01-T02', '50.00'),
('CTDPX_003', 'LDPX_TP_20261026_01', 'LOTP_20261002_001', 'B1-K01-T01', '450.00'),
('CTDPX_004', 'LDPX_TP_20261026_01', 'LOTP_20261010_002', 'B1-K01-T02', '150.00'),
('CTDPX_005', 'LDPX_TP_DONE_01', 'LOTP_20261012_007', 'B1-K02-T01', '500.00');
/*!40000 ALTER TABLE `chitietdieuphoixuat` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `chitietkiemke`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietkiemke`;
CREATE TABLE `chitietkiemke` (
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
  CONSTRAINT `fk_ctkk_bb` FOREIGN KEY (`maBienBan`) REFERENCES `bienbankiemke` (`maBienBan`) ON DELETE CASCADE,
  CONSTRAINT `fk_ctkk_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `chitietkiemke` (3 bản ghi)
LOCK TABLES `chitietkiemke` WRITE;
/*!40000 ALTER TABLE `chitietkiemke` DISABLE KEYS */;
INSERT INTO `chitietkiemke` (`maChiTiet`, `maBienBan`, `maLo`, `maViTri`, `soLuongTheoSoSach`, `soLuongTotThucTe`, `soLuongHongThucTe`, `soLuongChenhLech`, `tinhTrang`) VALUES
('CTKK_001', 'BBKK_20261025_01', 'LOTP_20261002_001', 'B1-K01-T01', '450.00', '450.00', '0.00', '0.00', 'Khớp sổ sách 100%, bảo quản tốt'),
('CTKK_002', 'BBKK_20261025_01', 'LOTP_20261010_002', 'B1-K01-T02', '500.00', '495.00', '5.00', '0.00', 'Phát hiện 5 hộp bị móp góc do va quẹt, số lượng khớp'),
('CTKK_003', 'BBKK_20261020_02', 'LONL_20261001_001', 'A1-K01-T01', '800.00', '796.00', '0.00', '-4.00', 'Hao hụt 4kg tự nhiên do nhiệt độ bay hơi bột mì');
/*!40000 ALTER TABLE `chitietkiemke` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `chitietyeucauxuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `chitietyeucauxuat`;
CREATE TABLE `chitietyeucauxuat` (
  `maChiTiet` varchar(30) NOT NULL,
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `maMatHang` varchar(20) NOT NULL COMMENT 'Lưu maNL hoặc maTP',
  `tenMatHang` varchar(150) DEFAULT NULL,
  `soLuongYeuCau` decimal(12,2) NOT NULL,
  `donViTinh` varchar(20) NOT NULL,
  PRIMARY KEY (`maChiTiet`),
  KEY `fk_ctycx_pycx` (`maPhieuYeuCau`),
  CONSTRAINT `fk_ctycx_pycx` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `phieuyeucauxuat` (`maPhieuYeuCau`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `chitietyeucauxuat` (8 bản ghi)
LOCK TABLES `chitietyeucauxuat` WRITE;
/*!40000 ALTER TABLE `chitietyeucauxuat` DISABLE KEYS */;
INSERT INTO `chitietyeucauxuat` (`maChiTiet`, `maPhieuYeuCau`, `maMatHang`, `tenMatHang`, `soLuongYeuCau`, `donViTinh`) VALUES
('CTYCX_001', 'PYCX_NL_20261026_001', 'NL001', 'Bột mì đa dụng thượng hạng số 8', '850.00', 'Kg'),
('CTYCX_002', 'PYCX_NL_20261026_001', 'NL002', 'Bơ lạt nguyên chất Anchor New Zealand', '100.00', 'Kg'),
('CTYCX_003', 'PYCX_NL_20261026_002', 'NL003', 'Đường tinh luyện cao cấp Biên Hòa', '500.00', 'Kg'),
('CTYCX_004', 'PYCX_NL_20261026_002', 'NL004', 'Sữa bột nguyên kem Fonterra 25kg', '300.00', 'Kg'),
('CTYCX_005', 'PYCX_TP_20261026_001', 'TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', '600.00', 'Hộp'),
('CTYCX_006', 'PYCX_TP_20261026_002', 'TP002', 'Bánh quy kẹp kem Socola Oreo 133g', '500.00', 'Hộp'),
('CTYCX_007', 'PYCX_TP_20261026_002', 'TP003', 'Bánh quy AFC lúa mì dinh dưỡng 172g', '350.00', 'Hộp'),
('CTYCX_008', 'PYCX_TP_20261020_003', 'TP004', 'Bánh quy bơ sữa Cosy Marie 336g', '500.00', 'Hộp');
/*!40000 ALTER TABLE `chitietyeucauxuat` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `denghitieuhuy`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `denghitieuhuy`;
CREATE TABLE `denghitieuhuy` (
  `maDeNghi` varchar(30) NOT NULL,
  `maLo` varchar(30) NOT NULL,
  `loaiHang` varchar(50) NOT NULL,
  `soLuong` decimal(12,2) NOT NULL,
  `lyDo` varchar(255) NOT NULL,
  `nguoiDeNghi` varchar(100) NOT NULL,
  `ngayDeNghi` date NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Ch??? gi??m ?????c duy???t',
  PRIMARY KEY (`maDeNghi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `denghitieuhuy` (2 bản ghi)
LOCK TABLES `denghitieuhuy` WRITE;
/*!40000 ALTER TABLE `denghitieuhuy` DISABLE KEYS */;
INSERT INTO `denghitieuhuy` (`maDeNghi`, `maLo`, `loaiHang`, `soLuong`, `lyDo`, `nguoiDeNghi`, `ngayDeNghi`, `trangThai`) VALUES
('DNTH_20261018_02', 'LONL_20261020_010', 'Nguyên liệu', '100.00', 'Hạt hạnh nhân nhập khẩu rách bao bị ẩm mốc, QC đánh giá không an toàn thực phẩm', 'Trần Thanh Toàn', '2026-10-18', 'Đã phê duyệt'),
('DNTH_20261020_01', 'LOTP_20261020_012', 'Thành phẩm', '120.00', 'Lô bánh quy Matcha bị cháy khét toàn bộ do sự cố lò nướng PX1, không thể tái chế', 'Trần Bích Thuận', '2026-10-21', 'Chờ giám đốc duyệt');
/*!40000 ALTER TABLE `denghitieuhuy` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `dotkiemke`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `dotkiemke`;
CREATE TABLE `dotkiemke` (
  `maDotKiemKe` varchar(30) NOT NULL,
  `tenDotKiemKe` varchar(150) NOT NULL,
  `ngayBatDau` date NOT NULL,
  `ngayKetThuc` date NOT NULL,
  `maKho` varchar(20) NOT NULL,
  `trangThai` varchar(50) DEFAULT '??ang th???c hi???n',
  PRIMARY KEY (`maDotKiemKe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `dotkiemke` (2 bản ghi)
LOCK TABLES `dotkiemke` WRITE;
/*!40000 ALTER TABLE `dotkiemke` DISABLE KEYS */;
INSERT INTO `dotkiemke` (`maDotKiemKe`, `tenDotKiemKe`, `ngayBatDau`, `ngayKetThuc`, `maKho`, `trangThai`) VALUES
('KK_2026_T10_01', 'Kiểm kê định kỳ Kho Thành Phẩm Tháng 10/2026', '2026-10-25', '2026-10-26', 'KHO_TP_01', 'Đang thực hiện'),
('KK_2026_T10_02', 'Kiểm kê đột xuất Kho Nguyên Liệu Khô 1', '2026-10-20', '2026-10-21', 'KHO_NVL_01', 'Đã hoàn tất');
/*!40000 ALTER TABLE `dotkiemke` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `kehoachsanxuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `kehoachsanxuat`;
CREATE TABLE `kehoachsanxuat` (
  `maKeHoach` varchar(20) NOT NULL,
  `maPhanXuong` varchar(20) NOT NULL,
  `ngayBatDau` date NOT NULL,
  `ngayKetThuc` date NOT NULL,
  `trangThai` varchar(50) DEFAULT '??ang s???n xu???t',
  PRIMARY KEY (`maKeHoach`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `kehoachsanxuat` (2 bản ghi)
LOCK TABLES `kehoachsanxuat` WRITE;
/*!40000 ALTER TABLE `kehoachsanxuat` DISABLE KEYS */;
INSERT INTO `kehoachsanxuat` (`maKeHoach`, `maPhanXuong`, `ngayBatDau`, `ngayKetThuc`, `trangThai`) VALUES
('KHSX-2026-Q4-01', 'PX-BANH-01', '2026-10-01', '2026-10-31', '??ang s???n xu???t'),
('KHSX-2026-Q4-02', 'PX-BANH-02', '2026-10-15', '2026-11-15', 'L???p k??? ho???ch');
/*!40000 ALTER TABLE `kehoachsanxuat` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `kho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `kho`;
CREATE TABLE `kho` (
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

-- Dữ liệu bảng `kho` (5 bản ghi)
LOCK TABLES `kho` WRITE;
/*!40000 ALTER TABLE `kho` DISABLE KEYS */;
INSERT INTO `kho` (`maKho`, `tenKho`, `loaiKho`, `dieuKienBaoQuan`, `diaChi`, `sucChuaToiDa`, `sucChuaHienTai`, `trangThai`) VALUES
('KHO_LOI_01', 'Kho Hàng Lỗi & Cách Ly', 'Kho Hàng lỗi', 'Kho khô', 'Khu C - Nhà máy Bánh Quy', 500, 0, 'Hoạt động'),
('KHO_NVL_01', 'Kho Nguyên Liệu Khô 1', 'Kho NVL', 'Kho khô', 'Khu A - Nhà máy Bánh Quy', 2000, 500, 'Hoạt động'),
('KHO_NVL_02', 'Kho Nguyên Liệu Lạnh 2', 'Kho NVL', 'Kho lạnh', 'Khu A - Nhà máy Bánh Quy', 1000, 200, 'Hoạt động'),
('KHO_TP_01', 'Kho Thành Phẩm Bánh Quy 1', 'Kho Thành phẩm', 'Kho khô', 'Khu B - Nhà máy Bánh Quy', 3000, 600, 'Hoạt động'),
('KHO-TP01', 'Kho Thành Phẩm 1 (KHO-TP01)', 'Kho Thành phẩm', 'Kho mát', 'Khu B - Nhà máy Bánh Quy', 3000, 900, 'Hoạt động');
/*!40000 ALTER TABLE `kho` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `lenhdieuphoinhap`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lenhdieuphoinhap`;
CREATE TABLE `lenhdieuphoinhap` (
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
  CONSTRAINT `fk_ldpn_kho` FOREIGN KEY (`maKhoTiepNhan`) REFERENCES `kho` (`maKho`),
  CONSTRAINT `fk_ldpn_nv` FOREIGN KEY (`nguoiDieuPhoi`) REFERENCES `nhanvien` (`maNV`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `lenhdieuphoinhap` (8 bản ghi)
LOCK TABLES `lenhdieuphoinhap` WRITE;
/*!40000 ALTER TABLE `lenhdieuphoinhap` DISABLE KEYS */;
INSERT INTO `lenhdieuphoinhap` (`maLenhDieuPhoi`, `loaiHang`, `maLo`, `maKhoTiepNhan`, `soLuongPhanBo`, `ngayDieuPhoi`, `nguoiDieuPhoi`, `lyDoGiaiTrinh`, `trangThai`) VALUES
('LDPN_NL_001', 'NL', 'LONL_20261022_006', 'KHO_NVL_01', '520.00', '2026-10-26 21:09:16', 'NV004', 'Nhập sữa bột Fonterra từ NCC', 'Chờ nhập kho'),
('LDPN_NL_002', 'NL', 'LONL_20261023_007', 'KHO_NVL_01', '180.00', '2026-10-26 21:09:16', 'NV003', 'Nhập bột nở Alsa từ NCC', 'Chờ tiếp nhận'),
('LDPN_TP_001', 'TP', 'LOTP_20261024_008', 'KHO_TP_01', '250.00', '2026-10-26 21:09:16', 'NV003', 'Điều phối tiếp nhận bánh Danisa ca sáng từ PX1 vào Kho TP 1', 'Chờ nhập kho'),
('LDPN_TP_002', 'TP', 'LOTP_20261025_009', 'KHO_TP_01', '300.00', '2026-10-26 21:09:16', 'NV003', 'Điều phối tiếp nhận bánh Oreo ca chiều từ PX2 vào Kho TP 1', 'Chờ tiếp nhận'),
('LDPN_TP_003', 'TP', 'LOTP_20261026_010', 'KHO_TP_01', '400.00', '2026-10-26 21:09:16', 'NV004', 'Điều phối tiếp nhận bánh Goute hộp thiếc vào Kho TP 1', 'Chờ nhập kho'),
('LDPN_TP_DONE_01', 'TP', 'LOTP_20261002_001', 'KHO_TP_01', '450.00', '2026-10-26 21:09:16', 'NV003', 'Nhập bánh Danisa lô 1', 'Hoàn tất nhập kho'),
('LDPN_TP_DONE_02', 'TP', 'LOTP_20261010_002', 'KHO_TP_01', '500.00', '2026-10-26 21:09:16', 'NV004', 'Nhập bánh Danisa lô 2', 'Hoàn tất nhập kho'),
('LDPN_TP_LECH_01', 'TP', 'LOTP_20261005_004', 'KHO_TP_01', '930.00', '2026-10-26 21:09:16', 'NV003', 'Nhập bánh Oreo lô 4', 'Hoàn tất nhập kho - Hao hụt');
/*!40000 ALTER TABLE `lenhdieuphoinhap` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `lenhdieuphoixuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lenhdieuphoixuat`;
CREATE TABLE `lenhdieuphoixuat` (
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
  CONSTRAINT `fk_ldpx_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`),
  CONSTRAINT `fk_ldpx_nv` FOREIGN KEY (`nguoiDieuPhoi`) REFERENCES `nhanvien` (`maNV`),
  CONSTRAINT `fk_ldpx_pycx` FOREIGN KEY (`maPhieuYeuCau`) REFERENCES `phieuyeucauxuat` (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `lenhdieuphoixuat` (3 bản ghi)
LOCK TABLES `lenhdieuphoixuat` WRITE;
/*!40000 ALTER TABLE `lenhdieuphoixuat` DISABLE KEYS */;
INSERT INTO `lenhdieuphoixuat` (`maLenh`, `maPhieuYeuCau`, `maKho`, `ngayDieuPhoi`, `nguoiDieuPhoi`, `ghiChu`, `trangThai`) VALUES
('LDPX_NL_20261026_01', 'PYCX_NL_20261026_001', 'KHO_NVL_01', '2026-10-26 10:00:00', 'NV004', 'Thuật toán FEFO: Lấy 800kg lô cận hạn LONL_20261001_001 + 50kg lô mới LONL_20261010_002', 'Chờ xuất'),
('LDPX_TP_20261026_01', 'PYCX_TP_20261026_001', 'KHO_TP_01', '2026-10-26 11:30:00', 'NV003', 'Thuật toán FEFO: Lấy 450 hộp lô hết hạn sớm LOTP_20261002_001 + 150 hộp lô LOTP_20261010_002', 'Chờ xuất'),
('LDPX_TP_DONE_01', 'PYCX_TP_20261020_003', 'KHO_TP_01', '2026-10-20 15:00:00', 'NV003', 'Đã xuất kho giao xe tải thành công', 'Đã xuất');
/*!40000 ALTER TABLE `lenhdieuphoixuat` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `longuyenlieu`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `longuyenlieu`;
CREATE TABLE `longuyenlieu` (
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
  CONSTRAINT `fk_lonl_nl` FOREIGN KEY (`maNL`) REFERENCES `nguyenlieu` (`maNL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `longuyenlieu` (10 bản ghi)
LOCK TABLES `longuyenlieu` WRITE;
/*!40000 ALTER TABLE `longuyenlieu` DISABLE KEYS */;
INSERT INTO `longuyenlieu` (`maLoNL`, `maNL`, `nhaCungCap`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
('LONL_20261001_001', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-09-01', '2026-11-15', '800.00', '800.00', 'Đạt', 'Đã lưu kho'),
('LONL_20261005_003', 'NL002', 'Fonterra Brands Vietnam', '2026-09-15', '2026-12-15', '300.00', '300.00', 'Đạt', 'Đã lưu kho'),
('LONL_20261008_005', 'NL003', 'Công ty CP Đường Biên Hòa', '2026-09-20', '2027-09-20', '1400.00', '1400.00', 'Đạt', 'Đã lưu kho'),
('LONL_20261010_002', 'NL001', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-01', '2027-04-01', '1000.00', '1000.00', 'Đạt', 'Đã lưu kho'),
('LONL_20261012_004', 'NL002', 'Fonterra Brands Vietnam', '2026-10-05', '2027-05-05', '350.00', '350.00', 'Đạt', 'Đã lưu kho'),
('LONL_20261020_010', 'NL009', 'Công ty CP Xay Lúa Mì Việt Nam', '2026-10-15', '2027-04-15', '100.00', '0.00', 'Không đạt', 'Khóa'),
('LONL_20261022_006', 'NL004', 'Fonterra Brands Vietnam', '2026-10-18', '2027-10-18', '520.00', '0.00', 'Đạt', 'Chờ nhập kho'),
('LONL_20261023_007', 'NL006', 'Công ty Tân Nhất Hương', '2026-10-20', '2027-10-20', '180.00', '0.00', 'Đạt', 'Chờ nhập kho'),
('LONL_20261024_008', 'NL007', 'Công ty Tân Nhất Hương', '2026-10-22', '2027-08-22', '320.00', '0.00', 'Chờ kiểm định', 'Chờ điều phối'),
('LONL_20261025_009', 'NL005', 'Công ty TNHH Ba Huân', '2026-10-24', '2026-11-24', '5500.00', '0.00', 'Chờ kiểm định', 'Chờ điều phối');
/*!40000 ALTER TABLE `longuyenlieu` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `lothanhpham`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `lothanhpham`;
CREATE TABLE `lothanhpham` (
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
  CONSTRAINT `fk_lotp_tp` FOREIGN KEY (`maTP`) REFERENCES `thanhpham` (`maTP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `lothanhpham` (12 bản ghi)
LOCK TABLES `lothanhpham` WRITE;
/*!40000 ALTER TABLE `lothanhpham` DISABLE KEYS */;
INSERT INTO `lothanhpham` (`maLoTP`, `maTP`, `phanXuong`, `ngaySanXuat`, `hanSuDung`, `soLuongLo`, `soLuongTon`, `trangThaiKiemDinh`, `trangThai`) VALUES
('LOTP_20261002_001', 'TP001', 'Phân xưởng nướng số 1', '2026-10-02', '2026-11-20', '450.00', '450.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261005_004', 'TP002', 'Phân xưởng nướng số 2', '2026-10-05', '2026-12-05', '920.00', '920.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261008_006', 'TP003', 'Phân xưởng nướng số 1', '2026-10-08', '2027-05-08', '980.00', '980.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261010_002', 'TP001', 'Phân xưởng nướng số 1', '2026-10-10', '2027-03-10', '500.00', '500.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261012_007', 'TP004', 'Phân xưởng nướng số 1', '2026-10-12', '2027-07-12', '1200.00', '1200.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261015_005', 'TP002', 'Phân xưởng nướng số 2', '2026-10-15', '2027-06-15', '1000.00', '1000.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261018_003', 'TP001', 'Phân xưởng nướng số 2', '2026-10-18', '2027-08-18', '500.00', '500.00', 'Đạt', 'Đã lưu kho'),
('LOTP_20261020_012', 'TP007', 'Phân xưởng nướng số 1', '2026-10-20', '2027-01-20', '120.00', '0.00', 'Không đạt', 'Khóa'),
('LOTP_20261024_008', 'TP001', 'Phân xưởng nướng số 1', '2026-10-24', '2027-10-24', '250.00', '0.00', 'Đạt', 'Chờ nhập kho'),
('LOTP_20261025_009', 'TP002', 'Phân xưởng nướng số 2', '2026-10-25', '2027-07-25', '300.00', '0.00', 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_010', 'TP005', 'Phân xưởng nướng số 1', '2026-10-26', '2027-06-26', '400.00', '0.00', 'Đạt', 'Chờ nhập kho'),
('LOTP_20261026_011', 'TP006', 'Phân xưởng nướng số 2', '2026-10-26', '2027-08-26', '350.00', '0.00', 'Chờ kiểm định', 'Chờ điều phối');
/*!40000 ALTER TABLE `lothanhpham` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `nguyenlieu`
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

-- Dữ liệu bảng `nguyenlieu` (10 bản ghi)
LOCK TABLES `nguyenlieu` WRITE;
/*!40000 ALTER TABLE `nguyenlieu` DISABLE KEYS */;
INSERT INTO `nguyenlieu` (`maNL`, `tenNL`, `donViTinh`, `loaiKhoBaoQuan`, `dinhMucToiThieu`, `soLuongTon`, `trangThai`) VALUES
('NL001', 'Bột mì đa dụng thượng hạng số 8', 'Kg', 'Kho khô', '300.00', '1800.00', 'Hoạt động'),
('NL002', 'Bơ lạt nguyên chất Anchor New Zealand', 'Kg', 'Kho lạnh', '100.00', '650.00', 'Hoạt động'),
('NL003', 'Đường tinh luyện cao cấp Biên Hòa', 'Kg', 'Kho khô', '200.00', '1400.00', 'Hoạt động'),
('NL004', 'Sữa bột nguyên kem Fonterra 25kg', 'Kg', 'Kho khô', '80.00', '520.00', 'Hoạt động'),
('NL005', 'Trứng gà tươi công nghiệp Ba Huân', 'Quả', 'Kho lạnh', '1000.00', '5500.00', 'Hoạt động'),
('NL006', 'Bột nở thực phẩm Baking Powder Alsa', 'Kg', 'Kho khô', '30.00', '180.00', 'Hoạt động'),
('NL007', 'Socola chip đen nguyên chất Hershey', 'Kg', 'Kho lạnh', '50.00', '320.00', 'Hoạt động'),
('NL008', 'Hương Vani Pháp tự nhiên Rayner', 'Lít', 'Kho lạnh', '20.00', '95.00', 'Hoạt động'),
('NL009', 'Hạt hạnh nhân sấy lát mỏng Blue Diamond', 'Kg', 'Kho khô', '40.00', '240.00', 'Hoạt động'),
('NL010', 'Mè vàng thơm tách vỏ xuất khẩu', 'Kg', 'Kho khô', '30.00', '160.00', 'Hoạt động');
/*!40000 ALTER TABLE `nguyenlieu` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `nhacungcap`
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

-- Dữ liệu bảng `nhacungcap` (5 bản ghi)
LOCK TABLES `nhacungcap` WRITE;
/*!40000 ALTER TABLE `nhacungcap` DISABLE KEYS */;
INSERT INTO `nhacungcap` (`maNCC`, `tenNCC`, `soDienThoai`, `diaChi`, `email`) VALUES
('NCC01', 'Công ty CP Xay Lúa Mì Việt Nam (Flour Mill)', '02838123456', 'KCN Tân Bình, Tân Phú, TP.HCM', 'sales@luami.vn'),
('NCC02', 'Fonterra Brands Vietnam (Anchor & Bơ sữa)', '02839988776', 'Landmark 81, Bình Thạnh, TP.HCM', 'contact@fonterra.com'),
('NCC03', 'Công ty CP Đường Biên Hòa (TTC Sugar)', '02513836171', 'KCN Biên Hòa 1, Đồng Nai', 'info@bhsugar.com'),
('NCC04', 'Công ty TNHH Ba Huân (Trứng gà sạch)', '02837550123', 'Bình Chánh, TP.HCM', 'kinhdoanh@bahuan.vn'),
('NCC05', 'Công ty Nguyên Liệu Thực Phẩm Tân Nhất Hương', '02838120099', 'Tân Phú, TP.HCM', 'support@tannhhathuo.com');
/*!40000 ALTER TABLE `nhacungcap` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `nhanvien`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nhanvien`;
CREATE TABLE `nhanvien` (
  `maNV` varchar(20) NOT NULL,
  `hoTen` varchar(100) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `vaiTro` varchar(50) NOT NULL COMMENT 'QuanLyKho, NhanVienKho, QC, KiemKe, BanGiamDoc, Admin',
  `soDienThoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `trangThai` varchar(50) DEFAULT 'Hoạt động',
  `maVaiTro` varchar(20) DEFAULT 'VT03',
  PRIMARY KEY (`maNV`),
  UNIQUE KEY `tenDangNhap` (`tenDangNhap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `nhanvien` (12 bản ghi)
LOCK TABLES `nhanvien` WRITE;
/*!40000 ALTER TABLE `nhanvien` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `nhanvien` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `nhomthanhpham`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `nhomthanhpham`;
CREATE TABLE `nhomthanhpham` (
  `maNhom` varchar(20) NOT NULL,
  `tenNhom` varchar(100) NOT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`maNhom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `nhomthanhpham` (3 bản ghi)
LOCK TABLES `nhomthanhpham` WRITE;
/*!40000 ALTER TABLE `nhomthanhpham` DISABLE KEYS */;
INSERT INTO `nhomthanhpham` (`maNhom`, `tenNhom`, `moTa`) VALUES
('NTP01', 'B??nh quy b?? cao c???p', 'D??ng b??nh quy n?????ng h????ng b?? truy???n th???ng h???p thi???c sang tr???ng'),
('NTP02', 'B??nh quy k???p kem', 'B??nh quy gi??n nh??n kem vani v?? socola ng???t d???u'),
('NTP03', 'B??nh quy dinh d?????ng h???t', 'B??nh quy ng?? c???c y???n m???ch t???t cho s???c kh???e');
/*!40000 ALTER TABLE `nhomthanhpham` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `phieunhapkho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieunhapkho`;
CREATE TABLE `phieunhapkho` (
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
  CONSTRAINT `fk_pnk_lenh` FOREIGN KEY (`maLenhDieuPhoi`) REFERENCES `lenhdieuphoinhap` (`maLenhDieuPhoi`),
  CONSTRAINT `fk_pnk_nv` FOREIGN KEY (`nguoiLap`) REFERENCES `nhanvien` (`maNV`),
  CONSTRAINT `fk_pnk_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `phieunhapkho` (3 bản ghi)
LOCK TABLES `phieunhapkho` WRITE;
/*!40000 ALTER TABLE `phieunhapkho` DISABLE KEYS */;
INSERT INTO `phieunhapkho` (`maPNK`, `maLenhDieuPhoi`, `maViTri`, `ngayNhapThucTe`, `nguoiLap`, `slThucNhap`, `soLuongChenhLech`, `lyDoChenhLech`, `ghiChu`, `trangThai`) VALUES
('PNK_TP_20261002_01', 'LDPN_TP_DONE_01', 'B1-K01-T01', '2026-10-02 09:30:00', 'NV006', '450.00', '0.00', NULL, 'Bao bì nguyên vẹn, nhập kho đúng quy cách', 'Hoàn tất'),
('PNK_TP_20261005_03', 'LDPN_TP_LECH_01', 'B1-K03-T01', '2026-10-05 16:45:00', 'NV006', '920.00', '10.00', 'Bị rách 10 thùng carton do va chạm với xe nâng trong lúc bốc dỡ', 'Đã lập biên bản kiểm định sự cố', 'Hoàn tất'),
('PNK_TP_20261010_02', 'LDPN_TP_DONE_02', 'B1-K01-T02', '2026-10-10 14:15:00', 'NV006', '500.00', '0.00', NULL, 'Chất lượng đồng đều, xếp đúng tầng 2', 'Hoàn tất');
/*!40000 ALTER TABLE `phieunhapkho` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `phieuxuatkho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuxuatkho`;
CREATE TABLE `phieuxuatkho` (
  `maPXK` varchar(30) NOT NULL,
  `maLenhDieuPhoi` varchar(30) NOT NULL,
  `ngayXuatThucTe` datetime DEFAULT current_timestamp(),
  `nguoiXuat` varchar(100) NOT NULL,
  `ghiChu` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`maPXK`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `phieuxuatkho` (1 bản ghi)
LOCK TABLES `phieuxuatkho` WRITE;
/*!40000 ALTER TABLE `phieuxuatkho` DISABLE KEYS */;
INSERT INTO `phieuxuatkho` (`maPXK`, `maLenhDieuPhoi`, `ngayXuatThucTe`, `nguoiXuat`, `ghiChu`) VALUES
('PXK_TP_20261020_001', 'LDPX_TP_DONE_01', '2026-10-20 16:30:00', 'Trần Văn Sang', 'Xuất đủ 500 hộp Cosy Marie cho đại lý Miền Đông xe 59C-123.45');
/*!40000 ALTER TABLE `phieuxuatkho` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `phieuyeucaunhap`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuyeucaunhap`;
CREATE TABLE `phieuyeucaunhap` (
  `maPhieuYeuCau` varchar(30) NOT NULL,
  `ngayYeuCau` date NOT NULL,
  `ngayGiaoDuKien` date DEFAULT NULL,
  `nguoiYeuCau` varchar(100) NOT NULL,
  `loaiYeuCau` varchar(50) DEFAULT 'Nh???p nguy??n li???u t??? NCC',
  `trangThai` varchar(50) DEFAULT '???? ph?? duy???t',
  PRIMARY KEY (`maPhieuYeuCau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `phieuyeucaunhap` (3 bản ghi)
LOCK TABLES `phieuyeucaunhap` WRITE;
/*!40000 ALTER TABLE `phieuyeucaunhap` DISABLE KEYS */;
INSERT INTO `phieuyeucaunhap` (`maPhieuYeuCau`, `ngayYeuCau`, `ngayGiaoDuKien`, `nguoiYeuCau`, `loaiYeuCau`, `trangThai`) VALUES
('PYCN-2026-101', '2026-10-22', '2026-10-25', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Đã tiếp nhận'),
('PYCN-2026-102', '2026-10-23', '2026-10-26', 'Trần Văn Sang', 'Nhập nguyên liệu từ NCC', 'Đã tiếp nhận'),
('PYCN-2026-103', '2026-10-25', '2026-10-28', 'Nguyễn Hữu Lộc', 'Nhập thành phẩm từ phân xưởng', 'Chờ kiểm định');
/*!40000 ALTER TABLE `phieuyeucaunhap` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `phieuyeucauxuat`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `phieuyeucauxuat`;
CREATE TABLE `phieuyeucauxuat` (
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

-- Dữ liệu bảng `phieuyeucauxuat` (5 bản ghi)
LOCK TABLES `phieuyeucauxuat` WRITE;
/*!40000 ALTER TABLE `phieuyeucauxuat` DISABLE KEYS */;
INSERT INTO `phieuyeucauxuat` (`maPhieuYeuCau`, `loaiYeuCau`, `donViYeuCau`, `ngayYeuCau`, `nguoiYeuCau`, `lyDoXuat`, `tongSoLuong`, `trangThai`) VALUES
('PYCX_NL_20261026_001', 'NL', 'Phân xưởng nướng số 1', '2026-10-26', 'Trưởng ca Nguyễn Văn Hùng', 'Cấp bột mì và bơ lạt làm bánh quy Danisa ca tối', '950.00', 'Chờ xử lý'),
('PYCX_NL_20261026_002', 'NL', 'Phân xưởng nướng số 2', '2026-10-26', 'Trưởng ca Trần Văn Thắng', 'Cấp đường tinh luyện và sữa bột làm bánh Oreo', '800.00', 'Chờ xử lý'),
('PYCX_TP_20261020_003', 'TP', 'Đại lý Bánh kẹo Miền Đông', '2026-10-20', 'Nguyễn Thị Thu', 'Xuất bánh Cosy Marie theo đơn đặt hàng', '500.00', 'Hoàn tất'),
('PYCX_TP_20261026_001', 'TP', 'Hệ thống Siêu thị Co.opmart Toàn quốc', '2026-10-26', 'Phòng Kinh Doanh Kênh MT', 'Xuất giao hợp đồng bánh Danisa Hoàng Gia phục vụ Tết', '600.00', 'Chờ xử lý'),
('PYCX_TP_20261026_002', 'TP', 'Hệ thống Đại siêu thị Big C / GO!', '2026-10-26', 'Phòng Kinh Doanh Kênh MT', 'Xuất giao bánh quy Oreo và bánh AFC', '850.00', 'Chờ xử lý');
/*!40000 ALTER TABLE `phieuyeucauxuat` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `taikhoan`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `taikhoan`;
CREATE TABLE `taikhoan` (
  `maTaiKhoan` varchar(20) NOT NULL,
  `maNV` varchar(20) NOT NULL,
  `maVaiTro` varchar(20) NOT NULL,
  `tenDangNhap` varchar(50) NOT NULL,
  `matKhau` varchar(255) NOT NULL,
  `trangThai` varchar(50) DEFAULT 'Ho???t ?????ng',
  `ngayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`maTaiKhoan`),
  UNIQUE KEY `maNV` (`maNV`),
  UNIQUE KEY `tenDangNhap` (`tenDangNhap`),
  KEY `fk_taikhoan_vaitro` (`maVaiTro`),
  CONSTRAINT `fk_taikhoan_nhanvien` FOREIGN KEY (`maNV`) REFERENCES `nhanvien` (`maNV`) ON UPDATE CASCADE,
  CONSTRAINT `fk_taikhoan_vaitro` FOREIGN KEY (`maVaiTro`) REFERENCES `vaitro` (`maVaiTro`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `taikhoan` (12 bản ghi)
LOCK TABLES `taikhoan` WRITE;
/*!40000 ALTER TABLE `taikhoan` DISABLE KEYS */;
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
/*!40000 ALTER TABLE `taikhoan` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `thanhpham`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `thanhpham`;
CREATE TABLE `thanhpham` (
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

-- Dữ liệu bảng `thanhpham` (8 bản ghi)
LOCK TABLES `thanhpham` WRITE;
/*!40000 ALTER TABLE `thanhpham` DISABLE KEYS */;
INSERT INTO `thanhpham` (`maTP`, `tenTP`, `tenNhom`, `donViTinh`, `quyCachDongGoi`, `soLuongToiThieu`, `soLuongToiDa`, `soLuongTon`, `trangThai`) VALUES
('TP001', 'Bánh quy bơ Danisa Hoàng Gia 200g', 'Bánh quy bơ cao cấp', 'Hộp', '24 hộp/thùng', 150, 3000, '1450.00', 'Hoạt động'),
('TP002', 'Bánh quy kẹp kem Socola Oreo 133g', 'Bánh quy kẹp kem', 'Hộp', '36 hộp/thùng', 200, 3500, '1920.00', 'Hoạt động'),
('TP003', 'Bánh quy AFC lúa mì dinh dưỡng 172g', 'Bánh quy dinh dưỡng hạt', 'Hộp', '24 hộp/thùng', 100, 2500, '980.00', 'Hoạt động'),
('TP004', 'Bánh quy bơ sữa Cosy Marie 336g', 'Bánh quy bơ cao cấp', 'Hộp', '20 hộp/thùng', 150, 2500, '1200.00', 'Hoạt động'),
('TP005', 'Bánh quy mè giòn Goute hộp thiếc 288g', 'Bánh quy dinh dưỡng hạt', 'Hộp', '16 hộp/thùng', 80, 2000, '750.00', 'Hoạt động'),
('TP006', 'Bánh quy Socola Chip Cookies 150g', 'Bánh quy kẹp kem', 'Hộp', '24 hộp/thùng', 100, 2000, '620.00', 'Hoạt động'),
('TP007', 'Bánh quy trà xanh Matcha LU Pháp 180g', 'Bánh quy bơ cao cấp', 'Hộp', '24 hộp/thùng', 80, 1500, '480.00', 'Hoạt động'),
('TP008', 'Bánh quy ngũ cốc yến mạch Fitnee 210g', 'Bánh quy dinh dưỡng hạt', 'Hộp', '24 hộp/thùng', 100, 2000, '830.00', 'Hoạt động');
/*!40000 ALTER TABLE `thanhpham` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `tieuchikiemdinh`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tieuchikiemdinh`;
CREATE TABLE `tieuchikiemdinh` (
  `maTieuChi` varchar(20) NOT NULL,
  `tenTieuChi` varchar(150) NOT NULL,
  `quyChuan` varchar(150) NOT NULL,
  `loaiHang` varchar(50) DEFAULT 'Nguy??n li???u',
  PRIMARY KEY (`maTieuChi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `tieuchikiemdinh` (5 bản ghi)
LOCK TABLES `tieuchikiemdinh` WRITE;
/*!40000 ALTER TABLE `tieuchikiemdinh` DISABLE KEYS */;
INSERT INTO `tieuchikiemdinh` (`maTieuChi`, `tenTieuChi`, `quyChuan`, `loaiHang`) VALUES
('TC-BM01', 'Độ ẩm bột tiêu chuẩn', '≤ 14.0 %', 'Nguyên liệu'),
('TC-BM02', 'Hàm lượng Protein (Gluten)', '9.5 % - 11.5 %', 'Nguyên liệu'),
('TC-BM03', 'Màu sắc, mùi vị & tạp chất', 'Trắng ngà, không ẩm mốc', 'Nguyên liệu'),
('TC-BO01', 'Hàm lượng chất béo bơ', '≥ 82.0 %', 'Nguyên liệu'),
('TC-BO02', 'Nhiệt độ bảo quản xe lạnh', '≤ 4.0 °C', 'Nguyên liệu');
/*!40000 ALTER TABLE `tieuchikiemdinh` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `tonkho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tonkho`;
CREATE TABLE `tonkho` (
  `maTonKho` varchar(20) NOT NULL,
  `loaiHang` varchar(20) NOT NULL DEFAULT 'NL' COMMENT 'NL (Nguyên liệu) hoặc TP (Thành phẩm)',
  `maLo` varchar(30) NOT NULL COMMENT 'Mã lô NL hoặc Mã lô TP',
  `maViTri` varchar(20) NOT NULL,
  `slTon` decimal(12,2) NOT NULL DEFAULT 0.00,
  `soLuongTamGiu` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Số lượng đang giữ cho lệnh điều phối chờ xuất',
  `ngayCapNhat` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trangThai` varchar(50) DEFAULT 'Khả dụng' COMMENT 'Khả dụng, Đang giữ, Khóa',
  PRIMARY KEY (`maTonKho`),
  UNIQUE KEY `uq_lo_vitri` (`maLo`,`maViTri`),
  KEY `fk_tonkho_vitri` (`maViTri`),
  CONSTRAINT `fk_tonkho_vitri` FOREIGN KEY (`maViTri`) REFERENCES `vitrikho` (`maViTri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `tonkho` (8 bản ghi)
LOCK TABLES `tonkho` WRITE;
/*!40000 ALTER TABLE `tonkho` DISABLE KEYS */;
INSERT INTO `tonkho` (`maTonKho`, `loaiHang`, `maLo`, `maViTri`, `slTon`, `soLuongTamGiu`, `ngayCapNhat`, `trangThai`) VALUES
('TK_NL_001', 'NL', 'LONL_20261001_001', 'A1-K01-T01', '800.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_NL_002', 'NL', 'LONL_20261010_002', 'A1-K01-T02', '1000.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_NL_003', 'NL', 'LONL_20261005_003', 'L1-K01-T01', '300.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_NL_004', 'NL', 'LONL_20261012_004', 'L1-K01-T02', '350.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_NL_005', 'NL', 'LONL_20261008_005', 'A1-K02-T01', '1400.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_TP_001', 'TP', 'LOTP_20261002_001', 'B1-K01-T01', '450.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_TP_002', 'TP', 'LOTP_20261010_002', 'B1-K01-T02', '500.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng'),
('TK_TP_003', 'TP', 'LOTP_20261005_004', 'B1-K03-T01', '600.00', '0.00', '2026-10-26 21:09:16', 'Khả dụng');
/*!40000 ALTER TABLE `tonkho` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `vaitro`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vaitro`;
CREATE TABLE `vaitro` (
  `maVaiTro` varchar(20) NOT NULL,
  `tenVaiTro` varchar(100) NOT NULL,
  `moTa` varchar(255) DEFAULT NULL,
  `ngayTao` date DEFAULT curdate(),
  PRIMARY KEY (`maVaiTro`),
  UNIQUE KEY `tenVaiTro` (`tenVaiTro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `vaitro` (6 bản ghi)
LOCK TABLES `vaitro` WRITE;
/*!40000 ALTER TABLE `vaitro` DISABLE KEYS */;
INSERT INTO `vaitro` (`maVaiTro`, `tenVaiTro`, `moTa`, `ngayTao`) VALUES
('VT01', 'Admin', 'Qu???n tr??? h??? th???ng: qu???n l?? t??i kho???n, vai tr??, danh m???c master data', '2026-10-26'),
('VT02', 'QuanLyKho', 'Ph?? duy???t y??u c???u nh???p/xu???t, ban h??nh l???nh ??i???u ph???i, xem b??o c??o t???n kho', '2026-10-26'),
('VT03', 'NhanVienKho', 'Th???c hi???n nh???p/xu???t th???c t???, l???p phi???u nh???p kho, phi???u xu???t kho, s???p x???p v??? tr?? k???', '2026-10-26'),
('VT05', 'KiemKe', 'Th???c hi???n ki???m ?????m th???c t???, l???p bi??n b???n ki???m k?? v?? ghi nh???n ch??nh l???ch', '2026-10-26'),
('VT06', 'QC', 'Ki???m ?????nh ch???t l?????ng l?? nguy??n li???u/th??nh ph???m, l???p bi??n b???n ki???m ?????nh', '2026-10-26'),
('VT07', 'BanGiamDoc', 'Xem b??o c??o th???ng k?? to??n h??? th???ng, ph?? duy???t ????? ngh??? ti??u h???y h??ng h???ng', '2026-10-26');
/*!40000 ALTER TABLE `vaitro` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Cấu trúc bảng `vitrikho`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vitrikho`;
CREATE TABLE `vitrikho` (
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
  CONSTRAINT `fk_vitri_kho` FOREIGN KEY (`maKho`) REFERENCES `kho` (`maKho`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu bảng `vitrikho` (20 bản ghi)
LOCK TABLES `vitrikho` WRITE;
/*!40000 ALTER TABLE `vitrikho` DISABLE KEYS */;
INSERT INTO `vitrikho` (`maViTri`, `maKho`, `day`, `ke`, `tang`, `sucChuaKhaDung`, `trangThaiSuDung`, `trangThaiChuaHang`) VALUES
('A1-K01-T01', 'KHO_NVL_01', 'A1', 'K01', 'T01', 500, 'Đang chứa', 'Khả dụng'),
('A1-K01-T02', 'KHO_NVL_01', 'A1', 'K01', 'T02', 500, 'Đang chứa', 'Khả dụng'),
('A1-K01-T03', 'KHO_NVL_01', 'A1', 'K01', 'T03', 500, 'Trống', 'Khả dụng'),
('A1-K02-T01', 'KHO_NVL_01', 'A1', 'K02', 'T01', 500, 'Đang chứa', 'Khả dụng'),
('A1-K02-T02', 'KHO_NVL_01', 'A1', 'K02', 'T02', 500, 'Trống', 'Khả dụng'),
('A1-K02-T03', 'KHO_NVL_01', 'A1', 'K02', 'T03', 500, 'Trống', 'Khả dụng'),
('B1-K01-T01', 'KHO_TP_01', 'B1', 'K01', 'T01', 500, 'Đang chứa', 'Khả dụng'),
('B1-K01-T02', 'KHO_TP_01', 'B1', 'K01', 'T02', 500, 'Đang chứa', 'Khả dụng'),
('B1-K01-T03', 'KHO_TP_01', 'B1', 'K01', 'T03', 500, 'Trống', 'Khả dụng'),
('B1-K02-T01', 'KHO_TP_01', 'B1', 'K02', 'T01', 500, 'Trống', 'Khả dụng'),
('B1-K02-T02', 'KHO_TP_01', 'B1', 'K02', 'T02', 500, 'Trống', 'Khả dụng'),
('B1-K02-T03', 'KHO_TP_01', 'B1', 'K02', 'T03', 500, 'Trống', 'Khả dụng'),
('B1-K03-T01', 'KHO_TP_01', 'B1', 'K03', 'T01', 600, 'Đầy', 'Khả dụng'),
('B1-K03-T02', 'KHO_TP_01', 'B1', 'K03', 'T02', 600, 'Trống', 'Khả dụng'),
('C1-K01-T01', 'KHO_LOI_01', 'C1', 'K01', 'T01', 300, 'Đang chứa', 'Khả dụng'),
('C1-K01-T02', 'KHO_LOI_01', 'C1', 'K01', 'T02', 300, 'Trống', 'Khả dụng'),
('L1-K01-T01', 'KHO_NVL_02', 'L1', 'K01', 'T01', 400, 'Đang chứa', 'Khả dụng'),
('L1-K01-T02', 'KHO_NVL_02', 'L1', 'K01', 'T02', 400, 'Đang chứa', 'Khả dụng'),
('L1-K02-T01', 'KHO_NVL_02', 'L1', 'K02', 'T01', 400, 'Trống', 'Khả dụng'),
('L1-K02-T02', 'KHO_NVL_02', 'L1', 'K02', 'T02', 400, 'Trống', 'Khả dụng');
/*!40000 ALTER TABLE `vitrikho` ENABLE KEYS */;
UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
