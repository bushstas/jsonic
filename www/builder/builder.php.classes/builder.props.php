<?php

class Props 
{	
	private static $list = array(
		'className' => 'c',
		'class' => 'c',
		'id' => 'i',
		'value' => 'v',
		'title' => 't',
		'placeholder' => 'p',
		'type' => 'tp',
		'href' => 'h',
		'src' => 's',
		'target' => 'tr',
		'method' => 'm',
		'style' => 'st',
		'width' => 'w',
		'height' => 'ht',
		'size' => 'sz',
		'maxlength' => 'mx',
		'action' => 'a',
		'name' => 'n',
		'scope' => 'sc',
		'role' => 'r',
		'cellpadding' => 'cp',
		'cellspacing' => 'cs'
	);

	public static function getList() {
		return array_flip(self::$list);
	}

	public static function get($name) {
		return self::$list[$name];
	}
}