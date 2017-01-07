<?php

class TemplateCompiler 
{
	private $configProvider, $routesCompiler, $jsCompiler;
	private $inheritance = array();
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
	private $components = array();
	private $foundComponents = array();
	private $filesByClassNames = array();

	public function init($jsCompiler, $routesCompiler) {
		$this->jsCompiler = $jsCompiler;
		$this->routesCompiler = $routesCompiler;
	}

	public function run($templatesFiles, $includesFiles) {
		$jsClasses = $this->jsCompiler->getClasses();
		$jsInitials = $this->jsCompiler->getInitials();
		$this->inheritance = $this->jsCompiler->getClassInheritance();
		$views = $this->routesCompiler->getRouteViews();		

		$namespaces = array();
		foreach ($templatesFiles as $templateFile) {
			$this->filesByClassNames[$templateFile['name']] = $templateFile;
		}
		$namespaces = array_keys($this->filesByClassNames);
		

		foreach ($views as $viewClass) {
			$list = array();
			$this->parseUsedComponents($viewClass, $this->filesByClassNames[$viewClass]['content'], $list);
			$this->components[$viewClass] = array_values(array_unique($list));
		}
		$disabledRoutes = $this->routesCompiler->getDisabledRoutes();
		$components = array();
		foreach ($this->components as $viewClass => $list) {
			if (!in_array($viewClass, $disabledRoutes)) {
				$components = array_merge($components, $list);
				$components[] = $viewClass;
			}
		}
		$this->allUsedComponents = $components;

		if (is_array($templatesFiles)) {
			foreach ($templatesFiles as $templateFile) {
				$content = $templateFile['content'];
				$this->parseElementClasses($templateFile['name'], $content, $namespaces);
				$this->templates[$templateFile['name']] = $content;
				if (in_array($templateFile['name'], $components)) {
					$this->initUsedComponents($templateFile['name'], $content);
				}
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
		$diff1 = array_diff($namespaces, $this->allUsedComponents);
		$diff2 = array_diff($this->allUsedComponents, $namespaces);
		$notUsedClasses = array_merge($diff1, $diff2);


		$addedJsCode = '';
		foreach ($this->allUsedComponents as $className) {
			$jsCode .= $jsClasses[$className]['content'];
			$jsCode .= implode("\n", $jsInitials[$className]);
		}
		$stillNotUsed = array();
		$addedClasses = array();
		foreach ($notUsedClasses as $className) {
			if ($jsClasses[$className]['type'] == 'application' || preg_match('/\b'.$className.'\b/', $jsCode)) {
				$addedJsCode .= $jsClasses[$className]['content'];
				$addedClasses[] = $className;
			} else {
				$stillNotUsed[] = $className;
			}
		}		
	}

	public function getAllUsedComponentsList() {
		return $this->allUsedComponents;
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

	private function parseUsedComponents($className, $content, &$list) {		
		if (!isset($this->foundComponents[$className])) {
			$foundComponents[$className] = array();
			preg_match_all($this->regexp, $content, $matches);
			$tagContents = $matches[2];
			foreach ($tagContents as $i => $tagContent) {
				preg_match_all("/class=[\"'](\w+)[\"']/i", $tagContent, $matches);
				$name = $matches[1][0];
				if (empty($name)) continue;
				$foundComponents[$className][] = $name;
				$list[] = $name;
				if (is_array($this->filesByClassNames[$name])) {
					$this->parseUsedComponents($name, $this->filesByClassNames[$name]['content'], $list);
				}
				$this->parseSuperClass($name, $list);
			}
			$this->parseSuperClass($className, $list);
		} else {
			$list = array_merge($list, $this->foundComponents[$className]);
		}
	}

	private function parseSuperClass($className, &$list) {
		if (is_array($this->inheritance[$className])) {
			foreach ($this->inheritance[$className] as $superClass) {
				$list[] = $superClass;
				$this->parseUsedComponents($superClass, $this->filesByClassNames[$superClass]['content'], $list);
			}
		}
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