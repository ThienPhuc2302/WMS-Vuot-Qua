<?php
class cPhieuYeuCauNhap {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuYeuCauNhap, không cần require!
        $this->model = new mPhieuYeuCauNhap();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuYeuCauNhap.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
