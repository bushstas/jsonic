<?php

define('CONFIG_FILENAME', 'config.json');
define('DEFAULT_PATH', '../');
define('DEFAULT_CSS_FOLDER', 'css');
define('DEFAULT_JS_FOLDER', 'js');
define('DEFAULT_CSS_COMPILED', 'styles');
define('DEFAULT_JS_COMPILED', 'base');
define('DEFAULT_SCOPE', './sources');
define('DEFAULT_ENTRY', 'App');
define('DEFAULT_PAGE', 'index.html');
define('PATH_TO_SOURCES', './core');
define('PATH_TO_BLANKS', './blanks');
define('DEFAULT_CONTAINER', 'app-view-container');
define('DEFAULT_PAGETITLE', 'Page title');
define('DEFAULT_CHARSET', 'windows-1251');

include_once 'engine/builder.error.php';
include_once 'engine/builder.config.php';
include_once 'engine/builder.gatherer.php';
include_once 'engine/builder.css.php';
include_once 'engine/builder.js.php';
include_once 'engine/builder.templates.php';
include_once 'engine/builder.html.php';


class Builder 
{
	private $config, $gatherer, $testsCompiler,
			$cssCompiler, $jsCompiler, $htmlCompiler,
			$templateCompiler;

	private $isTest = false, $files;
	
	public function run() {
		$this->isTest = !empty($_GET['istest']);

		$this->config = new Config();
		$this->config->init();
		
		$this->cssCompiler = new CSSCompiler($this->config);
		$this->cssCompiler->init();

		$this->jsCompiler = new JSCompiler($this->config);
		$this->jsCompiler->init();

		$this->templateCompiler = new TemplateCompiler();

		$this->htmlCompiler = new HTMLCompiler($this->config);
		$this->htmlCompiler->init();

		$this->gatherer = new Gatherer($this->config);
		$this->gatherer->init();
		
		if ($this->isTest) {
			$this->testsCompiler = new TestsCompiler($this->config);
			$this->testsCompiler->init();
		}


		$this->files = $this->gatherer->gatherFiles();
		
		$this->htmlCompiler->run();
		$this->cssCompiler->run($this->files);
		$this->templateCompiler->run($this->files);
	}
}
$builder = new Builder();
$builder->run();