<?php


class ControllersParser
{
	private static $classes;
	private static $controllers;
	private static $globals;
	private static $class;
	private static $initialsParser;

	public static function init($classes, $controllers, $initialsParser) {
		self::$classes = $classes;
		self::$controllers = $controllers;
		self::$globals = JSGlobals::getUsedNames();
		self::$initialsParser = $initialsParser;
	}

	public static function parse(&$class) {
		self::$class = &$class;
		$ctrs = array_keys(self::$controllers);	

		
		foreach ($class['functions'] as &$func) {
			$code = &$func['code'];
			foreach ($ctrs as $i => $ctr) {
				$actions = self::$initialsParser->getControllerActions($ctr);
				if (is_array($actions) && !empty($actions)) {
					$regexp  = '/\b'.$ctr.'\.('.implode('|', $actions).')\(/';
					$code = preg_replace($regexp, $ctr.".doAction(this,'$1',", $code);
				}
				$code = preg_replace('/\b'.$ctr.'\b/', self::$globals['CONTROLLER'].'.get('.$i.')', $code);
			}
			if ($class['type'] == 'controller') {
				$actions = self::$initialsParser->getControllerActions($class['name']);
				$regexp  = '/\bthis\.('.implode('|', $actions).')\(/';
				$code = preg_replace($regexp, "this.doAction(null,'$1',", $code);				
			}
		}
	}
}