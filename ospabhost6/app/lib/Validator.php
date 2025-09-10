<?php
/**
 * Класс для валидации пользовательских данных
 */
class Validator {
	private $errors = [];

	/**
	 * Валидация email
	 */
	public function email($value, $field = 'email') {
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			$this->errors[$field] = 'Некорректный email.';
		}
		return $this;
	}

	/**
	 * Валидация длины строки
	 */
	public function length($value, $min, $max, $field = 'field') {
		$len = mb_strlen($value);
		if ($len < $min || $len > $max) {
			$this->errors[$field] = "Длина должна быть от $min до $max символов.";
		}
		return $this;
	}

	/**
	 * Валидация на пустоту
	 */
	public function required($value, $field = 'field') {
		if (!isset($value) || trim($value) === '') {
			$this->errors[$field] = 'Поле обязательно для заполнения.';
		}
		return $this;
	}

	/**
	 * Получить ошибки
	 */
	public function errors() {
		return $this->errors;
	}

	/**
	 * Проверка, есть ли ошибки
	 */
	public function isValid() {
		return empty($this->errors);
	}
}
?>
