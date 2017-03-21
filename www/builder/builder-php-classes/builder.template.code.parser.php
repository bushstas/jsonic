<?php

class TemplateCodeParser
{	
	private static $code, $templateName, $className, $globalNames, $reservedNames,
				   $utilsFuncs;
	private static $expected = array();
	private static $expectedKeywords = array();
	private static $ternaries = array();
	private static $queue = array();
	private static $space = '¦';
	private static $data, $open, $isNum, $isLet, $varType, $currentPart, $isReact,
				   $notTextOrComp, $isKey, $anyVar, $objVar, $decOpen, $reactName,
				   $prevSign, $isCase, $notLetValue, $isStart, $quoted, $isSwitch,
				   $thereWasWord, $place, $parsedCode, $element, $context, $nextPart,
				   $isForeach, $foreach, $foreachKeyword;


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
		'limit' => 'ключевое слово limit',
		'while' => 'ключевое слово while',
		'var' => 'имя переменной',
		'method' => 'название метода класса',
		'comp' => 'идентификатор дочернего компонента',
		'global' => 'имя переменной State'
	);

	private static $errors = array(
		'unexpectedSign' => 'Неожиданный символ: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedCyr' => 'Неожиданный кириллический текст: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedLat' => 'Неожиданный текст: {{?}{??}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'unexpectedEnd' => 'Неожиданное окончание выражения: {{?}{?}<br><br>Ожидается:<xmp>{?}</xmp>',
		'usingReservedName' => 'Использование зарезервированного локально имени переменной {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: {{??}}',
		'capitalFirstLetter' => 'Использование вызова функции начинающейся с заглавной буквы в шаблоне {??} класса {??}<br><br>Имена функций утилит должны начинаться с маленькой буквы<br><br>Код в котором произошла ошибка: {{??}}',
		'usingGlobalName' => 'Использование зарезервированного глобально имени переменной {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: {{??}}',
		'usingUnknownFunc' => 'Использование функции {??} в шаблоне {??} класса {??}. Данная функция не найдена в утилитах<br><br>Код в котором произошла ошибка: <xmp>{{?}}</xmp>',
		'fewOuterTernaries' => 'Обнаружено несколько конфликтующих тернерных операций в шаблоне {??} класса {??}<br><br>Используйте скобки для их группировки<br><br>Код в котором произошла ошибка: {{??}}',
		'globalVarAsCompAttr' => 'Обнаружена попытка передать класс State дочернему компоненту в шаблоне {??} класса {??}<br>Данный класс является синглтоном и не нуждается в передаче по ссылке<br><br>Код в котором произошла ошибка: {{??}}<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>',
		'globalVarAsTemplAttr' => 'Обнаружена попытка передать класс State дочернему шаблону в шаблоне {??} класса {??}<br>Данный класс является синглтоном и не нуждается в передаче по ссылке<br><br>Код в котором произошла ошибка: {{??}}<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>',
		'thisKeyword' => 'Обнаружено использование ключевого слова <b>this</b> в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: {{??}}',
		'caseOutOfSwitchContext' => 'Обнаружен оператор {??} вне операторов <b>switch</b> или <b>if</b> в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{{?}}</xmp>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>',
		'operatorInAppropPlace' => 'Обнаружен оператор {??} в ненадлежащем месте в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>',
		'doubleForeachKeyword' => 'Обнаружено более одного ключевого слова в операторе <b>foreach</b> в шаблоне {??} класса {??}<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>',
		'improperCallback' => 'Обнаружен вызов неподходящего для этого метода {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'improperPlaceForTempl' => "Обнаружена попытка передать ссылку на шаблон {??} в неподходящем месте в шаблоне {??} класса {??}<br>Ссылки на шаблоны можно передавать только дочерним компонентам<xmp>{template .main}\n   <component class=\"Component\" tpl=\"{%item}\">\n{/template}\n\n{template .item}\n   <div></div>\n{/template}</xmp>",
		'improperTemplMain' => "Обнаружена попытка передать ссылку на шаблон {??} в шаблоне {??} класса {??}<br>Передача ссылок на главные шаблоны запрещена<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>",
		'improperTempl' =>  "Обнаружена попытка передать ссылку на шаблон {??} в этом же шаблоне класса {??}<br>Такое действие вызовет бесконечный вызов функции шаблона<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>",
		'incorrectClassType' => "Обнаружена попытка передать ссылку на класс {??} с типом {??} в шаблоне {??} класса {??}<br>Для передачи классов по ссылке доступны типы:<xmp>component\ncontrol\nmenu\nform</xmp><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>",
		'unknownClass' => "Обнаружена попытка передать ссылку на неподходящий или несуществуюший класс {??} в шаблоне {??} класса {??}<br>Для передачи классов по ссылке доступны типы:<xmp>component\ncontrol\nmenu\nform</xmp><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>",
		'improperClass' => "Обнаружена попытка передать ссылку на класс {??} в шаблоне {??} того же класса<br>Класс не может передать сам себя по ссылке<br><br>Элeмент в котором произошла ошибка: <xmp>{?}</xmp>"
	);


	private static $keywords = array(
		'false', 'true', 'null', 'undefined', 'NaN', 'Infinity'
	);

	private static $lists = array(
		'|' => array('.', 'a', '0', '~', '@', '#', '&', '-', '"', "'", '+'),
		'+' => array('a', '0', '~', '&', '@', '.', '-', '(', '"', "'"),
		'-' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'*' => array('a', '0', '.', '~', '&', '-', '(', '+'),
		'/' => array('a', '0', '.', '~', '&', '$', '-', '(', '+'),
		'%' => array('a', '0', '.', '~', '&', '$', '-', '(', '+')
	);

	private static $operators = array(
		'case', 'switch', 'if', 'foreach', 'let', 'default'
	);

	//logging
	private static $logging = false;
	//private static $logging = true;

	public static function setContext($context) {
		self::$context = $context;
	}

	public static function setGlobalNames($names, $reserved, $utilsFuncs, $userUtilsFuncs) {
		self::$globalNames = $names;
		self::$reservedNames = $reserved;
		self::$utilsFuncs = array_merge($utilsFuncs, $userUtilsFuncs);
	}

	public static function init($templateName, $className) {
		self::$templateName = $templateName;
		self::$className = $className;
		TemplateCallbackValidator::init();
	}

	private static function initiate(&$code, $place, $element) {
		self::defineExpected($place);
		$code = str_replace('_#_MORE_#_', '>', $code);
		$code = trim($code);
		self::$code = $code;
		self::$place = $place;
		self::$element = $element;
		$code = preg_replace('/\s+/', self::$space, $code);
		self::$data = array(
			'react' => array(),
			'global' => array(),
			'let' => array(),
			'callbacks' => array()
		);
		self::$open = array();
		self::$isStart = true;
		self::$prevSign = '';		

		self::$isLet = false;
		self::$isCase = false;
		self::$isSwitch = false;
		self::$isForeach = false;
		self::$quoted = false;
		self::$foreachKeyword = false;
		self::$expectedKeywords = array();
	}

	public static function parse($code, $place, $element = null) {
		self::initiate($code, $place, $element);
		$parts = preg_split('/\b/', $code);		

		$code = '';
		self::$parsedCode = '';		
		for ($i = 0; $i < count($parts); $i++) {
			$part = $parts[$i];
			self::$nextPart = $parts[$i + 1];
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
						case '%'           : self::handleMathSign($sign); break;
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
					if (self::$open['reactAdded']) {
						if ($sign != '.') {
							self::$parsedCode .= $sign;	
						}
					} else {
						self::$parsedCode .= $sign;
					}
					$code .= $sign;
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
				
					//adding
					if (!empty(self::$open['method']))
					{
						self::$parsedCode = rtrim(self::$parsedCode, '.')."$.".$part;
					}
					elseif (self::$open['react'])
					{
						self::$parsedCode = self::$parsedCode.".g('".$part."')";
					}
					elseif (self::$open['global'])
					{
						self::$parsedCode = rtrim(self::$parsedCode, ':').".a('".$part."')";
					}
					elseif (self::$open['var'])
					{
						$sign = self::$parsedCode[strlen(self::$parsedCode) - 1];
						if ($sign == '~') {
							self::$parsedCode = rtrim(self::$parsedCode, '~')."_['".$part."']";
						} else {
							self::$parsedCode = rtrim(self::$parsedCode, '&').$part;
						}
					} elseif (self::$open['text']) {
						self::$parsedCode = rtrim(self::$parsedCode, '@').self::$globalNames['CONSTANTS'].'.'.$part;
					} elseif (self::$open['data']) {
						self::$parsedCode = rtrim(self::$parsedCode, '#').'<data>'.$part;
					} else {
						self::$parsedCode .= $part;
					}
					$code .= $part;
				
					if (self::$isStart) {
						if (preg_match('/^[A-Z]/', $part)) {
							if (in_array($part, array_keys(TemplateParser::$classes))) {
								if (!in_array(TemplateParser::$classes[$part]['type'], array('component', 'control', 'form', 'menu'))) {
									new Error(self::$errors['incorrectClassType'], array($part, TemplateParser::$classes[$part]['type'], self::$templateName, self::$className, self::$element));
								}
								if ($part == self::$className) {
									new Error(self::$errors['improperClass'], array($part, self::$templateName, self::$element));
								}
								self::$expected = array('end');
								self::$thereWasWord = true;
								continue;
							} else {
								new Error(self::$errors['unknownClass'], array($part, self::$templateName, self::$className, self::$element));
							}
						}
						self::$isStart = false;
						switch ($part) {
							case 'foreach':
								if (self::$place != 'foreach') {
									new Error(self::$errors['operatorInAppropPlace'], array('foreach', self::$templateName, self::$className, self::$code, self::$element));
								}
								self::$isForeach = true;
								self::$expected = array('a', '.', '$', '~', '&', '#', self::$space);
							break;
							case 'let':
								if (self::$place != 'let') {die(self::$place);
									new Error(self::$errors['operatorInAppropPlace'], array('let', self::$templateName, self::$className, self::$code, self::$element));
								}
								self::$isLet = true;
								self::$expected = array('&', self::$space);
							break;
							case 'switch':
								if (self::$place != 'switch') {
									new Error(self::$errors['operatorInAppropPlace'], array('switch', self::$templateName, self::$className, self::$code, self::$element));
								}
								self::$isSwitch = true;
								self::$expected = array('~', '&', '$', '.', 'a', self::$space);
							break;
							case 'case':
								if (self::$context != 'ifswitch' && self::$context != 'switch') {
									new Error(self::$errors['caseOutOfSwitchContext'], array('case', self::$templateName, self::$className, self::$code, self::$element));
								}
								self::$isCase = true;
								if (self::$place != 'ifcase') {
									self::$expected = array('"', "'", 'b', '0', '~', '@', '&', self::$space);
								} else {
									self::$expected = array('!', '"', "'", 'b', '0', '~', '@', '&', '$', self::$space);
								}
							break;
							case 'default':
								if (self::$context != 'ifswitch' && self::$context != 'switch') {
									new Error(self::$errors['caseOutOfSwitchContext'], array('default', self::$templateName, self::$className, self::$code, self::$element));
								}
								$isDefault = true;
								self::$thereWasWord = true;
								self::$expected = array('end', self::$space);
							break;
						}						
						if (self::$isCase || $isDefault || self::$isLet || self::$isSwitch || self::$isForeach) continue;
					}
					if (self::$quoted) {
						self::$expected = '*';
					} elseif (($part == 'right' || $part == 'random') && self::$isForeach) {
						if (!self::$foreachKeyword) {
							self::$foreachKeyword = true;
						} else {
							new Error(self::$errors['doubleForeachKeyword'], array(self::$templateName, self::$className, self::$code));
						}
					} elseif ($part == 'as' && self::$open['foreachArr']) {
						self::off('name');
						self::off('foreachArr');
						self::on('foreachAs');
						self::$expected = array('&', self::$space);
					} else {
						$prepared = self::prepare($part, $code);
						if ($prepared) {
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
						self::$prevSign = !$isNum ? 'a' : '0';
						$prevCode = $code;
						self::finish($prepared);
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

		self::$data['code'] = self::getParsedData(self::$parsedCode);
		
		// logging
		if (self::$logging) Printer::log(self::$data['code']);
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
			self::off('recentVar');
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
			if (self::$prevSign == '$') {
				if (self::$place == 'componentAttribute') {
					new Error(self::$errors['globalVarAsCompAttr'], array(self::$className, self::$templateName, self::$code, self::$element));
				} elseif (self::$place == 'templateAttribute') {
					new Error(self::$errors['globalVarAsTemplAttr'], array(self::$className, self::$templateName, self::$code, self::$element));
				}
				self::off('react');
				self::on('global');
				self::$expected = array('a');
			} else if (self::$prevSign == ':')
			{
				self::$expected = array('a', '0', self::$space);
				self::on('placeholder');
			} 
			else if (self::$open['ternary'])
			{
				self::add('ternary2');
				array_pop(self::$ternaries);
				array_push(self::$expected, 'a', '0', '.', '~', '&', '@', '#', '-', '+', '!', '(', '"', "'", self::$space);
				self::maybeAddDollar();
			}
			self::off('name');
			self::off('recentVar');
			self::off('number');
		}
	}

	private static function handleDollar() {
		if (!self::$quoted) {
			self::off('react2');
			self::on('react');
			self::$reactName = '';
			self::$expected = array('a', ':');
			self::$varType = 'r';
		}
	}

	private static function tryToCloseTernary() {
		$env = self::$queue[count(self::$queue) - 1];
		$cnt = self::$open[$env];
		$ternary = self::$ternaries[count(self::$ternaries) - 1];
		if (!empty($ternary) && $ternary[$env] == $cnt) {
			array_pop(self::$ternaries);
			self::$parsedCode .= ":<emptystring>";
		}
	}

	private static function handleComma() {
		if (!self::$quoted) {
			self::tryToCloseTernary();
			self::$expected = array();
			if (!self::$isLet || self::$open['func'] || self::$open['array']) {
				self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', '[', self::$space);
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
			self::off('recentVar');
			self::off('number');
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
			$ternaryIndex = self::$open['ternary'];
			self::$ternaries[] = array(
				'bracket' => (int)self::$open['bracket'],
				'parenthesis' => (int)self::$open['parenthesis'],
				'func' => (int)self::$open['func']
			);
			if (!self::$open['bracket'] && !self::$open['parenthesis'] && !self::$open['func']) {
				if (self::$open['outerTernary']) {
					new Error(self::$errors['fewOuterTernaries'], array(self::$className, self::$templateName, self::$code));
				}
				self::on('outerTernary');
			}
			self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', '"', "'", self::$space);
			self::maybeAddDollar();
			self::off('name');
			self::off('recentVar');
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
				self::$expected = array('a', '0', '&', '~', '#', '=', '-', '+', '!', '(', self::$space);
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
			} else {
				self::on('array');
				self::$expected[] = ']';
			}
		}
	}

	private static function handleRightBracket() {
		if (!self::$quoted) {
			self::tryToCloseTernary();
			self::minus('bracket');
			self::$expected = array();
			self::off('number');			
			if (!self::$open['letvarname'] && !self::$open['array']) {
				array_push(self::$expected, '[', '&&', '||', '?', '-', '+', '/', '*', '%', '>', '<', '!', '=');
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
			if (self::$open['bracket'] > 0) {
				self::$expected[] = ']';
			}
			if (self::$varType == 'r') {
				self::on('react2');
			} elseif (!empty(self::$varType)) {
				self::on('var2');
			}
			if (self::$open['array']) {
				self::off('array');
				self::off('letvalue');
				self::off('letvarname');
				array_push(self::$expected, ',', self::$space);
				self::off('number');
			} else {
				self::on('recentVar');
			}
			if (self::$isLet) {
				if (!self::$open['letvalue']) {
					array_push(self::$expected, ',', 'end');
				} elseif (self::$open['letvarname']) {
					self::$expected[] = '=';
				}
			} else {
				self::$expected[] = 'end';
			}
			if (self::isForeachContext()) {
				self::removeExpected('?');
				self::$expected = array('a', self::$space);
			}
			self::off('recentQuote');
		}
	}

	private static function handleEqual() {
		if (!self::$quoted)
		{			
			self::$expected = array();
			self::off('name');
			self::off('recentVar');
			if (self::$open['placeholder'])
			{
				self::on('placeholderShouldHaveDefaultValue');
				if (!self::$open['placeholderHasName']) {
					self::$expected = array('a', '0', '"', "'", '&', '~', '.', self::$space);
					return;
				}
			}
			if (self::$open['foreachAs']) {
				self::on('equal');
				self::$expected = array('>');
				self::$expectedKeywords = array();
			} elseif (self::$open['recentQuote'] || self::$open['functionResult'] || (self::$isLet && !self::$open['letvarname'] && self::$prevSign != '=' && self::$prevSign != '!' && !self::$open['greater'])) {
				self::$expected = array('=');
				self::off('functionResult');
				self::off('recentQuote');
			} else {
				self::$expected = array('a', '0', '~', '&', '.', '@', '#', '+', '-', '!', '(', "'", '"', '=', self::$space);
				if (!self::$open['doubleEqual'] && !self::$open['greater'] && !self::$open['notEqual']) {
					if (self::$prevSign == '!') {
						self::on('notEqual');
						self::off('equal');
					} elseif (self::$prevSign == '=') {
						if (!self::$open['doubleEqual']) {
							self::on('doubleEqual');
							self::off('equal');
						}
					} else {
						self::on('equal');
					}
				} else {
					self::removeExpected('=');
					self::off('equal');
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
			if ($sign == '%' && self::$isStart) {
				self::$expected = array('a');
				self::on('template');
				return;
			}
			if ($sign == '-' || $sign == '+') {
				self::removeExpected($sign);
			}
			self::maybeAddDollar();
			self::$expected[] = self::$space;
			self::set('math', $sign);
			self::off('functionResult');
			self::$varType = '';
			self::off('name');
			self::off('recentVar');
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
			self::offEquals();
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
			self::offEquals();
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
				self::off('recentVar');
				array_push(self::$expected, '0', '~', '#', '+', '-', '!', '(', self::$space);
				self::maybeAddDollar();
			} else if ((self::$thereWasWord && !self::$open['letvarname'] && empty(self::$open['math']) && !self::$open['foreachAs'] && !self::$open['foreachAs2'] && !self::$open['foreachWhile']) || self::$open['bracket']) {
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
			self::$expected = array('"', "'", 'a', '0', '+', '-', '!', '&', '~', '@', '#', '.', '(', '[', self::$space);
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
			self::off('var2');
		}
	}

	private static function handleRightParens() {
		if (!self::$quoted) {
			self::tryToCloseTernary();
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
			} else {
				self::on('var2');
			}
			if (self::isForeachContext()) {
				self::removeExpected('?');
				self::$expected = array('a', self::$space);
			}
			self::off('recentQuote');
		}
	}

	private static function handleGreaterSign() {
		if (!self::$quoted) {
			if (self::$open['foreachAs']) {
				self::$expected = array('&', self::$space);
				self::off('foreachAs');
				self::on('foreachAs2');
				self::off('foreachAsVar');
			} else {
				self::$expected = array('=', '+', '-', 'a', '0', '~', '&', '#', self::$space);
				self::maybeAddDollar();
				self::on('greater');
				self::off('functionResult');
				self::off('name');
				self::off('recentVar');
				self::$varType = '';
			}
		}
	}

	private static function maybeAddDollar() {
		if (!self::$open['placeholder']) {
			self::$expected[] = '$';
		}
	}

	private static function prepare($part, $code) {
		self::$currentPart = $part;		
		if ($part == 'this') {
			new Error(self::$errors['thisKeyword'], array(self::$className, self::$templateName, self::$code));
		}
		self::$expected = array();
		
		self::$isReact = self::isOpen('react', 'react2', 'global');
		self::$notTextOrComp = !self::isOpen('text', 'comp');
		self::$isKey = self::isKeyword();
		self::$anyVar = self::isAnyVarOpen();
		self::$objVar = self::$anyVar && self::$notTextOrComp;
		self::$decOpen = self::isOpen('decimal');
		self::$notLetValue = !self::$open['letvarname'];
		self::$foreach = self::isForeachContext();


		if (self::$isNum) {
			if (self::$decOpen) {
				self::off('decimal');
			}
			self::on('number');
		} else {
			self::off('number');
			if (self::$prevSign == '&' && self::$currentPart == '_') {
				new Error(self::$errors['usingReservedName'], array('_', self::$className, self::$templateName, self::$code));
			}
		}

		if (self::$open['comp'] || self::$open['template']) {
			if (self::$open['template']) {
				if (self::$place != 'componentAttribute') {
					new Error(self::$errors['improperPlaceForTempl'], array(sself::$templateName, self::$className));
				}
				if (self::$currentPart == self::$templateName) {
					new Error(self::$errors['improperTempl'], array(self::$templateName, self::$className, self::$element));
				}
				if (self::$currentPart == 'main') {
					new Error(self::$errors['improperTemplMain'], array(self::$currentPart, self::$templateName, self::$className, self::$element));
				}
			}
			self::$expected = array('end');
			return false;
		}

		$isForeach = (self::$open['foreachAs'] || self::$open['foreachAs2']) && self::$open['foreachAsVar'];
		if ($isForeach) {
			self::off('foreachAs2');
			self::off('foreachAs');
			self::off('foreachAsVar');
			if ($part == 'limit') {
				self::on('foreachLimit');
				self::$expected = array('0', '~', '$', '&', '#', '.', self::$space);
			} elseif ($part == 'while') {
				self::on('foreachWhile');
				self::$expected = array('a', '0', '~', '$', '&', '.', '!', '(', self::$space);
			}
			return false;
		}
		if (!self::$isNum && !self::$anyVar && !self::$isKey && !self::$open['comp'] && (!self::$open['placeholder'] || self::$open['placeholderShouldHaveDefaultValue'])) {
			self::on('fn');
			self::$expected = array('(');
			if (!self::$open['method']) {
				if (!empty(self::$nextPart) && self::$nextPart[0] == '(') {
					self::validateFunction($part);
				}
				return false;
			} else {
				if (!TemplateCallbackValidator::isProper(self::$currentPart)) {
					new Error(self::$errors['improperCallback'], array(self::$currentPart, self::$className, self::$templateName, !empty(self::$element) ? self::$element : self::$code));
				}
			}
		}
		if ((self::$open['foreachAs'] || self::$open['foreachAs2']) && !self::$isNum) {
			if (self::$open['foreachAs2']) {
				self::on('foreachAsVar2');	
			}
			self::on('foreachAsVar');
			return false;
		}
		if (self::isOpen('placeholder') && !self::isOpen('placeholderShouldHaveDefaultValue')) {
			self::$expected = array('=', 'end', self::$space);
			return false;
		} 
		if (self::isOpen('method')) {
			self::$data['callbacks'][] = $part;
			self::$expected = array('(');
			return false;
		}
		return true;
	}

	private static function isForeachContext() {
		return self::$isForeach && empty(self::$open['bracket']) && empty(self::$open['func']) && empty(self::$open['array']) && empty(self::$open['method']);
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
		if (in_array($key, array('func', 'bracket', 'parenthesis'))) {
			self::$queue[] = $key;
		}
		if (!is_int(self::$open[$key])) {
			self::$open[$key] = 0;
		}
		self::$open[$key]++;
	}

	private static function minus($key) {
		if (in_array($key, array('func', 'bracket', 'parenthesis'))) {
			array_pop(self::$queue);
		}
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
		return (!self::$open['comp'] && !self::$open['text'] && !self::$open['letvarname'] && !self::$foreach) || self::$open['bracket'] || self::$isKey;
	}

	private static function couldBeExclamation() {
		return (self::$notTextOrComp && !self::$open['letvarname']) || self::$open['bracket'];
	}

	private static function couldBeLeftBracket() {
		return !self::$isNum && !self::$isKey && !self::$open['text'] && (!self::$open['letvarname'] || self::$open['array']);
	}

	private static function couldBeRightBracket() {
		return self::isOpen('bracket');
	}

	private static function couldBeColon() {
		return self::$open['ternary'] > 0 && self::$open['ternary2'] < self::$open['ternary'] && self::$notTextOrComp;
	}
	
	private static function couldBeEnd() {
		return !self::isAnyOpen() || self::$isCase;
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
		return self::$open['var'] || self::$open['react'] || self::$open['react2'] || self::$open['global'] || self::$isKey || self::$open['letvarname'] || self::$isNum;
	}

	private static function couldBeGreater() {
		return (self::$open['var'] && !self::$open['letvarname']) || self::$isNum || self::$open['react'] || self::$open['react2'] || self::$open['global'];
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

	private static function finish($prepared) {
		self::$expectedKeywords = array();
		self::off('recentQuote');
		if (self::$open['placeholder']) {
			if (!self::$open['placeholderShouldHaveDefaultValue']) {
				self::on('placeholderHasName');
			} else {
				self::on('placeholderHasDefaultValue');
			}
		}		

		if (self::$isReact) {
			$key = 'react';
			if (self::$open['global']) {
				$key = 'global';
			}
			if (empty(self::$reactName)) {
				self::$data[$key][self::$currentPart] = array();
				self::$reactName = self::$currentPart;
			} else {
				self::$data[$key][self::$reactName][] = self::$currentPart;
			}
		} else {
			self::$reactName = '';
		}

		if (self::$isForeach && !self::$open['foreachLimit'] && !self::$open['foreachWhile']) {
			if (!empty(self::$open['foreachAs2'])) {
				self::$expected = array(self::$space, 'end');
				self::$expectedKeywords = array('limit', 'while');
			} elseif (!empty(self::$open['foreachAs'])) {
				self::$expected = array('=', self::$space, 'end');
				self::$expectedKeywords[] = 'limit';
				self::$expectedKeywords[] = 'while';
			} elseif (empty(self::$open['foreachArr'])) {
				self::on('foreachArr');
				if (!self::$open['fn']) {
					self::$expected = array(self::$space);

					if (!empty(self::$nextPart) && self::$nextPart[0] == '(' && !self::$anyVar) {
						array_push(self::$expected, '(');
					}
				} else {
					self::$expected = array('(');
				}				
				if (!self::$open['method'] && !self::$open['fn']) {
					array_push(self::$expected, 'a', '[', '.');
				}
			}
		}

		if (self::$isLet && self::$open['letvalue'] && empty(self::$open['parenthesis']) && empty(self::$open['fn'])) {
			self::$expected[] = ',';
		}

		self::offVars($prepared);
		self::offEquals();
		self::off('letvalue');
		self::off('space');
	}

	private static function offEquals() {
		self::off('equal');
		self::off('doubleEqual');
		self::off('tripleEqual');
		self::off('notEqual');
		self::set('math', '');
	}

	private static function offVars($prepared) {
		if ($prepared && !self::$isNum) {
			self::on('name');
		}
		self::off('global');
		self::off('react');
		self::off('var');
		self::off('data');
		self::off('text');
		self::off('comp');
		self::set('or', 0);
		self::set('math', '');
		self::off('equal');
	}

	
	private static function isAnyVarOpen() {
		return self::isOpen('react', 'react2', 'text', 'comp', 'var', 'var2', 'data', 'global');
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

	private static function getPlaceCaption() {
		switch (self::$place) {
			case 'foreach':
				return 'foreach оператора';

			case 'eventAttribute':
				return 'атрибута события элемента';

			case 'componentClass':
				return 'имени класса компонента';
			
			case 'templateAttribute':
				return 'атрибута шаблона';

			case 'elementAttribute':
				return 'атрибута элемента';
			
			case 'componentAttribute':
				return 'атрибута компонента';
			
			case 'ifcase':
				return 'case оператора';

			case 'switch':
				return 'switch оператора';

			case 'textNode':
				return 'текстового элемента';

			case 'let':
				return 'let оператора';
			
			default:
				return '';
		}
	}

	private static function defineExpected($place) {
		switch ($place) {
			case 'componentClass':
				self::$expected = array('a', '.', '&', '~', '#', '%');
			break;
			case 'eventAttribute':
			case 'if':
			case 'else':
			case 'templateAttribute':
			case 'elementAttribute':
				self::$expected = array("'", '"', '0', '+', '-', '!', 'a', '.', '&', '$', '~', '@', '#', '%');
			break;
			case 'componentAttribute':
				self::$expected = array("'", '"', '0', '+', '-', '!', 'a', '.', '&', '$', '~', '@', '#', '^', '%');
			break;
			case 'let':
			case 'textNode':
				self::$expected = array("'", '"', '0', '+', '-', '!', 'a', '.', ':', '&', '$', '~', '@', '%');
			break;
			case 'switch':
			case 'ifcase':
			case 'foreach':
				self::$expected = array('a', '%');
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
		if (!empty(self::$expectedKeywords) && in_array($text, self::$expectedKeywords)) {
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
		self::$expected = array_unique(self::$expected);
		foreach (self::$expected as $exp) {
			if ($exp == self::$space) continue;
			if ($exp == 'a') {
				$items[] = self::getExpectedWord();
			} else {
				$items[] = self::$signs[$exp];
			}

		}
		if (!empty(self::$expectedKeywords)) {
			foreach (self::$expectedKeywords as $exp) {
				array_unshift($items, 'ключевое слово '.$exp);
			}			
		}
		return implode("\n", $items);
	}

	private static function error($name, $vars) {
		self::log();
		$elementCode = '';
		if (!empty(self::$element)) {
			$elementCode = 'Элeмент в котором произошла ошибка: <xmp>'.self::$element.'</xmp><br>';
		}
		$err = 'Ошибка в парсинге кода '.self::getPlaceCaption().' в шаблоне <b>'.self::$templateName.'</b> класса <b>'.self::$className.'</b><br><br>
				'.$elementCode.'
		        Код в котором произошла ошибка: {'.self::$code.'}<br><br>';
		new Error($err.self::$errors[$name], $vars);
	}

	private static function getExpectedWord() {
		if (self::$open['placeholder'] && !self::$open['placeholderHasName']) {
			return self::$names['pl'];
		}
		if (self::$open['var'] || self::$open['react']) {
			return self::$names['var'];
		}
		if (self::$open['global']) {
			return self::$names['global'];
		}
		if (!empty(self::$open['method'])) {
			return self::$names['method'];
		}
		if (!empty(self::$open['comp'])) {
			return self::$names['comp'];
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
		if (self::$thereWasWord) {
			self::$expected = null;
		}		
		
		if (!empty(self::$open['fn']))
		{
			self::$expected = array('(');
		}
		elseif (!empty(self::$open['ternary']) && !self::$open['functionResult'] && !self::$open['number'] && !self::$open['name'] && !self::$open['recentQuote'] && !self::isAnyVarOpen() && !self::$open['recentVar'])
		{
			self::$expected = array('a', '0', '!', '&', '~', '#', '@', '(', self::$space);
			self::maybeAddDollar();
		}
		elseif (self::$isForeach && !self::$open['foreachAs'] && !self::$open['foreachAs2'] && !self::$open['foreachLimit'] && !self::$open['foreachWhile'])
		{
			if (self::$open['foreachArr']) {
				self::$expected = array();
				self::$expectedKeywords = array('as');
			} elseif (self::$open['foreachAs']) {				
				self::$expected = array('&');
			} else {
				if (self::$foreachKeyword) {
					self::$expected = array('a', '.', '$', '~', '&', '#', self::$space);
				} else {
					self::$expected = array('a', '.', '$', '~', '&', '#', self::$space);
					array_push(self::$expectedKeywords, 'right', 'random');
				}
			}
		} 
		elseif ((self::$open['foreachAs2'] && !self::$open['foreachAsVar2']) || (self::$open['foreachAs'] && !self::$open['foreachAsVar']))
		{
			if (self::$open['var']) {
				self::$expected = array('a');
			} else {
				self::$expected = array('&');
			}
		}
		elseif (self::$open['equal'])
		{
			if (self::$open['foreachAs']) {
				self::$expected = array(">");	
			} elseif (self::$isLet) {
				self::$expected = array('a', '0', 'b', '.', '~', '$', '@', '#', '-', '+', '!', '[', '(', "'", '"');
			} elseif (self::$open['placeholderShouldHaveDefaultValue']) {
				self::$expected = array('a', '0', '.', '&', '~', '@', '#', '-', '+', '!', '[', '(', "'", '"');
			} else {
				self::$expected = array('=');
			}
		}
		elseif (self::$open['doubleEqual'] || self::$open['tripleEqual'] || self::$open['notEqual'])
		{
			self::$expected = array('a', '0', 'b', '.', '&', '~', '$', '@', '#', '-', '+', '!', '(', "'", '"');
			if (!self::$open['tripleEqual']) {
				self::$expected[] = '=';
			}
		}
		elseif (self::$open['decimal'])
		{
			self::$expected = array("1");
		}
		elseif (self::$open['placeholder'] && !self::$thereWasWord)
		{
			self::$expected = array("a");
		}
		elseif (!empty(self::$open['math']))
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
		elseif (self::isOpen('react', 'text', 'comp', 'var', 'data', 'global') && !self::$open['placeholderHasName'])
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
			if (self::$open['ternary'] > self::$open['ternary2']) {
				$code .= ":<emptystring>";
			}
			self::$data['ternary'] = true;
		}
		if (self::$open['placeholder']) {
			self::$data['placeholder'] = true;
			return self::getPlaceholderCode($code);		
		} 
		if (!empty(self::$data['react'])) {
			$code = self::getReactCode($code);
		}
		if (!empty(self::$data['global'])) {
			$code = self::getGlobalVarsCode($code);
		}
		if (self::$isLet) {
			self::$data['isLet'] = true;
			$code = preg_replace('/^\s*let\s*/', '', $code);
		} elseif (self::$isCase) {
			$code = preg_replace('/^\s*case\s*/', '', $code);
		} elseif (self::$isSwitch) {
			$code = preg_replace('/^\s*switch\s*/', '', $code);
		} elseif (self::$isForeach) {
			$code = preg_replace('/^\s*foreach\s*/', '', $code);
			if (preg_match('/\slimit\s/', $code)) {
				$parts = preg_split('/\slimit\s/', $code);
				$code = $parts[0];
				self::$data['limit'] = '<nq>'.trim($parts[1]).'<nq>';
				self::$data['reactiveLimit'] = preg_match('/\$\.g/', $parts[1]);
			}
			if (preg_match('/^right\b/', $code)) {
				$code = preg_replace('/^right\s*/', '', $code);
				self::$data['right'] = true;
			} elseif (preg_match('/^random\b/', $code)) {
				$code = preg_replace('/^random\s*/', '', $code);
				self::$data['random'] = true;
			}
			$parts = preg_split('/\s+while\s+/', $code);
			if (isset($parts[1])) {
				self::$data['while'] = $parts[1];
				$code = $parts[0];
			}
			$parts = preg_split('/\s+as\s+/', $code);
			self::$data['items'] = '<nq>'.$parts[0].'<nq>';
			self::$data['reactiveItems'] = preg_match('/\$\.([ga])\(\'([^\']+)\'/', $parts[0], $match);
			if (self::$data['reactiveItems']) {
				if ($match[1] == 'g') {
					self::$data['reactName'] = $match[2];
				} else {
					self::$data['globalName'] = $match[2];
				}
			}
			$parts = preg_split('/\s*=>\s*/', $parts[1]);
			if (isset($parts[1])) {
				self::$data['key'] = '<nq>'.$parts[0].'<nq>';
				self::$data['value'] = '<nq>'.$parts[1].'<nq>';
			} else {
				self::$data['value'] = '<nq>'.$parts[0].'<nq>';
			}
		}
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

	private static function getReactCode($code) {
		self::$data['reactNames'] = array_keys(self::$data['react']);
		unset(self::$data['react']);
		$names = self::$data['reactNames'];
		if (count($names) == 1) {
			$names = "'".$names[0]."'";
		} else {
			$names = json_encode($names);
		}
		if (!preg_match('/^\$\.g\(\'\w+\'\)$/', $code) && self::$place != 'if' && self::$place != 'else') {
			self::$data['inFunc'] = true;
		}
		return $code;
	}

	private static function getGlobalVarsCode($code) {
		self::$data['globalNames'] = array_keys(self::$data['global']);
		unset(self::$data['global']);
		$names = self::$data['globalNames'];
		if (count($names) == 1) {
			$names = "'".$names[0]."'";
		} else {
			$names = json_encode($names);
		}
		if (!self::$data['inFunc'] && !preg_match('/^\$\.a\(\'\w+\',1\)$/', $code) && self::$place != 'if' && self::$place != 'else') {
			self::$data['inFunc'] = true;
		}
		return $code;
	}

}