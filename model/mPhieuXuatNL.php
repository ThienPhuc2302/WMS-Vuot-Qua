<?php
class mPhieuXuatNL {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL từ Database.php, không cần require!
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách tất cả các lệnh điều phối xuất nguyên liệu từ `lenhdieuphoixuat`
     */
    public function selectAllLenhDieuPhoiXuat($tuKhoa = '') {
        $sql = "SELECT l.*, pyc.donViYeuCau, pyc.lyDoXuat, pyc.tongSoLuong, k.tenKho, nv.hoTen AS tenNguoiDieuPhoi
                FROM lenhdieuphoixuat l
                JOIN phieuyeucauxuat pyc ON l.maPhieuYeuCau = pyc.maPhieuYeuCau
                LEFT JOIN kho k ON l.maKho = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                WHERE pyc.loaiYeuCau = 'NL' ";

        if (!empty($tuKhoa)) {
            $sql .= "AND (l.maLenh LIKE :kw OR pyc.donViYeuCau LIKE :kw OR pyc.maPhieuYeuCau LIKE :kw) ";
        }
        $sql .= "ORDER BY l.ngayDieuPhoi DESC, l.maLenh DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($tuKhoa)) {
            $kw = "%$tuKhoa%";
            $stmt->bindParam(':kw', $kw);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết lệnh điều phối xuất kho nguyên liệu
     */
    public function selectChiTietDieuPhoiXuat($maLenh) {
        $sql = "SELECT l.*, pyc.donViYeuCau, pyc.lyDoXuat, pyc.tongSoLuong, pyc.nguoiYeuCau, k.tenKho, nv.hoTen AS tenNguoiDieuPhoi
                FROM lenhdieuphoixuat l
                JOIN phieuyeucauxuat pyc ON l.maPhieuYeuCau = pyc.maPhieuYeuCau
                LEFT JOIN kho k ON l.maKho = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                WHERE l.maLenh = :maLenh";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maLenh', $maLenh);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách nguyên liệu và kệ cần lấy hàng thuộc lệnh xuất
     */
    public function selectDanhSachChiTietXuat($maLenh) {
        $sql = "SELECT ctx.*, lo.maNL, n.tenNL, n.donViTinh, lo.hanSuDung, vk.day, vk.ke, vk.tang
                FROM chitietdieuphoixuat ctx
                JOIN longuyenlieu lo ON ctx.maLo = lo.maLoNL
                JOIN nguyenlieu n ON lo.maNL = n.maNL
                JOIN vitrikho vk ON ctx.maViTri = vk.maViTri
                WHERE ctx.maLenh = :maLenh";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maLenh', $maLenh);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lưu phiếu xuất kho nguyên liệu & Cập nhật các bảng liên quan (Transaction)
     */
    public function insertPhieuXuatNL($maLenh, $nguoiXuat, $ghiChu) {
        try {
            $this->conn->beginTransaction();

            $maPXK = 'PXK_NL_' . date('Ymd_His');

            // 1. Chèn bản ghi vào `phieuxuatkho`
            $sqlXuat = "INSERT INTO phieuxuatkho (maPXK, maLenhDieuPhoi, ngayXuatThucTe, nguoiXuat, ghiChu) 
                        VALUES (:maPXK, :maLenh, NOW(), :nguoiXuat, :ghiChu)";
            $stmtXuat = $this->conn->prepare($sqlXuat);
            $stmtXuat->execute([
                ':maPXK'      => $maPXK,
                ':maLenh'     => $maLenh,
                ':nguoiXuat'  => $nguoiXuat,
                ':ghiChu'     => $ghiChu
            ]);

            // 2. Cập nhật trạng thái `lenhdieuphoixuat` sang "Đã xuất kho"
            $sqlDP = "UPDATE lenhdieuphoixuat SET trangThai = 'Đã xuất kho' WHERE maLenh = :maLenh";
            $stmtDP = $this->conn->prepare($sqlDP);
            $stmtDP->execute([':maLenh' => $maLenh]);

            // 3. Cập nhật `phieuyeucauxuat` sang "Đã hoàn tất"
            $sqlPYC = "UPDATE phieuyeucauxuat SET trangThai = 'Đã hoàn tất' 
                       WHERE maPhieuYeuCau = (SELECT maPhieuYeuCau FROM lenhdieuphoixuat WHERE maLenh = :maLenh)";
            $stmtPYC = $this->conn->prepare($sqlPYC);
            $stmtPYC->execute([':maLenh' => $maLenh]);

            // 4. Trừ số lượng tồn trong `tonkho`, `longuyenlieu` và `nguyenlieu` theo chi tiết xuất
            $dsChiTiet = $this->selectDanhSachChiTietXuat($maLenh);
            foreach ($dsChiTiet as $item) {
                $slPhanBo = $item['soLuongPhanBo'];

                // Trừ tồn kho tại vị trí
                $sqlTK = "UPDATE tonkho SET slTon = GREATEST(0, slTon - :sl) WHERE maLo = :maLo AND maViTri = :maViTri";
                $stmtTK = $this->conn->prepare($sqlTK);
                $stmtTK->execute([':sl' => $slPhanBo, ':maLo' => $item['maLo'], ':maViTri' => $item['maViTri']]);

                // Nếu số lượng tồn tại kệ = 0 -> cập nhật vị trí kho thành 'Trống'
                $sqlCheckTK = "SELECT slTon FROM tonkho WHERE maLo = :maLo AND maViTri = :maViTri";
                $stmtCheck = $this->conn->prepare($sqlCheckTK);
                $stmtCheck->execute([':maLo' => $item['maLo'], ':maViTri' => $item['maViTri']]);
                $ton = $stmtCheck->fetchColumn();
                if ($ton <= 0) {
                    $sqlVT = "UPDATE vitrikho SET trangThaiSuDung = 'Trống' WHERE maViTri = :maViTri";
                    $stmtVT = $this->conn->prepare($sqlVT);
                    $stmtVT->execute([':maViTri' => $item['maViTri']]);
                }

                // Trừ số lượng tồn lô nguyên liệu & tổng tồn nguyên liệu
                $sqlLo = "UPDATE longuyenlieu SET soLuongTon = GREATEST(0, soLuongTon - :sl) WHERE maLoNL = :maLo";
                $stmtLo = $this->conn->prepare($sqlLo);
                $stmtLo->execute([':sl' => $slPhanBo, ':maLo' => $item['maLo']]);

                $sqlNL = "UPDATE nguyenlieu SET soLuongTon = GREATEST(0, soLuongTon - :sl) WHERE maNL = :maNL";
                $stmtNL = $this->conn->prepare($sqlNL);
                $stmtNL->execute([':sl' => $slPhanBo, ':maNL' => $item['maNL']]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}