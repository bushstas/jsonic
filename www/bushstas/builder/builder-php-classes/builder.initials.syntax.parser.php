<?php

class InitialsSyntaxParser
{
	private static $space = '�';
	private static $tab = '�';
	private static $newline = '�';
	private static $code, $fullCode, $prevSign;
	private static $openObjects, $openArrays;
	private static $isQuoted;
	private static $currentQuote;
	private static $expected;
	private static $keyExpected, $valueExpected, $methodExpected, $varNameExpected;
	private static $data, $queue, $classNames, $className, $initialName;
	private static $object, $quotedText, $key, $numberExpected;

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	private static $errors = array(
		'unexpectedSign' => "����������� ������: {{?}{??}<br>&nbsp; &nbsp; &nbsp;...<br>}<br><br>���������:<xmp>{?}</xmp>",
	);

	private static $signs = array(
		'.' => '�����',
		'#' => '���� #',
		'@' => '���� @',
		"'" => '��������� �������',
		'"' => '������� �������',
		'[' => '������������� ���������� ������',
		']' => '������������� ���������� ������',
		':' => '���������',
		',' => '�������',
		'-' => '���� -',
		'{' => '������������� �������� ������',
		'}' => '������������� �������� ������',
		'0' => '�����'
	);

	public static function init() {
		self::$data = array();
		self::$code = '';
		self::$fullCode = '';
		self::$openObjects = 0;
		self::$openArrays = 0;
		self::$isQuoted = false;
		self::$currentQuote = '';
		self::$expected = array('{', '[');
		self::$keyExpected = false;
		self::$valueExpected = false;
		self::$queue = array();
		self::$object = '';
		self::$prevSign = '';
		self::$methodExpected = false;
		self::$varNameExpected = false;
		self::$numberExpected = false;
	}

	public static function initClassNames($classNames) {
		self::$classNames = array_merge($classNames, Helpers::getList());
	}

	public static function initClassName($className) {
		self::$className = $className;
	}

	public static function parse($code, $initialName) {
		self::init();
		self::$initialName = $initialName;

		Printer::log($code);
	
		$code = preg_replace('/ +/', self::$space, trim($code));
		$code = preg_replace('/\t/', self::$tab, $code);
		$code = preg_replace('/\r\n|\n/', self::$newline, $code);

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
		Printer::log(self::$object);
		self::$data['code'] = self::$code;
		return self::$data;
	}

	private static function addCode($code) {
		if ($code == self::$space) {
			if (!$isQuoted) {
				self::$fullCode .= ' ';
				return;
			}
			$code = ' ';
		} else if ($code == self::$tab) {
			if (!$isQuoted) {
				self::$fullCode .= "\t";
				return;
			}
			$code = '';
		} else if ($code == self::$newline) {
			if (!$isQuoted) {
				self::$fullCode .= "\r\n";
				return;
			}
			$code = '';
		}
		self::$fullCode .= $code;
		self::$code .= $code;
		if (self::$isQuoted && $code != self::$currentQuote) {
			self::$quotedText .= $code;
		}
	}

	private static function addName($name) {
		if (self::$isQuoted) {
			self::$code .= $name;
			self::$quotedText .= $name;
		} else {			
			if (self::$prevSign == '@') {
				TextsConstantsParser::addInitialConstant($name, self::$initialName, self::$className);
				self::$object .= '"<nq>'.CONST_CONSTANTS.'.'.$name.'<nq>"';
			} elseif (self::$prevSign == '#') {
				
				
			} else {
				self::$code .= $name;
			}
		}
		self::$fullCode .= $name;
	}

	private static function handleNumber($number) {
		if (self::$isQuoted) return;
		if (!self::isExpected('0')) {
			self::throwUnexpectedSignError($number);
		}	
		self::$object .= $number;
		if (empty(self::$numberExpected)) {
			self::$expected = array('.', self::$space, self::$tab, self::$newline);
			self::$numberExpected = true;
		} else {
			self::$expected = array(self::$space, self::$tab, self::$newline);
			self::$numberExpected = false;
		}		
		self::handleStandartSituation();
	}

	private static function handleName($name) {
		if (self::$isQuoted) return;
		if (!self::isExpected('a')) {
			self::throwUnexpectedSignError($name);
		}
		if (!empty(self::$keyExpected)) {
			self::$object .= '"'.$name.'"';
			self::$expected = array(':', self::$space, self::$tab, self::$newline);
		} elseif (!empty(self::$valueExpected)) {
			if ($name == 'this') {
				self::$expected = array('.');
				self::$methodExpected = true;
				self::$object .= '"<nq>this';
				return;
			}
			self::$expected = array(self::$space, self::$tab, self::$newline);
			if (!empty(self::$methodExpected)) {
				self::$methodExpected = false;
				self::$object .= $name.'<nq>"';
			} elseif (!empty(self::$varNameExpected)) {
				self::$varNameExpected = false;				
			} else {
				if (!in_array($name, self::$classNames)) {
					self::throwUnknownNameError($name);
				}
				self::$object .= '"<nq>'.$name.'<nq>"';
			}
		}
		self::handleStandartSituation();
	}

	private static function handleKeyword($keyword) {
		self::$object .= $keyword;
		self::handleStandartSituation();
	}

	private static function handleSymbol($symbol) {
		if (self::$isQuoted && self::$currentQuote != $symbol) return;
		if (!self::isExpected($symbol)) {
			Printer::log(self::$object);
			Printer::log($symbol);
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
		self::$prevSign = $symbol;
	}

	private static function handleNumberSign() {
		self::$expected = array('a');
		self::$varNameExpected = true;
	}

	private static function handleAtSign() {
		self::$expected = array('a');
		self::$varNameExpected = true;
	}

	private static function handleDot() {
		self::$object .= '.';
		self::$expected = array('a');
		if (!empty(self::$numberExpected)) {
			self::$expected[] = '0';
		}
	}

	private static function handleComma() {
		self::$expected = array('a', '0', '"', "'", self::$space, self::$tab, self::$newline);
		$lastInQueue = self::$queue[count(self::$queue) - 1];
		if (!empty(self::$openObjects) && $lastInQueue == 'o') {
			self::$valueExpected = false;
			self::$keyExpected = true;
		}
		if (!empty(self::$openArrays) && $lastInQueue == 'a') {
			array_push(self::$expected, '-', '{', '[');
		}
		self::$object .= ',';
	}

	private static function handleColon() {
		self::$keyExpected = false;
		self::$valueExpected = true;
		self::$expected = array('a', '0', '"', "'", '-', '{', '[', '#', '@', self::$space, self::$tab, self::$newline);
		self::$object .= ':';
	}

	private static function handleQuote() {
		if (self::$isQuoted && self::$currentQuote == "'") {
			self::handleQuoteClosing();
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = "'";
			self::$quotedText = '';
			self::$object .= '"';
		}
	}

	private static function handleDoubleQuote() {
		if (self::$isQuoted && self::$currentQuote == '"') {
			self::handleQuoteClosing();
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = '"';
			self::$quotedText = '';
			self::$object .= '"';
		}
	}

	private static function handleQuoteClosing() {
		self::$isQuoted = false;
		self::$expected = array(self::$space, self::$tab, self::$newline);
		self::handleStandartSituation();
		self::$object .= self::$quotedText.'"';
	}

	private static function handleStandartSituation() {
		$lastInQueue = self::$queue[count(self::$queue) - 1];
		if (!empty(self::$openObjects) && $lastInQueue == 'o') {
			self::$expected[] = '}';
			if (self::$keyExpected) {
				self::$expected[] = ':';
			}
			if (self::$valueExpected) {
				self::$valueExpected = false;
				self::$expected[] = ',';
			}
		}
		if (!empty(self::$openArrays) && $lastInQueue == 'a') {
			array_push(self::$expected, ']', ',');
		}
		if (!empty(self::$openObjects) || !empty(self::$openArrays)) {
			self::$expected[] = ',';
		}
	}

	private static function handleLeftBrace() {
		self::$openObjects++;
		self::$keyExpected = true;
		self::$expected = array('a', '0', '"', "'", '{', '}', '[', self::$space, self::$tab, self::$newline);	
		self::$queue[] = 'o';
		self::$object .= '{';
	}

	private static function handleRightBrace() {
		self::$openObjects--;
		self::$keyExpected = false;
		self::$valueExpected = false;
		self::$expected = array(self::$space, self::$tab, self::$newline);
		array_pop(self::$queue);
		self::handleStandartSituation();
		self::$object .= '}';
	}

	private static function handleLeftBracket() {
		self::$openArrays++;
		self::$expected = array('a', '0', '"', "'", '{', '[', ']', self::$space, self::$tab, self::$newline);
		self::$queue[] = 'a';
		self::$object .= '[';
	}

	private static function handleRightBracket() {
		self::$openArrays--;
		self::$expected = array(self::$space, self::$tab, self::$newline);
		array_pop(self::$queue);
		self::handleStandartSituation();
		self::$object .= ']';
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

	private static function throwUnexpectedSignError($sign) {
		if ($sign == self::$space) {
			$sign = '&nbsp;';
		} elseif ($sign == self::$tab) {
			$sign = "&nbsp; &nbsp; &nbsp;";
		} elseif ($sign == self::$newline) {
			$sign = "&nbsp;";
		}
		self::$fullCode = preg_replace('/\t/', "&nbsp; &nbsp; &nbsp;", self::$fullCode);
		self::$fullCode = preg_replace('/\r\n/', "<br>", self::$fullCode);
		new Error(self::$errors['unexpectedSign'], array(self::$fullCode, $sign, self::getExpected()));
	}

	private static function throwUnknownNameError($name) {
		die('InitialsSyntaxParser error: throwUnknownNameError '.$name);
	}

	private static function getExpected() {
		$items = array();
		if (self::$isQuoted) {
			self::$expected = array(self::$currentQuote);
		} else {
			self::$expected = array_unique(self::$expected);
		}
		foreach (self::$expected as $exp) {
			if ($exp == self::$space || $exp == self::$tab || $exp == self::$newline) continue;
			if ($exp == 'a') {
				$items[] = '�������� ����� / �������� ������';
			} else {
				$items[] = self::$signs[$exp];
			}

		}
		return implode("\n", $items);
	}
}

?>