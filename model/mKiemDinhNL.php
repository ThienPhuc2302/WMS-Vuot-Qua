<?php
class mKiemDinhNL {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL từ Database.php, không cần require!
        $this->conn = Database::getInstance()->getConnection();
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM TRUY VẤN SQL CỦA BẠN TỪ ĐÂY ===

    // Lấy danh sách lô nguyên liệu đang chờ kiểm định.
    public function getLoChoKiemDinh($tuKhoa = '')
    {
        $sql = "
            SELECT
                l.maLoNL,
                l.maNL,
                n.tenNL,
                n.donViTinh,
                l.nhaCungCap,
                l.ngaySanXuat,
                l.hanSuDung,
                l.soLuongLo,
                l.trangThaiKiemDinh,
                l.trangThai
            FROM longuyenlieu l
            INNER JOIN nguyenlieu n ON n.maNL = l.maNL
            WHERE l.trangThaiKiemDinh = 'Chờ kiểm định'
              AND l.trangThai = 'Chờ kiểm định'
        ";

        $params = [];

        if ($tuKhoa !== '') {
            $sql .= "
                AND (
                    l.maLoNL LIKE :maLo
                    OR n.tenNL LIKE :tenNL
                )
            ";

            $params[':maLo'] = '%' . $tuKhoa . '%';
            $params[':tenNL'] = '%' . $tuKhoa . '%';
        }

        $sql .= " ORDER BY l.ngaySanXuat DESC, l.maLoNL";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin một lô đang chờ kiểm định.
    public function getLoChoKiemDinhTheoMa($maLoNL)
    {
        $sql = "
            SELECT
                l.maLoNL,
                l.maNL,
                n.tenNL,
                n.donViTinh,
                l.nhaCungCap,
                l.ngaySanXuat,
                l.hanSuDung,
                l.soLuongLo,
                l.trangThaiKiemDinh,
                l.trangThai
            FROM longuyenlieu l
            INNER JOIN nguyenlieu n ON n.maNL = l.maNL
            WHERE l.maLoNL = :maLoNL
              AND l.trangThaiKiemDinh = 'Chờ kiểm định'
              AND l.trangThai = 'Chờ kiểm định'
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maLoNL' => $maLoNL]);

        $lo = $stmt->fetch(PDO::FETCH_ASSOC);

        return $lo ?: null;
    }

    // Đọc tiêu chí từ bảng thực tế trong CSDL.
    public function getTieuChiNguyenLieu()
    {
        $sql = "
            SELECT maTieuChi, tenTieuChi, quyChuan
            FROM tieuchikiemdinh
            WHERE loaiHang = 'Nguyên liệu'
            ORDER BY maTieuChi
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function luuBienBan(array $duLieu, $maNV)
    {
        // Chặn dữ liệu dạng mảng tại các trường văn bản.
        foreach (
            ['maLoNL', 'ketQuaTongThe', 'lyDoKhongDat', 'ghiChu']
            as $tenTruong
        ) {
            if (
                isset($duLieu[$tenTruong])
                && !is_string($duLieu[$tenTruong])
            ) {
                throw new InvalidArgumentException(
                    'Dữ liệu gửi lên không hợp lệ.'
                );
            }
        }

        $maLoNL = trim($duLieu['maLoNL'] ?? '');
        $ketQuaTongThe = $duLieu['ketQuaTongThe'] ?? '';
        $lyDoKhongDat = trim($duLieu['lyDoKhongDat'] ?? '');
        $ghiChu = trim($duLieu['ghiChu'] ?? '');

        $giaTriDo = $duLieu['giaTriDoThucTe'] ?? [];
        $danhGia = $duLieu['danhGia'] ?? [];
        $ghiChuTieuChi = $duLieu['ghiChuTieuChi'] ?? [];

        if ($maLoNL === '') {
            throw new InvalidArgumentException(
                'Vui lòng chọn lô nguyên liệu.'
            );
        }

        if ($maNV === '') {
            throw new InvalidArgumentException(
                'Không xác định được nhân viên QC.'
            );
        }

        if (!in_array($ketQuaTongThe, ['Đạt', 'Không đạt'], true)) {
            throw new InvalidArgumentException(
                'Vui lòng chọn kết quả kiểm định tổng thể.'
            );
        }

        if ($ketQuaTongThe === 'Không đạt' && $lyDoKhongDat === '') {
            throw new InvalidArgumentException(
                'Cần nhập lý do khi kết luận lô không đạt.'
            );
        }

        if (
            mb_strlen($ghiChu) > 500
            || mb_strlen($lyDoKhongDat) > 500
        ) {
            throw new InvalidArgumentException(
                'Ghi chú và lý do tối đa 500 ký tự.'
            );
        }

        if (
            !is_array($giaTriDo)
            || !is_array($danhGia)
            || !is_array($ghiChuTieuChi)
            || empty($giaTriDo)
        ) {
            throw new InvalidArgumentException(
                'Hãy chọn ít nhất một tiêu chí và nhập kết quả.'
            );
        }

        $this->conn->beginTransaction();

        try {
            // Khóa bản ghi lô trong suốt giao dịch.
            // Hai QC không thể đồng thời xác nhận cùng một lô.
            $stmtLo = $this->conn->prepare("
                SELECT maNL, trangThaiKiemDinh, trangThai
                FROM longuyenlieu
                WHERE maLoNL = :maLoNL
                FOR UPDATE
            ");

            $stmtLo->execute([':maLoNL' => $maLoNL]);
            $lo = $stmtLo->fetch(PDO::FETCH_ASSOC);

            if (
                !$lo
                || $lo['trangThaiKiemDinh'] !== 'Chờ kiểm định'
                || $lo['trangThai'] !== 'Chờ kiểm định'
            ) {
                throw new DomainException(
                    'Lô không còn ở trạng thái chờ kiểm định. '
                    . 'Vui lòng tải lại danh sách.'
                );
            }

            // Lấy tên và quy chuẩn từ CSDL, không tin dữ liệu
            // tên tiêu chí hoặc quy chuẩn gửi từ trình duyệt.
            $tieuChiTheoMa = array_column(
                $this->getTieuChiNguyenLieu(),
                null,
                'maTieuChi'
            );

            $chiTiet = [];
            $coTieuChiKhongDat = false;

            foreach ($giaTriDo as $maTieuChi => $giaTri) {
                if (!isset($tieuChiTheoMa[$maTieuChi])) {
                    throw new InvalidArgumentException(
                        'Tiêu chí không tồn tại hoặc không thuộc Nguyên liệu.'
                    );
                }

                if (
                    !is_string($giaTri)
                    || !isset($danhGia[$maTieuChi])
                    || !is_string($danhGia[$maTieuChi])
                    || (
                        isset($ghiChuTieuChi[$maTieuChi])
                        && !is_string($ghiChuTieuChi[$maTieuChi])
                    )
                ) {
                    throw new InvalidArgumentException(
                        'Dữ liệu kết quả tiêu chí không hợp lệ.'
                    );
                }

                $ketQuaDo = trim($giaTri);
                $ketQuaTieuChi = $danhGia[$maTieuChi];
                $ghiChuDong = trim(
                    $ghiChuTieuChi[$maTieuChi] ?? ''
                );

                if (
                    $ketQuaDo === ''
                    || !in_array($ketQuaTieuChi, ['0', '1'], true)
                ) {
                    throw new InvalidArgumentException(
                        'Cần nhập kết quả đo và đánh giá '
                        . 'cho từng tiêu chí đã chọn.'
                    );
                }

                if (
                    mb_strlen($ketQuaDo) > 100
                    || mb_strlen($ghiChuDong) > 255
                ) {
                    throw new InvalidArgumentException(
                        'Kết quả đo tối đa 100 ký tự; '
                        . 'ghi chú tiêu chí tối đa 255 ký tự.'
                    );
                }

                if ($ketQuaTieuChi === '0') {
                    $coTieuChiKhongDat = true;
                }

                $tc = $tieuChiTheoMa[$maTieuChi];

                $chiTiet[] = [
                    'maTieuChi' => $maTieuChi,
                    'tenTieuChi' => $tc['tenTieuChi'],
                    'quyChuan' => $tc['quyChuan'],
                    'giaTriDoThucTe' => $ketQuaDo,
                    'danhGia' => (int) $ketQuaTieuChi,
                    'ghiChu' => $ghiChuDong
                ];
            }

            if (
                $ketQuaTongThe === 'Đạt'
                && $coTieuChiKhongDat
            ) {
                throw new InvalidArgumentException(
                    'Không thể kết luận Đạt khi có tiêu chí Không đạt.'
                );
            }

            // Kiểm tra cả trạng thái cũ trong dữ liệu mẫu.
            $stmtDaLap = $this->conn->prepare("
                SELECT maBienBan
                FROM bienbankiemdinh
                WHERE loaiKiemDinh = 1
                  AND maLo = :maLo
                  AND trangThaiBienBan IN (
                      'Đã xác nhận',
                      'Đã hoàn tất'
                  )
                LIMIT 1
            ");

            $stmtDaLap->execute([':maLo' => $maLoNL]);

            if ($stmtDaLap->fetch(PDO::FETCH_ASSOC)) {
                throw new DomainException(
                    'Lô này đã có biên bản kiểm định được xác nhận.'
                );
            }

            // Lưu chi tiết trong cột TEXT sẵn có.
            $tieuChiDanhGia = json_encode(
                $chiTiet,
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );

            if (strlen($tieuChiDanhGia) > 65535) {
                throw new InvalidArgumentException(
                    'Nội dung đánh giá vượt dung lượng cho phép.'
                );
            }

            // Mã dài 18 ký tự, phù hợp VARCHAR(20).
            $maBienBan = 'KD'
                . date('Ymd')
                . strtoupper(bin2hex(random_bytes(4)));

            $stmtBB = $this->conn->prepare("
                INSERT INTO bienbankiemdinh (
                    maBienBan,
                    loaiKiemDinh,
                    maLo,
                    ngayKiemDinh,
                    nguoiKiemDinh,
                    tieuChiDanhGia,
                    ketQuaTongThe,
                    lyDoKhongDat,
                    ghiChu,
                    trangThaiBienBan
                )
                VALUES (
                    :maBienBan,
                    1,
                    :maLo,
                    NOW(),
                    :nguoiKiemDinh,
                    :tieuChiDanhGia,
                    :ketQuaTongThe,
                    :lyDoKhongDat,
                    :ghiChu,
                    'Đã xác nhận'
                )
            ");

            $stmtBB->execute([
                ':maBienBan' => $maBienBan,
                ':maLo' => $maLoNL,
                ':nguoiKiemDinh' => $maNV,
                ':tieuChiDanhGia' => $tieuChiDanhGia,
                ':ketQuaTongThe' => $ketQuaTongThe,
                ':lyDoKhongDat' => $ketQuaTongThe === 'Không đạt'
                    ? $lyDoKhongDat
                    : null,
                ':ghiChu' => $ghiChu !== '' ? $ghiChu : null
            ]);

            $trangThaiLo = $ketQuaTongThe === 'Đạt'
                ? 'Chờ điều phối'
                : 'Trả NCC';

            // Chỉ thay đổi kết quả QC và trạng thái xử lý.
            // Không cập nhật bất kỳ số lượng tồn nào.
            $stmtCapNhat = $this->conn->prepare("
                UPDATE longuyenlieu
                SET trangThaiKiemDinh = :ketQua,
                    trangThai = :trangThai
                WHERE maLoNL = :maLo
            ");

            $stmtCapNhat->execute([
                ':ketQua' => $ketQuaTongThe,
                ':trangThai' => $trangThaiLo,
                ':maLo' => $maLoNL
            ]);

            $this->conn->commit();

            return $maBienBan;
        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            throw $e;
        }
    }
}