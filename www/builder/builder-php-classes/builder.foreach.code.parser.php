<?php

class ForeachCodeParser 
{	
	private static $code;

	public static function parse($content, $templateName, $className) {
		TemplateSyntaxParser::init('foreach', $templateName, $className);
		self::$code = 'foreach ';
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
		self::parseKey($key);
		self::parseValue($value);
		self::parseWhile($while);
		self::parseLimit($limit);
	}

	private static function parseItems($items) {
		$data = TemplateSyntaxParser::parse($items, array('$', '~', '&', '.', 'a'), self::$code);		 
		self::$code .= $items.' as ';
		Printer::log($data);
	}

	private static function parseKey($key) {
		if (!empty($key)) {
			$data = TemplateSyntaxParser::parse($key, array('&'), self::$code);
			self::$code .= $key.' => ';
			Printer::log($data);
		}
	}

	private static function parseValue($value) {
		$data = TemplateSyntaxParser::parse($value, array('&'), self::$code);
		self::$code .= $value.' ';
		Printer::log($data);
	}

	private static function parseWhile($while) {
		if (!empty($while)) {
			self::$code .= 'while ';
			$data = TemplateSyntaxParser::parse($while, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			Printer::log($data);
		}
	}

	private static function parseLimit($limit) {
		if (!empty($limit)) {
			self::$code .= 'limit ';
			$data = TemplateSyntaxParser::parse($limit, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			Printer::log($data);
		}
	}

	private static function select($keyword, $content) {
		$parts = explode($keyword, $content);
		foreach ($parts as &$part) {
			$part = trim($part);
		}
		return $parts;
	}
}