<?php
/**
 * Вспомогательные функции для проекта Ospab Host
 */

/**
 * Безопасный вывод данных в HTML
 * @param string $string
 * @return string
 */
function e($string) {
	return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Генерация CSRF-токена и его хранение в сессии
 * @return string
 */
function csrf_token() {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

/**
 * Проверка CSRF-токена
 * @param string $token
 * @return bool
 */
function check_csrf($token) {
	return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Быстрая генерация случайной строки
 * @param int $length
 * @return string
 */
function random_str($length = 16) {
	return bin2hex(random_bytes($length / 2));
}

/**
 * Перенаправление пользователя
 * @param string $url
 */
function redirect($url) {
	header('Location: ' . $url);
	exit;
}

/**
 * Проверка email
 * @param string $email
 * @return bool
 */
function is_valid_email($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Проверка IP-адреса
 * @param string $ip
 * @return bool
 */
function is_valid_ip($ip) {
	return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

/**
 * Проверка, что строка не пуста и не состоит только из пробелов
 * @param string $str
 * @return bool
 */
function not_empty($str) {
	return isset($str) && trim($str) !== '';
}

?>
