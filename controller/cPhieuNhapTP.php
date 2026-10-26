<?php
/**
 * =========================================================================
 * CONTROLLER: cPhieuNhapTP
 * Use Case: UC 3.4 - Lập phiếu nhập kho thành phẩm
 * Phụ trách: Nguyễn Hữu Lộc
 * =========================================================================
 */
class cPhieuNhapTP {
    private $model;

    public function __construct() {
        $this->model = new mPhieuNhapTP();
    }

    /**
     * Hiển thị giao diện chính Lập phiếu nhập kho thành phẩm
     */
    public function index() {
        // Lấy 100% dữ liệu trực tiếp từ CSDL MySQL. Nếu có lỗi, để Exception ném trực tiếp ra màn hình, tuyệt đối không nuốt lỗi thành mảng rỗng []
        $orders = $this->model->getDanhSachLenhDieuPhoi();
        // Lấy tự động danh sách vị trí kệ thuộc Loại kho Thành phẩm từ CSDL, không cố định mã kho
        $shelves = $this->model->getViTriKhoKhaDung();

        // Nhúng layout chuẩn và view
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuNhapTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    /**
     * Tiếp nhận và xử lý lưu Phiếu Nhập Kho thành phẩm
     */
    public function handleSave() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
            exit();
        }

        $maNV = $_SESSION['user']['maNV'] ?? '';
        $maLenh = trim($_POST['maLenhDieuPhoi'] ?? '');
        $maViTri = trim($_POST['maViTri'] ?? '');
        $ghiChu = trim($_POST['ghiChu'] ?? '');
        $slThucNhap = isset($_POST['slThucNhap']) && $_POST['slThucNhap'] !== '' ? (float)$_POST['slThucNhap'] : null;
        $lyDoChenhLech = trim($_POST['lyDoChenhLech'] ?? '');

        // Validation nghiệp vụ
        if (empty($maLenh)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn lệnh điều phối cần nhập kho!']);
            exit();
        }

        if (empty($maViTri)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn vị trí lưu kho (kệ)!']);
            exit();
        }

        if ($slThucNhap === null) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập số lượng thực nhận!']);
            exit();
        }

        if ($slThucNhap <= 0) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Số lượng thực nhận phải lớn hơn 0!']);
            exit();
        }

        // Gọi Model xử lý giao dịch CSDL
        $result = $this->model->luuPhieuNhapKho($maLenh, $maViTri, $maNV, $ghiChu, $slThucNhap, $lyDoChenhLech);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);
        exit();
    }

    /**
     * Lấy chi tiết phiếu nhập phục vụ xem/in phiếu
     */
    public function getPrintData() {
        $maNV = $_SESSION['user']['maNV'] ?? '';
        if (empty($maNV)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại!']);
            exit();
        }

        // Kiểm tra quyền xem phiếu
        $maVaiTro = $_SESSION['user']['maVaiTro'] ?? '';
        $tenVaiTro = $_SESSION['user']['vaiTro'] ?? '';
        $allowedRoles = ['VT03', 'NhanVienKho', 'VT02', 'QuanLyKho', 'VT07', 'BanGiamDoc'];
        if (!in_array($maVaiTro, $allowedRoles) && !in_array($tenVaiTro, $allowedRoles)) {
            header('HTTP/1.1 403 Forbidden');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Lỗi phân quyền (403): Bạn không có quyền truy cập dữ liệu phiếu nhập kho!']);
            exit();
        }

        $maPNK = trim($_GET['maPNK'] ?? '');
        header('Content-Type: application/json; charset=utf-8');
        try {
            $data = $this->model->getChiTietPhieuNhap($maPNK);
            if (!$data) {
                echo json_encode(['success' => false, 'message' => "Không tìm thấy dữ liệu phiếu nhập kho với mã: {$maPNK}"]);
            } else {
                echo json_encode(['success' => true, 'data' => $data]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Alias chuẩn MVC cho handleSave
     */
    public function save() {
        $this->handleSave();
    }
}
