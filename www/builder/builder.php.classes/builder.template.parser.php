<?php

class TemplateParser 
{	
	private static $calledClasses, $classes, $templates;
	private static $regexp = "/\{template +\.(\w+) *\}/";
	private static $simpleTags = array('br', 'input', 'img', 'hr');
	private static $class, $className, $tmpids, $isSwitchContext;

	private static $errors = array(
		'noMainTemplate' => 'Шаблон <b>main</b> класса {??} не найден среди прочих',
		'noClosingTag' => 'Ошибка валидации шаблонов класса {??}. Один из {?} {??} не имеет закрывающего тега',
		'extraClosingTag' => 'Ошибка валидации шаблонов класса {??}. Лишний закрывающийся {?} {??}'
	);

	public static function init($params) {
		self::$calledClasses = $params['classNames'];
		self::$classes = $params['classes'];
		self::$templates = $params['templates'];
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
			if (!empty($className) && !preg_match("/\{template +\.main *\}/", $template) && !self::hasParentMainTemplate($class) && in_array($class['name'], $calledComponents)) {
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
		foreach (self::$classes as $name => $class) {
			$template = self::$templates[$name];
			if (!empty($template) && in_array($name, $class['extends'])) {
				if (preg_match("/\{template +\.main *\}/", $template) || self::hasParentMainTemplate($class)) {
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
				if (isSimpleTag($tagName))
				{
					$child = array('t' => getTagIndex($tagName));
					getTagProperties($item, $child, $component);
				}
				elseif ($tagName == 'template')
				{
					preg_match("/<template +[\"']*(\w+)[\"']*[^=]/i",  $content, $match);
					$tmpName = $match[1];
					if (!empty($tmpName) && $tmpName == $templateName) {
						error('Шаблон <b>'.$templateName.'</b> класса <b>'.$component['name'].'</b> вызывает сам себя');
					}
					$child = array('tmp' => '<nq><this>getTemplate'.ucfirst($tmpName).'<nq>');
					getTemplateProperties($item['content'], $child, $component);
					if (is_array($child['p']) && !empty($child['p']['tmpid'])) {
						if (!empty(self::$tmpids[$child['p']['tmpid']]) && self::$tmpids[$child['p']['tmpid']] == $templateName) {
							error('Шаблон <b>'.$templateName.'</b> класса <b>'.$component['name'].'</b> вызывает сам себя');
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
					getTemplateProperties($item['content'], $child, $component);
				}
				elseif ($tagName == 'component' || $tagName == 'control' || $tagName == 'menu' || $tagName == 'form')
				{
					getTagProperties($item, $child, $component, true);
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
						getSwitch($item, $child, $component);
					} elseif ($tagName != 'foreach' && $tagName != 'if') {
						if ($tagName == 'forma') $tagName = 'form';
						$child['t'] = getTagIndex($tagName);
						getTagProperties($item, $child, $component);
					} else {
						if ($tagName == 'if') {
							if ($ifContentIsEmpty) {
								getIfSwitch($item, $child, $component);
							} else {
								checkIfConditionForContainigProps($match[1], $child, $component);
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
							getForeach($item, $child, $component);
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
				error('Шаблон класса <b>'.$class['name'].'</b> содержит некорректный код <b>'.$text.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b> или <b>$var.name</b> или <b>$var.0</b>. Использование записи вида <b>$var["name"]</b> недопустимо');
			} else {
				error('Один из шаблонов файле <b>'.$class.'</b> содержит некорректный код <b>'.$text.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b> или <b>$var.name</b> или <b>$var.0</b>. Использование записи вида <b>$var["name"]</b> недопустимо');
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
						error('Ошибка в коде оператора <b>let</b> в шаблоне класса <b>'.$component['name'].'</b><xmp>{'.$codes[$i].'}</xmp><b>Ожидается код вида</b><xmp>{let &var = 5}</xmp><b>или</b><xmp>{let &isEmpty: true}</xmp>');
					}
				}
				$content[] = array(parseCode($codes[$i], $component, 'prop', true));
			}
		}
		return $content;
	}
}