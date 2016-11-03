<?php


class ControllersParser
{
	private static $classes;
	private static $controllers;
	private static $ctrs;
	private static $globals;
	private static $class;
	private static $initialsParser;
	private static $regexp;

	private static $errors = array(
		'incorrectUse' => "���������� ������������ ������������� ����������� {??} � ������ {??} ������ {??}<br>������� ������������ ��� �������� � �������� ��������� ����������, ������� �� ����� ���� �������� ����������<br><br>���������� �������������:<xmp>Controller.load()</xmp>���<xmp>Controller.save({data: ...})</xmp><br>������������ �������������:<xmp>var ctr = Controller;\nctr.load();</xmp>���<xmp>this.doSome(Controller);</xmp>���<xmp>var data = [Controller];</xmp>���<xmp>var data = {ctr: Controller};</xmp>",
		'doActionUse' => '��������� ������ ����� ������ <b>doAction</b> ����������� {??} � ������ {??} ������ {??}<br><br>����������� ��� ����:<xmp>Controller.load();</xmp>���<xmp>Controller.remove({id: 345});</xmp><br>��� <b>load</b> � <b>remove</b> initial �������� <b>actions</b> ������� �����������'
	);

	public static function init($classes, $controllers, $initialsParser) {
		self::$classes = $classes;
		self::$controllers = $controllers;
		self::$ctrs = array_keys(self::$controllers);
		self::$globals = JSGlobals::getUsedNames();
		self::$initialsParser = $initialsParser;
		self::$regexp = '/\b('.implode('|', self::$ctrs).')\b/';
	}

	public static function parse(&$class) {
		self::$class = &$class;
				
		foreach ($class['functions'] as &$func) {
			preg_match(rtrim(self::$regexp, '/').'\.doAction\b/', $func['code'], $match);
			if (!empty($match)) {
				new Error(self::$errors['doActionUse'], array($match[1], $func['name'], self::$class['name']));
			}
			self::parseCode($func);
		}
				

		if ($class['type'] == 'controller') {
			$actions = self::$initialsParser->getControllerActions($class['name']);
			$regexp  = '/\bthis\.('.implode('|', $actions).')\(/';
			$code = preg_replace($regexp, "this.doAction(null,'$1',", $code);
			foreach ($actions as $a) {
				unset($class['calledMethods'][$a]);
			}
		}
	}

	public static function parseCode($func) {
		$code = &$func['code'];
		if (preg_match(self::$regexp, $code)) {
			foreach (self::$ctrs as $i => $ctr) {
				$actions = self::$initialsParser->getControllerActions($ctr);
				if (is_array($actions) && !empty($actions)) {
					$regexp  = '/\b'.$ctr.'\.('.implode('|', $actions).')\(/';
					$code = preg_replace($regexp, self::$globals['CONTROLLER'].'.get('.$i.')'.".doAction(this,'$1',", $code);
				}
			}
		}
		preg_match(self::$regexp, $code, $match);
		if (!empty($match)) {
			new Error(self::$errors['incorrectUse'], array($match[1], $func['name'], self::$class['name']));
		}
	}

	public static function parseInitialsCode(&$code) {
		if (preg_match(self::$regexp, $code)) {
			foreach (self::$ctrs as $i => $ctr) {
				$code = preg_replace('/\b'.$ctr.'\b/', self::$globals['CONTROLLER'].'.get('.$i.')', $code);
			}
		}
	}
}