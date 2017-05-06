<?php

class TemplateCompiler 
{
	private $configProvider;

	private $errors = array(
		'includeNoTemplateNames' => "В файле {??} нет ни одного имени шаблона. Код должен иметь вид:<xmp>{template .checkbox}\n\t<div></div>\n{/template}</xmp>",
		'includeHasTemplateKeys' => "В файле {??} один или несколько шаблонов имеют оператор <b>as</b>, что недопустимо. Код должен иметь вид:<xmp>{template .checkbox}\n\t<div></div>\n{/template}</xmp>",
		'fewNamespaces' => 'Обнаружено более одной метки <b>namespace</b> в шаблоне класса {??}',
		'unknownNamespaces' => 'Обнаружена <b>namespace</b> метка {??} ссылающаяся на несуществующий класс в шаблоне класса {??}',
		'doubleInclTmp' => "Обнаружено повторяющееся имя include шаблона {??} в файлах<xmp>{?}\n{?}</xmp>",
		'doubleInclTmp2' => 'Обнаружено повторяющееся имя include шаблона {??} в файле<xmp>{?}</xmp>'
	);
	private $templates = array();
	private $includedTemlates = array();
	private $includedTemlateNames = array();
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
			}
		}
		$includeSources = array();
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
				preg_match_all('/\{\s*template\s+\.(\w+)\s*\}/', $content, $matches);
				$matches = $matches[1];
				foreach ($matches as $inclTmpName) {
					if (in_array($inclTmpName, $this->includedTemlateNames)) {
						if (isset($includeSources[$inclTmpName]) && $includeSources[$inclTmpName] != $includesFile['path']) {
							new Error($this->errors['doubleInclTmp'], array($inclTmpName, $includeSources[$inclTmpName], $includesFile['path']));
						} else {
							new Error($this->errors['doubleInclTmp2'], array($inclTmpName, $includesFile['path']));
						}
					}
					$includeSources[$inclTmpName] = $includesFile['path'];
					$this->includedTemlateNames[] = $inclTmpName;
				}
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

	public function getIncludesNames() {
		return $this->includedTemlateNames;
	}

	public function getTemplateClasses() {
		return array_keys($this->templates);
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