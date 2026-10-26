<?php
class cDieuPhoiNhapNL {
    private $model;

    public function __construct() {
        // Tự động nhận mDieuPhoiNhapNL, không cần require!
        $this->model = new mDieuPhoiNhapNL();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDieuPhoiNhapNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
