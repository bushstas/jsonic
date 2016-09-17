<?php

class JSCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'entryNotFound' => 'Параметр конфигурации <b>entry</b>, обозначающий класс точку входа, не найден',
		'entryNotString' => 'Значение параметра конфигурации <b>entry</b> не является строкой',
		'forbiddenEntrySymbols' => "Параметр конфигурации entry = '{??}' содержит запрещенные символы",
		'noPatternEntry' => "Параметр конфигурации entry = '{??}' не соответствует паттерну <b>[A-Z]\w+</b>",
		'folderIsNotString' => 'Значение параметра конфигурации <b>jsFolder</b> не является строкой',
		'folderNameIsInvalid' => 'Значение параметра конфигурации <b>jsFolder</b> содержит запрещенные символы {??}',
		'tooltipClassNotString' => 'Параметр конфигурации <b>tooltipClass</b> должен быть строкой, содержащей название класса',
		'tooltipApiNotString' => 'Параметр конфигурации <b>tooltipApi</b> должен быть строкой, содержащей путь к api для загрузки текста подсказки',
		'jsFilesNotFound' => 'JS файлы для компиляции приложения, находящиеся внутри директории указанной в параметре конфигурации <b>scope</b>, не найдены',
		'coreFilesNotFound' => 'JS файлы ядра приложения, находящиеся внутри директории указанной в параметре конфигурации <b>so</b>, не найдены'
	);

	private $coreClasses = array(
		'Component', 'Controller', 'Application', 'View', 'Dialog', 'Menu'
	);

	private $reservedNames = array(
		'Component', 'Controller', 'Application', 'View', 'Level',
		'Control', 'AjaxRequest', 'Router', 'Objects', 'Corrector',
		'Condition', 'Core', 'Menu', 'EventHandler', 'Dialoger', 'Foreach',
		'Globals', 'User', 'StoreKeeper', 'Switch', 'Tooltiper', 'IfSwitch',
		'__', '__T', '__ROUTES', '__TAGS', '__A', '__EVENTTYPES', '__HASHROUTER', '__DEFAULTROUTE', '__ERRORROUTES',
		'__VIEWCONTAINER', '__USEROPTIONS', '__D', '__V', '__DW', '__CRRS'
	);

	private $superClasses = array('component', 'dialog', 'form', 'control', 'menu');
	private $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	private $classes = array();

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getJsConfig();


		$this->validateEntry();
		$this->validateJsFolder();
		$this->validateTooltipHelper();
	}

	public function run($jsFiles, $coreFiles) {
		if (!is_array($jsFiles) || empty($jsFiles)) {
			new Error($this->errors['jsFilesNotFound']);
		}
		if (!is_array($coreFiles) || empty($coreFiles)) {
			new Error($this->errors['coreFilesNotFound']);
		}
	
	}
	
	private function validateEntry() {
		$e = $this->config['entry'];
		if ($e === null) {
			new Error($this->errors['entryNotFound']);
		}
		if (!is_string($e)) {
			new Error($this->errors['entryNotString']);
		}
		if (preg_match('/[^\w]/', $e)) {
			new Error($this->errors['forbiddenEntrySymbols'], array($e));
		}
		if (!preg_match('/^[A-Z]\w*/', $e)) {
			new Error($this->errors['noPatternEntry'], array($e));
		}
	}

	private function validateJsFolder() {
		if (!is_string($this->config['folder'])) {
			new Error($this->errors['folderIsNotString']);
		}
		preg_match_all('/([^\w\-])/', $this->config['folder'], $matches);		
		if (!empty($matches[0])) {
			$symbols = array();
			foreach ($matches[1] as $s) {
				if (!in_array($s, $symbols)) {
					$symbols[] = $s;
				}
			}
			new Error($this->errors['folderNameIsInvalid'], array('&laquo;'.implode('&raquo;, &laquo;', $symbols).'&raquo;'));
		}
	}

	private function validateTooltipHelper() {
		$class = $this->config['tooltipClass'];
		$api = $this->config['tooltipApi'];
		if (isset($class) && $class !== null && $class !== false && !is_string($class)) {
			new Error($this->errors['tooltipClassNotString']);
		}
		if (isset($api) && !is_string($api)) {
			new Error($this->errors['tooltipApiNotString']);
		}
	}
}