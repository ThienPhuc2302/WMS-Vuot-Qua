<?php
class cPhieuYeuCauXuat {
    private $model;

    public function __construct() {
        // Tự động nhận mPhieuYeuCauXuat, không cần require!
        $this->model = new mPhieuYeuCauXuat();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPhieuYeuCauXuat.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
