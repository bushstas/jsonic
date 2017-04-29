<?php

class TemplateParser 
{	
	private static $calledClasses, $templates, $sources, $includes;
	public static $classes;
	private static $regexp = "/\{ *template +\.(\w+) *\}/";
	private static $space = '_u00A0_';
	private static $textNodes = array();
	
	private static $simpleTags = array(
		'br', 'input', 'img', 'hr'
	);

	private static $singleTags = array(
		'else', 'ifempty', 'let'
	);
	
	private static $class, $className, $tmpids, $propsShortcuts,
				   $eventTypesShortcuts, $obfuscate, $tagShortcuts,
				   $templateName, $parsedItem, $initials, $componentsOpen = 0;

	private static $errors = array(
		'noMainTemplate' => 'Шаблон <b>main</b> класса {??} не найден среди прочих',
		'forbiddenTag' => 'Обнаружен недопустимый тег {??} в шаблоне {??} класса {??}<xmp>{?}</xmp>',
		'noClosingTag' => 'Обнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'noClosingTag2' => 'Обнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag' => 'Обнаружен лишний закрывающийся {?} {??} в шаблоне {??} класса {??}<br>Данный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>Ожидается закрытие тега {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag2' => 'Обнаружен лишний закрывающийся {?} {??} в шаблоне {??} класса {??}<br>Данный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag3' => 'Обнаружен закрывающийся {?} {??} в начале шаблона {??} класса {??}',
		'unknownComponent' => 'Неопределенный компонент в шаблоне {??} класса {??}<xmp>{?}</xmp>Ожидается запись вида<xmp>{?}</xmp>',
		'controlWithoutName' => 'Контрол {??} в шаблоне {??} класса {??} не имеет атрибута <b>name</b><xmp>{?}</xmp>Ожидается запись вида<xmp>{?}</xmp>',
		'reactVarInInclude' => 'Шаблон {??}, содержащийся в файле {??} содержит код с реактивными переменными {??}<br><br>Глобальные шаблоны с типом <b>include</b> не могут содержать их. Допускается использование только входящих аргументов <b>~arg</b> и локальных переменных <b>&var</b>',
		'reactComponentName' => 'Название компонента в шаблоне {??} класса {??} не может определяться реактивной переменной<xmp>{?}</xmp>Допускается запись вида<xmp><component class="{~class}"></xmp>или<xmp><component class="{&class}"></xmp>',
		'reactControlName' => 'Атрибут <b>name</b> контрола в шаблоне {??} класса {??} не может определяться реактивной переменной<xmp>{?}</xmp>Допускается запись вида<xmp><control name="{~class}"></xmp>или<xmp><control name="{&class}"></xmp>',
		'letError' => 'Ошибка в коде оператора <b>let</b> в шаблоне {??} класса {??}<xmp>{{?}}</xmp><b>Ожидается код вида</b><xmp>{let &var = 5}</xmp><b>или</b><xmp>{let &isEmpty: true}</xmp>',
		'caseOutsideSwitch' => "Обнаружен оператор <b>case</b> вне оператора <b>switch</b> или подобного ему <b>if</b> в шаблоне {??} класса {??}<br><br><b>Используйте код вида</b><xmp>{switch ~value}\n\t{case 10}\n\t\t<div class=\"ten\">10</div>}\n\n\t{default}\n\t\tdefault text\n{/switch}</xmp><b>или</b><xmp>{if}\n\t{case !isUndefined(\$var)}\n\t\tvariant 1\n\n\t{case \$var2 === true}\n\t\tvariant 2\n\n\t{default}\n\t\tdefault text\n{/if}</xmp>",
		'dataInTextNode' => 'Обнаружено использование контстанты данных {??} внутри текстового нода в шаблоне {??} класса {??}<br><br>Допускается использование только внутри атрибутов тегов <xmp><component Item args="{#itemDefaultArgs}"></xmp>или внутри javascript кода класса<xmp>var params = #itemDefaultParams</xmp>',
		'usingThis' => 'Обнаружено использование ключевого слова <b>this</b> в шаблоне {??} класса {??}',
		'templateCallLoop' => 'Шаблон {??} класса {??} вызывает сам себя',
		'caseExpected' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Ожидается оператор <b>case</b><xmp>{case 'triangle'}</xmp>или<xmp>{case 2}</xmp>",
		'ifCaseExpected' => "Обнаружена ошибка в коде оператора <b>if</b> в шаблоне {??} класса {??}.<br><br>Найдено:<xmp>{?}</xmp>Ожидается оператор <b>case</b><xmp>{case isNumber(~var)}</xmp>или<xmp>{case &a > &b}</xmp>",
		'fewDefaults' => 'Обнаружено более одного условия <b>default</b> в коде оператора {??} в шаблоне {??} класса {??}',
		'conditionEmpty' => 'Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Условие {??} не содержит контента',
		'elseWithoutIf' => 'Элемент в шаблоне {??} класса {??} содержит атрибут <b>else</b>, но не содержит атрибут <b>if</b>',
		'incorrectIf' => 'Элемент в шаблоне {??} класса {??} содержит некорректный атрибут <b>if = "{?}"</b><br><br>Атрибут должен иметь вид <b>if = "{$a === true}"</b> или <b>if = "{!&name}"</b>',
		'handlerNotFound' => 'Функция {??}, указанная в шаблоне {??} класса {??} в качестве обработчика события {??}, не найдена среди методов данного класса',
		'noTemplateName' => 'Вызов шаблона без указания его имени в шаблоне {??} класса {??}. Код должен иметь вид:<xmp><template templ="table" rows="{~rows}"></xmp>',
		'noIncludeTemplateName' => 'Вызов шаблона без указания его имени в шаблоне {??} класса {??}. Код должен иметь вид:<xmp><include templ="table" rows="{~rows}"></xmp>',
		'codeOutsideAttribute' => 'Обнаружен код вне атрибута тега в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'keywordInEventAttr' => 'Обнаружено ключевое слово {??} в атрибуте события {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'numericEventAttr' => 'Обнаружено числовое значение {??} атрибута события {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'incorrectEventAttr' => 'Обнаружено некорректное значение {??} атрибута события {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'unknownSpecEventAttr' => 'Обнаружен неизвестный спецпараметр {??} атрибута события {??} в шаблоне {??} класса {??}<br><br>Ожидается одно из значений: <xmp>{?}</xmp>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'unknownEventAttr' => 'Обнаружен атрибут неизвестного события {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'specEventAttrInComp' => 'Обнаружен спецпараметр {??} события {??} в теге компонента в шаблоне {??} класса {??}<br><br>Данный функционал доступен только для элементов DOM<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'operatorInInnerLevel' => 'Обнаружен оператор {??} не на одном уровне с оператором {??} в шаблоне {??} класса {??}',
		'doubleOperator' => 'Обнаружен оператор {??} внутри другого оператора {??} в шаблоне {??} класса {??}',
		'fewSameOperators' => 'Обнаружено дублирование оператора {??} внутри оператора {??} в шаблоне {??} класса {??}',
		'loadingOperatorWithoutLoader' => 'Обнаружено использование одного из операторов <b>loading, loader</b> в шаблоне {??} класса {??}. У данного класса отсутствует initial параметр <b>loader</b>',
		'invalidTagName' => 'Некорректное имя тега {??} в шаблоне {??} класса {??}<br><br>Теги элементов DOM должны иметь вид:<xmp><div>, <h1>, <table></xmp>Теги компонентов должны иметь вид:<xmp><Select>, <TableColumn></xmp>Теги вызова шаблона класса:<xmp><:content>, <:innerContent></xmp>Теги вызова свободного шаблона:<xmp><::checkbox>, <::userArea></xmp>',
		'includeNotFound' => 'Обнаружен вызов несуществующего include шаблона {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'foreachVarsConflict' => 'Обнаружен конфликт имен переменных в операторе <b>foreach</b> в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'emptySwitch' => 'Обнаружен оператор <b>switch</b>, который не содержит ни одного оператора <b>case</b>, в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>'
	);

	public static function init($params) {
		self::$initials = $params['initialsParser']->get();
		self::$classes = $params['classes'];
		self::$sources = $params['sources'];
		self::$templates = $params['templates'];
		self::$includes = $params['includes'];
		self::$propsShortcuts = Props::getList();
		self::$eventTypesShortcuts = Events::getList();
		self::$tagShortcuts = Tags::getList();
		self::$obfuscate = $params['obfuscateCss'];
		$varNames = JSGlobals::getAllReservedVarNames();
		TemplateCodeParser::setGlobalNames($varNames, $params['utilsFuncs'], $params['userUtilsFuncs']);
	}

	public static function getTextNodes() {
		return self::$textNodes;
	}

	public static function parse($template, &$class, $className = '', &$tmpids = '') {
		self::$class = &$class;
		self::$className = $className;
		$template = preg_replace('/[\t\r\n]/', '', $template);
		$template = preg_replace('/ {2,}/', ' ', $template);
		$template = preg_replace('/&nbsp;/', self::$space, $template);
		$template = preg_replace('/\$children\b/', '~children', $template);

		preg_match_all("/\{template +\.(\w+) +as +\.(\w+) *\}/", $template, $matches);		
		foreach ($matches[1] as $i => $match) {
			$tmpids[$matches[2][$i]] = $match;
			$template = preg_replace('/\{template +\.'.$match.' +as +\.'.$matches[2][$i].' *\}/', '{template .'.$match.'}', $template);
		}
		self::$tmpids = $tmpids;
		preg_match_all(self::$regexp, $template, $matches);
		$templateNames = $matches[1];
		if (is_array($templateNames)) {
			self::$class['templatesList'] = $templateNames;
		}
		if (!empty($templateNames)) {
			if (!empty($className) && !preg_match("/\{template +\.main *\}/", $template) && !self::hasParentMainTemplate($class)) {
				new Error(self::$errors['noMainTemplate'], $className);
			}
			$templateContents = preg_split(self::$regexp, $template);
			array_shift($templateContents);
		} else {
			$templateNames = array('main');
			self::$class['templatesList'] = $templateNames;
			$templateContents = array($template);
		}
		
		$templates = array();
		for ($i = 0; $i < count($templateNames); $i++) {
			if (preg_match('/\{ *(\/*loading|\/*onload) *\}/', $templateContents[$i])) {
				if (!isset(self::$initials[$className]['loader'])) {
					new Error(self::$errors['loadingOperatorWithoutLoader'], array($templateNames[$i], $className));
				}
				$templateContents[$i] = preg_replace('/\{ *loading *\}/', '{if $__loading}', $templateContents[$i]);
				$templateContents[$i] = preg_replace('/\{ *onload *\}/', '{if !$__loading}', $templateContents[$i]);
				$templateContents[$i] = preg_replace('/\{ *\/(loading|onload) *\}/', '{/if}', $templateContents[$i]);
			}
			self::$templateName = $templateNames[$i];
			TemplateCodeParser::init(self::$templateName, $className);
			$tmp = self::getParsedTemplate($templateContents[$i]);
			$templates[] = $tmp;
		}

		$isSingle = count($templates) == 1;
		$templateFunctions = array();
		foreach ($templates as $template) {
			$data = json_encode($template['children']);
			$data = JSGlobals::normJsonStr($data);
			if ($data == '[]') {
				$data = '';
			} else {
				$data = str_replace('"', "'", $data);		
				$data = str_replace("\'", "'", $data);				
				$data = str_replace('<this>', '$.', $data);
				$data = preg_replace("/['\"]+<nq>/", '', $data);
				$data = preg_replace("/<nq>['\"]+/", '', $data);
				$data = preg_replace("/<nq>/", '', $data);

				$data = str_replace('\\', '', $data);
				$data = str_replace('_u00A0_', '\\u00A0', $data);
							
				$data = str_replace("<=let>,<let>", ";", $data);
				$data = str_replace("<let>", "function(){", $data);
				$data = preg_replace("/<=let>,*/", ";return[", $data);
				$data = preg_replace("/,'<\/let>'/", "]}", $data);
				$data = preg_replace("/<\/let>'/", "']}", $data);
				$data = str_replace("<quote>", "'", $data);
				$data = str_replace("<emptystring>", "''", $data);
				$data = str_replace("<space>", "' '", $data);
				$data = preg_replace("/''\+|\+''/", "", $data);
				$data = str_replace("_#_comma_#_", ",", $data);
				$data = str_replace("_#_equal_#_", "=", $data);
				$data = preg_replace("/\{\s*return\s*\}/", "{}", $data);
				$data = preg_replace("/return\[(\d+)\]/", "return $1", $data);
			}
			$templateFunctions[] = array('content' => $data, 'name' => $template['name'], 'let' => $template['let']);
		}
		return $templateFunctions;
	}

	private static function getParsedTemplate($content) {
		$html = preg_replace('/<([\w:][^>]*)<([\w:])/i', CONST_LESS."$1<$2", $content);
		$html = preg_replace(self::$regexp, '', $html);
		$html = str_replace('->>', "#classobfus#", $html);
		$html = preg_replace('/<('.implode('|', self::$simpleTags).') ([^\/>]*)\/>/', "<=$1 $2/>", $html);
		$html = preg_replace('/<(:*\w+)([^>]*)\/>/', "<$1$2></$1>", $html);
		$html = preg_replace('/<=(\w+) ([^\/>]*)\/>/', "<$1 $2/>", $html);
		$parts = preg_split('/\{\/template\}/', $html);
		$html = $parts[0];

		$regexp = "/\{[^\}]+\}/";
		preg_match_all($regexp, $html, $matches);
		$matches = $matches[0];
		foreach ($matches as &$match) {
			$match = str_replace('>', CONST_MORE, $match);
		}
		$parts = preg_split($regexp, $html);
		$html = '';
		foreach ($parts as $i => $part) {
			$html .= $part;
			if (isset($matches[$i])) {
				$html .= $matches[$i];
			}
		}
		$regexp = "/(<\/*:*[a-z]+[^>]*>|\{\s*\/*foreach\b[^\}]*\}|\{\s*\/*from\b[^\}]*\}|\{\s*\/*if\b[^\}]*\}|\{\s*\/*case\b[^\}]*\}|\{\s*\/*default\b[^\}]*\}|\{\s*else\s*\}|\{\s*ifempty\s*\}|\{\s*\/*switch\b[^\}]*\}|\{\s*\/*let\b[^\}]*\})/i";
		preg_match_all($regexp, $html, $matches);
		$tags = implode('_#_TMPDELIMITER_#_', $matches[1]);
		$tags = explode('_#_TMPDELIMITER_#_', str_replace(CONST_MORE, '>', $tags));
		$parts = preg_split($regexp, $html);
		$list = array();
		for ($j = 0; $j < count($parts); $j++) {
			$part = $parts[$j];
			if (!empty($part)) {
				$part = str_replace(CONST_LESS, "<", $part);
			}
			if (is_numeric($part) || !empty($part)) {
				$list[] = array('type' => 'text', 'content' => $part);
			}
			if (isset($tags[$j]) && !empty($tags[$j])) {
				preg_match('/^[<\{]\s*\/*(:*[a-z]\w*) */i', $tags[$j], $match);
				$tagName = $match[1];
				$tagContent = $tags[$j];
				if (self::invalidTagName($tagName)) {
					new Error(self::$errors['invalidTagName'], array($tagName, self::$templateName, self::$class['name'], $tagContent));
				}
				$isClosing = self::isTagClosing($tagName, $tagContent);
				$tagContent = str_replace('#classobfus#', '->>', $tagContent);
				$list[] = array(
					'type' => 'tag',
					'content' => $tagContent,
					'tagName' => $tagName,
					'isClosing' => $isClosing,
					'isSingle' => self::isSingleTag($tagName) || self::isSimpleTag($tagName)
				);
			}
		}
		$isLet = 0;
		TemplateValidator::validate($list, self::$templateName, self::$className);
		
		
		$children = array('c' => array());
		self::parseChildren($list, $children['c'], $children);
		//Printer::log($children);
		$finishedChildren = array();
		self::finish($children['c'], $finishedChildren);
		//Printer::log($finishedChildren);
		return array('name' => self::$templateName, 'children' => $finishedChildren, 'let' =>  $children['lets']);
	}

	private	static function hasParentMainTemplate($class) {
		if (!is_array($class['extends']) || empty($class['extends'])) {
			return false;
		}
		foreach ($class['extends'] as $className) {
			$template = self::$templates[$className];
			if (!empty($template)) {
				preg_match_all("/\{template +\.(\w+) *\}/", $template, $matches);
				if (empty($matches[1]) || in_array('main', $matches[1]) || self::hasParentMainTemplate(self::$classes[$className])) {
					return true;
				}
			}
		}
		return false;
	}

	private static function parseChildren($list, &$children, &$parentalChild, $ofElse = false) {
		$isElse = false;
		$isIfEmpty = false;
		$usedClasses = ClassAnalyzer::getUsedClasses();
		for ($i = 0; $i < count($list); $i++) {
			$item = $list[$i];
			$lets = array();
			if ($item['type'] == 'text') {
				if (!empty($item['content']) || is_numeric($item['content'])) {
					$children[] = $item['content'];
				}
			} else {
				self::$parsedItem = $item['content'];
				$tag = $item['tagName'];
				if ($tag == 'br') {
					$children[] = '<br>';
					continue;
				}
				$child = array('tagName' => $tag, 'content' => $item['content'], 'children' => array(), 'else' => array(), 'ifempty' => array());
				$ch = array();
				$ech = array();
				$iech = array();
				$open = 0;
				while (true) {
					if ($tag == 'let') {
						break;
					}
					$i++;
					if (isset($list[$i])) {
						if ($tag == 'if' && $open == 0 && $list[$i]['tagName'] == 'else') {
							$isElse = true;
							continue;
						}
						if ($tag == 'foreach' && $open == 0 && $list[$i]['tagName'] == 'ifempty') {
							$isIfEmpty = true;
							continue;
						}
						if ($isIfEmpty) {
							$iech[] = $list[$i];
						} else if (!$isElse) {
							$ch[] = $list[$i];
						} else {
							$ech[] = $list[$i];
						}
						if ($list[$i]['tagName'] == $tag) {
							if (empty($list[$i]['isClosing'])) {
								$open++;
							} else {
								if ($open > 0) {
									$open--;
								} else {
									if ($isIfEmpty) {
										array_pop($iech);
									} elseif (!$isElse) {
										array_pop($ch);
									} else {
										array_pop($ech);
									}
									break;
								}
							}
						}
					} else {
						break;
					}
				}
				$chld = array();
				$isElse = false;
				if (!empty($ch)) {
					self::parseChildren($ch, $child['children'], $chld);
				}
				if (!empty($ech)) {
					self::parseChildren($ech, $child['else'], $chld, true);
				}
				if (!empty($iech)) {
					self::parseChildren($iech, $child['ifempty'], $chld, true);
				}
				$isComponent = in_array($child['tagName'], $usedClasses) || $child['tagName'] == 'Component' || $child['tagName'] == 'Control';
				$attributes = self::parseTagAttributes($child, $isComponent, $child['tagName']);
				$child['attributes'] = $attributes;

				if (!empty($child['reactProps'])) {
					$chld['reactProps'] = $child['reactProps'];
				}
				if (!empty($child['globalProps'])) {
					$chld['globalProps'] = $child['globalProps'];
				}
				if (!empty($child['componentProps'])) {
					$chld['componentProps'] = $child['componentProps'];
				}
				if (!empty($child['controlName'])) {
					$chld['controlName'] = $child['controlName'];
				}
				
				if ($child['content'][0] == '{') {
					if (preg_match('/\{\s*if\s*\}/', $child['content'])) {
						$chld['ifswitch'] = 1;							
					} elseif ($child['tagName'] == 'switch') {
						$chld['switch'] = trim(preg_replace('/^\s*{\s*switch */', '', $child['content']), '}');
						if (empty($chld['switch'])) {
							$chld['switch'] = ' ';
						}
					} elseif ($child['tagName'] == 'from') {
						$chld['from'] = trim(preg_replace('/^\s*{\s*from */', '', $child['content']), '}');
						if (empty($chld['from'])) {
							$chld['from'] = ' ';
						}
					} elseif ($child['tagName'] == 'if') {
						$chld['if'] = trim(preg_replace('/^\s*{\s*if */', '', $child['content']), '}');
						if (empty($chld['if'])) {
							$chld['if'] = ' ';
						}
					} elseif ($child['tagName'] == 'let') {						
						$names = array();
						$globals = array();
						$lets = self::parseLetOperator($item['content'], $names, $globals);
						$letsKey = $ofElse ? 'elseLets' : 'lets';
						$reactNamesKey = $ofElse ? 'elseLetsReactNames' : 'letsReactNames';
						$globalNamesKey = $ofElse ? 'elseLetsGlobalNames' : 'letsGlobalNames';
						if (!empty($lets)) {
							if (!empty($names)) {
								$parentalChild[$reactNamesKey] = $names;
							}
							if (!empty($globals)) {
								$parentalChild[$globalNamesKey] = $globals;
							}
							if (!isset($parentalChild[$letsKey])) {
								$parentalChild[$letsKey] = $lets;
							} else {
								$parentalChild[$letsKey] = array_merge($parentalChild[$letsKey], $lets);
							}
						}
						continue;
					} elseif ($child['tagName'] == 'foreach') {
						$chld['foreach'] = trim(preg_replace('/^\s*{\s*foreach */', '', $child['content']), '}');
						if (empty($chld['foreach'])) {
							$chld['foreach'] = ' ';
						}
					} elseif ($child['tagName'] == 'case') {
						$chld['case'] = trim(preg_replace('/^\s*{\s*case */', '', $child['content']), '}');
						if (empty($chld['case'])) {
							$chld['case'] = ' ';
						}
					} elseif ($child['tagName'] == 'default') {
						$chld['default'] = trim(preg_replace('/^\s*{\s*default */', '', $child['content']), '}');
					}
				} else {
					if ($child['tagName'][0] == ':') {
						$key = $child['tagName'][1] == ':' ? 'include' : 'template';
						$tmpName = trim($child['tagName'], ':');
						if ($key == 'include' && !in_array($tmpName, self::$includes)) {
							new Error(self::$errors['includeNotFound'], array($tmpName, self::$templateName, self::$class['name'], $child['content']));
						}
						$chld[$key] = $tmpName;
					} elseif ($isComponent) {
						$chld['component'] = !empty($child['cmp']) ? $child['cmp'] : $child['tagName'];
					} else {
						$chld['element'] = $child['tagName'];
					}
					if (!empty($child['attributes'])) {
						$chld['attributes'] = $child['attributes'];
					}
				}
				if (!empty($child['children'])) {
					$chld['children'] = $child['children'];
				}
				if (!empty($child['else'])) {
					$chld['else'] = $child['else'];
				}
				if (!empty($child['ifempty'])) {
					$chld['ifempty'] = $child['ifempty'];
				}
				if (!empty($child['e'])) {
					$chld['events'] = $child['e'];	
				}
				$chld['content'] = $child['content'];
				
				$children[] = $chld;
			}
		}
	}


	private static function finish($children, &$finishedChildren) {
		if (!is_array($children)) {
			$finishedChildren = $children;
			return; 
		}
		foreach ($children as $child) {
			if (is_string($child) || is_numeric($child)) {
				self::parseTextNode($child, $finishedChildren);
			} else {
				$ch = array();
				$isElement = !empty($child['element']);
				$isComponent = !empty($child['component']);
				$isInclude = !empty($child['include']);
				$isTemplate = !empty($child['template']) || $isInclude;
				if ($isComponent) {
					self::$componentsOpen++;
				}
				if (!empty($child['attributes'])) {
					$ch['p'] = array();
					$parsedPlace = $isComponent ? 'componentAttribute' : 'elementAttribute';
					$attrs = $child['attributes'];
					foreach ($attrs as $attrName => $attrValue) {
						if (in_array($attrName, array('if', 'else'))) continue;
						self::addProperTagAttribute($attrName, $attrValue, $ch['p'], $isElement);
					}
					if (!empty($attrs['if'])) {
						$ch['c'] = array();
						self::finish($child['children'], $ch['c']);
						$else = array();
						self::parseTextNode($attrs['else'], $else);
						self::addIfConditionToChild($attrs['if'], $else, $ch);
						unset($child['children']);
					}
				}

				if ($isElement)
				{
					$idx = array_search($child['element'], self::$tagShortcuts);
					$ch['t'] = 	$idx !== false ? $idx : 'span';
				}
				elseif ($isComponent)
				{
					$ch['cmp'] = $child['component'];
					if (!empty($ch['p'])) {
						self::getProperComponentData($ch['p']);
					}
				}
				elseif ($isTemplate)
				{					
					$tmpName = $isInclude ? $child['include'] : $child['template'];
					self::parseTemplate($child['content'], $tmpName, $ch, $isInclude);
					unset($child['reactProps'], $child['globalProps']);
				}
				elseif (!empty($child['foreach']))
				{
					$ch['c'] = array();
					$ch['ie'] = array();
					if (!empty($child['ifempty'])) {
						self::finish($child['ifempty'], $ch['ie']);
					}
					self::finish($child['children'], $ch['c']);
					self::parseForeach('foreach '.$child['foreach'], $ch, $child);
					$finishedChildren[] = $ch;
					continue;
				}
				elseif (!empty($child['from']))
				{
					$ch['c'] = array();
					self::finish($child['children'], $ch['c']);
					self::parseFrom('from '.$child['from'], $ch, $child);
					$finishedChildren[] = $ch;
					continue;
				}
				elseif (!empty($child['ifswitch']))
				{
					$ch['c'] = array();
					self::parseSwitch('', $child['children'], $ch, $child, true);
					$finishedChildren[] = $ch;
					continue;
				}
				elseif (!empty($child['switch']))
				{
					$ch['c'] = array();
					self::parseSwitch($child['switch'], $child['children'], $ch, $child);
					$finishedChildren[] = $ch;
					continue;
				}
				elseif (!empty($child['if']))
				{
					$ch['c'] = array();
					$else = array();
					self::finish($child['children'], $ch['c']);
					self::finish($child['else'], $else);
					self::parseIf($child['if'], $ch, $else, $child);
					$finishedChildren[] = $ch;
					continue;
				}
				if (!empty($child['events'])) {
					$ch['e'] = $child['events'];
				}

				if (!empty($child['controlName'])) {
					$ch['nm'] = $child['controlName'];
				}
				if (!empty($child['componentProps'])) {
					$ch['w'] = $child['componentProps'];
				}
				$hasReactProps = !empty($child['reactProps']);
				$hasGlobalProps = !empty($child['globalProps']);
				if ($hasReactProps || $hasGlobalProps) {
					if ($hasReactProps) {
						$ch['n'] = $child['reactProps'];
					}
					if ($hasGlobalProps) {
						$ch['g'] = $child['globalProps'];
					}
					$ch['p'] = '<nq>function(){return'.self::getProperChildren($ch['p']).'}<nq>';
				}
				if (!empty($child['children'])) {
					if (is_array($child['children'])) {
						$ch['c'] = array();
						self::finish($child['children'], $ch['c']);
					} else {
						$ch['c'] = $child['children'];
					}
				}
				if (!empty($child['lets'])) {
					self::wrapInFunction($ch['c'], '', 'lets', $child);
				}
				$finishedChildren[] = $ch;
				if ($isComponent) {
					self::$componentsOpen--;
				}
			}
		}
		if (count($finishedChildren) == 1) {
			$finishedChildren = $finishedChildren[0];
		}
	}

	private static function addProperTagAttribute($name, $value, &$attrs, $isElement) {
		if (!$isElement && in_array($value, array('true', 'false', 'null', 'undefined'))) {
			$value = '<nq>'.$value.'<nq>';
		}		
		if ($name == 'scope') {
			$attrs[self::$propsShortcuts[$name]] = 1;
			return;
		}		
		if ($isElement && isset(self::$propsShortcuts[$name])) {
			$name = self::$propsShortcuts[$name];
		} else {
			$name = preg_replace('/^data-/', '_', $name);
		}
		$attrs[$name] = $value;
	}

	private static function invalidTagName($tagName) {
		if (strlen($tagName) > 1 && !preg_match('/[a-z]/', $tagName)) {
			return true;
		}
		return !(preg_match('/^[a-z][a-z0-9]*$/', $tagName) ||
			   preg_match('/^[A-Z][a-zA-Z0-9]*$/', $tagName) ||
			   preg_match('/^:{1,2}\w+$/', $tagName));
	}

	private static function isTagClosing($tagName, $tagContent) {
		return preg_match("/^[<\{]\//", $tagContent) ? 1 : 0;
	}

	private	static function isSimpleTag($tagName) {
		return in_array($tagName, self::$simpleTags);
	}

	private	static function isSingleTag($tagName) {
		return in_array($tagName, self::$singleTags);
	}

	private static function getProperChild($children) {
		if (is_array($children)) {
			while (is_array($children) && isset($children[0])) {
				if (count($children) == 1) {
					$children = $children[0];
				} else {
					break;
				}
			}
		}
		return $children;
	}

	private static function parseLetOperator(&$content, &$names, &$globals) {
		$lets = array();
		$data = Splitter::split('/\{\s*let([\s&][^\}]+)\}/', $content, 1);
		$content = '';
		if (empty($data['items'])) {
			$data['items'] = array(' ');
			$data['delimiters'] = array(' ');
		}
		foreach ($data['items'] as $i => $item) {
			$content .= $item;
			$delmr = $data['delimiters'][$i];
			if (!empty($delmr)) {
				$delmr = str_replace('<nq>', '', self::processCode('{let '.$delmr.'}', 'let', $names, $globals));
				if (preg_match('/\[/', $delmr)) {
					$d = Splitter::split('/[\[\]]/', $delmr);
					$open = 0;
					$delmr = '';
					foreach ($d['items'] as $j => $it) {
						if ($open > 0) {
							$it = str_replace(',', '_#_comma_#_', $it);
							$it = str_replace('=', '_#_equal_#_', $it);
						}
						$delmr .= $it;
						if (isset($d['delimiters'][$j])) {
							if ($d['delimiters'][$j] == '[') {
								$open++;
							} else {
								$open--;
							}
							$delmr .= $d['delimiters'][$j];
						}
					}
				}
				$parts = explode(',', $delmr);
				foreach ($parts as $part) {
					$p = explode('=', $part);
					$key = str_replace('&', '', trim($p[0]));
					$lets[$key] = trim($p[1]);
				}
			}
		}
		return $lets;
	}

	private static function getProperChildren($children, $addQuotes = false) {
		$children = self::getProperChild($children);
		if (is_string($children) && $addQuotes) {
			$children = "'".trim($children, "'")."'";
		}
		return is_array($children) ? '<nq>'.preg_replace('/\\\(?=[\'"])/', '', json_encode($children)).'<nq>' : $children;
	}

	private static function wrapInNQ($content) {
		if ($content[0] != '"' && $content[0] != "'") {
			$content = '<nq>'.$content;
		}
		$lastLetter = strlen($content) - 1;
		if ($content[$lastLetter] != '"' && $content[$lastLetter] != "'") {
			$content .= '<nq>';
		}
		return $content;
	}

	private static function wrapInFunction(&$children, $args = '', $mode = '', $data = null) {
		$c = preg_replace('/^<nq>/', '', self::getProperChildren($children));
		$space = ' ';
		if (in_array($c[0], array('[', '{', '(', "'")) || ($c[0] == '<' && preg_match('/^<quote>/', $c))) {
			$space = '';
		}
		$before = '';
		$after = '';
		$innerBefore = '';
		$innerAfter = '';
		if (!empty($data)) {
			if ($mode == 'lets') {
				$innerBefore = 'var '.self::getLetVars($data['lets']).';';
				if (!empty($data['letsReactNames']) || !empty($data['letsGlobalNames'])) {
					$before = "{'l':";
					$after = '}';
					if (!empty($data['letsReactNames'])) {
						if (count($data['letsReactNames']) == 1) {
							$after = ",'n':'".$data['letsReactNames'][0]."'}";
						} else {
							$after = ",'n':['".implode("','", $data['letsReactNames'])."']}";
						}
					}
					if (!empty($data['letsGlobalNames'])) {
						if (count($data['letsGlobalNames']) == 1) {
							$after = ",'g':'".$data['letsGlobalNames'][0]."'".$after;
						} else {
							$after = ",'g':['".implode("','", $data['letsGlobalNames'])."']".$after;
						}
					}
				} else {
					$before = '(';
					$after = ')()';
				}
			} elseif ($mode == 'while') {
				$innerBefore = 'if('.$data['while'].'){';
				$innerAfter = "}else{return'".CONST_BREAK."'}";
			}
		}
		$children = '<nq>'.$before.'function('.$args.'){'.$innerBefore.'return'.$space.$c.$innerAfter.'}'.$after.'<nq>';
	}

	private static function enquote(&$text) {
		$text = '<quote>'.$text.'<quote>';
	}

	private static function parseFrom($content, &$child, $source) {
		if (is_string($child['c']) && !preg_match('/^<nq>/', $child['c'])) {
			self::enquote($child['c']);
		}
		$child['f'] = $child['c'];
		if (!empty($source['lets'])) {
			self::wrapInFunction($child['f'], '', 'lets', $source);
		}
		unset($child['c']);
		$content = ltrim(rtrim($content, '}'), '{');
		$parser = new FromCodeParser();
		$data = $parser->parse($content, self::$templateName, self::$className);
		self::addTemplateCallbacks($data['m']);

		$args = array($data['var']);
		$args = implode(',', $args);
		self::wrapInFunction($child['f'], $args);

		$isReactive = !empty($data['r']) || !empty($data['g']);
		$child['p'] = array(
			self::wrapInNQ($data['from']),
			self::wrapInNQ($data['to'])
		);
		if (!empty($data['step'])) {
			$child['p'][] = self::wrapInNQ($data['step']);
		}
		if ($isReactive) {
			if (!empty($data['r'])) {
				$child['n'] = self::getProperChildren($data['r']);
			}
			if (!empty($data['g'])) {
				$child['g'] = self::getProperChildren($data['g']);
			}
			if (!empty(self::$componentsOpen)) {
				$child['$'] = '<nq>$<nq>';
			}
			self::wrapInFunction($child['p']);
		}
	}

	private static function parseForeach($content, &$child, $source) {
		if (is_string($child['c']) && !preg_match('/^<nq>/', $child['c'])) {
			self::enquote($child['c']);
		}
		if (is_array($child['ie'])) {
			$child['ie'] = self::getProperChild($child['ie']);
		}
		if (empty($child['ie'])) {
			unset($child['ie']);	
		}
		$child['h'] = $child['c'];
		if (!empty($source['lets'])) {
			self::wrapInFunction($child['h'], '', 'lets', $source);
		}
		unset($child['c']);
		$content = ltrim(rtrim($content, '}'), '{');
		$parser = new ForeachCodeParser();
		$data = $parser->parse($content, self::$templateName, self::$className);
		self::addTemplateCallbacks($data['m']);

		$isReactive = !empty($data['r']) || !empty($data['g']);
		if ($isReactive) {
			if (!empty($data['r'])) {
				$child['n'] = self::getProperChildren($data['r']);
			}
			if (!empty($data['g'])) {
				$child['g'] = self::getProperChildren($data['g']);
			}			
			if (!empty(self::$componentsOpen)) {
				$child['$'] = '<nq>$<nq>';
			}
			$child['p'] = array();
			$ch = &$child['p'];
		} else {
			$ch = &$child;
		}

		$ch['p'] = self::wrapInNQ($data['items']);
		if ($data['right']) {
			$child['r'] = 1;
		} elseif ($data['random']) {
			$child['ra'] = 1;
		}
		if (!empty($data['limit'])) {
			if (is_numeric($data['limit'])) {
				$data['limit'] = (int)$data['limit'];
			}
			$ch['l'] = self::wrapInNQ($data['limit']);
		}
		if (!empty($data['from'])) {
			if (is_numeric($data['from'])) {
				$data['from'] = (int)$data['from'];
			}
			$ch['fr'] = self::wrapInNQ($data['from']);
		}
		if (!empty($data['to'])) {
			if (is_numeric($data['to'])) {
				$data['to'] = (int)$data['to'];
			}
			$ch['to'] = self::wrapInNQ($data['to']);
		}
		$args = array($data['value']);
		if (isset($data['key'])) {
			$args[] = $data['key'];
		}
		$args = implode(',', $args);
		if (!empty($data['while'])) {
			self::wrapInFunction($child['h'], $args, 'while', $data);
		} else {
			self::wrapInFunction($child['h'], $args);
		}
		$vars = array($ch['p'], $data['value'], $data['key']);
		if (!empty($vars[0])) {
			if ($vars[0] == $vars[1] || $vars[0] == $vars[2]) {
				new Error(self::$errors['foreachVarsConflict'], array(self::$templateName, self::$className, '{'.$content.'}'));
			}
		}
		if (!empty($vars[1])) {
			if ($vars[1] == $vars[2]) {
				new Error(self::$errors['foreachVarsConflict'], array(self::$templateName, self::$className, '{'.$content.'}'));
			}
		}

		if ($isReactive) {
			self::wrapInFunction($ch);
		}		
		return $child;
	}

	private static function parseTemplate($html, $tmpName, &$child, $isInclude = false) {
		$child['tmp'] = '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>';
		self::$parsedItem = $html;
		$regexp = '/\{([^\}]+)\}/';
		$props = array();
		$names = array();
		$ifCondition = false;
		$else = null;
		list($propNames, $propValues) = self::getTagAttrs($html);	
		
		for ($i = 0; $i < count($propNames); $i++) {
			$propName = $propNames[$i];
			$propValue = trim($propValues[$i]);
			if ($propName == 'if') {
				$ifCondition = $propValue;
				continue;
			}
			if ($propName == 'else') {
				$else = $propValue;
				continue;
			}
			$hasCode = self::hasCode($propValue);
			if ($propName == 'templ') {
				$tmpName = $propValue;
				if (!$hasCode) {
					$child['tmp'] = '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>';
				} else {
					$child['tmp'] = self::processCode($propValue, 'templateAttribute');
				}
				continue;
			}
			if ($hasCode) {
				$propValue = self::processCode($propValue, 'templateAttribute');
			} else {
				self::checkPropertyForObfuscation($propValue);
			}
			$props[$propName] = $propValue;
		}
		if (!empty($props)) {
			$child['p'] = $props;
		}
		if (!$isInclude) {
			if (empty($tmpName)) {
				new Error(self::$errors['noTemplateName'], array(self::$templateName, self::$className));
			}
			if (!empty($tmpName) && $tmpName == self::$templateName) {
				new Error(self::$errors['templateCallLoop'], array(self::$templateName, self::$className));
			}
			if (is_array($child['p']) && !empty($child['p']['tmpid'])) {
				if (!empty(self::$tmpids[$child['p']['tmpid']]) && self::$tmpids[$child['p']['tmpid']] == self::$templateName) {
					new Error(self::$errors['templateCallLoop'], array(self::$templateName, self::$className));
				}
				$child['tmp'] = strip_tags($child['p']['tmpid']);
				unset($child['p']['tmpid']);
				if (count(array_keys($child['p'])) == 0) {
					unset($child['p']);
				}
			}
		} else {
			if (empty($tmpName)) {
				new Error(self::$errors['noIncludeTemplateName'], array(self::$templateName, self::$className));
			}
			$child['tmp'] = '<nq>__INC_TEMPLATE='.$tmpName.'__<nq>';
		}
		if (!empty($ifCondition) || !empty($else)) {
			self::addIfConditionToChild(trim($ifCondition), $else, $child);
		}
	}

	private static function addIfConditionToChild($ifCondition, $else, &$child) {
		if (!empty($else) && empty($ifCondition)) {
			new Error(self::$errors['elseWithoutIf'], array(self::$templateName, self::$className));
		}
		if (!preg_match('/^\{[^\}]+\}$/', $ifCondition)) {
			new Error(self::$errors['incorrectIf'], array(self::$templateName, self::$className, $ifCondition));
		}
		$child = array('c' => array($child));
		if (!empty($else) && $else[0] == '{') {
			$else = self::processCode($else, 'else');
		}
		self::parseIf($ifCondition, $child, $else);
	}

	private static function getLetVars($lets) {
		$list = array(); 
		foreach ($lets as $key => $value) {
			$list[] = $key.'='.$value;
		}
		return implode(',', $list);
	}

	private static function parseIf($ifCondition, &$child, $else = '', $source = null) {
		$hasCode = preg_match('/\$\w/', $ifCondition);
		if (is_string(self::$class) && $hasCode) {
			new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $ifCondition));
		}
		$names = array();
		$globals = array();
		if ($ifCondition[0] != '{') $ifCondition = '{'.$ifCondition.'}';

		$ifCondition = self::processCode($ifCondition, 'if', $names, $globals);
		$isStringC = is_string($child['c']);
		if (is_array($child['c'])) {
			if (isset($child['c'][0]) && count($child['c']) == 1) {
				$child['c'] = $child['c'][0];
			}
			if (is_array($child['c'])) {
				$child['c'] = str_replace('\\', '', json_encode($child['c']));
			}
		}
		$isStringE = is_string($else);
		if (is_array($else)) {
			if (isset($else[0]) && count($else) == 1) {
				$else = $else[0];
			}
			if (is_array($else)) {
				$else = str_replace('\\', '', json_encode($else));
			}
		}
		if (is_array($source)) {
			if (!empty($source['lets'])) {
				self::wrapInFunction($child['c'], '', 'lets', $source);
			}
			if (!empty($else) && !empty($source['elseLets'])) {
				$else = '(function(){var '.self::getLetVars($source['elseLets']).';return '.$else.'})()';
			}
		}
		if (!empty($names) || !empty($globals)) {
			$child['i'] = $ifCondition;
			if (!empty($names) && !empty(self::$componentsOpen)) {
				$child['$']	= '<nq>$<nq>';
			}
			self::wrapInFunction($child['i']);
			if (empty($child['c'])) {
				$child['c'] = "<emptystring>";
			} else {
				if (preg_match('/\$\.[ga]\(/', $child['c'])) {
					self::wrapInFunction($child['c']);
				}
				if (!$isStringC) {
					$child['c'] = '<nq>'.$child['c'].'<nq>';
				} else {
					$child['c'] = $child['c'];
				}				
			}
			if (!empty($else)) {
				if (preg_match('/\$\.[ga]\(/', $else)) {
					self::wrapInFunction($else);
				}
				if (!$isStringE) {
					$child['e'] = '<nq>'.$else.'<nq>';
				} else {
					$child['e'] = $else;
				}
			}
			if (!empty($names)) $child['n'] = $names;
			if (!empty($globals)) $child['g'] = $globals;
		} else {
			if (empty($else)) {
				$else = "<emptystring>";
			}
			$child = '<nq>'.str_replace('<nq>', '', $ifCondition).'?'.$child['c'].':'.$else.'<nq>';
		}
	}

	private static function parseTagAttributes(&$child, $isComponent, $cmpName = '') {
		$html = $child['content'];
		$html = preg_replace('/="([^"]*)"(?!\s)/', "=\"$1\" ", $html);
		$html = preg_replace('/=\'([^\']*)\'(?!\s)/', "='$1' ", $html);
		$html = preg_replace('/\sscope([\s>])/', " scope=\"1\"$1", $html);
		$regexp = "/ ([a-z][\w\-]*)=\"([^\"]+)\"/";
		preg_match_all($regexp, $html, $matches1);
		$regexp2 = "/ ([a-z][\w\-]*)='([^']+)'/";
		preg_match_all($regexp2, $html, $matches2);
		
		if ($html[0] == '<') {
			$tagContent = preg_replace($regexp, '', $html);
			$tagContent = preg_replace($regexp, '', $tagContent);
			if (preg_match('/\{[^\}]+\}/', $tagContent)) {
				new Error(self::$errors['codeOutsideAttribute'], array(self::$templateName, self::$className, $html));
			}
		}
		$names = array_merge($matches1[1], $matches2[1]);
		$values = array_merge($matches1[2], $matches2[2]);
		$attrs = array();
		$reactNames = array();
		$globalNames = array();
		$isDinamycComponent = $isComponent && ($child['tagName'] == 'Component' || $child['tagName'] == 'Control');
		$cmpType = self::$classes[$child['tagName']]['type'];
		foreach ($names as $i => $name) {
			$value = $values[$i];
			if (preg_match("/^on([A-Z]\w+)$/i", $name, $match)) {
				self::parseEventAttribute($match[1], $value, $child, $html, $isComponent);
				continue;
			}

			if ($isComponent) {
				if (($cmpType == 'control' || $child['tagName'] == 'Control') && $name == 'name') {
					$child['controlName'] = self::parseComponentClassName($value, $child['content'], true);
					continue;
				}
			}
			
			if ($name != 'if' && $name != 'else' && self::hasCode($value)) {
				$reactProps = array();
				$globalProps = array();
				$parsedPlace = $isComponent ? 'componentAttribute' : 'elementAttribute';
				$isObfClName = self::$obfuscate === true && $name == 'class';
				$code = self::processCode($value, $parsedPlace, $reactProps, $globalProps, $isObfClName);
				$value = self::correctTagAttributeText($name, $code);
				if ($isDinamycComponent && $name == 'class') {
					$child['cmp'] = $value;
					continue;
				}				
				if (!empty($reactProps)) {
					$reactProps = array_unique($reactProps);
					sort($reactProps);
					if (count($reactProps) == 1) {
						$reactProps = $reactProps[0];
					}
					$reactName = self::$propsShortcuts[$name];
					if (empty($reactName)) {
						$reactName = $name;
					}
					$reactNames[$reactName] = $reactProps;
				}
				if (!empty($globalProps)) {
					$globalProps = array_unique($globalProps);
					sort($globalProps);
					if (count($globalProps) == 1) {
						$globalProps = $globalProps[0];
					}
					$globalName = self::$propsShortcuts[$name];
					if (empty($globalName)) {
						$globalName = $name;
					}
					$globalNames[$globalName] = $globalProps;
				}
			} elseif (self::$obfuscate === true && $name == 'class') {
				self::getObfuscatedClassName($value);
			} else {
				self::checkPropertyForObfuscation($value);
			}
			$attrs[$name] = $value;
		}
		if (!empty($reactNames)) {
			$child['reactProps'] = $reactNames;
		}
		if (!empty($globalNames)) {
			$child['globalProps'] = $globalNames;
		}
		if (empty($cmpName)) {
			$cmpName = $child['cmp'];
		}
		if ($isComponent) {
			$comp = '<ComponentClassName/>';
			if ($cmpType == 'control') {
				$comp = '<ControlClassName name="controlName"/>';
			} elseif ($cmpType == 'menu') {
				$comp = '<MenuClassName/>';
			} elseif ($cmpType == 'form') {
				$comp = '<FormClassName/>';
			}
			if (empty($cmpName)) {
				new Error(self::$errors['unknownComponent'], array(self::$templateName, self::$className, $item['content'], $comp));
			}
			if ($cmpType == 'control' && empty($child['controlName'])) {
				new Error(self::$errors['controlWithoutName'], array($cmpName, self::$templateName, self::$className, $item['content'], $comp));
			}
		}
		if ($isComponent) {
			if (is_array($attrs)) {
				foreach ($attrs as $k => $v) {
					$v = strip_tags(trim($v));
					if ($v[0] == '^') {
						unset($attrs[$k]);
						if (!is_array($child['componentProps'])) {
							$child['componentProps'] = array();
						}
						$child['componentProps'][] = $k;
						$child['componentProps'][] = ltrim($v, '^');
					} elseif ($v[0] == '%') {
						$attrs[$k] = '<nq>$.getTemplate'.ucfirst(ltrim($v, '%')).'<nq>';
					}
				}
			}
		}
		return $attrs;
	}

	private static function getTagAttrs($html) {
		$html = preg_replace('/="([^"]*)"(?!\s)/', "=\"$1\" ", $html);
		$html = preg_replace('/=\'([^\']*)\'(?!\s)/', "='$1' ", $html);
		$html = preg_replace('/\sscope([\s>])/', " scope=\"1\"$1", $html);
		$regexp = "/ ([a-z][\w\-]*)=\"([^\"]+)\"/";
		preg_match_all($regexp, $html, $matches1);
		$regexp2 = "/ ([a-z][\w\-]*)='([^']+)'/";
		preg_match_all($regexp2, $html, $matches2);
		
		if ($html[0] == '<') {
			$tagContent = preg_replace($regexp, '', $html);
			$tagContent = preg_replace($regexp, '', $tagContent);
			if (preg_match('/\{[^\}]+\}/', $tagContent)) {
				new Error(self::$errors['codeOutsideAttribute'], array(self::$templateName, self::$className, $html));
			}
		}
		return array(
			array_merge($matches1[1], $matches2[1]),
			array_merge($matches1[2], $matches2[2])
		);
	}

	private static function processCode($code, $parsedPlace = 'elementAttribute', &$names = null, &$globals = null, $isObfClName = false) {
		$attrParts = array();
		$attrData = Splitter::split('/\{([^\}]*)\}/', $code);
		$items = $attrData['items'];
		$hasText = false;
		foreach ($items as $item) {
			if (!empty($item)) {
				$hasText = true;
				break;
			}
		}
		$parts = array();
		foreach ($items as $idx => $item) {
			if ($item !== '') {
				if ($isObfClName) {
					self::getObfuscatedClassName($item);
				}
				$attrParts[] = "<quote>".str_replace("'", "\\'", $item)."<quote>";
			}
			if (isset($attrData['delimiters'][$idx])) {
				$code = rtrim(ltrim($attrData['delimiters'][$idx], '{'), '}');
				$data = TemplateCodeParser::parse($code, $parsedPlace, self::$parsedItem);				
				$code = $data['code'];
				self::addTemplateCallbacks($data['callbacks']);
				if (is_array($data['reactNames'])) {
					if (is_array($names)) {
						$names = array_merge($names, $data['reactNames']);
					}
				}
				if (is_array($data['globalNames'])) {
					if (is_array($globals)) {
						$globals = array_merge($globals, $data['globalNames']);
					}
				}
				if ($data['ternary'] && $hasText) {
					$code = '('.$code.')';
				}
				$attrParts[] = $code;
			}
		}

		return '<nq>'.implode('+', $attrParts).'<nq>';
	}

	private static function addTemplateCallbacks($callbacks) {
		if (is_array(self::$class) && !empty($callbacks)) {
			if (!is_array(self::$class['tmpCallbacks'])) {
				self::$class['tmpCallbacks'] = array();
			}
			self::$class['tmpCallbacks'] = array_merge(self::$class['tmpCallbacks'], $callbacks);
		}
	}

	private static function checkPropertyForObfuscation(&$value) {
		if (preg_match('/->>/', $value)) {
			$data = Splitter::split('/->>\s*[\w\-]+/', $value);
			$value = '';
			foreach ($data['items'] as $i => $part) {
				$value .= $part;
				$d = $data['delimiters'][$i];
				if (!empty($d)) {
					$d = preg_replace('/->>\s*/', '', $d);
					self::getObfuscatedClassName($d);
					$value .= $d;
				}
			}
		}
	}

	private static function parseEventAttribute($match, $propValue, &$child, $itemContent, $isComponentTag) {
		$origValue = $propValue;
		$isDispatching = $propValue[0] == '!';
		$isSpecial     = $propValue[0] == ':';
		if ($isDispatching) {
			$propValue = preg_replace('/^\!/', '', $propValue);
			if ($propValue[0] == ':') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
			}
		}
		if ($isSpecial) {
			$propValue = preg_replace('/^:/', '', $propValue);
			if ($propValue[0] == '!') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
			}
			if ($isComponentTag) {
				new Error(self::$errors['specEventAttrInComp'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
			}
		}
		$parts = explode('(', $propValue);
		$propValue = $parts[0];
		if (preg_match('/^this\./', $propValue)) {
			$propValue = preg_replace('/^this\./', '', $propValue);
			$binding = true;
		}
		$eventArgs = '';
		if (isset($parts[1])) {
			if ($isSpecial) {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
			}
			$parts[0] = '';
			$eventArgs = implode('(', $parts);
			if ($eventArgs[strlen($eventArgs) - 1] != ')') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
			}
			preg_match_all('/\{/', $eventArgs, $matches1);
			preg_match_all('/\}/', $eventArgs, $matches2);
			if (count($matches1[0]) != count($matches2[0])) {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));		
			}
			$spacelessArgs = preg_replace('/\s/', '', $eventArgs);
			if (!empty($matches1[0])) {
				if (preg_match('/[^\(,]\{/', $spacelessArgs) || preg_match('/\}[^,\)]/', $spacelessArgs)) {
					new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));		
				}
			}
			$data = TemplateCodeParser::parse('.'.$propValue.preg_replace('/[\{\}]/', '', $eventArgs), 'eventAttribute', $itemContent);
			self::addTemplateCallbacks($data['callbacks']);
			$parts = explode('(', rtrim(trim($data['code']), ')'));
			$parts[0] = '';
			$eventArgs = trim(implode('(', $parts), '(');
		} else {
			if ($isSpecial) {
				$specs = array('stop', 'prevent');
				if (!in_array($propValue, $specs)) {
					new Error(self::$errors['unknownSpecEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, implode(', ', $specs), $itemContent));
				}
			}
		}
		if (empty($propValue)) {
			new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
		}
		if (in_array($propValue, array('this', 'true', 'false', 'null', 'undefined'))) {
			new Error(self::$errors['keywordInEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
		}
		if (is_numeric($propValue)) {
			new Error(self::$errors['numericEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
		}
		if (!preg_match('/^[:\!]*[_a-z]\w+$/i', $propValue)) {
			new Error(self::$errors['incorrectEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $itemContent));
		}
		$once      = false;
		$eventType = strtolower($match);
		$parts     = preg_split('/once/i', $eventType);
		
		if (!is_array($child['e'])) {
			$child['e']	= array();
		}		
		if (isset($parts[1]) && empty($parts[1])) {
			$eventType = preg_replace("/once$/i", '', $eventType);
			$once = true;
		}
		
		$callback = $propValue;
		if (!$isDispatching && !$isSpecial && is_array(self::$class) && !self::hasComponentMethod($callback, self::$class)) {
			new Error(self::$errors['handlerNotFound'], array($callback, self::$templateName, self::$className, 'on'.$match));
		}
		$eventTypeIndex = array_search($eventType, self::$eventTypesShortcuts);
		if ($eventTypeIndex > -1) {
			$eventType = $eventTypeIndex;
		} elseif (!$isComponentTag) {
			new Error(self::$errors['unknownEventAttr'], array('on'.$match, self::$templateName, self::$className, $itemContent));
		}
 		$child['e'][] = $eventType;
		if ($isDispatching) {
			$callback = 'd.b($,"'.$callback.'"';
			if (!empty($eventArgs)) {
				$callback .= ','.$eventArgs;
			}
			$callback .= ')';
		} elseif (!$isSpecial) {
			if (!empty($eventArgs)) {
				$callback .= '.b($,'.$eventArgs.')';
			} elseif ($binding) {
				$callback .= '.b($)';
			}
		}
		$specials = array(
			'prevent' => CONST_PREVENT,
			'stop' => CONST_STOP
		);
		$child['e'][] = '<nq>'.(!$isSpecial ? '$.'.$callback : $specials[$callback]).'<nq>';
		if ($once) {
			$child['e'][] = true;
		}
	}

	private static function getProperComponentData(&$props) {
		$properData = array();
		if (!empty($props['props'])) {
			$properData['ap'] = array();	
		} else {
			$properData['p'] = array();
		}
		foreach ($props as $k => $v) {
			if ($k == 'as') {
				$properData['i'] = $v;
			} elseif ($k == 'props') {
				$properData['p'] = $v;
			} else {
				if (is_array($properData['ap'])) {
					$properData['ap'][$k] = $v;
				} else {
					$properData['p'][$k] = $v;
				}
			}
		}
		if (empty($properData['ap'])) {
			unset($properData['ap']);
		}
		if (empty($properData['p'])) {
			unset($properData['p']);
		}
		$props = $properData;
	}

	private static function correctTagAttributeText($propName, $text) {
		if ($propName == 'st') {
			$text = preg_replace('/:\s+/', ':', $text);
		}
		return $text;
	}

	private static function hasComponentMethod($method, $class) {
		if (is_array($class['functionList']) && in_array($method, $class['functionList'])) return true;
		$parents = $class['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parent) {
				if (is_array(self::$sources[$parent]) && preg_match('/\b'.CONST_PROTO.'\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', self::$sources[$parent]['content'])) {
					return true;
				}
				if (self::hasComponentMethod($method, self::$classes[$parent])) {
					return true;
				}
			}
		}
		return false;
	}

	private static function hasCode($text) {
		return preg_match("/\{[^\}]+\}/", $text);
	}


	private static function parseComponentClassName($value, $content, $isControlName = false) {
		if (!self::hasCode($value)) return !$isControlName ? '<nq>'.$value.'<nq>' : $value;
		$hasReactive = preg_match('/\$\w/', $value);
		if ($hasReactive) {
			if (!$isControlName) {
				new Error(self::$errors['reactComponentName'], array(self::$templateName, self::$className, $content));
			} else {
				new Error(self::$errors['reactControlName'], array(self::$templateName, self::$className, $content));
			}
		}
		self::$parsedItem = $content;
		return self::processCode($value, 'componentClass');
	}

	private static function parseTextNode($content, &$children, &$let = 0, $place = 'textNode', &$names = null) {		
		if (!empty($content) || is_numeric($content)) {
			$regexp = '/\{([^\}]+)\}/';
			preg_match_all($regexp, $content, $matches);
			$codes = $matches[1];
			$count = count($codes);
			if (empty($codes)) {
				$children[] = self::getTextNode($content);
				return;
			}
			$isLet = false;
			$lets = array();
			foreach ($codes as &$code) {
				$data = TemplateCodeParser::parse($code, $place, $content);
				self::addTemplateCallbacks($data['callbacks']);
				if ($data['isLet']) {
					if (!$isLet) {
						$let++;
					}
					$isLet = true;
					$code = '';
					$lets[] = $data['code'];
				} else {
					if ($data['inFunc']) {
						self::wrapInFunction($data['code']);
					}
					if (!empty($data['reactNames']) || !empty($data['localNames']) || !empty($data['globalNames'])) {
						$child = array('v' => '<nq>'.$data['code'].'<nq>');
						if (!empty($data['reactNames'])) {
							$child['n'] = self::getProperChildren($data['reactNames']);
						}
						if (!empty($data['globalNames'])) {
							$child['g'] = self::getProperChildren($data['globalNames']);
						}
						if ($place == 'ifcase') {
							unset($child['n']);
							$child = $child['v'];
						} elseif (!empty(self::$componentsOpen)) {
							$child['$'] = '<nq>$<nq>';
						}
						$code = '<nq>'.str_replace('\\', '', json_encode($child)).'<nq>';
					} else {
						$code = '<nq>'.$data['code'].'<nq>';
					}
					$isLet = false;
				}			
				if (!empty($data['reactNames'])) {
					$names = $data['reactNames'];
				}			
			}
			
			if (!empty($lets)) {
				$letCode = '<nq><let>var '.implode(',', $lets).'<=let><nq>';
			}
			$parts = preg_split($regexp, $content);
			$items = array();			
			foreach ($parts as $i => $part) {					
				if (!empty($part)) {
					$items[] = self::getTextNode($part);
				}
				if (isset($codes[$i])) {
					$items[] = $codes[$i];
				}
			}
			if (!empty($letCode)) {
				array_unshift($items, $letCode);
			}
			foreach ($items as $item) {
				if (!empty($item)) {
					$children[] = $item;
				}
			}			
		}
	}

	private static function parseSwitch($content, $children, &$child, $source, $ifSwitch = false) {
		if (empty($children)) {
			new Error(self::$errors['emptySwitch'], array(self::$templateName, self::$className, !$ifSwitch ? '{switch '.trim($content).'}' : '{if}'));
		}		
		$globals = array();
		$names = array();
		if (!$ifSwitch) {
			$parser = new SwitchCodeParser();
			$data = $parser->parse($content, self::$templateName, self::$className);
			$data['switch'] = self::wrapInNQ($data['switch']);
			self::addTemplateCallbacks($data['m']);
		}		
		$cases = array();
		$default = '';
		self::parseCases($cases, $children, $data['r'], $data['g'], $default, $ifSwitch);
		if (empty($cases)) {
			$child = $default;
			return;
		}
		if (!$ifSwitch) {
			$key = 'sw';
			$child[$key] = $data['switch'];
			$child['cs'] = self::getProperChildren($cases);
		} else {
			$key = 'is';
			$child[$key] = self::getProperChildren($cases);
		}
		$child['c'] = self::getProperChildren($children);
		if (!empty($default)) {
			$child['d'] = self::getProperChildren($default);
		}
		if (!empty($names) || !empty($globals)) {
			self::wrapInFunction($child);
			$child = array($key => $child);
			if (!empty($names)) {
				$names = array_unique($names);
				sort($names);
				$child['n'] = self::getProperChildren($names);
			}
			if (!empty($globals)) {
				$globals = array_unique($globals);
				sort($globals);
				$child['g'] = self::getProperChildren($globals);
			}
		}
	}

	private	static function parseCases(&$cases, &$children, &$names, &$globals, &$default, $isIfSwitch) {
		TemplateCodeParser::setContext($isIfSwitch ? 'ifswitch' : 'switch');		
		$casesList = array();
		$childrenList = array();
		foreach ($children as $item) {
			self::$parsedItem = $item['content'];
			if (!empty($item['case']))
			{
				$code = self::processCode('{case '.$item['case'].'}', 'case', $names, $globals);
				$casesList[] = $code;
				$ch = array();
				self::finish($item['children'], $ch);
				if (!empty($item['lets'])) {
					self::wrapInFunction($ch, '', 'lets', $item);
				}
				$childrenList[] = $ch;
			}
			elseif (isset($item['default']))
			{
				self::processCode('{default '.$item['default'].'}', 'default', $names, $globals);
				$ch = array();
				self::finish($item['children'], $ch);
				if (!empty($item['lets'])) {
					self::wrapInFunction($ch, '', 'lets', $item);
				}
				$default = $ch;
			}
		}
		TemplateCodeParser::setContext(null);
		$cases = $casesList;		
		$children = $childrenList;
	}

	private static function getObfuscatedClassName(&$value) {
		$parts = explode(' ', $value);
		$newClassName = array();
		foreach ($parts as $part) {
			if (!empty($part) && isset(CSSCompiler::$cssClassIndex[$part])) {
				$newClassName[] = CSSCompiler::$cssClassIndex[$part];
			} else {
				if (!empty($part)) {
					$part = self::addToCssClassIndex($part);
				}
				$newClassName[] = $part;
			}
		}
		$value = implode(' ', $newClassName);
	}

	private static function addToCssClassIndex($className) {
		$obfuscatedClassName = CSSObfuscator::generate();
		CSSCompiler::$cssClassIndex[$className] = $obfuscatedClassName;
		return $obfuscatedClassName;
	}

	private static function getTextNode($text) {
		if (strlen($text) > 5) {
			$text = '<nq>'.CONST_TEXTS.'['.self::addTextNode($text).']<nq>';
		}
		return $text;
	}

	private static function addTextNode($text) {
		$index = array_search($text, self::$textNodes);
		if ($index !== false) {
			return $index;
		}
		if ($text == self::$space) {
			$text = '\\'.trim(self::$space, '_');
		}
		self::$textNodes[] = addcslashes($text, "'");
		return count(self::$textNodes) - 1;
	}
}