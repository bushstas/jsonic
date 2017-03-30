<?php

class ForeachCodeParser 
{	
	public static function parse($content) {
		$content = preg_replace('/^foreach\s*/', '', $content);
		list($items, $rest) = self::select('as', $content);
		list($rest, $while) = self::select('while', $rest);
		list($rest, $limit) = self::select('limit', $rest);
		list($key, $value) = self::select('=>', $rest);

		if (empty($value)) {
			$value = $key;
			$key = null;
		}
		


		self::parseItems($items);
		if (!empty($key)) {
			self::parseKey($key);
		}
		self::parseValue($value);
		self::parseWhile($while);
		self::parseLimit($limit);



	}

	private static function parseItems($items) {
		 
	}

	private static function select($keyword, $content) {
		$parts = explode($keyword, $content);
		foreach ($parts as &$part) {
			$part = trim($part);
		}
		return $parts;
	}
}