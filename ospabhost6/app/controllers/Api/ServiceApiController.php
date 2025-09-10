<?php
/**
 * API-контроллер для работы с услугами (REST)
 */
class ServiceApiController {
	private $serviceModel;

	public function __construct() {
		require_once '../../models/Service.php';
		$this->serviceModel = new Service();
		header('Content-Type: application/json; charset=utf-8');
	}

	/**
	 * Получить услугу по ID (GET /api/service/{id})
	 */
	public function get($id) {
		$service = $this->serviceModel->getServiceById($id);
		if ($service) {
			echo json_encode(['success' => true, 'service' => $service]);
		} else {
			http_response_code(404);
			echo json_encode(['success' => false, 'error' => 'Service not found']);
		}
	}
}
