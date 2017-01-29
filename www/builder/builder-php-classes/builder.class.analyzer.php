<?php


class ClassAnalyzer
{
	private static $matches = array();
	private static $usedClasses = array();
	private static $usedControllers = array();
	private static $usedCorrectors = array();
	private static $usedComponents = array();
	private static $notUsedClasses = array();
	private static $allJsClasses;
	private static $jsClassesData;

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
		//Printer::log(self::$usedControllers);
		//Printer::log(self::$usedClasses);
	}

	public static function addCorrectors($classes) {
		if (!is_array($classes)) {
			$classes = array($classes);
		}
		foreach ($classes as $className) {
			self::$usedCorrectors[] = $className;
			self::$usedClasses[] = $className;
		}
	}

	public static function getNotUsedClasses() {
		return self::$notUsedClasses;
	}

	public static function getUsedClasses() {
		return self::$usedClasses;
	}

	public static function getUsedCorrectors() {
		return self::$usedCorrectors;
	}

	public static function getUsedControllers() {
		return self::$usedControllers;
	}

	public static function getUsedComponents() {
		return self::$usedComponents;
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
					new Error(self::$errors['unknownClass'], array($file['name'], $className));
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