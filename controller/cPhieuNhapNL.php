<?php
class cPhieuNhapNL {
    private $model;

    public function __construct() {
        $this->model = new mPhieuNhapNL();
    }

    /**
     * Phương thức hiển thị giao diện chính
     */
    public function index() {
        $tuKhoa = $_GET['search'] ?? '';
        $dsDieuPhoi = $this->model->selectAllLenhDieuPhoiNhap($tuKhoa);

        $maLenhChon = $_GET['maLenhDieuPhoi'] ?? ($dsDieuPhoi[0]['maLenhDieuPhoi'] ?? null);
        $chiTiet = $maLenhChon ? $this->model->selectChiTietDieuPhoiNhap($maLenhChon) : null;

        $maKhoTarget = $chiTiet['maKhoTiepNhan'] ?? 'KHO_NVL_01';
        $dayFilter   = $_GET['day'] ?? '';
        $keFilter    = $_GET['ke'] ?? '';
        $tangFilter  = $_GET['tang'] ?? '';
        $dsViTri     = $this->model->selectViTriKhoTrong($maKhoTarget, $dayFilter, $keFilter, $tangFilter);

        // Lấy thông báo từ Session nếu có chuyển hướng
        $thongBao = $_SESSION['thongBao'] ?? null;
        unset($_SESSION['thongBao']);

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuNhapNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    /**
     *  Phương thức được Router gọi tự động khi submit action='luuPhieuNhap'
     */
public function luuPhieuNhap() {
        $maLenhDieuPhoi =$_POST['maLenhDieuPhoi'] ?? '';
        $maViTri        =$_POST['maViTri'] ?? '';
        $slThucNhap     = (float)($_POST['slThucNhap'] ?? 0);
        $soLuongPhanBo  = (float)($_POST['soLuongPhanBo'] ?? 0);
        $ghiChu         =$_POST['ghiChu'] ?? '';
        $maNV           =$_SESSION['maNV'] ?? 'NV005';

        //  1. KIỂM TRA CHỐNG NHẬP TRÙNG (SMART VALIDATION)
        $chiTietHienTai = $this->model->selectChiTietDieuPhoiNhap($maLenhDieuPhoi);
        if ($chiTietHienTai &&$chiTietHienTai['trangThai'] === 'Hoàn tất nhập kho') {
            $_SESSION['thongBao'] = [
                'type' => 'error', 
                'msg'  => '⚠️ Lệnh điều phối [' . $maLenhDieuPhoi . '] đã được nhập kho thành công trước đó. Không thể nhập lại!'
            ];
            header("Location: index.php?page=vPhieuNhapNL&maLenhDieuPhoi=" . urlencode($maLenhDieuPhoi));
            return ['success' => false, 'message' => 'Lệnh điều phối đã hoàn tất!'];
        }

        $soLuongChenhLech = $slThucNhap -$soLuongPhanBo;
        $lyDoChenhLech    = ($soLuongChenhLech != 0) ? ($_POST['lyDoChenhLech'] ?? 'Chênh lệch thực nhập') : null;

        if (!empty($maLenhDieuPhoi) && !empty($maViTri) && $slThucNhap > 0) {$kq = $this->model->insertPhieuNhapNL($maLenhDieuPhoi, $maViTri,$slThucNhap, $soLuongChenhLech,$lyDoChenhLech, $maNV,$ghiChu);
            if ($kq) {$_SESSION['thongBao'] = ['type' => 'success', 'msg' => 'Lưu phiếu nhập kho nguyên liệu thành công!'];
                header("Location: index.php?page=vPhieuNhapNL&maLenhDieuPhoi=" . urlencode($maLenhDieuPhoi));
                return ['success' => true, 'message' => 'Lưu phiếu nhập kho nguyên liệu thành công!'];
            } else {
                $_SESSION['thongBao'] = ['type' => 'error', 'msg' => 'Lưu phiếu nhập kho thất bại. Vui lòng thử lại!'];
                return ['success' => false, 'message' => 'Lưu phiếu nhập kho thất bại. Vui lòng thử lại!'];
            }
        } else {
            return ['success' => false, 'message' => 'Vui lòng chọn vị trí lưu trữ và nhập số lượng thực nhập hợp lệ!'];
        }
    }

    // Tự động alias các tên gọi handler khác mà Router có thể quét tới
    public function handleLuuPhieuNhap() { return $this->luuPhieuNhap(); }
    public function actionLuuPhieuNhap() { return $this->luuPhieuNhap(); }
}