<?php
require_once __DIR__ . '/../config/db.php';

class UserModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Kiểm tra email đã tồn tại chưa
    public function checkEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, email, fullname, status FROM users WHERE email = ?");
        $stmt->execute([trim($email)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Đăng ký tài khoản mới (Khách hàng)
    public function register($fullname, $email, $password, $phone, $address) {
        // Validate dữ liệu cơ bản
        $email = trim($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        if (strlen($password) < 6) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (fullname, email, password, phone, address, role, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'customer', 'active', NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([trim($fullname), $email, $hashed_password, trim($phone), trim($address)]);
    }

    // 3. Đăng nhập với kiểm tra mật khẩu và trạng thái hoạt động
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([trim($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Kiểm tra trạng thái tài khoản
            if (isset($user['status']) && $user['status'] === 'locked') {
                return ['error' => 'ACCOUNT_LOCKED', 'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.'];
            }
            return $user;
        }
        return false;
    }

    // 4. Lấy thông tin chi tiết user theo ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, fullname, email, phone, address, role, status, created_at FROM users WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 5. Cập nhật thông tin cá nhân
    public function updateProfile($id, $fullname, $phone, $address) {
        $sql = "UPDATE users SET fullname = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([trim($fullname), trim($phone), trim($address), (int)$id]);
    }

    // 6. Đổi mật khẩu có xác thực mật khẩu cũ
    public function changePassword($id, $oldPassword, $newPassword) {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([(int)$id]);
        $currentHashed = $stmt->fetchColumn();

        if ($currentHashed && password_verify($oldPassword, $currentHashed)) {
            $newHashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $update = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            return $update->execute([$newHashed, (int)$id]);
        }
        return false;
    }

    // 7. Tạo hoặc cấp API Bearer Token
    public function generateApiToken($userId, $deviceInfo = 'Web Client') {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $sql = "INSERT INTO user_tokens (user_id, token, device_info, expires_at, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([(int)$userId, $token, $deviceInfo, $expiresAt]);
            return ['token' => $token, 'expires_at' => $expiresAt];
        } catch (Exception $e) {
            // Trường hợp bảng user_tokens chưa được tạo, fallback token đơn giản
            return ['token' => base64_encode($userId . ':' . $token), 'expires_at' => $expiresAt];
        }
    }

    // 8. Xác thực người dùng bằng API Token
    public function getUserByToken($token) {
        try {
            $sql = "SELECT u.id, u.fullname, u.email, u.phone, u.role, u.status 
                    FROM users u 
                    JOIN user_tokens t ON u.id = t.user_id 
                    WHERE t.token = ? AND t.expires_at > NOW() AND u.status = 'active'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    // 9. Quản lý Quên mật khẩu
    public function saveResetToken($email, $token) {
        $sqlDelete = "DELETE FROM password_resets WHERE email = ?";
        $this->conn->prepare($sqlDelete)->execute([$email]);

        $sql = "INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$email, $token]);
    }

    public function verifyToken($email, $token) {
        $sql = "SELECT * FROM password_resets WHERE email = ? AND token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email, $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $createdAt = strtotime($result['created_at']);
            if (time() - $createdAt < 3600) { 
                return true;
            }
        }
        return false;
    }

    public function updatePasswordByEmail($email, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?";
        $this->conn->prepare($sql)->execute([$hashed, $email]);

        $this->conn->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
        return true;
    }
}
?>