<?php
/**
 * MODEL: mTaiKhoan
 * Phụ trách: Quản lý tài khoản & Xác thực người dùng thực tế từ MySQL
 * Bảng dữ liệu tương ứng: TaiKhoan, NhanVien, VaiTro
 */
require_once __DIR__ . '/Database.php';

class mTaiKhoan {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Xác thực thông tin đăng nhập trực tiếp từ CSDL MySQL (100% dữ liệu thật từ bảng TaiKhoan, NhanVien, VaiTro)
     */
    public function kiemTraDangNhap($usernameOrEmail, $password) {
        if (!$this->conn) {
            throw new Exception("LỖI KẾT NỐI CSDL: Không thể kết nối tới cơ sở dữ liệu MySQL quanlykho_vuotqua! Vui lòng kiểm tra dịch vụ MySQL trong XAMPP.");
        }
        if (empty($usernameOrEmail) || empty($password)) {
            return null;
        }

        $u = strtolower(trim($usernameOrEmail));

        try {
            $sql = "
                SELECT 
                    nv.maNV,
                    nv.hoTen,
                    tk.tenDangNhap,
                    tk.matKhau,
                    tk.maVaiTro,
                    vt.tenVaiTro,
                    nv.soDienThoai,
                    nv.email,
                    tk.trangThai
                FROM TaiKhoan tk
                INNER JOIN NhanVien nv ON tk.maNV = nv.maNV
                INNER JOIN VaiTro vt ON tk.maVaiTro = vt.maVaiTro
                WHERE (LOWER(tk.tenDangNhap) = :u1 OR LOWER(nv.email) = :u2)
                  AND tk.trangThai = 'Hoạt động'
                  AND nv.trangThai = 'Hoạt động'
                LIMIT 1
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':u1' => $u, ':u2' => $u]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && !empty($user['matKhau'])) {
                $isMatched = ($password === $user['matKhau']) || password_verify($password, $user['matKhau']);
                if ($isMatched) {
                    return [
                        'maNV'        => $user['maNV'],
                        'hoTen'       => $user['hoTen'],
                        'tenDangNhap' => $user['tenDangNhap'],
                        'email'       => $user['email'],
                        'soDienThoai' => $user['soDienThoai'] ?? '',
                        'maVaiTro'    => $user['maVaiTro'],
                        'vaiTro'      => $user['tenVaiTro']
                    ];
                }
            }

            return null;

        } catch (PDOException $e) {
            throw new Exception("LỖI TRUY VẤN CSDL XÁC THỰC: " . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách tất cả tài khoản từ MySQL
     */
    public function getAll() {
        if (!$this->conn) {
            throw new Exception("Lỗi: Chưa kết nối được với CSDL MySQL quanlykho_vuotqua!");
        }
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    tk.maTaiKhoan,
                    nv.maNV,
                    nv.hoTen,
                    tk.tenDangNhap,
                    nv.email,
                    nv.soDienThoai,
                    tk.maVaiTro,
                    vt.tenVaiTro,
                    tk.trangThai,
                    tk.ngayTao
                FROM TaiKhoan tk
                INNER JOIN NhanVien nv ON tk.maNV = nv.maNV
                INNER JOIN VaiTro vt ON tk.maVaiTro = vt.maVaiTro
                ORDER BY tk.maTaiKhoan ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("LỖI TRUY VẤN DANH SÁCH TÀI KHOẢN: " . $e->getMessage());
        }
    }
}