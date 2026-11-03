<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

class AdminStockModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Lấy danh sách sản phẩm theo Thương hiệu
    public function getProductsByBrand($brandId) {
        $sql = "SELECT id, name FROM products WHERE brand_id = ? ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$brandId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy tất cả thương hiệu
    public function getAllBrands() {
        return $this->conn->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Xử lý Nhập kho an toàn Transaction (Lưu lịch sử + Cộng dồn kho)
    public function importStock($productId, $size, $quantity, $importPrice) {
        $productId = (int)$productId;
        $size = trim($size);
        $quantity = (int)$quantity;
        $importPrice = (float)$importPrice;

        if ($productId <= 0 || empty($size) || $quantity <= 0 || $importPrice < 0) {
            return false;
        }

        try {
            $this->conn->beginTransaction();

            // A. Lưu vào bảng lịch sử nhập hàng (stock_imports)
            $sqlHist = "INSERT INTO stock_imports (product_id, size, quantity, import_price) VALUES (?, ?, ?, ?)";
            $stmtHist = $this->conn->prepare($sqlHist);
            $stmtHist->execute([$productId, $size, $quantity, $importPrice]);

            // B. Kiểm tra variant đã tồn tại chưa
            $sqlCheck = "SELECT stock FROM product_variants WHERE product_id = ? AND size = ?";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([$productId, $size]);
            
            if ($stmtCheck->rowCount() > 0) {
                // Đã có -> Cộng dồn số lượng
                $sqlUpdate = "UPDATE product_variants SET stock = stock + ? WHERE product_id = ? AND size = ?";
                $this->conn->prepare($sqlUpdate)->execute([$quantity, $productId, $size]);
            } else {
                // Chưa có -> Tạo biến thể mới
                $sqlInsert = "INSERT INTO product_variants (product_id, size, stock) VALUES (?, ?, ?)";
                $this->conn->prepare($sqlInsert)->execute([$productId, $size, $quantity]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    // 4. Lấy lịch sử nhập kho gom nhóm theo thời gian
    public function getHistory() {
        $sql = "SELECT created_at, 
                       SUM(quantity) as total_qty, 
                       SUM(quantity * import_price) as total_money, 
                       COUNT(*) as items_count 
                FROM stock_imports 
                GROUP BY created_at 
                ORDER BY created_at DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Tìm kiếm lịch sử nhập kho an toàn
    public function searchHistory($keyword) {
        $keyLike = "%$keyword%";
        $sql = "SELECT created_at, 
                       SUM(quantity) as total_qty, 
                       SUM(quantity * import_price) as total_money, 
                       COUNT(*) as items_count 
                FROM stock_imports 
                WHERE created_at LIKE ?
                GROUP BY created_at 
                ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$keyLike]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Chi tiết lô hàng theo thời điểm nhập
    public function getHistoryDetail($createdAt) {
        $sql = "SELECT s.*, p.name, p.image, b.name as brand_name
                FROM stock_imports s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE s.created_at = ?
                ORDER BY s.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$createdAt]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 7. Lấy danh sách tất cả các dòng nhập
    public function getAllImportItems() {
        $sql = "SELECT s.*, p.name, b.name as brand_name
                FROM stock_imports s
                JOIN products p ON s.product_id = p.id
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY s.created_at DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 8. Cảnh báo các biến thể size sắp hết hàng (Low Stock Warning: <= 3 đôi)
    public function getLowStockItems($threshold = 3) {
        $sql = "SELECT p.id as product_id, p.name, p.image, pv.size, pv.stock, b.name as brand_name
                FROM product_variants pv
                JOIN products p ON pv.product_id = p.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE pv.stock <= ?
                ORDER BY pv.stock ASC, p.name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>