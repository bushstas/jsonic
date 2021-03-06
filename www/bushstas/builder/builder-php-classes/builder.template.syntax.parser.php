<?php

class TemplateSyntaxParser
{
	private static $space = '�';
	private static $expected, $isQuoted, $currentQuote, $open, $prevSign,
				   $code, $currentCode, $place, $templateName, $className,
				   $reactNames, $globalNames, $openBrackets, $openParens,
				   $methodNames, $functionNames, $openTernaryQuestions,
				   $openTernaryColons, $ternaries, $queue, $openFuncs,
				   $fullCode, $fields, $localNames, $prevSign2;

	private static $errors = array(
		'unexpectedSign' => '����������� ������: {{?}{??} ...}<br><br>���������:<xmp>{?}</xmp>',
		'unexpectedEnd' => '����������� ����� ���������: {{?}{??}}<br><br>���������:<xmp>{?}</xmp>',
		'incorrectName' => '������������ ��� {{?}{??} ...} '
	);

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	private static $signs = array(
		'a' => '����������� ���������� ��� �������',
		'b' => 'false, true, null ��� undefined',
		'.' => '�����',
		'&' => '���� &',
		'$' => '���� $',
		'~' => '���� ~',
		'^' => '���� ^',
		'#' => '���� #',
		'@' => '���� @',
		"'" => '��������� �������',
		'"' => '������� �������',
		'(' => '������������� ������� ������',
		')' => '������������� ������� ������',
		'[' => '������������� ���������� ������',
		']' => '������������� ���������� ������',
		'?' => '�������������� ����',
		':' => '���������',
		',' => '�������',
		'!' => '��������������� ����',
		'-' => '���� -',
		'+' => '���� +',
		'=' => '���� =',
		'>' => '���� >',
		'<' => '���� <',
		'|' => '���� |',
		'*' => '���� *',
		'/' => '���� /',
		'%' => '���� %',
		'{' => '������������� �������� ������',
		'}' => '������������� �������� ������',
		';' => '����� � �������',
		'0' => '�����',
		'1' => '�����'
	);

	private static $names = array(
		'v' => '��� ����������',
		'm' => '�������� ������ ������',
		'f' => '��� �������',
		'g' => '��� ���������� ����������'
	);

	public static function init($palce, $templateName, $className) {
		self::$place = $place;
		self::$templateName = $templateName;
		self::$className = $className;
	}
	
	public static function parse($text, $expected, $currentCode) {
		$text = trim($text);
		if (!is_array($expected)) {
			$expected = array();
		}
		
		self::$expected = $expected;
		self::$open = array();
		self::$code = '';
		self::$fullCode = '';
		self::$prevSign = '';
		self::$prevSign2 = '';
		self::$isQuoted = false;
		self::$currentQuote = '';
		self::$currentCode = $currentCode;
		self::$reactNames = array();
		self::$globalNames = array();
		self::$methodNames = array();
		self::$functionNames = array();
		self::$openBrackets = 0;
		self::$openFuncs = 0;
		self::$openTernaryQuestions = 0;
		self::$openTernaryColons = 0;
		self::$openParens = array();
		self::$ternaries = array();
		self::$queue = array();
		self::$fields = array();
		self::$localNames = array();

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
						self::addName($part);
					} else {
						self::handleKeyword($part);
						self::addCode($part);
					}
					self::setPrevSign('a');
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

		self::checkCompleteness();

		return array(
			'r' => self::$reactNames,
			'g' => self::$globalNames,
			'm' => self::$methodNames,
			'f' => self::$functionNames,
			'l' => self::$localNames,
			'c' => self::$code
		);
	}

	private static function setPrevSign($sign) {
		if ($sign != self::$space) {
			self::$prevSign2 = self::$prevSign;
			self::$prevSign = $sign;
		}
	}

	private static function addName($name) {
		if (self::$isQuoted) {
			self::$code .= $name;
		} else {
			if (self::$prevSign == ':') {
				self::$code = rtrim(self::$code, ':').".a('".$name."')";
			} elseif (self::$prevSign == '$') {
				self::$code = self::$code.".g('".$name."')";
			} elseif (self::$prevSign == '~') {
				self::$code = rtrim(self::$code, '~')."_['".$name."']";
			} elseif (self::$prevSign == '&') {
				self::$code = rtrim(self::$code, '&').$name;
			} elseif (self::$prevSign == '.' && !self::isField()) {
				self::$code = rtrim(self::$code, '.').'$.'.$name;
			} elseif (self::$prevSign == '@') {
				self::$code = rtrim(self::$code, '@').CONST_CONST_CONSTANTS.'.'.$name;
				TextsConstantsParser::addTemplateConstant($name, self::$templateName, self::$className);
			} else {
				self::$code .= $name;
			}
		}
		self::$fullCode .= $name;
	}

	private static function addCode($code) {
		if ($code == self::$space) {
			if (!$isQuoted) {
				self::$fullCode .= ' ';
				return;
			}
			$code = ' ';
		}
		self::$fullCode .= $code;
		self::$code .= $code;
	}

	private static function handleNumber($number) {
		if (self::$isQuoted) return;
		if (!self::isExpected('0')) {
			self::throwUnexpectedSignError($number);
		}
		self::on('number');
		self::$expected = array('+', '-', '*', '/', '%', '>', '<', '=', '!', '?', self::$space);
		self::handleStandartSituation();
		if (!self::$open['decimal']) {
			self::$expected[] = '.';
		} else {
			self::off('decimal');
		}
		self::setPrevSign('0');
	}

	private static function handleKeyword($keyword) {
		if (self::$isQuoted) return;
		self::$expected = array('=', '!', '?', self::$space);
	}

	private static function handleName($name) {
		if (self::$isQuoted) return;
		if (!self::isExpected('a')) {
			self::throwUnexpectedSignError($name);
		}
		$signs = array('&', '~', '$', '@', '#');
		if (self::$open['globalVar']) {
			$signs[] = ':';
		}
		if (in_array(self::$prevSign, $signs)) {
			self::on('var');
			switch (self::$prevSign) {
				case '$':
					if (!in_array($name, self::$reactNames)) {
						self::$reactNames[] = $name;
					}
				break;
				case ':':
					if (!in_array($name, self::$globalNames)) {
						self::$globalNames[] = $name;
					}
					self::off('globalVar');
				break;
				case '&':
					if (!in_array($name, self::$localNames)) {
						self::$localNames[] = $name;
					}
				break;
			}
			self::$expected = array(self::$space);
			if (self::$prevSign != '@') {
				array_push(self::$expected, '.', '[', '+', '-', '*', '/', '%', '?', '<', '>', '!', '=');
			}
			if (!empty(self::$openParens)) {
				array_push(self::$expected, ',', ')');
			}
			self::handleStandartSituation();
		} else {			
			if (self::isField()) {
				self::$expected = array(self::$space, '.', '[', '&', '|', '?', '-', '+', '/', '*', '%', '>', '<', '!', '=');			
				self::handleStandartSituation();
				self::addField($name);
			} elseif (self::$prevSign == '.') {
				self::on('methodName');
				self::$openFuncs++;
				self::$expected = array('(');
				self::$methodNames[] = $name;
			} else {
				self::on('functionName');
				self::$openFuncs++;
				self::$expected = array('(');
				self::$functionNames[] = $name;
			}
		}
	}

	private static function isField() {
		return is_array(self::$fields[self::$openBrackets]);
	}

	private static function addField($value = null) {
		if (!is_array(self::$fields[self::$openBrackets])) {
			self::$fields[self::$openBrackets] = array();
		}
		if (!empty($value)) {
			self::$fields[self::$openBrackets][] = $value;
		}
	}

	private static function handleSymbol($symbol) {
		if (self::$isQuoted && self::$currentQuote != $symbol) return;
		if (!self::isExpected($symbol)) {
			self::throwUnexpectedSignError($symbol);
		}	
		switch ($symbol) {
			case self::$space  : self::handleSpace();           break;
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

	private static function handleSpace() {
		self::off('number');
	}

	private static function handleDefaultSign() {
		self::$expected = array();
	}

	private static function handleVerticalBar() {
		if (self::$prevSign == '|') {
			self::$expected = array('.', 'a', '0', '$', '~', '@', '#', '&', '+', '-', '"', "'", '!');
		} else {
			self::$expected = array('|');
		}
		self::off('var');
		self::off('number');
	}

	private static function handleEqual() {
		self::off('var');
		self::off('number');
		self::$expected = array();
		if (self::$prevSign == '=' || self::$prevSign == '!') {
			array_push(self::$expected, 'a', '0', '$', '~', '&', '.', '@', '#', '+', '-', '!', '(', "'", '"', self::$space);
		}
		if (self::$prevSign != '=' || (self::$prevSign2 != '=' && self::$prevSign2 != '!')) {
			self::$expected[] = '=';
		}
	}

	private static function handleExclamation() {
		if (!empty(self::$prevSign) && in_array(self::$prevSign, array('a', '0', ']', ')'))) {
			self::$expected = array('=');
		} else {
			self::$expected = array('a', '0', '.', '$', '~', '&', '#', '-', '+', '!', '(', self::$space);
		}
	}

	private static function handleMathSign($sign) {
		self::$expected = array('a', '0', '$', '~', '&', '@', '.', '-', '(', self::$space);
		if ($sign == '+') {
			array_push(self::$expected, '"', "'");
		}
		self::off('globalVar');
		self::off('var');
		self::off('number');
	}

	private static function handleTilde() {
		self::$expected = array('a');
	}

	private static function handleAtSign() {
		self::$expected = array('a');
	}

	private static function handleNumberSign() {
		self::$expected = array('a');
	}

	private static function handleGreaterSign() {
		self::$expected = array('=', '+', '-', 'a', '0', '$', '~', '&', '#', self::$space);
		self::off('globalVar');
		self::off('var');
		self::off('number');
	}	

	private static function handleDot() {
		if (self::$open['number']) {
			self::$expected = array('0');
			self::on('decimal');
		} else {
			if (self::$prevSign != 'a' && self::$prevSign != ']') {
				self::on('method');
			} else {
				self::addField();
			}
			self::$expected = array('a');
		}
	}

	private static function handleLeftBracket() {
		self::$queue[] = 'b';
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'", self::$space);
		self::addField();
		self::$openBrackets++;
	}

	private static function handleRightBracket() {
		self::tryToCloseTernary();
		array_pop(self::$queue);
		self::$openBrackets--;
		self::$expected = array('.', '[', '&', '|', '?', '-', '+', '/', '*', '%', '>', '<', '!', '=', self::$space);			
		self::handleStandartSituation();
		self::off('number');
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
		self::$expected = array( '?', '+', '&', '|', '=', '!', self::$space);		
		self::handleStandartSituation();
	}

	private static function handleStandartSituation() {
		$lastInQueue = self::$queue[count(self::$queue) - 1];
		if (!empty(self::$openTernaryQuestions) && self::$openTernaryQuestions > self::$openTernaryColons) {
			self::$expected[] = ':';
		}
		if (!empty(self::$openBrackets) && $lastInQueue == 'b') {
			self::$expected[] = ']';
		}
		if (!empty(self::$openFuncs)) {
			self::$expected[] = ',';
		}
		if (!empty(self::$openParens) && $lastInQueue == 'p') {
			self::$expected[] = ')';
		}
	}

	private static function handleComma() {
		self::tryToCloseTernary();
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'", self::$space);
		self::off('var');
		self::off('number');
	}

	private static function handleLeftParens() {
		self::$expected = array('a', '0', '.', '$', '~', '&', '@', '#', '!', '+', '-', '(', '"', "'", self::$space);
		if (self::$open['methodName'] || self::$open['functionName']) {
			self::off('methodName');
			self::off('functionName');
			self::$expected[] = ')';
		}		
		self::$openParens[] = 'f';
		self::$queue[] = 'p';
	}

	private static function handleRightParens() {
		self::tryToCloseTernary();
		self::$openFuncs--;
		array_pop(self::$openParens);
		array_pop(self::$queue);
		self::$expected = array('&', '|', '+', '-', '?', '/', '*', '%', '[', '=', '>', '<', '!', self::$space);
		self::handleStandartSituation();
	}

	private static function handleQuestion() {
		self::$openTernaryQuestions++;

		self::$ternaries[] = array(
			'b' => self::$openBrackets,
			'p' => count(self::$openParens)
		);
		if (empty(self::$openBrackets) && empty(self::$openParens) && empty(self::$openFuncs)) {
			if (self::$open['outerTernary']) {
				new Error(self::$errors['fewOuterTernaries'], array(self::$className, self::$templateName, self::$fullCode));
			}
			self::on('outerTernary');
		}
		self::$expected = array('.', 'a', '0', '!', '$', '~', '&', '#', '@', '(', '"', "'", self::$space);
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
		if ($sign == self::$space) {
			$sign = '&nbsp;';
		}
		new Error(self::$errors['unexpectedSign'], array(self::$currentCode.self::$fullCode, $sign, self::getExpected()));
	}

	private static function throwIncorrectNameError($name) {
		new Error(self::$errors['incorrectName'], array(self::$currentCode.self::$fullCode, $name));
	}

	private static function getExpected() {
		$items = array();
		if (self::$isQuoted) {
			self::$expected = array(self::$currentQuote);
		} else {
			self::$expected = array_unique(self::$expected);
		}
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

	private static function checkCompleteness() {
		extract(self::$open);
		if (!empty($methodName) || !empty($functionName) || !empty(self::$openParens) || !empty(self::$openBrackets) || !in_array(self::$prevSign, array(')', ']', 'a', '0', "'", '"'))) {
			new Error(self::$errors['unexpectedEnd'], array(self::$currentCode.self::$fullCode, '&nbsp;', self::getExpected()));
		}
	}

	public static function getKeywords() {
		return self::$keywords;
	}
}