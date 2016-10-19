<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className, $globalNames;
	private static $expected = array();
	private static $space = '¦';
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
		'+' => array('a', '0', '~', '&', '@', '.'),
		'-' => array('a', '0', '.', '~', '&'),
		'*' => array('a', '0', '.', '~', '&', '-'),
		'/' => array('a', '0', '.', '~', '&', '$', '-')
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

		$isStart = true;
		$isOpenFunction = 0;
		$isOpenMethod = 0;
		$isOpenBracket = 0;
		$isOpenTernary = 0;
		$isOpened = 0;
		$isClosingTernary = 0;

		$code = '';
		$parsedCode = '';
		$prevSign = '';
		for ($i = 0; $i < count($parts); $i++) {
			$part = $parts[$i];
			if ($part === '') continue;
			if (!preg_match('/[\wа-я]/si', $part)) {
				for ($j = 0; $j < strlen($part); $j++) {
					$sign = $part[$j];
					$nextSign = $part[$j + 1];
					$signToAdd = $sign;

					if (empty($nextSign)) {
						$nextSign = $parts[$i + 1][0];
					}
					if (!self::isSignExpected($sign)) {
						if ($sign == self::$space) $sign = '&nbsp;';
						self::error('unexpectedSign', array($code, $sign, ' ...}', self::getExpected()));
					}
					$quoted = self::$expected == '*';
					switch ($sign) {

						case self::$space:
							if (!$quoted) {
								self::$varType = '';
								$isReactive = false;
								self::off('react2');
							}
						break;

						case '|':
							if (!$quoted) {
								if ($prevSign == '|') {
									$orOpen = 2;
									self::set('or', 2);
									self::$expected = self::$lists['|'];
									array_push(self::$expected, '0', 'a', '~', '&', '#', '@', '-', '!', self::$space);
									if (!self::$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else {
									$orOpen = 1;
									self::set('or', 1);
									self::$expected = array('|');
								}
							}
						break;

						case ':':
							if (!$quoted) {
								self::$expected = array('a', '0', self::$space);
								if (!$isOpenTernary) {
									self::$expected[] = ':';
								} else {
									$isClosingTernary++;
									self::add('ternary2');
									array_push(self::$expected, '~', '&', '@', '#', '-', '+', '!', '(');
									if (!self::$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								}
								if ($prevSign == ':') {
									self::on('placeholder');
									$isPlaceholderOpen = true;
								}
							}
						break;

						case '=':
							if (!$quoted) {
								if ($isPlaceholderOpen) {
									$isPlaceholderDefaultOpen = true;
									self::on('placeholder2');
									self::$expected = array('a', '0', '"', "'", '&', '~', '.', self::$space);
								} else {
									self::$expected = array('a', '0', '~', '&', '$', '.', '@', '#', '-', '!', '+', '(');
									if (self::$isLet) {
										self::$expected[] = self::$space;
										self::$isLetValueExpected = true;
										self::on('letvalue');
										$signToAdd = '';
									}
								}
							}
						break;

						case '$':
							if (!$quoted) {
								$isReactive = false;
								$isReactiveOpen = true;
								self::off('react2');
								self::on('react');
								$reactName = '';
								self::$reactName = '';
								self::$expected = array('a');
								self::$varType = 'r';
							}
						break;

						case ',':
							if (!$quoted) {
								if (!self::$isLet || !empty($isOpenFunction)) {
									self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', self::$space);
									if (!self::$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else {
									self::$expected = array('&', self::$space);
								}
								if (self::$isLet && empty($isOpenFunction)) {
									self::$isLetValueExpected = false;
									self::off('letvalue');
								}
							}
						break;

						case '.':
							if (!$quoted) {
								if (!empty($isDecimalOpen)) {
									self::$expected = array('0');
								} else {
									if ($prevSign != 'a' && !$isReactive) {
										$isOpenMethod++;
										self::$varType = '';
									} else {
										switch (self::$varType) {
											case 'r':
												$isReactive = true;
												self::on('react2');
											break;
											case 'a':
											case 'l':
												$isVarOpen = true;
												self::on('var');
											break;
											case 'd':
												$isDataOpen = true;
												self::on('data');
											break;
										}
									}
									self::$expected = array('a');
								}
							}
						break;

						case "?":
							if (!$quoted) {
								$isOpenTernary++;
								self::add('ternary');
								self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', self::$space);
								if (!self::$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case "!":
							if (!$quoted) {
								self::$expected = array('a', '0', '!', '&', '~', '#', self::$space);
								if (!self::$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case "[":
							if (!$quoted) {
								$isOpenBracket++;
								self::add('bracket');
								self::$expected = array('"', "'", 'a', '0', '&', '~', '!', '@', self::$space);
								if (!self::$isLet && !$isCase) {
									self::$expected[] = '$';
								}
								if (($prevSign == 'a' || $prevSign == ']') && self::$varType == 'r') {
									$isReactive = true;
									self::on('react2');
								}
							}
						break;

						case "]":
							if (!$quoted) {
								if ($isOpenBracket > 0) {
									$isOpenBracket--;
									self::minus('bracket');
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', '.', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
								if (self::$varType == 'r') {
									$isReactive = true;
									self::on('react2');
								}
							}
						break;

						case '+':
						case '-':
						case '*':
						case '/':
							if (!$quoted) {
								self::$expected = self::$lists[$sign];
								if (!$isPlaceholderOpen && !self::$isLet && !$isCase) {
									self::$expected[] = '$';
								}
								self::$expected[] = self::$space;
								$isMathOpen = $sign;
								self::set('math', $sign);
								self::$varType = '';
							}
						break;
						
						case '"':
								if (!$isOpenQuote) {
									$isOpenQuote2 = !$isOpenQuote2;
								}
								if ($isOpenQuote2 || $isOpenQuote) {
									self::$expected = '*';
								} else {
									self::$expected = array(':', '?', '+', '&&', '||', self::$space);
									if ($isOpenBracket > 0) {
										self::$expected[] = ']';
									}
								}
								if ($isCase && !$isOpenQuote2) {
									$isCase = false;
									self::off('case');
									self::$expected = array('end', self::$space);
								}
						break;

						case "'":
								if (!$isOpenQuote2) {
									$isOpenQuote = !$isOpenQuote;
								}
								if ($isOpenQuote || $isOpenQuote2) {
									self::$expected = '*';
								} else {
									self::$expected = array(':', '?', '+', '&&', '||', self::$space);
									if ($isOpenBracket > 0) {
										self::$expected[] = ']';
									}
								}
								if ($isCase && !$isOpenQuote) {
									$isCase = false;
									self::off('case');
									self::$expected = array('end', self::$space);
								}
						break;

						case "~":
							if (!$quoted) {
								self::$expected = array('a');
								$isVarOpen = true;
								self::on('var');
								self::$varType = 'a';
							}
						break;

						case "@":
							if (!$quoted) {
								self::$expected = array('a');
								$isTextOpen = true;
								self::on('text');
								self::$varType = 't';
							}
						break;

						case "#":
							if (!$quoted) {
								self::$expected = array('a');
								self::on('data');
								$isDataOpen = true;
								self::$varType = 'd';
							}
						break;

						case "&":
							if (!$quoted) {								
								self::$expected = array('a');
								$isVarOpen = true;
								self::on('var');
								if ($prevSign == '&') {
									$orOpen = 2;
									self::set('or', 2);
									$isVarOpen = false;
									self::off('var');
									array_push(self::$expected, '0', '~', '#', '-', '!',self::$space);
									if (!self::$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else if ($thereWasWord) {
									self::$expected[] = '&';
								}
								if (self::$isLet && empty($letVarName)) {
									self::$expected = array('a');
								}
								self::$varType = 'l';
								if (in_array('&&', self::$expected)) {
									if ($nextSign != '&') {
										self::$expected = array('&');
										$code .= '&';
										self::error('unexpectedSign', array($code, $nextSign, ' ...}', self::getExpected()));
									}
								} elseif ($nextSign == '&') {
									$code .= '&';
									self::$expected = array('a');
									self::error('unexpectedSign', array($code, $nextSign, ' ...}', self::getExpected()));
								}
							}
						break;

						case "(":
							if (!$quoted) {
								if ($prevSign == 'a') {
									$isFuncExpected = false;
									self::off('fn');
									$isOpenFunction++;
									self::add('func');
									if ($isOpenMethod > 0) {
										self::minus('method');
										$isOpenMethod--;
									}
								} else {
									$isOpened++;
									self::add('parenthesis');
								}
								self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', ')', self::$space);
								if (!self::$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case ")":
							if (!$quoted) {
								if ($isOpened > 0) {
									$isOpened--;
									self::minus('parenthesis');
								} elseif ($isOpenFunction > 0) {
									$isOpenFunction--;
									self::minus('func');
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', ',', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
							}
						break;
					}
					if ($sign == self::$space) {
						$sign = ' ';
						$signToAdd = ' ';
					} else {
						$prevSign = $sign;
					}
					if (!$isReactive) {
						$parsedCode .= $sign;
						$code .= $sign;
					}

					if (self::$isLet && self::$isLetValueExpected) {
						$data['let'][count($data['let']) - 1]['value'] .= $signToAdd;						
					}
				}
			}
			else
			{
				if ($prevSign == ':' && !$isPlaceholderOpen && !$isOpenTernary) {
					self::$expected = array(':', self::$space);
					self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
				}
				$quoted = $isOpenQuote || $isOpenQuote2;
				$withoutCyr = preg_replace('/[^\w]/usi', '', $part);
				$isLatin = $part == $withoutCyr;
				
				$isNum = is_numeric($part);
				self::$isNum = $isNum;
				
				if ($isLatin) {
					if (!self::isLatinTextExpected($part, $code)) {
						self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
					}

					$prevSign = !$isNum ? 'a' : '0';
					$prevCode = $code;
					
					if ($isOpenMethod) {
						$parsedCode = rtrim($parsedCode, '.')."$.".$part;
					} elseif ($isReactive) {
						$parsedCode = rtrim($parsedCode, ')').",'".$part."')";
					} elseif ($isReactiveOpen) {
						$parsedCode = $parsedCode.".g('".$part."')";
					} elseif ($isVarOpen) {
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

					if (self::$isLet && empty($letVarName)) {
						if ($isNum || in_array($part, self::$keywords)) {
							self::$expected = array('a');
							self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
						}
						$letVarName = $part;
					}
					
					if ($isStart) {
						switch ($part) {
							case 'let':
								self::$isLet = true;
								self::$isLet = true;
								self::$expected = array('&', self::$space);
							break;
							case 'case':
								$isCase = true;
								self::$expected = array('"', "'", 'b', '0', '~', '@', '&', self::$space);
							break;
							case 'default':
								$isDefault = true;
								$thereWasWord = true;
								self::$expected = array('end', self::$space);
							break;
						}
						$isStart = false;
						if ($isCase || $isDefault || self::$isLet) continue;
					}
					if ($quoted) {
						self::$expected = '*';
					} else {

						if ($isCase) {
							if ($isNum || in_array($part, self::$keywords)) {
								self::$expected = array('end');
								$isCase = false;
								continue;
							} else {
								self::error('unexpectedLat', array($prevCode, $part, ' ...}', self::getExpected()));
							}
						}
						
						if (self::$prepare($part)) {

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
							if (self::couldBePoint()) {
								self::$expected[] = '.';
							}
						}					
					
						self::finish();					
					}
				} elseif (self::$expected != '*') {
					self::error('unexpectedCyr', array($code, $part, ' ...}', self::getExpected()));
				} else {
					$prevSign = 'a';
					$code .= $part;
				}
				$thereWasWord = true;
			}
			$isStart = false;
		}





		if (!$isCase && $thereWasWord) {
			self::$expected = null;
		}

		
		if ($isPlaceholderOpen && !$thereWasWord) {
			self::$expected = array("a");
		}
		if ($isFuncExpected) {
			self::$expected = array("(");
		}
		if (!empty($isMathOpen)) {
			self::$expected = self::$lists[$isMathOpen];
		}
		if ($isOpenQuote) {
			self::$expected = array("'");
		}
		if ($isOpenQuote2) {
			self::$expected = array('"');
		}
		if (!empty($orOpen)) {
			if ($orOpen == 1) {
				self::$expected = array('|');
			} else {
				self::$expected = self::$lists['|'];
			}
		}
		if ($isVarOpen || $isReactiveOpen || $isTextOpen || $isDataOpen || $isCompOpen) {
			self::$expected = array('a');
		}
		if (!empty($isOpenBracket)) {
			self::$expected = array(']');
		}
		if (!empty($isOpenFunction) || !empty($isOpened)) {
			self::$expected = array(')');
		}
		if (!empty($isOpenMethod)) {
			self::$expected = array('(');
		}
		if (is_array(self::$expected)) {
			self::error('unexpectedEnd', array($code, '<b>&nbsp;}</b>', self::getExpected()), true);
		}
		
		$code = $parsedCode;
		if (!empty($isPlaceholderOpen)) {
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
		return $data;

	}

	private static function prepare($part) {
		self::$currentPart = $part;
		self::$expected = array();
		
		self::$isReact = self::isOpen('react', 'react2');
		self::$notTextOrComp = !self::isOpen('text', 'comp');
		self::$isKey = self::isKeyword();
		self::$anyVar = self::isAnyVarOpen();
		self::$objVar = self::$anyVar && !self::$notTextOrComp;
		self::$decOpen = self::isOpen('decimal');

		if (self::$isNum) {
			if (!self::$decOpen) {
				self::on('decimal');
			} else {
				self::off('decimal');
			}
		}

		if (!self::$anyVar && !self::$isKey) {
			self::on('fn');
			self::$expected = array('(');			
			return false;
		}
		if (self::isOpen('placeholder') && !self::isOpen('placeholder2')) {
			self::$expected = array('=', 'end', self::$space);
			return false;
		} 
		if (self::$isOpen('method')) {
			self::$expected = array('(');
			return false;
		}
		return true;
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
		return self::$notTextOrComp;
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
		return self::$notTextOrComp;
	}

	private static function couldBeOr() {
		return self::$notTextOrComp;
	}

	private static function couldBeMinus() {
		return self::$notTextOrComp;
	}

	private static function couldBePlus() {
		return !self::isOpen('comp');
	}

	private static function couldBeSlash() {
		return self::$notTextOrComp;
	}

	private static function couldBePercent() {
		return self::$notTextOrComp;
	}

	private static function couldBeStar() {
		return self::$notTextOrComp;
	}

	private static function couldBeEqual() {
		return self::$isKey;
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

	private static function couldBePoint() {
		return self::$objVar || (self::$isNum && !self::$decOpen);
	}

	private static function finish() {
		if (self::$isReact) {
			if (empty(self::$reactName)) {
				self::$data['react'][self::$currentPart] = array();
				self::$reactName = self::$currentPart;
			} else {
				self::$data['react'][$reactName][] = self::$currentPart;
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
				self::$data['let'][$idx]['value'] .= self::$currentPartt;
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
		return self::isOpen('react', 'react2', 'text', 'comp', 'var', 'data', 'placeholder', 'placeholder2');
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
			$items[] = self::$signs[$exp];
		}
		return implode("\n", $items);
	}

	private static function error($name, $vars) {
		$err = 'Ошибка в парсинге кода в шаблоне <b>'.self::$templateName.'</b> класса <b>'.self::$className.'</b><br><br>Код в котором произошла ошибка: {'.self::$code.'}<br><br>';
		new Error($err.self::$errors[$name], $vars);
	}

}