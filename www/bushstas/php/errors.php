<?php

class Errors {
	private static $errors = array(
		'var_ovr' => '��������������� ���������� {??}'
	);

	public static function get($key) {
		return self::$errors[$key];
	}

}