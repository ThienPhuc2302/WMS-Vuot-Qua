<?php
/**
 * MODEL: mVaiTro
 * Phụ trách: Quản lý vai trò & quyền hạn hệ thống
 * Bảng dữ liệu tương ứng: VaiTro, NhanVien
 */
require_once __DIR__ . '/ketnoi.php';

class mVaiTro {
    private $conn;

    public function __construct() {
        $db = new KetNoi();
        $this->conn = $db->getConnection();
    }

    // Lấy danh sách vai trò hệ thống
    public function getAll() {
        if (!$this->conn) return $this->getDefaultRoles();
        try {
            $stmt = $this->conn->prepare("SELECT * FROM VaiTro ORDER BY maVaiTro ASC");
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return (!empty($data)) ? $data : $this->getDefaultRoles();
        } catch(PDOException $e) {
            return $this->getDefaultRoles();
        }
    }

    private function getDefaultRoles() {
        return [
            ['maVaiTro' => 'Admin', 'tenVaiTro' => 'Quản trị viên', 'moTa' => 'Quản lý tài khoản, vai trò và bảo mật hệ thống'],
            ['maVaiTro' => 'QuanLyKho', 'tenVaiTro' => 'Quản lý kho', 'moTa' => 'Điều phối nhập xuất, duyệt kế hoạch, quản lý kho'],
            ['maVaiTro' => 'NhanVienKho', 'tenVaiTro' => 'Nhân viên kho', 'moTa' => 'Lập phiếu nhập xuất, quản lý vị trí kệ tầng'],
            ['maVaiTro' => 'QC', 'tenVaiTro' => 'Bộ phận QC', 'moTa' => 'Kiểm định chất lượng nguyên liệu và thành phẩm'],
            ['maVaiTro' => 'KiemKe', 'tenVaiTro' => 'Nhóm kiểm kê', 'moTa' => 'Thực hiện đợt kiểm kê, lập biên bản đối soát'],
            ['maVaiTro' => 'BanGiamDoc', 'tenVaiTro' => 'Ban giám đốc', 'moTa' => 'Phê duyệt xuất bổ sung, xử lý tiêu hủy, xem báo cáo']
        ];
    }
}
?>