<?php
require_once 'models/AdminUserModel.php';
require_once 'helpers/AuthHelper.php';
require_once 'helpers/CsrfHelper.php';

class AdminUserController {
    private $model;

    public function __construct() {
        AuthHelper::requireAdmin();
        $this->model = new AdminUserModel();
    }

    public function index() {
        $currentUserId = $_SESSION['user']['id'];
        
        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
             $keyword = trim($_GET['keyword']);
             $users = $this->model->searchUsers($keyword, $currentUserId);
        } else {
             $users = $this->model->getAllUsers($currentUserId);
        }
        
        require_once 'views/admin/users/index.php';
    }

    public function lock() {
        if (isset($_GET['id'])) {
            $this->model->updateStatus((int)$_GET['id'], 'locked');
        }
        header("Location: index.php?controller=adminUser&action=index");
        exit;
    }

    public function unlock() {
        if (isset($_GET['id'])) {
            $this->model->updateStatus((int)$_GET['id'], 'active');
        }
        header("Location: index.php?controller=adminUser&action=index");
        exit;
    }

    public function update_role() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = (int)($_POST['user_id'] ?? 0);
            $role = $_POST['role'] ?? 'customer';
            if (in_array($role, ['admin', 'customer'])) {
                $this->model->updateRole($id, $role);
            }
            header("Location: index.php?controller=adminUser&action=index");
            exit;
        }
    }
}
?>