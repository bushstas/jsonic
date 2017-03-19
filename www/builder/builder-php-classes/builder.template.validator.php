<?php

class TemplateValidator
{	
	private static $openTags;
	private static $openTagNames;
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
		'noClosingTag' => 'ќбнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>ƒанный {?} {?}-й по счету открывающийс€ {?} {??}<xmp>{?}</xmp>',
		'noClosingTag2' => 'ќбнаружен незакрытый {?} {??} в шаблоне {??} класса {??}<br>ƒанный {?} {?}-й по счету открывающийс€ {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag' => 'ќбнаружен лишний закрывающийс€ {?} {??} в шаблоне {??} класса {??}<br>ƒанный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>ќжидаетс€ закрытие тега {??}<br>ƒанный {?} {?}-й по счету открывающийс€ {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag2' => 'ќбнаружен лишний закрывающийс€ {?} {??} в шаблоне {??} класса {??}<br>ƒанный {?} следует после {?}-го по счету {?} {?} {??}<xmp>{?}</xmp>',
		'extraClosingTag3' => 'ќбнаружен закрывающийс€ {?} {??} в начале шаблона {??} класса {??}'
	);

	private static function init() {
		self::$openTags = array();
		self::$openTagNames = array();
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
				if (empty($item['isClosing'])) {
					self::handleOpening($item);
				} else {
					self::handleClosing($item);
				}
				self::$allTagNames[] = self::$tag;
				self::$prevType = empty($item['isClosing']) ? 'open' : 'closed';
				self::$types[] = self::$prevType;
			}
		}
		self::checkCompleteness();
		die('ok');
	}

	private static function checkCompleteness() {
		if (!empty(self::$openedData)) {
			$openedData = array_reverse(self::$openedData);
			extract($openedData[0]);			
 			$object = self::getTagTypeName($tag);
		 	new Error(self::$errors['noClosingTag2'], array($object, $tag, self::$templateName, self::$className, $object, $orderNumber, $object, $tag, $content));
		}
	}

	private static function handleOpening($item) {
		self::$openTags[] = $item;
		self::$allOpenTagNames[] = self::$tag;		
		self::$openTagNames[] = self::$tag;
		if (!isset(self::$openedTags[self::$tag])) {
			self::$openedTags[self::$tag] = 0;
		}
		self::$openedTags[self::$tag]++;
		
		self::$openedData[] = array(
			'tag' => self::$tag,
			'content' => $item['content'],
			'orderNumber' => self::$openedTags[self::$tag]
		);

		self::$prev = self::$tag;
		self::$indexes[] = self::$index;
		self::$realIndexes[self::$index] = self::$currentIndex;
		self::$index++;
	}

	private static function handleClosing($item) {
		if (empty(self::$prev) || self::$prev != self::$tag) {
			if (in_array(self::$tag, self::$openTagNames)) { 
				self::onClosingError();						
			} else {
				self::onClosingError2();
			}
		}

		if (!isset(self::$closedTags[self::$tag])) {
			self::$closedTags[self::$tag] = 0;
		}
		self::$closedTags[self::$tag]++;
		self::$closedTagNames[] = self::$tag;
		
		array_pop(self::$openTags);
		array_pop(self::$openTagNames);
		array_pop(self::$openedData);
		array_pop(self::$indexes);
	}

	private static function getLastOpenTag() {
		return self::$allOpenTagNames[count(self::$allOpenTagNames) - 1];
	}

	private static function getLastClosedTag() {
		return self::$closedTagNames[count(self::$closedTagNames) - 1];
	}

	private static function onClosingError() {
		$prevIndex = array_pop(self::$indexes);
		$realIndex = self::$realIndexes[$prevIndex];		
		$count = 0;
		foreach (self::$allTagNames as $i => $tag) {
			if ($tag == self::$prev && self::$types[$i] == 'open') $count++;
			if ($i == $realIndex) break;
		}
		$object = self::getTagTypeName(self::$prev);
		new Error(self::$errors['noClosingTag'], array($object, self::$prev, self::$templateName, self::$className, $object, $count, $object, self::$prev, self::$items[$realIndex]['content']));
	}

	private static function onClosingError2() {
		$prev = self::$prevType == 'open' ? self::getLastOpenTag() : self::getLastClosedTag();
		$openedData = array_reverse(self::$openedData);
		if (isset($openedData[0])) {
			extract($openedData[0]);
		}
		$object = self::getTagTypeName(self::$tag);
		$object2 = self::getTagTypeName($prev, 'а');
		
		$typeTag = self::$prevType == 'open' ? 'открывающегос€' : 'закрывающегос€';
		if (self::$prev != $prev && self::$prevType == 'open') {
			$typeTag = '';
		}
		if (!empty($prev)) {
			if (isset($content)) {
				$object3 = self::getTagTypeName($tag);
				new Error(
					self::$errors['extraClosingTag'], array($object, $tn, self::$templateName, self::$className, $object, self::$openedTags[$prev], $typeTag, $object2, $prev, self::$items[self::$currentIndex - 1]['content'],
					$tag, $object3, $orderNumber, $object3, $tag, $content
				));
			} else {			
				new Error(self::$errors['extraClosingTag2'], array($object, self::$tag, self::$templateName, self::$className, $object, self::$openedTags[$prev], $typeTag, $object2, $prev, self::$items[self::$currentIndex - 1]['content']));			
			}
		} else {
			new Error(self::$errors['extraClosingTag3'], array($object, self::$tag, self::$templateName, self::$className));
		}
	}

	private	static function getTagTypeName($tn, $ending = '') {
		return ($tn == 'if' || $tn == 'switch' || $tn == 'foreach'|| $tn == 'else' || $tn == 'ifempty' || $tn == 'case' || $tn == 'default' ? 'оператор' : 'тег').$ending;
	}
}