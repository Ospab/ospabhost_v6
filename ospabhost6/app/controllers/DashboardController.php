<?php
class DashboardController {
    private $db;
    private $serviceModel;

    public function __construct() {
        require_once '../app/lib/Database.php';
        require_once '../app/lib/Auth.php';
        require_once '../app/models/Service.php';
        
        $this->db = new Database();
        $this->serviceModel = new Service();
    }

    public function index() {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = Auth::getUserId();
        $services = $this->serviceModel->getUserServices($userId);

        $data = [
            'title' => 'Личный кабинет - OSPAB Host',
            'services' => $services
        ];

        $this->render('dashboard/index', $data);
    }

    public function services() {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = Auth::getUserId();
        $services = $this->serviceModel->getUserServices($userId);

        // Получаем статусы для всех сервисов
        foreach ($services as &$service) {
            $service['status_info'] = $this->serviceModel->getServiceStatus($service['id']);
        }

        $data = [
            'title' => 'Мои серверы - OSPAB Host',
            'services' => $services
        ];

        $this->render('dashboard/services', $data);
    }

    public function createService() {
        if (!Auth::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $plan = $_POST['plan'];
            
            // Конфигурации для разных тарифов
            $plans = [
                'vps-basic' => [
                    'memory' => 1024,
                    'cores' => 1,
                    'disk' => 'local-lvm:20'
                ],
                'vps-standard' => [
                    'memory' => 2048,
                    'cores' => 2,
                    'disk' => 'local-lvm:40'
                ],
                'lxc-basic' => [
                    'memory' => 512,
                    'cores' => 1,
                    'rootfs' => 'local-lvm:10'
                ]
            ];

            if (isset($plans[$plan])) {
                try {
                    $serviceId = $this->serviceModel->createService(
                        Auth::getUserId(),
                        strpos($plan, 'lxc') !== false ? 'lxc' : 'vps',
                        $plans[$plan]
                    );

                    header('Location: /dashboard/services?success=service_created');
                    exit;
                } catch (Exception $e) {
                    $error = "Ошибка при создании сервера: " . $e->getMessage();
                }
            } else {
                $error = "Неверный тарифный план";
            }
        }

        $data = [
            'title' => 'Создать сервер - OSPAB Host',
            'error' => $error ?? null
        ];

        $this->render('dashboard/create_service', $data);
    }

    private function render($view, $data = []) {
        extract($data);
        require_once "../app/views/layouts/header.php";
        require_once "../app/views/$view.php";
        require_once "../app/views/layouts/footer.php";
    }
}
?>