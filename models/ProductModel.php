<?php
require_once __DIR__ . '/../config/db.php';

class ProductModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // =================================================================
    // CÁC HÀM TRUY VẤN TỐI ƯU HÓA (CHỐNG N+1 QUERY & SQL INJECTION)
    // =================================================================

    /**
     * Lấy danh sách sản phẩm với JOIN tối ưu, gom sizes tránh N+1 query
     */
    public function getProducts($keyword = '', $categoryId = null, $sort = 'newest', $limit = 9, $offset = 0, $brandId = null) {
        $sql = "SELECT p.*, 
                       c.name as cat_name, 
                       b.name as brand_name,
                       GROUP_CONCAT(DISTINCT pv.size ORDER BY CAST(pv.size AS DECIMAL(4,1)) ASC SEPARATOR ', ') as available_sizes,
                       SUM(COALESCE(pv.stock, 0)) as total_stock
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.stock > 0
                WHERE 1=1";
        
        $params = [];

        // Tìm kiếm theo tên hoặc mô tả
        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        // Lọc theo danh mục
        if (!empty($categoryId)) {
            $sql .= " AND p.category_id = ?";
            $params[] = (int)$categoryId;
        }

        // Lọc theo thương hiệu
        if (!empty($brandId)) {
            $sql .= " AND p.brand_id = ?";
            $params[] = (int)$brandId;
        }

        $sql .= " GROUP BY p.id";

        // Sắp xếp
        switch ($sort) {
            case 'price_asc': 
                $sql .= " ORDER BY p.price ASC"; 
                break;
            case 'price_desc': 
                $sql .= " ORDER BY p.price DESC"; 
                break;
            case 'name_az': 
                $sql .= " ORDER BY p.name ASC"; 
                break;
            case 'views_desc':
                $sql .= " ORDER BY p.views DESC";
                break;
            default: 
                $sql .= " ORDER BY p.id DESC"; 
                break;
        }

        // Phân trang với ép kiểu an toàn chống SQL Injection
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql .= " LIMIT $offset, $limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số lượng sản phẩm (phục vụ phân trang)
     */
    public function countTotal($keyword = '', $categoryId = null, $brandId = null) {
        $sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
        $params = [];
        if (!empty($keyword)) { 
            $sql .= " AND (name LIKE ? OR description LIKE ?)"; 
            $params[] = "%$keyword%"; 
            $params[] = "%$keyword%"; 
        }
        if (!empty($categoryId)) { 
            $sql .= " AND category_id = ?"; 
            $params[] = (int)$categoryId; 
        }
        if (!empty($brandId)) { 
            $sql .= " AND brand_id = ?"; 
            $params[] = (int)$brandId; 
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Lấy chi tiết 1 sản phẩm + Biến thể size + Thông tin hãng
     */
    public function getProductDetail($id) {
        $stmt = $this->conn->prepare("
            SELECT p.*, 
                   b.name as brand_name, 
                   c.name as cat_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.id 
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([(int)$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Lấy danh sách size và tồn kho còn hàng
            $stmtVars = $this->conn->prepare("
                SELECT id, product_id, size, stock 
                FROM product_variants 
                WHERE product_id = ? AND stock > 0 
                ORDER BY CAST(size AS DECIMAL(4,1)) ASC, size ASC
            ");
            $stmtVars->execute([(int)$id]);
            $product['variants'] = $stmtVars->fetchAll(PDO::FETCH_ASSOC);
        }
        return $product;
    }

    /**
     * Lấy 4 sản phẩm cùng danh mục gợi ý
     */
    public function getRelatedProducts($categoryId, $currentProductId) {
        $sql = "SELECT p.*, b.name as brand_name, c.name as cat_name 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.id 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ? AND p.id != ? 
                ORDER BY p.id DESC 
                LIMIT 4";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$categoryId, (int)$currentProductId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách ảnh phụ (Gallery)
     */
    public function getProductImages($productId) {
        $sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tăng lượt xem sản phẩm
     */
    public function incrementView($id) {
        $sql = "UPDATE products SET views = views + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([(int)$id]);
    }

    /**
     * Thêm ảnh phụ vào Gallery
     */
    public function addProductImage($productId, $imageUrl) {
        $sql = "INSERT INTO product_images (product_id, image_url) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([(int)$productId, $imageUrl]);
    }

    /**
     * Xóa ảnh phụ
     */
    public function deleteProductImageById($imageId) {
        $sql = "DELETE FROM product_images WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([(int)$imageId]);
    }

    /**
     * Lấy danh sách Flash Sale đang diễn ra
     */
    public function getFlashSaleProducts() {
        $sql = "SELECT p.*, c.name as cat_name, b.name as brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.sale_price > 0 
                  AND p.sale_start <= NOW() 
                  AND p.sale_end >= NOW()
                ORDER BY p.sale_end ASC 
                LIMIT 4";
        
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh mục sản phẩm kèm số lượng sản phẩm
     */
    public function getCategoriesWithCount() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id 
                ORDER BY c.id ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả thương hiệu
     */
    public function getAllBrands() {
        return $this->conn->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>