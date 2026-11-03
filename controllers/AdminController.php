<?php
require_once 'config/db.php';
require_once 'models/AdminDashboardModel.php'; 
require_once 'models/AdminStockModel.php';
require_once 'helpers/AuthHelper.php';

class AdminController {
    private $model;

    public function __construct() {
        AuthHelper::requireAdmin();
        $this->model = new AdminDashboardModel();
    }

    public function dashboard() {
        $revenue       = $this->model->getTotalRevenue();
        $newOrders     = $this->model->getPendingOrdersCount();
        $totalProducts = $this->model->getTotalProductsCount();
        $chartData     = $this->model->getRevenueChartData();
        $topProducts   = $this->model->getTopSellingProducts();

        // Lấy cảnh báo tồn kho thấp (size <= 3 đôi)
        $stockModel    = new AdminStockModel();
        $lowStockItems = $stockModel->getLowStockItems(3);

        require_once 'views/admin/dashboard.php';
    }
}
?>