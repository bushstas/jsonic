<?php

class Iteration {

	private static $i, $parts;


	public static function init($parts) {
		self::$i = -1;
		self::$parts = $parts;
	}

	public static function current($add = false) {
		$a = self::$parts[self::$i];
		if ($add === true) self::add();
		return $a;
	}

	public static function add($n = 1) {
		self::$i += $n;
	}

	public static function next($add = false) {
		$a = self::$parts[self::$i + 1];
		if ($add === true) self::add();
		return $a;
	}

	public static function prev($n = 1) {
		return self::$parts[self::$i - $n];
	}

	public static function has() {
		return isset(self::$parts[self::$i + 1]);
	}


	public static function isSpace($shift = 0) {
		$s = self::$parts[self::$i + $shift];
		return $s == ' ' || $s == "\r\n" || $s == "\r" || $s == "\r\n" || $s == "\t";
	}
}