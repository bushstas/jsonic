<?php

class TemplateSyntaxParser
{
	private static $space = '¦';
	private static $expected, $isQuoted, $currentQuote, $open, $prevSign,
				   $code, $currentCode, $place, $templateName, $className,
				   $reactNames, $globalNames, $openBrackets, $openParens;

	private static $errors = array(
		'unexpectedSign' => 'Неожиданный символ: {{?}{??} ...}'
	);

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	public static function init($palce, $templateName, $className) {
		self::$place = $place;
		self::$templateName = $templateName;
		self::$className = $className;
	}
	
	public static function parse($text, $expected, $currentCode) {
		if (!is_array($expected)) {
			$expected = array();
		}
		
		self::$expected = $expected;
		self::$open = array();
		self::$code = '';
		self::$prevSign = '';
		self::$isQuoted = false;
		self::$currentQuote = '';
		self::$currentCode = $currentCode;
		self::$reactNames = array();
		self::$globalNames = array();
		self::$openBrackets = 0;
		self::$openParens = array();

		$text = preg_replace('/\s+/', self::$space, $text);
		$parts = preg_split('/\b/', $text);
		
		foreach ($parts as $part) {
			if ($part !== '') {
				if (is_numeric($part)) {
					self::handleNumber($part);
					self::$code .= $part;
				} elseif (preg_match('/[a-z]/i', $part[0])) {
					if (!in_array($part, self::$keywords)) {
						self::handleName($part);	
					} else {
						self::handleKeyword($part);	
					}					
					self::$code .= $part;
				} else {
					for ($i = 0; $i < strlen($part); $i++) {
						self::handleSymbol($part[$i]);
						self::$code .= $part[$i];
					}
				}
			}
		}
	}

	private static function handleNumber($number) {
		if (!self::isExpected('0')) {
			self::throwUnexpectedSignError($number);
		}
		self::on('number');
	}

	private static function handleKeyword($keyword) {

	}

	private static function handleName($name) {
		if (!self::isExpected('a')) {
			self::throwUnexpectedSignError($keyword);
		}
		$signs = array('&', '~', '$', '@', '#');
		if (self::$open['globalVar']) {
			$signs[] = ':';
		}
		if (in_array(self::$prevSign, $signs)) {
			self::on('var');
			switch (self::$prevSign) {
				case '$':
					self::$reactNames[] = $name;
				break;
				case ':':
					self::$globalNames[] = $name;
					self::off('globalVar');
				break;
			}
			self::$expected = array();
			if (self::$prevSign != '@') {
				self::$expected = array('[', '+', '-', '*', '/', '%', '?');
			}
			if (!empty(self::$openParens)) {
				self::$expected[] = ')';
			}

		} elseif (self::$prevSign == '.') {
			self::on('methodName');
			self::$expected = array('(');
		} else {
			self::$expected = array('(');
			self::on('functionName');
		}
	}

	private static function handleSymbol($symbol) {
		if (!self::isExpected($symbol)) {
			self::throwUnexpectedSignError($symbol);
		}	
		switch ($symbol) {
			case '(':
				self::handleLeftParen();
			break;
			case ')':
				self::handleRightParen();
			break;
			case '?':
				self::handleQuestion();
			break;
			case ':':
				self::handleColon();
			break;
			case '$':
				self::handleDollar();
			break;
			case '&':
				self::handleAmpersand();
			break;
		}
		self::$prevSign = $symbol;
	}

	private static function handleLeftParen() {
		if (self::$open['methodName'] || self::$open['functionName']) {
			self::off('methodName');
			self::off('functionName');
			self::$openParens[] = 'f';
		}
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'");
	}

	private static function handleRightParen() {

	}

	private static function handleQuestion() {

	}

	private static function handleColon() {
		self::$expected = array('a');
		if (self::$prevSign == '$') {
			self::on('globalVar');
		}
	}

	private static function handleDollar() {
		self::$expected = array('a', ':');
	}

	private static function handleAmpersand() {
		if (self::$prevSign == '&') {
			self::$expected = array(self::$space);
		} else {
			self::$expected = array('a');
			if (self::$open['var']) {
				self::$expected[] = '&';
			}
		}		
	}

	private static function on($item) {
		self::$open[$item] = true;
	}

	private static function off($item) {
		unset(self::$open[$item]);
	}

	private static function isExpected($sign) {
		return in_array($sign, self::$expected);
	}

	private static function throwUnexpectedSignError($sign) {
		new Error(self::$errors['unexpectedSign'], array(self::$currentCode.self::$code, $sign));
	}
}