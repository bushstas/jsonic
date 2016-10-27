<?php

class TemplateParser 
{	
	private static $calledClasses, $classes, $templates, $sources;
	private static $regexp = "/\{ *template +\.(\w+) *\}/";
	private static $simpleTags = array('br', 'input', 'img', 'hr');
	private static $class, $className, $tmpids, $isSwitchContext,
				   $propsShortcuts, $eventTypesShortcuts, $obfuscate,
				   $tagShortcuts, $cssClassIndex, $templateName,
				   $globalNames, $parsedItem;

	private static $textNodes = array();

	private static $errors = array(
		'noMainTemplate' => 'Шаблон <b>main</b> класса {??} не найден среди прочих',
		'noClosingTag' => 'Ошибка валидации шаблона {??} класса {??}. Один из {?} {??} не имеет закрывающего тега',
		'extraClosingTag' => 'Ошибка валидации шаблона {??}  класса {??}. Обнаружен лишний закрывающийся {?} {??}',
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
		'incorrectForeach' => 'Невалидный код <b>foreach</b> в шаблоне {??} класса {??}: {??}',
		'switchError' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}<xmp>{?}</xmp><b>Ожидается код вида</b><xmp>{switch \$type}</xmp><b>или</b><xmp>{switch ~type}</xmp><b>или</b><xmp>{switch &type}</xmp><b>или</b><xmp>{switch .getType(\$a, ~b, &c)}</xmp>",
		'caseExpected' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Ожидается оператор <b>case</b><xmp>{case 'triangle'}</xmp>или<xmp>{case 2}</xmp>",
		'fewDefaults' => 'Обнаружено более одного условия <b>default</b> в коде оператора <b>switch</b> в шаблоне {??} класса {??}',
		'noSwitchContent' => 'Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Оператор {??} не содержит контента',
		'incorrectCaseCode' => "Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Некоррекнтый код в операторе <b>case</b><xmp>{{?}}</xmp>",
		'conditionEmpty' => 'Обнаружена ошибка в коде оператора <b>switch</b> в шаблоне {??} класса {??}. Условие {??} не содержит контента',
		'elseWithoutIf' => 'Элемент в шаблоне {??} класса {??} содержит атрибут <b>else</b>, но не содержит атрибут <b>if</b>',
		'incorrectIf' => 'Элемент в шаблоне {??} класса {??} содержит некорректный атрибут <b>if = "{?}"</b><br><br>Атрибут должен иметь вид <b>if = "{$a === true}"</b> или <b>if = "{!&name}"</b>',
		'eventHandlerExpected' => 'Фигурные скобки внутри атрибута события {??} в шаблоне {??} класса {??}. Ожидается название функции обработчика!',
		'handlerNotFound' => 'Функция {??}, указанная в шаблоне {??} класса {??} в качестве обработчика события {??}, не найдена среди методов данного класса',
		'noTemplateName' => 'Вызов шаблона без указания его имени в шаблоне {??} класса {??}. Код должен иметь вид:<xmp><template templ="table" rows="{~rows}"></xmp>',
		'noIncludeTemplateName' => 'Вызов шаблона без указания его имени в шаблоне {??} класса {??}. Код должен иметь вид:<xmp><include templ="table" rows="{~rows}"></xmp>',
		'codeOutsideAttribute' => 'Обнаружен код вне атрибута тега в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>'
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
		self::$globalNames = JSGlobals::getUsedNames();
		$varNames = array_values(JSGlobals::getVarNames());
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
				//replacing
				//Printer::log($data);
				
				$data = str_replace('<this>', '$.', $data);
				$data = preg_replace("/'<nq>/", '', $data);
				$data = preg_replace("/<nq>'/", '', $data);
				$data = preg_replace("/<nq>/", '', $data);
				$data = preg_replace("/,*<nc>,*/", '', $data);
				$data = preg_replace("/\[*<nb>\]*/", '', $data);

				$data = str_replace('<plus>', "'+", $data);
				$data = str_replace('<\/plus>', "+'", $data);
				$data = str_replace('\\', '', $data);
				

				$data = preg_replace("/\['<foreach ([^>]+)>',/", "function($1){return[", $data);
				$data = preg_replace("/,*'<\/foreach>'\]/", ']}', $data);

				
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
		$html = preg_replace('/<(\w+)([^>]*)\/>/', "<$1$2></$1>", $html);
		$html = preg_replace('/<\/(img|br|hr|input|component|control|form|menu)>/', '', $html);
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
		$regexp = "/(<\/*[a-z]+[^>]*>|\{\s*\/*foreach[^\}]*\}|\{\s*\/*if[^\}]*\}|\{\s*else\}|\{\s*\/*switch[^\}]*\})/i";
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
				preg_match('/^[<\{]\s*\/*([a-z]\w*) */i', $tags[$j], $match);
				$tagName = strtolower($match[1]);
				$tagContent = $tags[$j];
				$isClosing = self::isTagClosing($tagName, $tagContent);
				$list[] = array('type' => 'tag', 'content' => $tagContent, 'tagName' => $tagName, 'isClosing' => $isClosing);
			}
		}
		$isLet = 0;
		self::checkTagsPairing($list);
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
				new Error(self::$errors['noClosingTag'], array(self::$templateName, self::$class['name'], $object, strtoupper($tn)));
			}
		}
		foreach ($closed as $tn => $count) {
			if ($count > $opened[$tn]) {
				$object = $tn == 'if' || $tn == 'switch' || $tn == 'foreach' ? 'оператор' : 'тег';
				new Error(self::$errors['extraClosingTag'], array(self::$templateName, self::$class['name'], $object, strtoupper($tn)));
			}
		}
	}

	private	static function getHtmlChildren($list, &$let, $isSwitch = false) {
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
				elseif ($tagName == 'template' || $tagName == 'include')
				{
					self::getTemplateProperties($item['content'], $child, $tagName == 'include');
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
					$data = self::getHtmlChildren($childrenList, $isLet, self::$isSwitchContext);
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
						if (!is_array($data) || !isset($data['c'])) {
							$child['c'] = $data;
						} else {
							$child['c'] = $data['c'];
							$child['e'] = $data['e'];
						}
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
							if (is_array($child) && !empty($child['e'])) {
								$json = json_encode($child['e']);
								if (preg_match('/\$\.g\(/', $json)) {
									self::wrapInFunction($child['e']); 
								} else {
									$child['e'] = self::getProperChildren($child['e']);
								}
							}
						} elseif ($tagName == 'foreach') {
							self::getForeach($item, $child);
						}	
					}
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
				$elseChildren = $elseChildren[0]['c'];
			}
			return array('c' => $children, 'e' => $elseChildren);
		} else {
			return $children;
		}
	}

	private static function getProperChildren($children) {
		if (is_array($children)) {
			while (is_array($children) && isset($children[0])) {
				if (count($children) == 1) {
					$children = $children[0];
				} else {
					break;
				}
			}
		}
		return '<nq>'.(is_array($children) ? str_replace('\\', '', json_encode($children)) :  $children).'<nq>';
	}

	private static function wrapInFunction(&$children) {		
		$children = '<nq>function(){return '.self::getProperChildren($children).'}<nq>';
	}

	private static function getForeach($item, &$child) {
		$child['h'] = $child['c'];
		unset($child['c']);

		$content = preg_replace('/\s{2,}/', ' ', $item['content']);
		$content = preg_replace('/\{foreach\s+|\}/', '', $content);
		$parts = explode(' ', trim($content));
		
		if ($parts[1] != 'as' || (isset($parts[3]) && $parts[3] != '=>')) {
			new Error(self::$errors['incorrectForeach'], array(self::$templateName, self::$className, $item['content']));
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
			new Error(self::$errors['incorrectForeach'], array(self::$templateName, self::$className, $item['content']));
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
				new Error(self::$errors['incorrectForeach'], array(self::$templateName, self::$className, $item['content']));
			}
		}

		if (!preg_match('/^\&\w+$/', $val)) {
			new Error(self::$errors['incorrectForeach'], array(self::$templateName, self::$className, $item['content']));
		}
		if (!empty($key) && !preg_match('/^\&\w+$/', $key)) {
			new Error(self::$errors['incorrectForeach'], array(self::$templateName, self::$className, $item['content']));
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
		preg_match('/^\{\s*switch\s*([^\}]+)\}$/', $item['content'], $match);
		$switch = $match[1];
		if (empty($switch)) {
			new Error(self::$errors['switchError'], array(self::$templateName, self::$className, $item['content']));
		}
		preg_match('/\$(\w+)/', $switch, $match);
		$param = $match[1];

		$cases = array();
		$children = array();
		$default = array();
		$isDefault = false;
		$count = -1;
		$shouldBeCase = true;
		foreach ($child['c'] as $item) {
			$isString = is_string($item);
			if ($shouldBeCase && !$isString) {
				new Error(self::$errors['caseExpected'], array(self::$templateName, self::$className));
			}
			if ($isString) {
				$it = trim(strip_tags($item));
				if ($it == 'default') {
					if (!empty($default)) {
						new Error(self::$errors['fewDefaults'], array(self::$templateName, self::$className));
					}
					if (!empty($shouldBeContent)) {
						new Error(self::$errors['noSwitchContent'], array(self::$templateName, self::$className, $shouldBeContent));
					}
					$isDefault = true;
					$shouldBeCase = false;
					$shouldBeContent = $item;
					continue;
				}
				$pos = strpos($it, 'case');
				if (is_int($pos)) {
					if ($pos !== 0) {
						new Error(self::$errors['incorrectCaseCode'], array(self::$templateName, self::$className, $it));
					}
					if (!empty($shouldBeContent)) {
						new Error(self::$errors['conditionEmpty'], array(self::$templateName, self::$className, $shouldBeContent));
					}
					if (!preg_match('/^\s*case\s*\'[^\']*\'\s*$/', $it) && !preg_match('/^\s*case\s*"[^"]*"\s*$/', $it) && !preg_match('/^\s*case\s+\-*\d+\s*$/', $it) && !preg_match('/^\s*case\s+(true|false|null|undefined)\s*$/', $it)) {
						new Error(self::$errors['incorrectCaseCode'], array(self::$templateName, self::$className, $it));
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
					new Error(self::$errors['caseExpected'], array(self::$templateName, self::$className));
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
			$child['c'] = '<nq>function(){return '.str_replace('\\', '', json_encode($child['c'])).'}<nq>';
		}
	}

	private static function getTemplateProperties($html, &$child, $isInclude = false) {
		$tmpName = '';
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
			if ($propName == 'templ') {
				$tmpName = $propValue;
				$child['tmp'] = '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>';
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
			$hasCode = self::hasCode($propValue);
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
		self::checkIfConditionForContainigProps($ifCondition, $child);
	}

	private static function checkIfConditionForContainigProps($ifCondition, &$child) {
		$hasCode = preg_match('/\$\w/', $ifCondition);
		if (is_string(self::$class) && $hasCode) {
			new Error(self::$errors['reactVarInInclude'], array(self::$templateName, self::$class, $ifCondition));
		}
		$names = array();
		if ($ifCondition[0] != '{') $ifCondition = '{'.$ifCondition.'}';
		$ifCondition = self::processCode($ifCondition, 'if', $names);
		
		//Printer::log($child, true);
		
		$child['i'] = $ifCondition;
		if (!empty($names)) {
			$child['p'] = $names;
			if (empty($child['c'])) {
				$child['c'] = "<nq>function(){return''}</nq>";			 
			} else {
				$child['c'] = '<nq>function(){return '.str_replace('\\', '', json_encode($child['c'])).'}<nq>';
			}
		} else {
			//Printer::log($child['i']);
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
				//Printer::log($else);
			}
			$child = '<nq>'.str_replace('<nq>', '', $child['i']).'?'.$then.':'.$else.'<nq>';
			//unset($child['i'], $child['e']);
		}
		//Printer::log($child);
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

	private static function processCode($code, $parsedPlace = 'elementAttribute', &$names = null) {
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
				//Printer::log($data);
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
				if ($isObfClName) {
					self::getObfuscatedClassName($code);
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
		if ($inFunc) {
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
				if (preg_match("/\bon(\w+)/i", $propName, $match)) {
					if ($hasCode) {
						new Error(self::$errors['eventHandlerExpected'], array($propName, self::$templateName, self::$className));
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
						new Error(self::$errors['handlerNotFound'], array($callback, self::$templateName, self::$className, 'on'.$match[1]));
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
				$names[$propName] = array();
				$parsedPlace = $isComponentTag ? 'componentAttribute' : 'elementAttribute';
				$code = self::processCode($propValue, $parsedPlace, $names[$propName]);
				$code = self::correctTagAttributeText($propName, $code);
				//Printer::log($code);

				$props[$propName] = $code;
				$names[$propName] = array_unique($names[$propName]);
				sort($names[$propName]);
				if (count($names[$propName]) == 1) {
					$names[$propName] = $names[$propName][0];
				}
				if (empty($names[$propName])) {
					unset($names[$propName]);
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

	private static function parseTextNode($content, &$children, &$let) {
		
		if (!empty($content)) {
			
			$regexp = '/\{([^\}]+)\}/';
			preg_match_all($regexp, $content, $matches);
			$codes = $matches[1];
			$count = count($codes);
			if (empty($codes)) {
				if (strlen($content) > 3) {
					$children[] = '<nq>'.self::$globalNames['TEXTS'].'['.self::addTextNode($content).']<nq>';
				} else {
					$children[] = $content;
				}
				return;
			}
			$isLet = false;
			$lets = array();
			foreach ($codes as &$code) {
				$data = TemplateCodeParser::parse($code, 'textNode');
				if ($data['isLet']) {
					if (!$isLet) {
						$let++;
					}
					$isLet = true;
					$code = '';
					$lets[] = $data['code'];
				} else {
					$code = '<nq>'.$data['code'].'<nq>';
					$isLet = false;
				}			
				if (!empty($data['react'])) {
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
			//Printer::log($items);
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

	private static function getObfuscatedClassName(&$value, $isCode = false) {
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
		$value = preg_replace('/ {2,}/', ' ', $obfuscatedValue);
	}

	private static function addToCssClassIndex($className) {
		$obfuscatedClassName = CSSObfuscator::generate();
		self::$cssClassIndex[$className] = $obfuscatedClassName;
		return $obfuscatedClassName;
	}

	private static function addTextNode($text) {
		$index = array_search($text, self::$textNodes);
		if ($index !== false) {
			return $index;
		}
		self::$textNodes[] = $text;
		return count(self::$textNodes) - 1;
	}
}