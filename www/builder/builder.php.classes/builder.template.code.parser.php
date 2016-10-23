<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className, $globalNames, $reservedNames,
				   $utilsFuncs;
	private static $expected = array();
	private static $space = '¦';
	private static $data, $open, $isNum, $isLet, $varType, $currentPart, $isReact,
				   $notTextOrComp, $isKey, $anyVar, $objVar, $decOpen, $reactName,
				   $prevSign, $isCase, $notLetValue, $isStart, $quoted,
				   $thereWasWord, $place;

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
		'pl' => 'имя плэйсхолдера',
		'fn' => 'имя функции',
		'var' => 'имя переменной',
		'method' => 'название метода класса'
	);

	private static $errors = array(
		'unexpectedSign' => 'Неожиданный символ: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedCyr' => 'Неожиданный кириллический текст: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedLat' => 'Неожиданный текст: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedEnd' => 'Неожиданное окончание выражения: {{?}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'usingReservedName' => 'Использование зарезервированного локально имени переменной {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: {{??}}',
		'capitalFirstLetter' => 'Использование вызова функции начинающейся с заглавной буквы в шаблоне {??} класса {??}<br><br>Имена функций утилит должны начинаться с маленькой буквы<br><br>Код в котором произошла ошибка: {{??}}',
		'usingGlobalName' => 'Использование зарезервированного глобально имени переменной {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: {{??}}',
		'usingUnknownFunc' => 'Использование функции {??} в шаблоне {??} класса {??}. Данная функция не найдена в утилитах<br><br>Код в котором произошла ошибка: {{??}}',
		'fewOuterTernaries' => 'Обнаружено несколько конфликтующих тернерных операций в шаблоне {??} класса {??}<br><br>Используйте скобки для их группировки<br><br>Код в котором произошла ошибка: {{??}}'
	);

	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	private static $lists = array(
		'|' => array('.', 'a', '0', '~', '@', '#', '&', '-', '"', "'", '+'),
		'+' => array('a', '0', '~', '&', '@', '.', '-', '(', '"', "'"),
		'-' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'*' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'/' => array('a', '0', '.', '~', '&', '$', '-', '(', '+')
	);

	private static $operators = array(
		'case', 'switch', 'if', 'foreach', 'let', 'default'
	);

	private static $logging = false;
	//private static $logging = true;

	public static function setGlobalNames($names, $reserved, $utilsFuncs, $userUtilsFuncs) {
		self::$globalNames = $names;
		self::$reservedNames = $reserved;
		self::$utilsFuncs = array_merge($utilsFuncs, $userUtilsFuncs);
	}

	public static function init($templateName, $className) {
		self::$templateName = $templateName;
		self::$className = $className;
	}

	private static function initiate(&$code, $place) {
		self::defineExpected($place);
		$code = str_replace('_#_MORE_#_', '>', $code);
		$code = trim($code);
		self::$code = $code;
		self::$place = $place;
		$code = preg_replace('/\s+/', self::$space, $code);
		self::$data = array(
			'react' => array(),
			'let' => array()
		);
		self::$open = array();
		self::$isStart = true;
		self::$prevSign = '';		

		self::$isLet = false;
		self::$isCase = false;
		self::$quoted = false;
	}

	public static function parse($code, $place) {
		self::initiate($code, $place);
		$parts = preg_split('/\b/', $code);		

		$code = '';
		$parsedCode = '';		
		for ($i = 0; $i < count($parts); $i++) {
			$part = $parts[$i];
			if ($part === '') continue;
			if (!preg_match('/[\wа-я]/si', $part)) {
				for ($j = 0; $j < strlen($part); $j++) {
					$sign = $part[$j];
					$signToAdd = $sign;

					if (!self::isSignExpected($sign)) {
						if ($sign == self::$space) $sign = '&nbsp;';
						self::error('unexpectedSign', array($code, $sign, ' ...}', self::getExpected()));
					}
					self::$quoted = self::$expected == '*';
					switch ($sign) {

						case self::$space  : self::handleSpace();         break;
						case '^'           : self::handleCaret();         break;
						case '|'           : self::handleVerticalBar();   break;
						case ':'           : self::handleColon();         break;
						case '='           : self::handleEqual();         break;
						case '$'           : self::handleDollar();        break;
						case ','           : self::handleComma();         break;
						case '.'           : self::handleDot();           break;
						case '?'           : self::handleQuestion();      break;
						case '!'           : self::handleExclamation();   break;
						case '['           : self::handleLeftBracket();   break;
						case ']'           : self::handleRightBracket();  break;
						case '+'           : self::handleMathSign($sign); break;
						case '-'           : self::handleMathSign($sign); break;
						case '*'           : self::handleMathSign($sign); break;
						case '/'           : self::handleMathSign($sign); break;
						case "'"           : self::handleQuote();         break;
						case '"'           : self::handleDoubleQuote();   break;
						case '~'           : self::handleTilde();         break;
						case '@'           : self::handleAtSign();        break;
						case '#'           : self::handleNumberSign();    break;
						case '&'           : self::handleAmpersand();     break;
						case '('           : self::handleLeftParens();    break;
						case ')'           : self::handleRightParens();   break;
						case '>'           : self::handleGreaterSign();   break;
						case '<'           : self::handleGreaterSign();   break;

					}
					if ($sign == self::$space) {
						$sign = ' ';
						$signToAdd = ' ';
						self::on('space');
					} else {
						self::$prevSign = $sign;
						self::off('space');
					}
					$parsedCode .= $sign;
					$code .= $sign;
					if (self::$isLet && self::isOpen('letvalue')) {
						self::$data['let'][count(self::$data['let']) - 1]['value'] .= $signToAdd;						
					}
					self::$isStart = false;
				}
			}
			else
			{
				self::$quoted = self::$open['quote'] || self::$open['doubleQuote'];
				$withoutCyr = preg_replace('/[^\w]/usi', '', $part);
				$isLatin = $part == $withoutCyr;
				
				$isNum = is_numeric($part);
				self::$isNum = $isNum;
				
				if ($isLatin) {
					if (!self::isLatinTextExpected($part, $code)) {
						self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
					}

					self::$prevSign = !$isNum ? 'a' : '0';
					$prevCode = $code;
					
					//adding
					if (!empty(self::$open['method'])) {
						$parsedCode = rtrim($parsedCode, '.')."$.".$part;
					} elseif (self::$open['react2']) {
						$parsedCode = rtrim($parsedCode, ')').",'".$part."')";
					} elseif (self::$open['react']) {
						$parsedCode = $parsedCode.".g('".$part."')";
					} elseif (self::$open['var']) {
						$sign = $parsedCode[strlen($parsedCode) - 1];
						if ($sign == '~') {
							$parsedCode = rtrim($parsedCode, '~').'_.'.$part;
						} else {
							$parsedCode = rtrim($parsedCode, '&').$part;
						}
					} elseif (self::$open['text']) {
						$parsedCode = rtrim($parsedCode, '@').self::$globalNames['CONSTANTS'].'.'.$part;
					} elseif (self::$open['data']) {
						$parsedCode = rtrim($parsedCode, '#').'<data>'.$part;
					} else {
						$parsedCode .= $part;
					}
					$code .= $part;

				
					if (self::$isStart) {
						switch ($part) {
							case 'let':
								self::$isLet = true;
								self::$expected = array('&', '~', self::$space);
							break;
							case 'case':
								self::$isCase = true;
								self::$expected = array('"', "'", 'b', '0', '~', '@', '&', self::$space);
							break;
							case 'default':
								$isDefault = true;
								self::$thereWasWord = true;
								self::$expected = array('end', self::$space);
							break;
						}
						self::$isStart = false;
						if (self::$isCase || $isDefault || self::$isLet) continue;
					}
					if (self::$quoted) {
						self::$expected = '*';
					} else {

						if (self::$isCase) {
							if ($isNum || in_array($part, self::$keywords)) {
								self::$expected = array('end');
								self::$isCase = false;
								continue;
							} else {
								self::error('unexpectedLat', array($prevCode, $part, ' ...}', self::getExpected()));
							}
						}
						
						if (self::prepare($part)) {

							if (self::couldBeColon()) {
								self::$expected[] = ':';
							}
							if (self::couldBeQuestion()) {
								self::$expected[] = '?';
							}
							if (self::couldBeExclamation()) {
								self::$expected[] = '!';
							}
							if (self::couldBePlus()) {
								self::$expected[] = '+';
							}
							if (self::couldBeMinus()) {
								self::$expected[] = '-';
							}
							if (self::couldBeSlash()) {
								self::$expected[] = '/';
							}
							if (self::couldBeStar()) {
								self::$expected[] = '*';
							}
							if (self::couldBePercent()) {
								self::$expected[] = '%';
							}
							if (self::couldBeEqual()) {
								self::$expected[] = '=';
							}
							if (self::couldBeGreater()) {
								self::$expected[] = '>';
								self::$expected[] = '<';
							}
							if (self::couldBeAnd()) {
								self::$expected[] = '&&';
							}
							if (self::couldBeOr()) {
								self::$expected[] = '||';
							}
							if (self::couldBeEnd()) {
								self::$expected[] = 'end';
							}
							if (self::couldBeSpace()) {
								self::$expected[] = self::$space;
							}
							if (self::couldBeLeftParenthesis()) {
								self::$expected[] = '(';
							}
							if (self::couldBeRightParenthesis()) {
								self::$expected[] = ')';
							}
							if (self::couldBeComma()) {
								self::$expected[] = ',';
							}
							if (self::couldBeLeftBracket()) {
								self::$expected[] = '[';
							}
							if (self::couldBeRightBracket()) {
								self::$expected[] = ']';
							}
							if (self::couldBeDot()) {
								self::$expected[] = '.';
							}
						}					
					
						self::finish();					
					}
				} elseif (self::$expected != '*') {
					self::error('unexpectedCyr', array($code, $part, ' ...}', self::getExpected()));
				} else {
					self::$prevSign = 'a';
					$code .= $part;
				}
				self::$thereWasWord = true;
			}
			self::$isStart = false;
		}

		self::check($code);
		self::$expected = $expecteds;
		
		// logging
		if (self::$logging) self::log();

		self::$data['code'] = self::getParsedData($parsedCode);
		
		// logging
		if (self::$logging) Printer::log($data['code']);
		return self::$data;

	}

	private static function handleSpace() {
		if (!self::$quoted) {
			self::$varType = '';
			self::off('react2');
			self::off('var2');
			if (!self::$open['letvalue'] && !self::$open['array']) {
				self::removeExpected('[');
			}
			
			$mathSign = self::$open['math'];
			if ($mathSign == '+' || $mathSign == '-') {
				self::$expected[] = $mathSign;
			}
			if (self::$open['number'] || self::$open['name']) {
				self::removeExpected('.');
			}
		}
	}

	private static function handleCaret() {
		if (!self::$quoted) {
			self::$expected = array('a');
			self::on('comp');
		}
	}

	private static function handleVerticalBar() {	
		if (!self::$quoted) {
			if (self::$prevSign == '|') {
				self::set('or', 2);
				self::$expected = self::$lists['|'];
				array_push(self::$expected, '0', 'a', '~', '&', '#', '@', '-', '!', self::$space);
				self::maybeAddDollar();
			} else {
				self::set('or', 1);
				self::$expected = array('|');
			}
			self::off('name');
		}
	}


	private static function handleColon() {
		if (self::$isStart)
		{
			self::$expected = array(':');
		}
		elseif (!self::$quoted)
		{
			self::$expected = array();
			if (self::$prevSign == ':')
			{
				self::$expected = array('a', '0', self::$space);
				self::on('placeholder');
			} 
			else if (self::$open['ternary'])
			{
				self::add('ternary2');
				array_push(self::$expected, 'a', '0', '.', '~', '&', '@', '#', '-', '+', '!', '(', '"', "'", self::$space);
				self::maybeAddDollar();
			}
			self::off('name');
			self::off('number');
		}
	}

	private static function handleDollar() {
		if (!self::$quoted) {
			self::off('react2');
			self::on('react');
			self::$reactName = '';
			self::$expected = array('a');
			self::$varType = 'r';
		}
	}

	private static function handleComma() {
		if (!self::$quoted) {
			self::$expected = array();
			if (!self::$isLet || !empty(self::$open['func']) || self::$open['array']) {
				self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', self::$space);
				self::maybeAddDollar();
			} else {
				self::$expected = array('&', self::$space);
			}
			if (self::$isLet && empty(self::$open['func']) && !self::$open['array']) {
				self::off('letvalue');
				self::on('letvarname');
			} else {
				self::$expected[] = '(';
			}
			self::off('functionResult');
			self::off('name');
		}
	}

	private static function handleDot() {
		if (!self::$quoted) {
			if (self::$open['number']) {
				self::$expected = array('0');
				self::on('decimal');
			} else {
				if (self::$prevSign != 'a' && self::$prevSign != ']' && !self::$open['react2']) {
					self::add('method');
					self::$varType = '';
				} else {

					switch (self::$varType) {
						case 'r':
							self::on('react2');
						break;
						case 'a':
						case 'l':
							self::on('var');
						break;
						case 'd':
							self::on('data');
						break;
					}
				}
				self::$expected = array('a');
			}
		}
	}

	private static function handleQuestion() {
		if (!self::$quoted) {
			self::add('ternary');
			if (!self::$open['bracket'] && !self::$open['parenthesis'] && !self::$open['func']) {
				if (self::$open['outerTernary']) {
					new Error(self::$errors['fewOuterTernaries'], array(self::$className, self::$templateName, self::$code));
				}
				self::on('outerTernary');
			}
			self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', '"', "'", self::$space);
			self::maybeAddDollar();
			self::off('name');
			self::off('number');
			self::off('greater');
			self::off('doubleEqual');
			self::off('tripleEqual');
			self::off('notEqual');
		}
	}

	private static function handleExclamation() {
		if (!self::$quoted) {
			if (!self::$open['name']) {
				self::$expected = array('a', '0', '&', '~', '#', '=', '-', '+', '!', self::$space);
				self::maybeAddDollar();
			} else {
				self::$expected = array('=');
			}
		}
	}

	private static function handleLeftBracket() {
		if (!self::$quoted) {
			self::add('bracket');
			self::$expected = array('"', "'", 'a', '0', '&', '~', '!', '@', '+', '-', '(', self::$space);
			self::maybeAddDollar();
			if ((self::$prevSign == 'a' || self::$prevSign == ']')) {
				if (self::$varType == 'r') {
					self::on('react2');
				} elseif (!empty(self::$varType)) {
					self::on('var2');
				}
			}
			if (self::$open['letvalue']) {
				self::on('array');
				self::$expected[] = ']';
			}
		}
	}

	private static function handleRightBracket() {
		if (!self::$quoted) {
			self::minus('bracket');
			self::$expected = array('[', '.', self::$space);
			self::off('number');
			if (!self::$open['letvarname']) {
				array_push(self::$expected, '&&', '||', '?', '-', '+', '/', '*', '%', '>', '<', '!', '=');
			}
			if (!empty(self::$open['func']) || !empty(self::$open['parenthesis'])) {
				self::$expected[] = ')';
			}
			if (!empty(self::$open['func'])) {
				self::$expected[] = ',';
			}
			if (!empty(self::$open['ternary']) && self::$open['ternary'] > self::$open['ternary2']) {
				self::$expected[] = ':';
			}
			if (self::$varType == 'r') {
				self::on('react2');
			} elseif (!empty(self::$varType)) {
				self::on('var2');
			}
			if (self::$open['array']) {
				self::off('array');
				self::off('letvalue');
				self::off('number');
			}
			if (self::$isLet) {
				if (self::$open['letvarname']) {
					self::$expected[] = '=';
				} elseif (!self::$open['letvalue']) {
					array_push(self::$expected, ',', 'end');
				}
			} else {
				self::$expected[] = 'end';
			}
		}
	}

	private static function handleEqual() {
		if (!self::$quoted)
		{			
			self::$expected = array();
			self::off('name');
			if (self::$open['placeholder'])
			{
				self::on('placeholderShouldHaveDefaultValue');
				if (!self::$open['placeholderHasName']) {
					self::$expected = array('a', '0', '"', "'", '&', '~', '.', self::$space);
					return;
				}
			}
			if (self::$open['recentQuote'] || self::$open['functionResult'] || (self::$isLet && !self::$open['letvarname'] && self::$prevSign != '=' && self::$prevSign != '!' && !self::$open['greater'])) {
				self::$expected = array('=');
				self::off('functionResult');
				self::off('recentQuote');
			} else {
				self::$expected = array('a', '0', '~', '&', '.', '@', '#', '+', '-', '!', '(', "'", '"', '=', self::$space);
				if (!self::$open['doubleEqual'] && !self::$open['greater'] && !self::$open['notEqual']) {
					if (self::$prevSign == '!') {
						self::on('notEqual');
					} elseif (self::$prevSign == '=') {
						if (!self::$open['doubleEqual']) {
							self::on('doubleEqual');
						}
					}
				} else {
					self::removeExpected('=');
					self::off('doubleEqual');
					self::on('tripleEqual');
				}
				self::maybeAddDollar();
				if (self::$isLet) {
					self::$expected[] = self::$space;
					if (!self::$open['greater'] && !self::$open['doubleEqual'] && !self::$open['tripleEqual']) {
						self::$expected[] = '[';
					}
					if (!self::$open['parenthesis'] && !self::$open['func'] && !self::$open['bracket'] && !self::$open['array']) {
						self::removeExpected('=');
					}
					self::on('letvalue');
					self::off('letvarname');
					$signToAdd = '';
				}
			}
		}
	}

	private static function handleMathSign($sign) {
		if (!self::$quoted) {
			self::$expected = self::$lists[$sign];
			if ($sign == '-' || $sign == '+') {
				self::removeExpected($sign);
			}
			self::maybeAddDollar();
			self::$expected[] = self::$space;
			self::set('math', $sign);
			self::off('functionResult');
			self::$varType = '';
			self::off('name');
			self::off('number');
		}
	}

	private static function handleQuote() {
		if (!self::$open['doubleQuote']) {
			self::$open['quote'] = !self::$open['quote'];
			if (self::$open['placeholderShouldHaveDefaultValue']) {
				self::on('placeholderHasDefaultValue');
			}
		}
		if (self::$open['doubleQuote'] || self::$open['quote']) {
			self::$expected = '*';
		} else {
			self::on('recentQuote');
			self::$expected = array( '?', '+', '&&', '||', '=', '!', self::$space);
			if (!empty(self::$open['ternary']) && self::$open['ternary'] > self::$open['ternary2']) {
				self::$expected[] = ':';
			}
			if (!empty(self::$open['bracket'])) {
				self::$expected[] = ']';
			}
			if (!empty(self::$open['func'])) {
				self::$expected[] = ',';
			}
			if (!empty(self::$open['func']) || !empty(self::$open['parenthesis'])) {
				self::$expected[] = ')';
			}
			if (self::$isLet && !self::$open['letvarname']) {
				self::off('letvalue');
				self::$expected[] = ',';
			}
			self::set('math', '');
			self::set('or', 0);
		}
		if (self::$isCase && !self::$open['quote']) {
			self::$isCase = false;
			self::off('case');
			self::$expected = array('end', self::$space);
		}
	}

	private static function handleDoubleQuote() {
		if (!self::$open['quote']) {
			self::$open['doubleQuote'] = !self::$open['doubleQuote'];
			if (self::$open['placeholderShouldHaveDefaultValue']) {
				self::on('placeholderHasDefaultValue');
			}
		}
		if (self::$open['doubleQuote'] || self::$open['quote']) {
			self::$expected = '*';
		} else {
			self::on('recentQuote');
			self::$expected = array('?', '+', '&&', '||', '=', '!', self::$space);
			if (!empty(self::$open['ternary']) && self::$open['ternary'] > self::$open['ternary2']) {
				self::$expected[] = ':';
			}
			if (!empty(self::$open['bracket'])) {
				self::$expected[] = ']';
			}
			if (!empty(self::$open['func'])) {
				self::$expected[] = ',';
			}
			if (!empty(self::$open['func']) || !empty(self::$open['parenthesis'])) {
				self::$expected[] = ')';
			}
			if (self::$isLet && !self::$open['letvarname']) {
				self::off('letvalue');
				self::$expected[] = ',';
			}
			self::set('math', '');
			self::set('or', 0);
		}
		if (self::$isCase && !self::$open['doubleQuote']) {
			self::$isCase = false;
			self::off('case');
			self::$expected = array('end', self::$space);
		}
	}

	private static function handleTilde() {
		if (!self::$quoted) {
			self::$expected = array('a');
			self::on('var');
			self::off('var2');
			self::$varType = 'a';
			if (self::$isLet && !self::$open['letvarname'] && !self::$open['letvalue'] && empty(self::$open['func']) && empty(self::$open['parenthesis'])) {				
				self::$expected = array('a');
				self::on('letvarname');
			}
		}
	}

	private static function handleAtSign() {
		if (!self::$quoted) {
			self::$expected = array('a');
			self::on('text');
			self::$varType = 't';
		}
	}

	private static function handleNumberSign() {
		if (!self::$quoted) {
			self::$expected = array('a');
			self::on('data');
			self::$varType = 'd';
		}
	}

	private static function handleAmpersand() {
		if (!self::$quoted) {
			self::$expected = array();
			if (!self::$open['name'] && !self::$open['number']) {
				self::$expected[] = 'a';
			}
			self::on('var');
			self::off('var2');
			self::off('functionResult');
			if (self::$prevSign == '&') {
				self::off('letvarname');
				self::set('or', 2);
				self::off('var');
				self::off('name');
				array_push(self::$expected, '0', '~', '#', '+', '-', '!', '(', self::$space);
				self::maybeAddDollar();
			} else if ((self::$thereWasWord && !self::$open['letvarname'] && empty(self::$open['math'])) || self::$open['bracket']) {
				self::$expected[] = '&';
			}
			if (self::$isLet && !self::$open['letvarname'] && !self::$open['letvalue'] && empty(self::$open['func']) && empty(self::$open['parenthesis']) && self::$prevSign != '&') {
				self::on('letvarname');
			}
			self::$varType = 'l';
		}
	}


	private static function handleLeftParens() {
		if (!self::$quoted) {
			self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', '(', self::$space);
			if (self::$prevSign == 'a') {
				self::off('fn');
				self::add('func');
				self::minus('method');
			} else {
				self::add('parenthesis');
			}			
			self::maybeAddDollar();
			if ((self::$prevSign != '(' && empty(self::$open['parenthesis'])) || self::$open['func']) {
				self::$expected[] = ')';
			}
		}
	}

	private static function handleRightParens() {
		if (!self::$quoted) {
			self::$expected = array('&&', '||', '+', ',', '?', self::$space);
			if (!empty(self::$open['parenthesis'])) {
				self::minus('parenthesis');
				if (empty(self::$open['recentQuote'])) {
					array_push(self::$expected, '-', '/', '*', '%', '[', '=', '>', '<', '!');
				}
			} else {
				self::minus('func');
				self::on('functionResult');
				array_push(self::$expected, '-', '/', '*', '%', '[', '=', '>', '<', '!');
			}
			if (!empty(self::$open['ternary']) && self::$open['ternary'] > self::$open['ternary2']) {
				self::$expected[] = ':';
			}
			if (!empty(self::$open['func']) || !empty(self::$open['parenthesis'])) {
				self::$expected[] = ')';
			}
			if (!empty(self::$open['bracket'])) {
				self::$expected[] = ']';
			}
		}
	}

	private static function handleGreaterSign() {
		if (!self::$quoted) {
			self::$expected = array('=', '+', '-', 'a', '0', '~', '&', '#', self::$space);
			self::maybeAddDollar();
			self::on('greater');
			self::off('functionResult');
			self::off('name');
			self::$varType = '';
		}
	}

	private static function maybeAddDollar() {
		if (!self::$open['placeholder'] && !self::$isLet && !self::$isCase) {
			self::$expected[] = '$';
		}
	}

	private static function prepare($part) {
		self::$currentPart = $part;
		self::$expected = array();
		
		self::$isReact = self::isOpen('react', 'react2');
		self::$notTextOrComp = !self::isOpen('text', 'comp');
		self::$isKey = self::isKeyword();
		self::$anyVar = self::isAnyVarOpen();
		self::$objVar = self::$anyVar && self::$notTextOrComp;
		self::$decOpen = self::isOpen('decimal');
		self::$notLetValue = !self::$open['letvarname'];

		if (self::$isNum) {
			if (self::$decOpen) {
				self::off('decimal');
			}
			self::on('number');
		} else {
			self::off('number');
		}

		if (self::$open['comp']) {
			self::$expected = array('end');
			return false;
		}
		if (!self::$isNum && !self::$anyVar && !self::$isKey && !self::$open['comp'] && (!self::$open['placeholder'] || self::$open['placeholderShouldHaveDefaultValue'])) {
			if (!self::$open['method']) {
				self::validateFunction($part);
			}
			self::on('fn');
			self::$expected = array('(');
			return false;
		}
		if (self::isOpen('placeholder') && !self::isOpen('placeholderShouldHaveDefaultValue')) {
			self::$expected = array('=', 'end', self::$space);
			return false;
		} 
		if (self::isOpen('method')) {
			self::$expected = array('(');
			return false;
		}
		return true;
	}

	private static function validateFunction($funcName) {
		if ($funcName == '_') {
			new Error(self::$errors['usingReservedName'], array('_', self::$className, self::$templateName, self::$code));
		}
		if (preg_match('/^[A-Z]/', $funcName)) {
			new Error(self::$errors['capitalFirstLetter'], array(self::$className, self::$templateName, self::$code));
		}
		if (in_array($funcName, self::$reservedNames)) {
			new Error(self::$errors['usingGlobalName'], array($funcName, self::$className, self::$templateName, self::$code));
		}
		if (!in_array($funcName, self::$utilsFuncs)) {
			new Error(self::$errors['usingUnknownFunc'], array($funcName, self::$className, self::$templateName, self::$code));
		}
	}

	private static function removeExpected($sign) {
		if (is_array(self::$expected)) {
			$idx = array_search($sign, self::$expected);
			if (is_int($idx)) {
				array_splice(self::$expected, $idx, 1);
			}
		}
	}

	private static function on($key) {
		self::$open[$key] = true;
	}

	private static function off($key) {
		self::$open[$key] = false;
	}

	private static function add($key) {
		if (!is_int(self::$open[$key])) {
			self::$open[$key] = 0;
		}
		self::$open[$key]++;
	}

	private static function minus($key) {
		if (is_int(self::$open[$key]) && self::$open[$key] > 0) {
			self::$open[$key]--;
		}
	}

	private static function set($key, $value) {
		self::$open[$key] = $value;	
	}

	private static function isKeyword() {
		return in_array(self::$currentPart, self::$keywords);
	}

	private static function couldBeQuestion() {
		return (!self::$open['comp'] && !self::$open['text'] && !self::$open['letvarname']) || self::$open['bracket'] || self::$isKey;
	}

	private static function couldBeExclamation() {
		return (self::$notTextOrComp && !self::$open['letvarname']) || self::$open['bracket'];
	}

	private static function couldBeLeftBracket() {
		return !self::$isNum && !self::$isKey && !self::$open['text'];
	}

	private static function couldBeRightBracket() {
		return self::isOpen('bracket');
	}

	private static function couldBeColon() {
		return self::$open['ternary'] > 0 && self::$open['ternary2'] < self::$open['ternary'] && self::$notTextOrComp;
	}
	
	private static function couldBeEnd() {
		return !self::isAnyOpen();
	}

	private static function couldBeAnd() {
		return (self::$notTextOrComp && !self::$open['letvarname']) || self::$open['bracket'];
	}

	private static function couldBeOr() {
		return (self::$notTextOrComp && !self::$open['letvarname']) || self::$open['bracket'];
	}

	private static function couldBeMinus() {
		return (self::$notTextOrComp && self::$notLetValue) || self::$open['bracket'];
	}

	private static function couldBePlus() {
		return (!self::$open['comp'] && self::$notLetValue) || self::$open['bracket'];
	}

	private static function couldBeSlash() {
		return (self::$notTextOrComp && self::$notLetValue) || self::$open['bracket'];
	}

	private static function couldBePercent() {
		return (self::$notTextOrComp && self::$notLetValue) || self::$open['bracket'];
	}

	private static function couldBeStar() {
		return (self::$notTextOrComp && self::$notLetValue) || self::$open['bracket'];
	}

	private static function couldBeEqual() {
		return self::$open['var'] || self::$open['react'] || self::$open['react2'] || self::$isKey || self::$open['letvarname'] || self::$isNum;
	}

	private static function couldBeGreater() {
		return (self::$open['var'] && !self::$open['letvarname']) || self::$isNum || self::$open['react'] || self::$open['react2'];
	}

	private static function couldBeSpace() {
		return self::$isNum || self::$anyVar || self::$isKey;
	}

	private static function couldBeLeftParenthesis() {
		return !self::$isNum && self::$notLetValue && !self::$open['text'];
	}

	private static function couldBeRightParenthesis() {
		return self::isOpen('parenthesis', 'func');
	}

	private static function couldBeComma() {
		return self::$open['func'] || (self::$isLet && !self::$open['letvalue'] && !self::$open['letvarname']) || self::$open['array'];
	}

	private static function couldBeDot() {
		return (self::$objVar || (self::$isNum && !self::$decOpen)) && self::$notLetValue;
	}

	private static function finish() {
		self::off('recentQuote');
		if (self::$open['placeholder']) {
			if (!self::$open['placeholderShouldHaveDefaultValue']) {
				self::on('placeholderHasName');
			} else {
				self::on('placeholderHasDefaultValue');
			}
		}
		

		if (self::$isReact) {
			if (empty(self::$reactName)) {
				self::$data['react'][self::$currentPart] = array();
				self::$reactName = self::$currentPart;
			} else {
				self::$data['react'][self::$reactName][] = self::$currentPart;
			}
		} else {
			self::$reactName = '';
		}

		if (self::$isLet) {
			if (!self::isOpen('letvalue')) {
				self::$data['let'][] = array('name' => self::$currentPart);
			} else {
				$idx = count(self::$data['let']) - 1;
				if (!isset(self::$data['let'][$idx]['value'])) {
					self::$data['let'][$idx]['value'] = '';
				}
				self::$data['let'][$idx]['value'] .= self::$currentPart;
				if (empty(self::$open['parenthesis'])) {
					self::$expected[] = ',';
				}
			}
			
		}
		self::offVars();
		self::off('letvalue');
		self::off('space');
	}

	private static function offVars() {
		if (!self::$isNum) {
			self::on('name');
		}
		self::off('react');
		self::off('var');
		self::off('data');
		self::off('text');
		self::off('comp');
		self::set('or', 0);
		self::set('math', '');
	}

	
	private static function isAnyVarOpen() {
		return self::isOpen('react', 'react2', 'text', 'comp', 'var', 'var2', 'data');
	}

	private static function isOpen() {
		$args = func_get_args();
		if (is_array($args[0])) {
			$args = $args[0];
		}		
		foreach ($args as $a) {
			$open = self::$open[$a];
			if (is_int($open)) {
				if ($open > 0) return true;
			} else {
				if ($open === true) return true;
			}
		}
		return false;
	}

	private static function isAnyOpen() {
		return self::isOpen(array_keys(self::$open));
	}


	private static function defineExpected($place) {
		switch ($place) {
			case 'elementAttribute':
				self::$expected = array('0', '+', '-', '!', 'a', '.', '&', '$', '~', '@', '#');
			break;
			case 'componentAttribute':
				self::$expected = array('0', '+', '-', '!', 'a', '.', '&', '$', '~', '@', '#', '^');
			break;
			case 'textNode':
				self::$expected = array('0', '+', '-', '!', 'a', '.', ':', '&', '$', '~', '@');
			break;
			default:
				self::$expected = array();
		}
	}

	private static function isSignExpected($sign) {
		if (self::$expected == '*') return true;
		if (in_array($sign, self::$expected)) return true;
		if ($sign == '&' && in_array('&&', self::$expected)) return true;
		if ($sign == '|' && in_array('||', self::$expected)) return true;
		return false;
	}

	private static function isLatinTextExpected($text, $code) {
		if (self::$expected == '*') return true;
		if (is_numeric($text) && in_array('0', self::$expected)) {
			return true;
		}
		if (in_array('a', self::$expected) || in_array('b', self::$expected)) {
			if (is_numeric($text[0])) {
				return false;
			}
			return true;
		}
	}

	private static function getExpected() {
		$items = array();
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

	private static function error($name, $vars) {
		self::log();
		$err = 'Ошибка в парсинге кода в шаблоне <b>'.self::$templateName.'</b> класса <b>'.self::$className.'</b><br><br>Код в котором произошла ошибка: {'.self::$code.'}<br><br>';
		new Error($err.self::$errors[$name], $vars);
	}

	private static function getExpectedWord() {
		if (self::$open['placeholder'] && !self::$open['placeholderHasName']) {
			return self::$names['pl'];
		}
		if (self::$open['var'] || self::$open['react']) {
			return self::$names['var'];
		}
		if (!empty(self::$open['method'])) {
			return self::$names['method'];
		}
		return self::$names['fn'];
	}

	private static function printOpen() {
		$o = array();
		foreach (self::$open as $k => $v) {
			if (!empty($v)) {
				$o[$k] = $v;
			}
		}
		Printer::log($o);
	}

	private static function log() {
		self::printOpen();
		Printer::log(self::$expected);
	}

	//checking
	private static function check($code) {
		$expecteds = self::$expected;
		if (!self::$isCase && self::$thereWasWord) {
			self::$expected = null;
		}		
		
		if (!empty(self::$open['ternary']) && !self::$open['functionResult'] && !self::$open['number'] && !self::$open['name'] && !self::$open['recentQuote'] && !self::isAnyVarOpen())
		{
			self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', self::$space);
			self::maybeAddDollar();
		} 
		elseif (self::$open['decimal'])
		{
			self::$expected = array("1");
		}
		elseif (self::$open['placeholder'] && !self::$thereWasWord)
		{
			self::$expected = array("a");
		} elseif (self::$open['fn'])
		{
			self::$expected = array("(");
		} elseif (!empty(self::$open['math']))
		{
			self::$expected = self::$lists[self::$open['math']];
		}
		elseif (self::$open['quote'])
		{
			self::$expected = array("'");
		}
		elseif (self::$open['doubleQuote'])
		{
			self::$expected = array('"');
		}
		elseif (!empty(self::$open['or']))
		{
			if (self::$open['or'] == 1) {
				self::$expected = array('|');
			} else {
				self::$expected = self::$lists['|'];
			}
		}
		elseif (self::$open['placeholderShouldHaveDefaultValue'] && !self::$open['placeholderHasDefaultValue'])
		{
			self::$expected = $expecteds;
		}
		elseif (self::isOpen('react', 'text', 'comp', 'var', 'data') && !self::$open['placeholderHasName'])
		{
			self::$expected = array('a');
		}
		elseif (!empty(self::$open['bracket']))
		{
			self::$expected = array(']');
		}
		elseif (!empty(self::$open['func']) || !empty(self::$open['parenthesis']))
		{
			self::$expected = array(')');
		}
		elseif (!empty(self::$open['method']))
		{
			self::$expected = array('(');
		}
		elseif (self::$isLet)
		{
			if (self::$open['letvalue']) self::$expected = $expecteds;
			else if (self::$open['letvarname']) self::$expected = $expecteds;
		}
		
		if (is_array(self::$expected))
		{
			self::error('unexpectedEnd', array($code, '<b>&nbsp;}</b>', self::getExpected()), true);
		}
	}

	private static function getParsedData($code) {
		if (!empty(self::$open['ternary'])) {
			self::$data['ternary'] = true;
			if (self::$open['ternary'] > self::$open['ternary2']) {
				self::$data['ternaryIncomplete'] = true;
			}
		}
		if (self::$open['placeholder']) {
			return self::getPlaceholderCode($code);		
		} 
		if (self::$place == 'textNode' && !empty(self::$data['react'])) {
			return self::getReactTextNode($code);
			
		} elseif (!empty(self::$data['let'])) {
			$lets = array();
			foreach (self::$data['let'] as $let) {
				$lets[] = $let['name'].'='.$let['value'];
			}
			$code = '<let>var '.implode(',', $lets).'<=let>';
		}
		self::$data['code'] = '<nq>'.$code.'<nq>';
		return $code;
	}

	private static function getPlaceholderCode($code) {
		preg_match_all('/::(\w+)(.+)*/', $code, $matches);
		$name = $matches[1][0];
		$def = preg_replace('/^[\s=]+/', '', $matches[2][0]);
		if (empty($def)) {
			$code = "{'pl':'".$name."'}";	
		} else {
			$code = "{'pl':'".$name."','p':".$def."}";
		}
		return $code;
	}

	private static function getReactTextNode($code) {
		$names = array_keys(self::$data['react']);
		if (count($names) == 1) {
			$names = "'".$names[0]."'";
		} else {
			$names = json_encode($names);
			$code = 'function(){return'.$code.'}';
		}
		return "{'pr':".$names.",'p':".$code."}";
	}

}