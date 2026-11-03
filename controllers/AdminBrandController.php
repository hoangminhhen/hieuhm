<?php
require_once 'models/AdminBrandModel.php';
require_once 'helpers/AuthHelper.php';
require_once 'helpers/CsrfHelper.php';

class AdminBrandController {
    private $model;

    public function __construct() {
        AuthHelper::requireAdmin();
        $this->model = new AdminBrandModel();
    }

    public function index() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if (!empty($keyword)) {
            $brands = $this->model->searchBrands($keyword);
        } else {
            $brands = $this->model->getAll();
        }

        if (!is_array($brands)) {
            $brands = [];
        }

        // Lấy thương hiệu cần sửa nếu có edit_id trong URL
        $editBrand = null;
        if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
            $editBrand = $this->model->getById((int)$_GET['edit_id']);
        }

        require_once 'views/admin/brands/index.php';
    }

    public function edit() {
        $this->index();
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? null)) {
                die("Lỗi CSRF Token không hợp lệ!");
            }
            $name = trim($_POST['name'] ?? '');
            if (!empty($name)) {
                $this->model->insert($name);
            }
            header("Location: index.php?controller=adminBrand&action=index");
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->delete((int)$_GET['id']);
        }
        header("Location: index.php?controller=adminBrand&action=index");
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!CsrfHelper::verifyToken($_POST['csrf_token'] ?? null)) {
                die("Lỗi CSRF Token không hợp lệ!");
            }
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if ($id > 0 && !empty($name)) {
                $this->model->update($id, $name);
            }
            header("Location: index.php?controller=adminBrand&action=index");
            exit;
        }
    }

    public function showProducts() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $brand = $this->model->getById($id);
            $products = $this->model->getProductsByBrandId($id);
            require_once 'views/admin/brands/products.php';
        } else {
            header("Location: index.php?controller=adminBrand&action=index");
            exit;
        }
    }

    public function export() {
        $brands = $this->model->getAll(); 
        $filename = "Danh_sach_thuong_hieu_" . date('Y-m-d_H-i') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM cho Excel tiếng Việt
        fputcsv($output, ['ID', 'Tên thương hiệu']);

        foreach ($brands as $row) {
            fputcsv($output, [
                $row['id'],
                $row['name']
            ]);
        }
        fclose($output);
        exit();
    }
}
?>