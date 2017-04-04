<?php

class ForeachCodeParser 
{	
	private static $code;
	private static $D = '-||-';
	private static $items, $key, $value, $limit, $while;

	public static function parse($content, $templateName, $className) {
		TemplateSyntaxParser::init('foreach', $templateName, $className);
		self::$code = 'foreach ';
		self::prepareData($content);

		self::parseItems();
		self::parseKey();
		self::parseValue();
		self::parseWhile();
		self::parseLimit();
	}

	private static function prepareData($content) {
		TextParser::encode($content, 'foreach');		
		$content = preg_replace('/^foreach\s*/', '', $content);
		list($items, $rest) = self::select('\sas\b', $content);
		list($rest, $while) = self::select('\swhile\b', $rest);
		list($rest, $limit) = self::select('\slimit\b', $rest);
		list($key, $value) = self::select('=>', $rest);

		$content = $items.self::$D.$key.self::$D.$value.self::$D.$while.self::$D.$limit;

		TextParser::decode($content, 'foreach');
		list($items, $key, $value, $while, $limit) = explode(self::$D, $content);

		if (empty($value)) {
			$value = $key;
			$key = null;
		}
		self::$items = $items;
		self::$key = $key;
		self::$value = $value;
		self::$while = $while;
		self::$limit = $limit;
	}

	private static function parseItems() {
		$data = TemplateSyntaxParser::parse(self::$items, array('$', '~', '&', '.', 'a', '(', '!'), self::$code);		 
		self::$code .= self::$items.' as ';
		Printer::log($data);
	}

	private static function parseKey() {
		if (!empty(self::$key)) {
			$data = TemplateSyntaxParser::parse(self::$key, array('&'), self::$code);
			self::$code .= self::$key.' => ';
			Printer::log($data);
		}
	}

	private static function parseValue() {
		$data = TemplateSyntaxParser::parse(self::$value, array('&'), self::$code);
		self::$code .= self::$value.' ';
		Printer::log($data);
	}

	private static function parseWhile() {
		if (!empty(self::$while)) {
			self::$code .= 'while ';
			$data = TemplateSyntaxParser::parse(self::$while, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			Printer::log($data);
		}
	}

	private static function parseLimit() {
		if (!empty(self::$limit)) {
			self::$code .= 'limit ';
			$data = TemplateSyntaxParser::parse(self::$limit, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			Printer::log($data);
		}
	}

	private static function select($keyword, $content) {
		$parts = preg_split('/'.$keyword.'/', $content);
		foreach ($parts as &$part) {
			$part = trim($part);
		}
		return $parts;
	}
}