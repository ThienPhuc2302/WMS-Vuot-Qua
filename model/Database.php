<?php
/**
 * LỚP KẾT NỐI CƠ SỞ DỮ LIỆU MYSQL (DATABASE SINGLETON)
 * Áp dụng Design Pattern Singleton: Chỉ tạo duy nhất 1 kết nối PDO trong toàn bộ vòng đời ứng dụng.
 */
class Database {
    private static $instance = null;
    private $conn = null;

    private $host = "localhost";
    private $db_name = "quanlykho_vuotqua";
    private $username = "root";
    private $password = "";

    // Ngăn chặn việc khởi tạo đối tượng trực tiếp từ bên ngoài bằng "new Database()"
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            $errorMsg = "LỖI KẾT NỐI CƠ SỞ DỮ LIỆU MYSQL ({$this->db_name}): " . $e->getMessage();
            error_log($errorMsg);
            throw new Exception($errorMsg, (int)$e->getCode(), $e);
        }
    }

    // Mẫu Singleton: Trả về thể hiện duy nhất của Database
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Trả về đối tượng kết nối PDO
    public function getConnection() {
        return $this->conn;
    }

    // Chặn sao chép đối tượng (clone)
    private function __clone() {}

    // Chặn deserialize đối tượng
    public function __wakeup() {
        throw new Exception("Không thể deserialize một đối tượng Singleton.");
    }
}
