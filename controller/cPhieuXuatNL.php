<?php
class cPhieuXuatNL {
    private $model;

    public function __construct() {
        $this->model = new mPhieuXuatNL();
    }

    public function index() {
        $tuKhoa = $_GET['search'] ?? '';
        $dsDieuPhoiXuat = $this->model->selectAllLenhDieuPhoiXuat($tuKhoa);

        $maLenhChon = $_GET['maLenh'] ?? ($dsDieuPhoiXuat[0]['maLenh'] ?? null);
        $chiTiet = $maLenhChon ? $this->model->selectChiTietDieuPhoiXuat($maLenhChon) : null;
        $dsChiTietKiet = $maLenhChon ? $this->model->selectDanhSachChiTietXuat($maLenhChon) : [];

        $thongBao = $_SESSION['thongBao'] ?? null;
        unset($_SESSION['thongBao']);

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuXuatNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    public function luuPhieuXuat() {
        $maLenh    = $_POST['maLenh'] ?? '';
        $nguoiXuat = $_POST['nguoiXuat'] ?? 'Trần Văn Sang';
        $ghiChu    = $_POST['ghiChu'] ?? '';

        //  1. KIỂM TRA CHỐNG XUẤT TRÙNG LẶP (SMART VALIDATION)
        $chiTietHienTai = $this->model->selectChiTietDieuPhoiXuat($maLenh);
        if ($chiTietHienTai && ($chiTietHienTai['trangThai'] === 'Đã xuất kho' || $chiTietHienTai['trangThai'] === 'Đã hoàn tất')) {
            $_SESSION['thongBao'] = [
                'type' => 'error', 
                'msg'  => '⚠️ Lệnh điều phối xuất [' . $maLenh . '] đã được xuất kho thành công trước đó. Không thể xuất lại!'
            ];
            header("Location: index.php?page=vPhieuXuatNL&maLenh=" . urlencode($maLenh));
            return ['success' => false, 'message' => 'Lệnh điều phối xuất đã hoàn tất!'];
        }

        if (!empty($maLenh)) {
            $kq = $this->model->insertPhieuXuatNL($maLenh, $nguoiXuat, $ghiChu);
            if ($kq) {
                $_SESSION['thongBao'] = ['type' => 'success', 'msg' => 'Lưu phiếu xuất kho nguyên liệu thành công!'];
                header("Location: index.php?page=vPhieuXuatNL&maLenh=" . urlencode($maLenh));
                return ['success' => true, 'message' => 'Lưu phiếu xuất kho nguyên liệu thành công!'];
            } else {
                $_SESSION['thongBao'] = ['type' => 'error', 'msg' => 'Xuất kho thất bại. Vui lòng kiểm tra lại!'];
                return ['success' => false, 'message' => 'Xuất kho thất bại. Vui lòng kiểm tra lại!'];
            }
        } else {
            return ['success' => false, 'message' => 'Vui lòng chọn lệnh điều phối xuất hợp lệ!'];
        }
    }

    public function handleLuuPhieuXuat() { return $this->luuPhieuXuat(); }
    public function actionLuuPhieuXuat() { return $this->luuPhieuXuat(); }
}