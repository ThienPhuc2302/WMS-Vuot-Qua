<?php
class cBienBanKiemKe {
    private $model;

    public function __construct() {
        // Tự động nhận mBienBanKiemKe, không cần require!
        $this->model = new mBienBanKiemKe();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanKiemKe.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
