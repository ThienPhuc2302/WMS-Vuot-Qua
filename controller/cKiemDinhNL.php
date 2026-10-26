<?php
class cKiemDinhNL {
    private $model;

    public function __construct() {
        // Tự động nhận mKiemDinhNL, không cần require!
        $this->model = new mKiemDinhNL();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vKiemDinhNL.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
