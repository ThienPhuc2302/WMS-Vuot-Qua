<?php
class cPhieuXuatTP {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuXuatTP, không cần require!
        $this->model = new mPhieuXuatTP();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuXuatTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
