<?php
/**
 * =========================================================================
 * FRONT CONTROLLER & ROUTER TRUNG TÂM (index.php)
 * =========================================================================
 * 1. Đăng ký Autoloader tự động nạp Class (Chuẩn PSR-4).
 * 2. Điều phối tự động (Dynamic Routing) giữa Router -> Controller -> Model -> View.
 * 3. Bảo vệ hệ thống (Auth Guard) & Phân quyền truy cập (RBAC) 100% CSDL.
 * =========================================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tự động cấp mã bảo vệ CSRF Token tập trung cho toàn bộ phiên làm việc
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 1. TỰ ĐỘNG TÌM VÀ NẠP CLASS (AUTOLOADING) - KHÔNG AI CẦN GÕ REQUIRE
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/model/' . $className . '.php',
        __DIR__ . '/controller/' . $className . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$page = $_GET['page'] ?? 'trangchu';
$page = basename($page);

// 2. NẾU VÀO TRANG CHỦ
if ($page === 'trangchu' || $page === 'index' || empty($page)) {
    include __DIR__ . '/view/trangchu.php';
    exit();
}

// 3. BẢO VỆ TRANG (AUTH GUARD) - CHƯA ĐĂNG NHẬP THẬT THÌ CHẶN NGAY LẬP TỨC
if (!isset($_SESSION['user']) || empty($_SESSION['user']['maNV'])) {
    header("Location: index.php?page=trangchu&error=unauthorized");
    exit();
}

// 4. PHÂN QUYỀN TRUY CẬP (RBAC) DỰA VÀO CSDL THỰC TẾ (6 VAI TRÒ CHUẨN ĐẶC TẢ)
$rawRole = $_SESSION['user']['maVaiTro'] ?? ($_SESSION['user']['vaiTro'] ?? '');
$roleMap = [
    'VT01'         => 'Admin',        'Admin'         => 'Admin',        'Quản trị viên' => 'Admin',
    'VT02'         => 'QuanLyKho',    'QuanLyKho'     => 'QuanLyKho',    'Quản lý kho'   => 'QuanLyKho',
    'VT03'         => 'NhanVienKho',  'NhanVienKho'   => 'NhanVienKho',  'Thủ kho'       => 'NhanVienKho', 'Nhân viên kho' => 'NhanVienKho',
    'VT05'         => 'KiemKe',       'KiemKe'        => 'KiemKe',       'Nhóm kiểm kê'  => 'KiemKe',      'Nhân viên kiểm kê' => 'KiemKe',
    'VT06'         => 'QC',           'QC'            => 'QC',           'Bộ phận QC'    => 'QC',          'KCS/QC' => 'QC', 'KCS' => 'QC',
    'VT07'         => 'BanGiamDoc',   'BanGiamDoc'    => 'BanGiamDoc',   'Ban giám đốc'  => 'BanGiamDoc'
];

if (!isset($roleMap[$rawRole])) {
    die("
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 25px; border: 2px solid #e11d48; border-radius: 12px; background: #fff1f2; color: #9f1239;'>
            <h2 style='margin-top: 0; color: #be123c;'>⛔ Lỗi Phân Quyền Truy Cập (RBAC)</h2>
            <p>Mã vai trò <code>" . htmlspecialchars($rawRole) . "</code> của tài khoản chưa được định nghĩa danh mục quyền trong hệ thống.</p>
            <p><a href='logout.php' style='color: #be123c; font-weight: bold;'>Đăng xuất và đăng nhập lại</a></p>
        </div>
    ");
}

$currentRole = $roleMap[$rawRole];

// Danh sách các trang được phép truy cập theo từng vai trò (100% khớp bảng phân quyền đặc tả)
$roleAllowedPages = [
    // 1. Quản trị viên (2 chức năng)
    'Admin' => [
        'vTaiKhoan', 'vVaiTro'
    ],
    // 2. Quản lý kho (12 chức năng)
    'QuanLyKho' => [
        'vPhieuYeuCauNhapXuat', 'vNguyenLieu', 'vDieuPhoiNhapNL', 'vDieuPhoiXuatNL',
        'vDieuPhoiNhapTP', 'vThanhPham', 'vDieuPhoiXuatTP', 'vDeNghiTieuHuy',
        'vKho', 'vXuLyChenhLech', 'vBaoCaoNhapXuat', 'vTonKho'
    ],
    // 3. Nhân viên kho (5 chức năng)
    'NhanVienKho' => [
        'vPhieuNhapNL', 'vPhieuXuatNL', 'vPhieuNhapTP', 'vPhieuXuatTP', 'vViTriKho'
    ],
    // 4. Bộ phận QC (2 chức năng)
    'QC' => [
        'vKiemDinhNL', 'vKiemDinhTP'
    ],
    // 5. Nhóm kiểm kê (1 chức năng)
    'KiemKe' => [
        'vBienBanKiemKe'
    ],
    // 6. Ban giám đốc (5 chức năng)
    'BanGiamDoc' => [
        'vBaoCaoNhapXuat', 'vTonKho', 'vPheDuyetXuatBoSung', 'vPhieuPheDuyet', 'vBaoCaoThongKeTP'
    ]
];

// Trang hạ cánh mặc định khi truy cập sai quyền hoặc đăng nhập (Khớp Screenshot Danh sách 6 tài khoản)
$roleDefaultPages = [
    'Admin'        => 'vTaiKhoan',         // Quản trị viên -> Quản lý tài khoản
    'QuanLyKho'    => 'vDieuPhoiXuatTP',   // Quản lý kho -> Điều phối xuất thành phẩm
    'NhanVienKho'  => 'vPhieuNhapTP',      // Nhân viên kho -> Lập phiếu nhập thành phẩm
    'QC'           => 'vKiemDinhNL',       // Bộ phận QC -> Kiểm định nguyên liệu
    'KiemKe'       => 'vBienBanKiemKe',    // Nhóm kiểm kê -> Lập biên bản kiểm kê
    'BanGiamDoc'   => 'vPhieuPheDuyet'     // Ban giám đốc -> Phê duyệt kiểm kê & tiêu hủy
];

$allowedPages = $roleAllowedPages[$currentRole];
if (!in_array($page, $allowedPages)) {
    $targetPage = $roleDefaultPages[$currentRole];
    header("Location: index.php?page={$targetPage}&error=forbidden");
    exit();
}

// 5. ĐIỀU HƯỚNG TỰ ĐỘNG QUA CONTROLLER (PURE MVC - KHÔNG DÙNG CỬA NGÁCH)
$controllerName = 'c' . substr($page, 1);
$controllerFile = __DIR__ . "/controller/{$controllerName}.php";

if (!file_exists($controllerFile)) {
    http_response_code(404);
    die("
        <div style='font-family: Arial, sans-serif; max-width: 650px; margin: 40px auto; padding: 25px; border: 2px solid #e11d48; border-radius: 12px; background: #fff1f2; color: #9f1239;'>
            <h2 style='margin-top: 0; color: #be123c;'>⚠️ Lỗi 404 - Không Tìm Thấy Controller</h2>
            <p>Kiến trúc Pure MVC bắt buộc mọi trang giao diện phải được khởi tạo và điều phối bởi Controller tương ứng.</p>
            <p><strong>Chi tiết:</strong> Không tìm thấy file Controller <code>controller/{$controllerName}.php</code> cho trang <code>view/{$page}.php</code>.</p>
            <p style='margin-top: 15px;'><a href='index.php' style='color: #be123c; font-weight: bold;'>Quay lại trang làm việc</a></p>
        </div>
    ");
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'index';

// Tự động kiểm tra CSRF Token cho mọi yêu cầu POST có action (Lưu/Sửa/Xóa)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['action']) || isset($_GET['action']))) {
    $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($csrfToken) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
        header('HTTP/1.1 403 Forbidden');
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Lỗi bảo mật (CSRF Token không hợp lệ hoặc đã hết hạn)! Vui lòng tải lại trang.']);
        exit();
    }
}

// Khởi tạo Controller (Autoload tự nạp file)
$controller = new $controllerName();

// =========================================================================
// ĐIỀU PHỐI ACTION TỔNG QUÁT (UNIVERSAL MVC ACTION DISPATCHER)
// Hoạt động tự động, nhất quán cho toàn bộ 34 Controller trong hệ thống:
// - Nếu không có action hoặc action=index: Gọi $controller->index() để tải View.
// - Nếu có action (ví dụ: 'save', 'delete', 'search', 'export', ...):
//   Tự động phân giải theo các quy chuẩn đặt tên phương thức chuẩn MVC:
//   1. Tên chính xác: $action (ví dụ: save())
//   2. Quy chuẩn CamelCase: lcfirst(PascalCase) (ví dụ: saveTpInbound(), getPrintData())
//   3. Quy chuẩn Handler: handle + PascalCase (ví dụ: handleSave())
//   4. Quy chuẩn Action: action + PascalCase (ví dụ: actionSave())
// =========================================================================
if ($action === '' || $action === 'index') {
    if (method_exists($controller, 'index')) {
        $controller->index();
        exit();
    }
    http_response_code(500);
    die("Lỗi MVC: Controller {$controllerName} không chứa phương thức index() để hiển thị giao diện!");
}

// Chuẩn hóa tên hàm tìm kiếm (tổng quát 100% cho tất cả 34 Controller)
$pascalAction = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $action)));
$camelAction = lcfirst($pascalAction);

$candidates = array_unique([
    $action,
    $camelAction,
    'handle' . $pascalAction,
    'action' . $pascalAction
]);

foreach ($candidates as $method) {
    if (method_exists($controller, $method)) {
        $controller->$method();
        exit();
    }
}

// Báo lỗi minh bạch nếu Controller không hỗ trợ Action được gọi (Tuân thủ Quy tắc Zero Defensive Fallbacks)
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => false,
    'message' => "Lỗi điều phối (Pure MVC): Controller {$controllerName} không có phương thức xử lý cho action '{$action}'! Các phương thức đã tìm kiếm: " . implode(', ', $candidates)
], JSON_UNESCAPED_UNICODE);
exit();