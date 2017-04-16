<?php

class TextParser
{
	private static $texts = array();
	private static $dictionary = array();
	private static $mark = '_=ENCODEDTEXT=_';
	private static $regexp = '/[\'"\/]/';

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

	public static function encode(&$content, $key = null, $mark = null) {
		if (empty($mark)) $mark = self::$mark;
		self::initTexts($key);
		$content = str_replace('\\/', '<bslash>', $content);
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
					if ($matches[$i] == '/') {
						$p = trim($parts[$i][strlen($parts[$i]) - 1]);
						if ($p != '(' && $p != '=') {
							$isText = false;
							$currentQuote = '';
							$currentText = '';
							$content .= $matches[$i];
						}
					}
				}
			} else {
				$currentText .= $part;
				if (isset($matches[$i])) {
					if ($matches[$i] == $currentQuote) {
						$isText = false;
						$currentText .= $currentQuote;
						if (is_array(self::$texts[$key])) {
							self::$texts[$key][] = $currentText;
							$content .= $mark;
						} else {
							$content .= self::replaceArrayLikeSymbols($currentText);
						}
						$currentQuote = '';
						$currentText = '';						
					} else {
						$currentText .= $matches[$i];
					}
				} else {
					$content .= $currentText;
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

	public static function transformIntoValidJson(&$content) {
		$regexp = '/\s*([\{\}\[\],:])\s*/';
		preg_match_all($regexp, $content, $signs);
		$signs = $signs[1];
		
		$parts = preg_split($regexp, $content);
		foreach ($parts as $i => &$part) {
			if (!empty($part) && $parts != 'null' && $part != 'false' && $part != 'true' && !is_numeric($part)) {
				$isNotQuoted = !preg_match('/^["\']/', $part);
				$part = trim(trim($part, '"'), "'");
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
		return $content;
	}

	private static function inDictionary($keyword) {
		return in_array($keyword, self::$dictionary);
	}

	public static function decode(&$content, $key = null, $mark = null) {
		if (empty($mark)) $mark = self::$mark;
		if (is_string($key)) {
			$texts = self::$texts[$key];
		}
		if (is_array($key)) {
			$texts = array();
			foreach ($key as $k) {
				if (is_array(self::$texts[$k])) {
					$texts = array_merge($texts, self::$texts[$k]);
				}
			}
		}
		if (!is_array($texts) || empty($texts)) return;
		$parts = preg_split('/'.$mark.'/', $content);
		$content = '';
		foreach ($parts as $i => $part) {
			$content .= $part;
			if (isset($texts[$i])) {
				$content .= $texts[$i];
			}
		}
		$content = str_replace('<sldq>', '\"', $content);
		$content = str_replace("<slq>", "\'", $content);
		$content = str_replace("<bslash>", "\/", $content);
	}

	public static function decodeThis(&$content) {
		self::decode($content, '_this_');
	}

	public static function createObjectString(&$object, $replacements = null) {
		if (is_array($object)) {
			$object = json_encode($object);
		}
		if (is_array($replacements)) {
			for ($i = 0; $i < count($replacements); $i++) {
				if (is_string($replacements[$i + 1])) {
					$object = preg_replace($replacements[$i], $replacements[$i + 1], $object);
				}
				$i++;
			}
		}
 		$object = str_replace('"', "'", $object);
	}

	public static function getTexts($key) {
		return self::$texts[$key];
	}

	public static function getMark() {
		return self::$mark;
	}

	public static function setTexts($texts, $key) {
		return self::$texts[$key] = $texts;
	}
}