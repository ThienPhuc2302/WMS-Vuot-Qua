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

    public function getDanhSachKhoTheoLoai($loaiHang = 'NL') {
        try {
            $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';
            if ($loaiHang === 'TP') {
                $sql = "SELECT * FROM kho WHERE loaiKho LIKE '%thành phẩm%' OR loaiKho LIKE '%TP%' OR maKho LIKE '%TP%' ORDER BY maKho ASC";
            } else {
                $sql = "SELECT * FROM kho WHERE loaiKho LIKE '%nguyên liệu%' OR loaiKho LIKE '%NVL%' OR maKho LIKE '%NVL%' ORDER BY maKho ASC";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn danh sách kho: " . $e->getMessage());
        }
    }

    public function getDotKiemKeTheoMa($maDot) {
        try {
            $stmt = $this->conn->prepare("SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maDotKiemKe = :maDot LIMIT 1");
            $stmt->execute([':maDot' => $maDot]);
            $dot = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dot) {
                return null;
            }

            // Tìm biên bản kiểm kê tương ứng trong CSDL
            $stmtBB = $this->conn->prepare("
                SELECT * FROM bienbankiemke 
                WHERE maKho = :maKho AND (tenDotKiemKe = :tenDot OR tenDotKiemKe = :maDot)
                ORDER BY ngayKiemKe DESC, maBienBan DESC LIMIT 1
            ");
            $stmtBB->execute([
                ':maKho' => $dot['maKho'],
                ':tenDot' => $dot['tenDotKiemKe'],
                ':maDot' => $dot['maDotKiemKe']
            ]);
            $bb = $stmtBB->fetch(PDO::FETCH_ASSOC);
            if ($bb) {
                $dot['maBienBan'] = $bb['maBienBan'];
                $dot['trangThaiBienBan'] = $bb['trangThai'];
            } else {
                $dot['maBienBan'] = '';
                $dot['trangThaiBienBan'] = $dot['trangThai'] ?? 'Đang thực hiện';
            }

            return $dot;
        } catch (PDOException $e) {
            throw new Exception("Lỗi chi tiết đợt kiểm kê: " . $e->getMessage());
        }
    }

    public function getOrCreateBienBan($maDotKiemKe, $maNV = '') {
        $stmtDot = $this->conn->prepare("SELECT * FROM dotkiemke WHERE maDotKiemKe = ? LIMIT 1");
        $stmtDot->execute([$maDotKiemKe]);
        $dot = $stmtDot->fetch(PDO::FETCH_ASSOC);
        if (!$dot) {
            throw new Exception("Không tìm thấy đợt kiểm kê '{$maDotKiemKe}'!");
        }

        $maKho = $dot['maKho'];
        $tenDot = $dot['tenDotKiemKe'];
        $loaiKiemKe = (strpos($maKho, 'TP') !== false) ? 'Kiểm kê thành phẩm' : 'Kiểm kê nguyên liệu';

        $stmtBB = $this->conn->prepare("
            SELECT * FROM bienbankiemke 
            WHERE maKho = ? AND (tenDotKiemKe = ? OR tenDotKiemKe = ?)
            ORDER BY ngayKiemKe DESC, maBienBan DESC LIMIT 1
        ");
        $stmtBB->execute([$maKho, $tenDot, $maDotKiemKe]);
        $bb = $stmtBB->fetch(PDO::FETCH_ASSOC);

        if ($bb) {
            return $bb;
        }

        // Tạo mới biên bản kiểm kê nếu chưa có
        $maBienBan = 'BBKK_' . date('Ymd') . '_' . rand(10, 99);
        $nguoiLap = !empty($maNV) ? $maNV : 'NV002';
        $stmtIns = $this->conn->prepare("
            INSERT INTO bienbankiemke (maBienBan, tenDotKiemKe, loaiKiemKe, maKho, ngayKiemKe, nguoiKiemKe, trangThai)
            VALUES (?, ?, ?, ?, CURDATE(), ?, 'Đang thực hiện')
        ");
        $stmtIns->execute([$maBienBan, $tenDot, $loaiKiemKe, $maKho, $nguoiLap]);

        return [
            'maBienBan' => $maBienBan,
            'tenDotKiemKe' => $tenDot,
            'loaiKiemKe' => $loaiKiemKe,
            'maKho' => $maKho,
            'ngayKiemKe' => date('Y-m-d'),
            'nguoiKiemKe' => $nguoiLap,
            'trangThai' => 'Đang thực hiện'
        ];
    }

    public function getDanhSachKiemKeTheoKho($maKho, $loaiHang = 'NL', $maBienBan = '') {
        try {
            $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';
            $params = [':maKho' => $maKho];

            if ($loaiHang === 'NL') {
                $sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        nl.maNL AS maHang, nl.tenNL AS tenHang, nl.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang,
                        COALESCE(lnl.trangThai, 'Chờ kiểm đếm') AS trangThaiLo,
                        ct.maChiTiet, ct.soLuongTotThucTe, ct.soLuongHongThucTe, ct.soLuongChenhLech, ct.tinhTrang,
                        CASE WHEN ct.maChiTiet IS NOT NULL THEN 1 ELSE 0 END AS daKiem
                    FROM tonkho tk
                    INNER JOIN longuyenlieu lnl ON tk.maLo = lnl.maLoNL
                    INNER JOIN nguyenlieu nl ON lnl.maNL = nl.maNL
                    INNER JOIN vitrikho v ON tk.maViTri = v.maViTri
                    INNER JOIN kho k ON v.maKho = k.maKho
                    LEFT JOIN chitietkiemke ct ON tk.maLo = ct.maLo AND tk.maViTri = ct.maViTri " . 
                    (!empty($maBienBan) ? "AND ct.maBienBan = :maBienBan" : "AND ct.maBienBan = (SELECT maBienBan FROM bienbankiemke WHERE maKho = :maKhoSub ORDER BY ngayKiemKe DESC LIMIT 1)") . "
                    WHERE tk.loaiHang = 'NL' AND v.maKho = :maKho
                    ORDER BY daKiem DESC, tk.maLo ASC
                ";
            } else {
                $sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        tp.maTP AS maHang, tp.tenTP AS tenHang, tp.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang,
                        COALESCE(ltp.trangThai, 'Chờ kiểm đếm') AS trangThaiLo,
                        ct.maChiTiet, ct.soLuongTotThucTe, ct.soLuongHongThucTe, ct.soLuongChenhLech, ct.tinhTrang,
                        CASE WHEN ct.maChiTiet IS NOT NULL THEN 1 ELSE 0 END AS daKiem
                    FROM tonkho tk
                    INNER JOIN lothanhpham ltp ON tk.maLo = ltp.maLoTP
                    INNER JOIN thanhpham tp ON ltp.maTP = tp.maTP
                    INNER JOIN vitrikho v ON tk.maViTri = v.maViTri
                    INNER JOIN kho k ON v.maKho = k.maKho
                    LEFT JOIN chitietkiemke ct ON tk.maLo = ct.maLo AND tk.maViTri = ct.maViTri " . 
                    (!empty($maBienBan) ? "AND ct.maBienBan = :maBienBan" : "AND ct.maBienBan = (SELECT maBienBan FROM bienbankiemke WHERE maKho = :maKhoSub ORDER BY ngayKiemKe DESC LIMIT 1)") . "
                    WHERE tk.loaiHang = 'TP' AND v.maKho = :maKho
                    ORDER BY daKiem DESC, tk.maLo ASC
                ";
            }

            if (!empty($maBienBan)) {
                $params[':maBienBan'] = $maBienBan;
            } else {
                $params[':maKhoSub'] = $maKho;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn mặt hàng kiểm kê: " . $e->getMessage());
        }
    }

    public function xacNhanLoKiemKe($maDotKiemKe, $maLo, $maViTri, $slTot, $slHong, $ghiChu = '', $maNV = '') {
        try {
            $this->conn->beginTransaction();

            $bb = $this->getOrCreateBienBan($maDotKiemKe, $maNV);
            $maBienBan = $bb['maBienBan'];

            if ($bb['trangThai'] === 'Đã hoàn tất') {
                throw new Exception("Đợt kiểm kê này đã được hoàn tất và khóa sổ, không thể chỉnh sửa!");
            }

            // Lấy tồn sổ sách thực tế trong tonkho
            $stmtTK = $this->conn->prepare("SELECT slTon FROM tonkho WHERE maLo = ? AND maViTri = ? LIMIT 1");
            $stmtTK->execute([$maLo, $maViTri]);
            $tonSoSach = $stmtTK->fetchColumn();
            if ($tonSoSach === false) {
                // Fallback nếu không khớp chính xác vị trí, lấy theo mã lô
                $stmtTK2 = $this->conn->prepare("SELECT slTon, maViTri FROM tonkho WHERE maLo = ? LIMIT 1");
                $stmtTK2->execute([$maLo]);
                $rowTK = $stmtTK2->fetch(PDO::FETCH_ASSOC);
                $tonSoSach = $rowTK ? (float)$rowTK['slTon'] : 0.0;
                if ($rowTK && empty($maViTri)) $maViTri = $rowTK['maViTri'];
            } else {
                $tonSoSach = (float)$tonSoSach;
            }

            $slTot = (float)$slTot;
            $slHong = (float)$slHong;
            $chenhLech = ($slTot + $slHong) - $tonSoSach;

            if ($chenhLech == 0.0) {
                $tinhTrang = 'Khớp 100%';
            } elseif ($chenhLech > 0) {
                $tinhTrang = 'Thừa ' . $chenhLech;
            } else {
                $tinhTrang = 'Hao hụt ' . abs($chenhLech);
            }

            if (!empty($ghiChu)) {
                $tinhTrang .= ' - ' . trim($ghiChu);
            }

            // Kiểm tra xem lô này tại vị trí kệ này đã có dòng trong chitietkiemke của biên bản chưa
            $stmtCheck = $this->conn->prepare("SELECT maChiTiet FROM chitietkiemke WHERE maBienBan = ? AND maLo = ? AND maViTri = ? LIMIT 1");
            $stmtCheck->execute([$maBienBan, $maLo, $maViTri]);
            $existingChiTiet = $stmtCheck->fetchColumn();

            if ($existingChiTiet) {
                // Cập nhật lại số liệu đã đếm
                $stmtUpd = $this->conn->prepare("
                    UPDATE chitietkiemke 
                    SET soLuongTheoSoSach = ?, soLuongTotThucTe = ?, soLuongHongThucTe = ?, soLuongChenhLech = ?, tinhTrang = ?
                    WHERE maChiTiet = ?
                ");
                $stmtUpd->execute([$tonSoSach, $slTot, $slHong, $chenhLech, $tinhTrang, $existingChiTiet]);
                $maChiTiet = $existingChiTiet;
            } else {
                // Thêm mới chi tiết kiểm kê
                $maChiTiet = 'CTK_' . date('mdHis') . '_' . rand(10, 99);
                $stmtIns = $this->conn->prepare("
                    INSERT INTO chitietkiemke (maChiTiet, maBienBan, maLo, maViTri, soLuongTheoSoSach, soLuongTotThucTe, soLuongHongThucTe, soLuongChenhLech, tinhTrang)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtIns->execute([$maChiTiet, $maBienBan, $maLo, $maViTri, $tonSoSach, $slTot, $slHong, $chenhLech, $tinhTrang]);
            }

            $this->conn->commit();

            return [
                'success' => true,
                'message' => "Đã xác nhận kiểm đếm lô {$maLo} thành công!",
                'maChiTiet' => $maChiTiet,
                'maLo' => $maLo,
                'maBienBan' => $maBienBan,
                'tonSoSach' => $tonSoSach,
                'slTot' => $slTot,
                'slHong' => $slHong,
                'tongThucTe' => ($slTot + $slHong),
                'chenhLech' => $chenhLech,
                'tinhTrang' => $tinhTrang
            ];
        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception("Lỗi khi xác nhận lô kiểm kê: " . $e->getMessage());
        }
    }

    public function hoanTatBienBanKiemKe($maDotKiemKe, $maNV = '') {
        try {
            $this->conn->beginTransaction();

            $bb = $this->getOrCreateBienBan($maDotKiemKe, $maNV);
            $maBienBan = $bb['maBienBan'];

            // Cập nhật trạng thái biên bản kiểm kê thành 'Đã hoàn tất'
            $stmtBB = $this->conn->prepare("
                UPDATE bienbankiemke 
                SET trangThai = 'Đã hoàn tất', ngayKiemKe = CURDATE(), nguoiKiemKe = ? 
                WHERE maBienBan = ?
            ");
            $stmtBB->execute([!empty($maNV) ? $maNV : 'NV002', $maBienBan]);

            // Cập nhật đợt kiểm kê thành 'Đã hoàn tất'
            $stmtDot = $this->conn->prepare("
                UPDATE dotkiemke 
                SET trangThai = 'Đã hoàn tất', ngayKetThuc = CURDATE() 
                WHERE maDotKiemKe = ?
            ");
            $stmtDot->execute([$maDotKiemKe]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => "Đã hoàn tất kiểm kê đợt {$maDotKiemKe}! Toàn bộ dữ liệu biên bản {$maBienBan} đã được chốt và khóa sổ.",
                'maBienBan' => $maBienBan
            ];
        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw new Exception("Lỗi khi hoàn tất biên bản kiểm kê: " . $e->getMessage());
        }
    }

    public function luuBienBanKiemKe($maDotKiemKe, $items, $maNV) {
        // Tương thích ngược: lặp qua và xác nhận toàn bộ
        foreach ($items as $item) {
            $this->xacNhanLoKiemKe(
                $maDotKiemKe,
                $item['maLo'] ?? '',
                $item['maViTri'] ?? '',
                $item['slTot'] ?? 0,
                $item['slHong'] ?? 0,
                $item['ghiChu'] ?? '',
                $maNV
            );
        }
        return $this->hoanTatBienBanKiemKe($maDotKiemKe, $maNV);
    }
}
