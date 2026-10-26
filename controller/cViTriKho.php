<?php
class cViTriKho {
    private $model;

    public function __construct() {
        // Tự động nhận mViTriKho, không cần require!
        $this->model = new mViTriKho();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vViTriKho.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
