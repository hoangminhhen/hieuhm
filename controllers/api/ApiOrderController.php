<?php
require_once 'models/OrderModel.php';

class ApiOrderController extends BaseApiController
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new OrderModel();
    }

    // POST: /api/order/create
    public function create()
    {   
        $data = $this->getJsonPayload();
        
        $userId = $data['user_id'] ?? null;
        if (!$userId) {
            $user = $this->authenticateUser();
            if ($user) $userId = $user['id'];
        }

        if ($userId && isset($data['customer_data'], $data['cart_items'])) {
            $result = $this->model->createOrder($userId, $data['customer_data'], $data['cart_items']);
            if ($result && !empty($result['success'])) {
                $this->respond(201, "Đặt hàng thành công", $result);
            }
            $this->respond(400, $result['error'] ?? "Lỗi khi tạo đơn hàng");
        }
        $this->respond(400, "Thiếu thông tin người dùng, khách hàng hoặc sản phẩm giỏ hàng");
    }

    // GET: /api/order/history?user_id=1
    public function history()
    {
        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            $user = $this->authenticateUser();
            if ($user) $userId = $user['id'];
        }

        if ($userId) {
            $orders = $this->model->getOrdersByUserId($userId);
            $this->respond(200, "Thành công", $orders);
        }
        $this->respond(400, "Vui lòng cung cấp user_id");
    }

    // GET: /api/order/detail?id=1
    public function detail()
    {
        $orderId = $_GET['id'] ?? null;
        if ($orderId) {
            $order = $this->model->getOrderById($orderId);
            if ($order) {
                $order['items'] = $this->model->getOrderDetail($orderId);
                $this->respond(200, "Thành công", $order);
            }
            $this->respond(404, "Không tìm thấy đơn hàng");
        }
        $this->respond(400, "Thiếu mã đơn hàng id");
    }

    // POST: /api/order/cancel
    public function cancel()
    {
        $data = $this->getJsonPayload();
        $orderId = $data['order_id'] ?? null;
        $userId = $data['user_id'] ?? null;

        if ($orderId) {
            $success = $this->model->cancelOrder($orderId, $userId);
            if ($success) {
                $this->respond(200, "Hủy đơn hàng thành công và đã hoàn trả tồn kho");
            }
            $this->respond(400, "Không thể hủy đơn hàng này (chỉ được hủy đơn ở trạng thái chờ xử lý)");
        }
        $this->respond(400, "Thiếu order_id");
    }
}
?>