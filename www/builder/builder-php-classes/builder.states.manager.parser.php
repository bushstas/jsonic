<?php

class StatesManagerParser
{
	private static $functionName, $className;

	private static $methods = array(
		'set', 'get', 'dispatchEvent', 'subscribe', 'listen'
	);

	private static $usefulMethods = array(
		'set', 'get', 'dispatchEvent'
	);

	private static $errors = array(
		'unknownMethod' => 'Обнаружен неизвестный метод {??} менеджера {??} в методе {??} класса {??}',
		'notUsefulMethod' => 'Обнаружено использование метода {??} менеджера {??} в методе {??} класса {??}<br>Вместо этого используйте initial {?}',
		'usingBeforeRender' => 'Обнаружено использование метода {??} менеджера {??} в методе <b>initiate</b> класса {??}<br>Использование компонентом менеджера <b>LocalState</b> допускается только после его рендера, например в методе <b>onRendered</b>',
		'stateManagerUsing' => "Обнаружено использование класса <b>StateManager</b> в методе {??} класса {??}<br>Вместо этого используйте записи вида:<xmp>LocalState.set('name', 'Name');\n$:name = 'Name';</xmp>или<xmp>var ct = GlobalState.get('currentThing');\nvar ct = $::currentThing;</xmp>"
	);

	public static function parse(&$code, $functionName, $className) {
		self::$functionName = $functionName;
		self::$className = $className;
		self::validate($code);
		if (preg_match('/\$:[_a-z]/i', $code)) {
			self::parseSetShortcuts($code, ':');
		}
		if (preg_match('/\$::[_a-z]/i', $code)) {
			self::parseSetShortcuts($code, '::');
		}
		self::parseLocalStates($code);
		self::parseGlobalStates($code);
		self::parseGetShortcuts($code);
	}

	private static function validate($code) {
		preg_match_all('/\b(Loc|Glob)alState\.(\w+)/', $code, $matches);
		if (!empty($matches[2])) {
			foreach ($matches[2] as $i => $method) {
				if (!in_array($method, self::$methods)) {
					new Error(self::$errors['unknownMethod'], array($method, $matches[1][$i].'alState', self::$functionName, self::$className));
				}
				if (!in_array($method, self::$usefulMethods)) {
					$instead = $method == 'listen' ? 'параметр <b>listeners</b>' : 'параметры <b>globals</b> и <b>locals</b>';
					new Error(self::$errors['notUsefulMethod'], array($method, $matches[1][$i].'alState', self::$functionName, self::$className, $instead));
				}
				if ($matches[1][$i] == 'Loc' && self::$functionName == 'initiate') {
					new Error(self::$errors['usingBeforeRender'], array($method, $matches[1][$i].'alState', self::$className));
				}
			}
		}
		if (preg_match('/\bStateManager\b/', $code)) {
			new Error(self::$errors['stateManagerUsing'], array(self::$functionName, self::$className));
		}
	}

	private static function parseLocalStates(&$code) {
		$code = preg_replace('/\bLocalState\.(get|set)\(/', "StateManager.$1(this,0,", $code);
	}

	private static function parseGlobalStates(&$code) {
		$code = preg_replace('/\bGlobalState\.(get|set)\(/', "StateManager.$1(this,1,", $code);
	}

	private static function parseGetShortcuts(&$code) {
		$code = preg_replace('/\$:(\w+)/', "StateManager.get(this,0,'$1')", $code);
		$code = preg_replace('/\$::(\w+)/', "StateManager.get(this,1,'$1')", $code);
	}

	private static function parseSetShortcuts(&$code, $sign) {
		$isGlobal = $sign == '::' ? '1' : '0';
		$regexp = '/\$'.$sign.'(\w+)\s*=(?!=)\s*/';
		$data = Splitter::split($regexp, $code, 1);
		if (!empty($data['items'])) {
			$code = $data['items'][0];
			$signs = array('{' => '}', '(' => ')', '[' => ']');
			$isComma = false;
			for ($i = 1; $i < count($data['items']); $i++) {
				if (isset($data['items'][$i])) {
					$firstSign = $data['items'][$i][0];
					$closingSign = $signs[$firstSign];
					$value = '';
					if ($firstSign == '{' || $firstSign == '(' || $firstSign == '[') {
						$d = Splitter::getInner(ltrim($data['items'][$i], $firstSign), $closingSign, $firstSign);
						$value = $firstSign.preg_replace('/[\r\n\t]/', '', $d['inner']).$closingSign;
						$data['items'][$i] = $d['outer'];
					} else {
						$d = Splitter::split('/[\r\n;]/', $data['items'][$i], 0);
						$value = $d['items'][0];
						$trimmed = trim($value);
						if ($trimmed[strlen($trimmed) - 1] == ',') {
							$parts = explode(',', $value);
							$last = count($parts) - 1;
							$d['items'][0] = ','.$parts[$last];
							$parts[$last] = '';
							$value = rtrim(implode(',', $parts), ',');
						} else {
							$d['items'][0] = '';
						}
						$data['items'][$i] = Splitter::join($d['items'], $d['delimiters']);
						
					}
					$trimmed = trim($data['items'][$i]);
					if (!$isComma) {
						if ($trimmed == ',') {
							$code .= "StateManager.set(this,".$isGlobal.",{'".$data['delimiters'][$i - 1]."':".$value.",";
							$isComma = true;
						} else {
							$code .= "StateManager.set(this,".$isGlobal.",'".$data['delimiters'][$i - 1]."',".$value.")".$data['items'][$i];
						}
					} else {
						if ($trimmed == ',') {
							$code .= "'".$data['delimiters'][$i - 1]."':".$value.",";
							$isComma = true;
						} else {
							$code .= "'".$data['delimiters'][$i - 1]."':".$value."})".$data['items'][$i];
							$isComma = false;
						}
					}

				}
				
			}
		}
	}
}