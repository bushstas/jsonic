<?php
	
class Transformer
{
	public static function transform($array, $tab = '', $isFirstLevel = true) {
		$code = '';
		if (is_array($array)) {
			if (empty($array)) {
				$code .= '[]';
			} else {
				if (self::isAssoc($array)) {
					$code .= "{\n";
					$items = array();
					foreach ($array as $key => $value) {
						$items[] = '"'.trim($key).'": '.self::transform($value, $tab."\t", false);
					}
					$code .= $tab."\t".implode(",\n".$tab."\t", $items)."\n".$tab."}";
				} else {
					$code .= "[\n";
					$items = array();
					for ($i = 0; $i < count($array); $i++) {
						$items[] = self::transform($array[$i], $tab."\t", false);
					}
					$code .= $tab."\t".implode(",\n".$tab."\t", $items)."\n".$tab."]";
				}
			}
		} else {
			if ($array === null) {
				$code .= 'null';
			} elseif ($array === false) {
				$code .= 'false';
			} elseif ($array === true) {
				$code .= 'true';
			} elseif (is_numeric($array)) {
				$code .= $array;
			} else {
				$code .= '"'.$array.'"';
			}
		}
		if ($isFirstLevel) {
			return '<pre>'.$code.'</pre>';
		}
		return $code;
	}

	private static function isAssoc($arr) {
	    if (array() === $arr) return false;
	    foreach ($arr as $k => $v) {
	    	if (is_string($k)) return true;
	    }
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}
}

?>