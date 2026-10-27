<?php
class mBienBanKiemKe {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getDanhSachDotTheoLoai($loaiHang = 'NL') {
        try {
            $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';
            if ($loaiHang === 'TP') {
                $sql = "SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maKho LIKE '%TP%' ORDER BY d.ngayBatDau DESC";
            } else {
                $sql = "SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maKho NOT LIKE '%TP%' ORDER BY d.ngayBatDau DESC";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn đợt kiểm kê: " . $e->getMessage());
        }
    }

    public function getDotKiemKeTheoMa($maDot) {
        try {
            $stmt = $this->conn->prepare("SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maDotKiemKe = :maDot LIMIT 1");
            $stmt->execute([':maDot' => $maDot]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi chi tiết đợt kiểm kê: " . $e->getMessage());
        }
    }

    public function getDanhSachKiemKeTheoKho($maKho, $loaiHang = 'NL') {
        try {
            $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';
            if ($loaiHang === 'NL') {
                $sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        nl.maNL AS maHang, nl.tenNL AS tenHang, nl.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang,
                        COALESCE(lnl.trangThai, 'Chờ kiểm đếm') AS trangThaiLo
                    FROM tonkho tk
                    INNER JOIN longuyenlieu lnl ON tk.maLo = lnl.maLoNL
                    INNER JOIN nguyenlieu nl ON lnl.maNL = nl.maNL
                    INNER JOIN vitrikho v ON tk.maViTri = v.maViTri
                    INNER JOIN kho k ON v.maKho = k.maKho
                    WHERE tk.loaiHang = 'NL' AND v.maKho = :maKho
                    ORDER BY tk.maLo ASC
                ";
            } else {
                $sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        tp.maTP AS maHang, tp.tenTP AS tenHang, tp.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang,
                        COALESCE(ltp.trangThai, 'Chờ kiểm đếm') AS trangThaiLo
                    FROM tonkho tk
                    INNER JOIN lothanhpham ltp ON tk.maLo = ltp.maLoTP
                    INNER JOIN thanhpham tp ON ltp.maTP = tp.maTP
                    INNER JOIN vitrikho v ON tk.maViTri = v.maViTri
                    INNER JOIN kho k ON v.maKho = k.maKho
                    WHERE tk.loaiHang = 'TP' AND v.maKho = :maKho
                    ORDER BY tk.maLo ASC
                ";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':maKho' => $maKho]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn mặt hàng kiểm kê: " . $e->getMessage());
        }
    }

    public function luuBienBanKiemKe($maDotKiemKe, $items, $maNV) {
        try {
            $this->conn->beginTransaction();

            $stmtDot = $this->conn->prepare("SELECT * FROM dotkiemke WHERE maDotKiemKe = ?");
            $stmtDot->execute([$maDotKiemKe]);
            $dot = $stmtDot->fetch(PDO::FETCH_ASSOC);
            $maKho = $dot['maKho'] ?? 'KHO_NVL_01';
            $tenDot = $dot['tenDotKiemKe'] ?? 'Kiểm kê định kỳ';
            $loaiKiemKe = (strpos($maKho, 'TP') !== false) ? 'Kiểm kê thành phẩm' : 'Kiểm kê nguyên liệu';

            $maBienBan = 'BBKK_' . date('Ymd') . '_' . rand(100, 999);

            $sqlBB = "INSERT INTO bienbankiemke (maBienBan, tenDotKiemKe, loaiKiemKe, maKho, ngayKiemKe, nguoiKiemKe, trangThai) 
                      VALUES (?, ?, ?, ?, CURDATE(), ?, 'Đã hoàn tất')";
            $stmtBB = $this->conn->prepare($sqlBB);
            $stmtBB->execute([$maBienBan, $tenDot, $loaiKiemKe, $maKho, $maNV]);

            $sqlCT = "INSERT INTO chitietkiemke (maChiTiet, maBienBan, maLo, maViTri, soLuongTheoSoSach, soLuongTotThucTe, soLuongHongThucTe, soLuongChenhLech, tinhTrang) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtCT = $this->conn->prepare($sqlCT);
            $index = 1;

            foreach ($items as $item) {
                // Sinh mã ngắn gọn, kết hợp số thứ tự đếm tăng dần để tuyệt đối không trùng và không bị tràn độ dài cột CSDL
                $maChiTiet = 'CTK_' . date('mdHis') . '_' . $index++;
                $maLo = $item['maLo'] ?? '';
                $maViTri = $item['maViTri'] ?? ''; 
                
                $tonSoSach = (float)($item['tonSoSach'] ?? 0);
                $slTot = (float)($item['slTot'] ?? 0);
                $slHong = (float)($item['slHong'] ?? 0);
                $chenhLech = ($slTot + $slHong) - $tonSoSach;
                
                if ($chenhLech === 0.0) {
                    $tinhTrang = 'Khớp 100%';
                } elseif ($chenhLech > 0) {
                    $tinhTrang = 'Thừa ' . $chenhLech;
                } else {
                    $tinhTrang = 'Hao hụt ' . abs($chenhLech);
                }

                $stmtCT->execute([
                    $maChiTiet, $maBienBan, $maLo, $maViTri,
                    $tonSoSach, $slTot, $slHong, $chenhLech, $tinhTrang
                ]);
            }

            if (!empty($maDotKiemKe)) {
                $stmtUpdDot = $this->conn->prepare("UPDATE dotkiemke SET trangThai = 'Đã hoàn tất' WHERE maDotKiemKe = ?");
                $stmtUpdDot->execute([$maDotKiemKe]);
            }

            $this->conn->commit();
            return [
                'success' => true, 
                'message' => "Lập biên bản kiểm kê {$maBienBan} thành công! Đã chuyển dữ liệu chờ phê duyệt.",
                'maBienBan' => $maBienBan
            ];
        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception('Lỗi CSDL: ' . $e->getMessage());
        }
    }
}