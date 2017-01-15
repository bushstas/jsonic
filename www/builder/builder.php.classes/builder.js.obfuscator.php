<?php
class JSObfuscator
{
	private static $map = array();
	private static $letters = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm');
	private static $prevSign = '';
	private static $index = 0;

	public static function init() {
		shuffle(self::$letters);
	}

	public static function obfuscate(&$jsOutput) {
		$jsOutput = preg_replace('/<<-\s+(?=[\.\[])/', '<<-', $jsOutput);
		$regexp = '/([\w\-]+)\s*<<-/';
		preg_match_all($regexp, $jsOutput, $matches);
		
		$names = $matches[1];
		$parts = preg_split($regexp, $jsOutput);
		$jsOutput = '';
		foreach ($parts as $i => $part) {
			$jsOutput .= $part;
			if (isset($names[$i])) {
				if (!isset(self::$map[$names[$i]])) {
					self::obfuscateItem($names[$i]);
				}
				$jsOutput .= self::$map[$names[$i]];
			}
		}
		self::removeMarks($jsOutput);
	}
	

	private static function obfuscateItem($value) {
		if (isset(self::$map[$value])) {
			return;
		}
		$obfuscated = self::generateVarName();
		self::$map[$value] = $obfuscated;
	}

	private static function generateVarName() {
		$count = count(self::$letters);
		$name = self::$prevSign.self::$letters[self::$index];

		self::$index++;
		if (self::$index >= $count) {
			self::$index = 0;
			if (empty(self::$prevSign)) {
				self::$prevSign = self::$letters[0];
			} else {
				$i = array_search(self::$prevSign, self::$letters);
				self::$prevSign = self::$letters[$i + 1];
			}
		}
		return $name;
	}

	public static function saveData() {
		$data = "<?php \n\n\$map = array(\n";
		foreach (self::$map as $k => $v) {
			$data .= "\t'".$k."' => '".$v."',\n";
		}
		$data .= ");\n\n?>";
		file_put_contents('builder.php.classes/data/js.map.php', $data);
	}

	public static function removeMarks(&$jsOutput) {
		$jsOutput = preg_replace('/ *<<-/', '', $jsOutput);
	}
}