<?php
require_once 'models/ProductModel.php';
require_once 'models/OrderModel.php';
require_once 'models/CartModel.php';
require_once 'helpers/SecurityHelper.php';
require_once 'helpers/CsrfHelper.php';
require_once 'helpers/AuthHelper.php';

class CartController {

    public function index() {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $totalPrice = 0;
        foreach ($cart as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }
        require_once 'views/cart/index.php';
    }

    public function add() {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $size = isset($_POST['size']) ? trim($_POST['size']) : '';
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity < 1) $quantity = 1;
            $actionType = isset($_POST['action_type']) ? $_POST['action_type'] : 'add';

            if ($productId <= 0 || empty($size)) {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['status' => 400, 'message' => 'Vui lòng chọn size giày!']);
                    exit;
                }
                $_SESSION['error'] = 'Vui lòng chọn size giày!';
                header("Location: index.php");
                exit;
            }

            $productModel = new ProductModel();
            $product = $productModel->getProductDetail($productId);

            if ($product) {
                // Kiểm tra xem size có trong variants không và còn hàng không
                $variantStock = 0;
                $foundSize = false;
                if (!empty($product['variants'])) {
                    foreach ($product['variants'] as $v) {
                        if ($v['size'] == $size) {
                            $foundSize = true;
                            $variantStock = (int)$v['stock'];
                            break;
                        }
                    }
                }

                if (!$foundSize || $variantStock < $quantity) {
                    $msg = "Size $size hiện chỉ còn $variantStock đôi trong kho!";
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['status' => 400, 'message' => $msg]);
                        exit;
                    }
                    $_SESSION['error'] = $msg;
                    header("Location: index.php?controller=product&action=detail&id=" . $productId);
                    exit;
                }

                // Tính giá thực tế (xét cả flash sale)
                $realPrice = $product['price'];
                if (!empty($product['sale_price']) && $product['sale_price'] > 0 && !empty($product['sale_start']) && !empty($product['sale_end'])) {
                    $now = time();
                    if ($now >= strtotime($product['sale_start']) && $now <= strtotime($product['sale_end'])) {
                        $realPrice = $product['sale_price'];
                    }
                }

                $key = $productId . '_' . $size;
                $itemData = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'image'    => $product['image'],
                    'price'    => $realPrice,
                    'size'     => $size,
                    'quantity' => $quantity
                ];

                if ($actionType == 'buy_now') {
                    $_SESSION['direct_buy'] = [
                        $key => $itemData
                    ];
                    header("Location: index.php?controller=cart&action=checkout&type=direct");
                    exit;
                } else {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    if (isset($_SESSION['cart'][$key])) {
                        $_SESSION['cart'][$key]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$key] = $itemData;
                    }

                    // Đồng bộ vào DB nếu đã đăng nhập
                    if (isset($_SESSION['user']['id'])) {
                        $cartModel = new CartModel();
                        $cartModel->syncCart($_SESSION['user']['id'], $_SESSION['cart']);
                    }

                    $totalItems = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        $totalItems += $item['quantity'];
                    }

                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode([
                            'status'      => 200,
                            'message'     => 'Đã thêm ' . $product['name'] . ' (Size ' . $size . ') vào giỏ hàng!',
                            'cart_count'  => $totalItems,
                            'item'        => $itemData
                        ]);
                        exit;
                    }

                    $_SESSION['success'] = "Đã thêm sản phẩm vào giỏ hàng thành công!";
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        header("Location: " . $_SERVER['HTTP_REFERER']);
                    } else {
                        header("Location: index.php?controller=product&action=detail&id=" . $productId);
                    }
                    exit;
                }
            }
        }
        header("Location: index.php");
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $key = $_GET['id'];
            unset($_SESSION['cart'][$key]);

            if (isset($_SESSION['user']['id'])) {
                $cartModel = new CartModel();
                $cartModel->syncCart($_SESSION['user']['id'], $_SESSION['cart']);
            }
        }
        header("Location: index.php?controller=cart&action=index");
    }

    public function update() {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (isset($_POST['key']) && isset($_POST['quantity'])) {
            $key = $_POST['key'];
            $qty = (int)$_POST['quantity'];
            
            if ($qty > 0 && isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]['quantity'] = $qty;

                if (isset($_SESSION['user']['id'])) {
                    $cartModel = new CartModel();
                    $cartModel->syncCart($_SESSION['user']['id'], $_SESSION['cart']);
                }

                if ($isAjax) {
                    $totalItems = 0;
                    $totalMoney = 0;
                    foreach ($_SESSION['cart'] as $item) {
                        $totalItems += $item['quantity'];
                        $totalMoney += $item['price'] * $item['quantity'];
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'status'      => 200,
                        'message'     => 'Cập nhật số lượng thành công',
                        'cart_count'  => $totalItems,
                        'item_total'  => $_SESSION['cart'][$key]['price'] * $qty,
                        'cart_total'  => $totalMoney
                    ]);
                    exit;
                }
            }
        }
        header("Location: index.php?controller=cart&action=index");
    }

    public function checkout() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để tiến hành thanh toán.";
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_items'])) {
            $selectedKeys = $_POST['selected_items'];
            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            $checkoutItems = [];

            foreach ($selectedKeys as $key) {
                if (isset($cart[$key])) {
                    $checkoutItems[$key] = $cart[$key];
                }
            }

            if (!empty($checkoutItems)) {
                $_SESSION['direct_buy'] = $checkoutItems;
                header("Location: index.php?controller=cart&action=checkout&type=direct");
                exit;
            } else {
                header("Location: index.php?controller=cart&action=index");
                exit;
            }
        }

        $orderType = isset($_GET['type']) ? $_GET['type'] : 'cart'; 
        
        if ($orderType == 'direct') {
            $cart = isset($_SESSION['direct_buy']) ? $_SESSION['direct_buy'] : [];
        } else {
            $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        }

        if (empty($cart)) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }

        $totalPrice = 0;
        foreach ($cart as $item) $totalPrice += $item['price'] * $item['quantity'];
        
        $user = $_SESSION['user']; 

        require_once 'views/cart/checkout.php';
    }

    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
            // Xác thực CSRF token
            if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = "Lỗi bảo mật (CSRF). Vui lòng thử lại!";
                header("Location: index.php?controller=cart&action=checkout");
                exit;
            }

            $orderType = isset($_POST['order_type']) ? $_POST['order_type'] : 'cart';
            
            if ($orderType == 'direct') {
                $cartToOrder = isset($_SESSION['direct_buy']) ? $_SESSION['direct_buy'] : [];
            } else {
                $cartToOrder = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            }

            if (empty($cartToOrder)) {
                $_SESSION['error'] = "Không có sản phẩm nào để thanh toán.";
                header("Location: index.php?controller=cart&action=index");
                exit;
            }

            $customerData = [
                'fullname'       => $_POST['fullname'] ?? '',
                'phone'          => $_POST['phone'] ?? '',
                'address'        => $_POST['address'] ?? '',
                'note'           => $_POST['note'] ?? '',
                'payment_method' => $_POST['payment_method'] ?? 'COD'
            ];
            
            $orderModel = new OrderModel();
            $result = $orderModel->createOrder($_SESSION['user']['id'], $customerData, $cartToOrder);

            if ($result && !empty($result['success'])) {
                $_SESSION['latest_order'] = [
                    'id'             => $result['order_id'],
                    'order_code'     => $result['order_code'],
                    'total_money'    => $result['total'],
                    'payment_method' => $customerData['payment_method']
                ];

                if ($orderType == 'direct') {
                    unset($_SESSION['direct_buy']);
                } else {
                    unset($_SESSION['cart']);
                    $cartModel = new CartModel();
                    $cartModel->syncCart($_SESSION['user']['id'], []);
                }
                
                header("Location: index.php?controller=cart&action=success");
                exit; 
            } else {
                $_SESSION['error'] = "Đặt hàng không thành công: " . ($result['error'] ?? 'Lỗi không xác định.');
                header("Location: index.php?controller=cart&action=checkout" . ($orderType == 'direct' ? '&type=direct' : ''));
                exit;
            }
        }
        header("Location: index.php");
    }
    
    public function success() {
        if (isset($_SESSION['latest_order'])) {
            $order = $_SESSION['latest_order'];
            require_once 'views/cart/success.php';
        } else {
            header("Location: index.php");
        }
    }
}
?>