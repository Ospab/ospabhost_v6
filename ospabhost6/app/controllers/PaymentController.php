<?php
class PaymentController {
    private $db;

    public function __construct() {
        require_once '../app/lib/Database.php';
        require_once '../app/lib/Auth.php';
        $this->db = new Database();
    }

    public function index() {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        
        if ($orderId === 0) {
            header('Location: /dashboard');
            exit;
        }

        // Проверяем, что заказ принадлежит текущему пользователю
        $order = $this->db->query(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, Auth::getUserId()]
        )->fetch();

        if (!$order) {
            header('Location: /dashboard');
            exit;
        }

        $data = [
            'title' => 'Оплата заказа - OSPAB Host',
            'order' => $order
        ];

        $this->render('payment/index', $data);
    }

    public function uploadCheque() {
        if (!Auth::isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/login');
            exit;
        }

        $orderId = (int)$_POST['order_id'];
        $userId = Auth::getUserId();

        // Проверяем права на заказ
        $order = $this->db->query(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $userId]
        )->fetch();

        if (!$order) {
            $_SESSION['error'] = 'Заказ не найден';
            header('Location: /dashboard');
            exit;
        }

        // Обработка загрузки файла
        if (isset($_FILES['cheque']) && $_FILES['cheque']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['cheque']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = '../public/uploads/cheques/';
                $fileName = uniqid() . '_' . basename($_FILES['cheque']['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['cheque']['tmp_name'], $targetPath)) {
                    // Обновляем заказ
                    $this->db->query(
                        "UPDATE orders SET status = 'awaiting_verification', screenshot_path = ? WHERE id = ?",
                        [$fileName, $orderId]
                    );

                    $_SESSION['success'] = 'Чек успешно загружен. Ожидайте проверки.';
                    header('Location: /dashboard');
                    exit;
                }
            }
        }

        $_SESSION['error'] = 'Ошибка при загрузке файла';
        header("Location: /payment?order_id=$orderId");
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