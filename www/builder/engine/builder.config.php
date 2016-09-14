<?php

class Config 
{

	private $config, $pathToApiDir, $builder;
	private $errors = array(
		'noConfig' => 'Файл конфигурации {??} не найден. Данный файл должен располагаться в директории <b>builder</b>',
		'incorrectConfig' => 'Файл конфигурации {??} не корректен',
		'noBlanksDir' => 'Директория с шаблонами файлов {??}, указанная в параметре конфигурации <b>blanks</b> не найдена',
		'noIndexBlank' => 'Шаблон индексного файла <b>index.html</b> в директории {??} не найден',
		'invalidPathToApi' => 'Параметр конфигурации <b>pathToApi</b> существует, но пуст или не является строкой',
		'noApiDir' => 'Директория {??}, указанная в параметре конфигурации <b>pathToApi</b>, не найдена в корневом каталоге',
		'userLoginNotString' => "Параметр конфигурации <b>user['login']</b> должен быть строкой",
		'userLogoutNotString' => "Параметр конфигурации <b>user['logout']</b> должен быть строкой",
		'userSaveNotString' => "Параметр конфигурации <b>user['save']</b> должен быть строкой"
	);

	public function init($builder) {
		$this->builder = $builder;
		if (!file_exists(CONFIG_FILENAME)) {
			new Error($this->errors['noConfig'], array(CONFIG_FILENAME));
		}
		$configJson = file_get_contents(CONFIG_FILENAME);
		$this->config = json_decode($configJson, true);
		if (!is_array($this->config)) {
			new Error($this->errors['incorrectConfig'], array(CONFIG_FILENAME));
		}
		if (isset($this->config['pathToApi'])) {
			$this->pathToApiDir = $this->config['pathToApi'];
			if (empty($this->pathToApiDir) || !is_string($this->pathToApiDir)) {
				new Error($this->errors['invalidPathToApi']);
			}
			$this->pathToApiDir = '../'.$this->pathToApiDir;
			if (!is_dir($this->pathToApiDir)) {
				new Error($this->errors['noApiDir'], array($this->pathToApiDir));
			}
		}
		$this->validateUserConfig();
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
			'path' => trim($jsFolder, '/').'/'.$compiledJsFileName,
			'tooltipClass' => $this->config['tooltipClass'],
			'tooltipApi' => $this->config['tooltipApi']
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
			'js' => $jsConfig['path'],
			'views' => $jsConfig['views']
		);
	}

	public function getRoutesConfig() {
		return $this->config['router'];
	}

	public function getRoutes() {
		return $this->config['router']['routes'];
	}

	public function getErrorRoutes() {
		$compiler = $this->builder->getCompiler('routes');
		return $compiler->getErrorRoutes();
	}

	public function getTestsConfig() {
		return $this->config['tests'];
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

	public function needToCreateEnvironment() {
		return !empty($_GET['create']);
	}

	private function validateUserConfig() {
		$user = $this->config['user'];
		$isUser = is_array($user);
		if ($isUser) {
			if (isset($user['login']) && !is_string($user['login'])) {
				new Error($this->errors['userLoginNotString']);
			}
			if (isset($user['logout']) && !is_string($user['logout'])) {
				new Error($this->errors['userLogoutNotString']);
			}
			if (isset($user['save']) && !is_string($user['save'])) {
				new Error($this->errors['userSaveNotString']);
			}
		}
	}
}
