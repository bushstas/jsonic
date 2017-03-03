<?php


class ClassAnalyzer
{
	private static $matches = array();
	private static $usedClasses = array();
	private static $usedClassesByViews = array();
	private static $usedControllers = array();
	private static $usedCorrectors = array();
	private static $usedComponents = array();
	private static $notUsedClasses = array();
	private static $unknownClasses = array();
	private static $allJsClasses;
	private static $jsClassesData;
	private static $entryClassName;

	private static $errors = array(
		'unknownClass' => 'Вызываемый в одном из шаблонов класса {??} компонент {??} не найден<xmp>{?}</xmp>',
		'dialogCalling' => 'Недопустимая попытка вызвать компонент с типом <b>dialog</b> из шаблона в классе {??}<br><br>Для диалога синглтона используйте код вида<xmp>Dialoger.show(CommentsDialog, options)</xmp>в противном случае используйте третий аргумент в качестве id параметра<xmp>Dialoger.show(ItemDialog, options, itemId)</xmp>',
	);

	public static function run(&$files, $jsCompiler, $routesCompiler, $configProvider) {
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
		foreach ($defaultClasses as $defaultClass) {
			self::addUsedClassFor($defaultClass, $defaultClasses);
		}

		$usedClasses = array();
		self::$entryClassName = $configProvider->getEntry();
		$views[] = self::$entryClassName;
		foreach ($views as $viewClass) {
			$usedClasses[$viewClass] = $defaultClasses;
			if (!in_array($viewClass, $disabledViews)) {
				$usedClasses[$viewClass][] = $viewClass;
				self::addUsedClassFor($viewClass, $usedClasses[$viewClass]);
			}
		}
		self::$usedClassesByViews = &$usedClasses;
		$usedClassesInBase = self::$usedClassesByViews[self::$entryClassName];
		foreach ($usedClasses as $k => &$list) {
			if ($k != self::$entryClassName) {
				$list = array_diff($list, $usedClassesInBase);
			}
		}

		$allUsed = array();
		foreach ($usedClasses as $k => &$list) {
			if ($configProvider->isSplitMode() && $k != self::$entryClassName) {
				$index = array_search(self::$entryClassName, $list);
				if ($index !== false) {
					array_splice($list, $index, 1);
				}
			}
			$allUsed = array_merge($allUsed, $list);
		}
		$used = array_values(array_unique($allUsed));
		foreach ($used as $className) {
			$class = self::$jsClassesData[$className];
			if ($class['type'] == 'controller') {
				self::$usedControllers[] = $className;
			} elseif ($class['type'] == 'corrector') {
				self::$usedCorrectors[] = $className;
			} else {
				self::$usedComponents[] = $className;
			}
			self::$usedClasses[] = $className;
		}
		self::$notUsedClasses = array_diff(self::$allJsClasses, self::$usedClasses);
		foreach (self::$unknownClasses as $className => $fileName) {
			if (in_array($className, self::$usedClasses)) {
				new Error(self::$errors['unknownClass'], array($fileName, $className));	
			}
		}
		//Printer::log(self::$usedClasses, true);
		//Printer::log(self::$usedCorrectors);
	}

	public static function attachCorrectors($isSplitted) {
		$correctors = JSParser::getUsedCorrectorsByClass();
		if (!$isSplitted) {
			$crr = array();
			foreach (self::$usedClasses as $value) {
				if (is_array($correctors[$value])) {
					$crr = array_merge($crr, $correctors[$value]);
				}
			}
			if (!empty($crr)) {
				$crr = array_unique($crr);
				self::$usedClasses = array_merge(self::$usedClasses, $crr);
			}
		} else {
			foreach (self::$usedClassesByViews as $key => &$classes) {
				$crr = array();
				foreach ($classes as $value) {
					if (is_array($correctors[$value])) {
						$crr = array_merge($crr, $correctors[$value]);
					}
				}
				if (!empty($crr)) {
					$crr = array_unique($crr);
					$classes = array_merge($classes, $crr);
				}
			}
		}
	}

	public static function getNotUsedClasses() {
		return self::$notUsedClasses;
	}

	public static function getUsedClasses($routeName = null) {
		if ($routeName === true) {
			return self::$usedClassesByViews[self::$entryClassName];
		}
		if (empty($routeName)) {
			return self::$usedClasses;
		}
		return self::$usedClassesByViews[$routeName];
	}

	public static function getUsedControllers() {
		return self::$usedControllers;
	}

	public static function getUsedComponents($routeName = null) {
		if (empty($routeName)) {
			return self::$usedComponents;
		}
		if ($routeName === true) {
			$used = self::$usedClassesByViews[self::$entryClassName];
		} else {
			$used = self::$usedClassesByViews[$routeName];
		}
		$components = array();
		foreach ($used as $className) {
			$class = self::$jsClassesData[$className];
			if (!in_array($class['type'], array('controller', 'corrector'))) {
				$components[] = $className;
			}
		}
		return $components;
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
			$html = $file['content'];
			$regexp = "/\{[^\}]+\}/";
			preg_match_all($regexp, $html, $matches);
			$matches = $matches[0];
			foreach ($matches as &$match) {
				$match = str_replace('>', '', $match);
			}
			$parts = preg_split($regexp, $html);
			$html = '';
			foreach ($parts as $i => $part) {
				$html .= $part;
				if (isset($matches[$i])) {
					$html .= $matches[$i];
				}
			}
			$regexp = '/<([A-Z]\w*)[^>]*>/';
			preg_match_all($regexp, $html, $matches);
			$matches = $matches[1];
			self::$matches[$file['name']] = array();
			if (!in_array($file['name'], self::$allJsClasses)) {
				self::$allJsClasses[] = $file['name'];
			}
			$classes = array();
			foreach ($matches as $className) {
				if (self::$jsClassesData[$className]['type'] == 'dialog') {
					new Error(self::$errors['dialogCalling'], $file['name']);
				}
				if ($className != 'Component' && $className != 'Control') {
					$classes[] = $className;
				}
			}
			self::$matches[$file['name']] = array_unique($classes);
		}
		foreach (self::$matches as $class => $classNames) {
			foreach ($classNames as $className) {
				if (!in_array($className, self::$allJsClasses)) {
					self::$unknownClasses[$className] = $class;
				}
			}
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