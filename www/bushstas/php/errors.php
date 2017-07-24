<?php

class Errors {
	private static $errors = array(
		'var_ovr' => 'Переопределение переменной {??}'
	);

	public static function get($key) {
		return self::$errors[$key];
	}

}