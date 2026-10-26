<?php
class cLoThanhPham {
    private $model;

    public function __construct() {
        // Tự động nhận mLoThanhPham, không cần require!
        $this->model = new mLoThanhPham();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vLoThanhPham.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
