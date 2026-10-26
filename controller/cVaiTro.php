<?php
class cVaiTro {
    private $model;

    public function __construct() {
        // Tự động nhận mVaiTro, không cần require!
        $this->model = new mVaiTro();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vVaiTro.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
