<?php

class JSChecker
{	
	private static $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	public static $solidMethods = array(
		'render' => 'onRendered',
		'isDisabled' => '',
		'isRendered' => '',
		'isDisposed' => '',
		'instanceOf' => '',
		'disable' => '',
		'dispatchEvent' => '',
		'addListener' => '',
		'removeValueFrom' => '',
		'removeByIndexFrom' => '',
		'change' => '',		
		'addOneTo' => '',
		'addTo' => '',
		'get' => '',
		
		'g' => '',
		'a' => '',
		
		'showElement' => '',
		'setStyle' => '',
		'setPosition' => '',
		'setVisible' => '',
		'addClass' => '',
		'each' => '',
		'toggle' => '',
		'set' => '',
		'preset' => '',
		'update' => '',
		'delay' => '',
		'addChild' => '',
		'removeChild' => '',
		'forEachChild' => '',
		'forChildren' => '',
		'getControl' => '',
		'enableControl' => '',
		'forEachControl' => '',
		'hasControls' => '',
		'getControlsData' => '',
		'setControlsData' => '',
		'getParent' => '',
		'getChildAt' => '',
		'getChildIndex' => '',
		'getChildren' => '',
		'getChild' => '',
		'setId' => '',
		'getId' => '',
		'getElement' => '',
		'findElement' => '',
		'findElements' => '',
		'findElementWithinParent' => '',
		'findElementsWithinParent' => '',
		'fill' => '',
		'setAppended' => '',
		'placeTo' => '',
		'placeBack' => '',
		'appendChild' => '',
		'setScope' => '',
		'log' => '',
		'getUniqueId' => '',
		'dispose' => 'disposeInternal'
	);
	private static $errors = array(
		'Класс {??} не может иметь метод {??}, т.к. он зарезервирован и наследуется от класса <b>Component</b>. Вместо этого используйте метод {??}',
		'Класс {??} не может иметь метод {??}, т.к. он зарезервирован и наследуется от класса <b>Component</b>'
	);

	public static function check(&$class) {
		if (in_array($class['type'], self::$componentLikeClassTypes)) {
			foreach ($class['functions'] as $func) {
				$funcName = $func['name'];
				if (isset(self::$solidMethods[$funcName])) {
					if (!empty(self::$solidMethods[$funcName]))
					{
						new Error(self::$errors[0], array($class['name'], $funcName, self::$solidMethods[$funcName]));
					} 
					 else
					{
						new Error(self::$errors[1], array($class['name'], $funcName));
					}
				}
			}
		}
	}
}