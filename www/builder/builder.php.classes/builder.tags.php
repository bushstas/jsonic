<?php

class Tags 
{	
	private static $list = array(
		'div', 'span', 'table', 'tbody', 'thead', 'tr', 'td', 'th', 'ul', 'ol', 'li', 'p', 'a', 'form', 'input', 'img', 'video', 'audio', 'aside',
		'article', 'b', 'big', 'blockquote', 'button', 'canvas', 'caption', 'code', 'col', 'colgroup', 'footer', 'h1', 'h2', 'h3',
		'h4', 'h5', 'h6', 'header', 'hr', 'i', 'iframe', 'label', 'menu', 'pre', 's', 'section', 'select', 'strong', 'textarea', 'small', 'nav',
		'abbr', 'address', 'area', 'map', 'source', 'basefont', 'cite', 'datalist', 'dt', 'dl', 'dd', 'del', 'details', 'dfn', 'em', 'embed', 'fieldset',
		'figcaption', 'figure', 'ins', 'kbd', 'keygen', 'main', 'mark', 'meter', 'optgroup', 'option', 'output', 'param', 'progress', 'q', 'samp', 'sub',
		'summary', 'sup', 'tfoot', 'time', 'var', 'wbr'
	);

	public static function getList() {
		return self::$list;
	}

	public static function get($tag) {
		return array_search($tag, self::$list);
	}
}