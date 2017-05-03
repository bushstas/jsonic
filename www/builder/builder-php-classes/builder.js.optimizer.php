<?php

class JSOptimizer
{
	private static $regexp = '/function[\sa-zA-Z_]*\([^\)]*\)\s*\{|\{|\}/';
	private static $tmpMark = '_&&&TMPMARK&&&_';
	private static $content;
	private static $letters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
	private static $numbers = '1234567890';
	private static $openVarStatements;
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
		self::$openVarStatements = array();
		self::$content = '';
	}

	public static function optimize($content) {
		self::init();
		TextParser::encode($content, 'js');
		$a = explode('=_=&&&&=_=', $content);
		Printer::log(count($a));
		$functions = array();
		$data = Splitter::split(self::$regexp, $content);
		extract($data);
		$openBrackets = array(0);
		$contents = array();
		
		$currentArgs = '';		
		
		foreach ($items as $i => $item) {
			if (!empty($item)) {
				if (!isset($contents[self::$level])) {
					$contents[self::$level] = '';
				}
				$contents[self::$level] .= $item;
			}
			if (isset($delimiters[$i])) {
				$d = $delimiters[$i];
				if ($d[0] == 'f') {
					self::processCode($contents[self::$level]);
					self::$level++;
					if (!isset($openBrackets[self::$level])) {
						$openBrackets[self::$level] = 0;
					}
					$openBrackets[self::$level]++;
					preg_match('/function[\sa-zA-Z_]*\(([^\)]*)/', $d, $match);
					$contents[self::$level] .= $d;
					$currentArgs = $match[1];
					
				} elseif ($d == '{') {
					$contents[self::$level] .= '{';
					$openBrackets[self::$level]++;
				} else {
					$openBrackets[self::$level]--;
					$contents[self::$level] .= '}';
					if ($openBrackets[self::$level] == 0) {
						self::processCode($contents[self::$level], true);
						self::$level--;
					}
				}
			}
			
		}
		$a = explode('=_=&&&&=_=', self::$content);
		Printer::log(count($a));
		TextParser::decode(self::$content, 'js');
		return self::$content;
	}

	private static function processCode(&$content, $cleanVars = false) {
		$isOpenStatement = !!self::$openVarStatements[self::$level];
		if ($isOpenStatement) {
			self::$openVarStatements[self::$level] = false;
		}
		$cnt = trim(preg_replace('/\s+,|,\s+/', ',', $content));
		if (strlen($cnt) > 2) {
			if ($isOpenStatement && $cnt[0] == ',') {
				$cnt = 'var '.ltrim($cnt, ',');
			}
			$parts = preg_split('/\b(let|var|const)\b/', $cnt);
			if (count($parts) > 1) {
				for ($i = 1; $i < count($parts); $i++) {
					$ps = preg_split('/;|[\r\n]/', $parts[$i]);
					$p = trim($ps[0]);
					self::addVars($p);
					if ($p[strlen($p) - 1] == '=') {
						self::$openVarStatements[self::$level] = true;
					}
				}
			}
			self::obfuscate($content);
		}
		self::$content .= $content;
		if ($cleanVars) {
			self::$varsByLevels[self::$level] = array();
			foreach (self::$varMaps[self::$level] as $k => $v) {
				self::$allVars[$k] = false;
			}
		}
		$content = '';
	}

	private static function addVars($code) {
		$a = 0;
		$b = 0;
		$c = 0;
		$d = true;
		$data = Splitter::split('/[,\[\]\{\}\(\)]/', $code);
		if (empty($data)) {
			$items = array($code);
		} else {
			extract($data);
		}
		foreach ($items as $i => $item) {
			if ($a == 0 && $b == 0 && $c == 0 && $d) {
				if (empty($item)) continue;
				$ps = preg_split('/[=\s]/', $item);
				if (!preg_match('/^[a-zA-Z_\$]/', $ps[0])) continue;
				self::$varsByLevels[self::$level][] = trim($ps[0]);
			}
			if (isset($delimiters[$i])) {
				$d = false;
				switch ($delimiters[$i]) {
					case '{':
						$a++;
					break;

					case '}':
						$a--;
					break;

					case '[':
						$b++;
					break;

					case ']':
						$b--;
					break;

					case '(':
						$c++;
					break;

					case ')':
						$c--;
					break;

					case ',':
						$d = true;
					break;
				}
			}
		}
	}

	private static function obfuscate(&$code) {
		for ($i = self::$level; $i >= 0; $i--) {
			foreach (self::$varsByLevels[$i] as $var) {
				$ov = self::getVarName($i, $var);
				$code = preg_replace('/\.'.$var.'\b/',self::$tmpMark, $code);
				$code = preg_replace('/\b'.$var.'\b/', $ov, $code);
				$code = str_replace(self::$tmpMark, '.'.$var, $code);
			}
		}
	}

	private static function getVarName($level, $key) {
		if (!isset(self::$varMaps[$level][$key])) {
			self::$varMaps[$level][$key] = self::getNextVarName(self::$varMapCouns[$level]);
			self::$varMapCouns[$level]++;
			self::$allVars[self::$varMaps[$level][$key]] = true;
		}
		return self::$varMaps[$level][$key];
	}

	private static function getNextVarName($index) {
		for ($i = 0; $i < count(self::$varNames); $i++) {
			if (!isset(self::$allVars[self::$varNames[$i]])) {
				return self::$varNames[$i];
			}
		}
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
				if ($name != 'in') {
					$combined[] = $name;
				}
			}
		}
		shuffle($combined);
		self::$varNames = array_merge(self::$varNames, $combined);
	}
}