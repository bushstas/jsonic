<?php

class JSOptimizer
{
	private static $regexp = '/function[\sa-zA-Z_]*\([^\)]*\)\s*\{|\{|\}/';
	private static $content = '';

	public static function optimize($content) {
		TextParser::encode($content, 'js');
		$functions = array();
		$data = Splitter::split(self::$regexp, $content);
		extract($data);
		$level = 0;
		$openBrackets = array(0);
		$allVars = array();
		$varsByLevels = array();
		$contents = array();
		
		$currentArgs = '';		
		
		foreach ($items as $i => $item) {
			if (!empty($item)) {
				if (!isset($contents[$level])) {
					$contents[$level] = '';
				}
				$contents[$level] .= $item;
			}
			if (isset($delimiters[$i])) {
				$d = $delimiters[$i];
				if ($d[0] == 'f') {
					self::processCode($level, $contents[$level]);
					$level++;
					if (!isset($openBrackets[$level])) {
						$openBrackets[$level] = 0;
					}
					$openBrackets[$level]++;
					preg_match('/function[\sa-zA-Z_]*\(([^\)]*)/', $d, $match);
					$contents[$level] .= $d;
					$currentArgs = $match[1];
					
				} elseif ($d == '{') {
					$contents[$level] .= '{';
					$openBrackets[$level]++;
				} else {
					$openBrackets[$level]--;
					$contents[$level] .= '}';
					if ($openBrackets[$level] == 0) {
						self::processCode($level, $contents[$level], true);
						$level--;
					}
				}
			}
			
		}
		TextParser::decode($content, 'js');
	}

	private static function processCode($level, &$content, $cleanVars = false) {
		$cnt = preg_replace('/\s+,|,\s+/', ',', $content);
		self::$content .= $content;
		if (strlen($cnt) > 2) {
			$parts = preg_split('/\b(let|var|const)\b/', $cnt);
			if (count($parts) > 1) {
				for ($i = 1; $i < count($parts); $i++) {
					$ps = preg_split('/;|[\r\n]/', $parts[$i]);
					Printer::log($ps[0]);
				}
			}
		}
		if ($cleanVars) {
			$varsByLevels[$level] = array();
		}
		$content = '';
	}
}