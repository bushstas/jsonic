<?php

class JSChecker
{	
	private static $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	private static $solidMethods = array(
		'render' => 'onRendered',
		'disable' => '',
		'isDisabled' => '',
		'instanceOf' => '',
		'dispatchEvent' => '',
		'removeValueFrom' => '',
		'removeByIndexFrom' => '',
		'plusTo' => '',
		'addOneTo' => '',
		'addTo' => '',
		'get' => '',
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
		'isRendered' => '',
		'isDisposed' => '',
		'setAppended' => '',
		'placeTo' => '',
		'placeBack' => '',
		'appendChild' => '',
		'setScope' => '',
		'log' => '',
		'getUniqueId' => '',
		'dispose' => 'disposeInternal',

		'_getParentElement' => '',
		'_provideWithComponent' => '',
		'_getNextSiblingChild' => '',
		'_setPrevSiblingChild' => '',
		'_setNextSiblingChild' => '',
		'_disposeLinks' => '',
		'_getFirstNodeChild' => '',
		'_subscribe' => '',
		'_getWaitingChild' => '',
		'_registerElement' => '',
		'_registerChildComponent' => '',
		'_unregisterChildComponent' => '',
		'_registerControl' => '',
		'_unregisterControl' => '',
		'_setParent' => '',
		'_registerPropActivity' => '',
		'_disposePropActivities' => '',
		'_getTemplateById' => ''
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