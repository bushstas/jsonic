<?php


class ClassAnalyzer
{
	private static $regexp = '/<(component|control|form|menu)\s([^>]+)>/i';
	private static $matches = array();
	private static $usedClasses = array();
	private static $allJsClasses;
	private static $jsClassesData;
	private static $errors = array(
		'unknownClass' => 'Вызываемый в одном из шаблонов класса {??} компонент {??} не найден<xmp>{?}</xmp>'
	);

	public static function run(&$files, $jsCompiler, $routesCompiler, $configProvider) {
		self::$jsClassesData = $jsCompiler->getClasses();
		self::$allJsClasses = array_keys(self::$jsClassesData);
		self::prepareTemplateClasses($files['template']);
		self::findClassesInTemplates($files['template']);
		self::findClassesInScripts($files['js']);
		$views = $routesCompiler->getRouteViews();
		$disabledViews = $routesCompiler->getDisabledRoutes();

		$defaultClasses = array();
		$tooltipClass = $configProvider->getTooltipClass();
		if (!empty($tooltipClass)) {
			$defaultClasses[] = $tooltipClass;
		}
		foreach ($defaultClasses as $defaultClass) {
			self::addUsedClassFor($defaultClass, $defaultClasses);
		}

		$usedClasses = array();
		$views[] = $configProvider->getEntry();
		foreach ($views as $viewClass) {
			$usedClasses[$viewClass] = $defaultClasses;
			if (!in_array($viewClass, $disabledViews)) {
				$usedClasses[$viewClass][] = $viewClass;
				self::addUsedClassFor($viewClass, $usedClasses[$viewClass]);
			}
		}

		$allUsed = array();
		foreach ($usedClasses as $list) {
			$allUsed = array_merge($allUsed, $list);
		}
		self::$usedClasses = array_values(array_unique($allUsed));
		$notUsedClasses = array_diff(self::$allJsClasses, $allUsed);
		
		// Printer::log(self::$usedClasses);
		// Printer::log($notUsedClasses);
	}

	public static function addClasses($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}
		foreach ($classes as $className) {
			self::$usedClasses[] = $className;
		}
	}

	public static function getUsedClasses() {
		return self::$usedClasses;
	}

	private static function addUsedClassFor($className, &$list) {
		if (!empty(self::$matches[$className])) {
			foreach (self::$matches[$className] as $c) {
				if (!in_array($c, $list)) {
					$list[] = $c;
					self::addUsedClassFor($c, $list);
				}
			}
		}
	}

	private function prepareTemplateClasses(&$files) {
		foreach ($files as &$file) {
			$html = $file['content'];
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
			$data = Splitter::split('/<\/*([A-Z]\w*)([^>]*)>/', $html, 'all');
			if (is_array($data) && !empty($data)) {
				$items = $data['items'];
				$dels = $data['delimiters'];
				$html = '';
				foreach ($items as $i => $item) {
					$html .= $item;
					if (isset($dels[1][$i])) {
						$className = $dels[1][$i];
						$tag = $dels[0][$i];
						if (!in_array($className, self::$allJsClasses)) {
							new Error(self::$errors['unknownClass'], array($file['name'], $className, $tag));
				 		}
				 		$classType = self::$jsClassesData[$className]['type'];
				 		
				 		if ($tag[1] != '/') {
				 			$tag = preg_replace('/^<(\/*)(\w+)([^>]*)>$/', "<$1".$classType.' class="'.$className.'"'."$3>", $tag);
				 		} else {
				 			$tag = preg_replace('/^<(\/*)(\w+)([^>]*)>$/', "</".$classType.">", $tag);
				 		}
				 		Printer::log($tag);
				 		$html .= $tag;
					}
				}

				$file['content'] = $html;
			}
		}

	}

	private static function findClassesInTemplates($files) {
		foreach ($files as $file) {
			preg_match_all(self::$regexp, $file['content'], $matches);
			$tagContents = $matches[2];
			self::$matches[$file['name']] = array();
			if (!in_array($file['name'], self::$allJsClasses)) {
				self::$allJsClasses[] = $file['name'];
			}
			$classes = array();
			foreach ($tagContents as $i => $tagContent) {
				preg_match_all("/class=[\"'](\w+)[\"']/i", $tagContent, $matches);
				$name = $matches[1][0];
				if (empty($name)) continue;
				$classes[] = $name;
			}
			self::$matches[$file['name']] = array_unique($classes);
		}
	}

	private static function findClassesInScripts($files) {		
		foreach ($files as $file) {
			$classType = self::$jsClassesData[$file['name']]['type'];
			$content = $file['content'];
			$classes = self::$allJsClasses;
			$index = array_search($file['name'], $classes);
			if ($index !== false) {
				array_splice($classes, $index, 1);
			}
			$regexp = '/\b('.implode('|', $classes).')\b/';
			$key = 'analyzer_'.$file['name'];
			TextParser::encode($content, $key);
			preg_match_all($regexp, $content, $matches);
			if (!is_array(self::$matches[$file['name']])) {
				self::$matches[$file['name']] = $matches[0];
			} else {
				self::$matches[$file['name']] = array_merge(self::$matches[$file['name']], $matches[0]);
			}
			if ($classType == 'dialog') {
				self::$matches[$file['name']][] = 'Dialog';
			} elseif ($classType == 'form') {
				self::$matches[$file['name']][] = 'Form';
			}
			self::$matches[$file['name']] = array_unique(self::$matches[$file['name']]);
			self::$matches[$file['name']] = array_values(self::$matches[$file['name']]);
		}		
	}
}