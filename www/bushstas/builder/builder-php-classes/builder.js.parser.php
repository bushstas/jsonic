<?php

class JSParser
{
	private static $correctors;
	private static $usedCorrectors = array();
	private static $usedCorrectorsByClass = array();

	private static $errors = array(
		'validationError' => 'Ошибка в валидации кода класса {??}',
		'incorrectCorrName' => 'Некоррекнтое имя корректора {??} в методе {??} класса {??}',
		'unknownCorr' => 'Неизвестный корректор {??} в методе {??} класса {??}',
		'symbolsBetweenFuncs' => 'Обнаружен код после функции {??} класса {??}. Вне методов класса не должно быть никакого кода',
		'symbolsAfterFuncs' => 'Обнаружен код в конце класса {??}. Вне методов класса не должно быть никакого кода',
		'symbolsBeforeFuncs' => 'Обнаружен код в начале класса {??}. Вне методов класса не должно быть никакого кода',
		'innerFunc' => "Обнаружена вложенная функция {??} в методе {??} класса {??}.<br>Для создания замыканий используйте код вида:<xmp>function func() {\n\tvar innerFunc = function() {\n\n\t};\n}</xmp>",
		'doubleDollarSign' => 'Ошибка парсинга метода {??} класса {??}. Обнаружены два или более символа <b>$</b> подряд'
	);

	public static function init($correctors) {
		self::$correctors = $correctors;
	}

	public static function parse(&$class) {
		$code = $class['content'];
		$code = 'function __constructor(){}'.trim($code);		

		$data = Splitter::split('/\bfunction +(\w+) *\(([^\)]*)\) *\{/', $code, 'all');		
		$functions = array();
		$functionList = array();
		$class['calledMethods'] = array();
		foreach ($data['items'] as $i => $part) {
			$content .= $part;
			if (isset($data['delimiters'][1][$i])) {
				$functionName = $data['delimiters'][1][$i];
				$arguments = $data['delimiters'][2][$i];

				$d = Splitter::getInner($data['items'][$i + 1]);
				if (!$d['closed']) {
					if (isset($data['items'][$i + 2])) {
						new Error(self::$errors['innerFunc'], array($data['delimiters'][1][$i + 1], $functionName, $class['name']));
					} else {
						die('not closed');
					}
				}
				$d['outer'] = preg_replace('/[\s]/', '', $d['outer']);
				if ($d['outer'] == ';') $d['outer'] = '';
				if ($d['outer'] != '') {
					if ($functionName == '__constructor') {
						new Error(self::$errors['symbolsBeforeFuncs'], array($class['name']));
					} elseif (isset($data['items'][$i + 2])) {
						new Error(self::$errors['symbolsBetweenFuncs'], array($functionName, $class['name']));
					} else {
						new Error(self::$errors['symbolsAfterFuncs'], array($class['name']));
					}
				}
				$code = $d['inner'];
				//$code = self::parseFunctionCode($d['inner'], $functionName, $class['name']);
				self::parseArgsForCorrectors($arguments, $code, $class['name'], $functionName);
				//JSInterpreter::parse($code, $class['name']);
				//TextsConstantsParser::parse($code, $functionName, $class['name']);
				$functions[] = array(
					'name' => $functionName, 
					'args' => $arguments,
					'code' => $code
				);
				// preg_match_all('/\bthis\.(\w+)\(/', $code, $matches);
				// $matches = $matches[1];
				// if (!empty($matches)) {
				// 	foreach ($matches as $match) {
				// 		if (!isset($class['calledMethods'][$match])) {
				// 			$class['calledMethods'][$match] = $functionName;
				// 		}
				// 	}
				// }
				if ($functionName != '__constructor') {
					$functionList[] = $functionName;
				}
			}
		}
		$class['functions'] = $functions;
		$class['functionList'] = $functionList;		
		unset($class['content']);
	}

	public static function getUsedCorrectors() {
		return self::$usedCorrectors;
	}

	public static function getUsedCorrectorsByClass() {
		return self::$usedCorrectorsByClass;
	}

	private static function parseFunctionCode($code, $functionName, $className) {
		if (preg_match('/\${2,}/', $code)) {
			new Error(self::$errors['doubleDollarSign'], array($functionName, $className));
		}
		$regexp = '/\bget\s+([\w ,]+);*\s*/';
		preg_match_all($regexp, $code, $matches);
		if (!empty($matches[1])) {
			$parts = preg_split($regexp, $code);
			$code = '';
			foreach ($parts as $i => $part) {
				$code .= $part;
				if (isset($matches[1][$i])) {
					$vars = explode(',', $matches[1][$i]);
					foreach ($vars as $var) {
						$var = trim($var);
						$code .= 'var '.$var."=this.get('".$var."');\n";
					}
				}
			}
		}
		$regexp = '/(\w+)::([A-Z]\w+)\s*=\s*/';
		preg_match_all($regexp, $code, $matches);
		if (!empty($matches[0])) {
			$parts = preg_split($regexp, $code);
			$code = '';
			for ($i = 0; $i < count($parts); $i++) {
				$code .= $parts[$i];
				if (isset($matches[1][$i])) {
					$code .= $matches[1][$i].'=';
				}
				if (isset($parts[$i + 1])) {
					preg_match_all('/[\n;,]/', $parts[$i + 1], $ms);
					$ps = preg_split('/[\n;,]/', $parts[$i + 1]);
					$ps[0] = "Validator.assert(".$ps[0].",is".$matches[2][$i].",'".$matches[1][$i]." is not ".$matches[2][$i]." in ".$className.".".$functionName."')";
					$parts[$i + 1] = '';
					foreach ($ps as $j => $p) {
						$parts[$i + 1] .= $p;
						if (isset($ms[0][$j])) {
							$parts[$i + 1] .= $ms[0][$j];
						}
					}
				}
			}
		}
		if (preg_match('/\$[_a-z]/i', $code)) {
			$regexp = '/\*\$(\w+)\s*=(?!=)/';
			preg_match_all($regexp, $code, $matches);
			$varsToSet = $matches[1];
			if (!empty($varsToSet)) {
				$code = preg_replace($regexp, "$1=", $code);	
			}
			$code = preg_replace('/\$(\w+)\!\s*;*/', "this.toggle('$1');", $code);
			$code = preg_replace('/\$(\w+)\s*([\+\-\*\/\%])=\s*(\w+)/', "this.change('$1',$3,'$2')", $code);
			$code = preg_replace('/\$(\w+)\s*\+\+/', "this.change('$1',1)", $code);
			$code = preg_replace('/\$(\w+)\s*--/', "this.change('$1',-1)", $code);
			$code = preg_replace('/\$(\w+)\.removeAt\(/', "this.removeByIndexFrom('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.remove\(/', "this.removeValueFrom('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.each\(/', "this.each('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.add\(/', "this.addTo('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.addOne\(/', "this.addOneTo('$1', ", $code);
			$code = preg_replace('/\$(\w+)\s*=>/', "this.set('$1', $1);", $code);

			if (!empty($varsToSet)) {
				$items = array();
				foreach ($varsToSet as $varToSet) {
					$items[] = "\n\$".$varToSet."=".$varToSet;
				}
				$code .= implode(',', $items).';';
			}

			$regexp = '/\$(\w+)\s*=(?!=)\s*/';
			$data = Splitter::split($regexp, $code, 1);
			if (!empty($data['items'])) {
				$code = $data['items'][0];
				$signs = array('{' => '}', '(' => ')', '[' => ']');
				$isComma = false;
				$parensOpen = 0;
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
							$d = Splitter::split('/[\r\n;,]/', $data['items'][$i], 0);
							$value = '';
							foreach ($d['items'] as $j => $val) {
								preg_match_all('/\(/', $val, $ms);
								$parensOpen = count($ms[0]);
								preg_match_all('/\)/', $val, $ms);
								$parensOpen -= count($ms[0]);
								$value .= $val;								
								if ($parensOpen > 0) {
									$value .= $d['delimiters'][$j];
								} else break;
							}
							$itm = $d['delimiters'][$j];
							for ($jj = $j + 1; $jj < count($d['items']); $jj++) {
								$itm .= $d['items'][$jj];
								$itm .= $d['delimiters'][$jj];
							}
							$data['items'][$i] = $itm;							
						}
						$trimmed = trim($data['items'][$i]);
						if (!$isComma) {
							if ($trimmed == ',') {
								$code .= "this.set({'".$data['delimiters'][$i - 1]."':".$value.",";
								$isComma = true;
							} else {
								$code .= "this.set('".$data['delimiters'][$i - 1]."',".$value.")".$data['items'][$i];
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
			$code = preg_replace('/\$(\w+)/', "this.get('$1')", $code);
			$regexp = '([,:=\+\-\*>\!\?<;\(\)\|\}\{\[\]%\/])';
			$code = preg_replace('/'.$regexp.' {1,}/', "$1", $code);
			$code = preg_replace('/ {1,}'.$regexp.'/', "$1", $code);
		}
		StateParser::parse($code, $functionName, $className);
		return $code;
	}

	private static function parseArgsForCorrectors(&$args, &$code, $class, $name) {
		$parts = explode(',', $args);
		$args = array();
		$corrs = array();
		foreach ($parts as $part) {
			$dv = '';
			$part = trim($part);
			$p = explode(':', $part);
			$arg = $p[0];
			if (isset($p[1])) {
				$ps = explode('=', $p[1]);
				if (isset($ps[1])) {
					$p[1] = $ps[0];
					$dv = $ps[1];
				}
				foreach ($p as $i => $v) {
					if ($i > 0) {
						if (!is_array($corrs[$arg])) {
							$corrs[$arg] = array();
						}
						$corrs[$arg][] = $v;
					}
				}
			}
			if (!empty($dv)) {
				$arg .= '='.$dv;
			}
			$args[] = $arg;
		}
		$args = implode(',', $args);
		foreach ($corrs as $k => $v) {
			foreach ($v as $crr) {
				if (!preg_match('/^[a-z]\w*/i', $crr)) {
					new Error(self::$errors['incorrectCorrName'], array($crr, $name, $class));
				}
				$crrName = $crr.'Crr';
				if (!in_array($crrName, self::$correctors)) {
					new Error(self::$errors['unknownCorr'], array($crr, $name, $class));
				}
				$code = $k."=".CONST_GLOBAL.".get('".$crr."Crr').correct(".$k.");\n".$code;
				if (!in_array($crrName, self::$usedCorrectors)) {
					self::$usedCorrectors[] = $crrName;
					if (!isset(self::$usedCorrectorsByClass[$class])) {
						self::$usedCorrectorsByClass[$class] = array();
					}
					self::$usedCorrectorsByClass[$class][] = $crrName;
				}
			}
		}
	}

}