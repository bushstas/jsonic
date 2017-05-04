<?php

class Config 
{

	private $config, $pathToApiDir, $builder, $configJson, $isUser;
	private $LOAD_APP = 'loadApp';
	private $errors = array(
		'noConfig' => 'Файл конфигурации {??} не найден. Данный файл должен располагаться в директории <b>builder</b>',
		'incorrectConfig' => 'Файл конфигурации {??} не корректен',
		'noIndexBlank' => 'Шаблон индексного файла <b>index.html</b> в директории {??} не найден',
		'invalidPathToApi' => 'Параметр конфигурации <b>pathToApi</b> существует, но пуст или не является строкой',
		'noApiDir' => 'Директория {??}, указанная в параметре конфигурации <b>pathToApi</b>, не найдена в корневом каталоге',
		'userLoginNotString' => "Параметр конфигурации <b>user['login']</b> должен быть строкой",
		'userLogoutNotString' => "Параметр конфигурации <b>user['logout']</b> должен быть строкой",
		'userSaveNotString' => "Параметр конфигурации <b>user['save']</b> должен быть строкой",
		'userLoginEmpty1' => "Параметр конфигурации <b>user['login']</b> не найден, тогда как <b>user['logout']</b> задан",
		'userLoginEmpty2' => "Параметр конфигурации <b>user['login']</b> не найден, тогда как <b>user['save']</b> задан",
		'coreNotFound' => "Директория {??} не найдена в папке <b>builder</b>",
		'incorrectValue' => "Некорректоное значение параметра конфигурации {??}<br><br> Текущее значение: {??}<br><br>Корректное значение должно содержать имя директории и соответствовать паттерну <b>^[\w-]+$</b>",
		'folderNotFound' => "Директория {??}, указанная в параметре конфигурации {??} не найдена<br><br>Она должна располагаться непосредственно в папке <b>builder</b>",
		'folderNotFound2' => "Директория {??}, указанная в параметре конфигурации {??} не найдена<br><br>Она должна располагаться непосредственно в корневом каталоге",
		'defaultFolderNotFound' => "Параметр конфигурации {??} не заполнен, директория по умолчанию {??} также не найдена",
		'noDictionaryApi' => "Файл для загрузки словарей {??}, указанный в параметре конфигурации <b>pathToDictionary</b> не найден"
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
		if (!empty($this->config['pathToDictionary'])) {
			$this->pathToDictionary = $this->pathToApiDir.'/'.$this->config['pathToDictionary'];
			if (!file_exists($this->pathToDictionary)) {
				new Error($this->errors['noDictionaryApi'], array($this->pathToDictionary));	
			}
		}
		$this->validatePathNames();
		$this->validateUserConfig();

		define('CONST_JSBASE', $this->config['compiledJs']);
	}

	public function createAppLoader() {
		if (!empty($this->config['singleLoad'])) {
			$this->createLoadAppApi();
		}
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

	public function getPathToScope() {
		return $this->config['scope'];
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

		$pathToBlanks = PATH_TO_BLANKS;
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

	public function get($key) {
		return $this->config[$key];
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
		return PATH_TO_SOURCES;
	}

	public function getPathToTests() {
		return $this->config['tests'];
	}

	public function getFontsFolder() {
		return $this->config['fontsFolder'];
	}

	public function getPathToScripts() {
		return $this->config['scripts'];
	}

	public function needCssObfuscation() {
		return !empty($_REQUEST['obfuscate']);
	}

	public function needJsObfuscation() {
		return !empty($_REQUEST['js_obfuscate']);
	}

	public function isSplitMode() {
		return !empty($_REQUEST['split']);
	}

	public function needToCreateEnvironment() {
		return !empty($_REQUEST['create']);
	}

	public function isAdvancedMode() {
		return !empty($_REQUEST['advanced']);
	}

	public function isTest() {
		return !empty($_REQUEST['istest']);
	}

	public function getConfigJson() {
		return $this->configJson;
	}

	public function hasUser() {
		return $this->isUser;
	}

	private function validatePathNames() {
		$regexp = '/^[\w\-]+$/';
		if (!is_dir(PATH_TO_SOURCES)) {
			new Error($this->errors['coreNotFound'], array(PATH_TO_SOURCES));
		}
		if (!is_dir(PATH_TO_BLANKS)) {
			new Error($this->errors['coreNotFound'], array(PATH_TO_BLANKS));
		}
		$folders = array(
			'scope', 'tests', 'imagesFolder', 'fontsFolder'
		);
		$paths = array(
			'imagesFolder' => '../',
			'fontsFolder' => '../'
		);
		foreach ($folders as $k) {
			$path = $this->config[$k];
			if (!empty($path)) {
				if (!preg_match($regexp, $path)) {
					new Error($this->errors['incorrectValue'], array($k, $path));
				}
				if (!is_dir($paths[$k].$path)) {
					if (empty($paths[$k])) {
						new Error($this->errors['folderNotFound'], array($path, $k));
					}
					new Error($this->errors['folderNotFound2'], array($path, $k));
				}
			} else {
				if (!is_dir($paths[$k].$v)) {
					new Error($this->errors['defaultFolderNotFound'], array($k, $v));
				}
			}
		}
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

	private function createLoadAppApi() {
		$content = "<?php \n\n";
		$path = (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/'.$this->config['pathToApi'].'/';
		$params = array();
		if ($this->isUser) {
			$user = $this->config['user'];			
		
			$content .= '$opts = array(\'http\' => array(\'header\'=> \'Cookie: \' . $_SERVER[\'HTTP_COOKIE\']."\r\n"));'."\n".'$context = stream_context_create($opts);'."\n".'$user = file_get_contents(\''.$path.$user['login'].'\', false, $context);'."\n\ndie('{";

			$params[] = '"user": \'.$user.\'';
		}
		if (!empty($this->pathToDictionary)) {
			$params[] = '"dictionary":\'.file_get_contents(\''.$path.$this->config['pathToDictionary'].'?page=\'.$_GET[\'page\']).\'';	
		}
		if (!empty($this->config['loadData'])) {
			extract(JSGlobals::getDataForLoader());
			extract($textConstants);

			$textConstants = array();
			foreach ($index as $i => $value) {
				$textConstants[$value] = $texts[$i];
			}					
			$params[] = '"texts":'.preg_replace("/\\\u00A0/", "u00A0", preg_replace("/\\\'/", "'", json_encode($textNodes)));
			$params[] = '"textsConstants":'.json_encode($texts);
		}
		$content .= implode(', ', $params)."}'); ?>";
		file_put_contents($this->pathToApiDir.'/'.$this->LOAD_APP.'.php', $content);
	}

	public function isUsingDataLoader() {
		$a = $this->get('singleLoad');
		$b = $this->get('loadData');
		return !empty($a) && !empty($b);
	}

	public function getPathToLoadAppApi() {
		return $this->pathToApiDir.'/'.$this->LOAD_APP.'.php';
	}
} 
