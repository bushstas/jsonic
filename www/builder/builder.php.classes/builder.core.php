<?php

define('CONFIG_FILENAME', 'config.json');
define('DEFAULT_PATH', '../');
define('DEFAULT_CSS_FOLDER', 'css');
define('DEFAULT_JS_FOLDER', 'js');
define('DEFAULT_CSS_COMPILED', 'styles');
define('DEFAULT_JS_COMPILED', 'base');
define('DEFAULT_SCOPE', './sources');
define('DEFAULT_PAGE', 'index.html');
define('PATH_TO_SOURCES', './core');
define('PATH_TO_BLANKS', './blanks');
define('DEFAULT_CONTAINER', 'app-view-container');
define('DEFAULT_PAGETITLE', 'Page title');
define('DEFAULT_CHARSET', 'windows-1251');

$includes = array(
	'error', 'core.validator', 'config', 'gatherer', 'css', 'js', 'templates', 'html', 'routes',
	'tests', 'texts', 'decl', 'text.parser', 'initials', 'js.parser', 'js.checker', 'js.globals',
	'data', 'tags', 'props', 'events'
);
foreach ($includes as $inc) {
	include_once __DIR__.'/builder.'.$inc.'.php';	
}

class Builder 
{
	private $config, $gatherer, $testsCompiler,
			$cssCompiler, $jsCompiler, $htmlCompiler,
			$templateCompiler, $dataCompiler;

	private $files;
	
	public function run() {		
		$this->config = new Config();
		$this->config->init($this);

		$this->coreValidator = new CoreValidator();
		$this->coreValidator->validate($this->config->getPathToCore());
		
		$this->cssCompiler = new CSSCompiler($this->config);		

		$this->routesCompiler = new RoutesCompiler($this->config);
		$this->routesCompiler->init();

		$this->templateCompiler = new TemplateCompiler();
		$this->textsCompiler = new TextsCompiler();
		$this->dataCompiler = new DataCompiler();
		$this->declCompiler = new DeclCompiler();

		$this->jsCompiler = new JSCompiler($this->config);
		$this->jsCompiler->init();

		$this->htmlCompiler = new HTMLCompiler($this->config);
		$this->htmlCompiler->init();
		
		$this->gatherer = new Gatherer($this->config);
		$this->gatherer->init();
		
		if ($this->config->isTest()) {
			$this->testsCompiler = new TestsCompiler($this->config);
			$this->testsCompiler->init();
		}
		$this->files = $this->gatherer->gatherFiles();
		$this->runCompilers();
	}

	private function runCompilers() {
		$this->htmlCompiler     -> run ();
		$this->cssCompiler      -> run ($this->files['css'], $this->files['cssconst']);
		$this->textsCompiler    -> run ($this->files['texts']);
		$this->dataCompiler     -> run ($this->files['data']);
		$this->declCompiler     -> run ($this->files['decl']);
		$this->templateCompiler -> run ($this->files['template'], $this->files['include']);
		$this->jsCompiler       -> run ($this->files['js'], $this->files['core']);
	}

	public function getCompiler($compilerName) {
		switch ($compilerName) {
			case 'css':
				return $this->cssCompiler;
			case 'js':
				return $this->jsCompiler;
			case 'template':
				return $this->templateCompiler;
			case 'html':
				return $this->htmlCompiler;
			case 'routes':
				return $this->routesCompiler;
			case 'tests':
				return $this->testsCompiler;
			case 'texts':
				return $this->textsCompiler;
			case 'data':
				return $this->dataCompiler;
			case 'decl':
				return $this->declCompiler;
		}
	}
}
$builder = new Builder();
$builder->run();