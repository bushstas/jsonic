<?php

class CSSObfuscator 
{	
	private static $cssCounters = array(0, 0, 0);	
	private static $cssClassA = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m');
	private static $cssClassB = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9');
	private static $cssClassC = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9');
	
	public static function init() {
		shuffle(self::$cssClassA);
		shuffle(self::$cssClassB);
		shuffle(self::$cssClassC);
	}

	public static function generate() {
		$l1 = self::$cssClassA[self::$cssCounters[0]];
		$l2 = self::$cssClassB[self::$cssCounters[1]];
		$l3 = self::$cssClassC[self::$cssCounters[2]];
		self::$cssCounters[2]++;
		if (self::$cssCounters[2] == count(self::$cssClassC)) {
			self::$cssCounters[2] = 0;
			self::$cssCounters[1]++;
		}
		if (self::$cssCounters[1] == count(self::$cssClassB)) {
			self::$cssCounters[1] = 0;
			self::$cssCounters[0]++;
		}
		return $l1.$l2.$l3;
	}
}