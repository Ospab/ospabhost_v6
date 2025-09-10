<?php
/**
 * Контроллер для управления услугами (VPS, LXC и др.)
 * Позволяет просматривать, создавать и управлять услугами пользователя.
 */
class ServicesController {
	private $serviceModel;

	public function __construct() {
		require_once '../app/models/Service.php';
		require_once '../app/lib/Auth.php';
		$this->serviceModel = new Service();
	}

	/**
	 * Список всех услуг пользователя
	 */
	public function index() {
		if (!Auth::isLoggedIn()) {
			header('Location: /auth/login');
			exit;
		}
		$userId = Auth::getUserId();
		$services = $this->serviceModel->getUserServices($userId);
		$data = [
			'title' => 'Мои услуги - OSPAB Host',
			'services' => $services
		];
		$this->render('services/index', $data);
	}

	/**
	 * Просмотр одной услуги
	 */
	public function view($id) {
		if (!Auth::isLoggedIn()) {
			header('Location: /auth/login');
			exit;
		}
		$service = $this->serviceModel->getServiceById($id);
		if (!$service || $service['user_id'] != Auth::getUserId()) {
			header('Location: /services');
			exit;
		}
		$data = [
			'title' => 'Просмотр услуги',
			'service' => $service
		];
		$this->render('services/view', $data);
	}

	/**
	 * Остановка услуги
	 */
	public function suspend($id) {
		if (!Auth::isLoggedIn()) {
			header('Location: /auth/login');
			exit;
		}
		$service = $this->serviceModel->getServiceById($id);
		if ($service && $service['user_id'] == Auth::getUserId()) {
			$this->serviceModel->suspendService($id);
		}
		header('Location: /services');
		exit;
	}

	private function render($view, $data = []) {
		extract($data);
		require_once "../app/views/layouts/header.php";
		require_once "../app/views/$view.php";
		require_once "../app/views/layouts/footer.php";
	}
}
