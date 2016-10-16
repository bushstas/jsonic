<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className;
	private static $expected = array(
		'func', 'sign'
	);
	private static $errors = array(
		'unexpectedSign' => 'Имеется:<br>{??}<br>Ожидается:<br>{?}'
	);

	public static function init($templateName, $className) {
		self::$templateName = $templateName;
		self::$className = $className;
	}

	public static function parse($code) {
		$code = trim($code);
		self::$code = $code;
		$parts = preg_split('/\b/', $code);
		
		
		$expected = array();
		
		// func
		// varSign
		// text
		// quote
		// mathSigh
		// question
		// colon
		// openParenthesis
		// sloseParenthesis
		// openSquareBracket
		// closeSquareBracket

		for ($i = 0; $i < count($parts); $i++) {
			$part = trim($parts[$i]);
			$cleanPart = preg_replace('/\s/', '', $part);
			if (empty($cleanPart)) continue;
			if (!preg_match('/[\wа-я]/si', $part)) {
				for ($j = 0; $j < strlen($part); $j++) {
					$sign = $part[$j];
					
					switch ($sign) {
						case '&':
						case '@':
						case '#':
						case '^':
						case ':':
						case '~':
						case '.':
						case '$':
							if (!self::isExpected('sign')) {
								self::error('unexpectedSign', array($sign, self::getExpected()));
							}
						break;
					}
				}
			}
		}


		//Printer::log($parts);
	}

	private static function getExpected() {

	}

	private static function error($name, $vars) {
		$err = 'Ошибка в парсинге кода в шаблоне <b>'.self::$templateName.'</b> класса <b>'.self::$className.'</b><br>Код в котором произошла ошибка:<br><b>'.self::$code.'</b><br>';
		new Error($err.self::$errors[$name], $vars);
	}

}