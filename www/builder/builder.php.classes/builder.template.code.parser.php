<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className;
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

	private static $lists = array(
		'|' => array('.', 'a', '0', '~', '$', '@', '#', '&', '-'),
		'+' => array('a', '0', '~', '&', '$', '@', '.'),
		'-' => array('a', '0', '.', '~', '&', '$'),
		'*' => array('a', '0', '.', '~', '&', '$', '-'),
		'/' => array('a', '0', '.', '~', '&', '$', '-')
	);

	private static $operators = array(
		'case', 'switch', 'if', 'foreach', 'let', 'default'
	);

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
				
		$isStart = true;
		$isOpenFunction = 0;
		$isOpenMethod = 0;
		$isOpenBracket = 0;

		$code = '';
		$prevSign = '';
		for ($i = 0; $i < count($parts); $i++) {
			$part = $parts[$i];
			if ($part === '') continue;
			if (!preg_match('/[\wа-я]/si', $part)) {
				for ($j = 0; $j < strlen($part); $j++) {
					$sign = $part[$j];					
					if (!self::isSignExpected($sign)) {
						if ($sign == self::$space) $sign = '&nbsp;';
						self::error('unexpectedSign', array($code, $sign, ' ...}', self::getExpected()));
					}
					$quoted = self::$expected == '*';
					switch ($sign) {

						case '|':
							if (!$quoted) {
								if ($prevSign == '|') {
									$orOpen = 2;
									self::$expected = self::$lists['|'];
									self::$expected[] = self::$space;
								} else {
									$orOpen = 1;
									self::$expected = array('|');
								}
							}
						break;

						case ':':
							if (!$quoted) {
								self::$expected = array('a', '0');
								if (!$isOpenTernary) {
									self::$expected[] = ':';
								} else {

								}
								if ($prevSign == ':') {
									$isPlaceholderOpen = true;
								}
							}
						break;

						case '=':
							if (!$quoted) {
								if ($isPlaceholderOpen) {
									$isPlaceholderDefaultOpen = true;
									self::$expected = array('a', '0', '"', "'", '&', '~', '.', self::$space);
								} else {
									self::$expected = array('a', '0');
								}
							}
						break;

						case '$':
							if (!$quoted) {
								$isReactiveOpen = true;
								self::$expected = array('a');
								$varType = 'r';
							}
						break;

						case ',':
							if (!$quoted) {
								self::$expected = array('"', "'", 'a', '+', '-', '!', '&', '$', '~', '@', '#', '.', self::$space);
							}
						break;

						case '.':
							if (!$quoted) {
								if ($prevSign != 'a') {
									$isOpenMethod++;
								} else {
									switch ($varType) {
										case 'r':
											$isReactiveOpen = true;
										break;
										case 'a':
										case 'l':
											$isVarOpen = true;
										break;
										case 'd':
											$isDataOpen = true;
										break;
									}
								}
								self::$expected = array('a');
							}
						break;

						case '+':
						case '-':
						case '*':
						case '/':
							if (!$quoted) {
								self::$expected = self::$lists[$sign];
								self::$expected[] = self::$space;
								$isMathOpen = $sign;
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
									self::$expected = array('end', self::$space);
								}
						break;

						case "~":
							if (!$quoted) {
								self::$expected = array('a');
								$isVarOpen = true;
								$varType = 'a';
							}
						break;

						case "@":
							if (!$quoted) {
								self::$expected = array('a');
								$isTextOpen = true;
							}
						break;

						case "#":
							if (!$quoted) {
								self::$expected = array('a');
								$isDataOpen = true;
								$varType = 'd';
							}
						break;

						case "&":
							if (!$quoted) {
								self::$expected = array('a');
								$isVarOpen = true;
								$varType = 'l';
							}
						break;

						case "(":
							if (!$quoted) {
								$isFuncExpected = false;
								$isOpenFunction++;
								if ($isOpenMethod > 0) {
									$isOpenMethod--;
								}
								self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '$', '~', '@', '#', '.', ')', self::$space);
							}
						break;

						case "[":
							if (!$quoted) {
								$isOpenBracket++;
								self::$expected = array('"', "'", 'a', '0', '&', '$', '~', '@', self::$space);
							}
						break;

						case "]":
							if (!$quoted) {
								if ($isOpenBracket > 0) {
									$isOpenBracket--;
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
							}
						break;

						case ")":
							if (!$quoted) {
								if ($isOpenFunction > 0) {
									$isOpenFunction--;
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
							}
						break;
					}
					$prevSign = $sign;
					$code .= $sign == self::$space ? ' ' : $sign;
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
				if ($isLatin) {
					if (!self::isLatinTextExpected($part, $code)) {
						self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
					}

					$prevSign = !$isNum ? 'a' : '0';
					$prevCode = $code;
					$code .= $part;
					
					if ($isStart) {
						switch ($part) {
							case 'case':
								$isCase = true;
								self::$expected = array('"', "'", 'b', '0', '~', '@', '&', self::$space);
							break;
							case 'default':
								$isDefault = true;
								self::$expected = array('end', self::$space);
							break;
						}
						$isStart = false;
						if ($isCase || $isDefault) continue;
					}
					if ($quoted) {
						self::$expected = '*';
					} elseif ($isPlaceholderOpen && !$isPlaceholderDefaultOpen) {
						self::$expected = array('=', 'end', self::$space);
					} elseif (!empty($isOpenMethod)) {
						self::$expected = array('(');
					} else {
						if ($isCase) {
							if ($isNum || $part == 'false' || $part == 'true' || $part == 'null' || $part == 'undefined') {
								self::$expected = array('end');
								$isCase = false;
								continue;
							} else {
								self::error('unexpectedLat', array($prevCode, $part, ' ...}', self::getExpected()));
							}
						}
						self::$expected = array('+', 'end');


						if (!$isTextOpen && !$isCompOpen) {
							array_push(self::$expected, '?', '[', '-', '/', '*', '=', '%', '&&', '||');
						}
						
						if (!empty($isOpenFunction)) {
							self::$expected[] = ')';
							self::$expected[] = ',';
						} else {
							self::$expected[] = self::$space;
							if (!$isVarOpen && !$isTextOpen && !$isDataOpen && !$isReactiveOpen && !$isNum) {
								$isFuncExpected = true;
								self::$expected = array('(');
							}
						}
						
						if ($isOpenBracket) {
							self::$expected[] = ']';
						}
						
						if (!$isNum && ($isVarOpen || $isReactiveOpen)) {
							self::$expected[] = '.';
						}
					}
					$isReactiveOpen = false;
					$isVarOpen = false;
					$isTextOpen = false;
					$isDataOpen = false;
					$orOpen = 0;
					$isMathOpen = '';

				






				} elseif (self::$expected != '*') {
					self::error('unexpectedCyr', array($code, $part, ' ...}', self::getExpected()));
				} else {
					$prevSign = 'a';
					$code .= $part;
				}
			}
			$isStart = false;
		}













		if (!$isCase) {
			self::$expected = null;
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
		if (!empty($isOpenFunction)) {
			self::$expected = array(')');
		}
		if (!empty($isOpenMethod)) {
			self::$expected = array('(');
		}

		if (is_array(self::$expected)) {
			self::error('unexpectedEnd', array($code, '<b>&nbsp;}</b>', self::getExpected()), true);
		}
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