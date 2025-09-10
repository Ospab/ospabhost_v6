<?php
/**
 * Модель ProxmoxMaster для управления мастер-узлами Proxmox
 * Используется для хранения и управления данными о кластере.
 */
class ProxmoxMaster {
	/** @var Database */
	private $db;

	public function __construct() {
		require_once __DIR__ . '/../lib/Database.php';
		$this->db = new Database();
	}

	/**
	 * Получить все мастер-узлы
	 * @return array
	 */
	public function getAll() {
		$result = $this->db->query("SELECT * FROM proxmox_masters ORDER BY id ASC");
		return $result->fetchAll();
	}

	/**
	 * Получить мастер-узел по ID
	 * @param int $id
	 * @return array|null
	 */
	public function getById($id) {
		$result = $this->db->query("SELECT * FROM proxmox_masters WHERE id = ?", [$id]);
		return $result->fetch() ?: null;
	}
}
