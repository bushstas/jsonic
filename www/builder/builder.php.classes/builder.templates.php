<?php

class TemplateCompiler 
{
	private $configProvider;
	private $regexp = '/<(component|control|form|menu)\s([^>]+)>/i';

	private $errors = array(
		'differentTypes' => 'Используются различные типы при вызове компонента {??}<br><br>В шаблоне класса {??} указан тип {??},<br><br>{?} в шаблоне класса {??} указан другой тип {??}'
	);
	private $templates = array();
	private $usedComponents = array();

	public function run($templatesFiles, $includesFiles) {
		if (is_array($templatesFiles)) {
			foreach ($templatesFiles as $templateFile) {
				$content = $templateFile['content'];
				$this->templates[$templateFile['name']] = preg_replace("/<\!--.*?-->/", '', $content);
				$this->initUsedComponents($templateFile['name'], $content);
			}
		}
	}

	public function hasTemplate($className) {
		return isset($this->templates[$className]);
	}

	public function getTemplateClasses() {
		return array_keys($this->templates);
	}

	public function getUsedComponents() {
		return $this->usedComponents;
	}

	private function initUsedComponents($className, $content) {
		$used = &$this->usedComponents;
		preg_match_all($this->regexp, $content, $matches);
		$typeMatches = $matches[1];
		$tagContents = $matches[2];
		foreach ($tagContents as $i => $tagContent) {
			preg_match_all("/class=[\"'](\w+)[\"']/i", $tagContent, $matches);
			$name = $matches[1][0];
			if (empty($name)) continue;
			if (!is_array($used[$name])) {
				$used[$name] = array('classNames' => array());
			}				
			if (!empty($used[$name]['type']) && $used[$name]['type'] != $typeMatches[$i]) {
				$but = $className == $used[$name]['classNames'][0] ? 'и здесь же' : 'а';
				new Error($this->errors['differentTypes'], array($name, $className, $typeMatches[$i], $but, $used[$name]['classNames'][0], $used[$name]['type']));
			}
			if (!in_array($className, $used[$name]['classNames'])) {
				$used[$name]['classNames'][] = $className;
			}
			$used[$name]['type'] = $typeMatches[$i];
		}
	}
}