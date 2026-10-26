<?php
class cPheDuyetXuatBoSung {
    private $model;

    public function __construct() {
        // Tự động nhận mPheDuyetXuatBoSung, không cần require!
        $this->model = new mPheDuyetXuatBoSung();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vPheDuyetXuatBoSung.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
