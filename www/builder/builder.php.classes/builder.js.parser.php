<?php

class JSParser
{
	private static $correctors, $globals;

	private static $errors = array(
		'validationError' => 'Ошибка в валидации кода класса {??}',
		'incorrectCorrName' => 'Некоррекнтое имя корректора {??} в методе {??} класса {??}',
		'unknownCorr' => 'Неизвестный корректор {??} в методе {??} класса {??}'
	);

	public static function init($correctors, $globals) {
		self::$correctors = $correctors;
		self::$globals = $globals;
	}

	public static function parse(&$class) {
		$code = 'function(){}'.trim($class['content']);
		$code = preg_replace("/@(\w+)/", self::$globals['CONSTANTS'].".$1", $code);
		$braces = preg_replace("/[^\{\}]/", "", $code);
		$parts = preg_split("/[\{\}]/", $code);
					
		$properParts = array();
		for ($i = 0; $i < count($parts); $i++) {
			$properParts[] = $parts[$i];
			if (!empty($braces[$i])) {
				$properParts[] = $braces[$i];
			}
		}
		$temp = '';
		$opening = 0;
		$closing = 0;
		$functions = array();
		$functionList = array();
		$functionName = "__constructor";
		$class['calledMethods'] = array();
		for ($i = 1; $i < count($properParts); $i++) {				
			$part = $properParts[$i];
			if ($part == '{') {
				$opening++;
				if ($opening == 1) {
					$part = "";
				}
			} elseif ($part == '}') {
				$closing++;
			}
			if ($opening > 0 && $opening == $closing) {
				$code = self::parseFunctionCode($temp, $functionName, $class['name']);
				preg_match_all('/\bthis\.(\w+)\(/', $code, $ms);
				$ms = $ms[1];
				if (!empty($ms)) {
					foreach ($ms as $msi) {
						$class['calledMethods'][] = array('method' => $functionName, 'called' => $msi);
					}						
				}
				self::parseArgsForCorrectors($arguments, $code, $class['name'], $functionName);
				$functions[] = array('name' => $functionName, 'args' => $arguments, 'code' => $code);
				$functionList[] = $functionName;
				$nextPart = $properParts[$i + 1];
				if (preg_replace("/[\s;]/", "", $nextPart) != "") {
					preg_match_all("/[\s;]*function {1,}(\w{1,}) *\(([^\)]*)\) *$/", $nextPart, $matches);
					$functionName = $matches[1][0];
					$arguments = $matches[2][0];
					$i++;
					
					if ($properParts[$i + 2] == '{' || empty($functionName)) {
						new Error(self::$errors['validationError'], array($class['name']));
					}
				}
				$opening = 0;
				$closing = 0;
				$temp = '';
			} else {
				$temp .= $part;
			}
		}
		$class['functions'] = $functions;
		$class['functionList'] = $functionList;		
		unset($class['content']);
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
						$code .= 'var '.$var."=this.get('".$var."');\n\t";
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
					$code .= "\n\tthis.set('".$varToSet."', ".$varToSet.");";
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
				if (!in_array($crr.'Crr', self::$correctors)) {
					new Error(self::$errors['unknownCorr'], array($crr, $name, $class));
				}
				$code = "\t".$k."=Corrector.correct('".$crr."',".$k.");\n".$code;
			}
		}
	}

}