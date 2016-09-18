<?php

class TemplateCompiler 
{
	private $configProvider;

	private $errors = array(
		
	);
	private $templates = array();

	public function run($templatesFiles, $includesFiles) {
		if (is_array($templatesFiles)) {
			foreach ($templatesFiles as $templateFile) {
				$this->templates[$templateFile['name']] = preg_replace("/<\!--.*?-->/", '', $templateFile['content']);
			}
		}
	}

	public function hasTemplate($className) {
		return isset($this->templates[$className]);
	}
}