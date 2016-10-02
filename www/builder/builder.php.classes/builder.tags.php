<?php

class Tags 
{	
	private static $list = array(
		'div', 'span', 'table', 'tbody', 'thead', 'tr', 'td', 'th', 'ul', 'ol', 'li', 'p', 'a', 'form', 'input', 'img', 'video', 'audio', 'aside',
		'article', 'b', 'big', 'blockquote', 'button', 'canvas', 'caption', 'center', 'code', 'col', 'colgroup', 'footer', 'font', 'h1', 'h2', 'h3',
		'h4', 'h5', 'h6', 'header', 'hr', 'i', 'iframe', 'label', 'menu', 'pre', 's', 'section', 'select', 'strong', 'textarea', 'u', 'small'
	);

	public static function getList() {
		return self::$list;
	}

	public static function get($tag) {
		return array_search($tag, self::$list);
	}
}