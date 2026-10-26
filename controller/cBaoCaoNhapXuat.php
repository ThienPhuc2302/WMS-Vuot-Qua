<?php
class cBaoCaoNhapXuat {
    private $model;

    public function __construct() {
        // Tự động nhận mBaoCaoNhapXuat, không cần require!
        $this->model = new mBaoCaoNhapXuat();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBaoCaoNhapXuat.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
