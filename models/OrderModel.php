<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

class OrderModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Tạo đơn hàng với tính toàn vẹn Transaction, kiểm tra tồn kho nghiêm ngặt và tính toán giá từ DB
     */
    public function createOrder($userId, $customerData, $cartItems) {
        if (empty($cartItems)) {
            return ['success' => false, 'error' => 'Giỏ hàng trống!'];
        }

        try {
            $this->conn->beginTransaction();

            $totalCalculated = 0;
            $verifiedItems = [];

            // 1. Kiểm tra tồn kho và tính toán giá chính xác từ Database (Chống gian lận Client-side)
            foreach ($cartItems as $item) {
                $productId = (int)$item['id'];
                $size = (string)$item['size'];
                $qty = (int)$item['quantity'];

                if ($qty <= 0) {
                    throw new Exception("Số lượng sản phẩm không hợp lệ.");
                }

                // Khóa dòng để kiểm tra tồn kho chính xác (Pessimistic Locking / FOR UPDATE)
                $stmtCheck = $this->conn->prepare("SELECT stock FROM product_variants WHERE product_id = ? AND size = ? FOR UPDATE");
                $stmtCheck->execute([$productId, $size]);
                $variant = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$variant) {
                    throw new Exception("Biến thể size $size của sản phẩm mã #$productId không tồn tại!");
                }

                if ($variant['stock'] < $qty) {
                    throw new Exception("Size $size chỉ còn {$variant['stock']} đôi trong kho, không đủ số lượng bạn yêu cầu ($qty đôi).");
                }

                // Lấy giá chuẩn từ Database
                $stmtProd = $this->conn->prepare("SELECT name, price, old_price, sale_price, sale_start, sale_end FROM products WHERE id = ?");
                $stmtProd->execute([$productId]);
                $prod = $stmtProd->fetch(PDO::FETCH_ASSOC);

                if (!$prod) {
                    throw new Exception("Sản phẩm mã #$productId không tồn tại!");
                }

                // Tính giá bán thực tế (xét cả Flash Sale còn hiệu lực)
                $unitPrice = (float)$prod['price'];
                if (!empty($prod['sale_price']) && $prod['sale_price'] > 0 && !empty($prod['sale_start']) && !empty($prod['sale_end'])) {
                    $now = time();
                    $start = strtotime($prod['sale_start']);
                    $end = strtotime($prod['sale_end']);
                    if ($now >= $start && $now <= $end) {
                        $unitPrice = (float)$prod['sale_price'];
                    }
                }

                $subtotal = $unitPrice * $qty;
                $totalCalculated += $subtotal;

                $verifiedItems[] = [
                    'product_id' => $productId,
                    'name'       => $prod['name'],
                    'size'       => $size,
                    'quantity'   => $qty,
                    'price'      => $unitPrice,
                    'subtotal'   => $subtotal
                ];
            }

            // 2. Xác thực phương thức thanh toán
            $paymentMethod = $customerData['payment_method'] ?? 'COD';
            if (!in_array($paymentMethod, ['COD', 'BANKING', 'VNPAY', 'MOMO'])) {
                $paymentMethod = 'COD';
            }

            // 3. Thêm vào bảng `orders` (Tương thích 100% với schema hiện tại)
            $sqlOrder = "INSERT INTO orders (user_id, fullname, phone, address, note, total_money, payment_method, status, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            
            $stmtOrder = $this->conn->prepare($sqlOrder);
            $stmtOrder->execute([
                (int)$userId,
                SecurityHelper::cleanInput($customerData['fullname']),
                SecurityHelper::cleanInput($customerData['phone']),
                SecurityHelper::cleanInput($customerData['address']),
                SecurityHelper::cleanInput($customerData['note'] ?? ''),
                $totalCalculated,
                $paymentMethod
            ]);
            
            $orderId = (int)$this->conn->lastInsertId();

            // 4. Lưu từng sản phẩm vào `order_details` và trừ kho nguyên tử
            $sqlDetail = "INSERT INTO order_details (order_id, product_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)";
            $stmtDetail = $this->conn->prepare($sqlDetail);

            $sqlUpdateStock = "UPDATE product_variants SET stock = stock - ? WHERE product_id = ? AND size = ? AND stock >= ?";
            $stmtStock = $this->conn->prepare($sqlUpdateStock);

            foreach ($verifiedItems as $item) {
                $stmtDetail->execute([
                    $orderId,
                    $item['product_id'],
                    $item['size'],
                    $item['quantity'],
                    $item['price']
                ]);

                // Trừ tồn kho và đảm bảo không bao giờ âm
                $stmtStock->execute([$item['quantity'], $item['product_id'], $item['size'], $item['quantity']]);
                if ($stmtStock->rowCount() === 0) {
                    throw new Exception("Hàng trong kho vừa bị thay đổi bởi người mua khác. Vui lòng thử lại!");
                }
            }

            $this->conn->commit();
            return [
                'success'    => true,
                'order_id'   => $orderId,
                'order_code' => '#' . $orderId,
                'total'      => $totalCalculated
            ];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    public function getOrdersByUserId($userId) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getOrderDetail($orderId) {
        $sql = "SELECT od.*, p.name, p.image, p.price as original_price 
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($orderId) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([(int)$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Hủy đơn hàng và tự động hoàn trả tồn kho sản phẩm (Atomic Restock)
     */
    public function cancelOrder($orderId, $userId = null) {
        try {
            $this->conn->beginTransaction();

            $sql = "SELECT status FROM orders WHERE id = ?" . ($userId ? " AND user_id = ?" : "");
            $stmt = $this->conn->prepare($sql);
            $params = $userId ? [(int)$orderId, (int)$userId] : [(int)$orderId];
            $stmt->execute($params);
            $currentStatus = $stmt->fetchColumn();

            if (!$currentStatus) {
                throw new Exception("Không tìm thấy đơn hàng!");
            }

            if ($currentStatus !== 'pending') {
                throw new Exception("Chỉ có thể hủy đơn hàng khi đang ở trạng thái 'Chờ xử lý'.");
            }

            // Lấy danh sách sản phẩm để hoàn kho
            $items = $this->getOrderDetail($orderId);
            $sqlRestock = "UPDATE product_variants SET stock = stock + ? WHERE product_id = ? AND size = ?";
            $stmtRestock = $this->conn->prepare($sqlRestock);

            foreach ($items as $item) {
                $stmtRestock->execute([
                    (int)$item['quantity'],
                    (int)$item['product_id'],
                    $item['size']
                ]);
            }

            // Cập nhật trạng thái sang cancelled
            $sqlCancel = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
            $stmtCancel = $this->conn->prepare($sqlCancel);
            $stmtCancel->execute([(int)$orderId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function updateShippingInfo($orderId, $userId, $phone, $address) {
        $sql = "UPDATE orders SET phone = ?, address = ? 
                WHERE id = ? AND user_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            SecurityHelper::cleanInput($phone), 
            SecurityHelper::cleanInput($address), 
            (int)$orderId, 
            (int)$userId
        ]);
    }
}
?>