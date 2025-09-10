<?php
/**
 * API-контроллер для работы с пользователями (REST)
 */
class UserApiController {
	private $userModel;

	public function __construct() {
		require_once '../../models/User.php';
		$this->userModel = new User();
		header('Content-Type: application/json; charset=utf-8');
	}

	/**
	 * Получить пользователя по ID (GET /api/user/{id})
	 */
	public function get($id) {
		$user = $this->userModel->getById($id);
		if ($user) {
			unset($user['password']);
			echo json_encode(['success' => true, 'user' => $user]);
		} else {
			http_response_code(404);
			echo json_encode(['success' => false, 'error' => 'User not found']);
		}
	}
}
