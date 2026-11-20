<?php
class cBienBanKiemKe {
    private $model;

    public function __construct() {
        $this->model = new mBienBanKiemKe();
    }

    public function index() {
        $loaiHang = $_GET['loai'] ?? 'NL';
        $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';

        try {
            $danhSachDot = $this->model->getDanhSachDotTheoLoai($loaiHang);
            $maDotChon = $_GET['madot'] ?? '';
            $currentDot = null;

            if (!empty($maDotChon)) {
                $currentDot = $this->model->getDotKiemKeTheoMa($maDotChon);
            }
            
            if (!$currentDot && !empty($danhSachDot)) {
                $currentDot = $this->model->getDotKiemKeTheoMa($danhSachDot[0]['maDotKiemKe']);
            }

            if (!$currentDot) {
                $maKhoMacDinh = ($loaiHang === 'TP' ? 'KHO_TP_01' : 'KHO_NVL_01');
                $tenKhoMacDinh = ($loaiHang === 'TP' ? 'Kho Thành Phẩm Bánh Quy 1' : 'Kho Nguyên Liệu Khô 1');
                $currentDot = [
                    'maDotKiemKe' => 'DOT-KK-' . $loaiHang . '-' . date('mY'),
                    'tenDotKiemKe' => 'Kiểm kê định kỳ ' . $tenKhoMacDinh,
                    'maKho' => $maKhoMacDinh,
                    'tenKho' => $tenKhoMacDinh,
                    'trangThai' => 'Đang thực hiện',
                    'maBienBan' => '',
                    'trangThaiBienBan' => 'Đang thực hiện'
                ];
            }

            $maKho = $currentDot['maKho'] ?? (($loaiHang === 'TP') ? 'KHO_TP_01' : 'KHO_NVL_01');
            $maBienBan = $currentDot['maBienBan'] ?? '';
            $kiemKeItems = $this->model->getDanhSachKiemKeTheoKho($maKho, $loaiHang, $maBienBan);
        } catch (Exception $e) {
            $danhSachDot = [];
            $currentDot = [];
            $kiemKeItems = [];
        }

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanKiemKe.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    private function verifyCSRF() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
            exit();
        }

        $clientToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (empty($clientToken) || !hash_equals($sessionToken, $clientToken)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Lỗi bảo mật (CSRF Token không hợp lệ)! Vui lòng tải lại trang.']);
            exit();
        }
    }

    // Action xác nhận kiểm đếm từng lô riêng biệt (Nút màu xanh dương chữ trắng)
    public function xacNhanLo() {
        $this->verifyCSRF();
        $maNV = $_SESSION['user']['maNV'] ?? 'NV002';
        $maDotKiemKe = $_POST['maDotKiemKe'] ?? '';
        $maLo = $_POST['maLo'] ?? '';
        $maViTri = $_POST['maViTri'] ?? '';
        $slTot = (float)($_POST['slTot'] ?? 0);
        $slHong = (float)($_POST['slHong'] ?? 0);
        $ghiChu = trim($_POST['ghiChu'] ?? '');

        if (empty($maDotKiemKe) || empty($maLo)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin mã đợt hoặc mã lô hàng!']);
            exit();
        }

        try {
            $result = $this->model->xacNhanLoKiemKe($maDotKiemKe, $maLo, $maViTri, $slTot, $slHong, $ghiChu, $maNV);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }

    // Action chốt hoàn tất toàn bộ biên bản kiểm kê cả kho
    public function hoanTat() {
        $this->verifyCSRF();
        $maNV = $_SESSION['user']['maNhanVien'] ?? $_SESSION['user']['maNV'] ?? 'NV002';
        $maDotKiemKe = $_POST['maDotKiemKe'] ?? '';
        $itemsJson = $_POST['items'] ?? '';

        if (empty($maDotKiemKe)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Thiếu mã đợt kiểm kê!']);
            exit();
        }

        try {
            if (!empty($itemsJson)) {
                $items = json_decode($itemsJson, true);
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $slTot = ($item['slTot'] !== null && $item['slTot'] !== '') ? (float)$item['slTot'] : (float)($item['tonSoSach'] ?? 0);
                        $slHong = ($item['slHong'] !== null && $item['slHong'] !== '') ? (float)$item['slHong'] : 0;
                        $this->model->xacNhanLoKiemKe(
                            $maDotKiemKe,
                            $item['maLo'] ?? '',
                            $item['maViTri'] ?? '',
                            $slTot,
                            $slHong,
                            $item['tinhTrang'] ?? '',
                            $maNV
                        );
                    }
                }
            }
            $result = $this->model->hoanTatBienBanKiemKe($maDotKiemKe, $maNV);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }

    public function handleSave() {
        $this->verifyCSRF();
        $maNV = $_SESSION['user']['maNV'] ?? 'NV002';
        $maDotKiemKe = $_POST['maDotKiemKe'] ?? '';
        $itemsJson = $_POST['items'] ?? '[]';
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Không có mặt hàng nào để lập biên bản!']);
            exit();
        }

        try {
            $result = $this->model->luuBienBanKiemKe($maDotKiemKe, $items, $maNV);
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }

    public function save() {
        $this->handleSave();
    }
}
