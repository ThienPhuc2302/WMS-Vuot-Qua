<?php
class cThanhPham {
    private $model;

    public function __construct() {
        // Tự động nhận mThanhPham, không cần require!
        $this->model = new mThanhPham();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vThanhPham.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
