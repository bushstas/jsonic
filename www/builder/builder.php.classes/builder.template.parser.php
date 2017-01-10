<?php

class TemplateParser 
{	
	private static $calledClasses, $templates, $sources;
	public static $classes;
	private static $regexp = "/\{ *template +\.(\w+) *\}/";
	private static $space = '_u00A0_';
	private static $textNodes = array();
	
	private static $simpleTags = array(
		'br', 'input', 'img', 'hr'
	);

	private static $singleTags = array(
		'else', 'ifempty'
	);
	
	private static $contexts = array(
		'else' => 'if', 'ifempty' => 'foreach'
	);

	private static $forbiddenElements = array(
		'body', 'head', 'html', 'script', 'noscript', 'style', 'meta', 'link', 'title', 'frame',
		'base', 'bgsound', 'blink', 'center', 'comment', 'dir', 'font', 'applet', 'acronym',
		'frameset', 'hgroup', 'isindex', 'marquee', 'nobr', 'noembed', 'noframes', 'object',
		'plaintext', 'strike', 'tt', 'u', 'xmp'
	);

	private static $forbiddenInnerElements = array(
		'a' => array('a', 'form', 'caption'),
		'form' => array('a', 'form'),
		'address' => array('nav'),
		'pre' => array('big', 'img', 'small', 'sub', 'sup')
	);

	private static $onlyParentalElements = array(
		'command' => array('menu'),
		'tbody' => array('table'),
		'thead' => array('table'),
		'tr' => array('table', 'thead', 'tbody'),
		'th' => array('tr'),
		'td' => array('tr'),
		'tfoot' => array('table'),
		'colgroup' => array('table'),
		'col' => array('colgroup', 'table'),
		'area' => array('map'),
		'source' => array('audio', 'video'),
		'dd' => array('dl'),
		'dt' => array('dl'),
		'fieldset' => array('form'),
		'figcaption' => array('figure'),
		'keygen' => array('form'),
		'li' => array('ul', 'ol', 'menu'),
		'optgroup' => array('select'),
		'option' => array('select', 'optgroup'),
		'summary' => array('details')
	);

	private static $allowedInnerElements = array(
		'p' => array(
			'span', 'table', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th', 'a', 'input',
			'img', 'video', 'audio', 'b', 'big', 'button', 'canvas', 'code', 'i',
			'iframe', 'label', 's', 'select', 'strong', 'textarea', 'small', 'abbr',
			'map', 'basefont', 'cite', 'datalist', 'del', 'dfn', 'em', 'embed', 'ins',
			'kbd', 'mark', 'meter', 'output', 'progress', 'q', 'samp', 'sub', 'sup', 'time',
			'var', 'wbr'
		),
		'canvas' => array(),
		'iframe' => array(),
		'textarea' => array(),
		'table' => array(
			'caption', 'tbody', 'thead', 'tr', 'td', 'th', 'colgroup', 'col', 'tfoot'
		),
		'dl' => array('dt', 'dd'),
		'select' => array('optgroup', 'option'),
		'details' => array('summary')
	);

	private static $class, $className, $tmpids, $propsShortcuts,
				   $eventTypesShortcuts, $obfuscate, $tagShortcuts,
				   $templateName, $globalNames,
				   $parsedItem, $globalVarNames, $initials;

	private static $errors = array(
		'noMainTemplate' => 'Шаблон <b>main</b> класса {??} не найден среди прочих',
		'forbiddenTag' => 'Обнаружен недопустимый тег {??} в шаблоне {??} класса {??}<xmp>{?}</xmp>',
		'noClosingTag' => 'Обнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'tagInsideTag' => 'Обнаружена недопустимая вложенность: тег {??} внутри тега {??} в шаблоне {??} класса {??}<br>Данный тег {?}-й по счету открывающийся тег {??}<xmp>{?}</xmp>',
		'tagOutsideProperTag' => 'Обнаружена недопустимая вложенность: тег {??} вне {?} {??} в шаблоне {??} класса {??}<br>Данный тег {?}-й по счету открывающийся тег {??}<xmp>{?}</xmp>',
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
		'operatorOutOfPlace' => 'Обнаружен оператор {??} вне границ оператора {??} в шаблоне {??} класса {??}',
		'operatorInInnerLevel' => 'Обнаружен оператор {??} не на одном уровне с оператором {??} в шаблоне {??} класса {??}',
		'doubleOperator' => 'Обнаружен оператор {??} внутри другого оператора {??} в шаблоне {??} класса {??}',
		'fewSameOperators' => 'Обнаружено дублирование оператора {??} внутри оператора {??} в шаблоне {??} класса {??}',
		'loadingOperatorWithoutLoader' => 'Обнаружено использование одного из операторов <b>loading, loader</b> в шаблоне {??} класса {??}. У данного класса отсутствует initial параметр <b>loader</b>'
	);

	public static function init($params) {
		self::$initials = $params['initialsParser']->get();
		self::$calledClasses = $params['classNames'];
		self::$classes = $params['classes'];
		self::$sources = $params['sources'];
		self::$templates = $params['templates'];
		self::$propsShortcuts = Props::getList();
		self::$eventTypesShortcuts = Events::getList();
		self::$tagShortcuts = Tags::getList();
		self::$obfuscate = $params['obfuscateCss'];
		self::$globalNames = JSGlobals::getUsedNames();
		self::$globalVarNames = JSGlobals::getVarNames();
		$varNames = array_values(self::$globalVarNames);
		TemplateCodeParser::setGlobalNames(self::$globalNames, $varNames, $params['utilsFuncs'], $params['userUtilsFuncs']);
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
			if (!empty($className) && !preg_match("/\{template +\.main *\}/", $template) && !self::hasParentMainTemplate($class) && in_array($className, self::$calledClasses)) {
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
			$templates[] = self::getParsedTemplate($templateContents[$i]);
		}

		$isSingle = count($templates) == 1;
		$templateFunctions = array();
		foreach ($templates as $template) {
			$data = json_encode($template['children']);
			if ($data == '[]') {
				$data = '';
			} else {
				$data = str_replace('"', "'", $data);		
				$data = str_replace("\'", "'", $data);				
				$data = str_replace('<this>', '$.', $data);
				$data = preg_replace("/'<nq>/", '', $data);
				$data = preg_replace("/<nq>'/", '', $data);
				$data = preg_replace("/<nq>/", '', $data);

				$data = str_replace('\\', '', $data);
				$data = str_replace('_u00A0_', '\\u00A0', $data);
							
				$data = str_replace("<=let>,<let>", ";", $data);
				$data = str_replace("<let>", "function(){", $data);
				$data = preg_replace("/<=let>,*/", ";return[", $data);
				$data = preg_replace("/,'<\/let>'/", "]}", $data);
				$data = preg_replace("/<\/let>'/", "']}", $data);
				$data = str_replace("<quote>", "'", $data);
				$data = preg_replace("/''\+|\+''/", "", $data);
				$data = preg_replace("/return\[(\d+)\]/", "return $1", $data);
			}
			$templateFunctions[] = array('content' => $data, 'name' => $template['name'], 'let' => $template['let']);
		}
		return $templateFunctions;
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

	private static function getParsedTemplate($content) {
		$html = preg_replace(self::$regexp, '', $content);
		$html = str_replace('->>', "#classobfus#", $html);
		$html = preg_replace('/<(:*\w+)([^>]*)\/>/', "<$1$2></$1>", $html);
		$html = str_replace('#classobfus#', '->>', $html);
		$html = preg_replace('/<\/(img|br|hr|input)>/', '', $html);
		$parts = preg_split('/\{\/template\}/', $html);
		$html = $parts[0];

		$regexp = "/\{[^\}]+\}/";
		preg_match_all($regexp, $html, $matches);
		$matches = $matches[0];
		foreach ($matches as &$match) {
			$match = str_replace('>', '_#_MORE_#_', $match);
		}
		$parts = preg_split($regexp, $html);
		$html = '';
		foreach ($parts as $i => $part) {
			$html .= $part;
			if (isset($matches[$i])) {
				$html .= $matches[$i];
			}
		}
		$regexp = "/(<\/*:*[a-z]+[^>]*>|\{\s*\/*foreach\b[^\}]*\}|\{\s*\/*if\b[^\}]*\}|\{\s*else\s*\}|\{\s*ifempty\s*\}|\{\s*\/*switch\b[^\}]*\})/i";
		preg_match_all($regexp, $html, $matches);
		$tags = implode('_#_TMPDELIMITER_#_', $matches[1]);
		$tags = explode('_#_TMPDELIMITER_#_', str_replace('_#_MORE_#_', '>', $tags));
		$parts = preg_split($regexp, $html);
		$list = array();
		for ($j = 0; $j < count($parts); $j++) {
			$part = $parts[$j];
			if (!empty($part)) {
				$list[] = array('type' => 'text', 'content' => $part);
			}
			if (isset($tags[$j]) && !empty($tags[$j])) {
				preg_match('/^[<\{]\s*\/*(:*[a-z]\w*) */i', $tags[$j], $match);
				$tagName = strtolower($match[1]);
				$tagContent = $tags[$j];
				$isClosing = self::isTagClosing($tagName, $tagContent);
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
		self::checkHtmlTagStructure($list);
		$children = self::getHtmlChildren($list, $isLet, false);

		$let = '';
		if (isset($children[0]) && count($children) == 1) {
			$children = $children[0];
			if ($isLet > 0) {				
				$let = self::getLet($children);
				$children = '';
			}
		} elseif ($isLet > 0) {
			$letItem = '';
			$items = array();
			foreach ($children as $item) {
				if (is_string($item) && preg_match('/^<nq><let>/', $item)) {
					if (!empty($letItem)) {
						$item = preg_replace('/<nq><let>var /', '', $item);
						$letItem = preg_replace('/<=let><nq>/', '', $letItem);
						$letItem = $letItem.','.$item;
					} else {
						$letItem = $item;
					}
				} else {
					$items[] = $item;
				}
			}
			$let = self::getLet($letItem);
			$children = $items;
			if (count($children) == 1) {
				$children = $children[0];
			}
		}
		return array('name' => self::$templateName, 'children' => $children, 'let' => $let);
	}

	private static function getLet($content) {
		$parts = explode('<let>', $content);
		$parts = explode('<=let>', $parts[1]);
		return $parts[0];
	}

	private static function isTagClosing($tagName, $tagContent) {
		if (self::isSimpleTag($tagName)) return false;
		return preg_match("/^[<\{]\//", $tagContent) ? 1 : 0;
	}

	private	static function isSimpleTag($tagName) {
		return in_array($tagName, self::$simpleTags);
	}

	private	static function isSingleTag($tagName) {
		return in_array($tagName, self::$singleTags);
	}

	private	static function checkHtmlTagStructure($list) {
		$all = array();
		$allTypes = array();
		$indexes = array();
		$opened = array();
		$openedHtml = array();
		$openedData = array();
		
		$closed = array();
		$opened2 = array();
		$openedTags = array();
		$closedTags = array();
		$lastType = '';
		$ix = 0;
		$aixs = array();
		foreach ($list as $aix => $item) {
			$tn = $item['tagName'];
			if (!empty($tn)) {
				if (!$item['isSingle']) {
					if ($item['isClosing'] == 0) {
								
						if (!isset($openedTags[$tn])) {
							$openedTags[$tn] = 0;
						}

						$last = $openedHtml[count($openedHtml) - 1];					

						$openedTags[$tn]++;
						if (in_array($tn, self::$forbiddenElements)) {
							new Error(self::$errors['forbiddenTag'], array(strtoupper($tn), self::$templateName, self::$class['name'], $item['content']));
						}
						
						if (isset(self::$forbiddenInnerElements[$last]) && in_array($tn, self::$forbiddenInnerElements[$last])) {
							new Error(self::$errors['tagInsideTag'], array(strtoupper($tn), strtoupper($last), self::$templateName, self::$class['name'], $openedTags[$tn], strtoupper($tn), $item['content']));
						}
						if (isset(self::$onlyParentalElements[$tn]) && !in_array($last, self::$onlyParentalElements[$tn])) {
							new Error(self::$errors['tagOutsideProperTag'], array(strtoupper($tn), count(self::$onlyParentalElements[$tn]) > 1 ? 'тегов' : 'тега', strtoupper(implode(', ', self::$onlyParentalElements[$tn])), self::$templateName, self::$class['name'], $openedTags[$tn], strtoupper($tn), $item['content']));
						}
						if (isset(self::$allowedInnerElements[$last]) && !in_array($tn, self::$allowedInnerElements[$last])) {
							new Error(self::$errors['tagInsideTag'], array(strtoupper($tn), strtoupper($last), self::$templateName, self::$class['name'], $openedTags[$tn], strtoupper($tn), $item['content']));
						}
						$opened[] = $tn;
						if (self::isHtmlTag($tn)) {
							$openedHtml[] = $tn;
						}
						$openedData[] = array(
							'tag' => $tn,
							'content' => $item['content'],
							'orderNumber' => $openedTags[$tn]
						);
						$opened2[] = $tn;
						$lastType = 'open';
						$all[] = $tn;
						$allTypes[] = 'open';
						$indexes[] = $ix;
						$aixs[$ix] = $aix;
						$ix++;
					} else {
						$prev = $opened[count($opened) - 1];
						if (!isset($prev) || $prev != $tn) {

							if (in_array($tn, $opened)) { 								
								$prevIndex = array_pop($indexes);
								$realIndex = $aixs[$prevIndex];
								$count = 0;
								foreach ($all as $i => $tag) {
									if ($tag == $prev && $allTypes[$i] == 'open') $count++;
									if ($i == $realIndex) break;
								}
								$object = self::getTagTypeName($prev);
								new Error(
									self::$errors['noClosingTag'], 
									array(
										$object, strtoupper($prev), self::$templateName, self::$class['name'], $object, $count, $object, strtoupper($prev), $list[$realIndex]['content']
									)
								);
								
							} else {
								if ($lastType == 'open') {
									$prev2 = $opened2[count($opened2) - 1];
								} else {
									$prev2 = $closed[count($closed) - 1];
								}
								$openedData = array_reverse($openedData);
								if (isset($openedData[0])) {
									extract($openedData[0]);
								}

								$object = self::getTagTypeName($tn);
								$object2 = self::getTagTypeName($prev2, 'а');
								
								$typeTag = $lastType == 'open' ? 'открывающегося' : 'закрывающегося';
								if ($prev != $prev2 && $lastType == 'open') {
									$typeTag = '';
								}
								if (!empty($prev2)) {
									if (isset($content)) {
										$object3 = self::getTagTypeName($tag);
										new Error(
											self::$errors['extraClosingTag'], array($object, strtoupper($tn), self::$templateName, self::$class['name'], $object, $openedTags[$prev2], $typeTag, $object2, strtoupper($prev2), $list[$aix - 1]['content'],
											strtoupper($tag), $object3, $orderNumber, $object3, strtoupper($tag), $content
										));
									} else {
										new Error(
											self::$errors['extraClosingTag2'], array($object, strtoupper($tn), self::$templateName, self::$class['name'], $object, $openedTags[$prev2], $typeTag, $object2, strtoupper($prev2), $list[$aix - 1]['content']
										));
									}
								} else {
									new Error(
										self::$errors['extraClosingTag3'], array($object, strtoupper($tn), self::$templateName, self::$class['name']
									));
								}
							}
						}
						if (!isset($closedTags[$tn])) {
							$closedTags[$tn] = 0;
						}
						$closedTags[$tn]++;
						$closed[] = $tn;
						array_pop($opened);
						if (self::isHtmlTag($tn)) {
							array_pop($openedHtml);
						}
						array_pop($openedData);
						array_pop($indexes);
						$all[] = $tn;
						$lastType = 'closed';
						$allTypes[] = 'closed';
					}
				} else {
					if ($tn == 'ifempty' && !in_array('foreach', $opened)) {
						new Error(self::$errors['operatorOutOfPlace'], array('ifempty', 'foreach', self::$templateName, self::$className));
					} elseif ($tn == 'else' && !in_array('if', $opened)) {
						new Error(self::$errors['operatorOutOfPlace'], array('else', 'if', self::$templateName, self::$className));
					}
					if (!isset($openedTags[$tn])) {
						$openedTags[$tn] = 0;
					}
					$opened2[] = $tn;
					$openedTags[$tn]++;
					$lastType = 'open';
					$all[] = $tn;
					$allTypes[] = '';
				}
			}
		}
		if (!empty($openedData)) {
			$openedData = array_reverse($openedData);
			extract($openedData[0]);			
 			$object = self::getTagTypeName($tag);
		 	new Error(self::$errors['noClosingTag2'], array(
		 		$object, strtoupper($tag), self::$templateName, self::$class['name'], $object, $orderNumber, $object, strtoupper($tag), $content
		 	));
		}
	}

	private	static function isHtmlTag($tn) {
		return !($tn == 'if' || $tn == 'switch' || $tn == 'foreach'|| $tn == 'else' || $tn == 'ifempty');
	}

	private	static function getTagTypeName($tn, $ending = '') {
		return ($tn == 'if' || $tn == 'switch' || $tn == 'foreach'|| $tn == 'else' || $tn == 'ifempty' ? 'оператор' : 'тег').$ending;
	}

	private	static function getHtmlChildren($list, &$let) {
		if (empty($list)) {
			return array();
		}
		$children = array();
		$elseChildren = array();
		$ifEmptyChildren = array();
		$currentList = array();
		$isElse = false;
		$isIfempty = false;
		$currentIf = null;

		for ($i = 0; $i < count($list); $i++) {
			$item = $list[$i];

			$tagName = trim($item['tagName']);
			if ($item['type'] == 'text') {
				if (!$isElse) {
					self::parseTextNode($item['content'], $children, $let);
				} else {
					self::parseTextNode($item['content'], $elseChildren, $let);
				}
			} elseif ($tagName == 'br') {
				if (!$isElse) {
					$children[] = '<br>';
				} else {
					$elseChildren[] = '<br>';
				}
			} else {
				$child = self::getHtmlChild($item, $i, $list, $isElse, $isIfempty);
				if ($child === null) {
					continue;
				}
				if ($isElse) {
					$elseChildren[] = $child;
				} elseif ($isIfempty) {
					$ifEmptyChildren[] = $child;
				} else {
					$children[] = $child;
				}
			}
		}
		if (!empty($elseChildren)) {
			return array('c' => $children, 'e' => $elseChildren[0]['c']);
		} elseif (!empty($ifEmptyChildren)) {
			return array('c' => $children, 'ie' => $ifEmptyChildren);
		} else {
			return $children;
		}
	}

	private	static function getHtmlChild($item, &$i, $list, &$isElse = null, &$isIfempty = null) {
		$child = array();
		$tagName = trim($item['tagName']);
		if ($tagName == 'else') {
			$isElse = true;
		} elseif ($tagName == 'ifempty') {
			$isIfempty = true;
		}
		$content = $item['content'];
		if (self::isSimpleTag($tagName))
		{
			$child = array('t' => self::getTagIndex($tagName));
			self::getTagProperties($item, $child);
		}
		elseif ($tagName == 'template' || $tagName == 'include' || $tagName[0] == ':')
		{
			if (!$item['isClosing']) {
				self::getTemplateProperties($item['content'], $child, $tagName == 'include');
				$childrenList = self::gatherChildren($list, $i, $tagName);
				Printer::log($childrenList);
			} else {
				return null;
			}
		}
		elseif ($tagName == 'component' || $tagName == 'control' || $tagName == 'menu' || $tagName == 'form')
		{
			if (!$item['isClosing']) {
				self::getTagProperties($item, $child, true);
			} else {
				return null;
			}
		}
		else
		{
			
			$toProper = false;

			if ($tagName == 'if') {
				preg_match("/^\{\s*if\b\s*([^\}]+)\}/i",  $item['content'], $match);
				if (!is_string($match[1])) $match[1] = '';
				$ifContent = $match[1];
				$ifContentIsEmpty = preg_replace('/\s/', '', $ifContent) === '';
			}
			
			$childrenList = self::gatherChildren($list, $i, $tagName);
		
			$isLet = 0;
			if ($ifContentIsEmpty) {
				self::parseCases($childrenList, $child, true);
			} elseif ($tagName == 'switch') {
				self::parseSwitch($item, $childrenList, $child);
			} else {
				$data = self::getHtmlChildren($childrenList, $isLet);
				if ($isLet > 0) {
					for ($ii = 0; $ii < $isLet; $ii++) {
						if (is_array($data)) {
							if (!isset($data['c'])) {
								$data[] = '</let>';
							} else {
								$data['c'][] = '</let>';
							}
						} else {
							$data .= '</let>';
						}
					}
				}
				if (!empty($data)) {
					$toProper = true;
					if (!is_array($data) || !isset($data['c'])) {
						$child['c'] = $data;
					} else {
						$child['c'] = $data['c'];
						if (isset($data['e'])) {
							$child['e'] = $data['e'];
						}
						if (isset($data['ie'])) {
							$child['ie'] = $data['ie'];
						}
					}
				}
				switch ($tagName) {
					case 'if':
						self::parseIf($match[1], $child);
						if (is_array($child) && !empty($child['e'])) {
							$json = json_encode($child['e']);
							if (preg_match('/\$\.g\(/', $json)) {
								self::wrapInFunction($child['e']); 
							} else {
								$child['e'] = self::getProperChildren($child['e']);
							}
						}
					break;
				
					case 'foreach':
						self::parseForeach($item, $child);
						//if (!empty())
					break;

					case 'else':
					case 'ifempty':
					break;

					default:
						if ($tagName == 'forma') $tagName = 'form';
						$child['t'] = self::getTagIndex($tagName);
						self::getTagProperties($item, $child);
				}
			}
		}
		if ($toProper) {
			if (is_array($child['c'])) {
				$child['c'] = self::getProperChildren($child['c']);
			}
			if (is_array($child['e'])) {
				$child['e'] = self::getProperChildren($child['e']);
			}
			if (isset($child['ie'])) {
				$child['ie'] = self::getProperChildren($child['ie']);
			}
		}
		return $child;
	}

	private	static function gatherChildren($list, &$i, $tagName) {
		$t = $list[$i];
		$childrenList = array();
		$openedTagsCount = 1;
		$context = array();
		$levels = array();
		$operators = array();
		$level = 1;
		foreach (self::$contexts as $k => $v) {
			$levels[$v] = array();
			$operators[$k] = 0;
			if ($tagName == $v) {
				$context[] = $v;
				$levels[$v][] = 1;
			}
		}
	
		$i++;
		while (isset($list[$i])) {
			if ($list[$i]['type'] == 'tag') {
				if (!$list[$i]['isClosing']) {
					if (!$list[$i]['isSingle']) $level++;
					if ($list[$i]['tagName'] == $tagName) $openedTagsCount++;
				} elseif ($list[$i]['isClosing']) {
					if (!$list[$i]['isSingle']) $level--;
					if ($list[$i]['tagName'] == $tagName) $openedTagsCount--;
				}
			}
			if ($openedTagsCount > 0) {
				$childrenList[] = $list[$i];
				foreach (self::$contexts as $k => $v) {
					if ($list[$i]['tagName'] == $v) {
						if (!$list[$i]['isClosing']) {
							$context[] = $v;
							$levels[$v][] = $level;
						} else {
							array_pop($context);
							array_pop($levels[$v]);
							$operators[$k]--;
						}
					} elseif ($list[$i]['tagName'] == $k) {
						$operators[$k]++;
						$contextLevel = $levels[$v][count($levels[$v]) - 1];
						if (!in_array($v, $context) || $contextLevel != $level) {
							if ($operators[$k] > 1) {
								new Error(self::$errors['doubleOperator'], array($k, $k, self::$templateName, self::$className));
							} elseif ($contextLevel > 0) {
								new Error(self::$errors['operatorInInnerLevel'], array($k, $v, self::$templateName, self::$className));
							}
							new Error(self::$errors['operatorOutOfPlace'], array($k, $v, self::$templateName, self::$className));
						} elseif ($operators[$k] > 1) {							
							new Error(self::$errors['fewSameOperators'], array($k, $v, self::$templateName, self::$className));
						}
					}
				}
				$i++;
			} else break;
		}
		return $childrenList;
	}

	private	static function parseCases($childrenList, &$child, $isIfSwitch = false) {
		$caseType = $isIfSwitch ? 'ifcase' : 'textNode';
		$switchType = $isIfSwitch ? 'ifswitch' : 'switch';
		$errorType = $isIfSwitch ? 'ifCaseExpected' : 'caseExpected';
		TemplateCodeParser::setContext($switchType);
		$shouldBeCase = true;
		$switch = array();
		$children = array();
		$defaultCase = false;
		$defaultCases = 0;
		$let = 0;
		for ($j = 0; $j < count($childrenList); $j++) {
			$item = $childrenList[$j];
			if ($item['type'] == 'text') {
				$data = Splitter::split('/\{([^\}]*)\}/', $item['content']);
				$items = $data['items'];
				$codes = $data['delimiters'];
				if ($shouldBeCase && preg_replace('/\s/', '', $items[0]) != '') {
					new Error(self::$errors[$errorType], array(self::$templateName, self::$className, $items[0]));
				}

				for ($i = 0; $i < count($items); $i++) {
					$part = $items[$i];
					if (!empty($part)) {
						$children[] = $part;
					}
					if (isset($codes[$i])) {
						$code = $codes[$i];

						$isCase = preg_match('/\s*case\b/', $code);
						$isDefault = false;
						if (!$isCase) {
							$isDefault = preg_match('/\s*default\b/', $code);
						}
						if ($shouldBeCase && !$isCase && !$isDefault) {
							new Error(self::$errors[$errorType], array(self::$templateName, self::$className, $code));
						}
						if ($isCase || $isDefault) {
							if ($defaultCase) {
								if ($defaultCases > 0) {
									new Error(self::$errors['fewDefaults'], array('if', self::$templateName, self::$className));
								}
								$switch[] = array(
									'default' => '',
									'children' => $children
								);
							} else if (!empty($case)) {
								if ($isDefault) {
									$defaultCases++;
									$defaultCase = true;
								} else {
									$defaultCase = false;
								}
								$switch[] = array(
									'case' => $case,
									'children' => $children
								);
							}
							$children = array();
						}

						if ($isCase) {
							$case = array();
							self::parseTextNode($code, $case, $let, $caseType);
							$case = $case[0];
							$shouldBeCase = false;
						} elseif (!$isDefault) {
							self::parseTextNode($code, $children, $let);
						}
					}
				}
			} else {
				if ($shouldBeCase) {
					new Error(self::$errors[$errorType], array(self::$templateName, self::$className, $item['content']));
				}		

				$childList = self::gatherChildren($childrenList, $j, $item['tagName']);
				array_unshift($childList, $item);
				$childList[] = $childrenList[$j];
				TemplateCodeParser::setContext(null);
				$children = array_merge($children, self::getHtmlChildren($childList, $let));
				TemplateCodeParser::setContext($switchType);
			}
		}
		if ($defaultCase) {
			$switch[] = array(
				'default' => '',
				'children' => $children
			);
		} else {
			$switch[] = array(
				'case' => $case,
				'children' => $children
			);
		}		

		$child['is'] = array();
		$child['c'] = array();
		foreach ($switch as $case) {
			if (isset($case['default'])) {
				if (!empty($case['children'])) {
					$child['d'] = self::getProperChildren($case['children']);
				}
			} else {
				$child['is'][] = $case['case'];
				if (!empty($case['children'])) {
					$child['c'][] = self::getProperChildren($case['children']);
				} else {
					$child['c'][] = '';
				}
			}
		}
		TemplateCodeParser::setContext(null);
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

	private static function getProperChildren($children, $addQuotes = false) {
		$children = self::getProperChild($children);
		if (is_string($children) && $addQuotes) {
			$children = "'".trim($children, "'")."'";
		}
		return is_array($children) ? '<nq>'.preg_replace('/\\\(?=[\'"])/', '', json_encode($children)).'<nq>' : $children;
	}

	private static function wrapInFunction(&$children, $args = '') {
		$children = '<nq>function('.$args.'){return '.self::getProperChildren($children).'}<nq>';
	}

	private static function parseForeach($item, &$child) {
		if (is_array($child['ie'])) {
			$child['ie'] = self::getProperChild($child['ie']);
			if (isset($child['ie']['c'])) {
				$child['ie'] = $child['ie']['c'];
			}
		}
		if (empty($child['ie'])) {
			unset($child['ie']);	
		}
		$child['h'] = $child['c'];
		unset($child['c']);
		$content = ltrim(rtrim($item['content'], '}'), '{');
		$data = TemplateCodeParser::parse($content, 'foreach');
		$child['p'] = $data['items'];
		if (!empty($data['reactNames'])) {
			$child['n'] = self::getProperChildren($data['reactNames']);
		}
		if ($data['reactiveItems']) {
			self::wrapInFunction($child['p']);
		}
		$args = array($data['value']);
		if (isset($data['key'])) {
			$args[] = $data['key'];
		}
		if ($data['right']) {
			$child['r'] = 1;
		} elseif ($data['random']) {
			$child['ra'] = 1;
		}
		if (!empty($data['limit'])) {
			if ($data['reactiveLimit']) {
				self::wrapInFunction($data['limit']);
			}
			$child['l'] = $data['limit'];
		}
		$args = implode(',', $args);
		self::wrapInFunction($child['h'], $args);
		return $child;
	}

	private static function parseSwitch($item, $childrenList, &$child) {
		$code = rtrim(ltrim($item['content'], '{'), '}');
		$data = TemplateCodeParser::parse($code, 'switch', self::$parsedItem);
		
		self::parseCases($childrenList, $child);
		
		$switch = '[<nq>'.$data['code'].'<nq>,'.self::getProperChildren($child['is'], true).','.self::getProperChildren($child['c'], true);
		if (!empty($child['d'])) {
			$switch .= ','.self::getProperChildren($child['d'], true);
		}
		$switch .= ']';
		if (!empty($data['reactNames'])) {
			$child['n'] = self::getProperChildren($data['reactNames']);
			$child['sw'] = '<nq>function(){return'.$switch.'}<nq>';			
		} else {
			$child['sw'] = '<nq>'.$switch.'</nq>';
		}
		unset($child['d']);
		unset($child['c']);
		unset($child['is']);
	}

	private static function getTemplateProperties($html, &$child, $isInclude = false) {
		$tmpName = '';
		if (preg_match('/^<:{1,2}(\w+)/', $html, $match)) {
			$tmpName = $match[1];
			$child['tmp'] = '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>';
			if ($html[2] == ':') {
				$isInclude = true;
			}
		}
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
			$child['tmp'] = '<nq>includeGeneralTemplate'.ucfirst($tmpName).'<nq>';
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
		if (!empty($else)) {
			$child['e'] = self::processCode($else, 'else');
		}
		self::parseIf($ifCondition, $child);
	}

	private static function parseIf($ifCondition, &$child) {
		$hasCode = preg_match('/\$\w/', $ifCondition);
		if (is_string(self::$class) && $hasCode) {
			new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $ifCondition));
		}
		$names = array();
		if ($ifCondition[0] != '{') $ifCondition = '{'.$ifCondition.'}';
		$ifCondition = self::processCode($ifCondition, 'if', $names);
			
		$child['i'] = $ifCondition;
		if (!empty($names)) {
			$child['n'] = $names;
			if (empty($child['c'])) {
				$child['c'] = "<nq>function(){return''}</nq>";			 
			} else {
				$child['c'] = '<nq>function(){return '.str_replace('\\', '', json_encode($child['c'])).'}<nq>';
			}
		} else {
			$then = '""';
			$else = '""';
			if (!empty($child['c'])) {
				$then = is_array($child['c']) ? (count($child['c']) > 1 || is_array($child['c'][0]) ? str_replace('\\', '', json_encode($child['c'])) : $child['c'][0]) :  $child['c'];
			}
			if (is_array($child['e'][0]) && isset($child['e'][0][0])) {
				$child['e'] = $child['e'][0];
			}
			if (!empty($child['e'])) {
				$else = is_array($child['e']) ? (count($child['e']) > 1 || is_array($child['e'][0]) ? str_replace('\\', '', json_encode($child['e'])) : $child['e'][0]) :  $child['e'];
			}
			$child = '<nq>'.str_replace('<nq>', '', $child['i']).'?'.$then.':'.$else.'<nq>';
		}
	}

	private static function getTagIndex($tagName) {
		$tagNameIndex = array_search($tagName, self::$tagShortcuts);
		return $tagNameIndex !== false ? $tagNameIndex : $tagName;
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

	private static function processCode($code, $parsedPlace = 'elementAttribute', &$names = null, $isObfClName = false) {
		$attrParts = array();
		if (is_array(self::$class) && !is_array(self::$class['tmpCallbacks'])) {
			self::$class['tmpCallbacks'] = array();
		}
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
		$inFunc = false;
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
				if ($data['inFunc']) {
					$inFunc = true;
				}
				if (!empty($data['callbacks'])) {
					if (is_array(self::$class)) {
						self::$class['tmpCallbacks'] = array_merge(self::$class['tmpCallbacks'], $data['callbacks']);
					} else {
						/////// ошибка
					}
				}
				if (is_array($data['reactNames'])) {
					if (is_string(self::$class)) {
						new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $code));
					}
					if (is_array($names)) {
						$names = array_merge($names, $data['reactNames']);
					}
				}
				if ($data['ternary'] && $hasText) {
					$code = '('.$code.')';
				}
				$attrParts[] = $code;
			}
		}
		if (!$inFunc && is_array($names)) {
			if (in_array($parsedPlace, array('if', 'else'))) {
				$inFunc = count($names) > 0;	
			} else {
				$inFunc = count($names) > 1;
			}
		}
		$attrContent = implode('+', $attrParts);
		if ($inFunc && $parsedPlace != 'componentAttribute' && $parsedPlace != 'elementAttribute') {
			$attrContent = '<nq>function(){return '.$attrContent.'}<nq>';
		} else {
			$attrContent = '<nq>'.$attrContent.'<nq>';
		}
		return $attrContent;
	}

	private static function getTagProperties($item, &$child, $isComponentTag = false) {
		self::$parsedItem = $item['content'];
		$props = array();
		$names = array();
		$ifCondition = false;
		$else = null;
		list($propNames, $propValues) = self::getTagAttrs($item['content']);
		$hasNames = false;
		for ($i = 0; $i < count($propNames); $i++) {		
			$propName = $propNames[$i];
			$propValue = trim($propValues[$i]);
			$hasCode = self::hasCode($propValue);
			$fullPropName = $propName;
			$isObfClName = self::$obfuscate === true && $fullPropName == 'class';
			$isTag = !$isComponentTag && !isset($child['tmp']);
			if (!$isTag && in_array($propValue, array('true', 'false', 'null', 'undefined'))) {
				$propValue = '<nq>'.$propValue.'<nq>';
			}
			if (is_numeric($child['t']) || $isComponentTag) {
				if ($propName == 'scope') {
					$props[self::$propsShortcuts[$propName]] = 1;
					continue;
				}
				if ($propName == 'if') {
					$ifCondition = $propValue;
					continue;
				}
				if ($propName == 'else') {
					$else = $propValue;
					continue;
				}
				
				if ($isTag && isset(self::$propsShortcuts[$propName])) {
					$propName = self::$propsShortcuts[$propName];
				} else {
					$propName = preg_replace('/^data-/', '_', $propName);
					if ($isComponentTag) {
						if ($propName == 'class') {
							$child['cmp'] = self::parseComponentClassName($propValue, $item['content']);
							continue;
						} elseif ($item['tagName'] == 'control' && $propName == 'name') {
							$child['nm'] = self::parseComponentClassName($propValue, $item['content'], true);
							continue;
						}
					}
				}
				if (preg_match("/\bon(\w{3,})/i", $propName, $match)) {
					self::parseEventAttribute($match[1], $propValue, $child, $item, $isComponentTag);
					continue;
				}
			}
			$props[$propName] = $propValue;
			if ($hasCode) {
				$names[$propName] = array();
				$parsedPlace = $isComponentTag ? 'componentAttribute' : 'elementAttribute';
				$code = self::processCode($propValue, $parsedPlace, $names[$propName], $isObfClName);
				$props[$propName] = self::correctTagAttributeText($propName, $code);
				$names[$propName] = array_unique($names[$propName]);
				sort($names[$propName]);
				if (count($names[$propName]) == 1) {
					$names[$propName] = $names[$propName][0];
				}
				if (empty($names[$propName])) {
					unset($names[$propName]);
				} else {
					$hasNames = true;
				}
			} else if ($isObfClName) {
				self::getObfuscatedClassName($propValue);
				$props[$propName] = $propValue;
			}
		}
	
		if ($isComponentTag) {
			$comp = '<component class="ComponentClassName">';
			if ($item['tagName'] == 'control') {
				$comp = '<control class="ControlClassName" name="controlName">';
			} elseif ($item['tagName'] == 'menu') {
				$comp = '<menu class="MenuClassName">';
			} elseif ($item['tagName'] == 'form') {
				$comp = '<form class="FormClassName">';
			}
			if (empty($child['cmp'])) {
				new Error(self::$errors['unknownComponent'], array(self::$templateName, self::$className, $item['content'], $comp));
			}
			if ($item['tagName'] == 'control' && empty($child['nm'])) {
				new Error(self::$errors['controlWithoutName'], array($child['cmp'], self::$templateName, self::$className, $item['content'], $comp));
			}
		}
		if (!empty($props)) {
			$child['p'] = $props;
		}
		if (!empty($names)) {
			$child['n'] = $names;
		}
		if ($isComponentTag) {
			if (is_array($child['p'])) {
				foreach ($child['p'] as $k => $v) {
					$v = strip_tags(trim($v));
					if ($v[0] == '^') {
						unset($child['p'][$k]);
						if (!is_array($child['w'])) {
							$child['w'] = array();
						}
						$child['w'][] = $k;
						$child['w'][] = ltrim($v, '^');
					} elseif ($v[0] == '%') {
						$child['p'][$k] = '<nq>$.getTemplate'.ucfirst(ltrim($v, '%')).'<nq>';
					}
				}				
				if (empty($child['p'])) {
					unset($child['p']);
				} else {					
					self::getProperComponentData($child);
				}
			}
		}
		if ($hasNames) {
			$child['p'] = '<nq>function(){return'.self::getProperChildren($child['p']).'}<nq>';
		}
		if (!empty($ifCondition) || !empty($else)) {
			self::addIfConditionToChild(trim($ifCondition), trim($else), $child);
		}
	}

	private static function parseEventAttribute($match, $propValue, &$child, $item, $isComponentTag) {
		$origValue = $propValue;
		$isDispatching = $propValue[0] == '!';
		$isSpecial     = $propValue[0] == ':';
		if ($isDispatching) {
			$propValue = preg_replace('/^\!/', '', $propValue);
			if ($propValue[0] == ':') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
			}
		}
		if ($isSpecial) {
			$propValue = preg_replace('/^:/', '', $propValue);
			if ($propValue[0] == '!') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
			}
			if ($isComponentTag) {
				new Error(self::$errors['specEventAttrInComp'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
			}
		}
		$parts = explode('(', $propValue);
		$propValue = $parts[0];
		$eventArgs = '';
		if (isset($parts[1])) {
			if ($isSpecial) {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
			}
			$parts[0] = '';
			$eventArgs = implode('(', $parts);
			if ($eventArgs[strlen($eventArgs) - 1] != ')') {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
			}
			preg_match_all('/\{/', $eventArgs, $matches1);
			preg_match_all('/\}/', $eventArgs, $matches2);
			if (count($matches1[0]) != count($matches2[0])) {
				new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));		
			}
			$spacelessArgs = preg_replace('/\s/', '', $eventArgs);
			if (!empty($matches1[0])) {
				if (preg_match('/[^\(,]\{/', $spacelessArgs) || preg_match('/\}[^,\)]/', $spacelessArgs)) {
					new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));		
				}
			}
			$data = TemplateCodeParser::parse('.'.$propValue.preg_replace('/[\{\}]/', '', $eventArgs), 'eventAttribute');
			$parts = explode('(', rtrim(trim($data['code']), ')'));
			$parts[0] = '';
			$eventArgs = trim(implode('(', $parts), '(');
		} else {
			if ($isSpecial) {
				$specs = array('stop', 'prevent');
				if (!in_array($propValue, $specs)) {
					new Error(self::$errors['unknownSpecEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, implode(', ', $specs), $item['content']));
				}
			}
		}
		if (empty($propValue)) {
			new Error(self::$errors['incorrectEventAttr'], array($origValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
		}
		if (in_array($propValue, array('this', 'true', 'false', 'null', 'undefined'))) {
			new Error(self::$errors['keywordInEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
		}
		if (is_numeric($propValue)) {
			new Error(self::$errors['numericEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
		}

		if (!preg_match('/^[:\!]*[_a-z]\w+$/i', $propValue)) {
			new Error(self::$errors['incorrectEventAttr'], array($propValue, 'on'.$match, self::$templateName, self::$className, $item['content']));
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
			new Error(self::$errors['unknownEventAttr'], array('on'.$match, self::$templateName, self::$className, $item['content']));
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
			}
		}
		$child['e'][] = '<nq>'.(!$isSpecial ? '$.'.$callback : self::$globalVarNames[$callback]).'<nq>';
		if ($once) {
			$child['e'][] = true;
		}
	}

	private static function getProperComponentData(&$child) {
		$props = $child['p'];
		$properData = array();
		if (!empty($props['props'])) {
			$properData['ap'] = array();	
		} else {
			$properData['p'] = array();
		}
		if (!empty($props['args'])) {
			$properData['aa'] = array();	
		} else {
			$properData['a'] = array();
		}
		foreach ($props as $k => $v) {
			if ($k == 'opts') {
				$properData['op'] = $v;
			} else if ($k == 'cmpid') {
				$properData['i'] = $v;
			} elseif ($k == 'props' || $k == 'args') {
				$properData[$k == 'props' ? 'p' : 'a'] = $v;
			} else {
				if (preg_match('/^arg-/', $k)) {
					$k = preg_replace('/^arg-/', '', $k);
					if (is_array($properData['aa'])) {
						$properData['aa'][$k] = $v;
					} else {
						$properData['a'][$k] = $v;
					}
				} else {
					if (is_array($properData['ap'])) {
						$properData['ap'][$k] = $v;
					} else {
						$properData['p'][$k] = $v;
					}
				}
			}
		}
		if (empty($properData['ap'])) {
			unset($properData['ap']);
		}
		if (empty($properData['p'])) {
			unset($properData['p']);
		}
		if (empty($properData['aa'])) {
			unset($properData['aa']);
		}
		if (empty($properData['a'])) {
			unset($properData['a']);
		}
		$child['p'] = $properData;
		if (is_array($child['n']) && !empty($child['n'])) {
			foreach ($child['n'] as $k => $v) {
				if ($k == 'args' || preg_match('/^arg-/', $k)) {
					unset($child['n'][$k]);
				}
			}
			if (empty($child['n'])) unset($child['n']);
		}
	}

	private static function correctTagAttributeText($propName, $text) {
		if ($propName == 'st') {
			$text = preg_replace('/:\s+/', ':', $text);
		}
		return $text;
	}

	private static function hasClassVar($code) {
		return preg_match('/\$\w/', $code);
	}

	private static function hasComponentMethod($method, $class) {
		if (is_array($class['functionList']) && in_array($method, $class['functionList'])) return true;
		$parents = $class['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parent) {
				if (is_array(self::$sources[$parent]) && preg_match('/\b'.$parent.'.prototype\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', self::$sources[$parent]['content'])) {
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

	private static function parseTextNode($content, &$children, &$let, $place = 'textNode') {		
		if (!empty($content)) {			
			$regexp = '/\{([^\}]+)\}/';
			preg_match_all($regexp, $content, $matches);
			$codes = $matches[1];
			$count = count($codes);
			if (empty($codes)) {
				if (strlen($content) > 5) {
					$children[] = '<nq>'.self::$globalNames['TEXTS'].'['.self::addTextNode($content).']<nq>';
				} else {
					$children[] = $content;
				}
				return;
			}
			$isLet = false;
			$lets = array();
			foreach ($codes as &$code) {
				$data = TemplateCodeParser::parse($code, $place);
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
						if (!empty($data['localNames'])) {
							$child['lc'] = self::getProperChildren($data['localNames']);
						}
						if (!empty($data['globalNames'])) {
							$child['gl'] = self::getProperChildren($data['globalNames']);
						}
						$code = '<nq>'.json_encode($child).'<nq>';
					} else {
						$code = '<nq>'.$data['code'].'<nq>';
					}
					$isLet = false;
				}			
				if (!empty($data['reactNames'])) {
					if (!is_array(self::$class)) {
						new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $content));
					}
				}			
			}
			
			if (!empty($lets)) {
				$letCode = '<nq><let>var '.implode(',', $lets).'<=let><nq>';
			}
			$parts = preg_split($regexp, $content);
			$items = array();
			foreach ($parts as $i => $part) {
				if (!empty($part)) {
					$items[] = $part;
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

	private static function hasFunctionCall($code) {
		preg_match_all('/^\s*\.([a-z]\w*)|[^\w\]]\.([a-z]\w*)/i', $code, $matches);
		$funcs = array();
		foreach ($matches[1] as $i => $match) {
			if (!empty($match)) {
				$funcs[] = $match;
			}
			if (!empty($matches[2][$i])) {
				$funcs[] = $matches[2][$i];
			}
			if (!empty($matches[3][$i])) {
				$funcs[] = $matches[3][$i];
			}
		}
		$funcs = array_unique($funcs);
		if (empty($funcs)) return false;
		return $funcs;
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

	private static function addTextNode($text) {
		$index = array_search($text, self::$textNodes);
		if ($index !== false) {
			return $index;
		}
		if ($text == self::$space) {
			$text = '\\'.trim(self::$space, '_');
		}
		self::$textNodes[] = $text;
		return count(self::$textNodes) - 1;
	}
}