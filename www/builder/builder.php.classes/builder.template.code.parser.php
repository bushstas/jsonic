<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className, $globalNames;
	private static $expected = array();
	private static $space = '¦';
	private static $data, $open, $isNum, $isLet, $varType, $currentPart, $isReact,
				   $notTextOrComp, $isKey, $anyVar, $objVar, $decOpen, $reactName,
				   $prevSign, $isCase, $couldBeMathSign, $isStart, $quoted;

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
		'|' => 'знак |',
		'||' => 'знак |',
		'*' => 'знак *',
		'/' => 'знак /',
		'%' => 'знак %',
		'{' => 'открывающаяся фигурная скобка',
		'}' => 'закрывающаяся фигурная скобка',
		';' => 'точка с запятой',
		'end' => 'конец выражения',
		'0' => 'число'
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
		'unexpectedEnd' => 'Неожиданное окончание выражения: {{?}{?}<br><br>Ожидается:<xmp>{?}</xmp>'
	);

	private static $keywords = array(
		'false', 'true', 'null', 'undefined'
	);

	private static $lists = array(
		'|' => array('.', 'a', '0', '~', '@', '#', '&', '-'),
		'+' => array('a', '0', '~', '&', '@', '.', '-', '('),
		'-' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'*' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'/' => array('a', '0', '.', '~', '&', '$', '-', '(', '+')
	);

	private static $operators = array(
		'case', 'switch', 'if', 'foreach', 'let', 'default'
	);

	public static function setGlobalNames($names) {
		self::$globalNames = $names;
	}

	public static function init($templateName, $className) {
		self::$templateName = $templateName;
		self::$className = $className;
	}

	public static function parse($code, $place) {
		self::defineExpected($place);		
		$code = trim($code);
		//Printer::log($code);
		self::$code = $code;
		$code = preg_replace('/\s+/', self::$space, $code);
		$parts = preg_split('/\b/', $code);
		//Printer::log($parts);
		$data = array(
			'react' => array(),
			'let' => array()
		);
		self::$data = $data;
		self::$open = array();

		self::$isStart = true;
		$isOpened = 0;
		$isClosingTernary = 0;

		$code = '';
		$parsedCode = '';
		self::$prevSign = '';
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
							
						
						case '"':
								if (!$isOpenQuote) {
									$isOpenQuote2 = !$isOpenQuote2;
									if (self::$open['placeholderShouldHaveDefaultValue']) {
										self::on('placeholderHasDefaultValue');
									}
								}
								if ($isOpenQuote2 || $isOpenQuote) {
									self::$expected = '*';
								} else {
									self::$expected = array(':', '?', '+', '&&', '||', self::$space);
									if (!empty(self::$open['bracket'])) {
										self::$expected[] = ']';
									}
								}
								if (self::$isCase && !$isOpenQuote2) {
									self::$isCase = false;
									self::off('case');
									self::$expected = array('end', self::$space);
								}
						break;

						case "~":
							if (!self::$quoted) {
								self::$expected = array('a');
								self::on('var');
								self::$varType = 'a';
								if (self::$isLet && !self::isOpen('letvarname')) {
									self::$expected = array('a');
									self::on('letvarname');
								}
							}
						break;

						case "@":
							if (!self::$quoted) {
								self::$expected = array('a');
								$isTextOpen = true;
								self::on('text');
								self::$varType = 't';
							}
						break;

						case "#":
							if (!self::$quoted) {
								self::$expected = array('a');
								self::on('data');
								self::$varType = 'd';
							}
						break;

						case "&":
							if (!self::$quoted) {								
								self::$expected = array('a');
								self::on('var');
								if (self::$prevSign == '&') {
									self::set('or', 2);
									self::off('var');
									array_push(self::$expected, '0', '~', '#', '-', '!',self::$space);
									if (!self::$isLet && !self::$isCase) {
										self::$expected[] = '$';
									}
								} else if ($thereWasWord) {
									self::$expected[] = '&';
								}
								if (self::$isLet && !self::$open['letvarname'] && !self::$open['letvalue']) {
									self::on('letvarname');
								}
								self::$varType = 'l';
							}
						break;

						case "(":
							if (!self::$quoted) {
								if (self::$prevSign == 'a') {
									$isFuncExpected = false;
									self::off('fn');
									self::add('func');
									self::minus('method');
								} else {
									$isOpened++;
									self::add('parenthesis');
								}
								self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', ')', self::$space);
								if (!self::$isLet && !self::$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case ")":
							if (!self::$quoted) {
								if ($isOpened > 0) {
									$isOpened--;
									self::minus('parenthesis');
								} else {
									self::minus('func');
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', ',', self::$space);
								if (!empty(self::$open['ternary'])) {
									self::$expected[] = ':';
								}
							}
						break;
					}
					if ($sign == self::$space) {
						$sign = ' ';
						$signToAdd = ' ';
						self::on('space');
					} else {
						self::$prevSign = $sign;
						self::off('space');
					}
					if (!self::$open['react2']) {
						$parsedCode .= $sign;
						$code .= $sign;
					}

					if (self::$isLet && self::isOpen('letvalue')) {
						$data['let'][count($data['let']) - 1]['value'] .= $signToAdd;						
					}
					self::$isStart = false;
				}
			}
			else
			{
				self::$quoted = $isOpenQuote || $isOpenQuote2;
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
					} elseif($isTextOpen) {
						$parsedCode = rtrim($parsedCode, '@').self::$globalNames['CONSTANTS'].'.'.$part;
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
								$thereWasWord = true;
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
				$thereWasWord = true;
			}
			self::$isStart = false;
		}


		$expecteds = self::$expected;
		if (!self::$isCase && $thereWasWord) {
			self::$expected = null;
		}		
		if (self::$open['placeholder'] && !$thereWasWord) {
			self::$expected = array("a");
		}
		if (self::$open['fn']) {
			self::$expected = array("(");
		}
		if (!empty(self::$open['math'])) {
			self::$expected = self::$lists[self::$open['math']];
		}
		if ($isOpenQuote) {
			self::$expected = array("'");
		}
		if ($isOpenQuote2) {
			self::$expected = array('"');
		}
		if (!empty(self::$open['or'])) {
			if (self::$open['or'] == 1) {
				self::$expected = array('|');
			} else {
				self::$expected = self::$lists['|'];
			}
		}
		if (self::$open['placeholderShouldHaveDefaultValue'] && !self::$open['placeholderHasDefaultValue']) {
			self::$expected = $expecteds;
		}
		if (self::isAnyVarOpen() && !self::$open['placeholderHasName']) {
			self::$expected = array('a');
		}
		if (!empty(self::$open['bracket'])) {
			self::$expected = array(']');
		}
		if (!empty(self::$open['func']) || !empty($isOpened)) {
			self::$expected = array(')');
		}
		if (!empty(self::$open['method'])) {
			self::$expected = array('(');
		}
		if (is_array(self::$expected)) {
			self::error('unexpectedEnd', array($code, '<b>&nbsp;}</b>', self::getExpected()), true);
		}
		
		$code = $parsedCode;
		if (self::$open['placeholder']) {
			preg_match_all('/::(\w+)(.+)*/', $code, $matches);
			$name = $matches[1][0];
			$def = $matches[2][0];
			if (empty($def)) {
				$code = "{'pl':'".$name."'}";	
			} else {
				$code = "{'pl':'".$name."','p':".ltrim($def, '=')."}";
			}			
		
		} elseif (!empty($data['react'])) {
			$names = array_keys($data['react']);
			if (count($names) == 1) {
				$names = "'".$names[0]."'";
			} else {
				$names = json_encode($names);
				$code = 'function(){return'.$code.'}';
			}
			$code = "{'pr':".$names.",'p':".$code."}";
			//Printer::log($code);
		} elseif (!empty($data['let'])) {
			$lets = array();
			foreach ($data['let'] as $let) {
				$lets[] = $let['name'].'='.$let['value'];
			}
			$code = '<let>var '.implode(',', $lets).'<=let>';
		}
		$data['code'] = '<nq>'.$code.'<nq>';
		


		self::$expected = $expecteds;
		self::log();

		return $data;

	}

	private static function handleSpace() {
		if (!self::$quoted) {
			self::$varType = '';
			self::off('react2');
			self::removeExpected('[');
			
			$mathSign = self::$open['math'];
			if ($mathSign == '+' || $mathSign == '-') {
				self::$expected[] = $mathSign;
			}
			if (self::$open['number']) {
				self::removeExpected('.');
			}
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
				array_push(self::$expected, '~', '&', '@', '#', '-', '+', '!', '(');
				self::maybeAddDollar();
			}
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
			if (!self::$isLet || !empty(self::$open['func'])) {
				self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', self::$space);
				self::maybeAddDollar();
			} else {
				self::$expected = array('&', self::$space);
			}
			if (self::$isLet && empty(self::$open['func'])) {
				self::off('letvalue');
			}
		}
	}

	private static function handleDot() {
		if (!self::$quoted) {
			if (self::$open['number']) {
				self::$expected = array('0');
				self::open('decimal');
			} else {
				if (self::$prevSign != 'a' && !self::$open['react2']) {
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
			self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', self::$space);
			self::maybeAddDollar();
		}
	}

	private static function handleExclamation() {
		if (!self::$quoted) {
			self::$expected = array('a', '0', '!', '&', '~', '#', self::$space);
			self::maybeAddDollar();
		}
	}

	private static function handleLeftBracket() {
		if (!self::$quoted) {
			self::add('bracket');
			self::$expected = array('"', "'", 'a', '0', '&', '~', '!', '@', self::$space);
			self::maybeAddDollar();
			if ((self::$prevSign == 'a' || self::$prevSign == ']') && self::$varType == 'r') {
				self::on('react2');
			}
		}
	}

	private static function handleRightBracket() {
		if (!self::$quoted) {
			self::minus('bracket');
			self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', '.', self::$space);
			if (!empty(self::$open['ternary'])) {
				self::$expected[] = ':';
			}
			if (self::$varType == 'r') {
				self::on('react2');
			}
		}
	}

	private static function handleEqual() {
		if (!self::$quoted)
		{			
			self::$expected = array();
			if (self::$open['placeholder'])
			{
				self::on('placeholderShouldHaveDefaultValue');
				if (!self::$open['placeholderHasName']) {
					self::$expected = array('a', '0', '"', "'", '&', '~', '.', self::$space);
					return;
				}
			}

			self::$expected = array('a', '0', '~', '&', '.', '@', '#', '-', '!', '+', '(', self::$space);
			self::maybeAddDollar();
			if (self::$isLet) {
				self::$expected[] = self::$space;
				self::on('letvalue');
				self::off('letvarname');
				$signToAdd = '';
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
			self::$varType = '';
		}
	}

	private static function handleQuote() {
		if (!self::$open['doubleQuote']) {
			$isOpenQuote = !$isOpenQuote;
			if (self::$open['placeholderShouldHaveDefaultValue']) {
				self::on('placeholderHasDefaultValue');
			}
		}
		if ($isOpenQuote || $isOpenQuote2) {
			self::$expected = '*';
		} else {
			self::$expected = array(':', '?', '+', '&&', '||', self::$space);
			if (!empty(self::$open['bracket'])) {
				self::$expected[] = ']';
			}
		}
		if (self::$isCase && !$isOpenQuote) {
			self::$isCase = false;
			self::off('case');
			self::$expected = array('end', self::$space);
		}
	}

	private static function handleDoubleQuote() {
		
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
		self::$couldBeMathSign = !self::isOpen('letvarname');

		if (self::$isNum) {
			if (self::$decOpen) {
				self::off('decimal');
			}
			self::on('number');
		} else {
			self::off('number');
		}


		if (!self::$isNum && !self::$anyVar && !self::$isKey && (!self::$open['placeholder'] || self::$open['placeholderShouldHaveDefaultValue'])) {
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
		return self::$notTextOrComp && !self::$open['letvarname'];
	}

	private static function couldBeLeftBracket() {
		return !self::$isNum;
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
		return self::$notTextOrComp && !self::$open['letvarname'];
	}

	private static function couldBeOr() {
		return self::$notTextOrComp && !self::$open['letvarname'];
	}

	private static function couldBeMinus() {
		return self::$notTextOrComp && self::$couldBeMathSign;
	}

	private static function couldBePlus() {
		return !self::isOpen('comp') && self::$couldBeMathSign;
	}

	private static function couldBeSlash() {
		return self::$notTextOrComp && self::$couldBeMathSign;
	}

	private static function couldBePercent() {
		return self::$notTextOrComp && self::$couldBeMathSign;
	}

	private static function couldBeStar() {
		return self::$notTextOrComp && self::$couldBeMathSign;
	}

	private static function couldBeEqual() {
		return self::$isKey || self::$open['letvarname'];
	}

	private static function couldBeSpace() {
		return self::$isNum || self::$anyVar;
	}

	private static function couldBeLeftParenthesis() {
		return false;
	}

	private static function couldBeRightParenthesis() {
		return self::isOpen('parenthesis', 'func');
	}

	private static function couldBeComma() {
		return self::isOpen('func');
	}

	private static function couldBeDot() {
		return self::$objVar || (self::$isNum && !self::$decOpen);
	}

	private static function finish() {
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
				if (empty($isClosingTernary) && empty($isOpened)) {
					self::$expected[] = ',';
				}
			}
			
		}
		self::offVars();
	}

	private static function offVars() {
		self::off('react');
		self::off('var');
		self::off('data');
		self::off('text');
		self::off('comp');
		self::set('or', 0);
		self::set('math', '');
	}

	
	private static function isAnyVarOpen() {
		return self::isOpen('react', 'react2', 'text', 'comp', 'var', 'data');
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
				self::$expected = array('+', '-', '!', 'a', '.', '&', '$', '~', '@');
			break;
			case 'templateAttribute':
				self::$expected = array('+', '-', '!', 'a', '.', '&', '$', '~', '@');
			break;
			case 'componentAttribute':
				self::$expected = array('+', '-', '!', 'a', '.', '&', '$', '~', '@', '#', '@');
			break;
			case 'textNode':
				self::$expected = array('+', '-', '!', 'a', '.', ':', '&', '$', '~', '@');
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

}