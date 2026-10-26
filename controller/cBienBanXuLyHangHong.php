<?php
class cBienBanXuLyHangHong {
    private $model;

    public function __construct() {
        // Tự động nhận mBienBanXuLyHangHong, không cần require!
        $this->model = new mBienBanXuLyHangHong();
    }

    public function index() {
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vBienBanXuLyHangHong.php';
        include __DIR__ . '/../view/layout/footer.php';
    }

    // === 👉 BẮT ĐẦU VIẾT CÁC HÀM XỬ LÝ NGHIỆP VỤ CỦA BẠN TỪ ĐÂY ===
}
