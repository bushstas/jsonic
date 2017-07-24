<?php


class Error {

	public static function unexpected($found, $expected) {
		die('Неожиданный символ '.$found);		
	}

	public static function show($error, $args = null) {
		$err = Errors::get($error);
		if (!empty($err)) {
			$error = $err;
		}
		if (is_string($args) && !empty($args)) {
			$args = array($args);
		}
		if (is_array($args) && !empty($args)) {
			$error = str_replace('{??}', '<b>{?}</b>', $error);
			$regexp = '/\{\?\}/';
			$parts = preg_split($regexp, $error);
			$error = '';
			foreach ($parts as $i => $part) {
				$error .= $part;
				if (isset($args[$i])) {
					$error .= $args[$i];
				}
			}
		}	
		die($error);
	}
}