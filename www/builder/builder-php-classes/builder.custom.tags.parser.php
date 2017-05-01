<?php

class CustomTagsParser 
{	
	private static $regexp = '/@(\w+)\s*:\s*/';
	private static $tags = array();

	public static function init($tagsFiles) {
		if (is_array($tagsFiles)) {
			foreach ($tagsFiles as $tagsFile) {
				preg_match_all(self::$regexp, $tagsFile['content'], $matches);
				$varNames = $matches[1];
				if (!empty($varNames)) {
					$parts = preg_split(self::$regexp, $tagsFile['content']);
					array_shift($parts);
					foreach ($parts as $i => $part) {
						self::$tags[$varNames[$i]] = self::parseTag(trim($part));
					}
				}
			}
		}
	}

	private static function parseTag($content) {
		preg_match_all('/^<([a-z]\w*)([^<>]*)>$/', $content, $matches);
		if (empty($matches[1])) {
			die('45');
		}
		preg_match_all('/([a-z]\w*)="([^"]+)"/', $matches[2][0], $m1);
		preg_match_all('/([a-z]\w*)=\'([^\']+)\'/', $matches[2][0], $m2);
		
		$names = $m1[1];
		$values = $m1[2];
		if (!empty($m2[1])) {
			$names = array_merge($names, $m2[1]);
			$values = array_merge($values, $m2[2]);
		}
		$attrs = array();
		foreach ($names as $i => $name) {
			$attrs[$name] = $values[$i];
		}
		return array('tagName' => $matches[1][0], 'attributes' => $attrs);
	}

	public static function get() {
		return self::$tags;
	}
}