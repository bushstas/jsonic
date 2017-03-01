<?php


class DialogsParser
{
	private static $classes;
	private static $sources;
	private static $dialogs;
	private static $dlgs;
	private static $globals;
	private static $class;
	private static $initialsParser;
	private static $regexp;
	private static $methods = array('show', 'hide', 'get', 'dispose', 'expand', 'minimize');

	private static $errors = array(
		'unknownDialogMethod' => "Обнаружен вызов несуществующего метода {??} у диалогового окна {??} в методе {??} класса {??}<br>Доступные медоты:<xmp>show\nhide\nget\nexpand\nminimize\ndispose</xmp>"
	);

	public static function init($classes, $sources, $dialogs, $initialsParser) {
		self::$classes = $classes;
		self::$sources = $sources;
		self::$dialogs = $dialogs;
		self::$dlgs = array_keys(self::$dialogs);
		self::$globals = JSGlobals::getUsedNames();
		self::$initialsParser = $initialsParser;
		self::$regexp = '/\b('.implode('|', self::$dlgs).')\b/';
	}

	public static function parse(&$class) {
		self::$class = &$class;				
		foreach ($class['functions'] as &$func) {
			self::parseCode($func);
		}
	}

	public static function parseCode(&$func) {
		$code = &$func['code'];
		if (preg_match(self::$regexp, $code)) {
			foreach (self::$dlgs as $i => $dlg) {				
				$regexp  = '/\b'.$dlg.'\.('.implode('|', self::$methods).')\(/';
				$code = preg_replace($regexp, self::$globals['DIALOGER'].".$1('$dlg',", $code);
			}
			$regexp = rtrim(self::$regexp, '/').'\.(\w+)/';
			preg_match_all($regexp, $code, $matches);
			if (!empty($matches[2])) {
				foreach ($matches[2] as $i => $match) {
					new Error(self::$errors['unknownDialogMethod'], array($match, $matches[1][$i], $func['name'], self::$class['name']));
				}
			}
		}
	}
}