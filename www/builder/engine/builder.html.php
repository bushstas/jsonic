<?php

class HTMLCompiler 
{
	private $configProvider, $config;

	private $errors = array(
		'emptyIndexBlank' => 'Шаблон индексного файла <b>index.html</b> в директории {??} пуст'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getHtmlConfig();
	}

	public function run() {
		$blankIndexFileContent = file_get_contents($this->config['blank']);
		if (empty($blankIndexFileContent)) {
			new Error($this->errors['emptyIndexBlank'], array($this->config['dir']));
		}

		$replacements = array(
			'charset' => $this->config['charset'],
			'title' => $this->config['title'],
			'css' => $this->config['css'],
			'js' => $this->config['js']
		);
		foreach ($replacements as $k => $v) {
			$blankIndexFileContent = str_replace('{$'.$k.'}', $v, $blankIndexFileContent);
		}
		Gatherer::createFile(DEFAULT_PATH.$this->config['index'], $blankIndexFileContent);
	}
}