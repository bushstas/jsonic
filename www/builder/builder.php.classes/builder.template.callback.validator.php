<?php

class TemplateCallbackValidator
{
	private static $list;

	public static function init() {
		self::$list = array_keys(JSChecker::$solidMethods);
	}

	public static function isProper($methodName) {
		return !in_array($methodName, self::$list);
	}
}