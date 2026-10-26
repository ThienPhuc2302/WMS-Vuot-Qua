<?php
class cPhieuPheDuyet {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuPheDuyet, không cần require!
        $this->model = new mPhieuPheDuyet();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuPheDuyet.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
