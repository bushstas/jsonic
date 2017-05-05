<?php

class TextsConstantsParser
{	
	private static $regexp = '/@(\w+)/';
	private static $jsList = array();
	private static $tmpList = array();
	private static $index;

	private static $errors = array(
		'textConstNotFound' => 'Текстовая константа {??} используемая в шаблоне {??} класса {??} не найдена',
		'textConstNotFound2' => 'Текстовая константа {??} используемая в методе {??} класса {??} не найдена'
	);

	public static function parse(&$code, $method, $className) {
		preg_match_all(self::$regexp, $code, $matches);
		$code = preg_replace(self::$regexp, CONST_CONSTANTS.".$1", $code);
		foreach ($matches[1] as $match) {
			self::check($match, $method, $className, 1);
			self::add(self::$jsList, $match, $method, $className);
		}
	}

	public function setIndex($index) {
		self::$index = $index;
	}

	public static function getList() {
		return array(
			'js'  => self::$jsList,
			'tmp' => self::$tmpList
		);
	}

	public static function addTemplateConstant($name, $templateName, $className) {
		self::check($name, $templateName, $className, 2);
		self::add(self::$tmpList, $name, $templateName, $className);
	}

	private static function add(&$list, $name, $place, $class) {
		if (!isset($list[$name])) {
			$list[$name] = array();
			$list[$name][] = array($place, $class);
		} else {
			$found = false;
			foreach ($list[$name] as $item) {
				if ($item[0] == $place && $item[1] == $class) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$list[$name][] = array($place, $class);
			}
		}
	}

	private static function check($name, $place, $class, $type) {
		if (!isset(self::$index[$name])) {
			if ($type == 1) {
				new Error(self::$errors['textConstNotFound2'], array($name, $place, $class));
			} else if ($type == 2) {
				new Error(self::$errors['textConstNotFound'], array($name, $place, $class));
			}
		}
	}
}