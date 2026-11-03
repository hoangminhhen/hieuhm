<?php
require_once 'models/UserModel.php';

class ApiUserController extends BaseApiController
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
    }

    // POST: /api/user/login
    public function login()
    {
        $data = $this->getJsonPayload();
        if (isset($data['email'], $data['password'])) {
            $user = $this->model->login($data['email'], $data['password']);
            
            if (is_array($user) && isset($user['error']) && $user['error'] === 'ACCOUNT_LOCKED') {
                $this->respond(403, $user['message']);
            }

            if ($user && is_array($user)) {
                unset($user['password']);
                // Cấp API Token
                $tokenData = $this->model->generateApiToken($user['id']);
                $user['access_token'] = $tokenData['token'];
                $user['token_expires'] = $tokenData['expires_at'];

                $this->respond(200, "Đăng nhập thành công", $user);
            }
            $this->respond(401, "Email hoặc mật khẩu không chính xác");
        }
        $this->respond(400, "Vui lòng cung cấp đầy đủ email và password");
    }

    // POST: /api/user/register
    public function register()
    {
        $data = $this->getJsonPayload();
        if (!empty($data['fullname']) && !empty($data['email']) && !empty($data['password'])) {
            if ($this->model->checkEmail($data['email'])) {
                $this->respond(409, "Địa chỉ email này đã được sử dụng");
            }
            if (strlen($data['password']) < 6) {
                $this->respond(422, "Mật khẩu phải có độ dài tối thiểu 6 ký tự");
            }

            $phone = $data['phone'] ?? '';
            $address = $data['address'] ?? '';
            $success = $this->model->register($data['fullname'], $data['email'], $data['password'], $phone, $address);
            if ($success) {
                $this->respond(201, "Đăng ký tài khoản thành công!");
            }
            $this->respond(500, "Đăng ký thất bại do lỗi hệ thống");
        }
        $this->respond(400, "Thiếu các trường bắt buộc (fullname, email, password)");
    }

    // GET: /api/user/profile?id=1
    public function profile()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $user = $this->authenticateUser();
            if ($user) $id = $user['id'];
        }

        if ($id) {
            $user = $this->model->getUserById($id);
            if ($user) {
                unset($user['password']);
                $this->respond(200, "Thành công", $user);
            }
            $this->respond(404, "Không tìm thấy người dùng");
        }
        $this->respond(400, "Thiếu user id");
    }

    // POST: /api/user/update
    public function update()
    {
        $data = $this->getJsonPayload();
        $id = $data['id'] ?? null;
        if (!$id) {
            $user = $this->authenticateUser();
            if ($user) $id = $user['id'];
        }

        if ($id && isset($data['fullname'], $data['phone'], $data['address'])) {
            $success = $this->model->updateProfile($id, $data['fullname'], $data['phone'], $data['address']);
            if ($success) {
                $this->respond(200, "Cập nhật hồ sơ thành công");
            }
            $this->respond(500, "Cập nhật thất bại");
        }
        $this->respond(400, "Thiếu thông tin cần cập nhật (fullname, phone, address)");
    }
}
?>