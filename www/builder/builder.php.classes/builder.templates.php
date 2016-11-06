<?php

class TemplateCompiler 
{
	private $configProvider;
	private $regexp = '/<(component|control|form|menu)\s([^>]+)>/i';

	private $errors = array(
		'differentTypes' => 'Используются различные типы при вызове компонента {??}<br><br>В шаблоне класса {??} указан тип {??},<br><br>{?} в шаблоне класса {??} указан другой тип {??}',
		'includeNoTemplateNames' => "В файле {??} нет ни одного имени шаблона. Код должен иметь вид:<xmp>{template .checkbox}\n\t<div></div>\n{/template}</xmp>",
		'includeHasTemplateKeys' => "В файле {??} один или несколько шаблонов имеют оператор <b>as</b>, что недопустимо. Код должен иметь вид:<xmp>{template .checkbox}\n\t<div></div>\n{/template}</xmp>",
		'fewNamespaces' => 'Обнаружено более одной метки <b>namespace</b> в шаблоне класса {??}',
		'unknownNamespaces' => 'Обнаружена <b>namespace</b> метка {??} ссылающаяся на несуществующий класс в шаблоне класса {??}'
	);
	private $templates = array();
	private $includedTemlates = array();
	private $usedComponents = array();

	public function run($templatesFiles, $includesFiles) {		
		if (is_array($templatesFiles)) {
			$namespaces = array();
			foreach ($templatesFiles as $templateFile) {
				$namespaces[] = $templateFile['name'];
			}
			foreach ($templatesFiles as $templateFile) {
				$content = $templateFile['content'];
				$this->parseElementClasses($templateFile['name'], $content, $namespaces);
				$this->templates[$templateFile['name']] = preg_replace("/<\!--.*?-->/", '', $content);
				$this->initUsedComponents($templateFile['name'], $content);
			}
		}
		if (is_array($includesFiles)) {
			foreach ($includesFiles as $includesFile) {
				$content = preg_replace("/<\!--.*?-->/", '', $includesFile['content']);
				if (preg_match('/\{ *template +\.\w+ +as +\.\w+ *\}/', $content)) {
					new Error($this->errors['includeHasTemplateKeys'], $includesFile['filename']);
				}
				if (!preg_match('/\{ *template +\.\w+ *\}/', $content)) {
					new Error($this->errors['includeNoTemplateNames'], $includesFile['filename']);
				}
				$this->includedTemlates[$includesFile['filename']] = $content;
			}
		}
	}

	public function hasTemplate($className) {
		return isset($this->templates[$className]);
	}

	public function getTemplates() {
		return $this->templates;
	}

	public function getIncludes() {
		return $this->includedTemlates;
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

	private function parseElementClasses($className, &$content, $namespaces) {
		preg_match_all('/\{ *namespace +\.(\w+) *\}/', $content, $matches);
		if (!empty($matches[1])) {
			if (count($matches[1]) > 1) {
				new Error($this->errors['fewNamespaces'], array($className));
			}
			$namespace = $matches[1][0];
			if (!in_array($namespace, $namespaces)) {
				new Error($this->errors['unknownNamespaces'], array($namespace, $className));	
			}
			$className = $namespace;
			$content = preg_replace('/\{ *namespace +\.(\w+) *\}/', '', $content);
		}
		$data = Splitter::split('/[A-Z]/', $className);
		$className = '';
		foreach ($data['items'] as $i => $item) {
			$className .= $item.'-';
			if (isset($data['delimiters'][$i])) {
				$className .= strtolower($data['delimiters'][$i]);
			}
		}
		$className = trim($className, '-');
		$content = preg_replace('/class=["\']@(?=[\'" ])/', "class=\"".$className." ", $content);
		$content = preg_replace('/class=["\']@([\w\-]+)([^\'"\{]*)/', "class=\"".$className."_$1$2", $content);
	}
}