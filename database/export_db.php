<?php
/**
 * Export quanlykho_vuotqua database with 100% clean UTF-8 encoding
 */
$host = 'localhost';
$db   = 'quanlykho_vuotqua';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);

$sql = "-- =====================================================================\n";
$sql .= "-- MariaDB / MySQL Database Dump\n";
$sql .= "-- Hệ Thống Quản Lý Kho Vượt Quá (BiscuitWMS)\n";
$sql .= "-- Database: $db\n";
$sql .= "-- Export Date: " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- =====================================================================\n\n";

$sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
$sql .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
$sql .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
$sql .= "/*!40101 SET NAMES utf8mb4 */;\n";
$sql .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n";
$sql .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
$sql .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
$sql .= "/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n";

$sql .= "CREATE DATABASE IF NOT EXISTS `$db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
$sql .= "USE `$db`;\n\n";

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    $sql .= "-- -------------------------------------------------------------\n";
    $sql .= "-- Cấu trúc bảng `$table`\n";
    $sql .= "-- -------------------------------------------------------------\n";
    $sql .= "DROP TABLE IF EXISTS `$table`;\n";
    
    $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    $createTableSql = $createStmt['Create Table'] ?? '';
    $sql .= $createTableSql . ";\n\n";
    
    // Dump data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $sql .= "-- Dữ liệu bảng `$table` (" . count($rows) . " bản ghi)\n";
        $sql .= "LOCK TABLES `$table` WRITE;\n";
        $sql .= "/*!40000 ALTER TABLE `$table` DISABLE KEYS */;\n";
        
        $colNames = array_keys($rows[0]);
        $escapedCols = array_map(function($c) { return "`$c`"; }, $colNames);
        
        $valueRows = [];
        foreach ($rows as $row) {
            $vals = [];
            foreach ($row as $val) {
                if ($val === null) {
                    $vals[] = "NULL";
                } elseif (is_numeric($val) && !is_string($val)) {
                    $vals[] = $val;
                } else {
                    $vals[] = $pdo->quote($val);
                }
            }
            $valueRows[] = "(" . implode(", ", $vals) . ")";
        }
        
        $sql .= "INSERT INTO `$table` (" . implode(", ", $escapedCols) . ") VALUES\n";
        $sql .= implode(",\n", $valueRows) . ";\n";
        $sql .= "/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n";
        $sql .= "UNLOCK TABLES;\n\n";
    }
}

$sql .= "/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;\n";
$sql .= "/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;\n";
$sql .= "/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;\n";
$sql .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
$sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
$sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
$sql .= "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n";

$targetWorkspace = __DIR__ . "/quanlykho.sql";
$targetXampp = "D:/xamp/htdocs/QuanLyKho_VuotQua/database/quanlykho.sql";

file_put_contents($targetWorkspace, $sql);
echo "Exported to $targetWorkspace (" . strlen($sql) . " bytes)\n";

if (is_dir(dirname($targetXampp))) {
    file_put_contents($targetXampp, $sql);
    echo "Exported to $targetXampp (" . strlen($sql) . " bytes)\n";
}
