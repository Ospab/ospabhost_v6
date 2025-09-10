<?php
/**
 * Модель User для работы с пользователями хостинга
 * Реализует безопасную регистрацию, аутентификацию и управление профилем.
 */
class User {
	/** @var Database */
	private $db;

	public function __construct() {
		require_once __DIR__ . '/../lib/Database.php';
		$this->db = new Database();
	}

	/**
	 * Зарегистрировать нового пользователя
	 * @param string $email
	 * @param string $password
	 * @param string $name
	 * @return int|false
	 */
	public function register($email, $password, $name) {
		$hash = password_hash($password, PASSWORD_DEFAULT);
		try {
			$this->db->query(
				"INSERT INTO users (email, password, name, created_at) VALUES (?, ?, ?, NOW())",
				[$email, $hash, $name]
			);
			return $this->db->getLastInsertId();
		} catch (Exception $e) {
			error_log('User register error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Получить пользователя по email
	 * @param string $email
	 * @return array|null
	 */
	public function getByEmail($email) {
		$result = $this->db->query("SELECT * FROM users WHERE email = ?", [$email]);
		return $result->fetch() ?: null;
	}

	/**
	 * Получить пользователя по ID
	 * @param int $id
	 * @return array|null
	 */
	public function getById($id) {
		$result = $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
		return $result->fetch() ?: null;
	}

	/**
	 * Проверить пароль пользователя
	 * @param string $email
	 * @param string $password
	 * @return bool
	 */
	public function verifyPassword($email, $password) {
		$user = $this->getByEmail($email);
		if ($user && password_verify($password, $user['password'])) {
			return true;
		}
		return false;
	}
}
