<?php
require_once 'models/AdminOrderModel.php';
require_once 'helpers/AuthHelper.php';

class AdminOrderController {
    private $model;
    public function __construct() {
        AuthHelper::requireAdmin();
        $this->model = new AdminOrderModel();
    }
    // HIỂN THỊ DANH SÁCH
    public function index() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : ''; 

        if (!empty($keyword)) {
            $orders = $this->model->searchOrders($keyword);
        } else {
            $orders = $this->model->getAllOrders();
        }

        require_once 'views/admin/orders/list.php';
    }

    //HIỂN THỊ CHI TIẾT
    public function detail() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $order = $this->model->getOrderById($id);
            if (!$order) {
                header("Location: index.php?controller=adminOrder");
                exit();
            }
            // Lấy danh sách sản phẩm trong đơn
            $details = $this->model->getOrderDetails($id);
            
            require_once 'views/admin/orders/detail.php';
        } else {
            header("Location: index.php?controller=adminOrder");
        }
    }

    // CẬP NHẬT TRẠNG THÁI
    public function update_status() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $orderId = $_POST['order_id'];
            $status = $_POST['status'];
            
            $this->model->updateStatus($orderId, $status);
            
            header("Location: index.php?controller=adminOrder&action=detail&id=" . $orderId);
            exit();
        }
    }
    public function export() {
        $orders = $this->model->getAllOrders();

        $filename = "Danh_sach_don_hang_" . date('Y-m-d_H-i') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM cho tiếng Việt

        // Tiêu đề cột
        fputcsv($output, ['Mã ĐH', 'Khách hàng', 'SĐT', 'Địa chỉ', 'Tổng tiền (VNĐ)', 'Trạng thái', 'Ngày đặt']);
        $statusMap = [
            'pending'   => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];

        foreach ($orders as $row) {
            // Lấy trạng thái tiếng Việt, mặc định là trạng thái gốc nếu không tìm thấy
            $statusVN = $statusMap[$row['status']] ?? $row['status'];

            fputcsv($output, [
                "#" . $row['id'],
                $row['fullname'],
                $row['phone'],
                $row['address'] ?? '',
                number_format($row['total_money'], 0, '', '.'),
                $statusVN,
                date('d/m/Y H:i', strtotime($row['created_at']))
            ]);
        }

        fclose($output);
        exit();
    }

    //IN HÓA ĐƠN
    public function invoice() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $order = $this->model->getOrderById($id);
            if (!$order) {
                header("Location: index.php?controller=adminOrder");
                exit();
            }
            $details = $this->model->getOrderDetails($id);
            require_once 'views/admin/orders/invoice.php';
        } else {
            header("Location: index.php?controller=adminOrder");
        }
    }
}
?>