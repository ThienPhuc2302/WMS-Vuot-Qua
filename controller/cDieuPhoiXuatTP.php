<?php
/**
 * =========================================================================
 * CONTROLLER: cDieuPhoiXuatTP
 * Use Case: UC 4.1 - Điều phối xuất thành phẩm (FEFO)
 * Phụ trách: Quản lý kho (Role: VT02 / QuanLyKho)
 * =========================================================================
 */
class cDieuPhoiXuatTP {
    private $model;

    public function __construct() {
        // Tự động nạp Model qua PSR-4 Autoload
        $this->model = new mDieuPhoiXuatTP();
    }

    /**
     * Kiểm tra quyền truy cập Quản lý kho (RBAC)
     */
    private function checkPermission() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;
        if (!$user || empty($user['maNV'])) {
            return false;
        }

        $rawRole = $user['maVaiTro'] ?? ($user['vaiTro'] ?? '');
        $allowed = ['VT02', 'QuanLyKho', 'VT01', 'Admin'];
        return in_array($rawRole, $allowed);
    }

    /**
     * Hiển thị giao diện chính Điều phối xuất thành phẩm
     */
    public function index() {
        $user = $_SESSION['user'] ?? null;
        $keyword = trim($_GET['keyword'] ?? '');
        $trangThaiFilter = trim($_GET['trangThai'] ?? '');

        // Lấy danh sách phiếu yêu cầu xuất TP
        $danhSachPhieu = $this->model->getDanhSachPhieuYeuCau($keyword, $trangThaiFilter);

        // Xác định phiếu được chọn mặc định
        $selectedPYCId = trim($_GET['pyc'] ?? '');
        if (empty($selectedPYCId) && !empty($danhSachPhieu)) {
            // Ưu tiên chọn phiếu "Chờ điều phối" đầu tiên
            foreach ($danhSachPhieu as $p) {
                if (in_array($p['trangThai'], ['Chờ điều phối', 'Chờ xử lý'])) {
                    $selectedPYCId = $p['maPhieuYeuCau'];
                    break;
                }
            }
            if (empty($selectedPYCId)) {
                $selectedPYCId = $danhSachPhieu[0]['maPhieuYeuCau'];
            }
        }

        // Tính toán phân bổ FEFO cho phiếu đang được chọn
        $fefoData = null;
        $errorFefo = null;
        if (!empty($selectedPYCId)) {
            try {
                $fefoData = $this->model->tinhToanPhanBoFEFO($selectedPYCId);
            } catch (Exception $e) {
                $errorFefo = $e->getMessage();
            }
        }

        // Nhúng layout chuẩn ERP và View
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDieuPhoiXuatTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    /**
     * AJAX Action: Lấy chi tiết phân bổ FEFO của 1 phiếu yêu cầu khi người dùng click chọn
     */
    public function handleGetDetails() {
        header('Content-Type: application/json; charset=utf-8');

        if (!$this->checkPermission()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Lỗi phân quyền: Bạn không có quyền thực hiện chức năng này!']);
            exit();
        }

        $maPYC = trim($_GET['maPhieuYeuCau'] ?? $_POST['maPhieuYeuCau'] ?? '');
        if (empty($maPYC)) {
            echo json_encode(['success' => false, 'message' => 'Mã phiếu yêu cầu không được để trống!']);
            exit();
        }

        try {
            $fefoData = $this->model->tinhToanPhanBoFEFO($maPYC);
            echo json_encode(['success' => true, 'data' => $fefoData]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * AJAX Action: Xác nhận lưu Lệnh điều phối xuất kho (FEFO)
     */
    public function handleConfirmDispatch() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ!']);
            exit();
        }

        if (!$this->checkPermission()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Lỗi phân quyền: Chỉ Quản lý kho mới có quyền xác nhận điều phối xuất kho!']);
            exit();
        }

        $maNV = $_SESSION['user']['maNV'] ?? '';
        $maPYC = trim($_POST['maPhieuYeuCau'] ?? '');
        $ghiChu = trim($_POST['ghiChu'] ?? '');

        if (empty($maPYC)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn phiếu yêu cầu cần điều phối!']);
            exit();
        }

        // Gọi Model thực thi Transaction
        $result = $this->model->xacNhanDieuPhoi($maPYC, $maNV, $ghiChu);
        echo json_encode($result);
        exit();
    }

    /**
     * AJAX Action: Lấy dữ liệu để render bản in A4 cho Lệnh điều phối
     */
    public function handleGetPrintData() {
        header('Content-Type: application/json; charset=utf-8');

        $maLenh = trim($_GET['maLenh'] ?? $_POST['maLenh'] ?? '');
        $maPYC = trim($_GET['maPhieuYeuCau'] ?? $_POST['maPhieuYeuCau'] ?? '');

        try {
            if (!empty($maLenh)) {
                $lenh = $this->model->getThongTinInLenhDieuPhoi($maLenh);
                if (!$lenh) {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy dữ liệu lệnh điều phối: ' . htmlspecialchars($maLenh)]);
                    exit();
                }
                echo json_encode(['success' => true, 'type' => 'saved', 'data' => $lenh]);
                exit();
            }

            if (!empty($maPYC)) {
                $fefo = $this->model->tinhToanPhanBoFEFO($maPYC);
                echo json_encode(['success' => true, 'type' => 'preview', 'data' => $fefo]);
                exit();
            }

            echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp mã lệnh hoặc mã phiếu yêu cầu để in!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }
}
