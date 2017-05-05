<?php

class InitialsSyntaxParser
{
	private static $space = '¦';
	private static $code;
	private static $openObjects, $openArrays;
	private static $isQuoted;
	private static $currentQuote;
	private static $expected;

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	public static function init() {
		self::$code = '';
		self::$openObjects = 0;
		self::$openArrays = 0;
		self::$isQuoted = false;
		self::$currentQuote = '';
		self::$expected = array('{', '[');
	}

	public static function parse($code) {
		self::init();
		$data = array();
		
		$code = preg_replace('/\s+/', self::$space, trim($code));
		self::validate($code);
		$parts = preg_split('/\b/', $code);
		foreach ($parts as $i => $part) {
			if ($part !== '') {
				if (is_numeric($part)) {
					self::handleNumber($part);
					self::addCode($part);
				} elseif (preg_match('/[a-z]/i', $part[0])) {
					if (!in_array($part, self::$keywords)) {
						self::handleName($part);
						self::addName($part);
					} else {
						self::handleKeyword($part);
						self::addCode($part);
					}
				} elseif (is_numeric($part[0]) && !self::$isQuoted) {
					self::throwIncorrectNameError($part);
				} else {
					for ($i = 0; $i < strlen($part); $i++) {
						self::handleSymbol($part[$i]);
						self::addCode($part[$i]);
					}
				}
			}			
		}

		$data['code'] = self::$code;
		return $data;
	}

	private static function addCode($code) {
		self::$code .= $code;
	}

	private static function addName($name) {
		self::$code .= $name;
	}

	private static function handleNumber($number) {

	}

	private static function handleName($name) {

	}

	private static function handleKeyword($keyword) {

	}

	private static function handleSymbol($symbol) {
		if (self::$isQuoted && self::$currentQuote != $symbol) return;
		if (!self::isExpected($symbol)) {
			self::throwUnexpectedSignError($symbol);
		}	
		switch ($symbol) {
			case self::$space  : self::handleSpace();           break;
			case '{'           : self::handleLeftBrace();       break;
			case '}'           : self::handleRightBrace();      break;
			case '|'           : self::handleVerticalBar();     break;
			case ':'           : self::handleColon();           break;
			case '='           : self::handleEqual();           break;
			case '$'           : self::handleDollar();          break;
			case ','           : self::handleComma();           break;
			case '.'           : self::handleDot();             break;
			case '?'           : self::handleQuestion();        break;
			case '!'           : self::handleExclamation();     break;
			case '['           : self::handleLeftBracket();     break;
			case ']'           : self::handleRightBracket();    break;
			case '+'           : self::handleMathSign($symbol); break;
			case '-'           : self::handleMathSign($symbol); break;
			case '*'           : self::handleMathSign($symbol); break;
			case '%'           : self::handleMathSign($symbol); break;
			case '/'           : self::handleMathSign($symbol); break;
			case "'"           : self::handleQuote();           break;
			case '"'           : self::handleDoubleQuote();     break;
			case '~'           : self::handleTilde();           break;
			case '@'           : self::handleAtSign();          break;
			case '#'           : self::handleNumberSign();      break;
			case '&'           : self::handleAmpersand();       break;
			case '('           : self::handleLeftParens();      break;
			case ')'           : self::handleRightParens();     break;
			case '>'           : self::handleGreaterSign();     break;
			case '<'           : self::handleGreaterSign();     break;
			default            : self::handleDefaultSign();
		}
		self::setPrevSign($symbol);
	}

	private static function handleLeftBrace() {
		self::$openObjects++;
		self::$expected = array('a', '0', '"', "'", '{', '}', '[');
	}

	private static function handleRightBrace() {
		self::$openObjects--;
		self::$expected = array(',', '}', ']');
	}

	private static function isExpected($sign) {
		return self::$isQuoted ? true : in_array($sign, self::$expected);
	}

	private static function validate($code) {
		$a = array('{', '[');
		$b = array('}', ']');
		if (!in_array($code[0], $a) || !in_array($code[strlen($code) - 1], $b)) {
			die('InitialsSyntaxParser error');
		}
	}

	private static function throwIncorrectNameError($word) {
		die('InitialsSyntaxParser error');
	}

	private static function throwUnexpectedSignError($word) {
		die('InitialsSyntaxParser error');
	}
}

?>