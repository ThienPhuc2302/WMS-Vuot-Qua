<?php
/**
 * CONTROLLER: cVaiTro
 * Phụ trách: Quản lý vai trò và phân quyền người dùng
 */
require_once __DIR__ . '/../model/mVaiTro.php';

class cVaiTro {
    private $model;

    public function __construct() {
        $this->model = new mVaiTro();
    }

    public function index() {
        return $this->model->getAll();
    }
}
?>