<?php
class cTonKho {
    private $model;

    public function __construct() {
        // Tự động nhận mTonKho, không cần require!
        $this->model = new mTonKho();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vTonKho.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
