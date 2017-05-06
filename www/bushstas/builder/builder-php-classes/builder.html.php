<?php

class HTMLCompiler 
{
	private $configProvider, $config;
	private $defaultViewCode = '';
	private $defaultViewTemplateContent = '';

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
			'css' => $this->config['css'].'.css',
			'js' => $this->config['js'].'.js'
		);
		foreach ($replacements as $k => $v) {
			$blankIndexFileContent = str_replace('{$'.$k.'}', $v, $blankIndexFileContent);
		}
		FileManager::createFile(DEFAULT_PATH.$this->config['index'], $blankIndexFileContent);

		if ($this->configProvider->needToCreateEnvironment()) {
			$this->createEnvironment();
		}
	}

	private function createEnvironment() {
		$pathToBlanks = PATH_TO_BLANKS;
		if (file_exists($pathToBlanks.'/view.js')) {
			$this->defaultViewCode = file_get_contents($pathToBlanks.'/view.js');
		}
		if (file_exists($pathToBlanks.'/view.template')) {
			$this->defaultViewTemplateContent = file_get_contents($pathToBlanks.'/view.template');
		}
		
		$viewsFolder = $this->config['views'];
		if (empty($viewsFolder)) {
			$viewsFolder = 'views';
		}
		$pathToSrc = $this->configProvider->getPathToScope();
		$pathToViews = trim($pathToSrc, '/').'/'.$viewsFolder; 
		if (!is_dir($pathToViews)) {
			Gatherer::createDir($pathToViews);
		}
		$this->createViewDirs($this->configProvider->getRoutes(), $pathToViews);
		$this->createErrorViewDirs($this->configProvider->getErrorRoutes(), $pathToViews);
	}

	private	function createViewDirs($routes, $pathToViews) {		
		foreach ($routes as $route) {
			$this->createViewDir($pathToViews.'/'.$route['view'], $route['view']);
			if (is_array($route['children'])) {
				$this->createViewDirs($route['children'], $pathToViews);
			}			
		}
	}

	private	function createViewDir($dir, $viewName, $templateContent = '', $cssContent = '') {
		if (!is_dir($dir)) {
			Gatherer::createDir($dir);
		}
		$pathToViewFile = $dir.'/'.$viewName.'.js';
		if (!file_exists($pathToViewFile)) {
			Gatherer::createFile($pathToViewFile, 'view '.$viewName."\n\n".$this->defaultViewCode);
		}
		$pathToViewTemplate = $dir.'/'.$viewName.'.template';
		if (!file_exists($pathToViewTemplate)) {
			if (empty($templateContent)) {
				$templateContent = $this->defaultViewTemplateContent;
			}
			Gatherer::createFile($pathToViewTemplate, $templateContent);
		}
		if (!empty($cssContent)) {
			$pathToViewCss = $dir.'/'.$viewName.'.css';
			if (!file_exists($pathToViewCss)) {
				Gatherer::createFile($pathToViewCss, $cssContent);
			}
		}
	}

	private function createErrorViewDirs($errorViews, $pathToViews) {
		$pathToBlanks = PATH_TO_BLANKS;
		if (is_array($errorViews)) {
			foreach ($errorViews as $errorCode => $errorViewName) {
				$defaultTemplateContent = '';
				$defaultCssContent = '';
				$pathToBlankErrorViewTemplate = $pathToBlanks.'/error'.$errorCode.'.template';
				if (file_exists($pathToBlankErrorViewTemplate)) {
					$defaultTemplateContent = file_get_contents($pathToBlankErrorViewTemplate);
				}
				$pathToBlankErrorViewCss = $pathToBlanks.'/error'.$errorCode.'.css';
				if (file_exists($pathToBlankErrorViewCss)) {
					$defaultCssContent = file_get_contents($pathToBlankErrorViewCss);
				}
				$this->createViewDir($pathToViews.'/'.$errorViewName, $errorViewName, $defaultTemplateContent, $defaultCssContent);
			}
		}
	}
}