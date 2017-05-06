<?php

class Obfuscator
{
	private static $unsetUnknown = false;
	private static $namesMap;

	public static function obfuscate(&$data, $deobfuscation = false) {
		include __DIR__.'/../data/js.map.php';
		self::$namesMap = $deobfuscation ? array_flip($map) : $map;
		if (!is_array($map) || !is_array($data)) {
			return;
		}
		self::run($data);
	}

	private static function run(&$data) {
		$obfuscatedData = array();
		foreach ($data as $k => &$v) {
			$isNum = is_numeric($k);
			if (!$isNum && isset(self::$namesMap[$k])) {
				$obfuscatedData[self::$namesMap[$k]] = &$v;
			} elseif ($isNum || !self::$unsetUnknown) {
				$obfuscatedData[$k] = &$v;
			}
			if (is_array($v)) {
				self::run($v);
			}
		}
		$data = $obfuscatedData;
	}
}