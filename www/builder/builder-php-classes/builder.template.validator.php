<?php

class TemplateValidator
{	
	private static $openTags;
	private static $openTagNames;
	private static $openHtmlTagNames;
	private static $closedTagNames;
	private static $allOpenTagNames;
	private static $allTagNames;
	private static $types;
	private static $openedData;
	private static $openedTags;
	private static $closedTags;
	private static $tag, $prev;
	private static $index;
	private static $indexes;
	private static $realIndexes;
	private static $currentIndex;
	private static $templateName;
	private static $className;
	private static $items;
	private static $prevType;

	private static $errors = array(
		'noClosingTag' => 'Обнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag' => 'Обнаружен лишний закрывающийся {?} {??} в шаблоне {??} класса {??}<br>Данный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>Ожидается закрытие тега {??}<br>Данный {?} {?}-й по счету открывающийся {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag2' => 'Обнаружен лишний закрывающийся {?} {??} в шаблоне {??} класса {??}<br>Данный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag3' => 'Обнаружен закрывающийся {?} {??} в начале шаблона {??} класса {??}',
		'tagInsideTag' => 'Обнаружена недопустимая вложенность: тег {??} внутри тега {??} в шаблоне {??} класса {??}<br>Данный тег {?}-й по счету открывающийся тег {??}<xmp>{?}</xmp>',
		'tagOutsideProperTag' => 'Обнаружена недопустимая вложенность: тег {??} вне {?} {??} в шаблоне {??} класса {??}<br>Данный тег {?}-й по счету открывающийся тег {??}<xmp>{?}</xmp>',
		'operatorOutOfPlace' => 'Обнаружен оператор {??} вне границ оператора {??} в шаблоне {??} класса {??}<br><br>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'textInSwitch' => 'Обнаружен текстовый элемент непосредственно внутри оператора <b>switch</b> в шаблоне {??} класса {??}<br><br>Обнаруженный текст: <xmp>{?}</xmp>',
		'elementInSwitch' => 'Обнаружен элемент DOM непосредственно внутри оператора <b>switch</b> в шаблоне {??} класса {??}<br><br>Обнаруженный элемент: <xmp>{?}</xmp>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'componentInSwitch' => 'Обнаружен компонент непосредственно внутри оператора <b>switch</b> в шаблоне {??} класса {??}<br><br>Обнаруженный компонент: <xmp>{?}</xmp>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'operatorInSwitch' => 'Обнаружен оператор непосредственно внутри оператора <b>switch</b> в шаблоне {??} класса {??}<br><br>Обнаруженный оператор: <xmp>{?}</xmp>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'templateInSwitch' => 'Обнаружен вызов шаблона непосредственно внутри оператора <b>switch</b> в шаблоне {??} класса {??}<br><br>Обнаруженный вызов шаблона: <xmp>{?}</xmp>Код в котором произошла ошибка: <xmp>{?}</xmp>',
		'closingSimpleTag' => 'Обнаружен закрывающийся тег {??} в шаблоне {??} класса {??}<xmp>{?}</xmp>'
	);

	private static $onlyParentalElements = array(
		'command' => array('menu'),
		'tbody' => array('table'),
		'thead' => array('table'),
		'tr' => array('table', 'thead', 'tbody'),
		'th' => array('tr'),
		'td' => array('tr'),
		'tfoot' => array('table'),
		'colgroup' => array('table'),
		'col' => array('colgroup', 'table'),
		'area' => array('map'),
		'source' => array('audio', 'video'),
		'dd' => array('dl'),
		'dt' => array('dl'),
		'fieldset' => array('form'),
		'figcaption' => array('figure'),
		'keygen' => array('form'),
		'li' => array('ul', 'ol', 'menu'),
		'optgroup' => array('select'),
		'option' => array('select', 'optgroup'),
		'summary' => array('details')
	);

	private static $forbiddenElements = array(
		'body', 'head', 'html', 'script', 'noscript', 'style', 'meta', 'link', 'title', 'frame',
		'base', 'bgsound', 'blink', 'center', 'comment', 'dir', 'font', 'applet', 'acronym',
		'frameset', 'hgroup', 'isindex', 'marquee', 'nobr', 'noembed', 'noframes', 'object',
		'plaintext', 'strike', 'tt', 'u', 'xmp'
	);

	private static $forbiddenInnerElements = array(
		'a' => array('a', 'form', 'caption'),
		'form' => array('a', 'form'),
		'address' => array('nav'),
		'pre' => array('big', 'img', 'small', 'sub', 'sup')
	);

	private static $allowedInnerElements = array(
		'p' => array(
			'span', 'table', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th', 'a', 'input',
			'img', 'video', 'audio', 'b', 'big', 'button', 'canvas', 'code', 'i',
			'iframe', 'label', 's', 'select', 'strong', 'textarea', 'small', 'abbr',
			'map', 'basefont', 'cite', 'datalist', 'del', 'dfn', 'em', 'embed', 'ins',
			'kbd', 'mark', 'meter', 'output', 'progress', 'q', 'samp', 'sub', 'sup', 'time',
			'var', 'wbr'
		),
		'canvas' => array(),
		'iframe' => array(),
		'textarea' => array(),
		'table' => array(
			'caption', 'tbody', 'thead', 'tr', 'td', 'th', 'colgroup', 'col', 'tfoot'
		),
		'dl' => array('dt', 'dd'),
		'select' => array('optgroup', 'option'),
		'details' => array('summary')
	);

	private static function init() {
		self::$openTags = array();
		self::$openTagNames = array();
		self::$openHtmlTagNames = array();
		self::$closedTagNames = array();
		self::$allOpenTagNames = array();
		self::$allTagNames = array();
		self::$types = array();
		self::$indexes = array();
		self::$realIndexes = array();
		self::$openedData = array();
		self::$openedTags = array();
		self::$closedTags = array();		
		self::$index = 0;
	}

	public static function validate($items, $templateName, $className) {
		self::init();
		self::$items = $items;
		self::$templateName = $templateName;
		self::$className = $className;
		foreach ($items as $index => $item) {
			if ($item['type'] == 'tag') {
				self::$currentIndex = $index;
				self::$tag = $item['tagName'];
				if (!$item['isSingle']) {
					if (empty($item['isClosing'])) {
						self::handleOpening($item);
					} else {
						self::handleClosing($item);
					}
				} else {
					self::handleSingleTag($item);
				}
				self::$allTagNames[] = self::$tag;
				self::$prevType = empty($item['isClosing']) ? 'open' : 'closed';
				self::$types[] = self::$prevType;
			} else {
				self::validateTextParent($item);
			}
		}
		self::checkCompleteness();
		//die('ok');
	}

	private static function validateTextParent($item) {
		$last = self::getLastOpenTag();
		if ($last == 'switch') {
			new Error(self::$errors['textInSwitch'], array(self::$templateName, self::$className, $item['content']));
		}
	}

	private static function checkCompleteness() {
		if (!empty(self::$openedData)) {
			$openedData = array_reverse(self::$openedData);
			extract($openedData[0]);			
 			$object = self::getTagTypeName($tag);
		 	new Error(self::$errors['noClosingTag'], array($object, $tag, self::$templateName, self::$className, $object, $orderNumber, $object, $tag, $content));
		}
	}

	private static function handleSingleTag($item) {
		$tag = self::$tag;
		if (!empty($item['isSingle']) && !empty($item['isClosing'])) {
			new Error(self::$errors['closingSimpleTag'], array($tag, self::$templateName, self::$className, $item['content']));
		}
		if ($tag == 'ifempty' && !in_array('foreach', self::$openTagNames)) {
			new Error(self::$errors['operatorOutOfPlace'], array($tag, 'foreach', self::$templateName, self::$className, $item['content']));
		} elseif ($tag == 'else' && !in_array('if', self::$openTagNames)) {
			new Error(self::$errors['operatorOutOfPlace'], array('else', 'if', self::$templateName, self::$className, $item['content']));
		}
		self::validateOpenTag($item);	
		self::$allOpenTagNames[] = $tag;
	}

	private static function handleOpening($item) {
		$tag = self::$tag;
		if (!isset(self::$openedTags[$tag])) {
			self::$openedTags[$tag] = 0;
		}
		self::$openedTags[$tag]++;
		self::validateOpenTag($item);
		
		self::$openTags[] = $item;
		self::$allOpenTagNames[] = $tag;
		self::$openTagNames[] = $tag;

		if (self::isHtmlTag($tag)) {
			self::$openHtmlTagNames[] = $tag;
		}

		self::$openedData[] = array(
			'tag' => self::$tag,
			'content' => $item['content'],
			'orderNumber' => self::$openedTags[self::$tag]
		);

		self::$indexes[] = self::$index;
		self::$realIndexes[self::$index] = self::$currentIndex;
		self::$index++;
	}

	private static function validateOpenTag($item) {
		$tag = self::$tag;
		$last = self::getLastOpenTag();
		$lastHtmlTag = self::getLastOpenHtmlTag();
		if ($tag != 'case' && $tag != 'default' && $last == 'switch') {
			$error = self::getSwitchError($tag);
			new Error(self::$errors[$error], array(self::$templateName, self::$className, $tag, $item['content']));
		}
		if (($tag == 'case' || $tag == 'default') && $last != 'switch') {
			new Error(self::$errors['operatorOutOfPlace'], array($tag, 'switch', self::$templateName, self::$className, $item['content']));
		}
		if (in_array($tag, self::$forbiddenElements)) {
			new Error(self::$errors['forbiddenTag'], array($tag, self::$templateName, self::$className, $item['content']));
		}
		
		if (isset(self::$forbiddenInnerElements[$lastHtmlTag]) && in_array($tag, self::$forbiddenInnerElements[$lastHtmlTag])) {
			new Error(self::$errors['tagInsideTag'], array($tag, $lastHtmlTag, self::$templateName, self::$className, self::$openedTags[$tag], $tag, $item['content']));
		}
		if (isset(self::$onlyParentalElements[$tag]) && !in_array($lastHtmlTag, self::$onlyParentalElements[$tag])) {
			new Error(self::$errors['tagOutsideProperTag'], array($tag, count(self::$onlyParentalElements[$tag]) > 1 ? 'тегов' : 'тега', implode(', ', self::$onlyParentalElements[$tag]), self::$templateName, self::$className, self::$openedTags[$tag], $tag, $item['content']));
		}
		if (isset(self::$allowedInnerElements[$lastHtmlTag]) && !in_array($tag, self::$allowedInnerElements[$lastHtmlTag])) {
			new Error(self::$errors['tagInsideTag'], array($tag, $lastHtmlTag, self::$templateName, self::$className, self::$openedTags[$tag], $tag, $item['content']));
		}
	}

	private static function getSwitchError($tag) {
		if (self::isOperator($tag)) {
			return 'operatorInSwitch';
		}
		if ($tag[0] == ':') {
			return 'templateInSwitch';
		}
		if (preg_match('/^[a-z]/', $tag)) {
			return 'elementInSwitch';
		}
		return 'componentInSwitch';
	}

	private static function handleClosing($item) {
		$tag = self::$tag;
		$prev = self::getLastOpenTag();
		if (empty($prev) || $prev != $tag) {
			if (in_array($tag, self::$openTagNames)) { 
				self::onClosingError();						
			} else {
				self::onClosingError2();
			}
		}

		if (!isset(self::$closedTags[$tag])) {
			self::$closedTags[$tag] = 0;
		}
		self::$closedTags[$tag]++;
		self::$closedTagNames[] = $tag;
		
		array_pop(self::$openTags);
		array_pop(self::$openTagNames);
		array_pop(self::$openedData);
		array_pop(self::$indexes);
		if (self::isHtmlTag($tag)) {
			array_pop(self::$openHtmlTagNames);
		}
	}

	private static function getLastOpenTag($isAll = false) {
		$source = $isAll ? self::$allOpenTagNames : self::$openTagNames;
		return $source[count($source) - 1];
	}

	private static function getLastOpenHtmlTag() {
		return self::$openHtmlTagNames[count(self::$openHtmlTagNames) - 1];
	}

	private static function getLastClosedTag() {
		return self::$closedTagNames[count(self::$closedTagNames) - 1];
	}

	private static function onClosingError() {
		$prev = self::getLastOpenTag();
		$prevIndex = array_pop(self::$indexes);
		$realIndex = self::$realIndexes[$prevIndex];		
		$count = 0;
		foreach (self::$allTagNames as $i => $tag) {
			if ($tag == $prev && self::$types[$i] == 'open') $count++;
			if ($i == $realIndex) break;
		}
		$object = self::getTagTypeName($prev);
		new Error(self::$errors['noClosingTag'], array($object, $prev, self::$templateName, self::$className, $object, $count, $object, $prev, self::$items[$realIndex]['content']));
	}

	private static function onClosingError2() {
		$prevOpen = self::getLastOpenTag();
		$prev = self::$prevType == 'open' ? self::getLastOpenTag(true) : self::getLastClosedTag();
		$openedData = array_reverse(self::$openedData);
		if (isset($openedData[0])) {
			extract($openedData[0]);
		}
		$object = self::getTagTypeName(self::$tag);
		$object2 = self::getTagTypeName($prev, 'а');
		
		$typeTag = self::$prevType == 'open' ? 'открывающегося' : 'закрывающегося';
		if ($prevOpen != $prev && self::$prevType == 'open') {
			$typeTag = '';
		}
		if (!empty($prev)) {
			if (isset($content)) {
				$object3 = self::getTagTypeName($tag);
				new Error(
					self::$errors['extraClosingTag'], array($object, self::$tag, self::$templateName, self::$className, $object, self::$openedTags[$prev], $typeTag, $object2, $prev, self::$items[self::$currentIndex - 1]['content'],
					$tag, $object3, $orderNumber, $object3, $tag, $content
				));
			} else {			
				new Error(self::$errors['extraClosingTag2'], array($object, self::$tag, self::$templateName, self::$className, $object, self::$openedTags[$prev], $typeTag, $object2, $prev, self::$items[self::$currentIndex - 1]['content']));			
			}
		} else {
			new Error(self::$errors['extraClosingTag3'], array($object, self::$tag, self::$templateName, self::$className));
		}
	}

	private	static function isHtmlTag($tag) {
		return !self::isOperator($tag);
	}

	private	static function isOperator($tag) {
		return $tag == 'if' || $tag == 'switch' || $tag == 'foreach'|| $tag == 'else' || $tag == 'ifempty' || $tag == 'case' || $tag == 'default';
	}

	private	static function getTagTypeName($tag, $ending = '') {
		return (self::isOperator($tag) ? 'оператор' : 'тег').$ending;
	}
}