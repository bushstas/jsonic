<?php

class JSCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'folderIsNotString' => '�������� ��������� ������������ <b>jsFolder</b> �� �������� �������',
		'folderNameIsInvalid' => '�������� ��������� ������������ <b>jsFolder</b> �������� ����������� ������� {??}',
		'tooltipClassNotString' => '�������� ������������ <b>tooltipClass</b> ������ ���� �������, ���������� �������� ������',
		'tooltipApiNotString' => '�������� ������������ <b>tooltipApi</b> ������ ���� �������, ���������� ���� � api ��� �������� ������ ���������'
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

		$this->validateTooltipHelper();
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