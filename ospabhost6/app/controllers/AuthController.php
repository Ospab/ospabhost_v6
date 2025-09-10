<?php
class AuthController {
    private $db;

    public function __construct() {
        require_once '../app/lib/Database.php';
        require_once '../app/lib/Auth.php';
        $this->db = new Database();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            // Ищем пользователя
            $user = $this->db->query(
                "SELECT * FROM users WHERE email = ?", 
                [$email]
            )->fetch();

            if ($user && Auth::verifyPassword($password, $user['password'])) {
                Auth::login($user['id'], $user['email']);
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Неверный email или пароль";
            }
        }

        $this->render('auth/login', ['error' => $error ?? null]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(trim($_POST['name']));
            $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Валидация
            if ($password !== $confirm_password) {
                $error = "Пароли не совпадают";
            } elseif (strlen($password) < 6) {
                $error = "Пароль должен быть не менее 6 символов";
            } else {
                // Проверяем, нет ли уже такого email
                $existing = $this->db->query(
                    "SELECT id FROM users WHERE email = ?", 
                    [$email]
                )->fetch();

                if ($existing) {
                    $error = "Пользователь с таким email уже существует";
                } else {
                    // Регистрируем
                    $hashedPassword = Auth::hashPassword($password);
                    $this->db->query(
                        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
                        [$name, $email, $hashedPassword]
                    );

                    // Автоматический вход
                    $userId = $this->db->getLastInsertId();
                    Auth::login($userId, $email);
                    header('Location: /dashboard');
                    exit;
                }
            }
        }

        $this->render('auth/register', ['error' => $error ?? null]);
    }

    public function logout() {
        Auth::logout();
        header('Location: /');
        exit;
    }

    private function render($view, $data = []) {
        extract($data);
        require_once "../app/views/layouts/header.php";
        require_once "../app/views/$view.php";
        require_once "../app/views/layouts/footer.php";
    }
}
?>