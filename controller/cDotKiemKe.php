<?php
class cDotKiemKe {
    private $model;

    public function __construct() {
        // Tự động nhận mDotKiemKe, không cần require!
        $this->model = new mDotKiemKe();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDotKiemKe.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
