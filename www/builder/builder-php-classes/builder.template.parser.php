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
				   $parsedItem, $globalVarNames, $initials,
				   $componentsOpen = array();

	private static $errors = array(
		'noMainTemplate' => '������ <b>main</b> ������ {??} �� ������ ����� ������',
		'forbiddenTag' => '��������� ������������ ��� {??} � ������� {??} ������ {??}<xmp>{?}</xmp>',
		'closingSimpleTag' => '��������� ������������� ��� {??} � ������� {??} ������ {??}<xmp>{?}</xmp>',
		'noClosingTag' => '��������� ���������� {?} {??} � ������� {??} ������ {??}<br>������ {?} {?}-� �� ����� ������������� {?} {??}<xmp>{?}</xmp>',
		'tagInsideTag' => '���������� ������������ �����������: ��� {??} ������ ���� {??} � ������� {??} ������ {??}<br>������ ��� {?}-� �� ����� ������������� ��� {??}<xmp>{?}</xmp>',
		'tagOutsideProperTag' => '���������� ������������ �����������: ��� {??} ��� {?} {??} � ������� {??} ������ {??}<br>������ ��� {?}-� �� ����� ������������� ��� {??}<xmp>{?}</xmp>',
		'noClosingTag2' => '��������� ���������� {?} {??} � ������� {??} ������ {??}<br>������ {?} {?}-� �� ����� ������������� {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag' => '��������� ������ ������������� {?} {??} � ������� {??} ������ {??}<br>������ {?} ������� ����� {?}-�� �� ����� {?} {?} {??}<xmp>{?}</xmp>��������� �������� ���� {??}<br>������ {?} {?}-� �� ����� ������������� {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag2' => '��������� ������ ������������� {?} {??} � ������� {??} ������ {??}<br>������ {?} ������� ����� {?}-�� �� ����� {?} {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag3' => '��������� ������������� {?} {??} � ������ ������� {??} ������ {??}',
		'unknownComponent' => '�������������� ��������� � ������� {??} ������ {??}<xmp>{?}</xmp>��������� ������ ����<xmp>{?}</xmp>',
		'controlWithoutName' => '������� {??} � ������� {??} ������ {??} �� ����� �������� <b>name</b><xmp>{?}</xmp>��������� ������ ����<xmp>{?}</xmp>',
		'reactVarInInclude' => '������ {??}, ������������ � ����� {??} �������� ��� � ����������� ����������� {??}<br><br>���������� ������� � ����� <b>include</b> �� ����� ��������� ��. ����������� ������������� ������ �������� ���������� <b>~arg</b> � ��������� ���������� <b>&var</b>',
		'reactComponentName' => '�������� ���������� � ������� {??} ������ {??} �� ����� ������������ ���������� ����������<xmp>{?}</xmp>����������� ������ ����<xmp><component class="{~class}"></xmp>���<xmp><component class="{&class}"></xmp>',
		'reactControlName' => '������� <b>name</b> �������� � ������� {??} ������ {??} �� ����� ������������ ���������� ����������<xmp>{?}</xmp>����������� ������ ����<xmp><control name="{~class}"></xmp>���<xmp><control name="{&class}"></xmp>',
		'letError' => '������ � ���� ��������� <b>let</b> � ������� {??} ������ {??}<xmp>{{?}}</xmp><b>��������� ��� ����</b><xmp>{let &var = 5}</xmp><b>���</b><xmp>{let &isEmpty: true}</xmp>',
		'caseOutsideSwitch' => "��������� �������� <b>case</b> ��� ��������� <b>switch</b> ��� ��������� ��� <b>if</b> � ������� {??} ������ {??}<br><br><b>����������� ��� ����</b><xmp>{switch ~value}\n\t{case 10}\n\t\t<div class=\"ten\">10</div>}\n\n\t{default}\n\t\tdefault text\n{/switch}</xmp><b>���</b><xmp>{if}\n\t{case !isUndefined(\$var)}\n\t\tvariant 1\n\n\t{case \$var2 === true}\n\t\tvariant 2\n\n\t{default}\n\t\tdefault text\n{/if}</xmp>",
		'dataInTextNode' => '���������� ������������� ���������� ������ {??} ������ ���������� ���� � ������� {??} ������ {??}<br><br>����������� ������������� ������ ������ ��������� ����� <xmp><component Item args="{#itemDefaultArgs}"></xmp>��� ������ javascript ���� ������<xmp>var params = #itemDefaultParams</xmp>',
		'usingThis' => '���������� ������������� ��������� ����� <b>this</b> � ������� {??} ������ {??}',
		'templateCallLoop' => '������ {??} ������ {??} �������� ��� ����',
		'caseExpected' => "���������� ������ � ���� ��������� <b>switch</b> � ������� {??} ������ {??}. ��������� �������� <b>case</b><xmp>{case 'triangle'}</xmp>���<xmp>{case 2}</xmp>",
		'ifCaseExpected' => "���������� ������ � ���� ��������� <b>if</b> � ������� {??} ������ {??}.<br><br>�������:<xmp>{?}</xmp>��������� �������� <b>case</b><xmp>{case isNumber(~var)}</xmp>���<xmp>{case &a > &b}</xmp>",
		'fewDefaults' => '���������� ����� ������ ������� <b>default</b> � ���� ��������� {??} � ������� {??} ������ {??}',
		'conditionEmpty' => '���������� ������ � ���� ��������� <b>switch</b> � ������� {??} ������ {??}. ������� {??} �� �������� ��������',
		'elseWithoutIf' => '������� � ������� {??} ������ {??} �������� ������� <b>else</b>, �� �� �������� ������� <b>if</b>',
		'incorrectIf' => '������� � ������� {??} ������ {??} �������� ������������ ������� <b>if = "{?}"</b><br><br>������� ������ ����� ��� <b>if = "{$a === true}"</b> ��� <b>if = "{!&name}"</b>',
		'handlerNotFound' => '������� {??}, ��������� � ������� {??} ������ {??} � �������� ����������� ������� {??}, �� ������� ����� ������� ������� ������',
		'noTemplateName' => '����� ������� ��� �������� ��� ����� � ������� {??} ������ {??}. ��� ������ ����� ���:<xmp><template templ="table" rows="{~rows}"></xmp>',
		'noIncludeTemplateName' => '����� ������� ��� �������� ��� ����� � ������� {??} ������ {??}. ��� ������ ����� ���:<xmp><include templ="table" rows="{~rows}"></xmp>',
		'codeOutsideAttribute' => '��������� ��� ��� �������� ���� � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'keywordInEventAttr' => '���������� �������� ����� {??} � �������� ������� {??} � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'numericEventAttr' => '���������� �������� �������� {??} �������� ������� {??} � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'incorrectEventAttr' => '���������� ������������ �������� {??} �������� ������� {??} � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'unknownSpecEventAttr' => '��������� ����������� ������������ {??} �������� ������� {??} � ������� {??} ������ {??}<br><br>��������� ���� �� ��������: <xmp>{?}</xmp>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'unknownEventAttr' => '��������� ������� ������������ ������� {??} � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'specEventAttrInComp' => '��������� ������������ {??} ������� {??} � ���� ���������� � ������� {??} ������ {??}<br><br>������ ���������� �������� ������ ��� ��������� DOM<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>',
		'operatorOutOfPlace' => '��������� �������� {??} ��� ������ ��������� {??} � ������� {??} ������ {??}',
		'operatorInInnerLevel' => '��������� �������� {??} �� �� ����� ������ � ���������� {??} � ������� {??} ������ {??}',
		'doubleOperator' => '��������� �������� {??} ������ ������� ��������� {??} � ������� {??} ������ {??}',
		'fewSameOperators' => '���������� ������������ ��������� {??} ������ ��������� {??} � ������� {??} ������ {??}',
		'loadingOperatorWithoutLoader' => '���������� ������������� ������ �� ���������� <b>loading, loader</b> � ������� {??} ������ {??}. � ������� ������ ����������� initial �������� <b>loader</b>',
		'invalidTagName' => '������������ ��� ���� {??} � ������� {??} ������ {??}<br><br>���� ��������� DOM ������ ����� ���:<xmp><div>, <h1>, <table></xmp>���� ����������� ������ ����� ���:<xmp><Select>, <TableColumn></xmp>���� ������ ������� ������:<xmp><:content>, <:innerContent></xmp>���� ������ ���������� �������:<xmp><::checkbox>, <::userArea></xmp>'
	);

	public static function init($params) {
		self::$initials = $params['initialsParser']->get();
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

	private static function parseLetOperator(&$content) {
		$lets = array();
		$data = Splitter::split('/\{\s*let([\s&][^\}]+)\}/', $content, 1);
		$content = '';
		foreach ($data['items'] as $i => $item) {
			$content .= $item;
		}
		$names = array();
		foreach ($data['delimiters'] as $i => $delmr) {
			$delmr = str_replace('<nq>', '', self::processCode('{let '.$delmr.'}', 'textNode', $names));
			$parts = explode(',', $delmr);
			foreach ($parts as $part) {
				$p = explode('=', $part);
				$key = str_replace('&', '', trim($p[0]));
				$lets[$key] = trim($p[1]);
			}
		}
		return $lets;
	}

	private static function parseChildren($list, &$children, &$parentalChild, $ofElse = false) {
		$isElse = false;
		$usedClasses = ClassAnalyzer::getUsedClasses();
		for ($i = 0; $i < count($list); $i++) {
			$item = $list[$i];
			$lets = array();
			if ($item['type'] == 'text') {
				if (self::hasCode($item['content'])) {
					if (preg_match('/\{\s*let\b/', $item['content'])) {
						$lets = self::parseLetOperator($item['content']);
						$letsKey = $ofElse ? 'elseLets' : 'lets';
						if (!empty($lets)) {
							if (!isset($parentalChild[$letsKey])) {
								$parentalChild[$letsKey] = $lets;
							} else {
								$parentalChild[$letsKey] = array_merge($parentalChild[$letsKey], $lets);
							}
						}
					}
				}
				if (!empty($item['content'])) {
					$children[] = $item['content'];
				}
			} else {
				$tag = $item['tagName'];
				if ($tag == 'br') {
					$children[] = '<br>';
					continue;
				}
				$child = array('tagName' => $tag, 'content' => $item['content'], 'children' => array(), 'else' => array());
				$ch = array();
				$ech = array();
				$open = 0;
				while (true) {
					$i++;
					if (isset($list[$i])) {
						if ($tag == 'if' && $open == 0 && $list[$i]['tagName'] == 'else') {
							$isElse = true;
							continue;
						}
						if (!$isElse) {
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
									if (!$isElse) {
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
				$isComponent = in_array($child['tagName'], $usedClasses) || $child['tagName'] == 'Component' || $child['tagName'] == 'Control';
				$attributes = self::parseTagAttributes($child, $isComponent, $child['tagName']);
				$child['attributes'] = $attributes;

				if (!empty($child['reactProps'])) {
					$chld['reactProps'] = $child['reactProps'];
				}
				if (!empty($child['componentProps'])) {
					$chld['componentProps'] = $child['componentProps'];
				}
				
				if ($child['content'][0] == '{') {
					if (preg_match('/\{\s*if\s*\}/', $child['content'])) {
						$chld['ifswitch'] = 1;	
					} elseif ($child['tagName'] == 'if') {
						$chld['if'] = trim(preg_replace('/^\s*{\s*if */', '', $child['content']), '}');
					} elseif ($child['tagName'] == 'foreach') {
						$chld['foreach'] = trim(preg_replace('/^\s*{\s*foreach */', '', $child['content']), '}');
					} elseif ($child['tagName'] == 'case') {
						$chld['case'] = trim(preg_replace('/^\s*{\s*case */', '', $child['content']), '}');
					}
				} else {
					if ($child['tagName'][0] == ':') {
						$chld[$child['tagName'][1] == ':' ? 'include' : 'template'] = trim($child['tagName'], ':');
					} elseif ($isComponent) {
						$chld['component'] = $child['tagName'];
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
			if (is_string($child)) {
				if (self::hasCode($child)) {
					self::parseTextNode($child, $finishedChildren);
				} else {
					$finishedChildren[] = self::getTextNode($child);
				}
			} else {
				$ch = array();
				$isElement = false;
				$isComponent = false;
				$isTemplate = false;
				
				if (!empty($child['element']))
				{
					$isElement = true;
					$idx = array_search($child['element'], self::$tagShortcuts);
					$ch['t'] = 	$idx !== false ? $idx : 'span';
				}
				elseif (!empty($child['component']))
				{
					$isComponent = true;
					$ch['cmp'] = $child['component'];
				}
				elseif (!empty($child['template']))
				{
					$isTemplate = true;
					$ch['tmp'] =  '<nq><this>getTemplate'.ucfirst($child['template']).'<nq>';
				}
				elseif (!empty($child['foreach']))
				{
					$ch['c'] = array();
					self::finish($child['children'], $ch['c']);
					self::parseForeach('foreach '.$child['foreach'], $ch);
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
				if (!empty($child['attributes'])) {
					$ch['p'] = array();
					$parsedPlace = $isComponent ? 'componentAttribute' : 'elementAttribute';
					$names = array();
					$attrs = $child['attributes'];
					foreach ($attrs as $attrName => $attrValue) {
						if (in_array($attrName, array('if', 'else'))) continue;
						if ($attrValue[0] == '{') {
							$isObfClName = self::$obfuscate === true && $attrName == 'class';
							$names[$attrName] = array();
							$code = self::processCode($attrValue, $parsedPlace, $names[$attrName], $isObfClName);
						}
						self::addProperTagAttribute($attrName, $attrValue, $ch['p'], $isElement);
					}
					if (!empty($attrs['if'])) {
						$ch['c'] = array();
						self::finish($child['children'], $ch['c']);

						self::addIfConditionToChild($attrs['if'], $attrs['else'], $ch);
						unset($child['children']);
					}

				}
				if (!empty($child['componentProps'])) {
					$ch['w'] = $child['componentProps'];
				}
				if (!empty($child['reactProps'])) {
					$ch['n'] = $child['reactProps'];
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
				$finishedChildren[] = $ch;
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

	private static function getParsedTemplate($content) {
		$html = preg_replace(self::$regexp, '', $content);
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
		$regexp = "/(<\/*:*[a-z]+[^>]*>|\{\s*\/*foreach\b[^\}]*\}|\{\s*\/*if\b[^\}]*\}|\{\s*\/*case\b[^\}]*\}|\{\s*else\s*\}|\{\s*ifempty\s*\}|\{\s*\/*switch\b[^\}]*\})/i";
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
		self::checkHtmlTagStructure($list);
		
		$children = array('c' => array());
		self::parseChildren($list, $children['c'], $children);
		//Printer::log($children);
		$finishedChildren = array();

		self::finish($children['c'], $finishedChildren);
		//Printer::log($finishedChildren);
		return array('name' => self::$templateName, 'children' => $finishedChildren, 'let' =>  $children['lets']);


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

	private static function invalidTagName($tagName) {
		if (strlen($tagName) > 1 && !preg_match('/[a-z]/', $tagName)) {
			return true;
		}
		return !(preg_match('/^[a-z][a-z0-9]*$/', $tagName) ||
			   preg_match('/^[A-Z][a-zA-Z0-9]*$/', $tagName) ||
			   preg_match('/^:{1,2}\w+$/', $tagName));
	}

	private static function getLet($content) {
		$parts = explode('<let>', $content);
		$parts = explode('<=let>', $parts[1]);
		return $parts[0];
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
							new Error(self::$errors['forbiddenTag'], array($tn, self::$templateName, self::$class['name'], $item['content']));
						}
						
						if (isset(self::$forbiddenInnerElements[$last]) && in_array($tn, self::$forbiddenInnerElements[$last])) {
							new Error(self::$errors['tagInsideTag'], array($tn, $last, self::$templateName, self::$class['name'], $openedTags[$tn], $tn, $item['content']));
						}
						if (isset(self::$onlyParentalElements[$tn]) && !in_array($last, self::$onlyParentalElements[$tn])) {
							new Error(self::$errors['tagOutsideProperTag'], array($tn, count(self::$onlyParentalElements[$tn]) > 1 ? '�����' : '����', implode(', ', self::$onlyParentalElements[$tn]), self::$templateName, self::$class['name'], $openedTags[$tn], $tn, $item['content']));
						}
						if (isset(self::$allowedInnerElements[$last]) && !in_array($tn, self::$allowedInnerElements[$last])) {
							new Error(self::$errors['tagInsideTag'], array($tn, $last, self::$templateName, self::$class['name'], $openedTags[$tn], $tn, $item['content']));
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
										$object, $prev, self::$templateName, self::$class['name'], $object, $count, $object, $prev, $list[$realIndex]['content']
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
								$object2 = self::getTagTypeName($prev2, '�');
								
								$typeTag = $lastType == 'open' ? '��������������' : '��������������';
								if ($prev != $prev2 && $lastType == 'open') {
									$typeTag = '';
								}
								if (!empty($prev2)) {
									if (isset($content)) {
										$object3 = self::getTagTypeName($tag);
										new Error(
											self::$errors['extraClosingTag'], array($object, $tn, self::$templateName, self::$class['name'], $object, $openedTags[$prev2], $typeTag, $object2, $prev2, $list[$aix - 1]['content'],
											$tag, $object3, $orderNumber, $object3, $tag, $content
										));
									} else {
										new Error(
											self::$errors['extraClosingTag2'], array($object, $tn, self::$templateName, self::$class['name'], $object, $openedTags[$prev2], $typeTag, $object2, $prev2, $list[$aix - 1]['content']
										));
									}
								} else {
									new Error(
										self::$errors['extraClosingTag3'], array($object, $tn, self::$templateName, self::$class['name']
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
					if (self::isSimpleTag($tn) && $item['isClosing']) {
						new Error(self::$errors['closingSimpleTag'], array($tn, self::$templateName, self::$className, $item['content']));
					}
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
		return !($tn == 'if' || $tn == 'switch' || $tn == 'foreach'|| $tn == 'else' || $tn == 'ifempty' || $tn == 'case');
	}

	private	static function getTagTypeName($tn, $ending = '') {
		return ($tn == 'if' || $tn == 'switch' || $tn == 'foreach'|| $tn == 'else' || $tn == 'ifempty' || $tn == 'case' ? '��������' : '���').$ending;
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
		else
		{

			$isTemplate = false;
			$isComponent = false;
			$usedClasses = ClassAnalyzer::getUsedClasses();
			if ($tagName[0] == ':')
			{
				if (!$item['isClosing']) {
					$isTemplate = true;
					self::getTemplateProperties($item['content'], $child);
				} else {
					return null;
				}
			} 
			elseif (in_array($tagName, $usedClasses) || $tagName == 'Component' || $tagName == 'Control')
			{
				if (!$item['isClosing']) {
					self::$componentsOpen[] = $tagName;
					$isComponent = true;

					self::getTagProperties($item, $child, true);
				} else return null;
			}
			
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
						self::parseForeach($item['content'], $child);
						//if (!empty())
					break;

					case 'else':
					case 'ifempty':
					break;

					default:
						if (!$isTemplate && !$isComponent) {
							$child['t'] = self::getTagIndex($tagName);
							self::getTagProperties($item, $child);
						}						
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
		if ((is_array($usedClasses) && in_array($tagName, $usedClasses)) || $tagName == 'Component' || $tagName == 'Control') {
			array_pop(self::$componentsOpen);
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
					if ($list[$i]['tagName'] == $tagName) {
						$openedTagsCount++;
					}
				} else {
					if (!$list[$i]['isSingle']) $level--;
					if ($list[$i]['tagName'] == $tagName) {
						$openedTagsCount--;
					}
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
				$allReactNames = array();
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
							$names = array();
							self::parseTextNode($code, $case, $let, $caseType, $names);
							$allReactNames = array_merge($allReactNames, $names);

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
		if (!empty($allReactNames)) {
			$child['n'] = array_values(array_unique($allReactNames));
		}
		if (!empty(self::$componentsOpen)) {
			$child['$'] = '<nq>$<nq>';
		}
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

	private static function parseForeach($content, &$child) {
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
		$content = ltrim(rtrim($content, '}'), '{');
		$data = TemplateCodeParser::parse($content, 'foreach', null);
		$child['p'] = $data['items'];
		if (!empty($data['reactNames'])) {
			$child['n'] = self::getProperChildren($data['reactNames']);
		}
		if (!empty(self::$componentsOpen)) {
			$child['$'] = '<nq>$<nq>';
		}
		if ($data['reactiveItems']) {
			$child['$'] = '<nq>$<nq>';
			unset($child['p']);
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
			if (!empty(self::$componentsOpen)) {
				$child['$'] = '<nq>$<nq>';
			}
		} else {
			$child['sw'] = '<nq>'.$switch.'<nq>';
		}
		unset($child['d']);
		unset($child['c']);
		unset($child['is']);
	}

	private static function getTemplateProperties($html, &$child) {
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
		if ($ifCondition[0] != '{') $ifCondition = '{'.$ifCondition.'}';
		$ifCondition = self::processCode($ifCondition, 'if', $names);
			
		if (is_array($child['c'])) {
			if (isset($child['c'][0]) && count($child['c']) == 1) {
				$child['c'] = $child['c'][0];
			}
			if (is_array($child['c'])) {
				$child['c'] = str_replace('\\', '', json_encode($child['c']));
			}
		}
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
				$child['c'] = '(function(){var '.self::getLetVars($source['lets']).';return '.$child['c'].'})()';
			}
			if (!empty($else) && !empty($source['elseLets'])) {
				$else = '(function(){var '.self::getLetVars($source['elseLets']).';return '.$else.'})()';
			}
		}
		if (empty($else)) {
			$else = "<emptystring>";
		}
		if (!empty($names)) {
			if (empty($child['c'])) {
				$child['i'] = "<nq>function(){return ".$ifCondition."?'':".$else."}<nq>";			 
			} else {
				$child['i'] = '<nq>function(){return '.$ifCondition.'?'.$child['c'].':'.$else.'}<nq>';
			}
			$child['n'] = $names;
			unset($child['c']);
		} else {
			$child = '<nq>'.str_replace('<nq>', '', $ifCondition).'?'.$child['c'].':'.$else.'<nq>';
		}
	}

	private static function getTagIndex($tagName) {
		$tagNameIndex = array_search($tagName, self::$tagShortcuts);
		return $tagNameIndex !== false ? $tagNameIndex : $tagName;
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
					$attrs['nm'] = self::parseComponentClassName($value, $child['content'], true);
					continue;
				}
			}
			
			if ($name != 'if' && $name != 'else' && self::hasCode($value)) {
				$reactProps = array();
				$parsedPlace = $isComponent ? 'componentAttribute' : 'elementAttribute';
				$code = self::processCode($value, $parsedPlace, $reactProps, $isObfClName);
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
			if ($cmpType == 'control' && empty($attrs['nm'])) {
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
				if (!empty($attrs)) {
					self::getProperComponentData($attrs);
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
						/////// ������
					}
				}
				if (is_array($data['reactNames'])) {
					// if (is_string(self::$class)) {
					// 	new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $code));
					// }
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
				//$inFunc = count($names) > 0;	
			} else {
				//$inFunc = count($names) > 1;
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
		$tn = $item['tagName'];
		if ($isComponentTag) {
			if ($tn != 'Component' && $tn != 'Control') {
				$child['cmp'] = $tn;
				$cmpType = self::$classes[$tn]['type'];
			} else {
				$isDinamycComponent = true;
			}
		}
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
				
				if ($isTag && !$isComponentTag && isset(self::$propsShortcuts[$propName])) {
					$propName = self::$propsShortcuts[$propName];
				} else {
					$propName = preg_replace('/^data-/', '_', $propName);
					if ($isComponentTag) {
						if (($cmpType == 'control' || $tn == 'Control') && $propName == 'name') {
							$child['nm'] = self::parseComponentClassName($propValue, $item['content'], true);
							continue;
						}
					}
				}
				if (preg_match("/^on([A-Z]\w+)$/i", $propName, $match)) {
					self::parseEventAttribute($match[1], $propValue, $child, $item['content'], $isComponentTag);
					continue;
				}
			}
			$props[$propName] = $propValue;
			if ($hasCode) {
				$names[$propName] = array();
				$parsedPlace = $isComponentTag ? 'componentAttribute' : 'elementAttribute';
				$code = self::processCode($propValue, $parsedPlace, $names[$propName], $isObfClName);
				$propValue = self::correctTagAttributeText($propName, $code);
				if ($isDinamycComponent && $propName == 'class') {
					$child['cmp'] = $propValue;
					unset($props[$propName]);
				} else {
					$props[$propName] = $propValue;
				}
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
			} else {
				self::checkPropertyForObfuscation($propValue);
			}
		}
	
		if ($isComponentTag) {
			$comp = '<ComponentClassName/>';
			if ($cmpType == 'control') {
				$comp = '<ControlClassName name="controlName"/>';
			} elseif ($cmpType == 'menu') {
				$comp = '<MenuClassName/>';
			} elseif ($cmpType == 'form') {
				$comp = '<FormClassName/>';
			}
			if (empty($child['cmp'])) {
				new Error(self::$errors['unknownComponent'], array(self::$templateName, self::$className, $item['content'], $comp));
			}
			if ($cmpType == 'control' && empty($child['nm'])) {
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
					self::getProperComponentData($child['p']);
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
			$data = TemplateCodeParser::parse('.'.$propValue.preg_replace('/[\{\}]/', '', $eventArgs), 'eventAttribute', null);
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
		$child['e'][] = '<nq>'.(!$isSpecial ? '$.'.$callback : self::$globalVarNames[$callback]).'<nq>';
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

	private static function hasClassVar($code) {
		return preg_match('/\$\w/', $code);
	}

	private static function hasComponentMethod($method, $class) {
		$globals = JSGlobals::getVarNames();
		if (is_array($class['functionList']) && in_array($method, $class['functionList'])) return true;
		$parents = $class['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parent) {
				if (is_array(self::$sources[$parent]) && preg_match('/\b'.$globals['proto'].'\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', self::$sources[$parent]['content'])) {
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
		if (!empty($content)) {
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
				$data = TemplateCodeParser::parse($code, $place, null);
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

	private static function getTextNode($text) {
		if (strlen($text) > 5) {
			$text = '<nq>'.self::$globalNames['TEXTS'].'['.self::addTextNode($text).']<nq>';
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
		self::$textNodes[] = $text;
		return count(self::$textNodes) - 1;
	}
}