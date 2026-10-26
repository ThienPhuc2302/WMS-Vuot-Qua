<?php
class cPhieuYeuCauNhapXuat {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuYeuCauNhapXuat, không cần require!
        $this->model = new mPhieuYeuCauNhapXuat();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuYeuCauNhapXuat.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
