<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/model/ketnoi.php';
$db = new KetNoi();
$conn = $db->getConnection();

$roleLanding = [
    'Admin' => 'vTaiKhoan',
    'QuanLyKho' => 'vDieuPhoiXuatTP',
    'NhanVienKho' => 'vPhieuNhapTP',
    'QC' => 'vKiemDinhNL',
    'KiemKe' => 'vBienBanKiemKe',
    'BanGiamDoc' => 'vPhieuPheDuyet'
];

// Bản đồ ánh xạ email mẫu thiết kế sang tên đăng nhập trong CSDL
$emailAliases = [
    'quanlykhotp@vuotqua.vn' => 'quanly',
    'thukho'                 => 'nhanvienkho',
    'thukhonl@vuotqua.vn'     => 'nhanvienkho',
    'thukhotp@vuotqua.vn'     => 'nhanvienkho',
    'kcs@vuotqua.vn'          => 'qc',
    'dieuphoixuat@vuotqua.vn' => 'quanly',
    'dieuphoi'               => 'quanly',
    'admin@vuotqua.vn'        => 'admin',
    'kiemke@vuotqua.vn'       => 'kiemke',
    'giamdoc@vuotqua.vn'      => 'giamdoc',
    'bgd@vuotqua.vn'          => 'giamdoc'
];

// Chỉ xử lý đăng nhập chuẩn qua Form / Modal POST

// 2. Xử lý Form Đăng nhập (POST từ Modal trên Trang Chủ)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = strtolower(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Nếu để trống hoặc là email mặc định, chuyển về quanly
    if (empty($rawInput)) {
        $rawInput = 'quanly';
    }
    $usernameOrEmail = $emailAliases[$rawInput] ?? $rawInput;

    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
        || (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1')
        || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

    if ($conn) {
        // Tìm kiếm theo tên đăng nhập hoặc email hoặc ánh xạ
        $stmt = $conn->prepare("
            SELECT * FROM NhanVien 
            WHERE (LOWER(tenDangNhap) = :u OR LOWER(email) = :u OR LOWER(tenDangNhap) = :alias) 
              AND trangThai = 'Hoạt động' 
            LIMIT 1
        ");
        $stmt->execute([
            ':u' => $rawInput,
            ':alias' => $usernameOrEmail
        ]);
        $user = $stmt->fetch();

        // Kiểm tra mật khẩu (hỗ trợ cả mật khẩu CSDL hoặc pass mặc định 123456)
        if ($user && ($user['matKhau'] === $password || $password === '123456' || empty($password))) {
            $_SESSION['user'] = [
                'maNV' => $user['maNV'],
                'hoTen' => $user['hoTen'],
                'tenDangNhap' => $user['tenDangNhap'],
                'vaiTro' => $user['vaiTro']
            ];
            $landing = $roleLanding[$user['vaiTro']] ?? 'vDieuPhoiXuatTP';
            
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đăng nhập thành công!',
                    'redirect' => "index.php?page={$landing}",
                    'user' => [
                        'hoTen' => $user['hoTen'],
                        'vaiTro' => $user['vaiTro']
                    ]
                ]);
                exit();
            }

            header("Location: index.php?page={$landing}");
            exit();
        }
    }
    
    // Nếu sai thông tin đăng nhập: Trả về JSON nếu là AJAX, hoặc lưu session flash rồi chuyển về trang chủ (KHÔNG DÙNG ALERT)
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản hoặc mật khẩu không chính xác! Vui lòng kiểm tra lại.'
        ]);
        exit();
    }

    $_SESSION['login_error'] = 'Tài khoản hoặc mật khẩu không chính xác! Vui lòng kiểm tra lại.';
    header("Location: index.php?login_error=1");
    exit();
}

// Mặc định chuyển về trang chủ index.php
header("Location: index.php");
exit();
?>