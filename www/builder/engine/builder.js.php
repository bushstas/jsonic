<?php

class JSCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'folderIsNotString' => '�������� ��������� ������������ <b>jsFolder</b> �� �������� �������',
		'folderNameIsInvalid' => '�������� ��������� ������������ <b>jsFolder</b> �������� ����������� ������� {??}'
	);

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
	}
}