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
            $maDot = $_GET['madot'] ?? ($danhSachDot[0]['maDotKiemKe'] ?? '');
            $currentDot = $this->model->getDotKiemKeTheoMa($maDot);

            if (!$currentDot) {
                $maKho = ($loaiHang === 'TP') ? 'KHO_TP_01' : 'KHO_NVL_01';
                $tenKhoHienTai = ($loaiHang === 'TP') ? 'Kho Thành Phẩm 1' : 'Kho Nguyên Liệu Khô 1';
                $currentDot = [
                    'maDotKiemKe' => 'DOT-KK-' . $loaiHang . '-' . date('mY'),
                    'tenDotKiemKe' => 'Kiểm kê định kỳ',
                    'maKho' => $maKho,
                    'tenKho' => $tenKhoHienTai,
                    'trangThai' => 'Đang kiểm đếm'
                ];
            }

            $maKho = $currentDot['maKho'] ?? (($loaiHang === 'TP') ? 'KHO_TP_01' : 'KHO_NVL_01');
            $kiemKeItems = $this->model->getDanhSachKiemKeTheoKho($maKho, $loaiHang);
        } catch (Exception $e) {
            $danhSachDot = [];
            $currentDot = [];
            $kiemKeItems = [];
        }

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanKiemKe.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    public function handleSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
            exit();
        }

        $clientToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        if (empty($clientToken) || !hash_equals($sessionToken, $clientToken)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Lỗi bảo mật (CSRF Token không hợp lệ)! Vui lòng tải lại trang.']);
            exit();
        }

        $maNV = $_SESSION['user']['maNV'] ?? '';
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