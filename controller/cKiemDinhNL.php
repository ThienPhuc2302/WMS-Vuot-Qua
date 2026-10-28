<?php

class cKiemDinhNL
{
    private $model;

    public function __construct()
    {
        $this->model = new mKiemDinhNL();
    }

    public function index()
    {
        $tuKhoa = isset($_GET['q']) && is_string($_GET['q'])
            ? trim($_GET['q'])
            : '';

        $maLoChon = isset($_GET['maLo']) && is_string($_GET['maLo'])
            ? trim($_GET['maLo'])
            : '';

        $danhSachLo = $this->model->getLoChoKiemDinh($tuKhoa);

        if ($maLoChon === '' && !empty($danhSachLo)) {
            $maLoChon = $danhSachLo[0]['maLoNL'];
        }

        $loDangChon = $maLoChon !== ''
            ? $this->model->getLoChoKiemDinhTheoMa($maLoChon)
            : null;

        $danhSachTieuChi = $loDangChon
            ? $this->model->getTieuChiNguyenLieu()
            : [];

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vKiemDinhNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->traJson([
                'success' => false,
                'message' => 'Phương thức không được hỗ trợ.'
            ], 405);
        }

        $user = $_SESSION['user'] ?? [];

        $vaiTro = $user['maVaiTro']
            ?? ($user['vaiTro'] ?? '');

        if (
            !in_array(
                $vaiTro,
                ['VT06', 'QC', 'Bộ phận QC', 'KCS/QC', 'KCS'],
                true
            )
            || empty($user['maNV'])
        ) {
            $this->traJson([
                'success' => false,
                'message' => 'Chỉ tài khoản QC được xác nhận biên bản.'
            ], 403);
        }

        $clientToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (
            !is_string($clientToken)
            || !is_string($sessionToken)
            || $clientToken === ''
            || $sessionToken === ''
            || !hash_equals($sessionToken, $clientToken)
        ) {
            $this->traJson([
                'success' => false,
                'message' => 'Mã bảo mật không hợp lệ. Vui lòng tải lại trang.'
            ], 403);
        }

        try {
            $maBienBan = $this->model->luuBienBan(
                $_POST,
                (string) $user['maNV']
            );

            $ketQua = $_POST['ketQuaTongThe'];

            $this->traJson([
                'success' => true,
                'message' => 'Lưu biên bản kiểm định thành công.',
                'maBienBan' => $maBienBan,
                'maLoNL' => trim($_POST['maLoNL']),
                'ketQuaTongThe' => $ketQua,
                'trangThaiLo' => $ketQua === 'Đạt'
                    ? 'Chờ điều phối'
                    : 'Trả NCC'
            ]);
        } catch (InvalidArgumentException | DomainException $e) {
            $this->traJson([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (Throwable $e) {
            error_log(
                'KIEM_DINH_NL_SAVE: ' . $e->getMessage()
            );

            $this->traJson([
                'success' => false,
                'message' => 'Không thể lưu biên bản do lỗi hệ thống. '
                    . 'Chi tiết đã được ghi vào nhật ký PHP.'
            ], 500);
        }
    }

    private function traJson(array $data, $status = 200)
    {
        http_response_code($status);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit();
    }
}