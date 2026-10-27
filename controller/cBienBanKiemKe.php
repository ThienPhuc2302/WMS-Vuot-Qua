<?php
class cBienBanKiemKe {
    private $model;

    public function __construct() {
        $this->model = new mBienBanKiemKe();
    }

    public function index() {
        $loaiHang = $_GET['loai'] ?? 'NL';
        $loaiHang = strtoupper($loaiHang) === 'TP' ? 'TP' : 'NL';

        // Lấy danh sách đợt kiểm kê phù hợp với tab (NL hoặc TP)
        $danhSachDot = $this->model->getDanhSachDotTheoLoai($loaiHang);

        // Lấy đợt đang chọn hoặc mặc định đợt đầu tiên
        $maDot = $_GET['madot'] ?? ($danhSachDot[0]['maDotKiemKe'] ?? '');
        $currentDot = $this->model->getDotKiemKeTheoMa($maDot);

        // Nếu chưa có đợt nào, gán kho mặc định tương ứng
        if (!$currentDot) {
            $maKho = ($loaiHang === 'TP') ? 'KHO_TP_01' : 'KHO_NL_01';
            $tenKhoHienTai = ($loaiHang === 'TP') ? 'Kho Thành Phẩm 1' : 'Kho Nguyên Liệu Khô 1';
            $currentDot = [
                'maDotKiemKe' => 'DOT-KK-' . $loaiHang . '-' . date('mY'),
                'tenDotKiemKe' => 'Kiểm kê định kỳ',
                'maKho' => $maKho,
                'tenKho' => $tenKhoHienTai,
                'trangThai' => 'Đang kiểm đếm'
            ];
        }

        $maKho = $currentDot['maKho'] ?? (($loaiHang === 'TP') ? 'KHO_TP_01' : 'KHO_NL_01');
        $kiemKeItems = $this->model->getDanhSachKiemKeTheoKho($maKho, $loaiHang);

        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanKiemKe.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    public function handleSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
            exit();
        }

        $maNV = $_SESSION['user']['maNV'] ?? '';
        $maDotKiemKe = $_POST['maDotKiemKe'] ?? '';
        $itemsJson = $_POST['items'] ?? '[]';
        $items = json_decode($itemsJson, true);

        $result = $this->model->luuBienBanKiemKe($maDotKiemKe, $items, $maNV);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }

    public function save() {
        $this->handleSave();
    }
}