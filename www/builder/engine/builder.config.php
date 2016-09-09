<?php

class Config 
{

	private $config = null;
	private $errors = array(
		'noConfig' => 'Файл конфигурации {??} не найден. Данный файл должен располагаться в директории <b>builder</b>',
		'incorrectConfig' => 'Файл конфигурации {??} не корректен',
		'noBlanksDir' => 'Директория с шаблонами файлов {??} не найдена',
		'noIndexBlank' => 'Шаблон индексного файла <b>index.html</b> в директории {??} не найден'
	);

	public function init() {
		if (!file_exists(CONFIG_FILENAME)) {
			new Error($this->errors['noConfig'], array(CONFIG_FILENAME));
		}
		$configJson = file_get_contents(CONFIG_FILENAME);
		$this->config = json_decode($configJson, true);
		if (!is_array($this->config)) {
			new Error($this->errors['incorrectConfig'], array(CONFIG_FILENAME));
		}
		
		$pathToBlanks = $this->getPathToBlanks();
		if (!is_dir($pathToBlanks)) {
			new Error($this->errors['noBlanksDir'], array($pathToBlanks));	
		}
	}

	public function getGathererConfig() {
		return array(
			'pathToSrc' => $this->getPathToScope(),
			'pathToCore' => $this->getPathToCore(),
			'pathToTests' => $this->getPathToTests(),
			'pathToScripts' => $this->getPathToScripts()
		);
	}

	public function getCssConfig() {
		$cssFolder = $this->config['cssFolder'];
		$compiledCssFileName = $this->config['compiledCss'];
		if (empty($cssFolder)) {
			$cssFolder = DEFAULT_CSS_FOLDER;
		}
		if (empty($compiledCssFileName)) {
			$compiledCssFileName = DEFAULT_CSS_COMPILED;
		}
		return array(
			'folder' => $cssFolder,
			'file' => $compiledCssFileName
		);
	}

	public function getTestsConfig() {
		
	}

	public function getPathToCore() {
		$pathToCore = $this->config['sources'];
		if (empty($pathToCore)) {
			print("Параметр конфигурации <b>sources</b> не найден. Использованы настройки по умолчанию.<br>");
			$pathToCore = PATH_TO_SOURCES;
		}
		return $pathToCore;
	}

	public function getPathToScope() {
		$pathToSrc = $this->config['scope'];
		if (empty($pathToSrc)) {
			print("Параметр конфигурации <b>scope</b> не найден. Использованы настройки по умолчанию.<br>");
			$pathToSrc = DEFAULT_SCOPE;
		}
		return $pathToSrc;
	}

	public function getPathToTests() {
		return $this->config['tests'];
	}

	public function getPathToScripts() {
		return $this->config['scripts'];
	}

	public function getPathToBlanks() {
		$pathToBlanks = $this->config['blanks'];
		if (empty($pathToBlanks)) {
			print("Параметр конфигурации <b>blanks</b> не найден. Использованы настройки по умолчанию.<br>");
			$pathToBlanks = PATH_TO_BLANKS;
		}
		return $pathToBlanks;
	}
}
