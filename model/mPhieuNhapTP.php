<?php
/**
 * =========================================================================
 * MODEL: mPhieuNhapTP
 * Use Case: UC 3.4 - Lập phiếu nhập kho thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc
 * Bảng dữ liệu tương ứng: LenhDieuPhoiNhap, PhieuNhapKho, ViTriKho, TonKho, LoThanhPham
 * =========================================================================
 */
require_once __DIR__ . '/Database.php';

class mPhieuNhapTP {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL Singleton từ Database.php
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách Lệnh điều phối tiếp nhận thành phẩm thực tế từ CSDL MySQL
     */
    public function getDanhSachLenhDieuPhoi() {
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
                    pnk.ghiChu AS ghiChuNhapKho,
                    pnk.slThucNhap,
                    pnk.soLuongChenhLech,
                    pnk.lyDoChenhLech
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
        } catch (PDOException $e) {
            $errMsg = "Lỗi truy vấn danh sách lệnh điều phối: " . $e->getMessage();
            error_log($errMsg);
            throw new Exception($errMsg);
        }
    }

    /**
     * Lấy danh sách Vị trí kệ khả dụng trong kho thành phẩm từ CSDL MySQL
     * Phân loại tự động dựa trên loaiKho = 'Kho Thành phẩm' trong bảng kho,
     * không cố định mã kho để hỗ trợ đa kho thành phẩm (Kho TP 1, Kho TP 2...)
     */
    public function getViTriKhoKhaDung($maKho = null) {
        try {
            $sql = "
                SELECT 
                    v.maViTri,
                    v.maKho,
                    k.tenKho,
                    k.loaiKho,
                    v.day,
                    v.ke,
                    v.tang,
                    v.sucChuaKhaDung,
                    v.trangThaiSuDung,
                    v.trangThaiChuaHang
                FROM vitrikho v
                INNER JOIN kho k ON v.maKho = k.maKho
                WHERE k.loaiKho = 'Kho Thành phẩm'
                  AND v.maViTri NOT LIKE '%/%'
            ";

            $params = [];
            if (!empty($maKho)) {
                $sql .= " AND v.maKho = :maKho";
                $params[':maKho'] = $maKho;
            }

            $sql .= "
                ORDER BY 
                    CASE 
                        WHEN v.trangThaiSuDung = 'Trống' OR v.trangThaiSuDung = 'Còn trống' THEN 1 
                        WHEN v.trangThaiSuDung = 'Đang chứa' THEN 2
                        ELSE 3 
                    END ASC,
                    v.maKho ASC, v.day ASC, v.ke ASC, v.tang ASC
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errMsg = "Lỗi truy vấn vị trí kệ thành phẩm: " . $e->getMessage();
            error_log($errMsg);
            throw new Exception($errMsg);
        }
    }

    /**
     * Thực hiện Lưu Phiếu Nhập Kho Thành Phẩm (Giao dịch Transaction toàn vẹn CSDL)
     */
    public function luuPhieuNhapKho($maLenhDieuPhoi, $maViTri, $maNV, $ghiChu = '', $slThucNhap = null, $lyDoChenhLech = null) {
        if (empty($maNV)) {
            return ['success' => false, 'message' => 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại!'];
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
                return ['success' => false, 'message' => 'Không tìm thấy lệnh điều phối tiếp nhận trong CSDL!'];
            }

            // [CẢI TIẾN 1 - WHITELIST CHUẨN]: Chỉ cho phép lập phiếu khi lệnh ở trạng thái sẵn sàng tiếp nhận
            $trangThaiHopLe = ['Chờ nhập kho', 'Chờ tiếp nhận'];
            if (!in_array($lenh['trangThai'], $trangThaiHopLe)) {
                $this->conn->rollBack();
                return [
                    'success' => false, 
                    'message' => "Lệnh điều phối này không ở trạng thái sẵn sàng tiếp nhận nhập kho (Trạng thái hiện tại: '{$lenh['trangThai']}')!"
                ];
            }

            // Chặn trùng lặp ở mức bảng PhieuNhapKho: Một lệnh chỉ được có duy nhất 1 phiếu nhập
            $stmtCheckPNK = $this->conn->prepare("SELECT maPNK FROM PhieuNhapKho WHERE maLenhDieuPhoi = :maLenh LIMIT 1");
            $stmtCheckPNK->execute([':maLenh' => $maLenhDieuPhoi]);
            if ($stmtCheckPNK->fetch()) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Lệnh điều phối này đã tồn tại Phiếu nhập kho trong hệ thống!'];
            }

            // 2. Tính toán số lượng thực nhập & kiểm tra chênh lệch nghiệp vụ UC 3.2
            if ($slThucNhap === null) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Số lượng thực nhận không được để trống!'];
            }

            $slThuc = (float)$slThucNhap;
            $slPhanBo = (float)$lenh['soLuongPhanBo'];

            if ($slThuc <= 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Số lượng thực nhận phải lớn hơn 0!'];
            }

            if ($slPhanBo > 0 && $slThuc > $slPhanBo) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Số lượng thực nhận không được vượt quá số lượng điều phối!'];
            }

            $soLuongChenhLech = max(0, $slPhanBo - $slThuc);

            // BẮT BUỘC NHẬP LÝ DO NẾU CÓ HAO HỤT (TUYỆT ĐỐI KHÔNG TỰ BỊA)
            if ($soLuongChenhLech > 0 && empty(trim($lyDoChenhLech ?? ''))) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Số lượng thực nhận nhỏ hơn điều phối. Bắt buộc phải nhập lý do chênh lệch/hao hụt!'];
            }

            // Thống nhất trạng thái chuẩn đồng bộ với CSDL seed và Giao diện: 'Hoàn tất nhập kho - Hao hụt'
            $trangThaiLenh = ($soLuongChenhLech > 0) ? 'Hoàn tất nhập kho - Hao hụt' : 'Hoàn tất nhập kho';

            // 3. Kiểm tra Vị trí kệ
            $stmtViTri = $this->conn->prepare("SELECT * FROM ViTriKho WHERE maViTri = :maVT LIMIT 1 FOR UPDATE");
            $stmtViTri->execute([':maVT' => $maViTri]);
            $viTri = $stmtViTri->fetch(PDO::FETCH_ASSOC);

            if (!$viTri) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Vị trí kệ không hợp lệ!'];
            }

            // [LỖI 1 ĐÃ SỬA]: Đối chiếu kho của vị trí với maKhoTiepNhan của lệnh điều phối (Ngăn chặn lưu sai kho)
            if (!empty($lenh['maKhoTiepNhan']) && !empty($viTri['maKho']) && $viTri['maKho'] !== $lenh['maKhoTiepNhan']) {
                $this->conn->rollBack();
                return [
                    'success' => false, 
                    'message' => "Lỗi sai kho tiếp nhận: Lệnh này chỉ định tiếp nhận vào kho '{$lenh['maKhoTiepNhan']}', nhưng vị trí kệ '{$maViTri}' lại thuộc kho '{$viTri['maKho']}'!"
                ];
            }

            // [LỖI 3 ĐÃ SỬA]: Kiểm tra trạng thái sử dụng VÀ trạng thái chứa hàng (Khóa / Bảo trì)
            if ($viTri['trangThaiSuDung'] !== 'Trống' && $viTri['trangThaiSuDung'] !== 'Còn trống') {
                $this->conn->rollBack();
                return ['success' => false, 'message' => "Vị trí kệ {$maViTri} hiện không trống ({$viTri['trangThaiSuDung']}). Vui lòng chọn kệ còn trống!"];
            }

            if (!empty($viTri['trangThaiChuaHang']) && $viTri['trangThaiChuaHang'] !== 'Khả dụng') {
                $this->conn->rollBack();
                return ['success' => false, 'message' => "Vị trí kệ {$maViTri} hiện đang ở trạng thái '{$viTri['trangThaiChuaHang']}' (không khả dụng để chứa hàng)!"];
            }

            // [LỖI 4 ĐÃ SỬA]: Kiểm tra sức chứa tối đa của kệ so với số lượng thực nhận
            $sucChuaKhaDung = (float)($viTri['sucChuaKhaDung'] ?? 0);
            if ($sucChuaKhaDung > 0 && $slThuc > $sucChuaKhaDung) {
                $this->conn->rollBack();
                return [
                    'success' => false, 
                    'message' => "Vượt quá sức chứa kệ: Số lượng thực nhận ({$slThuc}) vượt quá sức chứa tối đa của vị trí {$maViTri} ({$sucChuaKhaDung} Thùng)!"
                ];
            }

            // 4. Sinh mã Phiếu Nhập Kho (PNK) tự động (Khóa bi quan FOR UPDATE MAX chống Race Condition)
            $datePrefix = date('Ymd');
            $stmtMax = $this->conn->prepare("
                SELECT maPNK 
                FROM PhieuNhapKho 
                WHERE maPNK LIKE :pattern 
                ORDER BY maPNK DESC 
                LIMIT 1 
                FOR UPDATE
            ");
            $stmtMax->execute([':pattern' => "PNK_TP_{$datePrefix}%"]);
            $lastCode = $stmtMax->fetchColumn();

            $nextSeq = 1;
            if ($lastCode) {
                $parts = explode('_', $lastCode);
                $lastSeq = (int)end($parts);
                $nextSeq = $lastSeq + 1;
            }
            $maPNK = 'PNK_TP_' . $datePrefix . '_' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

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
                ':lyDoChenhLech'    => !empty(trim($lyDoChenhLech ?? '')) ? trim($lyDoChenhLech) : NULL,
                ':ghiChu'           => !empty(trim($ghiChu ?? '')) ? trim($ghiChu) : NULL
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

            // 7. CẬP NHẬT bảng LoThanhPham -> Tăng tồn kho và chuyển trạng thái 'Đã lưu kho'
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

            // 8. CẬP NHẬT bảng TonKho
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
            if ($this->conn && $this->conn->inTransaction()) {
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
     * Lấy thông tin phiếu nhập đầy đủ để phục vụ xem/in phiếu
     */
    public function getChiTietPhieuNhap($maPNK) {
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
        } catch (PDOException $e) {
            $msg = "Lỗi truy vấn chi tiết phiếu nhập kho: " . $e->getMessage();
            error_log($msg);
            throw new Exception($msg);
        }
    }
}
