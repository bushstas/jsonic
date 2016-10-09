<?php

class TemplateParser 
{	
	private static $calledClasses, $classes, $templates, $sources;
	private static $regexp = "/\{template +\.(\w+) *\}/";
	private static $simpleTags = array('br', 'input', 'img', 'hr');
	private static $class, $className, $tmpids, $isSwitchContext,
				   $propsShortcuts, $eventTypesShortcuts, $obfuscate,
				   $tagShortcuts, $cssClassIndex;

	private static $errors = array(
		'noMainTemplate' => 'Шаблон <b>main</b> класса {??} не найден среди прочих',
		'noClosingTag' => 'Ошибка валидации шаблонов класса {??}. Один из {?} {??} не имеет закрывающего тега',
		'extraClosingTag' => 'Ошибка валидации шаблонов класса {??}. Лишний закрывающийся {?} {??}',
		'unknownComponent' => 'Неопределенный компонент в шаблоне класса {??}<xmp>{?}</xmp>Ожидается запись вида<xmp>{?}</xmp>',
		'controlWithoutName' => 'Контрол {??} в шаблоне класса {??} не имеет атрибута <b>name</b><xmp>{?}</xmp>Ожидается запись вида<xmp>{?}</xmp>',
		'incorrectReactVar' => 'Элемент в шаблоне класса {??} содержит атрибут с некорректным кодом {??}<br><br>Реактивные переменные класса должны иметь вид <b>$var</b> или <b>$var.name</b> или <b>$var.0</b>. Использование записи вида <b>$var["name"]</b> недопустимо',
		'reactVarInInclude' => 'Шаблон, содержащийся в файле {??} содержит код с реактивными переменными {??}<br><br>Глобальные шаблоны с типом <b>include</b> не могут содержать их. Допускается использование только входящих аргументов (локальных переменных) <b>&var</b>',
		'reactComponentName' => 'Название компонента в шаблоне класса {??} не может определяться реактивной переменной<xmp>{?}</xmp>Допускается запись вида<xmp><component class="{~class}"></xmp>или<xmp><component class="{&class}"></xmp>',
		'reactControlName' => 'Атрибут <b>name</b> контрола в шаблоне класса {??} не может определяться реактивной переменной<xmp>{?}</xmp>Допускается запись вида<xmp><control name="{~class}"></xmp>или<xmp><control name="{&class}"></xmp>',
		'incorrectReactVar2' => 'Шаблон класса {??} содержит некорректный код {??}<br><br>Реактивные переменные класса должны иметь вид <b>$var</b> или <b>$var.name</b> или <b>$var.0</b>. Использование записи вида <b>$var["name"]</b> недопустимо',
		'letError' => 'Ошибка в коде оператора <b>let</b> в шаблоне класса {??}<xmp>{{?}}</xmp><b>Ожидается код вида</b><xmp>{let &var = 5}</xmp><b>или</b><xmp>{let &isEmpty: true}</xmp>',
		'caseOutsideSwitch' => "Обнаружен оператор <b>case</b> вне оператора <b>switch</b> или подобного ему <b>if</b> в шаблоне класса {??}<br><br><b>Используйте код вида</b><xmp>{switch ~value}\n\t{case 10}\n\t\t<div class=\"ten\">10</div>}\n\n\t{default}\n\t\tdefault text\n{/switch}</xmp><b>или</b><xmp>{if}\n\t{case !isUndefined(\$var)}\n\t\tvariant 1\n\n\t{case \$var2 === true}\n\t\tvariant 2\n\n\t{default}\n\t\tdefault text\n{/if}</xmp>",
		'dataInTextNode' => 'Обнаружено использование контстанты данных {??} внутри текстового нода в шаблоне класса {??}<br><br>Допускается использование только внутри атрибутов тегов <xmp><component Item args="{#itemDefaultArgs}"></xmp>или внутри javascript кода класса<xmp>var params = #itemDefaultParams</xmp>',
		'usingThis' => 'Обнаружено использование ключевого слова <b>this</b> в шаблоне класса {??}',
		'templateCallLoop' => 'Шаблон {??} класса {??} вызывает сам себя',
		'incorrectForeach' => 'Невалидный код <b>foreach</b> в шаблоне класса {??}: {??}',
		'switchError' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне класса {??}<xmp>{?}</xmp><b>Ожидается код вида</b><xmp>{switch \$type}</xmp><b>или</b><xmp>{switch ~type}</xmp><b>или</b><xmp>{switch &type}</xmp><b>или</b><xmp>{switch .getType(\$a, ~b, &c)}</xmp>",
		'caseExpected' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне класса {??}. Ожидается оператор <b>case</b><xmp>{case 'triangle'}</xmp>или<xmp>{case 2}</xmp>",
		'fewDefaults' => 'Обнаружено более одного условия <b>default</b> в коде оператора <b>switch</b> в шаблоне класса {??}',
		'noSwitchContent' => 'Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне класса {??}. Оператор {??} не содержит контента',
		'incorrectCaseCode' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне класса {??}. Некоррекнтый код в операторе <b>case</b><xmp>{{?}}</xmp>",
		'conditionEmpty' => 'Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне класса {??}. Условие {??} не содержит контента',
		'elseWithoutIf' => 'Элемент в шаблоне класса {??} содержит атрибут <b>else</b>, но не содержит атрибут <b>if</b>',
		'incorrectIf' => 'Элемент в шаблоне класса {??} содержит некорректный атрибут <b>if = "{?}"</b><br><br>Атрибут должен иметь вид <b>if = "{$a === true}"</b> или <b>if = "{!&name}"</b>',
		'eventHandlerExpected' => 'Фигурные скобки внутри атрибута события {??} в шаблоне класса {??}. Ожидается название функции обработчика!',
		'handlerNotFound' => 'Функция обработчик события {??} не найдена среди методов класса {??}'
	);

	public static function init($params) {
		self::$calledClasses = $params['classNames'];
		self::$classes = $params['classes'];
		self::$sources = $params['sources'];
		self::$templates = $params['templates'];
		self::$propsShortcuts = Props::getList();
		self::$eventTypesShortcuts = Events::getList();
		self::$tagShortcuts = Tags::getList();
		self::$obfuscate = $params['obfuscateCss'];
		self::$cssClassIndex = &$params['cssClassIndex'];
	}

	public static function parse($template, &$class, $className, &$tmpids) {
		self::$class = &$class;
		self::$className = $className;
		$template = preg_replace('/[\t\r\n]/', '', $template);
		$template = preg_replace('/ {2,}/', ' ', $template);
		$template = preg_replace('/&nbsp;/', '\u00A0', $template);

		preg_match_all("/\{template +\.(\w+) +as +\.(\w+) *\}/", $template, $matches);		
		foreach ($matches[1] as $i => $match) {
			$tmpids[$matches[2][$i]] = $match;
			$template = preg_replace('/\{template +\.'.$match.' +as +\.'.$matches[2][$i].' *\}/', '{template .'.$match.'}', $template);
		}
		self::$tmpids = $tmpids;
		preg_match_all(self::$regexp, $template, $matches);
		$templateNames = $matches[1];
		if (!empty($templateNames)) {
			if (!empty($className) && !preg_match("/\{template +\.main *\}/", $template) && !self::hasParentMainTemplate($class) && in_array($className, self::$calledClasses)) {
				new Error(self::$errors['noMainTemplate'], $className);
			}
			$templateContents = preg_split(self::$regexp, $template);
			array_shift($templateContents);
		} else {
			$templateNames = array('main');
			$templateContents = array($template);
		}
		
		$templates = array();
		for ($i = 0; $i < count($templateNames); $i++) {
			$templates[] = self::getParsedTemplate($templateContents[$i], $templateNames[$i]); 
		}

		$isSingle = count($templates) == 1;
		$templateFunctions = array();
		foreach ($templates as $template) {
			$data = json_encode($template['children']);
			if ($data == '[]') {
				$data = ' null';
			} else {
				$data = str_replace('"', "'", $data);
		
				$data = str_replace("\'", "'", $data);

				$data = str_replace('<this>', '$.', $data);
				$data = preg_replace("/'<nq>/", '', $data);
				$data = preg_replace("/<nq>'/", '', $data);
				$data = preg_replace("/<nq>/", '', $data);
				$data = preg_replace("/,*<nc>,*/", '', $data);
				$data = preg_replace("/\[*<nb>\]*/", '', $data);

				$data = str_replace('<plus>', "'+", $data);
				$data = str_replace('<\/plus>', "+'", $data);
				$data = str_replace('\\', '', $data);
				
				$data = preg_replace("/\[*<function>\[*(',)*/", 'function(){return ', $data);
				$data = preg_replace("/(,')*\]*<\/function>\]*/", '}', $data);

				$data = preg_replace("/\[*<function_returns_array>\[*(',)*/", 'function(){return[', $data);
				$data = preg_replace("/(,')*\]*<\/function_returns_array>\]*/", ']}', $data);

				$data = preg_replace("/\['<foreach ([^>]+)>',/", "function($1){return[", $data);
				$data = preg_replace("/,*'<\/foreach>'\]/", ']}', $data);

				
				$data = str_replace("<=let>,<let>", ";", $data);
				$data = preg_replace("/(,'<\/let>'){2,}/", ",'</let>'", $data);
				$data = str_replace("<let>", "function(){", $data);
				$data = preg_replace("/<=let>,*/", ";return[", $data);
				$data = preg_replace("/,'<\/let>'/", "]}", $data);

				$data = preg_replace("/''\+|\+''/", "", $data);
				
				$data = preg_replace("/([^\d])\.(\d+)/", "$1[$2]", $data);

				$data = preg_replace("/return\[(\d+)\]/", "return $1", $data);
			}
			$templateFunctions[] = array('content' => $data, 'name' => $template['name']);
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

	private static function getParsedTemplate($content, $name) {
		$html = preg_replace(self::$regexp, '', $content);
		$html = preg_replace('/<(\w+)([^>]*)\/>/', "<$1$2></$1>", $html);
		$html = preg_replace('/<\/(img|br|hr|input|component|control|form|menu)>/', '', $html);
		$parts = preg_split('/\{\/template\}/', $html);
		$html = $parts[0];

		$regexp = "/\{[^\}]+\}/";
		preg_match_all($regexp, $html, $matches);
		$matches = $matches[0];
		foreach ($matches as &$match) {
			$match = str_replace('>', '__MORE__', $match);
		}
		$parts = preg_split($regexp, $html);
		$html = '';
		foreach ($parts as $i => $part) {
			$html .= $part;
			if (isset($matches[$i])) {
				$html .= $matches[$i];
			}
		}
		$regexp = "/(<\/*[a-z]+[^>]*>|\{\s*\/*foreach[^\}]*\}|\{\s*\/*if[^\}]*\}|\{\s*else\}|\{\s*\/*switch[^\}]*\})/i";
		preg_match_all($regexp, $html, $matches);
		$tags = implode('__TMPDELIMITER__', $matches[1]);
		$tags = explode('__TMPDELIMITER__', str_replace('__MORE__', '>', $tags));
		$parts = preg_split($regexp, $html);
		
		$list = array();
		for ($j = 0; $j < count($parts); $j++) {
			$part = $parts[$j];
			if (!empty($part)) {
				$list[] = array('type' => 'text', 'content' => $part);
			}
			if (isset($tags[$j])) {
				preg_match('/^[<\{]\s*\/*([a-z]\w*) */i', $tags[$j], $match);
				$tagName = strtolower($match[1]);
				$tagContent = $tags[$j];
				$isClosing = self::isTagClosing($tagName, $tagContent);
				$list[] = array('type' => 'tag', 'content' => $tagContent, 'tagName' => $tagName, 'isClosing' => $isClosing);
			}
		}
		$isLet = 0;
		self::checkTagsPairing($list);
		$children = self::getHtmlChildren($list, $isLet, false, $name);
		return array('name' => $name, 'children' => $children);
	}

	private static function isTagClosing($tagName, $tagContent) {
		if (self::isSimpleTag($tagName)) return false;
		return preg_match("/^[<\{]\//", $tagContent) ? 1 : 0;
	}

	private	static function isSimpleTag($tagName) {
		return in_array($tagName, self::$simpleTags);
	}

	private	static function checkTagsPairing($list) {
		$closed = array();
		$opened = array();
		foreach ($list as $item) {
			$tn = $item['tagName'];
			if (!empty($tn)) {
				if (!self::isSimpleTag($tn) && $tn != 'template' && $tn != 'include' && $tn != 'component' && $tn != 'control' && $tn != 'form' && $tn != 'menu' && $tn != 'else') {
					if ($item['isClosing'] == 0) {
						if (!isset($opened[$tn])) {
							$opened[$tn] = 0;
						}
						$opened[$tn]++;
					} else {
						if (!isset($closed[$tn])) {
							$closed[$tn] = 0;
						}
						$closed[$tn]++;
					}
				}
			}
		}
		foreach ($opened as $tn => $count) {
			if ($count > $closed[$tn]) {
				$object = $tn == 'if' || $tn == 'switch' || $tn == 'foreach' ? 'операторов' : 'тегов';
				new Error(self::$errors['noClosingTag'], array(self::$class['name'], $object, $tn));
			} elseif ($count < $closed[$tn]) {
				$object = $tn == 'if' || $tn == 'switch' || $tn == 'foreach' ? 'оператор' : 'тег';
				new Error(self::$errors['extraClosingTag'], array(self::$class['name'], $object, $tn));
			}
		}
	}

	private	static function getHtmlChildren($list, &$let, $isSwitch = false, $templateName) {
		if (empty($list)) {
			return array();
		}
		$children = array();
		$elseChildren = array();
		$currentList = array();
		$isElse = false;
		$currentIf = null;
		for ($i = 0; $i < count($list); $i++) {
			$child = array();
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
			} elseif ($item['isClosing'] != 1) {
				if ($tagName == 'else') {
					$isElse = true;
				}
				$content = $item['content'];
				if (self::isSimpleTag($tagName))
				{
					$child = array('t' => self::getTagIndex($tagName));
					self::getTagProperties($item, $child);
				}
				elseif ($tagName == 'template')
				{
					preg_match("/<template +[\"']*(\w+)[\"']*[^=]/i",  $content, $match);
					$tmpName = $match[1];
					if (!empty($tmpName) && $tmpName == $templateName) {
						new Error(self::$errors['templateCallLoop'], array($templateName, self::$className));
					}
					$child = array('tmp' => '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>');
					self::getTemplateProperties($item['content'], $child);
					if (is_array($child['p']) && !empty($child['p']['tmpid'])) {
						if (!empty(self::$tmpids[$child['p']['tmpid']]) && self::$tmpids[$child['p']['tmpid']] == $templateName) {
							new Error(self::$errors['templateCallLoop'], array($templateName, self::$className));
						}
						$child['tmp'] = strip_tags($child['p']['tmpid']);
						unset($child['p']['tmpid']);
						if (count(array_keys($child['p'])) == 0) {
							unset($child['p']);
						}
					}
				}
				elseif ($tagName == 'include')
				{
					preg_match("/<include +[\"']*(\w+)[\"']*/i",  $content, $match);
					$child = array('tmp' => '<nq>includeGeneralTemplate'.ucfirst($match[1]).'<nq>');
					self::getTemplateProperties($item['content'], $child);
				}
				elseif ($tagName == 'component' || $tagName == 'control' || $tagName == 'menu' || $tagName == 'form')
				{
					self::getTagProperties($item, $child, true);
				}
				else
				{					
					$childrenList = array();
					$openedTagsCount = 1;
					$i++;
					while (isset($list[$i])) {
						if ($list[$i]['type'] == 'tag') {
							if (!$list[$i]['isClosing'] && $list[$i]['tagName'] == $tagName) {
								$openedTagsCount++;
							} elseif ($list[$i]['isClosing'] && $list[$i]['tagName'] == $tagName) {
								$openedTagsCount--;
							}
						}
						if ($openedTagsCount > 0) {
							$childrenList[] = $list[$i];
							$i++;
						} else {
							break;
						}
					}
					if ($tagName == 'if') {
						preg_match("/^\{\s*if\b\s*([^\}]+)\}/i",  $item['content'], $match);
						if (!is_string($match[1])) $match[1] = '';
						$ifContent = $match[1];
						$ifContentIsEmpty = preg_replace('/\s/', '', $ifContent) === '';
					}
					self::$isSwitchContext = $tagName == 'switch' || ($tagName == 'if' && $ifContentIsEmpty);
					if ($isSwitch) self::$isSwitchContext = true;
					$child = array();
					$isLet = 0;
					$data = self::getHtmlChildren($childrenList, $isLet, self::$isSwitchContext, $templateName);
					if ($isLet > 0) {
						for ($ii = 0; $ii < $isLet; $ii++) {
							$data[] = '</let>';
						}
					}
					if (!empty($data)) {
						if (!isset($data['c'])) {
							$child['c'] = $data;
						} else {
							$child['c'] = $data['c'];
							$child['e'] = $data['e'];
						}
					}
					if (!is_array($child['c'])) {
						$child['c'] = array();
					}
					if ($tagName == 'switch') {
						self::getSwitch($item, $child);
					} elseif ($tagName != 'foreach' && $tagName != 'if') {
						if ($tagName == 'forma') $tagName = 'form';
						$child['t'] = self::getTagIndex($tagName);
						self::getTagProperties($item, $child);
					} else {
						if ($tagName == 'if') {
							if ($ifContentIsEmpty) {
								getIfSwitch($item, $child);
							} else {
								self::checkIfConditionForContainigProps($match[1], $child);
							}
							if (!empty($child['e'])) {
								if ($child['e'][0]['t'] == 'else') {
									$child['e'] = array($child['e'][0]);
								}
								if (!empty($child['p']) && !empty($child['e'])) {
									array_unshift($child['e'], '<nq><function_returns_array>');
									array_push($child['e'], '</function_returns_array><nq>');				
								}
							}
						} elseif ($tagName == 'foreach') {
							self::getForeach($item, $child);
						}	
					}
				}
				$keys = array();
				if (is_array($child['c'])) {
					$keys = array_keys($child['c']);
				}
				$isSimpleArray = isset($keys[0]) && $keys[0] === 0;
				if (isset($child['c'])) {
					if (!empty($child['i']) && empty($child['p'])) {
						if (empty($child['aic'])) {
							array_unshift($child['c'], '<nq><nb>'.preg_replace('/<nq>$/', '?[<nc><nq>', $child['i']));
							if (empty($child['e'])) {
								array_push($child['c'], '<nq><nc>]:""<nb><nq>');
							} else {
								array_push($child['c'], '<nq><nc>]:'.str_replace('\\', '', json_encode($child['e'][0])).'<nb><nq>');
							}
							$child['i'] = true;
							unset( $child['aic'], $child['e']);
						} else {
							$ch = '<nq>'.str_replace('<nq>', '', $child['i']).'?'.str_replace('\\', '', json_encode($child['c'])).':'.(empty($child['e']) ? '""' : $child['e']).'<nq>';
							$child = $ch;
						}
					}
				}

				if (!isset($keys[0])) {
					unset($child['c']);
				} elseif ($isSimpleArray && count($child['c']) == 1) {
					$child['c'] = $child['c'][0];
				}
				if (!$isElse) {
					$children[] = $child;
				} else {
					$elseChildren[] = $child;
				}
			} else {
				if ($tagName == 'if') {
					$isElse = false;
				}
			}
		}
		if (!empty($elseChildren)) {
			if (is_array($elseChildren[0]['c'])) {
				$elseChildren = array($elseChildren[0]['c']);
			}
			return array('c' => $children, 'e' => $elseChildren);
		} else {
			return $children;
		}
	}

	private static function getForeach($item, &$child) {
		$child['h'] = $child['c'];
		unset($child['c']);

		$content = preg_replace('/\s{2,}/', ' ', $item['content']);
		$content = preg_replace('/\{foreach\s+|\}/', '', $content);
		$parts = explode(' ', trim($content));
		
		if ($parts[1] != 'as' || (isset($parts[3]) && $parts[3] != '=>')) {
			new Error(self::$errors['incorrectForeach'], array(self::$className, $item['content']));
		}
		$variable = $parts[0];
		if (isset($parts[4])) {
			$key = $parts[2];
			$val = $parts[4];
		} else {
			$key = '';
			$val = $parts[2];
			$parts = explode('=>', $val);
			if (isset($parts[1])) {
				$key = $parts[0];
				$val = $parts[1];
			}
		}
		if (!preg_match_all('/^([\$&~])(\w[\w\.]*)$/', $variable, $matches)) {
			new Error(self::$errors['incorrectForeach'], array(self::$className, $item['content']));
		}
		$sign = $matches[1][0];
		$variableParts = explode('.', $matches[2][0]);
		$variable = $variableParts[0];
		if ($sign == '&' || $sign == '~') {
			$variableParts[0] = '';
			$variableParts = implode('.', $variableParts);
			$variable .= $variableParts;
		} else {
			if (isset($variableParts[1])) {
				new Error(self::$errors['incorrectForeach'], array(self::$className, $item['content']));
			}
		}

		if (!preg_match('/^\&\w+$/', $val)) {
			new Error(self::$errors['incorrectForeach'], array(self::$className, $item['content']));
		}
		if (!empty($key) && !preg_match('/^\&\w+$/', $key)) {
			new Error(self::$errors['incorrectForeach'], array(self::$className, $item['content']));
		}
		if ($sign == '~') {
			$child['p'] = "<nq>_['".$variable."']<nq>";
		} elseif ($sign == '&') {
			$child['p'] = '<nq>'.$variable.'<nq>';
		} else {
			$child['p'] = "<nq>\<this>g('".$variable."')<nq>";
			$child['f'] = $variable;
		}
		if (!empty($key)) {
			$key = ','.str_replace('&', '', $key);
		}
		$val = str_replace('&', '', $val);
		array_unshift($child['h'], '<foreach '.$val.$key.'>');
		array_push($child['h'], '</foreach>');
		return $child;
	}

	private static function getSwitch($item, &$child) {
		preg_match('/^\{\s*switch\s*([^\s\}]+)\s*\}$/', $item['content'], $match);
		$switch = $match[1];
		if (empty($switch)) {
			new Error(self::$errors['switchError'], array(self::$className, $item['content']));
		}
		preg_match('/\$(\w+)/', $switch, $match);
		$param = $match[1];
		$switch = self::parseCode($switch, 'sw');

		$cases = array();
		$children = array();
		$default = array();
		$isDefault = false;
		$count = -1;
		$shouldBeCase = true;
		foreach ($child['c'] as $item) {
			$isString = is_string($item);
			if ($shouldBeCase && !$isString) {
				new Error(self::$errors['caseExpected'], array(self::$className));
			}
			if ($isString) {
				$it = trim(strip_tags($item));
				if ($it == 'default') {
					if (!empty($default)) {
						new Error(self::$errors['fewDefaults'], array(self::$className));
					}
					if (!empty($shouldBeContent)) {
						new Error(self::$errors['noSwitchContent'], array(self::$className, $shouldBeContent));
					}
					$isDefault = true;
					$shouldBeCase = false;
					$shouldBeContent = $item;
					continue;
				}
				$pos = strpos($it, 'case');
				if (is_int($pos)) {
					if ($pos !== 0) {
						new Error(self::$errors['incorrectCaseCode'], array(self::$className, $it));
					}
					if (!empty($shouldBeContent)) {
						new Error(self::$errors['conditionEmpty'], array(self::$className, $shouldBeContent));
					}
					if (!preg_match('/^\s*case\s*\'[^\']*\'\s*$/', $it) && !preg_match('/^\s*case\s*"[^"]*"\s*$/', $it) && !preg_match('/^\s*case\s+\-*\d+\s*$/', $it) && !preg_match('/^\s*case\s+(true|false|null|undefined)\s*$/', $it)) {
						new Error(self::$errors['incorrectCaseCode'], array(self::$className, $it));
					}
					$it = trim(preg_replace('/\s*case\s*/', '', $it));
					if (!is_numeric($it) && $it[0] != '"' && $it[0] != "'") {
						$it = '<nq>'.$it.'<nq>';
					} elseif (!is_numeric($it)) {
						$it = preg_replace('/[\'"]/', '', $it);
					}
					$cases[] = $it;
					$shouldBeCase = false;
					$shouldBeContent = $item;
					$count++;
					continue;
				} elseif ($shouldBeCase) {
					new Error(self::$errors['caseExpected'], array(self::$className));
				}
			}
			if ($isDefault) {
				$default[] = $item;
			} else {
				if (!is_array($children[$count])) {
					$children[$count] = array();
				}
				$children[$count][] = $item;
			}
			$shouldBeContent = false;
		}
		$child['sw'] = $switch;
		$child['s'] = $cases;
		$child['c'] = $children;
		if (!empty($default)) {
			$child['d'] = $default;
		}
		if (!empty($param)) {
			$child['p'] = $param;
			array_unshift($child['c'], '<nq><function_returns_array>');
			array_push($child['c'], '</function_returns_array><nq>');
		}
	}

	private static function getTemplateProperties($html, &$child) {
		$regexp = '/\{([^\}]+)\}/';
		$props = array();
		$names = array();
		$html = preg_replace('/([\'"])(\w)/', "$1 $2", $html);
		preg_match_all("/ ([a-z][\w\-]*)=\"([^\"]+)\"/", $html, $matches1);
		preg_match_all("/ ([a-z][\w\-]*)='([^']+)'/", $html, $matches2);
		$propNames = array_merge($matches1[1], $matches2[1]);
		$propValues = array_merge($matches1[2], $matches2[2]);
		$ifCondition = false;
		$else = null;
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
			if ($hasCode) {
				if (is_string(self::$class)) {
					new Error(self::$errors['reactVarInInclude'], array(self::$class, $propValue));
				}
				if (preg_match('/\$\w+[\[]/', $propValue)) {
					new Error(self::$errors['incorrectReactVar'], array(self::$className, $propValue));
				}				
				preg_match_all($regexp, $propValue, $matches);
				$codes = $matches[1];
				if (!empty($codes)) {
					$parts = preg_split($regexp, $propValue);
					$content = array();
					foreach ($parts as $j => $part) {
						if (!empty($part)) {
							$content[] = $part;
						}
						if (isset($codes[$j])) {
							$content[] = '<plus>'.self::parseCode($codes[$j], 'tmp').'</plus>';
						}
					}
					$propValue = implode($content);
				}
			}
			$props[$propName] = $propValue;
		}
		if (!empty($props)) {
			$child['p'] = $props;
		}
		if (!empty($ifCondition) || !empty($else)) {
			self::addIfConditionToChild(trim($ifCondition), $else, $child);
		}
	}

	private static function addIfConditionToChild($ifCondition, $else, &$child) {
		if (!empty($else) && empty($ifCondition)) {
			new Error(self::$errors['elseWithoutIf'], array(self::$className));
		}
		if (!preg_match('/^\{[^\}]+\}$/', $ifCondition)) {
			new Error(self::$errors['incorrectIf'], array(self::$className, $ifCondition));
		}
		$ifCondition = ltrim($ifCondition, '{');
		$ifCondition = rtrim($ifCondition, '}');

		$child = array('c' => $child);
		if (!empty($else)) {
			$else = ltrim($else, '{');
			$else = rtrim($else, '}');
			if (preg_match('/\$\w+[\[]/', $else)) {
				new Error(self::$errors['incorrectReactVar'], array(self::$className, $else));
			}
			$child['e'] = self::parseCode($else, 'else');
		}
		self::checkIfConditionForContainigProps($ifCondition, $child, true);
	}

	private static function checkIfConditionForContainigProps($ifCondition, &$child, $addingIntoChild = false) {
		if (is_array(self::$class) && preg_match('/\$\w+[\[]/', $ifCondition)) {
			new Error(self::$errors['incorrectReactVar'], array(self::$className, $ifCondition));
		}
		$hasCode = preg_match('/\$\w/', $ifCondition);
		if (is_string(self::$class) && $hasCode) {
			new Error(self::$errors['reactVarInInclude'], array(self::$class, $ifCondition));
		}
		self::parseClassMethodCalls($ifCondition);
		$signs = '[\+\-><\!=\/\*%\?:&\|]';
		$ifCondition = preg_replace('/\s+(?='.$signs.')/', '', $ifCondition);
		$ifCondition = preg_replace('/('.$signs.')\s+/', "$1", $ifCondition);
		$ifCondition = preg_replace('/\&(\w+)/', "$1", $ifCondition);
		$ifCondition = preg_replace('/~(\w+)/', "_['$1']", $ifCondition);
		if ($hasCode) {
			preg_match_all('/\$(\w+)/', $ifCondition, $matches);
			$ifCondition = preg_replace('/\$(\w+)/', "\<this>g('$1')", $ifCondition);
			if (!empty($matches[1])) {
				$child['p'] = $matches[1];
			}
			$child['i'] = '<nq>function(){return('.$ifCondition.')}<nq>';
			if (count($child['c']) < 2) {
				array_unshift($child['c'], '<nq><function>');
				array_push($child['c'], '</function><nq>');
			} else {
				array_unshift($child['c'], '<nq><function_returns_array>');
				array_push($child['c'], '</function_returns_array><nq>');
			}
			if (count($child['c']) == 2) {
				$child['c'] = array('<nq><function>','','</function><nq>');
			}
		} else {
			if (!preg_match('/'.$signs.'/', $ifCondition)) {
				$child['i'] = '<nq>!!'.$ifCondition.'<nq>';
			} else {
				$child['i'] = '<nq>'.$ifCondition.'<nq>';
			}
			$child['aic'] = $addingIntoChild;
		}
	}

	private static function getTagIndex($tagName) {
		$tagNameIndex = array_search($tagName, self::$tagShortcuts);
		return $tagNameIndex !== false ? $tagNameIndex : $tagName;
	}

	private static function getTagProperties($item, &$child, $isComponentTag = false) {
		$html = $item['content'];
		$props = array();
		$names = array();
		$ifCondition = false;
		$else = null;

		$html = preg_replace('/="([^"]*)"(?!\s)/', "=\"$1\" ", $html);
		$html = preg_replace('/=\'([^\']*)\'(?!\s)/', "='$1' ", $html);
		$html = preg_replace('/\sscope([\s>])/', " scope=\"1\"$1", $html);
		preg_match_all("/ ([a-z][\w\-]*)=\"([^\"]+)\"/", $html, $matches1);
		preg_match_all("/ ([a-z][\w\-]*)='([^']+)'/", $html, $matches2);
		$propNames = array_merge($matches1[1], $matches2[1]);
		$propValues = array_merge($matches1[2], $matches2[2]);
		for ($i = 0; $i < count($propNames); $i++) {		
			$propName = $propNames[$i];
			$propValue = trim($propValues[$i]);
			$hasCode = self::hasCode($propValue);
			$fullPropName = $propName;
			$isObfClName = self::$obfuscate === true && $fullPropName == 'class';
			$isTag = !$isComponentTag && !isset($child['tmp']);

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
				if (preg_match("/\bon(\w+)/i", $propName, $match)) {
					if ($hasCode) {
						new Error(self::$errors['eventHandlerExpected'], array($propName, self::$className));
					}
					if (!is_array($child['e'])) {
						$child['e']	= array();
					}
					$eventType = strtolower($match[1]);
					$once = false;
					$parts = preg_split('/once/i', $eventType);
					if (isset($parts[1]) && empty($parts[1])) {
						$eventType = preg_replace("/once$/i", '', $eventType);
						$once = true;
					}
					$isDispatching = preg_match('/^\!/', $propValue);
					preg_match_all('/\(([^\)]*)\)/', $propValue, $matches);
					$args = '';
					if (!empty($matches[1])) {
						$propValue = str_replace($matches[0][0], '', $propValue);
						$args = $matches[1][0];
					}
					$callback = preg_replace("/[^\w]/", "", $propValue);
					if (!$isDispatching && is_array(self::$class) && !self::hasComponentMethod($callback, self::$class)) {
						new Error(self::$errors['handlerNotFound'], array($callback, self::$className));
					}
					$eventTypeIndex = array_search($eventType, self::$eventTypesShortcuts);
					if ($eventTypeIndex > -1) {
						$eventType = $eventTypeIndex;
					}
					$child['e'][] = $eventType;
					if (!$isDispatching) {
						if (empty($args)) {
							$child['e'][] = '<nq><this>'.$callback.'<nq>';
						} else {
							$child['e'][] = '<nq><this>'.$callback.'.bind($,'.$args.')<nq>';
						}
					} else {
						$child['e'][] = $callback;
					}
					if ($once) {
						$child['e'][] = true;
					}
					continue;
				}
			}
			$props[$propName] = $propValue;
			if ($hasCode) {
				$propValue = preg_replace("/\{\s*\#([a-z]\w*)\s*\}/", "{__#$1}", $propValue);
				$propValue = preg_replace("/&(\w+)/", "$1", $propValue);
				$propValue = preg_replace("/~(\w+)/", "_['$1']", $propValue);
				$propValue = preg_replace("/@(\w+)/", "<nq>__.$1<nq>", $propValue);

				$regexp = '/\{([^\}]*)\}/';
				$hasClassVar = self::hasClassVar($propValue);
				preg_match_all($regexp, $propValue, $matches);
				$codes = $matches[1];
				$parts = preg_split($regexp, $propValue);
				$names[$propName] = array();
				$attrContent = '';
				$attrParts = array();
				foreach ($parts as $idx => $part) {
					if ($part !== '') {
						if ($isObfClName) {
							$part = self::getObfuscatedClassName($part);
						}
						$attrContent .= $part;
						$attrParts[] = $part;
					}
					if (isset($codes[$idx])) {
						$code = $codes[$idx];
						$code = self::checkTernary($code);
						if ($isObfClName) {
							$code = self::getObfuscatedClassName($code, true);
						}
						if (self::hasClassVar($code)) {
							$code = self::parseAttributeClassVars($code, $names[$propName]);
							$attrParts[] = '<nq>'.$code.'<nq>';
						} else {
							if ($hasClassVar) {
								$attrParts[] = '<nq>'.$code.'<nq>';
							} else {
								$attrContent .= '<plus>'.$code.'</plus>';
							}
						}
					}
				}
				
				if ($hasClassVar) {
					$attrContent = implode('"+"', $attrParts);
				}
				self::parseClassMethodCalls($attrContent);
				if ($hasClassVar) {
					$attrContent = '<nq><function>"'.$attrContent.'"</function><nq>';
				}				
				$attrContent = self::correctTagAttributeText($propName, $attrContent);
				$props[$propName] = $attrContent;
				$names[$propName] = array_unique($names[$propName]);
				sort($names[$propName]);
				if (count($names[$propName]) == 1) {
					$names[$propName] = $names[$propName][0];
				}
				if (empty($names[$propName])) {
					unset($names[$propName]);
				}
			} else if ($isObfClName) {
				$props[$propName] = self::getObfuscatedClassName($propValue);
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
				new Error(self::$errors['unknownComponent'], array(self::$className, $item['content'], $comp));
			}
			if ($item['tagName'] == 'control' && empty($child['nm'])) {
				new Error(self::$errors['controlWithoutName'], array($child['cmp'], self::$className, $item['content'], $comp));
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
					preg_match('/^\^(\w+)$/', $v, $match);
					if (!empty($match[1])) {
						unset($child['p'][$k]);
						if (!is_array($child['w'])) {
							$child['w'] = array();
						}
						$child['w'][] = $k;
						$child['w'][] = $match[1];
					}
				}
				if (empty($child['p'])) {
					unset($child['p']);
				} else {
					self::getProperComponentData($child);
				}
			}
		}
		if (!empty($ifCondition) || !empty($else)) {
			self::addIfConditionToChild(trim($ifCondition), trim($else), $child);
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
					if ($k != 'args') $k = preg_replace('/^arg-/', '', $k);
					if (!is_array($child['na'])) {
						$child['na'] = array();
					}
					$child['na'][$k] = $v;
				}
			}
			if (empty($child['n'])) unset($child['n']);
		}
		if (is_array($child['n']) && is_array($child['na'])) {
			$properNames = array();
			foreach ($child['n'] as $n) {
				if (!in_array($n, $child['na'])) {
					$properNames[] = $n;
				}
			}
			$child['n'] = $properNames;
			if (empty($child['n'])) unset($child['n']);
		}
		if (is_array($child['n'])) {
			$child['n'] = array_unique($child['n']);
		}
		if (is_array($child['na'])) {
			$child['na'] = array_unique($child['na']);
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

	private static function parseAttributeClassVars($code, &$names) {
		if (preg_match('/\$\w+[\[]/', $code)) {
			if (is_array(self::$class)) {
				new Error(self::$errors['incorrectReactVar'], array(self::$className, $code));
			}
		}
		$regexp = '/\$(\w+)/';
		preg_match_all($regexp, $code, $matches);
		if (!empty($matches[1])) {
			if (is_string(self::$class)) {
				new Error(self::$errors['reactVarInInclude'], array(self::$class, $code));
			}
			foreach ($matches[1] as $i => $match) {
				$names[] = $match;
			}
		}
		return preg_replace($regexp, "\<this>g('$1')", $code);
	}

	private static function hasCode($text) {
		return preg_match("/\{[^\}]+\}/", $text);
	}


	private static function parseComponentClassName($value, $content, $isControlName = false) {
		if (!self::hasCode($value)) return !$isControlName ? '<nq>'.$value.'<nq>' : $value;
		$hasReactive = preg_match('/\$\w/', $value);
		if ($hasReactive) {
			if (!$isControlName) {
				new Error(self::$errors['reactComponentName'], array(self::$className, $content));
			} else {
				new Error(self::$errors['reactControlName'], array(self::$className, $content));
			}
		}
		$value = ltrim($value, '{');
		$value = rtrim($value, '}');
		return self::parseCode($value);
	}

	private static function parseTextNode($content, &$children, &$let) {
		if (!empty($content)) {
			$items = self::checkTextForContainigProps($content, $let);
			foreach ($items as $item) {
				if (is_array($item)) {
					$children[] = $item[0];
				} else if (strlen($item) > 3) {
					$children[] = '<nq>__T['.addTextNode($item).']<nq>';
				} else {
					$children[] = $item;
				}
			}
		}
	}

	private static function checkTextForContainigProps($text, &$let) {
		$regexp = '/\{([^\}]+)\}/';
		preg_match_all($regexp, $text, $matches);
		$codes = $matches[1];
		if (empty($codes)) {
			return array($text);
		}
		if (preg_match('/\$\w+[\[]/', $text)) {
			if (is_array(self::$class)) {
				new Error(self::$errors['incorrectReactVar2'], array(self::$className, $text));
			} else {
				new Error(self::$errors['reactVarInInclude'], array(self::$class, $text));
			}
		}
		$parts = preg_split($regexp, $text);
		$content = array();
		foreach ($parts as $i => $part) {
			if (!empty($part)) {
				$content[] = $part;
			}
			if (isset($codes[$i])) {
				if (preg_match('/^\s*let\s/', $codes[$i])) {
					if (preg_match('/^\s*let &[a-z][\w\.\'"\[\]]*\s*[=:]\s*[^\s]+\s*$/i', $codes[$i])) {
						$codes[$i] = preg_replace('/^\s*let &(\w+)\s*[:=]\s*(.+)/', "<let>var $1=$2<=let>", $codes[$i]);
						$codes[$i] = preg_replace('/^\s*let &(\w[^\s:=]*)\s*[:=]\s*(.+)/', "<let>$1=$2<=let>", $codes[$i]);
						$let++;
					} else {
						new Error(self::$errors['letError'], array(self::$className, $codes[$i]));
					}
				}
				$content[] = array(self::parseCode($codes[$i], 'prop', true));
			}
		}
		return $content;
	}

	private static function parseCode($code, $role = null, $toPropNodes = false) {
		$code = trim($code);
		self::parseClassMethodCalls($code);
		$code = self::checkTernary($code);
		$code = preg_replace('/\s*@(\w+)\s*/', "__.$1", $code);
		$code = preg_replace('/^\s*::(\d+)\s*(=.+)*$/', "{'pl':$1,'d':'<noeq>$2'}", $code);
		$code = preg_replace('/^\s*::(\w+)\s*(=.+)*$/', "{'pl':'$1','d':'<noeq>$2'}", $code);
		$code = preg_replace('/<noeq>=*/', '', $code);
		if ($toPropNodes) {
			if (preg_match('/\bcase\b/', $code)) {
				if (!self::$isSwitchContext) new Error(self::$errors['caseOutsideSwitch'], self::$className);
			}
			if (preg_match('/\#\w/', $code)) {
				new Error(self::$errors['dataInTextNode'], array($code, self::$className));
			}
			if (preg_match('/\$\w/', $code)) {
				$regexp = '/\$([a-z][\w+\.]*)/i';
				preg_match_all($regexp, $code, $matches);
				$matches = array_unique($matches[1]);
				sort($matches);
				$p = array();
				$n = array();
				foreach ($matches as $i => $match) {
					$parts = explode('.', $match);
					if (count($parts) > 1) {
						$name = $parts[0];
						$n[] = $name;
						array_shift($parts);
						$p[] = "\<this>g('".$name."',['".implode("','",$parts)."'])";
					} else {
						$n[] = $match;
						$p[] = "\<this>g('".$match."')";
					}
				}
				$parts = preg_split($regexp, $code);
				$c = '';
				foreach ($parts as $i => $part) {
					$c .= $part;
					if (isset($p[$i])) {
						$c .= $p[$i];
					}
				}
				if (count($n) > 1) {
					$n = '<nq>'.json_encode($n).'<nq>';
				} else {
					$n = "'".$n[0]."'";
				}
				$code = "{'pr':".$n.",'p':".$c."}";
			}
		} else {
			$regexp = '/\$([a-z][\w+\.\-]*)/i';
			preg_match_all($regexp, $code, $matches);
			$parts = preg_split($regexp, $code);
			$matches = $matches[1];
			$code = '';
			foreach ($parts as $i => $part) {
				$code .= $part;
				if (isset($matches[$i])) {
					$p = explode('.', $matches[$i]);
					if (count($p) == 1) {
						$code .= "<this>g('".$matches[$i]."')";
					} else {
						$name = $p[0];
						array_shift($p);
						$code .= "<this>g('".$name."',['".implode("','",$p)."'])";
					}
				}
			}
		}
		$code = preg_replace('/\#([a-z]\w*)/i', "__#$1", $code);
		$code = preg_replace('/^&([a-z])/i', "$1", $code);
		$code = preg_replace('/([^&])&([a-z])/i', "$1$2", $code);
		$code = preg_replace('/~([a-z]\w*)/i', "_['$1']", $code);		
		return '<nq>'.preg_replace('/\s+([\?:\+\-><=\!]{1,3})\s+/', "$1", $code).'<nq>';
	}

	private static function parseClassMethodCalls(&$code) {
		if (preg_match('/\bthis\./', $code)) {
			new Error(self::$errors['usingThis'], self::$className);
		}
		$hasFunctionCall = self::hasFunctionCall($code);
		if ($hasFunctionCall) {
			if (!is_array(self::$class['tmpCallbacks'])) {
				self::$class['tmpCallbacks'] = array();
			}
			self::$class['tmpCallbacks'] = array_merge(self::$class['tmpCallbacks'], $hasFunctionCall);
			$code = preg_replace('/\.(\w+)\(([^\)]*)\)/', "<this>$1($2)", $code);
			$code = preg_replace('/^\s*\.(\w+)/', "<this>$1()", $code);
			$code = preg_replace('/([^\w\]])\.(\w+)/', "$1<this>$2()", $code);					
		}
		return !!$hasFunctionCall;
	}

	private static function checkTernary($code) {
		$originalCode = $code;
		if (preg_match('/\?/', $code)) {
			$originalCode = '('.trim(trim($originalCode, ')'), '(').')';
			$strings = array();
			$signs = array("'", '"');
			for ($i = 0; $i < 2; $i++) {
				$strings[$i] = array();
				$parts = explode($signs[$i], $code);
				$code = '';
				$isString = false;
				foreach ($parts as $part) {
					if ($isString) {
						$strings[$i][] = $part;
						$code .= '__S'.$i.'__';
					} else {
						$code .= $part;
					}
					$isString = !$isString;
				}
			}
			if (preg_match('/\?[^:]+$/', $code)) {
				$strings = array_reverse($strings);
				$code = trim($code).":''";
				$signs = array('__S1__', '__S0__');
				$signs2 = array('"', "'");
				for ($i = 0; $i < 2; $i++) {
					$parts = explode($signs[$i], $code);
					$code = '';
					foreach ($parts as $j => $part) {
						$code .= $part;
						if (isset($strings[$i][$j])) {
							$code .= $signs2[$i].$strings[$i][$j].$signs2[$i];
						}
					}

				}
				$originalCode = '('.$code.')';
			}
		}
		return $originalCode;
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

	private static function getObfuscatedClassName($value, $isCode = false) {
		if (!$isCode) {
			$value = "'".$value."'";
		}
		$value = preg_replace('/\[\s*[\'"]([^\'"]+)[\'"]\s*\]/', "[#$1#]", $value);
		$regexp = '/"[^"]+"|\'[^\']+\'/';
		preg_match_all($regexp, $value, $matches);
		$strings = $matches[0];
		$codeParts = preg_split($regexp, $value);
		$obfuscatedValue = '';
		foreach ($codeParts as $i => $codePart) {
			$obfuscatedValue .= $codePart;
			if (isset($strings[$i])) {
				$string = preg_replace('/["\']/', '', $strings[$i]);
				$parts = explode(' ', $string);
				$newClassName = array();
				foreach ($parts as $part) {
					if (!empty($part) && isset(self::$cssClassIndex[$part])) {
						$newClassName[] = self::$cssClassIndex[$part];
					} else {
						if (!empty($part)) {
							$part = self::addToCssClassIndex($part);
						}
						$newClassName[] = $part;
					}
				}
				$obfuscatedValue .= "'".implode(' ', $newClassName)."'";
			}
		}		
		if (!$isCode) {
			$obfuscatedValue = trim($obfuscatedValue, "'");
		}
		$obfuscatedValue = preg_replace('/\[\#([^\#]+)\#\]/', "['$1']", $obfuscatedValue);
		return preg_replace('/ {2,}/', ' ', $obfuscatedValue);
	}

	private static function addToCssClassIndex($className) {
		$obfuscatedClassName = CSSObfuscator::generate();
		self::$cssClassIndex[$className] = $obfuscatedClassName;
		return $obfuscatedClassName;
	}
}