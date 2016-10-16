<?php

class JSParser
{
	private static $correctors, $globals;
	private static $usedCorrectors = array();

	private static $errors = array(
		'validationError' => 'Ошибка в валидации кода класса {??}',
		'incorrectCorrName' => 'Некоррекнтое имя корректора {??} в методе {??} класса {??}',
		'unknownCorr' => 'Неизвестный корректор {??} в методе {??} класса {??}',
		'symbolsBetweenFuncs' => 'Обнаружен код после функции {??} класса {??}. Вне методов класса не должно быть никакого кода',
		'symbolsAfterFuncs' => 'Обнаружен код в конце класса {??}. Вне методов класса не должно быть никакого кода',
		'symbolsBeforeFuncs' => 'Обнаружен код в начале класса {??}. Вне методов класса не должно быть никакого кода',
		'innerFunc' => "Обнаружена вложенная функция {??} в методе {??} класса {??}.<br>Для создания замыканий используйте код вида:<xmp>function func() {\n\tvar innerFunc = function() {\n\n\t};\n}</xmp>"
	);

	public static function init($correctors, $globals) {
		self::$correctors = $correctors;
		self::$globals = $globals;
	}

	public static function parse(&$class) {
		$code = 'function __constructor(){}'.trim($class['content']);
		$code = preg_replace("/@(\w+)/", self::$globals['CONSTANTS'].".$1", $code);	

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
				$code = self::parseFunctionCode($d['inner'], $functionName, $class['name']);
				self::parseArgsForCorrectors($arguments, $code, $class['name'], $functionName);
				$functions[] = array(
					'name' => $functionName, 
					'args' => $arguments,
					'code' => $code
				);
				preg_match_all('/\bthis\.(\w+)\(/', $code, $matches);
				$matches = $matches[1];
				if (!empty($matches)) {
					foreach ($matches as $match) {
						$class['calledMethods'][] = array('method' => $functionName, 'called' => $match);
					}						
				}
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

	private static function parseFunctionCode($code, $functionName, $className) {
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
		if (preg_match('/\$[a-z]/i', $code)) {
			$regexp = '/\b(var|let|const)\s+\$(\w+)/';
			preg_match_all($regexp, $code, $matches);
			$varsToSet = $matches[2];
			if (!empty($varsToSet)) {
				$code = preg_replace($regexp, "$1 $2", $code);	
			}
			$code = preg_replace('/\$(\w+)\!\s*;*/', "this.toggle('$1');", $code);
			$code = preg_replace('/\$(\w+)\s*([\+\-\*\/\%])=\s*(\w+)/', "this.plusTo('$1',$3,'$2')", $code);
			$code = preg_replace('/\$(\w+)\s*\+\+/', "this.plusTo('$1',1)", $code);
			$code = preg_replace('/\$(\w+)\s*--/', "this.plusTo('$1',-1)", $code);
			$code = preg_replace('/\$(\w+)\.removeAt\(/', "this.removeByIndexFrom('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.remove\(/', "this.removeValueFrom('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.each\(/', "this.each('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.add\(/', "this.addTo('$1', ", $code);
			$code = preg_replace('/\$(\w+)\.addOne\(/', "this.addOneTo('$1', ", $code);
			$code = preg_replace('/\$(\w+)=>/', "this.set('$1', $1);", $code);
			$code = preg_replace('/,(?=\s*\$\w)/', "```", $code);
			$code = preg_replace('/\$(\w+)[\s\t]*=(?!=)[\s\t]*([^\r\n;\`]+)/', "this.set('$1',$2)", $code);
			$code = preg_replace('/\$(\w+)/', "this.get('$1')", $code);
			
			$regexp = '/[;\n]/';
			preg_match_all($regexp, $code, $matches);
			$signs = $matches[0];
			$parts = preg_split($regexp, $code);
			$isSet = false;
			$code = '';
			$prevPart = '';
			foreach ($parts as $i => $part) {
				$p = preg_replace('/\s/', '', $part);
				if (!empty($p)) {
					if (preg_match_all('/^(\s*)this\.set\(\'(\w+)\',(.+?)\)\s*(```)*\s*$/', $part, $matches)) {
						if (!$isSet) {
							$set = array();
							$isSet = true;
						}
						$set[] = array($matches[1][0], $matches[2][0], trim($matches[3][0]));
						$match = trim($matches[0][0]);
						if (preg_match('/```$/', $match)) {
							$prevPart = $part;
							continue;
						}

					}						

					if (!empty($set)) {
						$moreTheOne = count($set) > 1;
						if ($moreTheOne) {
							$code .= $set[0][0].'this.set({';
							$setts = array();
							foreach ($set as $item) {
								$setts[] = "'".$item[1]."':".$item[2];
							}
							$code .= implode(',', $setts)."});\n";
						} elseif (!empty($prevPart)) {
							$code .= $prevPart;
							$code .= $signs[$i - 1];
						}
						$prevPart = '';
						$set = null;
						$isSet = false;
						if ($moreTheOne) continue;
					}
				}
				$code .= $part;
				if (isset($signs[$i])) {
					$code .= $signs[$i];
				}
			}
			
			$parts = explode('<text>', $code);
			$code = '';
			foreach ($parts as $i => $part) {
				$code .= $part;
				if (isset($texts[$i])) {
					$code .= $texts[$i];
				}
			}
			$code = str_replace("```", ",", $code);
			if (!empty($varsToSet)) {
				foreach ($varsToSet as $varToSet) {
					$code .= "\nthis.set('".$varToSet."', ".$varToSet.");";
				}
			}
		}
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
				$code = $k."=".$crr."Crr.correct(".$k.");\n".$code;
				if (!in_array($crrName, self::$usedCorrectors)) {
					self::$usedCorrectors[] = $crrName;
				}
			}
		}
	}

}