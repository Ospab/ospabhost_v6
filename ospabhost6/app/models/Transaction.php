<?php
/**
 * Модель Transaction для работы с транзакциями пользователей
 * Безопасная работа с платежами и историями операций.
 */
class Transaction {
	/** @var Database */
	private $db;

	public function __construct() {
		require_once __DIR__ . '/../lib/Database.php';
		$this->db = new Database();
	}

	/**
	 * Создать новую транзакцию
	 * @param int $userId
	 * @param float $amount
	 * @param string $type
	 * @param string $status
	 * @return int|false
	 */
	public function create($userId, $amount, $type, $status = 'pending') {
		try {
			$this->db->query(
				"INSERT INTO transactions (user_id, amount, type, status, created_at) VALUES (?, ?, ?, ?, NOW())",
				[$userId, $amount, $type, $status]
			);
			return $this->db->getLastInsertId();
		} catch (Exception $e) {
			error_log('Transaction create error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Получить все транзакции пользователя
	 * @param int $userId
	 * @return array
	 */
	public function getByUser($userId) {
		$result = $this->db->query("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
		return $result->fetchAll();
	}
}
