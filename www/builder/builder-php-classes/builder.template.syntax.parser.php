<?php

class TemplateSyntaxParser
{
	private static $space = '¦';
	private static $expected, $isQuoted, $currentQuote, $open, $prevSign,
				   $code, $currentCode, $place, $templateName, $className,
				   $reactNames, $globalNames, $openBrackets, $openParens,
				   $methodNames, $functionNames, $openTernaryQuestions,
				   $openTernaryColons, $ternaries, $queue;

	private static $errors = array(
		'unexpectedSign' => 'Неожиданный символ: {{?}{??} ...}',
		'unexpectedEnd' => 'Неожиданный конец выражения: {{?}{??}}<br><br>Ожидается:<xmp>{?}</xmp>'
	);

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	private static $signs = array(
		'a' => 'определение переменной или функции',
		'b' => 'false, true, null или undefined',
		'.' => 'точка',
		'&' => 'знак &',
		'&&' => 'знак &',
		'$' => 'знак $',
		'~' => 'знак ~',
		'^' => 'знак ^',
		'#' => 'знак #',
		'@' => 'знак @',
		"'" => 'одинарная кавычка',
		'"' => 'двойная кавычка',
		'(' => 'открывающаяся круглая скобка',
		')' => 'закрывающаяся круглая скобка',
		'[' => 'открывающаяся квадратная скобка',
		']' => 'закрывающаяся квадратная скобка',
		'?' => 'вопросительный знак',
		':' => 'двоеточие',
		',' => 'запятая',
		'!' => 'восклицательный знак',
		'-' => 'знак -',
		'+' => 'знак +',
		'=' => 'знак =',
		'>' => 'знак >',
		'<' => 'знак <',
		'|' => 'знак |',
		'||' => 'знак |',
		'*' => 'знак *',
		'/' => 'знак /',
		'%' => 'знак %',
		'{' => 'открывающаяся фигурная скобка',
		'}' => 'закрывающаяся фигурная скобка',
		';' => 'точка с запятой',
		'end' => 'конец выражения',
		'0' => 'число',
		'1' => 'цифра'
	);

	private static $names = array(
		'v' => 'имя переменной',
		'm' => 'название метода класса',
		'f' => 'имя функции',
		'g' => 'имя глобальной переменной'
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
		self::$methodNames = array();
		self::$functionNames = array();
		self::$openBrackets = 0;
		self::$openTernaryQuestions = 0;
		self::$openTernaryColons = 0;
		self::$openParens = array();
		self::$ternaries = array();
		self::$queue = array();

		$text = preg_replace('/\s+/', self::$space, $text);
		$parts = preg_split('/\b/', $text);
		
		foreach ($parts as $part) {
			if ($part !== '') {
				if (is_numeric($part)) {
					self::handleNumber($part);
					self::addCode($part);
				} elseif (preg_match('/[a-z]/i', $part[0])) {
					if (!in_array($part, self::$keywords)) {
						self::handleName($part);	
					} else {
						self::handleKeyword($part);	
					}					
					self::addCode($part);
				} else {
					for ($i = 0; $i < strlen($part); $i++) {
						self::handleSymbol($part[$i]);
						self::addCode($part[$i]);
					}
				}
			}
		}

		self::checkCompleteness();

		return array(
			'r' => self::$reactNames,
			'g' => self::$globalNames,
			'm' => self::$methodNames,
			'f' => self::$functionNames,
			'c' => self::getCode()
		);
	}

	private static function addCode($code) {
		if ($code == self::$space) {
			if (!$isQuoted) return;
			$code = ' ';
		}
		self::$code .= $code;
	}

	private static function getCode() {
		$c = self::$code;
		$c = preg_replace('/\.([a-z]\w*)/i', "\$.$1", $c);
		$c = preg_replace('/&(\w+)/', "$1", $c);
		$c = preg_replace('/~(\w+)/', "_['$1']", $c);
		$c = preg_replace('/\$:(\w+)/', '$'.".a('$1')", $c);
		$c = preg_replace('/\$(\w+)/', '$'.".g('$1')", $c);
		
		return $c;
	}

	private static function handleNumber($number) {
		if (self::$isQuoted) return;
		if (!self::isExpected('0')) {
			self::throwUnexpectedSignError($number);
		}
		self::on('number');
	}

	private static function handleKeyword($keyword) {
		if (self::$isQuoted) return;
	}

	private static function handleName($name) {
		if (self::$isQuoted) return;
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
			self::$expected = array(self::$space);
			if (self::$prevSign != '@') {
				array_push(self::$expected, '[', '+', '-', '*', '/', '%', '?');
			}
			if (!empty(self::$openParens)) {
				array_push(self::$expected, ',', ')');
			}

		} elseif (self::$prevSign == '.') {
			self::on('methodName');
			self::$expected = array('(');
			self::$methodNames[] = $name;
		} else {
			self::$expected = array('(');
			self::on('functionName');
			self::$functionNames[] = $name;
		}
	}

	private static function handleSymbol($symbol) {
		if (self::$isQuoted) return;
		if (!self::isExpected($symbol)) {
			self::throwUnexpectedSignError($symbol);
		}	
		switch ($symbol) {
			case "[":
				self::handleLeftBracket();
			break;
			case "]":
				self::handleRightBracket();
			break;
			case "'":
				self::handleQuote();
			break;
			case '"':
				self::handleDoubleQuote();
			break;
			case ',':
				self::handleComma();
			break;
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

	private static function handleLeftBracket() {
		self::$openBrackets++;
		self::$queue[] = 'b';
	}

	private static function handleRightBracket() {
		self::tryToCloseTernary();
		array_pop(self::$queue);
	}

	private static function handleQuote() {
		if (self::$isQuoted && self::$currentQuote == "'") {
			self::$isQuoted = false;
			if (!empty(self::$openParens)) {
				array_push(self::$expected, ',', ')');
			}
			if (empty(self::$openTernaryQuestions)) {

			}
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = "'";
		}
	}

	private static function handleDoubleQuote() {
		if (self::$isQuoted && self::$currentQuote == '"') {
			self::$isQuoted = false;
			if (!empty(self::$openParens)) {
				array_push(self::$expected, ',', ')');
			}
		} elseif (!self::$isQuoted) {
			self::$isQuoted = true;
			self::$currentQuote = '"';
		}
	}

	private static function handleComma() {
		self::tryToCloseTernary();
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'", self::$space);
	}

	private static function handleLeftParen() {
		if (self::$open['methodName'] || self::$open['functionName']) {
			self::off('methodName');
			self::off('functionName');
			self::$openParens[] = 'f';
			self::$queue[] = 'p';
		}
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'", self::$space);
	}

	private static function handleRightParen() {
		self::tryToCloseTernary();
		array_pop(self::$openParens);
		array_pop(self::$queue);
	}

	private static function handleQuestion() {
		self::$openTernaryQuestions++;

		self::$ternaries[] = array(
			'b' => self::$openBrackets,
			'p' => count(self::$openParens)
		);
		if (!self::$open['bracket'] && !self::$open['parenthesis'] && !self::$open['func']) {
			if (self::$open['outerTernary']) {
				new Error(self::$errors['fewOuterTernaries'], array(self::$className, self::$templateName, self::$code));
			}
			self::on('outerTernary');
		}
		self::$expected = array('a', '0', '!', '$', '~', '&', '#', '@', '(', '"', "'", self::$space);
		self::off('var');
		self::off('number');
		self::off('greater');
		self::off('doubleEqual');
		self::off('tripleEqual');
		self::off('notEqual');
	}

	private static function handleColon() {
		if (self::$prevSign == '$') {
			self::$expected = array('a');
			self::on('globalVar');
		} else {			
			self::$openTernaryColons++;
			array_pop(self::$ternaries);
			self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '-', '+', '!', '(', '"', "'", self::$space);
			self::off('var');
			self::off('number');
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

	private static function tryToCloseTernary() {
		$env = self::$queue[count(self::$queue) - 1];
		if ($env == 'p') {
			$cnt = count(self::$openParens);
		} else {
			$cnt = self::$openBrackets;
		}		
		$ternary = self::$ternaries[count(self::$ternaries) - 1];
		if (!empty($ternary) && $ternary[$env] == $cnt) {
			array_pop(self::$ternaries);
			self::$code .= ":<emptystring>";
		}		
	}

	private static function on($item) {
		self::$open[$item] = true;
	}

	private static function off($item) {
		unset(self::$open[$item]);
	}

	private static function isExpected($sign) {
		return self::$isQuoted ? true : in_array($sign, self::$expected);
	}

	private static function throwUnexpectedSignError($sign) {
		new Error(self::$errors['unexpectedSign'], array(self::$currentCode.self::$code, $sign));
	}

		private static function checkCompleteness() {
		extract(self::$open);
		if (!empty($methodName)) {
			new Error(self::$errors['unexpectedEnd'], array(self::$currentCode.self::$code, '&nbsp;', self::getExpected()));
		}
	}

	private static function getExpected() {
		$items = array();
		self::$expected = array_unique(self::$expected);
		foreach (self::$expected as $exp) {
			if ($exp == self::$space) continue;
			if ($exp == 'a') {
				$items[] = self::getExpectedWord();
			} else {
				$items[] = self::$signs[$exp];
			}

		}
		return implode("\n", $items);
	}

	private static function getExpectedWord() {
		if (self::$open['var']) {
			return self::$names['v'];
		}
		if (self::$open['global']) {
			return self::$names['g'];
		}
		if (!empty(self::$open['methodName'])) {
			return self::$names['m'];
		}
		return self::$names['f'];
	}
}