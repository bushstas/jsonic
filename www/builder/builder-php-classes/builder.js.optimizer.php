<?php

class JSOptimizer
{
	private static $regexp = '/function[\sa-zA-Z_]*\([^\)]*\)\s*\{|\{|\}/';
	private static $tmpMark = '&&&TMPMARK&&&';
	private static $funcArgsMark = '&&&FNCARGMARK&&&';
	private static $content;
	private static $letters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
	private static $numbers = '1234567890';
	private static $varMaps;
	private static $varMapCouns;
	private static $varNames = array();
	private static $varsByLevels;
	private static $allVars;
	private static $level;

	private static function init() {
		if (empty(self::$varNames)) {
			self::generateNames();
		}
		self::$varsByLevels = array(
			array(), array(), array(), array(), array(),
			array(), array(), array(), array(), array(),
			array(), array(), array(), array(), array()
		);
		self::$allVars = array();
		self::$level = 0;
		self::$varMapCouns = array(
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0,
			0, 0, 0, 0, 0
		);
		self::$varMaps = array(
			array(), array(), array(), array(), array(),
			array(), array(), array(), array(), array(),
			array(), array(), array(), array(), array()
		);
	}

	public static function optimize(&$content) {
		self::init();
		TextParser::encode($content, 'js');		
		$content = self::process($content, array());				
		TextParser::decode($content, 'js');
	}

	private static function process($content, $vars) {
		$data = Splitter::split(self::$regexp, $content);
		if (!empty($data)) {
			extract($data);
		} else {
			$items = array($content);
			$delimiters = array();
		}
		$parts = array(
			array('content' => '')
		);
		$currentPart = &$parts[count($parts) - 1];
		$openBrackets = array();

		$level = 0;
		foreach ($items as $i => $item) {
			if (!empty($item)) {
				$currentPart['content'] .= $item;
			}
			if (isset($delimiters[$i])) {
				$d = $delimiters[$i];
				if ($d[0] == 'f') {
					$level++;
					if ($level == 1) {
						$funcsArgs = '';
						preg_match_all('/^function\s*(\w*)\s*\(([^\)]*)/', $d, $matches);
						if (!empty($matches[1][0]) && !isset($vars[$matches[1][0]])) {
							self::addVar($matches[1][0], $vars, array_values($vars));
						}
						if (!empty($matches[2][0])) {
							$funcsArgs = $matches[2][0];
						}
						$currentPart['content'] .= 'function'.(!empty($matches[1][0]) ? ' '.$matches[1][0]: '').'('.self::$funcArgsMark.'){';
						$parts[] = array(
							'isChild' => true,
							'args' => $funcsArgs,
							'content' => ''
						);
						$currentPart = &$parts[count($parts) - 1];
					} else {
						$currentPart['content'] .= $d;
					}
					if (!isset($openBrackets[$level])) {
		 				$openBrackets[$level] = 0;
		 			}
		 			$openBrackets[$level]++;
				} elseif ($d == '{') {
					$openBrackets[$level]++;
					$currentPart['content'] .= $d;
				} else {
					$openBrackets[$level]--;
					if ($openBrackets[$level] == 0) {
		 				if ($level == 1) {
			 				$parts[] = array(
								'content' => $d
							);
							$currentPart = &$parts[count($parts) - 1];
						} else {
							$currentPart['content'] .= $d;
						}
						if ($level > 0) {
							$level--;
						}
		 			} else {
						$currentPart['content'] .= $d;
					}
				}
			}
		}
		$code = '';
		foreach ($parts as $part) {
			if (empty($part['isChild'])) {
				$code .= $part['content'];
			}
		}
		self::processCode($code, $vars);
		$content = '';
		$funcArgs = array();
		$argsList = array();
		$usedNames = array_values($vars);
		foreach ($parts as $part) {
			if (!empty($part['isChild'])) {
				$args = $vars;
				if (!empty($part['args'])) {
					$argsList[] = $part['args'];
					self::parseArgs($part['args'], $args, $usedNames, $funcArgs);
				} else {
					$argsList[] = '';
				}
				$content .= self::process($part['content'], $args);
			} else {
				$content .= self::obfuscate($part['content'], $vars);
			}
		}
		if (!empty($funcArgs)) {
			$allArgs = implode('|', $argsList);
			foreach ($funcArgs as $k => $v) {
				$allArgs = preg_replace('/\b'.$k.'\b/', $v, $allArgs);
			}
			$argsList = explode('|', $allArgs);
			$parts = explode(self::$funcArgsMark, $content);
			$content = '';			
			foreach ($parts as $i => $part) {
				$content .= $part;
				if (isset($argsList[$i])) {
					$content .= $argsList[$i];
				}
			}
		} else {
			$content = str_replace(self::$funcArgsMark, '', $content);
		}
		return $content;
	}

	private static function parseArgs($argsCode, &$vars, &$usedNames, &$funcArgs) {
		$parts = explode(',', $argsCode);
		foreach ($parts as $part) {
			$part = trim($part);
			$ps = explode('=', $part);
			if (isset($ps[1])) {
				$part = trim($ps[0]);
			}
			if ($part == CONST_THIS || $part == CONST_PROPS) continue;
			if (!isset($funcArgs[$part])) {
				$obfuscatedName = self::addVar($part, $vars, $usedNames);
				$funcArgs[$part] = $obfuscatedName;
			} else {
				$vars[$part] = $funcArgs[$part];
				$usedNames[] = $funcArgs[$part];
			}
		}		
	}

	private static function processCode($code, &$vars) {
		Printer::log($vars);
		$code = trim(preg_replace('/\s+,|,\s+/', ',', $code));
		if (strlen($code) > 2) {
			$parts = preg_split('/\b(let|var|const)\b/', $code);
			if (count($parts) > 1) {
				for ($i = 1; $i < count($parts); $i++) {
					$ps = preg_split('/;|[\r\n]/', $parts[$i]);
					$p = trim($ps[0]);
					self::addVars($p, $vars);
				}
			}
		}
	}

	private static function addVars($code, &$vars) {
		$usedNames = array_values($vars);
		$a = 0;
		$prevDelimiter = ',';
		$data = Splitter::split('/[,\[\]\{\}\(\)]/', $code);
		if (empty($data)) {
			$items = array($code);
		} else {
			extract($data);
		}
		foreach ($items as $i => $item) {
			if ($a == 0 && $prevDelimiter == ',') {
				if (empty($item)) continue;
				$ps = preg_split('/[=\s]/', $item);
				if (!preg_match('/^[a-zA-Z_\$]/', $ps[0])) continue;
				self::addVar(trim($ps[0]), $vars, $usedNames);
			}
			if (isset($delimiters[$i])) {
				switch ($delimiters[$i]) {
					case '{':
					case '[':
					case '(':
						$a++;
					break;

					case '}':
					case ']':
					case ')':
						if ($a > 0) {
							$a--;
						}
					break;
				}
				$prevDelimiter = $delimiters[$i];
			}
		}
	}

	private static function addVar($varName, &$vars, &$usedNames) {
		if (!isset($vars[$varName])) {
			$name = self::getVarName($usedNames);
			$vars[$varName] = $name;
			$usedNames[] = $name;
			return $name;
		}
		return $vars[$varName];
	}

	private static function getVarName($usedNames) {
		for ($i = 0; $i < count(self::$varNames); $i++) {
			if (!in_array(self::$varNames[$i], $usedNames)) {
				return self::$varNames[$i];
			}
		}		
	}

	private static function obfuscate($code, $vars) {		
		foreach ($vars as $realName => $obfuscatedName) {
			$code = preg_replace('/\.'.$realName.'\b/',self::$tmpMark, $code);
			$code = preg_replace('/\b'.$realName.'\b/', $obfuscatedName, $code);
			$code = str_replace(self::$tmpMark, '.'.$realName, $code);
		}
		return $code;
	}

	private static function generateNames() {
		$letters = str_split(self::$letters, 1);
		$numbers = str_split(self::$numbers, 1);
		shuffle($letters);
		foreach ($letters as $l) {
			self::$varNames[] = $l;
		}
		$combined = array();
		shuffle($letters);
		shuffle($numbers);
		foreach ($letters as $l) {
			foreach ($numbers as $n) {
				$combined[] = $l.$n;
			}
			foreach ($letters as $a) {
				$name = $l.$a;
				if ($name != 'in' && $name != 'if') {
					$combined[] = $name;
				}
			}
		}
		shuffle($combined);
		self::$varNames = array_merge(self::$varNames, $combined);
	}
}