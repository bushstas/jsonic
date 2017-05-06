<?php

class StateParser
{
	private static $functionName, $className;

	private static $methods = array(
		'set', 'get', 'dispatchEvent', 'subscribe', 'listen', 'unlisten', 'unsubscribe'
	);

	private static $usefulMethods = array(
		'set', 'get', 'dispatchEvent', 'unsubscribe'
	);

	private static $errors = array(
		'unknownMethod' => 'Обнаружен неизвестный метод {??} менеджера {??} в методе {??} класса {??}',
		'notUsefulMethod' => 'Обнаружено использование метода {??} менеджера {??} в методе {??} класса {??}<br>Вместо этого используйте initial {?}'
	);

	public static function parse(&$code, $functionName, $className) {
		self::$functionName = $functionName;
		self::$className = $className;
		self::validate($code);
		if (preg_match('/\$:[_a-z]/i', $code)) {
			self::parseSetShortcuts($code);
		}
		self::parseGetShortcuts($code);
	}

	private static function validate($code) {
		preg_match_all('/\bState\.(\w+)/', $code, $matches);
		if (!empty($matches[1])) {
			foreach ($matches[1] as $i => $method) {
				if (!in_array($method, self::$methods)) {
					new Error(self::$errors['unknownMethod'], array($method, 'State', self::$functionName, self::$className));
				}
				if (!in_array($method, self::$usefulMethods)) {
					$instead = $method == 'listen' ? 'параметр <b>listeners</b>' : 'параметр <b>globals</b>';
					new Error(self::$errors['notUsefulMethod'], array($method, 'State', self::$functionName, self::$className, $instead));
				}
			}
		}
	}

	private static function parseGetShortcuts(&$code) {
		$code = preg_replace('/\$:(\w+)/', "State.get('$1')", $code);
	}

	private static function parseSetShortcuts(&$code) {
		$regexp = '/\$:(\w+)\s*=(?!=)\s*/';
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
							$code .= "State.set({'".$data['delimiters'][$i - 1]."':".$value.",";
							$isComma = true;
						} else {
							$code .= "State.set('".$data['delimiters'][$i - 1]."',".$value.")".$data['items'][$i];
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