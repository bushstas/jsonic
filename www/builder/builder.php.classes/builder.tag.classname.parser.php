<?php

class TagClassNameParser 
{
	private static $delimiter = '#_#DELIMITER#_#';

	public static function parseTexts(&$texts, $className, $shouldNotBePoint = false) {
		$isArr = is_array($texts);
		if ($isArr) {
			$text = implode(self::$delimiter, $texts);
		} else {
			$text = $texts;
		}
		$start = !$shouldNotBePoint ? '\. *(->>)*' : '->>';
		if (preg_match('/'.$start.' *@/', $text)) {
			$data = Splitter::split('/'.$start.' *@([\w\-]+)*/', $text);
			$text = '';
			$className = self::getTagClassName($className);
			foreach ($data['items'] as $i => $item) {
				$text .= $item;
				if (isset($data['delimiters'][$i])) {
					$parts = explode('@', $data['delimiters'][$i]);
					$cl = trim($parts[1]);					
					if (!empty($cl)) {
						$cl = $className.'_'.$cl;
					} else {
						$cl = $className;
					}
					$text .= (!$shouldNotBePoint ? '.' : '').'->>'.$cl;
				}
			}
			if ($isArr) {
				$texts = explode(self::$delimiter, $text);
			} else {
				$texts = $text;
			}
			return true;
		}
	}


	private static function getTagClassName($className) {
		$data = Splitter::split('/[A-Z]/', $className);
		$className = '';
		foreach ($data['items'] as $i => $item) {
			$className .= $item.'-';
			if (isset($data['delimiters'][$i])) {
				$className .= strtolower($data['delimiters'][$i]);
			}
		}
		return trim($className, '-');
	}


}