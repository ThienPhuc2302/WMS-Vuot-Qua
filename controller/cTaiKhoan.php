<?php
/**
 * CONTROLLER: cTaiKhoan
 * Phụ trách: Quản lý tài khoản, Đăng nhập, Đăng xuất, Phân quyền
 */
require_once __DIR__ . '/../model/mTaiKhoan.php';

class cTaiKhoan {
    private $model;

    public function __construct() {
        $this->model = new mTaiKhoan();
    }

    // Xử lý đăng nhập
    public function xuLyDangNhap($username, $password) {
        if (empty($username)) {
            return ['success' => false, 'message' => 'Vui lòng nhập tên đăng nhập!'];
        }
        $user = $this->model->kiemTraDangNhap($username, $password);
        if ($user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = [
                'maNV'        => $user['maNV'],
                'hoTen'       => $user['hoTen'],
                'tenDangNhap' => $user['tenDangNhap'],
                'vaiTro'      => $user['vaiTro']
            ];
            return ['success' => true, 'user' => $_SESSION['user']];
        }
        return ['success' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác!'];
    }

    // Xử lý đăng xuất
    public function xuLyDangXuat() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['user']);
        session_destroy();
        return true;
    }

    // Lấy thông tin user hiện tại
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    // Lấy danh sách tài khoản
    public function index() {
        return $this->model->getAll();
    }
}
?>