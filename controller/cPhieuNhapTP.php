<?php
/**
 * CONTROLLER: cPhieuNhapTP
 * Use Case: UC 3.4 - Lập phiếu nhập kho thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc
 */
require_once __DIR__ . '/../model/mPhieuNhapTP.php';

class cPhieuNhapTP {
    private $model;

    public function __construct() {
        $this->model = new mPhieuNhapTP();
    }

    /**
     * Lấy dữ liệu khởi tạo cho giao diện View
     */
    public function index() {
        $orders = $this->model->getDanhSachLenhDieuPhoi();
        $shelves = $this->model->getViTriKhoKhaDung('KHO_TP_01');
        return [
            'orders'  => $orders,
            'shelves' => $shelves
        ];
    }

    /**
     * Xử lý lưu phiếu nhập kho từ form hoặc fetch AJAX
     */
    public function handleSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!'];
        }

        $maLenh = trim($_POST['maLenhDieuPhoi'] ?? '');
        $maViTri = trim($_POST['maViTri'] ?? '');
        $ghiChu = trim($_POST['ghiChu'] ?? '');
        $slThucNhap = isset($_POST['slThucNhap']) ? (float)$_POST['slThucNhap'] : null;
        $lyDoChenhLech = trim($_POST['lyDoChenhLech'] ?? '');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $maNV = $_SESSION['user']['maNV'] ?? 'NV006';

        // Validation nghiệp vụ chặt chẽ
        if (empty($maLenh)) {
            return ['success' => false, 'message' => 'Vui lòng chọn lệnh điều phối cần nhập kho!'];
        }

        if (empty($maViTri)) {
            return ['success' => false, 'message' => 'Vui lòng chọn vị trí lưu kho (kệ)!'];
        }

        // Gọi Model xử lý giao dịch CSDL
        $result = $this->model->luuPhieuNhapKho($maLenh, $maViTri, $maNV, $ghiChu, $slThucNhap, $lyDoChenhLech);
        return $result;
    }

    /**
     * Lấy chi tiết phiếu nhập để phục vụ in A4
     */
    public function getPrintData($maPNK) {
        return $this->model->getChiTietPhieuNhap($maPNK);
    }
}

// Xử lý trực tiếp nếu gọi qua AJAX API
if (isset($_GET['action']) || (isset($_POST['action']) && $_POST['action'] === 'save_tp_inbound')) {
    $controller = new cPhieuNhapTP();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if ($action === 'save_tp_inbound') {
        header('Content-Type: application/json; charset=utf-8');
        $res = $controller->handleSave();
        echo json_encode($res);
        exit();
    }

    if ($action === 'get_print_receipt') {
        header('Content-Type: application/json; charset=utf-8');
        $maPNK = trim($_GET['maPNK'] ?? '');
        $data = $controller->getPrintData($maPNK);
        echo json_encode(['success' => (bool)$data, 'data' => $data]);
        exit();
    }
}
?>
