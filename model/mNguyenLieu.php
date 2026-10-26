<?php
class mNguyenLieu {
    private $conn;

    public function __construct() {
        // Tự động nhận kết nối MySQL từ Database.php, không cần require!
        $this->conn = Database::getInstance()->getConnection();
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM TRUY VẤN SQL CỦA BẠN TỪ ĐÂY ===
}
