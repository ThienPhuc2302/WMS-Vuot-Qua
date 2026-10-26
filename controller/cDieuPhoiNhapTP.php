<?php
class cDieuPhoiNhapTP {
    private $model;

    public function __construct() {
        // Tự động nhận mDieuPhoiNhapTP, không cần require!
        $this->model = new mDieuPhoiNhapTP();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDieuPhoiNhapTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
