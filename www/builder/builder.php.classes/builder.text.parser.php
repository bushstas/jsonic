<?php

class TextParser
{
	private static $texts = array();
	private static $dictionary = array();
	private static $mark = '__TEXT__';
	private static $regexp = '/[\'"]/';

	public static function addToDictionary($keywords) {
		if (is_string($keywords)) {
			self::$dictionary[] = $keywords;
		} elseif (is_array($keywords)) {
			self::$dictionary = array_merge(self::$dictionary, $keywords);
		}
	}

	public static function getDictionary() {
		return self::$dictionary;
	}

	public static function encode(&$content, $key = null) {
		self::initTexts($key);
		$content = preg_replace('/\\\"/', '<sldq>', $content);
		$content = preg_replace("/\\\'/", '<slq>', $content);
		preg_match_all(self::$regexp, $content, $matches);
		$matches = $matches[0];
		$parts = preg_split(self::$regexp, $content);
		$content = '';
		$isText = false;
		$currentText = '';
		foreach ($parts as $i => $part) {
			if (!$isText) {
				$content .= $part;
				if (isset($matches[$i])) {
					$isText = true;
					$currentQuote = $matches[$i];
					$currentText = $currentQuote;
				}
			} else {
				$currentText .= $part;
				if (isset($matches[$i])) {
					if ($matches[$i] == $currentQuote) {
						$isText = false;
						$currentText .= $currentQuote;
						if (is_array(self::$texts[$key])) {
							self::$texts[$key][] = $currentText;
							$content .= self::$mark;
						} else {
							$content .= self::replaceArrayLikeSymbols($currentText);
						}
						$currentQuote = '';
						$currentText = '';						
					} else {
						$currentText .= $matches[$i];
					}
				}
			}
		}
	}

	private static function replaceArrayLikeSymbols($text) {
		$text = str_replace('[', '<arr>', $text);
		$text = str_replace(']', '</arr>', $text);
		$text = str_replace('{', '<obj>', $text);
		$text = str_replace('}', '</obj>', $text);
		return $text;
	}

	private static function initTexts($key) {
		if (is_string($key)) {
			self::$texts[$key] = array();
		}
	}

	public static function encodeThis(&$content) {
		self::encode($content, '_this_');
	}

	public static function transformIntoValidJson(&$content, $addNQs = false) {
		$regexp = '/\s*([\{\}\[\],:])\s*/';
		preg_match_all($regexp, $content, $signs);
		$signs = $signs[1];
		
		$parts = preg_split($regexp, $content);
		foreach ($parts as $i => &$part) {
			if (!empty($part) && $parts != 'null' && $part != 'false' && $part != 'true' && !is_numeric($part)) {
				$isNotQuoted = !preg_match('/^["\']/', $part);
				$part = trim(trim($part, '"'), "'");
				if ($addNQs && $isNotQuoted && self::inDictionary($part)) {
					$part = '<nq>'.$part.'<nq>';
				}
				$part = '"'.str_replace('"', "'", $part).'"';
			}
		}
		$content = '';
		for ($i = 0; $i < count($parts); $i++) {
			$content .= $parts[$i];
			if (isset($signs[$i])) {
				$content .= $signs[$i];
			}
		}		
	}

	private static function inDictionary($keyword) {
		return in_array($keyword, self::$dictionary);
	}

	public static function decode(&$content, $key) {
		if (!is_array(self::$texts[$key])) return;
		$parts = explode(self::$mark, $content);		
		$content = '';
		foreach ($parts as $i => $part) {
			$content .= $part;
			if (isset(self::$texts[$key][$i])) {
				$content .= self::$texts[$key][$i];
			}
		}
		$content = str_replace('<sldq>', '\"', $content);
		$content = str_replace("<slq>", "\'", $content);
	}

	public static function decodeThis(&$content) {
		self::decode($content, '_this_');
	}
}