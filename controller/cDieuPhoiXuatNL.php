<?php
class cDieuPhoiXuatNL {
    private $model;

    public function __construct() {
        // Tự động nhận mDieuPhoiXuatNL, không cần require!
        $this->model = new mDieuPhoiXuatNL();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vDieuPhoiXuatNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
