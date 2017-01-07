<?php


class ClassAnalyzer
{
	private static $regexp = '/<(component|control|form|menu)\s([^>]+)>/i';
	private static $matches = array();
	private static $usedClasses = array();
	private static $allJsClasses;
	private static $jsClassesData;

	public static function run($files, $jsCompiler, $routesCompiler, $configProvider) {
		self::$jsClassesData = $jsCompiler->getClasses();
		self::$allJsClasses = array_keys(self::$jsClassesData);
		self::findClassesInTemplates($files['template']);
		self::findClassesInScripts($files['js']);
		$views = $routesCompiler->getRouteViews();
		$disabledViews = $routesCompiler->getDisabledRoutes();

		$defaultClasses = array();
		$tooltipClass = $configProvider->getTooltipClass();
		if (!empty($tooltipClass)) {
			$defaultClasses[] = $tooltipClass;
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
		
		Printer::log(self::$usedClasses);
		Printer::log($notUsedClasses);
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

	private static function findClassesInTemplates($files) {
		foreach ($files as $file) {
			preg_match_all(self::$regexp, $file['content'], $matches);
			$tagContents = $matches[2];
			self::$matches[$file['name']] = array();
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