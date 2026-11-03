<?php
require_once 'models/UserModel.php';
require_once 'models/OrderModel.php';
require_once 'models/CartModel.php';

class UserController
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function register()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = trim($_POST['fullname']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);

            if (empty($fullname) || empty($email) || empty($password)) {
                $error = "Vui lòng nhập đầy đủ thông tin!";
            } elseif ($password != $confirm_password) {
                $error = "Mật khẩu xác nhận không khớp!";
            } elseif ($this->model->checkEmail($email)) {
                $error = "Email này đã được đăng ký!";
            } else {
                if ($this->model->register($fullname, $email, $password, $phone, $address)) {
                    header("Location: index.php?controller=user&action=login&status=success");
                    exit;
                } else {
                    $error = "Có lỗi xảy ra, vui lòng thử lại!";
                }
            }
        }
        require_once 'views/auth/register.php';
    }

    public function login()
    {
        $error = '';
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $success = "Đăng ký thành công! Hãy đăng nhập.";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = $this->model->login($email, $password);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role']
                ];

                $cartModel = new CartModel();
                $savedCart = $cartModel->getCartByUserId($user['id']);

                if (!empty($savedCart)) {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    foreach ($savedCart as $item) {
                        $key = $item['id'] . '_' . $item['size'];
                        $_SESSION['cart'][$key] = [
                            'id' => $item['id'],
                            'name' => $item['name'],
                            'price' => $item['price'],
                            'image' => $item['image'],
                            'quantity' => $item['quantity'],
                            'size' => $item['size']
                        ];
                    }
                }

                if ($user['role'] == 'admin') {
                    header("Location: index.php?controller=admin&action=dashboard");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
            }
        }
        require_once 'views/auth/login.php';
    }

    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public function profile()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $user = $this->model->getUserById($userId);
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = $_POST['fullname'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];

            if ($this->model->updateProfile($userId, $fullname, $phone, $address)) {
                $message = "Cập nhật thông tin thành công!";
                $_SESSION['user']['fullname'] = $fullname;
                $user = $this->model->getUserById($userId);
            } else {
                $message = "Có lỗi xảy ra!";
            }
        }
        require_once 'views/auth/profile.php';
    }

    public function orders()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrdersByUserId($userId);

        require_once 'views/users/orders.php';
    }
    public function cancelOrder()
    {
        if (!isset($_GET['id']) || !isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        $orderId = $_GET['id'];
        $userId = $_SESSION['user']['id'];
        $orderModel = new OrderModel();

        if ($orderModel->cancelOrder($orderId, $userId)) {
            echo "<script>alert('Đã hủy đơn hàng thành công!'); window.location.href='index.php?controller=user&action=orders';</script>";
        } else {
            echo "<script>alert('Không thể hủy đơn hàng này!'); window.location.href='index.php?controller=user&action=orders';</script>";
        }
    }

    public function editOrder()
    {
        if (!isset($_GET['id']) || !isset($_SESSION['user'])) {
            header("Location: index.php");
            exit;
        }

        $orderId = $_GET['id'];
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderById($orderId);

        if (!$order || $order['user_id'] != $_SESSION['user']['id'] || $order['status'] != 'pending') {
            echo "<script>alert('Không thể sửa đơn hàng này!'); window.history.back();</script>";
            exit;
        }

        require_once 'views/users/order_edit.php';
    }

    public function updateOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $userId = $_SESSION['user']['id'] ?? null;

            if (!$orderId || !$userId) {
                echo "<script>alert('Dữ liệu đơn hàng không hợp lệ!'); window.location.href='index.php?controller=user&action=orders';</script>";
                return;
            }

            $orderModel = new OrderModel();
            $result = false;

            if (method_exists($orderModel, 'updateOrder')) {
                $result = call_user_func_array([$orderModel, 'updateOrder'], [$orderId, $userId, $phone, $address]);
            }

            if ($result) {
                echo "<script>alert('Cập nhật thành công!'); window.location.href='index.php?controller=user&action=orders';</script>";
            } else {
                echo "<script>alert('Cập nhật thất bại hoặc chức năng cập nhật đơn hàng chưa được hỗ trợ!'); window.location.href='index.php?controller=user&action=orders';</script>";
            }
        }
    }


    public function forgotPassword()
    {
        require_once 'views/auth/forgot.php';
    }

    public function sendResetLink()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);

            $user = $this->model->checkEmail($email);

            if ($user) {

                $token = bin2hex(random_bytes(16));

                $this->model->saveResetToken($email, $token);

                $link = "http://localhost/shop_ban_hang/index.php?controller=user&action=resetPassword&email=$email&token=$token";

                echo "<div style='padding: 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin: 20px; border-radius: 5px;'>";
                echo "<strong>[TEST MODE] Link khôi phục mật khẩu:</strong><br>";
                echo "<a href='$link'>$link</a>";
                echo "</div>";
            } else {
                echo "<script>alert('Email này chưa được đăng ký!'); history.back();</script>";
            }
        }
    }

    public function resetPassword()
    {
        if (isset($_GET['email']) && isset($_GET['token'])) {
            $email = $_GET['email'];
            $token = $_GET['token'];

            // Kiểm tra token có hợp lệ không
            if ($this->model->verifyToken($email, $token)) {
                require_once 'views/auth/reset.php';
            } else {
                echo "<div class='alert alert-danger text-center mt-5'>Link khôi phục không hợp lệ hoặc đã hết hạn!</div>";
                echo "<div class='text-center'><a href='index.php?controller=user&action=forgotPassword'>Thử lại</a></div>";
            }
        } else {
            header("Location: index.php");
        }
    }

    // 4. Xử lý lưu mật khẩu mới
    public function updateNewPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $pass = $_POST['password'];
            $confirm = $_POST['confirm_password'];

            if (strlen($pass) < 6) {
                echo "<script>alert('Mật khẩu phải có ít nhất 6 ký tự!'); history.back();</script>";
                return;
            }

            if ($pass === $confirm) {
                // Cập nhật mật khẩu mới
                $this->model->updatePasswordByEmail($email, $pass);
                echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='index.php?controller=user&action=login';</script>";
            } else {
                echo "<script>alert('Mật khẩu xác nhận không khớp!'); history.back();</script>";
            }
        }
    }
}
