<?php
class cBienBanThieuHang {
    private $model;

    public function __construct() {
        // Tự động nhận mBienBanThieuHang, không cần require!
        $this->model = new mBienBanThieuHang();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanThieuHang.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
