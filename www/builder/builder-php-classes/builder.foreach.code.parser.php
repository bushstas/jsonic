<?php

class ForeachCodeParser 
{	
	private static $code;
	private static $D = '-||-';
	private static $items, $key, $value, $limit, $while, $data = array();

	public static function parse($content, $templateName, $className) {
		TemplateSyntaxParser::init('foreach', $templateName, $className);
		self::$code = 'foreach ';
		self::prepareData($content);

		self::parseItems();
		self::parseKey();
		self::parseValue();
		self::parseWhile();
		self::parseLimit();

		self::$data['items'] = self::$items;
		self::$data['key'] = self::$key;
		self::$data['value'] = self::$value;
		self::$data['while'] = self::$while;
		self::$data['limit'] = self::$limit;
		return self::$data;
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
		self::addData($data);
		self::$items = $data['c'];
	}

	private static function parseKey() {
		if (!empty(self::$key)) {
			$data = TemplateSyntaxParser::parse(self::$key, array('&'), self::$code);
			self::$code .= self::$key.' => ';
			self::addData($data);
			self::$key = $data['c'];
		}
	}

	private static function parseValue() {
		$data = TemplateSyntaxParser::parse(self::$value, array('&'), self::$code);
		self::$code .= self::$value.' ';
		self::addData($data);
		self::$value = $data['c'];
	}

	private static function parseWhile() {
		if (!empty(self::$while)) {
			self::$code .= 'while ';
			$data = TemplateSyntaxParser::parse(self::$while, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			self::addData($data);
			self::$while = $data['c'];
		}
	}

	private static function parseLimit() {
		if (!empty(self::$limit)) {
			self::$code .= 'limit ';
			$data = TemplateSyntaxParser::parse(self::$limit, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), self::$code);
			self::addData($data);
			self::$limit = $data['c'];
		}
	}

	private static function addData($data) {
		foreach ($data as $k => $v) {
			if ($k == 'c') continue;
			if (!is_array(self::$data[$k])) {
				self::$data[$k] = $v;
			} else {
				foreach ($v as $i) {
					if (!in_array($i, self::$data[$k])) {
						self::$data[$k][] = $i;
					}
				}
			}
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