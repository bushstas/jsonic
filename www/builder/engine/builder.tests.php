<?php

class TestsCompiler 
{
	private $configProvider, $config;

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getTestsConfig();
	}
}