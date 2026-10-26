<?php
class cDeNghiTieuHuy {
    private $model;

    public function __construct() {
        // Tự động nhận mDeNghiTieuHuy, không cần require!
        $this->model = new mDeNghiTieuHuy();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDeNghiTieuHuy.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
