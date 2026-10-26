<?php
class cPhieuNhapNL {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuNhapNL, không cần require!
        $this->model = new mPhieuNhapNL();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuNhapNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
