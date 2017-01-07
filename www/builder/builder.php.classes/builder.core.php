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
	'data', 'tags', 'props', 'events', 'template.parser', 'css.obfuscator', 'printer', 'js.interpreter',
	'splitter', 'template.code.parser', 'utils', 'controllers.parser', 'tag.classname.parser',
	'template.callback.validator', 'states.manager.parser', 'class.analyzer'
);
foreach ($includes as $inc) {
	include_once __DIR__.'/builder.'.$inc.'.php';	
}

class Builder 
{
	private $config, $gatherer, $testsCompiler,
			$cssCompiler, $jsCompiler, $htmlCompiler,
			$templateCompiler, $dataCompiler, $utilsCompiler;

	private $files;
	
	public function run() {
		$this->config = new Config();
		$this->config->init($this);

		$this->coreValidator = new CoreValidator();
		$this->coreValidator->validate($this->config->getPathToCore());
		
		CSSObfuscator::init();
		$this->cssCompiler = new CSSCompiler($this->config);
		$this->utilsCompiler = new UtilsCompiler($this->config);

		$this->routesCompiler = new RoutesCompiler($this->config);
		$this->routesCompiler->init();

		$this->jsCompiler = new JSCompiler($this->config);
		$this->templateCompiler = new TemplateCompiler();

		$this->textsCompiler = new TextsCompiler();
		$this->dataCompiler = new DataCompiler();
		$this->declCompiler = new DeclCompiler();
		if ($this->config->isTest()) {
			$this->testsCompiler = new TestsCompiler($this->config);
			$this->testsCompiler->init();
		}

		$this->gatherer = new Gatherer($this->config);
		$this->gatherer->init();		
		$this->files = $this->gatherer->gatherFiles();
		
		$this->jsCompiler->init($this->files['js'], $this->files['core'], $this->files['scripts'], $this->files['data'], $this->files['utils']);

		$this->htmlCompiler = new HTMLCompiler($this->config);
		$this->htmlCompiler->init();
		

		$this->coreValidator->validateUtilsFunction($this->files['core']);
		$this->runCompilers();
	}

	private function runCompilers() {
		ClassAnalyzer::run($this->files, $this->jsCompiler, $this->routesCompiler, $this->config);
		
		$this->htmlCompiler     -> run ();
		$this->cssCompiler      -> run ($this->files['css'], $this->files['cssconst']);
		$this->utilsCompiler    -> run ($this->files['utils'], $this->coreValidator->getUtilsFunctionNames());
		$this->textsCompiler    -> run ($this->files['texts']);
		$this->declCompiler     -> run ($this->files['decl']);
		$this->templateCompiler -> run ($this->files['template'], $this->files['include']);
		$this->jsCompiler       -> run ();
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
			case 'validator':
				return $this->coreValidator;
			case 'utils':
				return $this->utilsCompiler;
		}
	}
}
$builder = new Builder();
$builder->run();