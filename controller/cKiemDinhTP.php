<?php
class cKiemDinhTP {
    private $model;

    public function __construct() {
        // Tự động nhận mKiemDinhTP, không cần require!
        $this->model = new mKiemDinhTP();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vKiemDinhTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
