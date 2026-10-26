<?php
class cNguyenLieu {
    private $model;

    public function __construct() {
        // Tự động nhận mNguyenLieu, không cần require!
        $this->model = new mNguyenLieu();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vNguyenLieu.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
