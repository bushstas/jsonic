<?php

class Iterator {
	private static $i, $parts;

	static function init($parts) {
		self::$i = 0;
		self::$parts = $parts;
	}

	static function current($add = false) {
		$a = self::$parts[self::$i];
		if ($add === true) self::add();
		return $a;
	}

	static function add($n = 1) {
		self::$i += $n;
	}

	static function next($add = false) {
		$a = self::$parts[self::$i + 1];
		if ($add === true) self::add();
		return $a;
	}

	static function prev($n = 1) {
		return self::$parts[self::$i - $n];
	}

	static function has() {
		return isset(self::$parts[self::$i + 1]);
	}
}