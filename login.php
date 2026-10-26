<?php
/**
 * XỬ LÝ ĐĂNG NHẬP HỆ THỐNG WMS
 * Xác thực tài khoản 100% từ CSDL MySQL (Bảng TaiKhoan & NhanVien)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/model/Database.php';
require_once __DIR__ . '/model/mTaiKhoan.php';

// Bản đồ trang chuyển hướng mặc định theo mã vai trò từ CSDL (Chuẩn 6 vai trò dự án)
$roleLanding = [
    'VT01'        => 'vTaiKhoan',            // Quản trị viên (Admin) -> Quản lý tài khoản
    'Admin'       => 'vTaiKhoan',
    'VT02'        => 'vDieuPhoiXuatTP',      // Quản lý kho -> Điều phối xuất thành phẩm
    'QuanLyKho'   => 'vDieuPhoiXuatTP',
    'VT03'        => 'vPhieuNhapTP',         // Nhân viên kho (Thủ kho) -> Lập phiếu nhập thành phẩm
    'NhanVienKho' => 'vPhieuNhapTP',
    'VT05'        => 'vBienBanKiemKe',       // Nhóm kiểm kê -> Lập biên bản kiểm kê
    'KiemKe'      => 'vBienBanKiemKe',
    'VT06'        => 'vKiemDinhNL',          // Bộ phận QC -> Kiểm định nguyên liệu
    'QC'          => 'vKiemDinhNL',
    'VT07'        => 'vPhieuPheDuyet',       // Ban giám đốc -> Phê duyệt kiểm kê & tiêu hủy
    'BanGiamDoc'  => 'vPhieuPheDuyet'
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
        || (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1')
        || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

    // Kiểm tra không để trống
    if (empty($rawInput) || empty($password)) {
        $msg = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit();
        }
        $_SESSION['login_error'] = $msg;
        header("Location: index.php?login_error=1");
        exit();
    }

    // Xác thực thực tế qua Model
    try {
        $modelTK = new mTaiKhoan();
        $user = $modelTK->kiemTraDangNhap($rawInput, $password);
    } catch (Exception $e) {
        // Ghi chi tiết kỹ thuật vào log hệ thống để phục vụ quản trị và bảo trì
        error_log("LOGIN_ERROR: " . $e->getMessage());
        // Hiển thị thông báo an toàn, ngăn lộ lọt thông tin nội bộ (OWASP A05)
        $msg = 'Hệ thống gặp sự cố kết nối cơ sở dữ liệu! Vui lòng kiểm tra dịch vụ MySQL trong XAMPP hoặc liên hệ Quản trị viên.';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit();
        }
        $_SESSION['login_error'] = $msg;
        header("Location: index.php?login_error=1");
        exit();
    }

    if ($user) {
        // Đổi mới session ID sau khi xác thực thành công để chống tấn công cố định phiên (Session Fixation - OWASP)
        session_regenerate_id(true);

        // Đăng nhập thành công -> Lưu session thực tế từ CSDL
        $_SESSION['user'] = [
            'maNV'        => $user['maNV'],
            'hoTen'       => $user['hoTen'],
            'tenDangNhap' => $user['tenDangNhap'],
            'email'       => $user['email'] ?? '',
            'maVaiTro'    => $user['maVaiTro'],
            'vaiTro'      => $user['vaiTro']
        ];

        $landing = $roleLanding[$user['maVaiTro']] ?? ($roleLanding[$user['vaiTro']] ?? null);
        if (!$landing) {
            $msg = "Lỗi phân quyền: Chưa cấu hình trang khởi đầu cho vai trò '{$user['vaiTro']}'!";
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit();
            }
            $_SESSION['login_error'] = $msg;
            header("Location: index.php?login_error=1");
            exit();
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'  => true,
                'message'  => 'Đăng nhập thành công!',
                'redirect' => "index.php?page={$landing}",
                'user'     => [
                    'hoTen'    => $user['hoTen'],
                    'vaiTro'   => $user['vaiTro'],
                    'maVaiTro' => $user['maVaiTro']
                ]
            ]);
            exit();
        }

        header("Location: index.php?page={$landing}");
        exit();
    } else {
        $msg = 'Tên đăng nhập hoặc mật khẩu không chính xác! Vui lòng kiểm tra lại.';
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => $msg]);
            exit();
        }
        $_SESSION['login_error'] = $msg;
        header("Location: index.php?login_error=1");
        exit();
    }
}

// Nếu không phải POST, chuyển về trang chủ
header("Location: index.php");
exit();