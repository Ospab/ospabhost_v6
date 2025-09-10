<?php
class AdminController {
    private $db;

    public function __construct() {
        require_once '../app/lib/Database.php';
        require_once '../app/lib/Auth.php';
        $this->db = new Database();
        
        // Проверка прав администратора
        if (!Auth::isLoggedIn() || !$this->isOperator()) {
            header('Location: /auth/login');
            exit;
        }
    }

    private function isOperator() {
        // Здесь должна быть проверка роли пользователя
        // Временная заглушка - всегда true для демонстрации
        return true;
    }

    public function orders() {
        $orders = $this->db->query("
            SELECT o.*, u.name as user_name, u.email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.status = 'awaiting_verification'
            ORDER BY o.created_at DESC
        ")->fetchAll();

        $data = [
            'title' => 'Панель оператора - OSPAB Host',
            'orders' => $orders
        ];

        $this->render('admin/orders', $data);
    }

    public function approveOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $action = $_POST['action'];

            $order = $this->db->query(
                "SELECT * FROM orders WHERE id = ?",
                [$orderId]
            )->fetch();

            if ($order) {
                if ($action === 'approve') {
                    // Зачисляем средства или активируем услугу
                    $this->db->query(
                        "UPDATE orders SET status = 'completed' WHERE id = ?",
                        [$orderId]
                    );

                    if ($order['type'] === 'invoice') {
                        // Пополнение баланса
                        $this->db->query(
                            "UPDATE users SET balance = balance + ? WHERE id = ?",
                            [$order['amount'], $order['user_id']]
                        );
                    } else {
                        // Активация услуги
                        // Здесь будет вызов Proxmox API
                    }

                    $_SESSION['success'] = 'Заказ подтвержден';
                } else {
                    // Отклонение заказа
                    $this->db->query(
                        "UPDATE orders SET status = 'rejected' WHERE id = ?",
                        [$orderId]
                    );
                    $_SESSION['success'] = 'Заказ отклонен';
                }
            }

            header('Location: /admin/orders');
            exit;
        }
    }

    private function render($view, $data = []) {
        extract($data);
        require_once "../app/views/layouts/header.php";
        require_once "../app/views/$view.php";
        require_once "../app/views/layouts/footer.php";
    }
}
?>