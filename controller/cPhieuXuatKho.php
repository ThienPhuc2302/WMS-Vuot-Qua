<?php
class cPhieuXuatKho {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuXuatKho, không cần require!
        $this->model = new mPhieuXuatKho();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuXuatKho.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
