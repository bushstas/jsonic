<?php

class Config 
{

	private $config = null;
	private $errors = array(
		'noConfig' => 'Файл конфигурации {??} не найден. Данный файл должен располагаться в директории <b>builder</b>',
		'incorrectConfig' => 'Файл конфигурации {??} не корректен',
		'noBlanksDir' => 'Директория с шаблонами файлов {??}, указанная в параметре конфигурации <b>blanks</b> не найдена',
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
		$imagesFolder = $this->config['imagesFolder'];
		$compiledCssFileName = $this->config['compiledCss'];
		if (empty($cssFolder)) {
			$cssFolder = DEFAULT_CSS_FOLDER;
		}
		if (empty($compiledCssFileName)) {
			$compiledCssFileName = DEFAULT_CSS_COMPILED;
		}
		return array(
			'folder' => $cssFolder,
			'images' => $imagesFolder,
			'file' => $compiledCssFileName,
			'path' => trim($cssFolder, '/').'/'.$compiledCssFileName
		);
	}

	public function getJsConfig() {
		$jsFolder = $this->config['jsFolder'];
		$compiledJsFileName = $this->config['compiledJs'];
		if (empty($jsFolder)) {
			$jsFolder = DEFAULT_JS_FOLDER;
		}
		if (empty($compiledJsFileName)) {
			$compiledJsFileName = DEFAULT_JS_COMPILED;
		}
		return array(
			'folder' => $jsFolder,
			'file' => $compiledJsFileName,
			'path' => trim($jsFolder, '/').'/'.$compiledJsFileName
		);
	}

	public function getHtmlConfig() {
		$pathToIndexFile = $this->config['indexPage'];
		if (empty($pathToIndexFile)) {
			$pathToIndexFile = DEFAULT_PAGE;
		}

		$pathToBlanks = $this->getPathToBlanks();
		if (!is_dir($pathToBlanks)) {
			new Error($this->errors['noBlanksDir'], array($pathToBlanks));
		}
		$pathToBlankIndexFile = rtrim($pathToBlanks, '/').'/index.html';		
		if (!file_exists($pathToBlankIndexFile)) {
			new Error($this->errors['noIndexBlank'], array($pathToBlanks));
		}
		$charset = $this->config['charset'];
		if (empty($charset)) {
			$charset = DEFAULT_CHARSET;
		}
		$pageTitle = $this->config['title'];
		if (empty($pageTitle)) {
			$pageTitle = DEFAULT_PAGETITLE;
		}
		$cssConfig = $this->getCssConfig();
		$jsConfig = $this->getJsConfig();
		return array(
			'index' => $pathToIndexFile,
			'dir' => $pathToBlanks,
			'blank' => $pathToBlankIndexFile,
			'charset' => $charset,
			'title' => $pageTitle,
			'css' => $cssConfig['path'],
			'js' => $jsConfig['path']
		);
	}

	public function getTestsConfig() {
		
	}

	public function getPathToCore() {
		$pathToCore = $this->config['sources'];
		if (empty($pathToCore)) {
			$pathToCore = PATH_TO_SOURCES;
		}
		return $pathToCore;
	}

	public function getPathToScope() {
		$pathToSrc = $this->config['scope'];
		if (empty($pathToSrc)) {
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
			$pathToBlanks = PATH_TO_BLANKS;
		}
		return $pathToBlanks;
	}

	public function needCssObfuscation() {
		return !empty($_GET['obfuscate']);
	}
}
