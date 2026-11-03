<?php
require_once 'models/CartModel.php';
require_once 'models/ProductModel.php';

class ApiCartController extends BaseApiController
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new CartModel();
    }

    // GET: /api/cart/get?user_id=1
    public function get()
    {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            $user = $this->authenticateUser();
            if ($user) $userId = $user['id'];
        }

        if ($userId) {
            $cart = $this->model->getCartByUserId($userId);
            $this->respond(200, "Thành công", $cart);
        }
        $this->respond(400, "Vui lòng cung cấp user_id");
    }

    // POST: /api/cart/sync
    public function sync()
    {
        $data = $this->getJsonPayload();
        $userId = $data['user_id'] ?? null;
        if (!$userId) {
            $user = $this->authenticateUser();
            if ($user) $userId = $user['id'];
        }

        $cartItems = $data['cart_items'] ?? [];

        if ($userId) {
            $this->model->syncCart($userId, $cartItems);
            $this->respond(200, "Đồng bộ giỏ hàng thành công");
        }
        $this->respond(400, "Thiếu user_id để đồng bộ");
    }

    // POST: /api/cart/add
    public function add()
    {
        $data = $this->getJsonPayload();
        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
        $size = isset($data['size']) ? trim($data['size']) : '';
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

        if ($productId <= 0 || empty($size)) {
            $this->respond(400, "Vui lòng cung cấp product_id và size");
        }

        $prodModel = new ProductModel();
        $product = $prodModel->getProductDetail($productId);

        if (!$product) {
            $this->respond(404, "Sản phẩm không tồn tại");
        }

        // Kiểm tra tồn kho size
        $stock = 0;
        $found = false;
        if (!empty($product['variants'])) {
            foreach ($product['variants'] as $v) {
                if ($v['size'] == $size) {
                    $found = true;
                    $stock = (int)$v['stock'];
                    break;
                }
            }
        }

        if (!$found || $stock < $quantity) {
            $this->respond(400, "Size $size hiện chỉ còn $stock đôi trong kho!");
        }

        if (session_status() === PHP_SESSION_NONE) session_start();
        $key = $productId . '_' . $size;
        $realPrice = $product['price'];
        if (!empty($product['sale_price']) && $product['sale_price'] > 0 && !empty($product['sale_start']) && !empty($product['sale_end'])) {
            $now = time();
            if ($now >= strtotime($product['sale_start']) && $now <= strtotime($product['sale_end'])) {
                $realPrice = $product['sale_price'];
            }
        }

        $itemData = [
            'id'       => $product['id'],
            'name'     => $product['name'],
            'image'    => $product['image'],
            'price'    => $realPrice,
            'size'     => $size,
            'quantity' => $quantity
        ];

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$key] = $itemData;
        }

        $userId = $this->currentUser['id'] ?? $_SESSION['user']['id'] ?? null;
        if ($userId) {
            $this->model->syncCart($userId, $_SESSION['cart']);
        }

        $totalCount = 0;
        foreach ($_SESSION['cart'] as $it) $totalCount += $it['quantity'];

        $this->respond(200, "Đã thêm vào giỏ hàng thành công", [
            'cart_count' => $totalCount,
            'item'       => $itemData
        ]);
    }
}
?>