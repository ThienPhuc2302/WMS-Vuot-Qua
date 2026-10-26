<?php
/**
 * =========================================================================
 * CONTROLLER: cTaiKhoan
 * Use Case: UC Quản lý tài khoản (Dành cho Quản trị viên - VT01)
 * =========================================================================
 */
class cTaiKhoan {
    private $model;

    public function __construct() {
        $this->model = new mTaiKhoan();
    }

    /**
     * Hiển thị danh sách tài khoản người dùng từ CSDL MySQL
     */
    public function index() {
        $dsTaiKhoan = $this->model->getAll();
        include __DIR__ . '/../view/layout/header.php';
        include __DIR__ . '/../view/vTaiKhoan.php';
        include __DIR__ . '/../view/layout/footer.php';
    }
}