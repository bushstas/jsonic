<?php

class Printer 
{	
	public function log($arr, $isExit = false) {
		if (!is_array($arr)) {
			$arr = array($arr);
		}
		print('<xmp>');
		print_r($arr);
		print('</xmp>');
		if ($isExit) exit();
	}
}