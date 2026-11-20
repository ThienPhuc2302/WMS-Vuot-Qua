<?php
class mDieuPhoiXuatNL {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getDanhSachYeuCau() {
        $sql = "
            SELECT
                p.maPhieuYeuCau,
                p.donViYeuCau,
                p.ngayYeuCau,
                p.nguoiYeuCau,
                p.lyDoXuat,
                p.trangThai,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM lenhdieuphoixuat l
                        JOIN phieuxuatkho px ON px.maLenhDieuPhoi = l.maLenh
                        WHERE l.maPhieuYeuCau = p.maPhieuYeuCau
                    ) THEN 'Đã xuất kho'
                    WHEN EXISTS (
                        SELECT 1
                        FROM lenhdieuphoixuat l
                        WHERE l.maPhieuYeuCau = p.maPhieuYeuCau
                    ) THEN 'Đã điều phối'
                    ELSE p.trangThai
                END AS trangThaiHienThi,
                c.maChiTiet,
                c.tenMatHang,
                c.soLuongYeuCau,
                c.donViTinh
            FROM phieuyeucauxuat p
            LEFT JOIN chitietyeucauxuat c ON c.maPhieuYeuCau = p.maPhieuYeuCau
            WHERE p.loaiYeuCau = 'NL'
            ORDER BY p.ngayYeuCau DESC, p.maPhieuYeuCau DESC, c.maChiTiet ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $requests = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id = $row['maPhieuYeuCau'];
            if (!isset($requests[$id])) {
                $requests[$id] = [
                    'maPhieuYeuCau' => $id,
                    'donViYeuCau' => $row['donViYeuCau'],
                    'ngayYeuCau' => $row['ngayYeuCau'],
                    'nguoiYeuCau' => $row['nguoiYeuCau'],
                    'lyDoXuat' => $row['lyDoXuat'],
                    'trangThai' => $row['trangThaiHienThi'],
                    'chiTiet' => []
                ];
            }

            if ($row['maChiTiet'] !== null) {
                $requests[$id]['chiTiet'][] = [
                    'tenMatHang' => $row['tenMatHang'],
                    'soLuongYeuCau' => (float)$row['soLuongYeuCau'],
                    'donViTinh' => $row['donViTinh']
                ];
            }
        }

        return array_values($requests);
    }

    public function getYeuCau($maPhieuYeuCau) {
        $stmt = $this->conn->prepare("
            SELECT
                p.maPhieuYeuCau,
                p.donViYeuCau,
                p.ngayYeuCau,
                p.nguoiYeuCau,
                p.lyDoXuat,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM lenhdieuphoixuat l
                        JOIN phieuxuatkho px ON px.maLenhDieuPhoi = l.maLenh
                        WHERE l.maPhieuYeuCau = p.maPhieuYeuCau
                    ) THEN 'Đã xuất kho'
                    WHEN EXISTS (
                        SELECT 1 FROM lenhdieuphoixuat l
                        WHERE l.maPhieuYeuCau = p.maPhieuYeuCau
                    ) THEN 'Đã điều phối'
                    ELSE p.trangThai
                END AS trangThai
            FROM phieuyeucauxuat p
            WHERE p.maPhieuYeuCau = :maPhieuYeuCau AND p.loaiYeuCau = 'NL'
            LIMIT 1
        ");
        $stmt->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            return null;
        }

        $details = $this->conn->prepare("
            SELECT maChiTiet, maMatHang, tenMatHang, soLuongYeuCau, donViTinh
            FROM chitietyeucauxuat
            WHERE maPhieuYeuCau = :maPhieuYeuCau
            ORDER BY maChiTiet ASC
        ");
        $details->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
        $request['chiTiet'] = $details->fetchAll(PDO::FETCH_ASSOC);

        return $request;
    }

    public function getDeXuatFefo($maPhieuYeuCau) {
        $request = $this->getYeuCau($maPhieuYeuCau);
        if (!$request || $request['trangThai'] !== 'Chờ điều phối') {
            return [];
        }

        return $this->phanBoFefo($request['chiTiet'], $this->getTonKhoNguyenLieu(false));
    }

    public function taoLenhDieuPhoi($maPhieuYeuCau, $maNV) {
        if (empty($maPhieuYeuCau) || empty($maNV)) {
            throw new DomainException('Thiếu thông tin phiếu yêu cầu hoặc nhân viên điều phối.');
        }

        $this->conn->beginTransaction();
        try {
            $requestStmt = $this->conn->prepare("
                SELECT maPhieuYeuCau, trangThai
                FROM phieuyeucauxuat
                WHERE maPhieuYeuCau = :maPhieuYeuCau AND loaiYeuCau = 'NL'
                LIMIT 1
                FOR UPDATE
            ");
            $requestStmt->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
            $request = $requestStmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new DomainException('Không tìm thấy phiếu yêu cầu xuất nguyên liệu.');
            }
            if ($request['trangThai'] !== 'Chờ điều phối') {
                throw new DomainException('Phiếu này không còn ở trạng thái chờ điều phối hoặc đã được xử lý.');
            }

            $existingCommandStmt = $this->conn->prepare("
                SELECT maLenh
                FROM lenhdieuphoixuat
                WHERE maPhieuYeuCau = :maPhieuYeuCau
                LIMIT 1
                FOR UPDATE
            ");
            $existingCommandStmt->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
            if ($existingCommandStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new DomainException('Phiếu này đã có lệnh điều phối, không thể tạo lệnh trùng.');
            }

            $detailStmt = $this->conn->prepare("
                SELECT maChiTiet, maMatHang, tenMatHang, soLuongYeuCau, donViTinh
                FROM chitietyeucauxuat
                WHERE maPhieuYeuCau = :maPhieuYeuCau
                ORDER BY maChiTiet ASC
            ");
            $detailStmt->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
            $details = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$details) {
                throw new DomainException('Phiếu yêu cầu chưa có mặt hàng nguyên liệu để điều phối.');
            }

            $stock = $this->getTonKhoNguyenLieu(true);
            $allocations = $this->phanBoFefo($details, $stock);
            $shortages = [];
            foreach ($allocations as $line) {
                if ($line['conThieu'] > 0) {
                    $shortages[] = $line['tenMatHang'] . ': thiếu ' .
                        number_format($line['conThieu'], 2, ',', '.') . ' ' . $line['donViTinh'];
                }
            }
            if ($shortages) {
                throw new DomainException('Không đủ tồn khả dụng để điều phối. ' . implode('; ', $shortages) . '.');
            }

            $allocationsByWarehouse = [];
            foreach ($allocations as $line) {
                foreach ($line['phanBo'] as $allocation) {
                    $warehouseId = $allocation['maKho'];
                    if (!isset($allocationsByWarehouse[$warehouseId])) {
                        $allocationsByWarehouse[$warehouseId] = [];
                    }
                    $allocationsByWarehouse[$warehouseId][] = $allocation;
                }
            }

            foreach ($allocationsByWarehouse as $warehouseId => $warehouseAllocations) {
                $commandId = 'LDPXNL_' . date('Ymd') . '_' . strtoupper(bin2hex(random_bytes(5)));
                $commandStmt = $this->conn->prepare("
                    INSERT INTO lenhdieuphoixuat
                        (maLenh, maPhieuYeuCau, maKho, nguoiDieuPhoi, ghiChu, trangThai)
                    VALUES
                        (:maLenh, :maPhieuYeuCau, :maKho, :nguoiDieuPhoi, :ghiChu, 'Chờ xuất kho')
                ");
                $commandStmt->execute([
                    ':maLenh' => $commandId,
                    ':maPhieuYeuCau' => $maPhieuYeuCau,
                    ':maKho' => $warehouseId,
                    ':nguoiDieuPhoi' => $maNV,
                    ':ghiChu' => 'Phân bổ FEFO cho phiếu ' . $maPhieuYeuCau
                ]);

                foreach ($warehouseAllocations as $index => $allocation) {
                    $reserveStmt = $this->conn->prepare("
                        UPDATE tonkho
                        SET soLuongTamGiu = COALESCE(soLuongTamGiu, 0) + :soLuongGiu,
                            ngayCapNhat = CURRENT_TIMESTAMP
                        WHERE maTonKho = :maTonKho
                          AND trangThai = 'Khả dụng'
                          AND slTon - COALESCE(soLuongTamGiu, 0) >= :soLuongKiemTra
                    ");
                    $reserveStmt->execute([
                        ':soLuongGiu' => $allocation['soLuongPhanBo'],
                        ':maTonKho' => $allocation['maTonKho'],
                        ':soLuongKiemTra' => $allocation['soLuongPhanBo']
                    ]);
                    if ($reserveStmt->rowCount() !== 1) {
                        throw new DomainException('Tồn kho vừa thay đổi trong lúc điều phối. Vui lòng tải lại và thử lại.');
                    }

                    $detailId = 'CTDPXNL_' . strtoupper(bin2hex(random_bytes(5)));
                    $allocationStmt = $this->conn->prepare("
                        INSERT INTO chitietdieuphoixuat
                            (maChiTiet, maLenh, maLo, maViTri, soLuongPhanBo)
                        VALUES
                            (:maChiTiet, :maLenh, :maLo, :maViTri, :soLuongPhanBo)
                    ");
                    $allocationStmt->execute([
                        ':maChiTiet' => $detailId,
                        ':maLenh' => $commandId,
                        ':maLo' => $allocation['maLoNL'],
                        ':maViTri' => $allocation['maViTri'],
                        ':soLuongPhanBo' => $allocation['soLuongPhanBo']
                    ]);
                }
            }

            $statusStmt = $this->conn->prepare("
                UPDATE phieuyeucauxuat
                SET trangThai = 'Đã điều phối'
                WHERE maPhieuYeuCau = :maPhieuYeuCau AND trangThai = 'Chờ điều phối'
            ");
            $statusStmt->execute([':maPhieuYeuCau' => $maPhieuYeuCau]);
            if ($statusStmt->rowCount() !== 1) {
                throw new DomainException('Trạng thái phiếu đã thay đổi. Vui lòng tải lại danh sách.');
            }

            $this->conn->commit();
            return count($allocationsByWarehouse);
        } catch (Throwable $exception) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $exception;
        }
    }

    private function getTonKhoNguyenLieu($forUpdate) {
        $sql = "
            SELECT
                tk.maTonKho,
                tk.maLo AS maLoNL,
                tk.maViTri,
                tk.slTon,
                COALESCE(tk.soLuongTamGiu, 0) AS soLuongTamGiu,
                l.maNL,
                l.hanSuDung,
                nl.tenNL,
                nl.donViTinh,
                v.maKho,
                k.tenKho,
                v.day,
                v.ke,
                v.tang
            FROM tonkho tk
            JOIN longuyenlieu l ON l.maLoNL = tk.maLo
            JOIN nguyenlieu nl ON nl.maNL = l.maNL
            JOIN vitrikho v ON v.maViTri = tk.maViTri
            JOIN kho k ON k.maKho = v.maKho
            WHERE tk.loaiHang = 'NL'
              AND tk.trangThai = 'Khả dụng'
              AND tk.slTon > COALESCE(tk.soLuongTamGiu, 0)
              AND l.trangThaiKiemDinh = 'Đạt'
              AND l.trangThai = 'Đã lưu kho'
              AND l.hanSuDung >= CURDATE()
              AND nl.trangThai = 'Hoạt động'
              AND k.loaiKho = 'Kho nguyên liệu'
              AND k.trangThai = 'Hoạt động'
              AND v.trangThaiChuaHang = 'Khả dụng'
            ORDER BY l.hanSuDung ASC, v.maKho ASC, l.maLoNL ASC, v.maViTri ASC
        ";
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function phanBoFefo($details, $stockRows) {
        $availableByInventory = [];
        foreach ($stockRows as $row) {
            $row['soLuongKhaDung'] = round(
                (float)$row['slTon'] - (float)$row['soLuongTamGiu'],
                2
            );
            $availableByInventory[] = $row;
        }

        $result = [];
        foreach ($details as $detail) {
            $needed = round((float)$detail['soLuongYeuCau'], 2);
            $remaining = $needed;
            $totalAvailable = 0.0;
            $allocations = [];

            foreach ($availableByInventory as &$inventory) {
                if ($inventory['maNL'] !== $detail['maMatHang'] || $inventory['soLuongKhaDung'] <= 0) {
                    continue;
                }

                $totalAvailable += $inventory['soLuongKhaDung'];
                if ($remaining <= 0) {
                    continue;
                }

                $quantity = round(min($remaining, $inventory['soLuongKhaDung']), 2);
                if ($quantity <= 0) {
                    continue;
                }

                $allocations[] = [
                    'maTonKho' => $inventory['maTonKho'],
                    'maLoNL' => $inventory['maLoNL'],
                    'maViTri' => $inventory['maViTri'],
                    'maKho' => $inventory['maKho'],
                    'tenKho' => $inventory['tenKho'],
                    'tenNL' => $inventory['tenNL'],
                    'hanSuDung' => $inventory['hanSuDung'],
                    'viTri' => $inventory['day'] . ' / ' . $inventory['ke'] . ' / ' . $inventory['tang'],
                    'soLuongTon' => (float)$inventory['slTon'],
                    'soLuongTamGiu' => (float)$inventory['soLuongTamGiu'],
                    'soLuongKhaDung' => $inventory['soLuongKhaDung'],
                    'soLuongPhanBo' => $quantity
                ];
                $inventory['soLuongKhaDung'] = round($inventory['soLuongKhaDung'] - $quantity, 2);
                $remaining = round($remaining - $quantity, 2);
            }
            unset($inventory);

            $result[] = [
                'maMatHang' => $detail['maMatHang'],
                'tenMatHang' => $detail['tenMatHang'],
                'donViTinh' => $detail['donViTinh'],
                'soLuongYeuCau' => $needed,
                'tongTonKhaDung' => round($totalAvailable, 2),
                'soLuongPhanBo' => round($needed - $remaining, 2),
                'conThieu' => max(0, $remaining),
                'phanBo' => $allocations
            ];
        }

        return $result;
    }
}