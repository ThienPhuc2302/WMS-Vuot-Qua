<?php
class cPhieuXuatNL {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuXuatNL, không cần require!
        $this->model = new mPhieuXuatNL();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuXuatNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
