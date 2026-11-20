<?php
class mPhieuNhapNL {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL từ Database.php, không cần require!
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách lệnh điều phối nhập nguyên liệu từ bảng `lenhdieuphoinhap`
     */
    public function selectAllLenhDieuPhoiNhap($tuKhoa = '') {
        $sql = "SELECT l.*, lo.maNL, lo.nhaCungCap, lo.hanSuDung, lo.ngaySanXuat, lo.trangThaiKiemDinh,
                       n.tenNL, n.donViTinh, k.tenKho, nv.hoTen AS tenNguoiDieuPhoi
                FROM lenhdieuphoinhap l
                JOIN longuyenlieu lo ON l.maLo = lo.maLoNL
                JOIN nguyenlieu n ON lo.maNL = n.maNL
                LEFT JOIN kho k ON l.maKhoTiepNhan = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                WHERE l.loaiHang = 'NL' ";
        
        if (!empty($tuKhoa)) {
            $sql .= "AND (l.maLenhDieuPhoi LIKE :kw OR l.maLo LIKE :kw OR n.tenNL LIKE :kw) ";
        }
        $sql .= "ORDER BY l.ngayDieuPhoi DESC, l.maLenhDieuPhoi DESC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($tuKhoa)) {
            $kw = "%$tuKhoa%";
            $stmt->bindParam(':kw', $kw);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết lệnh điều phối nhập nguyên liệu
     */
    public function selectChiTietDieuPhoiNhap($maLenhDieuPhoi) {
        $sql = "SELECT l.*, lo.maNL, lo.nhaCungCap, lo.hanSuDung, lo.ngaySanXuat, lo.trangThaiKiemDinh,
                       n.tenNL, n.donViTinh, k.tenKho, nv.hoTen AS tenNguoiDieuPhoi,
                       bb.ketQuaTongThe AS ketQuaKiemDinh
                FROM lenhdieuphoinhap l
                JOIN longuyenlieu lo ON l.maLo = lo.maLoNL
                JOIN nguyenlieu n ON lo.maNL = n.maNL
                LEFT JOIN kho k ON l.maKhoTiepNhan = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                LEFT JOIN bienbankiemdinh bb ON (lo.maLoNL = bb.maLo AND bb.loaiKiemDinh = 1)
                WHERE l.maLenhDieuPhoi = :maLenhDieuPhoi AND l.loaiHang = 'NL'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':maLenhDieuPhoi', $maLenhDieuPhoi);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách vị trí kệ kho khả dụng còn trống từ `vitrikho`
     */
    public function selectViTriKhoTrong($maKho = '', $day = '', $ke = '', $tang = '') {
        $sql = "SELECT * FROM vitrikho WHERE trangThaiSuDung = 'Trống' AND trangThaiChuaHang = 'Khả dụng' ";
        if (!empty($maKho)) $sql .= "AND maKho = :maKho ";
        if (!empty($day))   $sql .= "AND day = :day ";
        if (!empty($ke))    $sql .= "AND ke = :ke ";
        if (!empty($tang))  $sql .= "AND tang = :tang ";
        $sql .= "ORDER BY day ASC, ke ASC, tang ASC";

        $stmt = $this->conn->prepare($sql);
        if (!empty($maKho)) $stmt->bindParam(':maKho', $maKho);
        if (!empty($day))   $stmt->bindParam(':day', $day);
        if (!empty($ke))    $stmt->bindParam(':ke', $ke);
        if (!empty($tang))  $stmt->bindParam(':tang', $tang);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lưu phiếu nhập nguyên liệu & Cập nhật các bảng liên quan (Transaction)
     */
    public function insertPhieuNhapNL($maLenhDieuPhoi, $maViTri, $slThucNhap, $soLuongChenhLech, $lyDoChenhLech, $maNV, $ghiChu) {
        try {
            $this->conn->beginTransaction();

            // Sinh mã phiếu nhập kho ngẫu nhiên chuẩn ERP
            $maPNK = 'PNK_NL_' . date('Ymd_His');

            // 1. Chèn bản ghi vào `phieunhapkho`
            $sqlNhap = "INSERT INTO phieunhapkho (maPNK, maLenhDieuPhoi, maViTri, ngayNhapThucTe, nguoiLap, slThucNhap, soLuongChenhLech, lyDoChenhLech, ghiChu, trangThai) 
                        VALUES (:maPNK, :maLenhDieuPhoi, :maViTri, NOW(), :nguoiLap, :slThucNhap, :soLuongChenhLech, :lyDoChenhLech, :ghiChu, 'Hoàn tất')";
            $stmtNhap = $this->conn->prepare($sqlNhap);
            $stmtNhap->execute([
                ':maPNK'            => $maPNK,
                ':maLenhDieuPhoi'   => $maLenhDieuPhoi,
                ':maViTri'          => $maViTri,
                ':nguoiLap'         => $maNV,
                ':slThucNhap'       => $slThucNhap,
                ':soLuongChenhLech' => $soLuongChenhLech,
                ':lyDoChenhLech'    => $lyDoChenhLech,
                ':ghiChu'           => $ghiChu
            ]);

            // 2. Cập nhật trạng thái `lenhdieuphoinhap` sang "Hoàn tất nhập kho"
            $sqlDP = "UPDATE lenhdieuphoinhap SET trangThai = 'Hoàn tất nhập kho' WHERE maLenhDieuPhoi = :maLenhDieuPhoi";
            $stmtDP = $this->conn->prepare($sqlDP);
            $stmtDP->execute([':maLenhDieuPhoi' => $maLenhDieuPhoi]);

            // 3. Cập nhật trạng thái lô nguyên liệu sang "Đã lưu kho" và tăng số lượng tồn của lô
            $sqlLo = "UPDATE longuyenlieu SET trangThai = 'Đã lưu kho', soLuongTon = soLuongTon + :slThucNhap 
                      WHERE maLoNL = (SELECT maLo FROM lenhdieuphoinhap WHERE maLenhDieuPhoi = :maLenhDieuPhoi)";
            $stmtLo = $this->conn->prepare($sqlLo);
            $stmtLo->execute([':slThucNhap' => $slThucNhap, ':maLenhDieuPhoi' => $maLenhDieuPhoi]);

            // 4. Cập nhật tổng số lượng tồn của nguyên liệu trong bảng `nguyenlieu`
            $sqlNL = "UPDATE nguyenlieu SET soLuongTon = soLuongTon + :slThucNhap 
                      WHERE maNL = (SELECT maNL FROM longuyenlieu WHERE maLoNL = (SELECT maLo FROM lenhdieuphoinhap WHERE maLenhDieuPhoi = :maLenhDieuPhoi))";
            $stmtNL = $this->conn->prepare($sqlNL);
            $stmtNL->execute([':slThucNhap' => $slThucNhap, ':maLenhDieuPhoi' => $maLenhDieuPhoi]);

            // 5. Cập nhật trạng thái vị trí kho sang "Đang chứa"
            $sqlVT = "UPDATE vitrikho SET trangThaiSuDung = 'Đang chứa' WHERE maViTri = :maViTri";
            $stmtVT = $this->conn->prepare($sqlVT);
            $stmtVT->execute([':maViTri' => $maViTri]);

            // 6. Ghi nhận thông tin vào bảng `tonkho`
            $maTonKho = 'TK_NL_' . date('Ymd_His');
            $sqlTK = "INSERT INTO tonkho (maTonKho, loaiHang, maLo, maViTri, slTon, soLuongTamGiu, ngayCapNhat, trangThai)
                      VALUES (:maTonKho, 'NL', (SELECT maLo FROM lenhdieuphoinhap WHERE maLenhDieuPhoi = :maLenhDieuPhoi), :maViTri, :slTon, 0, NOW(), 'Khả dụng')";
            $stmtTK = $this->conn->prepare($sqlTK);
            $stmtTK->execute([
                ':maTonKho'       => $maTonKho,
                ':maLenhDieuPhoi' => $maLenhDieuPhoi,
                ':maViTri'        => $maViTri,
                ':slTon'          => $slThucNhap
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}