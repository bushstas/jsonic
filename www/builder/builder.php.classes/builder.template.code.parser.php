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
			'react' => array()
		);
				
		$isStart = true;
		$isOpenFunction = 0;
		$isOpenMethod = 0;
		$isOpenBracket = 0;
		$isOpenTernary = 0;

		$code = '';
		$parsedCode = '';
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

						case self::$space:
							if (!$quoted) {
								$varType = '';
								$isReactive = false;
							}
						break;

						case '|':
							if (!$quoted) {
								if ($prevSign == '|') {
									$orOpen = 2;
									self::$expected = self::$lists['|'];
									array_push(self::$expected, '0', 'a', '~', '&', '#', '@', '-', '!', self::$space);
									if (!$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else {
									$orOpen = 1;
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
									if ($isLet) {
										self::$expected[] = self::$space;
									}
								}
							}
						break;

						case '$':
							if (!$quoted) {
								$isReactive = false;
								$isReactiveOpen = true;
								$reactName = '';
								self::$expected = array('a');
								$varType = 'r';
							}
						break;

						case ',':
							if (!$quoted) {
								if (!$isLet || !empty($isOpenFunction)) {
									self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', self::$space);
									if (!$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else {
									self::$expected = array('&', self::$space);
								}
							}
						break;

						case '.':
							if (!$quoted) {
								if ($prevSign != 'a' && !$isReactive) {
									$isOpenMethod++;
									$varType = '';
								} else {
									switch ($varType) {
										case 'r':
											$isReactive = true;
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

						case "?":
							if (!$quoted) {
								$isOpenTernary++;
								self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', self::$space);
								if (!$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case "!":
							if (!$quoted) {
								self::$expected = array('a', '0', '!', '&', '~', '#', self::$space);
								if (!$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case "[":
							if (!$quoted) {
								$isOpenBracket++;
								self::$expected = array('"', "'", 'a', '0', '&', '~', '!', '@', self::$space);
								if (!$isLet && !$isCase) {
									self::$expected[] = '$';
								}
								if (($prevSign == 'a' || $prevSign == ']') && $varType == 'r') {
									$isReactive = true;
								}
							}
						break;

						case "]":
							if (!$quoted) {
								if ($isOpenBracket > 0) {
									$isOpenBracket--;
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', '.', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
								if ($varType == 'r') {
									$isReactive = true;
								}
							}
						break;

						case '+':
						case '-':
						case '*':
						case '/':
							if (!$quoted) {
								self::$expected = self::$lists[$sign];
								if (!$isPlaceholderOpen && !$isLet && !$isCase) {
									self::$expected[] = '$';
								}
								self::$expected[] = self::$space;
								$isMathOpen = $sign;
								$varType = '';
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
								$varType = 't';
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
								if ($prevSign == '&') {
									$orOpen = 2;
									$isVarOpen = false;
									array_push(self::$expected, '0', '~', '#', '-', '!',self::$space);
									if (!$isLet && !$isCase) {
										self::$expected[] = '$';
									}
								} else if ($thereWasWord) {
									self::$expected[] = '&';
								}
								if ($isLet && empty($letVarName)) {
									self::$expected = array('a');
								}
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
								self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', ')', self::$space);
								if (!$isLet && !$isCase) {
									self::$expected[] = '$';
								}
							}
						break;

						case ")":
							if (!$quoted) {
								if ($isOpenFunction > 0) {
									$isOpenFunction--;
								}
								self::$expected = array('&&', '||', '-', '+', '?', '/', '*', '%', '[', ',', self::$space);
								if (!empty($isOpenTernary)) {
									self::$expected[] = ':';
								}
							}
						break;
					}
					if ($sign == self::$space) {
						$code .= ' ';
						$parsedCode .= ' ';
						continue;
					} 
					$prevSign = $sign;
					if ($isReactive) {

					} else {
						//Printer::log($code);
						$parsedCode .= $sign;
						$code .= $sign;
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
						$parsedCode = $parsedCode.".g('".$parsedCode."')";
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

					if ($isLet && empty($letVarName)) {
						if ($isNum || in_array($part, self::$keywords)) {
							self::$expected = array('a');
							self::error('unexpectedLat', array($code, $part, ' ...}', self::getExpected()));
						}
						$letVarName = $part;
					}
					
					if ($isStart) {
						switch ($part) {
							case 'let':
								$isLet = true;
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
						if ($isCase || $isDefault || $isLet) continue;
					}
					if ($quoted) {
						self::$expected = '*';
					} elseif ($isPlaceholderOpen && !$isPlaceholderDefaultOpen) {
						self::$expected = array('=', 'end', self::$space);
					} elseif (!empty($isOpenMethod)) {
						self::$expected = array('(');
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
						self::$expected = array('+', 'end');


						if (!$isTextOpen && !$isCompOpen) {
							array_push(self::$expected, '?', '[', '-', '/', '*', '=', '%', '&&', '||', self::$space);
						}

						if (!empty($isOpenTernary)) {
							self::$expected[] = ':';
						}
						
						if (!empty($isOpenFunction)) {
							self::$expected[] = ')';
							self::$expected[] = ',';
						}
						if (!$isVarOpen && !$isTextOpen && !$isDataOpen && !$isReactiveOpen && !$isReactive && !$isNum && !in_array($part, self::$keywords)) {
							$isFuncExpected = true;
							self::$expected = array('(');
						}
						
						if ($isOpenBracket) {
							self::$expected[] = ']';
						}
						
						if (!$isNum && ($isVarOpen || $isReactiveOpen || $isReactive)) {
							self::$expected[] = '.';
						}
					}
					if ($isReactiveOpen || $isReactive) {
						if (empty($reactName)) {
							$data['react'][$part] = array();
							$reactName = $part;
						} else {
							$data['react'][$reactName][] = $part;
						}
					} else {
						$reactName = '';
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
		if (!empty($isOpenFunction)) {
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
		}
		$data['code'] = '<nq>'.$code.'<nq>';
		return $data;

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