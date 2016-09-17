<?php

class Error 
{	
	public function __construct($errors, $args = null) {
		if (!is_array($errors)) {
			if (is_array($args) && !empty($args)) {
				$errors = str_replace('{??}', '<b>{?}</b>', $errors);
				$regexp = '/\{\?\}/';
				$parts = preg_split($regexp, $errors);
				$errors = '';
				foreach ($parts as $i => $part) {
					$errors .= $part;
					if (isset($args[$i])) {
						$errors .= $args[$i];
					}
				}
			}
			$errors = array($errors);
		}
		$errors = implode('<div class="delimiter"></div>', $errors);
		die($errors);
	}
}