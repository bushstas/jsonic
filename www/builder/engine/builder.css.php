<?php

class CSSCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'folderIsNotString' => '�������� ��������� ������������ <b>cssFolder</b> �� �������� �������',
		'folderNameIsInvalid' => '�������� ��������� ������������ <b>cssFolder</b> �������� ����������� ������� {??}'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getCssConfig();

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

	public function run() {
		
	}
}