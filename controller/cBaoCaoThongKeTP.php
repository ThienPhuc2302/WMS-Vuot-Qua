<?php
class cBaoCaoThongKeTP {
    private $model;

    public function __construct() {
        // Tự động nhận mBaoCaoThongKeTP, không cần require!
        $this->model = new mBaoCaoThongKeTP();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBaoCaoThongKeTP.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
