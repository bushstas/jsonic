<?php

class Config 
{

	private $config, $pathToApiDir, $builder, $configJson, $isUser;
	private $errors = array(
		'noConfig' => '���� ������������ {??} �� ������. ������ ���� ������ ������������� � ���������� <b>builder</b>',
		'incorrectConfig' => '���� ������������ {??} �� ���������',
		'noBlanksDir' => '���������� � ��������� ������ {??}, ��������� � ��������� ������������ <b>blanks</b> �� �������',
		'noIndexBlank' => '������ ���������� ����� <b>index.html</b> � ���������� {??} �� ������',
		'invalidPathToApi' => '�������� ������������ <b>pathToApi</b> ����������, �� ���� ��� �� �������� �������',
		'noApiDir' => '���������� {??}, ��������� � ��������� ������������ <b>pathToApi</b>, �� ������� � �������� ��������',
		'userLoginNotString' => "�������� ������������ <b>user['login']</b> ������ ���� �������",
		'userLogoutNotString' => "�������� ������������ <b>user['logout']</b> ������ ���� �������",
		'userSaveNotString' => "�������� ������������ <b>user['save']</b> ������ ���� �������",
		'userLoginEmpty1' => "�������� ������������ <b>user['login']</b> �� ������, ����� ��� <b>user['logout']</b> �����",
		'userLoginEmpty2' => "�������� ������������ <b>user['login']</b> �� ������, ����� ��� <b>user['save']</b> �����"
	);

	public function init($builder) {
		$this->builder = $builder;
		if (!file_exists(CONFIG_FILENAME)) {
			new Error($this->errors['noConfig'], array(CONFIG_FILENAME));
		}
		$this->configJson = file_get_contents(CONFIG_FILENAME);
		$this->config = json_decode($this->configJson, true);
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

	public function getBuilder() {
		return $this->builder;
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
			'entry' => $this->config['entry'],
			'folder' => $jsFolder,
			'file' => $compiledJsFileName,
			'path' => trim($jsFolder, '/').'/'.$compiledJsFileName,
			'tooltipClass' => $this->config['tooltipClass'],
			'tooltipApi' => $this->config['tooltipApi'],
			'pathToCore' => $this->getPathToCore(),
			'router' => $this->getRoutesConfig(),
			'routerMenu' => $this->getRouterMenu(),
			'routerControllers' => $this->getRouterControllers(),
			'pathToDictionary' => $this->config['pathToDictionary'],
			'viewContainer' => $this->config['container'],
			'pathToApi' => $this->config['pathToApi'],
			'pagetitle' => $this->config['title'],
			'user' => $this->config['user'],
			'hasUser' => $this->hasUser()
		);
	}

	public function getEntry() {
		return $this->config['entry'];
	}

	public function getTooltipClass() {
		return $this->config['tooltipClass'];
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

	public function getRouterMenu() {
		return $this->builder->getCompiler('routes')->getMenu();
	}

	public function getRouterControllers() {
		return $this->builder->getCompiler('routes')->getControllers();
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
		$pathToCore = $this->config['core'];
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

	public function needJsObfuscation() {
		return !empty($_GET['js_obfuscate']);
	}

	public function needToCreateEnvironment() {
		return !empty($_GET['create']);
	}

	public function isAdvancedMode() {
		return !empty($_GET['advanced']);
	}

	public function isTest() {
		return !empty($_GET['istest']);
	}

	public function getConfigJson() {
		return $this->configJson;
	}

	public function hasUser() {
		return $this->isUser;
	}

	private function validateUserConfig() {
		$user = $this->config['user'];
		$this->isUser = false;
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
			if (!empty($user['logout']) && empty($user['login'])) {
				new Error($this->errors['userLoginEmpty1']);
			}
			if (!empty($user['save']) && empty($user['login'])) {
				new Error($this->errors['userLoginEmpty2']);
			}
			$this->isUser = !empty($user['login']);
		}
	}
}