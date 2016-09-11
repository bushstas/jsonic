<?php

class RoutesCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'incorrectRouter' => '�������� ������������ <b>router</b> ����������� ��� �� �������� ��������',
		'' => ''
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getRoutesConfig();
		if (empty($this->config) || !is_array($this->config)) {
			new Error($this->errors['incorrectRouter']);
		}
	}


}