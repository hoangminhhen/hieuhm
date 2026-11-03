<?php
require_once __DIR__ . '/../config/db.php';

class AdminBrandModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        try {
            if (!$this->conn) return [];
            $stmt = $this->conn->prepare("SELECT * FROM brands ORDER BY id ASC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function searchBrands($keyword) {
        try {
            if (!$this->conn) return [];
            $keyword = "%$keyword%";
            $stmt = $this->conn->prepare("SELECT * FROM brands WHERE name LIKE ? ORDER BY id ASC");
            $stmt->execute([$keyword]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function insert($name) {
        try {
            if (!$this->conn) return false;
            $stmt = $this->conn->prepare("INSERT INTO brands (name) VALUES (?)");
            return $stmt->execute([trim($name)]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id) {
        try {
            if (!$this->conn) return false;
            $stmt = $this->conn->prepare("DELETE FROM brands WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getById($id) {
        try {
            if (!$this->conn) return null;
            $stmt = $this->conn->prepare("SELECT * FROM brands WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function update($id, $name) {
        try {
            if (!$this->conn) return false;
            $stmt = $this->conn->prepare("UPDATE brands SET name = ? WHERE id = ?");
            return $stmt->execute([trim($name), (int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProductsByBrandId($brandId) {
        try {
            if (!$this->conn) return [];
            $sql = "SELECT * FROM products WHERE brand_id = ? ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([(int)$brandId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }
}
?>