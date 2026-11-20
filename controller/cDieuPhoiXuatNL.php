<?php
class cDieuPhoiXuatNL {
    private $model;

    public function __construct() {
        // Tự động nhận mDieuPhoiXuatNL, không cần require!
        $this->model = new mDieuPhoiXuatNL();
    }

    public function index() {
        $danhSachYeuCau = $this->model->getDanhSachYeuCau();
        $maPhieuYeuCau = trim($_GET['maPhieuYeuCau'] ?? '');
        $yeuCauDangChon = null;
        $deXuatFefo = [];

        if ($maPhieuYeuCau !== '') {
            $yeuCauDangChon = $this->model->getYeuCau($maPhieuYeuCau);
            if ($yeuCauDangChon && $yeuCauDangChon['trangThai'] === 'Chờ điều phối') {
                $deXuatFefo = $this->model->getDeXuatFefo($maPhieuYeuCau);
            }
        }

        $thongBao = $_SESSION['dieuphoixuatnl_flash'] ?? null;
        unset($_SESSION['dieuphoixuatnl_flash']);

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDieuPhoiXuatNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    public function confirm() {
        $maPhieuYeuCau = trim($_POST['maPhieuYeuCau'] ?? '');
        $maNV = $_SESSION['user']['maNV'] ?? '';

        try {
            $soKho = $this->model->taoLenhDieuPhoi($maPhieuYeuCau, $maNV);
            $_SESSION['dieuphoixuatnl_flash'] = [
                'type' => 'success',
                'message' => 'Đã tạo lệnh điều phối FEFO tại ' . $soKho . ' kho và giữ tồn khả dụng.'
            ];
        } catch (DomainException $exception) {
            $_SESSION['dieuphoixuatnl_flash'] = [
                'type' => 'error',
                'message' => $exception->getMessage()
            ];
        }

        $query = $maPhieuYeuCau !== ''
            ? '&maPhieuYeuCau=' . rawurlencode($maPhieuYeuCau)
            : '';
        header('Location: index.php?page=vDieuPhoiXuatNL' . $query);
        exit();
    }
}