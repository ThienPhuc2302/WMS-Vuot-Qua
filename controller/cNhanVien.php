<?php
class cNhanVien {
    private $model;

    public function __construct() {
        // Tự động nhận mNhanVien, không cần require!
        $this->model = new mNhanVien();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vNhanVien.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
