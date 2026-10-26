<?php
class cXuLyChenhLech {
    private $model;

    public function __construct() {
        // Tự động nhận mXuLyChenhLech, không cần require!
        $this->model = new mXuLyChenhLech();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vXuLyChenhLech.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
