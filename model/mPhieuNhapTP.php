<?php
/**
 * MODEL: mPhieuNhapTP
 * Use Case: UC 3.4 - Lập phiếu nhập kho thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc
 * Bảng dữ liệu tương ứng: LenhDieuPhoiNhap, PhieuNhapKho, ViTriKho, TonKho, LoThanhPham
 */
require_once __DIR__ . '/ketnoi.php';

class mPhieuNhapTP {
    private $conn;

    public function __construct() {
        $db = new KetNoi();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy danh sách Lệnh điều phối tiếp nhận thành phẩm từ CSDL
     */
    public function getDanhSachLenhDieuPhoi() {
        if (!$this->conn) return [];
        try {
            $sql = "
                SELECT 
                    l.maLenhDieuPhoi,
                    l.loaiHang,
                    l.maLo,
                    l.maKhoTiepNhan,
                    l.soLuongPhanBo,
                    l.ngayDieuPhoi,
                    l.nguoiDieuPhoi,
                    l.lyDoGiaiTrinh,
                    l.trangThai,
                    tp.maTP,
                    tp.tenTP,
                    tp.donViTinh,
                    tp.quyCachDongGoi,
                    lotp.phanXuong,
                    lotp.ngaySanXuat,
                    lotp.hanSuDung,
                    lotp.trangThaiKiemDinh,
                    k.tenKho,
                    COALESCE(nv.hoTen, l.nguoiDieuPhoi) AS tenNguoiDieuPhoi,
                    pnk.maPNK,
                    pnk.ngayNhapThucTe,
                    pnk.maViTri AS viTriDaNhap,
                    pnk.ghiChu AS ghiChuNhapKho
                FROM LenhDieuPhoiNhap l
                INNER JOIN LoThanhPham lotp ON l.maLo = lotp.maLoTP
                INNER JOIN ThanhPham tp ON lotp.maTP = tp.maTP
                LEFT JOIN Kho k ON l.maKhoTiepNhan = k.maKho
                LEFT JOIN NhanVien nv ON l.nguoiDieuPhoi = nv.maNV
                LEFT JOIN PhieuNhapKho pnk ON l.maLenhDieuPhoi = pnk.maLenhDieuPhoi
                WHERE l.loaiHang = 'TP'
                ORDER BY 
                    CASE 
                        WHEN l.trangThai = 'Chờ nhập kho' THEN 0 
                        WHEN l.trangThai LIKE 'Chờ%' THEN 1 
                        ELSE 2 
                    END ASC,
                    l.maLenhDieuPhoi ASC
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Lỗi getDanhSachLenhDieuPhoi: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách Vị trí kệ khả dụng trong kho thành phẩm
     */
    public function getViTriKhoKhaDung($maKho = 'KHO_TP_01') {
        if (!$this->conn) return [];
        try {
            $sql = "
                SELECT 
                    maViTri,
                    maKho,
                    day,
                    ke,
                    tang,
                    sucChuaKhaDung,
                    trangThaiSuDung,
                    trangThaiChuaHang
                FROM ViTriKho
                WHERE (maKho = 'KHO_TP_01' OR maKho = 'KHO-TP01' OR maKho LIKE '%TP%')
                  AND maViTri NOT LIKE '%/%'
                ORDER BY 
                    CASE 
                        WHEN trangThaiSuDung = 'Trống' OR trangThaiSuDung = 'Còn trống' THEN 1 
                        ELSE 2 
                    END ASC,
                    day ASC, ke ASC, tang ASC
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Lỗi getViTriKhoKhaDung: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thực hiện Lưu Phiếu Nhập Kho Thành Phẩm (Giao dịch Transaction toàn vẹn CSDL)
     */
    public function luuPhieuNhapKho($maLenhDieuPhoi, $maViTri, $maNV = 'NV006', $ghiChu = '', $slThucNhap = null, $lyDoChenhLech = null) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Không thể kết nối cơ sở dữ liệu!'];
        }

        try {
            $this->conn->beginTransaction();

            // 1. Kiểm tra Lệnh điều phối
            $stmtCheck = $this->conn->prepare("
                SELECT l.*, lotp.soLuongTon AS slTonLo 
                FROM LenhDieuPhoiNhap l
                JOIN LoThanhPham lotp ON l.maLo = lotp.maLoTP
                WHERE l.maLenhDieuPhoi = :maLenh
                LIMIT 1
                FOR UPDATE
            ");
            $stmtCheck->execute([':maLenh' => $maLenhDieuPhoi]);
            $lenh = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$lenh) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Không tìm thấy lệnh điều phối tiếp nhận!'];
            }

            if ($lenh['trangThai'] === 'Hoàn tất nhập kho' || $lenh['trangThai'] === 'Hoàn tất nhập kho (Hao hụt)') {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Lệnh điều phối này đã được lập phiếu nhập kho trước đó!'];
            }

            // 2. Kiểm tra Vị trí kệ
            $stmtViTri = $this->conn->prepare("SELECT * FROM ViTriKho WHERE maViTri = :maVT LIMIT 1");
            $stmtViTri->execute([':maVT' => $maViTri]);
            $viTri = $stmtViTri->fetch(PDO::FETCH_ASSOC);

            if (!$viTri) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Vị trí kệ không hợp lệ!'];
            }

            // 3. Tính toán số lượng thực nhập & chênh lệch theo Use Case 3.2 Luồng 5.1
            $slPhanBo = (float)$lenh['soLuongPhanBo'];
            $slThuc = ($slThucNhap !== null && (float)$slThucNhap > 0) ? (float)$slThucNhap : $slPhanBo;
            $soLuongChenhLech = max(0, $slPhanBo - $slThuc);
            $trangThaiLenh = ($soLuongChenhLech > 0) ? 'Hoàn tất nhập kho (Hao hụt)' : 'Hoàn tất nhập kho';

            // 4. Tự động sinh mã Phiếu Nhập Kho (PNK)
            $datePrefix = date('Ymd');
            $stmtCount = $this->conn->query("SELECT COUNT(*) FROM PhieuNhapKho WHERE maPNK LIKE 'PNK_TP_{$datePrefix}%'");
            $count = (int)$stmtCount->fetchColumn() + 1;
            $maPNK = 'PNK_TP_' . $datePrefix . '_' . str_pad($count, 3, '0', STR_PAD_LEFT);

            // 5. INSERT vào bảng PhieuNhapKho
            $stmtInsert = $this->conn->prepare("
                INSERT INTO PhieuNhapKho 
                (maPNK, maLenhDieuPhoi, maViTri, ngayNhapThucTe, nguoiLap, slThucNhap, soLuongChenhLech, lyDoChenhLech, ghiChu, trangThai)
                VALUES 
                (:maPNK, :maLenh, :maViTri, NOW(), :nguoiLap, :slThucNhap, :soLuongChenhLech, :lyDoChenhLech, :ghiChu, 'Hoàn tất')
            ");
            $stmtInsert->execute([
                ':maPNK'            => $maPNK,
                ':maLenh'           => $maLenhDieuPhoi,
                ':maViTri'          => $maViTri,
                ':nguoiLap'         => $maNV,
                ':slThucNhap'       => $slThuc,
                ':soLuongChenhLech' => $soLuongChenhLech,
                ':lyDoChenhLech'    => $lyDoChenhLech ?: ($soLuongChenhLech > 0 ? 'Hao hụt vận chuyển bốc dỡ' : NULL),
                ':ghiChu'           => $ghiChu ?: 'Nhập thành phẩm đạt chuẩn theo lệnh điều phối'
            ]);

            // 6. CẬP NHẬT bảng LenhDieuPhoiNhap
            $stmtUpdateLenh = $this->conn->prepare("
                UPDATE LenhDieuPhoiNhap 
                SET trangThai = :trangThai 
                WHERE maLenhDieuPhoi = :maLenh
            ");
            $stmtUpdateLenh->execute([
                ':trangThai' => $trangThaiLenh,
                ':maLenh'    => $maLenhDieuPhoi
            ]);

            // 7. CẬP NHẬT bảng LoThanhPham -> Tăng tồn kho thực nhập và chuyển trạng thái 'Đã lưu kho'
            $stmtUpdateLo = $this->conn->prepare("
                UPDATE LoThanhPham 
                SET soLuongTon = soLuongTon + :sl,
                    trangThai = 'Đã lưu kho'
                WHERE maLoTP = :maLo
            ");
            $stmtUpdateLo->execute([
                ':sl'   => $slThuc,
                ':maLo' => $lenh['maLo']
            ]);

            // 8. CẬP NHẬT Tồn kho tại Vị trí kệ (Bảng TonKho)
            $stmtCheckTK = $this->conn->prepare("
                SELECT maTonKho, slTon FROM TonKho 
                WHERE loaiHang = 'TP' AND maLo = :maLo AND maViTri = :maViTri 
                LIMIT 1
            ");
            $stmtCheckTK->execute([
                ':maLo'    => $lenh['maLo'],
                ':maViTri' => $maViTri
            ]);
            $tonKho = $stmtCheckTK->fetch(PDO::FETCH_ASSOC);

            if ($tonKho) {
                $stmtUpdateTK = $this->conn->prepare("
                    UPDATE TonKho 
                    SET slTon = slTon + :sl, ngayCapNhat = NOW() 
                    WHERE maTonKho = :maTK
                ");
                $stmtUpdateTK->execute([
                    ':sl'   => $slThuc,
                    ':maTK' => $tonKho['maTonKho']
                ]);
            } else {
                $maTK = 'TK_' . date('Ymd') . '_' . strtoupper(substr(md5(uniqid()), 0, 6));
                $stmtInsertTK = $this->conn->prepare("
                    INSERT INTO TonKho 
                    (maTonKho, loaiHang, maLo, maViTri, slTon, soLuongTamGiu, trangThai)
                    VALUES 
                    (:maTK, 'TP', :maLo, :maViTri, :sl, 0.00, 'Khả dụng')
                ");
                $stmtInsertTK->execute([
                    ':maTK'    => $maTK,
                    ':maLo'    => $lenh['maLo'],
                    ':maViTri' => $maViTri,
                    ':sl'      => $slThuc
                ]);
            }

            // 9. CẬP NHẬT trạng thái Vị trí kệ -> 'Đang chứa'
            $stmtUpdateVT = $this->conn->prepare("
                UPDATE ViTriKho 
                SET trangThaiSuDung = 'Đang chứa' 
                WHERE maViTri = :maViTri
            ");
            $stmtUpdateVT->execute([':maViTri' => $maViTri]);

            // Hoàn tất transaction an toàn
            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Lập phiếu nhập kho thành phẩm thành công!',
                'maPNK'   => $maPNK,
                'maViTri' => $maViTri
            ];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Lỗi luuPhieuNhapKho: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi xử lý cơ sở dữ liệu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy thông tin phiếu nhập đầy đủ để in A4
     */
    public function getChiTietPhieuNhap($maPNK) {
        if (!$this->conn) return null;
        try {
            $sql = "
                SELECT 
                    pnk.*,
                    l.soLuongPhanBo,
                    l.lyDoGiaiTrinh,
                    lotp.maLoTP,
                    lotp.phanXuong,
                    lotp.ngaySanXuat,
                    lotp.hanSuDung,
                    tp.maTP,
                    tp.tenTP,
                    tp.donViTinh,
                    k.tenKho,
                    nv.hoTen AS tenNguoiLap
                FROM PhieuNhapKho pnk
                INNER JOIN LenhDieuPhoiNhap l ON pnk.maLenhDieuPhoi = l.maLenhDieuPhoi
                INNER JOIN LoThanhPham lotp ON l.maLo = lotp.maLoTP
                INNER JOIN ThanhPham tp ON lotp.maTP = tp.maTP
                LEFT JOIN Kho k ON l.maKhoTiepNhan = k.maKho
                LEFT JOIN NhanVien nv ON pnk.nguoiLap = nv.maNV
                WHERE pnk.maPNK = :maPNK
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':maPNK' => $maPNK]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }
}
?>
