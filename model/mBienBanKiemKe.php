<?php
class mBienBanKiemKe {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách đợt kiểm kê theo loại hàng ('NL' hoặc 'TP')
     */
    public function getDanhSachDotTheoLoai($loaiHang = 'NL') {
        try {
            if ($loaiHang === 'TP') {$sql = "SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maKho LIKE '%TP%' ORDER BY d.ngayBatDau DESC";
            } else {
                $sql = "SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maKho NOT LIKE '%TP%' ORDER BY d.ngayBatDau DESC";
            }
            $stmt =$this->conn->prepare($sql);$stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getDotKiemKeTheoMa($maDot) {
        try {
            $stmt =$this->conn->prepare("SELECT d.*, k.tenKho FROM dotkiemke d LEFT JOIN kho k ON d.maKho = k.maKho WHERE d.maDotKiemKe = :maDot LIMIT 1");
            $stmt->execute([':maDot' =>$maDot]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getDanhSachKiemKeTheoKho($maKho,$loaiHang = 'NL') {
        try {
            if ($loaiHang === 'NL') {$sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        nl.maNL AS maHang, nl.tenNL AS tenHang, nl.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang
                    FROM TonKho tk
                    INNER JOIN LoNguyenLieu lnl ON tk.maLo = lnl.maLoNL
                    INNER JOIN NguyenLieu nl ON lnl.maNL = nl.maNL
                    INNER JOIN ViTriKho v ON tk.maViTri = v.maViTri
                    INNER JOIN Kho k ON v.maKho = k.maKho
                    WHERE tk.loaiHang = 'NL' AND v.maKho = :maKho
                    ORDER BY tk.maLo ASC
                ";
            } else {
                $sql = "
                    SELECT 
                        tk.maTonKho, tk.maLo, tk.maViTri, tk.slTon AS tonSoSach,
                        tp.maTP AS maHang, tp.tenTP AS tenHang, tp.donViTinh,
                        k.maKho, k.tenKho, v.day, v.ke, v.tang
                    FROM TonKho tk
                    INNER JOIN LoThanhPham ltp ON tk.maLo = ltp.maLoTP
                    INNER JOIN ThanhPham tp ON ltp.maTP = tp.maTP
                    INNER JOIN ViTriKho v ON tk.maViTri = v.maViTri
                    INNER JOIN Kho k ON v.maKho = k.maKho
                    WHERE tk.loaiHang = 'TP' AND v.maKho = :maKho
                    ORDER BY tk.maLo ASC
                ";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':maKho' =>$maKho]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function luuBienBanKiemKe($maDotKiemKe, $items,$maNV) {
        try {
            $this->conn->beginTransaction();

            foreach ($items as $item) {$maTonKho = $item['maTonKho'] ?? '';$slTot = (float)($item['slTot'] ?? 0);$slHong = (float)($item['slHong'] ?? 0);$tongThucTe = $slTot +$slHong;

                $stmtUpd =$this->conn->prepare("UPDATE TonKho SET slTon = :slTon, ngayCapNhat = NOW() WHERE maTonKho = :maTonKho");
                $stmtUpd->execute([':slTon' => $tongThucTe, ':maTonKho' =>$maTonKho]);
            }

            if (!empty($maDotKiemKe)) {
                $stmtDot =$this->conn->prepare("UPDATE dotkiemke SET trangThai = 'Đã hoàn tất' WHERE maDotKiemKe = :maDot");
                $stmtDot->execute([':maDot' =>$maDotKiemKe]);
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'Lập và hoàn tất biên bản kiểm kê thành công!'];
        } catch (Exception $e) {
            if ($this->conn && $this->conn->inTransaction())$this->conn->rollBack();
            return ['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()];
        }
    }
}