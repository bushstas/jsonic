<?php


class ControllersParser
{
	private static $classes;
	private static $sources;
	private static $controllers;
	private static $ctrs;
	private static $globals;
	private static $class;
	private static $initialsParser;
	private static $regexp;

	private static $errors = array(
		'incorrectUse' => "���������� ������������ ������������� ����������� {??} � ������ {??} ������ {??}<br>������� ������������ ��� �������� � �������� ��������� ����������, ������� �� ����� ���� �������� ����������<br><br>���������� �������������:<xmp>Controller.load()</xmp>���<xmp>Controller.save({data: ...})</xmp><br>������������ �������������:<xmp>var ctr = Controller;\nctr.load();</xmp>���<xmp>this.doSome(Controller);</xmp>���<xmp>var data = [Controller];</xmp>���<xmp>var data = {ctr: Controller};</xmp>",
		'doActionUse' => '��������� ������ ����� ������ <b>doAction</b> ����������� {??} � ������ {??} ������ {??}<br><br>����������� ��� ����:<xmp>Controller.load();</xmp>���<xmp>Controller.remove({id: 345});</xmp><br>��� <b>load</b> � <b>remove</b> initial �������� <b>actions</b> ������� �����������',
		'hasIntersects' => '��������� ����� {??} � ����������� initial �������� action  � ������ {??}<br>������ ����� ����� �������� ������������� � �� ����� ���� �������������.',
		'unknownControllerMethod' => '��������� ����� ��������������� ������ {??} � ����������� {??} � ������ {??} ������ {??}'
	);

	public static function init($classes, $sources, $controllers, $initialsParser) {
		self::$classes = $classes;
		self::$sources = $sources;
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
	}

	public static function parseCode(&$func) {
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
		$regexp = rtrim(self::$regexp, '/').'\.(\w+)/';
		preg_match_all($regexp, $code, $matches);
		if (!empty($matches[2])) {
			foreach ($matches[2] as $i => $match) {
				if (!self::hasComponentMethod($match, self::$controllers[$matches[1][$i]])) {
					new Error(self::$errors['unknownControllerMethod'], array($match, $matches[1][$i], $func['name'], self::$class['name']));
				}
			}
		}
		$regexp = rtrim(self::$regexp, '/').'(?!\.)/';
		preg_match($regexp, $code, $match);
		if (!empty($match)) {
			new Error(self::$errors['incorrectUse'], array($match[1], $func['name'], self::$class['name']));
		}
		if (self::$class['type'] == 'controller') {
			$actions = self::$initialsParser->getControllerActions(self::$class['name']);
			$regexp  = '/\bthis\.('.implode('|', $actions).')\(/';
			$code = preg_replace($regexp, "this.doAction(null,'$1',", $code);
			foreach ($actions as $a) {
				unset(self::$class['calledMethods'][$a]);
			}
			$intersects = array_values(array_intersect(self::$class['functionList'], $actions));
			if (!empty($intersects)) {
				new Error(self::$errors['hasIntersects'], array($intersects[0], self::$class['name']));
			}			
		}
	}

	public static function parseInitialsCode(&$code) {
		if (preg_match(self::$regexp, $code)) {
			foreach (self::$ctrs as $i => $ctr) {
				$code = preg_replace('/\b'.$ctr.'\b/', self::$globals['CONTROLLER'].'.get('.$i.')', $code);
			}
		}
	}

	private static function hasComponentMethod($method, $class) {
		if (is_array($class['functionList']) && in_array($method, $class['functionList'])) return true;
		$parents = $class['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parent) {
				if (is_array(self::$classes[$parent]) && self::hasComponentMethod($method, self::$classes[$parent])) {
					return true;
				}
				if (is_array(self::$sources[$parent]) && preg_match('/\b'.$parent.'\.prototype\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', self::$sources[$parent]['content'])) {
					return true;	
				}
				if (in_array($parent, JSCompiler::$componentLikeClasses) && preg_match('/\bComponent.prototype\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', self::$sources['Component']['content'])) {
					return true;
				}
			}
		}
		return false;
	}
}