<?php

class JSInterpreter 
{	
	private static $sources = array();
	private static $className;
	private static $sourcesDir = '/custom-parsers';

	public static function init() {
		$sources = Gatherer::getFiles(__DIR__.self::$sourcesDir, 'php');
		$ranked = array();
		foreach ($sources as $file) {
			$content = file_get_contents($file);
			preg_match('/\/\/\s*priority +(\d+)/i', $content, $match);
			if (empty($match[1])) {
				$match[1] = '99999';
			}
			if (!is_array($ranked[$match[1]])) {
				$ranked[$match[1]] = array();
			}
			$ranked[$match[1]][] = $file;
		}
		ksort($ranked);
		foreach ($ranked as $files) {
			self::$sources = array_merge(self::$sources, $files);
		}
	}

	public static function parse(&$content, $className) {
		self::prepareCode($content);
		self::$className = $className;
		foreach (self::$sources as $file) {
			include $file;
		}
		self::cleanCode($content);
	}

	private static function prepareCode(&$content) {
		$content = preg_replace("/\t/", " ", $content);
		$content = preg_replace("/\n +/", "\n", $content);
	}

	private static function cleanCode(&$content) {
		$content = str_replace(",)", ")", $content);
		$content = str_replace("= ", "=", $content);
	}

}