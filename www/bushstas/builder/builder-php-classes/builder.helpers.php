<?php

class Helpers
{
	private static $list = array();
	private static $listToCheck = array();

	public static function register($name) {
		self::$list[] = $name;
	}

	public static function registerForChecking($name) {
		self::$listToCheck[] = $name;
	}
	
	public static function getList() {
		return self::$list;
	}

	public static function getListToCheckUsing() {
		return self::$listToCheck;
	}
}