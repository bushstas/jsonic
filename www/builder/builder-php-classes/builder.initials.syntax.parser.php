<?php




 ÑÄÅËÀÒÜ ÐÅÊÓÐÑÈÂÍÎ

class InitialsSyntaxParser
{
	private static $space = '¦';
	private static $code;
	private static $openObjects, $openArrays;
	private static $isQuoted;
	private static $currentQuote;
	private static $expected;
	private static $keyExpected;
	private static $valueExpected;
	private static $data, $queue;
	private static $currentObjects;

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	public static function init() {
		self::$data = array(
			'object' => array()
		);
		self::$code = '';
		self::$openObjects = 0;
		self::$openArrays = 0;
		self::$isQuoted = false;
		self::$currentQuote = '';
		self::$expected = array('{', '[');
		self::$keyExpected = false;
		self::$valueExpected = false;
		self::$queue = array();
		self::$currentObjects = array(
			&self::$data['object']
		);
	}

	public static function parse($code) {
		self::init();
	
		$code = preg_replace('/\s+/', self::$space, trim($code));
		Printer::log($code);
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

		self::$data['code'] = self::$code;
		return self::$data;
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
		if (self::$isQuoted) return;
		if (!self::isExpected('a')) {
			self::throwUnexpectedSignError($name);
		}
		if (empty($keyExpected)) {
			
		}
	}

	private static function handleKeyword($keyword) {

	}

	private static function handleSymbol($symbol) {
		if (self::$isQuoted && self::$currentQuote != $symbol) return;
		if (!self::isExpected($symbol)) {
			self::throwUnexpectedSignError($symbol);
		}	
		switch ($symbol) {
			case '{': self::handleLeftBrace();       break;
			case '}': self::handleRightBrace();      break;
			case ':': self::handleColon();           break;
			case ',': self::handleComma();           break;
			case '.': self::handleDot();             break;
			case '[': self::handleLeftBracket();     break;
			case ']': self::handleRightBracket();    break;
			case '-': self::handleMathSign($symbol); break;
			case "'": self::handleQuote();           break;
			case '"': self::handleDoubleQuote();     break;
			case '@': self::handleAtSign();          break;
			case '#': self::handleNumberSign();      break;
		}
	}

	private static function handleQuote() {
		if (self::$isQuoted && self::$currentQuote == "'") {
			self::handleQuoteClosing();
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = "'";
		}
	}

	private static function handleDoubleQuote() {
		if (self::$isQuoted && self::$currentQuote == '"') {			
			self::handleQuoteClosing();
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = '"';
		}
	}

	private static function handleQuoteClosing() {
		self::$isQuoted = false;
		self::$expected = array(self::$space);		
		self::handleStandartSituation();
	}

	private static function handleStandartSituation() {
		$lastInQueue = self::$queue[count(self::$queue) - 1];
		if (!empty(self::$openObjects) && $lastInQueue == 'o') {
			self::$expected[] = '}';
			if (self::$valueExpected) {
				self::$valueExpected = false;
				self::$expected[] = ',';
			}
		}
		if (!empty(self::$openArrays) && $lastInQueue == 'a') {
			array_push(self::$expected, ']', ',');
		}
	}

	private static function handleLeftBrace() {
		self::$openObjects++;
		self::$keyExpected = true;
		self::$expected = array('a', '0', '"', "'", '{', '}', '[', self::$space);
		
		$c = count(self::$currentObjects);
		$o = &self::$currentObjects[$c - 1];
		$o[] = array();
		self::$queue[] = 'o';
	}

	private static function handleRightBrace() {
		self::$openObjects--;
		self::$keyExpected = false;
		self::$valueExpected = false;
		self::$expected = array(self::$space);
		array_pop(self::$queue);
		self::handleStandartSituation();
	}

	private static function handleLeftBracket() {
		self::$openArrays++;
		self::$expected = array('a', '0', '"', "'", '{', '[', ']', self::$space);
		//self::$currentObject
		self::$queue[] = 'a';
	}

	private static function handleRightBracket() {
		self::$openArrays--;
		self::$expected = array(self::$space);
		array_pop(self::$queue);
		self::handleStandartSituation();
	}

	private static function isExpected($sign) {
		return self::$isQuoted ? true : in_array($sign, self::$expected);
	}

	private static function validate($code) {
		$a = array('{', '[');
		$b = array('}', ']');
		if (!in_array($code[0], $a) || !in_array($code[strlen($code) - 1], $b)) {
			die('InitialsSyntaxParser error: validate');
		}
	}

	private static function throwIncorrectNameError($word) {
		die('InitialsSyntaxParser error: throwIncorrectNameError');
	}

	private static function throwUnexpectedSignError($word) {
		die('InitialsSyntaxParser error: throwUnexpectedSignError '.$word);
	}
}

?>