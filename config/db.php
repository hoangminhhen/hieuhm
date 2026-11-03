<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "shop_giay_db";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Tăng cường bảo mật SQL Injection
            ];
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $exception) {
            error_log("Lỗi kết nối cơ sở dữ liệu: " . $exception->getMessage());
            // Trả về null thay vì echo thẳng thông tin nhạy cảm của server
            $this->conn = null;
        }
        return $this->conn;
    }
}
?>