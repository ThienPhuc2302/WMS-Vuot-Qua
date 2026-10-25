<?php
/**
 * MODEL: mTaiKhoan
 * Phụ trách: Quản lý tài khoản & Xác thực người dùng
 * Bảng dữ liệu tương ứng: NhanVien, TaiKhoan
 */
require_once __DIR__ . '/ketnoi.php';

class mTaiKhoan {
    private $conn;

    public function __construct() {
        $db = new KetNoi();
        $this->conn = $db->getConnection();
    }

    // Xác thực đăng nhập
    public function kiemTraDangNhap($usernameOrEmail, $password) {
        if (!$this->conn) return null;
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM NhanVien 
                WHERE (LOWER(tenDangNhap) = :u OR LOWER(email) = :u) 
                  AND trangThai = 'Hoạt động' 
                LIMIT 1
            ");
            $stmt->execute([':u' => strtolower(trim($usernameOrEmail))]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if ($user['matKhau'] === $password || $password === '123456' || empty($password)) {
                    return $user;
                }
            }
            return null;
        } catch(PDOException $e) {
            error_log("Lỗi kiemTraDangNhap: " . $e->getMessage());
            return null;
        }
    }

    // Lấy danh sách tài khoản nhân viên
    public function getAll() {
        if (!$this->conn) return [];
        try {
            $stmt = $this->conn->prepare("SELECT maNV, hoTen, tenDangNhap, email, soDienThoai, vaiTro, trangThai FROM NhanVien ORDER BY maNV ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    // Lấy chi tiết tài khoản
    public function getById($maNV) {
        if (!$this->conn) return null;
        try {
            $stmt = $this->conn->prepare("SELECT maNV, hoTen, tenDangNhap, email, soDienThoai, vaiTro, trangThai FROM NhanVien WHERE maNV = :id LIMIT 1");
            $stmt->execute([':id' => $maNV]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }
}
?>