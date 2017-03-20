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
		'else', 'ifempty'
	);
	
	private static $class, $className, $tmpids, $propsShortcuts,
				   $eventTypesShortcuts, $obfuscate, $tagShortcuts,
				   $templateName, $globalNames,
				   $parsedItem, $globalVarNames, $initials,
				   $componentsOpen = 0;

	private static $errors = array(
		'noMainTemplate' => '������ <b>main</b> ������ {??} �� ������ ����� ������',
		'forbiddenTag' => '��������� ������������ ��� {??} � ������� {??} ������ {??}<xmp>{?}</xmp>',
		'noClosingTag' => '��������� ���������� {?} {??} � ������� {??} ������ {??}<br>������ {?} {?}-� �� ����� ������������� {?} {??}<xmp>{?}</xmp>',
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
		'operatorInInnerLevel' => '��������� �������� {??} �� �� ����� ������ � ���������� {??} � ������� {??} ������ {??}',
		'doubleOperator' => '��������� �������� {??} ������ ������� ��������� {??} � ������� {??} ������ {??}',
		'fewSameOperators' => '���������� ������������ ��������� {??} ������ ��������� {??} � ������� {??} ������ {??}',
		'loadingOperatorWithoutLoader' => '���������� ������������� ������ �� ���������� <b>loading, loader</b> � ������� {??} ������ {??}. � ������� ������ ����������� initial �������� <b>loader</b>',
		'invalidTagName' => '������������ ��� ���� {??} � ������� {??} ������ {??}<br><br>���� ��������� DOM ������ ����� ���:<xmp><div>, <h1>, <table></xmp>���� ����������� ������ ����� ���:<xmp><Select>, <TableColumn></xmp>���� ������ ������� ������:<xmp><:content>, <:innerContent></xmp>���� ������ ���������� �������:<xmp><::checkbox>, <::userArea></xmp>',
		'includeNotFound' => '��������� ����� ��������������� include ������� {??} � ������� {??} ������ {??}<br><br>��� � ������� ��������� ������: <xmp>{?}</xmp>'
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
				$data = str_replace("_#_comma_#_", ",", $data);
				$data = str_replace("_#_equal_#_", "=", $data);
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

	private static function parseLetOperator($content) {
		$lets = array();
		$data = Splitter::split('/\{\s*let([\s&][^\}]+)\}/', $content, 1);
		$names = array();
		$globals = array();
		foreach ($data['delimiters'] as $i => $delmr) {
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
		return $lets;
	}

	private static function parseChildren($list, &$children, &$parentalChild, $ofElse = false) {
		$isElse = false;
		$isIfEmpty = false;
		$usedClasses = ClassAnalyzer::getUsedClasses();
		for ($i = 0; $i < count($list); $i++) {
			$item = $list[$i];
			$lets = array();
			if ($item['type'] == 'text') {
				if (self::hasCode($item['content'])) {
					if (preg_match('/\{\s*let\b/', $item['content'])) {
						$lets = self::parseLetOperator($item['content']);
						$letsKey = $ofElse ? 'elseLets' : 'lets';
						$parentalChild[$letsKey.'Content'] = $item['content'];
						if (!empty($lets)) {
							if (!isset($parentalChild[$letsKey])) {
								$parentalChild[$letsKey] = $lets;
							} else {
								$parentalChild[$letsKey] = array_merge($parentalChild[$letsKey], $lets);
							}
						}
						$item['content'] = '';
					}
				}
				if (!empty($item['content'])) {
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
					} elseif ($child['tagName'] == 'if') {
						$chld['if'] = trim(preg_replace('/^\s*{\s*if */', '', $child['content']), '}');
					} elseif ($child['tagName'] == 'foreach') {
						$chld['foreach'] = trim(preg_replace('/^\s*{\s*foreach */', '', $child['content']), '}');
					} elseif ($child['tagName'] == 'case') {
						$chld['case'] = trim(preg_replace('/^\s*{\s*case */', '', $child['content']), '}');
					} elseif ($child['tagName'] == 'default') {
						$chld['default'] = trim(preg_replace('/^\s*{\s*default */', '', 1), '}');
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
			if (is_string($child)) {
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
				}
				elseif (!empty($child['foreach']))
				{
					$ch['c'] = array();
					$ch['ie'] = array();
					if (!empty($child['ifempty'])) {
						self::finish($child['ifempty'], $ch['ie']);
					}
					self::finish($child['children'], $ch['c']);
					self::parseForeach('foreach '.$child['foreach'], $ch);
					$finishedChildren[] = $ch;
					continue;
				}
				elseif (!empty($child['switch']))
				{
					$ch['c'] = array();
					self::parseSwitch($child['switch'], $child['children'], $ch, $child);
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
					self::wrapInFunction($ch['c'], '', $child['lets']);
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
		$regexp = "/(<\/*:*[a-z]+[^>]*>|\{\s*\/*foreach\b[^\}]*\}|\{\s*\/*if\b[^\}]*\}|\{\s*\/*case\b[^\}]*\}|\{\s*\/*default\s*\}|\{\s*else\s*\}|\{\s*ifempty\s*\}|\{\s*\/*switch\b[^\}]*\})/i";
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
		TemplateValidator::validate($list, self::$templateName, self::$className);
		
		$children = array('c' => array());
		self::parseChildren($list, $children['c'], $children);
		//Printer::log($children);
		$finishedChildren = array();
		self::finish($children['c'], $finishedChildren);
		//Printer::log($finishedChildren);
		return array('name' => self::$templateName, 'children' => $finishedChildren, 'let' =>  $children['lets']);
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

	private static function getProperChildren($children, $addQuotes = false) {
		$children = self::getProperChild($children);
		if (is_string($children) && $addQuotes) {
			$children = "'".trim($children, "'")."'";
		}
		return is_array($children) ? '<nq>'.preg_replace('/\\\(?=[\'"])/', '', json_encode($children)).'<nq>' : $children;
	}

	private static function wrapInFunction(&$children, $args = '', $lets = null) {
		$c = preg_replace('/^<nq>/', '', self::getProperChildren($children));
		$space = ' ';
		if (in_array($c[0], array('[', '{', '(', "'"))) {
			$space = '';
		}
		$before = '';
		$after = '';
		$inner = '';
		if (!empty($lets)) {
			$before = '(';
			$after = ')()';
			$inner = 'var '.self::getLetVars($lets).';';
		}
		$children = '<nq>'.$before.'function('.$args.'){'.$inner.'return'.$space.$c.'}'.$after.'<nq>';
	}

	private static function parseForeach($content, &$child) {
		if (is_array($child['ie'])) {
			$child['ie'] = self::getProperChild($child['ie']);
		}
		if (empty($child['ie'])) {
			unset($child['ie']);	
		}
		$child['h'] = $child['c'];
		unset($child['c']);
		$content = ltrim(rtrim($content, '}'), '{');
		$data = TemplateCodeParser::parse($content, 'foreach', $content);
		$child['p'] = $data['items'];
		
		$hasReactNames = !empty($data['reactNames']);
		$hasGlobalNames = !empty($data['globalNames']);
		if ($hasReactNames || $hasGlobalNames) {
			if ($hasGlobalNames) $child['g'] = self::getProperChildren($data['globalNames']);
			if ($hasReactNames) $child['n'] = self::getProperChildren($data['reactNames']);
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
				$child['c'] = '(function(){var '.self::getLetVars($source['lets']).';return '.$child['c'].'})()';
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

		$attrContent = implode('+', $attrParts);
		if ($inFunc && $parsedPlace != 'componentAttribute' && $parsedPlace != 'elementAttribute') {
			self::wrapInFunction($attrContent);
		} else {
			$attrContent = '<nq>'.$attrContent.'<nq>';
		}
		return $attrContent;
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
				$data = TemplateCodeParser::parse($code, $place, $content);
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

	private static function parseSwitch($content, $childrenList, &$child, $source) {
		if (!empty($source['letsContent'])) {
			new Error(self::$errors['operatorInSwitch'], array('let', self::$templateName, self::$className, $source['letsContent'], '{switch '.$content.'}'));
		}
		$data = TemplateCodeParser::parse('switch '.$content, 'switch', self::$parsedItem);
		
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
		self::$textNodes[] = addcslashes($text, "'");
		return count(self::$textNodes) - 1;
	}
}