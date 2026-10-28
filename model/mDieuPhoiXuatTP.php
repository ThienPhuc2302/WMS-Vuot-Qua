<?php
/**
 * =========================================================================
 * MODEL: mDieuPhoiXuatTP
 * Chức năng: Điều phối xuất thành phẩm (Thuật toán FEFO)
 * Bảng liên quan: phieuyeucauxuat, chitietyeucauxuat, lenhdieuphoixuat,
 *                 chitietdieuphoixuat, tonkho, lothanhpham, vitrikho, kho, thanhpham
 * =========================================================================
 */
class mDieuPhoiXuatTP {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL từ Database.php (Singleton)
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách Phiếu yêu cầu xuất thành phẩm
     * Lọc loại yêu cầu là 'TP', hỗ trợ tìm kiếm theo từ khóa và lọc trạng thái
     */
    public function getDanhSachPhieuYeuCau($keyword = '', $trangThai = '') {
        try {
            $params = [];
            $where = ["p.loaiYeuCau = 'TP'"];

            if (!empty($keyword)) {
                $where[] = "(
                    p.maPhieuYeuCau LIKE :kw 
                    OR p.donViYeuCau LIKE :kw 
                    OR p.lyDoXuat LIKE :kw
                    OR EXISTS (
                        SELECT 1 FROM chitietyeucauxuat ct 
                        WHERE ct.maPhieuYeuCau = p.maPhieuYeuCau 
                          AND (ct.tenMatHang LIKE :kw OR ct.maMatHang LIKE :kw)
                    )
                )";
                $params[':kw'] = '%' . trim($keyword) . '%';
            }

            if (!empty($trangThai)) {
                $where[] = "p.trangThai = :trangThai";
                $params[':trangThai'] = trim($trangThai);
            }

            $whereSql = implode(' AND ', $where);

            $sql = "
                SELECT 
                    p.maPhieuYeuCau,
                    p.loaiYeuCau,
                    p.donViYeuCau,
                    p.ngayYeuCau,
                    p.nguoiYeuCau,
                    p.lyDoXuat,
                    p.tongSoLuong,
                    p.trangThai,
                    COUNT(ct.maChiTiet) AS soMatHang,
                    GROUP_CONCAT(CONCAT(ct.tenMatHang, ' (', FORMAT(ct.soLuongYeuCau, 0), ' ', ct.donViTinh, ')') SEPARATOR '; ') AS danhSachMatHang,
                    (
                        SELECT l.maLenh 
                        FROM lenhdieuphoixuat l 
                        WHERE l.maPhieuYeuCau = p.maPhieuYeuCau 
                        ORDER BY l.ngayDieuPhoi DESC 
                        LIMIT 1
                    ) AS maLenhDieuPhoi
                FROM phieuyeucauxuat p
                LEFT JOIN chitietyeucauxuat ct ON p.maPhieuYeuCau = ct.maPhieuYeuCau
                WHERE {$whereSql}
                GROUP BY p.maPhieuYeuCau
                ORDER BY 
                    CASE 
                        WHEN p.trangThai IN ('Chờ điều phối', 'Chờ xử lý') THEN 0 
                        WHEN p.trangThai = 'Đã điều phối' THEN 1 
                        ELSE 2 
                    END ASC,
                    p.ngayYeuCau DESC,
                    p.maPhieuYeuCau DESC
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi mDieuPhoiXuatTP::getDanhSachPhieuYeuCau: " . $e->getMessage());
            throw new Exception("Lỗi khi tải danh sách phiếu yêu cầu: " . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin chi tiết một Phiếu yêu cầu xuất kèm danh sách mặt hàng
     */
    public function getChiTietPhieuYeuCau($maPhieuYeuCau) {
        try {
            $sqlPhieu = "
                SELECT 
                    p.maPhieuYeuCau,
                    p.loaiYeuCau,
                    p.donViYeuCau,
                    p.ngayYeuCau,
                    p.nguoiYeuCau,
                    p.lyDoXuat,
                    p.tongSoLuong,
                    p.trangThai
                FROM phieuyeucauxuat p
                WHERE p.maPhieuYeuCau = :maPYC AND p.loaiYeuCau = 'TP'
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($sqlPhieu);
            $stmt->execute([':maPYC' => $maPhieuYeuCau]);
            $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$phieu) {
                return null;
            }

            $sqlCT = "
                SELECT 
                    ct.maChiTiet,
                    ct.maPhieuYeuCau,
                    ct.maMatHang,
                    ct.tenMatHang,
                    ct.soLuongYeuCau,
                    ct.donViTinh,
                    tp.quyCachDongGoi,
                    tp.tenNhom
                FROM chitietyeucauxuat ct
                LEFT JOIN thanhpham tp ON ct.maMatHang = tp.maTP
                WHERE ct.maPhieuYeuCau = :maPYC
                ORDER BY ct.maChiTiet ASC
            ";
            $stmtCT = $this->conn->prepare($sqlCT);
            $stmtCT->execute([':maPYC' => $maPhieuYeuCau]);
            $phieu['matHang'] = $stmtCT->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra xem đã có Lệnh điều phối xuất kho chưa
            $sqlLenh = "
                SELECT 
                    l.maLenh,
                    l.maPhieuYeuCau,
                    l.maKho,
                    k.tenKho,
                    l.ngayDieuPhoi,
                    l.nguoiDieuPhoi,
                    nv.hoTen AS tenNguoiDieuPhoi,
                    l.ghiChu,
                    l.trangThai
                FROM lenhdieuphoixuat l
                LEFT JOIN kho k ON l.maKho = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                WHERE l.maPhieuYeuCau = :maPYC
                ORDER BY l.ngayDieuPhoi DESC
                LIMIT 1
            ";
            $stmtLenh = $this->conn->prepare($sqlLenh);
            $stmtLenh->execute([':maPYC' => $maPhieuYeuCau]);
            $phieu['lenhDieuPhoi'] = $stmtLenh->fetch(PDO::FETCH_ASSOC);

            return $phieu;
        } catch (PDOException $e) {
            error_log("Lỗi mDieuPhoiXuatTP::getChiTietPhieuYeuCau: " . $e->getMessage());
            throw new Exception("Lỗi khi tải chi tiết phiếu yêu cầu: " . $e->getMessage());
        }
    }

    /**
     * TÍNH TOÁN PHÂN BỔ LÔ THÀNH PHẨM XUẤT KHO THEO THUẬT TOÁN FEFO
     * - Tiêu chí FEFO: Lô có Hạn sử dụng gần nhất (gần ngày hiện tại nhất nhưng chưa hết hạn) được xuất trước.
     * - Điều kiện lô hợp lệ:
     *   1. Thuộc loại hàng 'TP' và đúng mã thành phẩm (maTP)
     *   2. Đã kiểm định đạt chất lượng (trangThaiKiemDinh = 'Đạt' hoặc trangThai = 'Đã lưu kho')
     *   3. Chưa hết hạn sử dụng (hanSuDung >= CURDATE())
     *   4. Trạng thái vị trí và tồn kho 'Khả dụng'
     *   5. Tồn khả dụng thực tế > 0: (slTon - soLuongTamGiu) > 0
     * - Sắp xếp: lotp.hanSuDung ASC, tk.maLo ASC, tk.maTonKho ASC
     */
    public function tinhToanPhanBoFEFO($maPhieuYeuCau) {
        $phieu = $this->getChiTietPhieuYeuCau($maPhieuYeuCau);
        if (!$phieu) {
            throw new Exception("Không tìm thấy phiếu yêu cầu xuất: " . htmlspecialchars($maPhieuYeuCau));
        }

        $isDaDieuPhoi = in_array($phieu['trangThai'], ['Đã điều phối', 'Đã xuất kho']);
        $result = [
            'phieu'                => $phieu,
            'isDaDieuPhoi'         => $isDaDieuPhoi,
            'isDuTonToanBo'        => true,
            'tongSoLuongYeuCau'    => (float)$phieu['tongSoLuong'],
            'tongSoLuongPhanBo'    => 0,
            'tongSoLuongKhaDung'   => 0,
            'danhSachMatHang'      => []
        ];

        // Nếu phiếu đã được điều phối, đọc chi tiết phân bổ đã lưu trong CSDL
        if ($isDaDieuPhoi && !empty($phieu['lenhDieuPhoi']['maLenh'])) {
            $maLenh = $phieu['lenhDieuPhoi']['maLenh'];
            $sqlDaLuu = "
                SELECT 
                    ct.maChiTiet,
                    ct.maLenh,
                    ct.maLo,
                    ct.maViTri,
                    ct.soLuongPhanBo,
                    lotp.maTP,
                    tp.tenTP,
                    tp.donViTinh,
                    lotp.hanSuDung,
                    lotp.ngaySanXuat,
                    lotp.phanXuong,
                    vk.day,
                    vk.ke,
                    vk.tang,
                    k.maKho,
                    k.tenKho,
                    tk.slTon,
                    tk.soLuongTamGiu,
                    (tk.slTon - tk.soLuongTamGiu) AS slKhaDungHienTai
                FROM chitietdieuphoixuat ct
                INNER JOIN lenhdieuphoixuat l ON ct.maLenh = l.maLenh
                INNER JOIN lothanhpham lotp ON ct.maLo = lotp.maLoTP
                INNER JOIN thanhpham tp ON lotp.maTP = tp.maTP
                INNER JOIN vitrikho vk ON ct.maViTri = vk.maViTri
                INNER JOIN kho k ON vk.maKho = k.maKho
                LEFT JOIN tonkho tk ON (tk.maLo = ct.maLo AND tk.maViTri = ct.maViTri AND tk.loaiHang = 'TP')
                WHERE ct.maLenh = :maLenh
                ORDER BY lotp.hanSuDung ASC, ct.maChiTiet ASC
            ";
            $stmtDaLuu = $this->conn->prepare($sqlDaLuu);
            $stmtDaLuu->execute([':maLenh' => $maLenh]);
            $chiTietDaLuu = $stmtDaLuu->fetchAll(PDO::FETCH_ASSOC);

            // Gom nhóm theo mã mặt hàng
            $groupedSaved = [];
            foreach ($chiTietDaLuu as $row) {
                $maTP = $row['maTP'];
                if (!isset($groupedSaved[$maTP])) {
                    $groupedSaved[$maTP] = [];
                }
                $groupedSaved[$maTP][] = $row;
            }

            foreach ($phieu['matHang'] as $mh) {
                $maTP = $mh['maMatHang'];
                $slYeuCau = (float)$mh['soLuongYeuCau'];
                $rows = $groupedSaved[$maTP] ?? [];

                $tongPhanBoItem = 0;
                $loList = [];
                $stt = 1;

                foreach ($rows as $r) {
                    $slPB = (float)$r['soLuongPhanBo'];
                    $tongPhanBoItem += $slPB;
                    $loList[] = [
                        'stt'              => $stt++,
                        'maTonKho'         => $r['maChiTiet'],
                        'maLo'             => $r['maLo'],
                        'hanSuDung'        => $r['hanSuDung'],
                        'ngaySanXuat'      => $r['ngaySanXuat'],
                        'phanXuong'        => $r['phanXuong'],
                        'maViTri'          => $r['maViTri'],
                        'viTriHienThi'     => "{$r['tenKho']} / Dãy {$r['day']} • Kệ {$r['ke']} • Tầng {$r['tang']}",
                        'slTon'            => (float)$r['slTon'],
                        'slKhaDung'        => (float)$r['slKhaDungHienTai'] + $slPB,
                        'soLuongPhanBo'    => $slPB,
                        'ghiChuFEFO'       => $stt === 2 ? 'Lô xuất thực tế (FEFO)' : "Ưu tiên {$stt}"
                    ];
                }

                $result['tongSoLuongPhanBo'] += $tongPhanBoItem;
                $result['tongSoLuongKhaDung'] += $tongPhanBoItem;

                $result['danhSachMatHang'][] = [
                    'maMatHang'        => $maTP,
                    'tenMatHang'       => $mh['tenMatHang'],
                    'donViTinh'        => $mh['donViTinh'],
                    'soLuongYeuCau'    => $slYeuCau,
                    'tongTonKhaDung'   => $tongPhanBoItem,
                    'soLuongDaPhanBo'  => $tongPhanBoItem,
                    'soLuongThieu'     => 0,
                    'isDuTon'          => true,
                    'tyLePhanBo'       => 100.0,
                    'danhSachLo'       => $loList
                ];
            }

            return $result;
        }

        // Trường hợp phiếu "Chờ điều phối": Thực hiện thuật toán tự động FEFO
        $sqlLoFEFO = "
            SELECT 
                tk.maTonKho,
                tk.maLo,
                tk.maViTri,
                tk.slTon,
                tk.soLuongTamGiu,
                (tk.slTon - tk.soLuongTamGiu) AS slKhaDung,
                lotp.maLoTP,
                lotp.maTP,
                tp.tenTP,
                lotp.phanXuong,
                lotp.ngaySanXuat,
                lotp.hanSuDung,
                lotp.trangThaiKiemDinh,
                lotp.trangThai AS trangThaiLo,
                vk.maKho,
                vk.day,
                vk.ke,
                vk.tang,
                k.tenKho,
                DATEDIFF(lotp.hanSuDung, CURDATE()) AS soNgayConHan
            FROM tonkho tk
            INNER JOIN lothanhpham lotp ON tk.maLo = lotp.maLoTP
            INNER JOIN thanhpham tp ON lotp.maTP = tp.maTP
            INNER JOIN vitrikho vk ON tk.maViTri = vk.maViTri
            INNER JOIN kho k ON vk.maKho = k.maKho
            WHERE tk.loaiHang = 'TP'
              AND lotp.maTP = :maTP
              AND (lotp.trangThaiKiemDinh = 'Đạt' OR lotp.trangThai = 'Đã lưu kho')
              AND (lotp.hanSuDung IS NULL OR lotp.hanSuDung >= CURDATE())
              AND tk.trangThai = 'Khả dụng'
              AND (tk.slTon - tk.soLuongTamGiu) > 0
            ORDER BY 
                lotp.hanSuDung ASC,
                tk.maLo ASC,
                tk.maTonKho ASC
        ";
        $stmtLo = $this->conn->prepare($sqlLoFEFO);

        foreach ($phieu['matHang'] as $mh) {
            $maTP = $mh['maMatHang'];
            $slYeuCau = (float)$mh['soLuongYeuCau'];

            $stmtLo->execute([':maTP' => $maTP]);
            $availableLots = $stmtLo->fetchAll(PDO::FETCH_ASSOC);

            // Tính tổng tồn khả dụng của mặt hàng này
            $tongTonKhaDungItem = 0.0;
            foreach ($availableLots as $lot) {
                $tongTonKhaDungItem += (float)$lot['slKhaDung'];
            }

            $isDuTonItem = ($tongTonKhaDungItem >= $slYeuCau);
            if (!$isDuTonItem) {
                $result['isDuTonToanBo'] = false;
            }

            // Thuật toán rót số lượng theo FEFO
            $soLuongCanPhanBo = $slYeuCau;
            $loPhanBoList = [];
            $stt = 1;
            $firstHsd = null;

            foreach ($availableLots as $idx => $lot) {
                $slKhaDung = (float)$lot['slKhaDung'];
                $slPhanBo = 0.0;

                if ($soLuongCanPhanBo > 0) {
                    $slPhanBo = min($soLuongCanPhanBo, $slKhaDung);
                    $soLuongCanPhanBo -= $slPhanBo;
                }

                // Ghi chú FEFO trực quan
                if ($stt === 1) {
                    $firstHsd = $lot['hanSuDung'];
                    $ghiChuFEFO = 'Ưu tiên 1 (HSD gần nhất)';
                } else {
                    $diffDays = 0;
                    if ($firstHsd && $lot['hanSuDung']) {
                        $diffDays = (int)round((strtotime($lot['hanSuDung']) - strtotime($firstHsd)) / 86400);
                    }
                    $ghiChuFEFO = $diffDays > 0 ? "Ưu tiên {$stt} (Sau {$diffDays} ngày)" : "Ưu tiên {$stt}";
                }

                $loPhanBoList[] = [
                    'stt'              => $stt++,
                    'maTonKho'         => $lot['maTonKho'],
                    'maLo'             => $lot['maLo'],
                    'maTP'             => $maTP,
                    'hanSuDung'        => $lot['hanSuDung'],
                    'ngaySanXuat'      => $lot['ngaySanXuat'],
                    'phanXuong'        => $lot['phanXuong'],
                    'maViTri'          => $lot['maViTri'],
                    'maKho'            => $lot['maKho'],
                    'tenKho'           => $lot['tenKho'],
                    'viTriHienThi'     => "{$lot['tenKho']} / Dãy {$lot['day']} • Kệ {$lot['ke']} • Tầng {$lot['tang']}",
                    'slTon'            => (float)$lot['slTon'],
                    'slKhaDung'        => $slKhaDung,
                    'soLuongPhanBo'    => $slPhanBo,
                    'soNgayConHan'     => (int)$lot['soNgayConHan'],
                    'ghiChuFEFO'       => $ghiChuFEFO
                ];
            }

            $soLuongDaPhanBoItem = $slYeuCau - max(0, $soLuongCanPhanBo);
            $soLuongThieuItem = max(0, $slYeuCau - $tongTonKhaDungItem);
            $tyLe = $slYeuCau > 0 ? round(($soLuongDaPhanBoItem / $slYeuCau) * 100, 1) : 100;

            $result['tongSoLuongPhanBo'] += $soLuongDaPhanBoItem;
            $result['tongSoLuongKhaDung'] += $tongTonKhaDungItem;

            $result['danhSachMatHang'][] = [
                'maMatHang'        => $maTP,
                'tenMatHang'       => $mh['tenMatHang'],
                'donViTinh'        => $mh['donViTinh'],
                'soLuongYeuCau'    => $slYeuCau,
                'tongTonKhaDung'   => $tongTonKhaDungItem,
                'soLuongDaPhanBo'  => $soLuongDaPhanBoItem,
                'soLuongThieu'     => $soLuongThieuItem,
                'isDuTon'          => $isDuTonItem,
                'tyLePhanBo'       => $tyLe,
                'danhSachLo'       => $loPhanBoList
            ];
        }

        return $result;
    }

    /**
     * XÁC NHẬN ĐIỀU PHỐI XUẤT KHO THÀNH PHẨM (TRANSACTION)
     * Các bước nghiệp vụ:
     * 1. Khóa bản ghi phieuyeucauxuat (FOR UPDATE) để tránh xung đột đồng thời (Concurrency check - 7.2)
     * 2. Kiểm tra trạng thái phiếu phải là 'Chờ điều phối' hoặc 'Chờ xử lý'
     * 3. Tính toán lại FEFO với lượng tồn kho mới nhất (FOR UPDATE trên bảng tonkho)
     * 4. Kiểm tra tồn khả dụng mới nhất có đáp ứng đủ 100% không. Nếu thiếu -> rollback, báo lỗi (4.1)
     * 5. Tạo mã Lệnh điều phối xuất kho: LDPX_TP_YYYYMMDD_XXX
     * 6. Lưu vào lenhdieuphoixuat với trạng thái 'Chờ xuất kho'
     * 7. Lưu từng dòng phân bổ vào chitietdieuphoixuat
     * 8. Cập nhật giữ tồn kho: tonkho.soLuongTamGiu = soLuongTamGiu + soLuongPhanBo
     * 9. Cập nhật phieuyeucauxuat.trangThai = 'Đã điều phối'
     * 10. Commit transaction
     */
    public function xacNhanDieuPhoi($maPhieuYeuCau, $maNV, $ghiChu = '') {
        try {
            $this->conn->beginTransaction();

            // 1. Kiểm tra trạng thái mới nhất của phiếu yêu cầu trong CSDL (Khóa FOR UPDATE)
            $sqlCheck = "
                SELECT maPhieuYeuCau, trangThai, donViYeuCau, tongSoLuong 
                FROM phieuyeucauxuat 
                WHERE maPhieuYeuCau = :maPYC AND loaiYeuCau = 'TP'
                FOR UPDATE
            ";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([':maPYC' => $maPhieuYeuCau]);
            $phieu = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$phieu) {
                throw new Exception("Phiếu yêu cầu xuất kho '{$maPhieuYeuCau}' không tồn tại trong hệ thống!");
            }

            if (!in_array($phieu['trangThai'], ['Chờ điều phối', 'Chờ xử lý'])) {
                throw new Exception("Phiếu yêu cầu này đã được xử lý bởi người dùng khác hoặc không còn ở trạng thái chờ điều phối (Trạng thái hiện tại: {$phieu['trangThai']}). Vui lòng tải lại danh sách!");
            }

            // 2. Lấy danh sách mặt hàng cần xuất
            $sqlCT = "SELECT maMatHang, tenMatHang, soLuongYeuCau, donViTinh FROM chitietyeucauxuat WHERE maPhieuYeuCau = :maPYC";
            $stmtCT = $this->conn->prepare($sqlCT);
            $stmtCT->execute([':maPYC' => $maPhieuYeuCau]);
            $items = $stmtCT->fetchAll(PDO::FETCH_ASSOC);

            if (empty($items)) {
                throw new Exception("Phiếu yêu cầu '{$maPhieuYeuCau}' không có mặt hàng nào cần xuất!");
            }

            // 3. Khóa các dòng tồn kho liên quan và phân bổ theo FEFO
            $sqlLoLock = "
                SELECT 
                    tk.maTonKho,
                    tk.maLo,
                    tk.maViTri,
                    tk.slTon,
                    tk.soLuongTamGiu,
                    (tk.slTon - tk.soLuongTamGiu) AS slKhaDung,
                    lotp.maLoTP,
                    lotp.maTP,
                    tp.tenTP,
                    lotp.hanSuDung,
                    vk.maKho
                FROM tonkho tk
                INNER JOIN lothanhpham lotp ON tk.maLo = lotp.maLoTP
                INNER JOIN thanhpham tp ON lotp.maTP = tp.maTP
                INNER JOIN vitrikho vk ON tk.maViTri = vk.maViTri
                WHERE tk.loaiHang = 'TP'
                  AND lotp.maTP = :maTP
                  AND (lotp.trangThaiKiemDinh = 'Đạt' OR lotp.trangThai = 'Đã lưu kho')
                  AND (lotp.hanSuDung IS NULL OR lotp.hanSuDung >= CURDATE())
                  AND tk.trangThai = 'Khả dụng'
                  AND (tk.slTon - tk.soLuongTamGiu) > 0
                ORDER BY 
                    lotp.hanSuDung ASC,
                    tk.maLo ASC,
                    tk.maTonKho ASC
                FOR UPDATE
            ";
            $stmtLoLock = $this->conn->prepare($sqlLoLock);

            $allocationList = [];
            $primaryKho = null;

            foreach ($items as $it) {
                $maTP = $it['maMatHang'];
                $slYeuCau = (float)$it['soLuongYeuCau'];

                $stmtLoLock->execute([':maTP' => $maTP]);
                $lots = $stmtLoLock->fetchAll(PDO::FETCH_ASSOC);

                $tongKhaDung = 0.0;
                foreach ($lots as $l) {
                    $tongKhaDung += (float)$l['slKhaDung'];
                }

                if ($tongKhaDung < $slYeuCau) {
                    $thieu = $slYeuCau - $tongKhaDung;
                    throw new Exception("Không đủ số lượng tồn khả dụng cho mặt hàng: {$it['tenMatHang']} (Mã: {$maTP}). Cần xuất: " . number_format($slYeuCau, 0) . " {$it['donViTinh']}, tồn khả dụng hiện có: " . number_format($tongKhaDung, 0) . " {$it['donViTinh']}, còn thiếu: " . number_format($thieu, 0) . " {$it['donViTinh']}. Hệ thống không thể điều phối!");
                }

                $need = $slYeuCau;
                foreach ($lots as $l) {
                    if ($need <= 0) break;
                    $alloc = min($need, (float)$l['slKhaDung']);
                    if ($alloc > 0) {
                        $allocationList[] = [
                            'maTonKho'      => $l['maTonKho'],
                            'maLo'          => $l['maLo'],
                            'maViTri'       => $l['maViTri'],
                            'soLuongPhanBo' => $alloc,
                            'maKho'         => $l['maKho']
                        ];
                        if (!$primaryKho) {
                            $primaryKho = $l['maKho'];
                        }
                        $need -= $alloc;
                    }
                }
            }

            if (empty($allocationList)) {
                throw new Exception("Không có dòng phân bổ nào được tạo!");
            }

            if (!$primaryKho) {
                $primaryKho = 'KHO_TP_01';
            }

            // 4. Sinh mã Lệnh điều phối xuất kho duy nhất: LDPX_TP_YYYYMMDD_XXX
            $today = date('Ymd');
            $prefix = "LDPX_TP_{$today}_";
            $sqlMaxLenh = "SELECT maLenh FROM lenhdieuphoixuat WHERE maLenh LIKE :prefix ORDER BY maLenh DESC LIMIT 1 FOR UPDATE";
            $stmtMaxLenh = $this->conn->prepare($sqlMaxLenh);
            $stmtMaxLenh->execute([':prefix' => $prefix . '%']);
            $lastMaLenh = $stmtMaxLenh->fetchColumn();

            $seq = 1;
            if ($lastMaLenh && preg_match('/_(\d+)$/', $lastMaLenh, $m)) {
                $seq = (int)$m[1] + 1;
            }
            $maLenh = $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);

            // Ghi chú mặc định nếu không truyền
            if (empty($ghiChu)) {
                $ghiChu = "Điều phối xuất kho tự động theo thuật toán FEFO cho phiếu yêu cầu {$maPhieuYeuCau}";
            }

            // 5. Lưu vào bảng lenhdieuphoixuat
            $sqlInsertLenh = "
                INSERT INTO lenhdieuphoixuat (
                    maLenh, maPhieuYeuCau, maKho, ngayDieuPhoi, nguoiDieuPhoi, ghiChu, trangThai
                ) VALUES (
                    :maLenh, :maPYC, :maKho, NOW(), :nguoiDieuPhoi, :ghiChu, 'Chờ xuất kho'
                )
            ";
            $stmtInsertLenh = $this->conn->prepare($sqlInsertLenh);
            $stmtInsertLenh->execute([
                ':maLenh'        => $maLenh,
                ':maPYC'         => $maPhieuYeuCau,
                ':maKho'         => $primaryKho,
                ':nguoiDieuPhoi' => $maNV,
                ':ghiChu'        => $ghiChu
            ]);

            // 6. Lưu vào chitietdieuphoixuat và cập nhật giữ tồn kho
            $prefixCT = "CTDPX_{$today}_";
            $sqlMaxCT = "SELECT maChiTiet FROM chitietdieuphoixuat WHERE maChiTiet LIKE :pfx ORDER BY maChiTiet DESC LIMIT 1 FOR UPDATE";
            $stmtMaxCT = $this->conn->prepare($sqlMaxCT);
            $stmtMaxCT->execute([':pfx' => $prefixCT . '%']);
            $lastCT = $stmtMaxCT->fetchColumn();

            $seqCT = 1;
            if ($lastCT && preg_match('/_(\d+)$/', $lastCT, $mCT)) {
                $seqCT = (int)$mCT[1] + 1;
            }

            $sqlInsertCT = "
                INSERT INTO chitietdieuphoixuat (
                    maChiTiet, maLenh, maLo, maViTri, soLuongPhanBo
                ) VALUES (
                    :maChiTiet, :maLenh, :maLo, :maViTri, :soLuongPhanBo
                )
            ";
            $stmtInsertCT = $this->conn->prepare($sqlInsertCT);

            $sqlUpdateTon = "
                UPDATE tonkho 
                SET soLuongTamGiu = soLuongTamGiu + :alloc,
                    ngayCapNhat = NOW()
                WHERE maTonKho = :maTonKho
            ";
            $stmtUpdateTon = $this->conn->prepare($sqlUpdateTon);

            foreach ($allocationList as $row) {
                $maChiTiet = $prefixCT . str_pad($seqCT++, 4, '0', STR_PAD_LEFT);
                $stmtInsertCT->execute([
                    ':maChiTiet'     => $maChiTiet,
                    ':maLenh'        => $maLenh,
                    ':maLo'          => $row['maLo'],
                    ':maViTri'       => $row['maViTri'],
                    ':soLuongPhanBo' => $row['soLuongPhanBo']
                ]);

                $stmtUpdateTon->execute([
                    ':alloc'    => $row['soLuongPhanBo'],
                    ':maTonKho' => $row['maTonKho']
                ]);
            }

            // 7. Cập nhật trạng thái phiếu yêu cầu xuất thành 'Đã điều phối'
            $sqlUpdatePhieu = "UPDATE phieuyeucauxuat SET trangThai = 'Đã điều phối' WHERE maPhieuYeuCau = :maPYC";
            $stmtUpdatePhieu = $this->conn->prepare($sqlUpdatePhieu);
            $stmtUpdatePhieu->execute([':maPYC' => $maPhieuYeuCau]);

            // 8. Hoàn tất giao dịch
            $this->conn->commit();

            return [
                'success' => true,
                'message' => "Điều phối xuất kho thành công! Đã ban hành Lệnh điều phối {$maLenh} chuyển tiếp Nhân viên kho thực hiện.",
                'maLenh'  => $maLenh,
                'maPhieuYeuCau' => $maPhieuYeuCau
            ];
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Lỗi mDieuPhoiXuatTP::xacNhanDieuPhoi: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy dữ liệu đầy đủ của một Lệnh điều phối để in ấn chứng từ A4
     */
    public function getThongTinInLenhDieuPhoi($maLenh) {
        try {
            $sqlLenh = "
                SELECT 
                    l.maLenh,
                    l.maPhieuYeuCau,
                    l.maKho,
                    k.tenKho,
                    k.diaChi AS diaChiKho,
                    l.ngayDieuPhoi,
                    l.nguoiDieuPhoi,
                    nv.hoTen AS tenNguoiDieuPhoi,
                    nv.soDienThoai AS sdtNguoiDieuPhoi,
                    l.ghiChu,
                    l.trangThai,
                    p.donViYeuCau,
                    p.nguoiYeuCau,
                    p.lyDoXuat,
                    p.ngayYeuCau,
                    p.tongSoLuong
                FROM lenhdieuphoixuat l
                INNER JOIN phieuyeucauxuat p ON l.maPhieuYeuCau = p.maPhieuYeuCau
                LEFT JOIN kho k ON l.maKho = k.maKho
                LEFT JOIN nhanvien nv ON l.nguoiDieuPhoi = nv.maNV
                WHERE l.maLenh = :maLenh
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($sqlLenh);
            $stmt->execute([':maLenh' => $maLenh]);
            $lenh = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$lenh) {
                return null;
            }

            $sqlChiTiet = "
                SELECT 
                    ct.maChiTiet,
                    ct.maLo,
                    ct.maViTri,
                    ct.soLuongPhanBo,
                    lotp.maTP,
                    tp.tenTP,
                    tp.donViTinh,
                    lotp.hanSuDung,
                    lotp.ngaySanXuat,
                    lotp.phanXuong,
                    vk.day,
                    vk.ke,
                    vk.tang
                FROM chitietdieuphoixuat ct
                INNER JOIN lothanhpham lotp ON ct.maLo = lotp.maLoTP
                INNER JOIN thanhpham tp ON lotp.maTP = tp.maTP
                INNER JOIN vitrikho vk ON ct.maViTri = vk.maViTri
                WHERE ct.maLenh = :maLenh
                ORDER BY lotp.hanSuDung ASC, ct.maChiTiet ASC
            ";
            $stmtCT = $this->conn->prepare($sqlChiTiet);
            $stmtCT->execute([':maLenh' => $maLenh]);
            $lenh['chiTiet'] = $stmtCT->fetchAll(PDO::FETCH_ASSOC);

            return $lenh;
        } catch (PDOException $e) {
            error_log("Lỗi mDieuPhoiXuatTP::getThongTinInLenhDieuPhoi: " . $e->getMessage());
            throw new Exception("Lỗi khi tải thông tin in lệnh: " . $e->getMessage());
        }
    }
}
