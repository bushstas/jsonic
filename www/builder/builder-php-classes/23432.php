<?php

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
					self::parseTemplate($item['content'], $child);
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

	private static function hasClassVar($code) {
		return preg_match('/\$\w/', $code);
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