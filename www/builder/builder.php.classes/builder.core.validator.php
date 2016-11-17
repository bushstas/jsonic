<?php

class CoreValidator 
{	
	private $errors = array(
		'noCoreFolder' => 'Директория скриптов ядра {??} не обнаружена. Путь к диретории: {??}',
		'noCoreFile' => 'Файл {??} в директории {??} не обнаружен. Путь к файлу: {??}',
		'noUtilsFunc' => 'Системная функция {??} не найдена в файле утилит {??}',
		'invalidFuncName' => 'Некорректное имя функции {??} в файле {??}. Имя функции утилиты должно начинаться с маленькой буквы'
	);

	private $content = array(
		'components' => array(
			'Application', 'Component', 'Condition', 'Control', 'Controller', 'Foreach', 'IfSwitch', 'Level', 'Menu', 'Switch', 'View'
		),
		'helpers' => array(
			'State', 'StateManager'
		),
		'prototypes' => array(
			'Array', 'Element', 'MouseEvent', 'String', 'Function'
		),
		'services' => array(
			'Core', 'AjaxRequest', 'EventHandler', 'Router', 'Tester', 'User', 'Validator', 'Controllers', 'Logger'
		),
		'utils' => array(
			'utils', 'Objects'
		)
	);

	public static $utilsClasses = array(
		'Dates', 'Decliner'
	);

	private $allUtilsFunctionNames = array();

	private $utilsFunctions = array(
		'generateRandomKey', 'toCamelCase', 'isComponentLike', 'isComponent', 'isController', 'isControl', 'isObject',
		'isArray', 'isArrayLike', 'isElement', 'isNode', 'isText', 'isFunction', 'isBool', 'isBoolean', 'isString', 'isNumber',
		'isPrimitive', 'isNumeric', 'isUndefined', 'isNull', 'isNone', 'isZero', 'isNotEmptyString', 'stringToNumber', 'getCount'
	);
	
	public function validate($pathToCore) {
		$pathToCore = rtrim($pathToCore, '/').'/';
		foreach ($this->content as $folder => $files) {
			if (!is_dir($pathToCore.$folder)) {
				new Error($this->errors['noCoreFolder'], array($folder, $pathToCore.$folder));
			}
			foreach ($files as $file) {
				if (!file_exists($pathToCore.$folder.'/'.$file.'.js')) {
					new Error($this->errors['noCoreFile'], array($file.'.js', $folder, $pathToCore.$folder.'/'.$file.'.js'));
				}
			}
		}
	}

	public function getUtilsFunctionNames() {
		return $this->allUtilsFunctionNames;
	}

	public function validateUtilsFunction($coreFiles) {
		foreach ($coreFiles as $coreFile) {
			if ($coreFile['name'] == 'utils') {
				preg_match_all('/\bfunction\s+(\w+)\s*\(/', $coreFile['content'], $matches);
				$funcs = $this->allUtilsFunctionNames = $matches[1];
				foreach ($funcs as $funcName) {
					if (!$this->isValidFuncName($funcName)) {
						new Error($this->errors['invalidFuncName'], array($funcName, $coreFile['path']));
					}
					if (!in_array($funcName, $funcs)) {
						new Error($this->errors['noUtilsFunc'], array($funcName, $coreFile['path']));
					}
				}
				break;
			}
		}
	}

	private function isValidFuncName($name) {
		return preg_match("/^[a-z]\w*$/", $name);
	}
}