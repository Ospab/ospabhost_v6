<?php
/**
 * Модель заказа (Order) для системы хостинга Ospab Host
 * Реализует безопасную работу с заказами, защиту от SQL-инъекций и удобный API.
 */
class Order {
	/** @var Database */
	private $db;

	public function __construct() {
		require_once __DIR__ . '/../lib/Database.php';
		$this->db = new Database();
	}

	/**
	 * Создать новый заказ
	 * @param int $userId
	 * @param int $serviceId
	 * @param float $amount
	 * @param string $status
	 * @return int|false ID заказа или false при ошибке
	 */
	public function create($userId, $serviceId, $amount, $status = 'pending') {
		try {
			$this->db->query(
				"INSERT INTO orders (user_id, service_id, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())",
				[$userId, $serviceId, $amount, $status]
			);
			return $this->db->getLastInsertId();
		} catch (Exception $e) {
			error_log('Order create error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Получить заказ по ID
	 * @param int $orderId
	 * @return array|null
	 */
	public function getById($orderId) {
		$result = $this->db->query("SELECT * FROM orders WHERE id = ?", [$orderId]);
		return $result->fetch() ?: null;
	}

	/**
	 * Получить все заказы пользователя
	 * @param int $userId
	 * @return array
	 */
	public function getByUser($userId) {
		$result = $this->db->query("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
		return $result->fetchAll();
	}

	/**
	 * Обновить статус заказа
	 * @param int $orderId
	 * @param string $status
	 * @return bool
	 */
	public function updateStatus($orderId, $status) {
		try {
			$this->db->query("UPDATE orders SET status = ? WHERE id = ?", [$status, $orderId]);
			return true;
		} catch (Exception $e) {
			error_log('Order updateStatus error: ' . $e->getMessage());
			return false;
		}
	}
}
