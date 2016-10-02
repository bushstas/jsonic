<?php

class JSCompiler 
{
	private $configProvider, $config, $apiConfig;

	private $errors = array(
		'entryNotFound' => '�������� ������������ <b>entry</b>, ������������ ����� ����� �����, �� ������',
		'entryNotString' => '�������� ��������� ������������ <b>entry</b> �� �������� �������',
		'forbiddenEntrySymbols' => "�������� ������������ entry = '{??}' �������� ����������� �������",
		'noPatternEntry' => "�������� ������������ entry = '{??}' �� ������������� �������� <b>[A-Z]\w+</b>",
		'folderIsNotString' => '�������� ��������� ������������ <b>jsFolder</b> �� �������� �������',
		'folderNameIsInvalid' => '�������� ��������� ������������ <b>jsFolder</b> �������� ����������� ������� {??}',
		'tooltipClassNotString' => '�������� ������������ <b>tooltipClass</b> ������ ���� �������, ���������� �������� ������',
		'tooltipApiNotString' => '�������� ������������ <b>tooltipApi</b> ������ ���� �������, ���������� ���� � api ��� �������� ������ ���������',
		'jsFilesNotFound' => 'JS ����� ��� ���������� ����������, ����������� ������ ���������� ��������� � ��������� ������������ <b>scope</b>, �� �������',
		'coreFilesNotFound' => 'JS ����� ���� ����������, ����������� ������ ���������� ��������� � ��������� ������������ <b>so</b>, �� �������',
		'incorrectConfig' => "���� ������������ ����� � api <b>config.js</b> ������ ����� ��� <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>",
		'configExists' => '���������� ��������� ������ <b>config.js</b>',
		'emptyFile' => '���� {??} ����',
		'cyrSymbols' => '������������ ������������� ������� � ������ ������ ����� {??}',
		'incorrectKeyword' => '������������ ������� � �������� ����� {??} ������������ ��� ������ � ����� {??}',
		'incorrectDefinition' => '����������� ���������� ����������� ������ {??} � ����� {??}',
		'unknownClassType' => '����������� ��� ������ {??} � ����� {??}.<br>���������� ��������: {?}',
		'incorrectClassName' => '�������� ������ {??} �����������. ����������� ������ ���� <b>ClassName</b>',
		'extendsExpected' => '������������ �������� ����� {??} � ������ ������ ����� {??}. ��������� �������� ����� <b>extends</b>',
		'extendsEmpty' => '�����-������ �� ������� ����� ��������� ����� <b>extends</b> � ������ ������ ����� {??}. ��������� ��� ������ ��� ����� ������� ����� �������',
		'incorrectFirstLine' => '������������ ����������� ������ {??} � ����� {??}',
		'differentNames' => '���� {??} ������ ��������� ����� {??}, ����� ��� �������� ����� � ������ {??}',
		'incorrectSuper' => '�������� �����-������ {??} ��� {??} �����������. ����������� ������ ���� <b>ClassName</b>',
		'classExists' => '������� ��������� ������� � ���������� ������ {??}',
		'appNotFound' => '����� � ����� <b>application</b> �� ������',
		'fewAppClasses' => '������� ��������� ������� � ����� <b>application</b>',
		'appExtends' => '����� {??} ����� ��� <b>application</b> � �� ����� ����������� ������� ��������',
		'viewNotFound' => '����� {??} � ����� <b>view</b> ���������� � ��������� ������������ routes �� ������',
		'404NotFound' => '����� {??} � ����� <b>view</b>, ��������� ��� ��������� ������ 404, �� ������',
		'superClassNotFound' => '������������ � �������� �����-������ ��� {??}, ����� {??} �� ������',
		'incorrectSuperClass' => '����� {??} �� ����� ���� ����������� �� ������ {??}. ��� ������ ���� ����������� ����',
		'usedClassNotFound' => '����� {??}, ���������� � ������� �����{?} {??}, �� ������',
		'usedClassNotFound2' => '����� {??} �� ������',
		'duplicateMethod' => '���������� ����� ������ ������ � ������ {??} � ������ {??}',
		'noSuperClasses' => '{?}� ������� ������ ����������� �����-������',
		'noSuperClassMethod' => '{?}������ ����� �� ������ � �����-�������',
		'fewSuperMethods' => '{?}� ������� ������ ���� ��������� �����-������� � ������ �������. ����������� ������ <b>super(ClassName)</b>',
		'noSuperClass' => '{?}�����-����� {??} �� ������',
		'noThisSuperClassMethod' => '{?}����� {??} ����������� � �����-������ {??}',
		'nameReserved' => '�������� ������ {??} ��������������� ��������',
		'noController' => '���������� {??} ���������� � ������ {??} �� ������',
		'noHelper' => '������ {??} ���������� � ������ {??} �� ������',
		'noHelperSubscribe' => '� ������� {??} ����������� � ������ {??} ����������� ����� <b>subscribe</b>',
		'noDialog' => '���������� ���� {??} ���������� � ������ {??} �� �������',
		'actionNotFound' => '������� {??} ���������� � initial ��������� <b>controllers</b> ������ {??} �� ������� � initial ��������� <b>actions</b> ����������� {??}',
		'noRouterMenuClass' => "����� {??}, ��������� � ��������� ������������ <b>router['menu']</b>, �� ������",
		'incorrectRouterMenuClass' => "����� {??}, ��������� � ��������� ������������ <b>router['menu']</b>, ������ ����� ��� <b>menu</b>",
		'diffClassType' => '����� {??} ����� ��� {??}, ������ ���������� � ����� {??} � ������� ������ {??}',
		'dialogCalling' => '������������ ������� ������� ��������� � ����� <b>dialog</b> �� ������� � ������ {??}<br><br>��� ������� ��������� ����������� ��� ����<xmp>Dialoger.show(CommentsDialog, options)</xmp>� ��������� ������ ����������� ������ �������� � �������� id ���������<xmp>Dialoger.show(ItemDialog, options, itemId)</xmp>',
		'noRouteController' => '���������� {??} ���������� � ������������ ������� �� ������',
		'noTooltipClass' => '����� {??} ��������� � ��������� ������������ <b>tooltipClass</b> �� ������'
	);

	private $coreClasses = array(
		'Component', 'Controller', 'Application', 'View', 'Dialog', 'Menu', 'Control'
	);

	private $reservedNames = array(
		'Component', 'Controller', 'Application', 'View', 'Level',
		'Control', 'AjaxRequest', 'Router', 'Objects', 'Corrector',
		'Condition', 'Core', 'Menu', 'EventHandler', 'Dialoger', 'Foreach',
		'Globals', 'User', 'StoreKeeper', 'Switch', 'Tooltiper', 'IfSwitch',
		'__', '__T', '__ROUTES', '__TAGS', '__A', '__EVENTTYPES', '__HASHROUTER', '__DEFAULTROUTE', '__ERRORROUTES',
		'__VIEWCONTAINER', '__USEROPTIONS', '__D', '__V', '__DW', '__CRRS'
	);

	private $classTypes = array(
		'application', 'view', 'component', 'controller', 'dialog', 'form', 'control', 'menu', 'corrector'
	);

	private $superClasses = array('component', 'dialog', 'form', 'control', 'menu');
	private $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	private $classes = array();
	private $classesByTypes = array();
	private $sources = array();
	private $correctors = array();
	private $helpers = array();
	private $JSFileNames = array();
	private $jsCode = '';
	private $jsOutput = '';
	private $initialsParser;
	private $templateCompiler;
	private $textsCompiler;
	private $dataCompiler;
	private $declCompiler;
	private $routesCompiler;
	private $usedComponents;
	private $usedComponentsNames;


	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
		$this->initialsParser = new InitialsParser();
	}

	public function init() {
		$this->config = $this->configProvider->getJsConfig();
		$this->templateCompiler = $this->configProvider->getBuilder()->getCompiler('template');
		$this->textsCompiler = $this->configProvider->getBuilder()->getCompiler('texts');
		$this->dataCompiler = $this->configProvider->getBuilder()->getCompiler('data');
		$this->declCompiler = $this->configProvider->getBuilder()->getCompiler('decl');
		$this->routesCompiler = $this->configProvider->getBuilder()->getCompiler('routes');

		$this->validateEntry();
		$this->validateJsFolder();
		$this->validateTooltipHelper();
	}

	public function run($jsFiles, $coreFiles) {
		if (!is_array($jsFiles) || empty($jsFiles)) {
			new Error($this->errors['jsFilesNotFound']);
		}
		foreach ($jsFiles as $jsFile) {
			$this->processJSFile($jsFile);
		}
		$this->initCore($coreFiles);
		$this->validateApplication();
		$this->validateViews();
		$this->validateSuperClasses();
		$this->unsetNotUsedClasses();
		$this->addClassesFromTemplates();
		$this->validateUsedClasses();
		$this->initialsParser->run($this->classes);
		$this->parseClasses();
		$this->checkClasses();
		$this->addGlobals();
	}

	private function validateApplication() {
		if (empty($this->classesByTypes['application'])) {
			new Error($this->errors['appNotFound']);
		}
		$appClasses = array_keys($this->classesByTypes['application']);
		if (count($appClasses) > 1) {
			new Error($this->errors['fewAppClasses']);
		}
		$application = $this->classesByTypes['application'][$appClasses[0]];
		if (count($application['extends']) > 1) {
			new Error($this->errors['appExtends'], array($appClasses[0]));
		}
	}

	private function validateViews() {
		$views = $this->classesByTypes['view'];
		$router = $this->config['router'];
		if (is_array($router['routes'])) {
			foreach ($router['routes'] as $route) {
				if (!empty($route['view']) && !isset($views[$route['view']])) {
					new Error($this->errors['viewNotFound'], array($route['view']));
				}
			}
		}
		if (!empty($router['404']) && !isset($views[$router['404']])) {
			new Error($this->errors['404NotFound'], array($router['404']));
		}
	}

	private function validateSuperClasses() {
		$this->usedComponents = $this->templateCompiler->getUsedComponents();
		foreach ($this->classes as $class) {
			if (is_array($class['extends'])) {
				foreach ($class['extends'] as $superClass) {
					if (array_search($superClass, $this->coreClasses) === false) {
						if (!isset($this->classes[$superClass])) {
							new Error($this->errors['superClassNotFound'], array($class['name'], $superClass));
						}
						if ($this->classes[$superClass]['type'] != 'component' && $this->classes[$superClass]['type'] != $class['type']) {
							new Error($this->errors['incorrectSuperClass'], array($class['name'], $superClass));
						}
						if (!isset($this->usedComponents[$superClass])) {
							$this->usedComponents[$superClass] = array();
						}
					}
				}
			}
		}
	}

	private function validateUsedClasses() {
		foreach ($this->usedComponents as $usedComponent => $data) {
			if (!preg_match("/^[A-Z][a-zA-Z\d]+$/", $usedComponent)) {
				new Error($this->errors['incorrectClassName'], array($usedComponent));
			}
			$inClasses = '';
			if (!isset($this->classes[$usedComponent])) {
				if (is_array($data['classes'])) {
					$ending = count($data['classes']) > 1 ? '��' : '�';
					$inClasses = implode(', ', $data['classes']);
				}
				if (!empty($inClasses)) {
					new Error($this->errors['usedClassNotFound'], array($usedComponent, $ending, $inClasses));
				} else {
					new Error($this->errors['usedClassNotFound2'], array($usedComponent));
				}
			}
		}
	}

	private function unsetNotUsedClasses() {
		$configJson = $this->configProvider->getConfigJson();
		$used = array_keys($this->usedComponents);
		$notUsedClasses = array();
		$parentalClasses = array();
		foreach ($this->classes as $className => $classData) {
			if (is_array($classData['extends'])) {
				$parentalClasses = array_merge($parentalClasses, $classData['extends']);
			}
			if (in_array($classData['type'], $this->superClasses) && !in_array($className, $used)) {
				$notUsedClasses[] = $className;
			}
		}
		$parentalClasses = array_unique($parentalClasses);
		$properNotUsedComponents = array();
		foreach ($notUsedClasses as $className) {
			$regexp = '/\b'.$className.'\b/';
			$modifiedJs = preg_replace('/(component|control|menu|form|dialog)\s+'.$className.'\b/', '', $this->jsCode);
			if (!in_array($className, $parentalClasses) && !preg_match($regexp, $modifiedJs) && !preg_match($regexp, $configJson)) {
				$properNotUsedComponents[] = $className;
			}
		}
		foreach ($properNotUsedComponents as $className) {
			unset($this->classes[$className]);
		}
		$this->usedComponentsNames = array_reverse(array_unique(array_keys($this->usedComponents)));
	}

	private function addClassesFromTemplates() {
		$templateClasses = $this->templateCompiler->getTemplateClasses();
		$jsClasses = array_keys($this->classes);
		foreach ($templateClasses as $templateClass) {
			if (!in_array($templateClass, $jsClasses)) {				
				$this->classes[$templateClass] = array(
					'name' => $templateClass,
					'content' => '',
					'type' => $this->usedComponents[$templateClass]['type'],
					'extends' => array(ucfirst($this->usedComponents[$templateClass]['type'])),
					'isSuper' => true
				);
			}
		}
	}

	private function initCore($coreFiles) {
		if (!is_array($coreFiles) || empty($coreFiles)) {
			new Error($this->errors['coreFilesNotFound']);
		}
		foreach ($coreFiles as $coreFile) {
			if (preg_match('/\bhelpers\//', $coreFile['path'])) {
				$this->helpers[] = $coreFile['name'];
				$coreFile['isHelper'] = true;
			}
			$this->sources[$coreFile['name']] = $coreFile;
		}
		$this->checkHelpersUse();
	}

	private function checkHelpersUse() {
		$missingHelpers = array();
		foreach ($this->helpers as $helper) {
			$isMissing = false;
			if (!preg_match('/\b'.$helper.'\b/', $this->jsCode)) {
				$isMissing = true;
				if ($helper == 'Tooltiper') {
					$isMissing = empty($this->config['tooltipClass']);
				} elseif ($helper == 'Globals') {
					$isMissing = !preg_match('/\binitial globals\b/', ' '.$this->jsCode);
				} elseif ($helper == 'StoreKeeper') {
					$isMissing = !preg_match('/\bstoreAs\b/', ' '.$this->jsCode);
				}
			}
			if ($isMissing) $missingHelpers[] = $helper;
		}
		foreach ($missingHelpers as $missingHelper) {
			if (isset($this->sources[$missingHelper]) && $this->sources[$missingHelper]['isHelper']) {
				unset($this->sources[$missingHelper]);
			}
		}
	}

	private function processJSFile(&$jsFile) {
		$content = &$jsFile['content'];
		$content = preg_replace("/\/\*[\S\s]*?\*\//", "", $content);
		$content = preg_replace("/\n\s*\/\/[^\n]*/", "\n", $content);
		if ($jsFile['name'] == 'config') {
			if (is_string($this->apiConfig)) {
				new Error($this->errors['configExists']);
			}
			if (!preg_match('/^\s*var +CONFIG *= *\{/', $content)) {
				new Error($this->errors['incorrectConfig']);
			}
			$this->apiConfig = preg_replace('/^\s*var +CONFIG *= *|[;\r\n\t]/', '', $content);
		} else {
			$this->jsCode .= $content;
			$this->defineJsClass($content, $jsFile);
			$this->JSFileNames[] = $jsFile['name'];
		}
	}

	private function defineJsClass(&$content, &$jsFile) {
		$parts = preg_split('/\n/', trim($content));
		$originalFirstLine = trim($parts[0]);
		$firstLine = trim(preg_replace('/\s*,\s*/', ',', $originalFirstLine), ';');
		if (empty($content)) {
			new Error($this->errors['emptyFile'], array($jsFile['path']));
		}
		if (preg_match('/[�-�]/si', $firstLine)) {
			new Error($this->errors['cyrSymbols'], array($jsFile['path']));
		}
		$lineParts = preg_split('/\s+/', $firstLine);
		$classType = strtolower($lineParts[0]);
		
		if (!preg_match('/^[a-z]+$/', $classType)) {
			new Error($this->errors['incorrectKeyword'], array($classType, $jsFile['path']));
		}
		if (!in_array($classType, $this->classTypes)) {
			if (!isset($lineParts[1])) {
				new Error($this->errors['incorrectDefinition'], array($jsFile['name'], $jsFile['path']));
			}
			new Error($this->errors['unknownClassType'], array($classType, $jsFile['path'], $this->getAvailableClassTypes()));
		}
		$classNameRegExp = "/^[A-Z][a-zA-Z\d]+$/";
		$className = $lineParts[1];
		if (!preg_match($classNameRegExp, $className)) {
			new Error($this->errors['incorrectClassName'], array($className));
		}
		if (isset($lineParts[2]) && $lineParts[2] != 'extends') {
			new Error($this->errors['extendsExpected'], array($lineParts[2], $jsFile['path']));
		}
		if ($lineParts[2] == 'extends' && empty($lineParts[3])) {
			new Error($this->errors['extendsEmpty'], array($jsFile['path']));	
		}
		$extends = array();
		if (isset($lineParts[3])) {
			$extendsString = $lineParts[3];
			$extends = explode(',', $extendsString);
			$index = array_search('Component', $extends);
			if (is_int($index)) {
				array_splice($extends, $index, 1);
			}
			if ($classType == 'application') {
				$index = array_search('Application', $extends);
				if (is_int($index)) {
					array_splice($extends, $index, 1);
				}	
			}
		}
		if (isset($lineParts[4])) {
			new Error($this->errors['incorrectFirstLine'], array($originalFirstLine, $jsFile['path']));
		}
		unset($parts[0]);
		$content = implode("\n", $parts);
		if ($className != $jsFile['name']) {
			new Error($this->errors['differentNames'], array($jsFile['path'], $jsFile['name'], $className));
		}
		$properExtends = array();
		foreach ($extends as $superClassName) {
			if (!preg_match($classNameRegExp, $superClassName)) {
				new Error($this->errors['incorrectSuper'], array($superClassName, $className));
			}
			$properExtends[] = $superClassName;
		}
		array_unshift($properExtends, ucfirst($classType));
		if ($classType == 'corrector') {
			$className .=  'Crr';
			$this->correctors[] = $className;
			$properExtends = array();
		}
		if (isset($this->classes[$className])) {
			new Error($this->errors['classExists'], array($className));
		}
		$this->classes[$className] = array(
			'content' => &$content,
			'extends' => $properExtends,
			'name'    => $className,
			'type'    => $classType,
			'isSuper' => in_array($type, $this->superClasses)
		);
		$this->initialsParser->fetch($content, $this->classes[$className]);
		$this->parseSpecialJSCode($content, $className);
		if (!is_array($this->classesByTypes[$classType])) {
			$this->classesByTypes[$classType] = array();
		}
		$this->classesByTypes[$classType][$className] = &$this->classes[$className];
		TextParser::addToDictionary($className);
	}

	private function getAvailableClassTypes() {
		return '<b>'.implode('</b>, <b>', $this->classTypes).'</b>';
	}

	private function parseSpecialJSCode(&$content, $className) {
		TextParser::encode($content, $className);
		$content = preg_replace('/\#([a-z]\w*)/i', "__#$1", $content);
		$regexp = '([,:=\+\-\*>\!\?<;\(\)\|\}\{\[\]%\/])';
		$content = preg_replace('/'.$regexp.' {1,}/', "$1", $content);
		$content = preg_replace('/ {1,}'.$regexp.'/', "$1", $content);

		$content = preg_replace('/(\$*[\w\]\[\.]+) *\{ *([\w\]\[\.,]+) *\}/', "Objects.get($1,$2)", $content);
		$content = str_replace('<>', 'this.getElement()', $content);
		$content = preg_replace('/\+\+> *(\w+) *(\((.*)\))* *;*/', "Dialoger.show($1,$3)", $content);
		$content = preg_replace('/<\+\+ *(\w+) *(\((.*)\))* *;*/', "Dialoger.hide($1,$3)", $content);
		$content = preg_replace('/\+> *(\w+) *(\((.*)\))*/', "Dialoger.get($1,$3)", $content);
		$content = preg_replace('/--> *(\w+) *(\((.*)\))* *;*/', "this.dispatchEvent('$1',$3);", $content);
		$content = preg_replace('/==> *(\w+) *(\((.*)\))* *;*/', "Globals.dispatchEvent('$1',$3);", $content);
		$content = str_replace(",)", ")", $content);
		
		$regexp = '/[\w\]\[\.]*<[\.\#:]*[a-z][\w\-\.\#\]\[]*>/i';
		$parts = preg_split($regexp, $content);
		preg_match_all($regexp, $content, $matches);
		$matches = $matches[0];
		$content = '';
		foreach ($parts as $i => $part) {
			$content .= $part;
			if (isset($matches[$i])) {
				$p = preg_split('/[<>]/', $matches[$i]);
				$tag = $p[1];
				$scope = '';
				$index = null;
				if ($p[0] == 'return') {
					$content .= 'return ';
				} elseif (!empty($p[0])) {
					$scope = ','.$p[0];
				}
				$p = explode('[', $tag);
				if (isset($p[1])) {
					$tag = $p[0];
					$p = explode(']', $p[1]);
					if (isset($p[1])) {
						$index = $p[0];
					}
				}
				$tag = preg_replace('/[^\.\#:\-\w]/', '', $tag);
				preg_match_all('/([\.\#:]*)([\w\-\.\#]+)/', $tag, $ms);
				if ($ms[1][0] == ':') {
					$content .= "this.getElement('".$ms[2][0]."')";
				} elseif ($ms[1][0] == '::') {
					$content .= "this.getChild('".$ms[2][0]."')";
				} else {
					$selector = !empty($ms[1][0]) ? $ms[1][0].'->>' : '';
					if ($index === null) {
						$content .= "this.findElement('".$selector.$ms[2][0].$scope."')";
					} elseif(empty($index)) {
						$content .= "this.findElements('".$selector.$ms[2][0].$scope."')";
					} else {
						$content .= "this.findElements('".$selector.$ms[2][0].$scope."')[".$index."]";
					}
				}				
			}
		}
	}
	
	private function validateEntry() {
		$e = $this->config['entry'];
		if ($e === null) {
			new Error($this->errors['entryNotFound']);
		}
		if (!is_string($e)) {
			new Error($this->errors['entryNotString']);
		}
		if (preg_match('/[^\w]/', $e)) {
			new Error($this->errors['forbiddenEntrySymbols'], array($e));
		}
		if (!preg_match('/^[A-Z]\w*/', $e)) {
			new Error($this->errors['noPatternEntry'], array($e));
		}
	}

	private function validateJsFolder() {
		if (!is_string($this->config['folder'])) {
			new Error($this->errors['folderIsNotString']);
		}
		preg_match_all('/([^\w\-])/', $this->config['folder'], $matches);		
		if (!empty($matches[0])) {
			$symbols = array();
			foreach ($matches[1] as $s) {
				if (!in_array($s, $symbols)) {
					$symbols[] = $s;
				}
			}
			new Error($this->errors['folderNameIsInvalid'], array('&laquo;'.implode('&raquo;, &laquo;', $symbols).'&raquo;'));
		}
	}

	private function validateTooltipHelper() {
		$class = $this->config['tooltipClass'];
		$api = $this->config['tooltipApi'];
		if (isset($class) && $class !== null && $class !== false && !is_string($class)) {
			new Error($this->errors['tooltipClassNotString']);
		}
		if (isset($api) && !is_string($api)) {
			new Error($this->errors['tooltipApiNotString']);
		}
	}

	private function checkClasses() {
		$routerMenuClasses = $this->config['routerMenu'];
		if (is_array($routerMenuClasses)) {
			foreach ($routerMenuClasses as $routerMenuClass) {
				if (!isset($this->classes[$routerMenuClass])) {
					new Error($this->errors['noRouterMenuClass'], $routerMenuClass);
				}
				if (!isset($this->classesByTypes['menu'][$routerMenuClass])) {
					new Error($this->errors['incorrectRouterMenuClass'], $routerMenuClass);
				}
			}
		}
		$routeControllersToLoad = $this->config['routerControllers'];
		foreach ($routeControllersToLoad as $routeControllersToLoad) {
			if (!isset($this->classesByTypes['controller'][$routeControllersToLoad])) {
				new Error($this->errors['noRouteController'], $routeControllersToLoad);
			}
		}
		$tooltipClass = $this->config['tooltipClass'];
		if (!empty($tooltipClass) && !isset($this->classes[$tooltipClass])) {
			new Error($this->errors['noTooltipClass'], $tooltipClass);
		}
		foreach ($this->classes as $className => &$class) {
			if (in_array($className, $this->reservedNames)) {
				new Error($this->errors['nameReserved'], $className);
			}
			if (is_array($class['controllers'])) {
				foreach ($class['controllers'] as $controller) {
					if (!isset($this->classesByTypes['controller'][$controller])) {
						new Error($this->errors['noController'], array($controller, $class['name']));
					}
				}
			}
			if (is_array($class['helpers'])) {
				foreach ($class['helpers'] as $helper) {						
					if (!isset($this->sources[$helper])) {
						new Error($this->errors['noHelper'], array($helper, $class['name']));
					}
					$code = $this->sources[$helper]['content'];
					if (!preg_match('/\bthis\.subscribe\s*=\s*function\b/', $code) && !preg_match('/\b'.$helper.'\.prototype\.subscribe\s*=\s*function\b/', $code)) {
						new Error($this->errors['noHelperSubscribe'], array($helper, $class['name']));
					}
				}
			}
			if (is_array($class['dialogs'])) {
				if (is_array($class['dialogs'])) {
					foreach ($class['dialogs'] as $dialog) {
						if (!isset($this->classesByTypes['dialog'][$dialog])) {
							new Error($this->errors['noDialog'], array($dialog, $class['name']));
						}
					}
				}
			}
			if (is_array($class['onActions'])) {
				foreach ($class['onActions'] as $action) {
					$controller = $this->classesByTypes['controller'][$action['controller']];
					$actions = $controller['actions'];
					if (!is_array($actions)) {
						$actions = array();
					}
					if (!isset($actions[$action['action']])) {
						new Error($this->errors['actionNotFound'], array($action['action'], $class['name'], $action['controller']));
					}
				}
			}
			if (isset($this->usedComponents[$className]) && isset($this->usedComponents[$className]['type']) && $class['type'] != $this->usedComponents[$className]['type']) {
				if ($class['type'] == 'dialog') {
					new Error($this->errors['dialogCalling'], $this->usedComponents[$className]['classNames'][0]);
				}
				new Error($this->errors['diffClassType'], array($className, $class['type'], $this->usedComponents[$className]['type'], $this->usedComponents[$className]['classNames'][0]));
			}
			$class['extends'] = array_unique($this->getAllExtendClasses($class['extends']));
		}
	}

	private function addGlobals() {
		$data = array(
			'texts' => $this->textsCompiler->get(),
			'config' => $this->apiConfig,
			'data' => $this->dataCompiler->get(),
			'pathToDictionary' => $this->config['pathToDictionary'],
			'tags' => Tags::getList(),
			'props' => Props::getList(),
			'events' => Events::getList(),
			'decls' => $this->declCompiler->get(),
			'routes' => $this->config['router']['routes'],
			'errorRoutes' => $this->routesCompiler->getErrorRoutes(),
			'hashRouter' => $this->routesCompiler->getHashRouter(),
			'indexRoute' => $this->routesCompiler->getIndexRoute(),
			'defaultRoute' => $this->routesCompiler->getDefaultRoute(),
			'viewContainer' => $this->config['viewContainer']
		);
		$jsOutput = JSGlobals::run($data);
	}

	
	private	function getAllExtendClasses($extends) {
		if (!is_array($extends)) return array();
		foreach ($extends as $class) {
			if (is_array($this->classes[$class])) {
				$extClasses = $this->classes[$class]['extends'];
				if (is_array($extClasses)) {
					$extends = array_merge($extends, $this->getAllExtendClasses($extClasses));	
				}
			}
		}
		return $extends;
	}

	private function parseClasses() {
		JSParser::setCorrectors($this->correctors);
		foreach ($this->classes as &$class) {
			JSParser::parse($class);
			JSChecker::check($class);
			$this->checkSuperClassesCallings($class);

			$vals = array_unique($class['functionList']);
			if (count($vals) != count($class['functionList'])) {
				$vals = array_count_values($class['functionList']);
				foreach ($vals as $functionName => $count) {
					if ($count > 1) {
						new Error($this->errors['duplicateMethod'], array($functionName, $class['name']));
					}
				}
			}
		}
	}

	private function checkSuperClassesCallings(&$class) {
		foreach ($class['functions'] as &$func) {
			$regExp = '/\bsuper\((.*)\)/';
			$parts = preg_split($regExp, $func['code']);
			if (count($parts) > 1) {
				preg_match_all($regExp, $func['code'], $matches);
				$callings = $matches[1];
				foreach ($callings as &$call) {
					$arguments = '';
					$errorText = '������ ������ <b>super('.$call.')</b> � ������ <b>'.$func['name'].'</b> ������ <b>'.$class['name'].'</b>. ';
					$method = $func['name'];
					if (empty($call)) {
						$superClass = $this->getSuperClassWithMethod($func['name'], $class, $errorText);
					} else {
						$callOpts = $this->getArgumentsOfSuperClassCalling($call, $func['name'], $errorText);
						$arguments = $callOpts['args'];
						$superClass = $callOpts['superClass'];
						$method = $callOpts['method'];
					}
					$call = $superClass.'.prototype.'.$method.'.call(this'.$arguments.')';
				}
				$func['code'] = '';
				foreach ($parts as $i => $part) {
					$func['code'] .= $part;
					if (isset($callings[$i])) {
						$func['code'] .= $callings[$i];
					}
				}
			}
		}		
	}

	private	function getSuperClassWithMethod($funcName, $class, $errorText) {
		$extends = $class['extends'];
		if (empty($extends) || !is_array($extends)) {
			new Error($this->errors['noSuperClasses'], $errorText);
		}
		$superClasses = array();
		foreach ($extends as $className) {
			$code = $this->classes[$className]['content'];
			if (!empty($code) && preg_match('/\bfunction +'.$funcName.'*\(/', $code)) {
				$superClasses[] = $className;
			}
			$code = $this->sources[$className]['content'];
			if (!empty($code) && preg_match('/\bprototype\.'.$funcName.'\b/', $code)) {
				$superClasses[] = $className;
			}
		}
		if (empty($superClasses)) {
			new Error($this->errors['noSuperClassMethod'], $errorText);
		} else if (count($superClasses) > 1) {
			new Error($this->errors['fewSuperMethods'], $errorText);
		}
		return $superClasses[0];
	}

	private function getArgumentsOfSuperClassCalling($call, $funcName, $errorText) {
		$args = explode(',', $call);
		$superClass = trim($args[0]);
		$parts = explode('.', $superClass);
		if (isset($parts[1])) {
			$funcName = $parts[1];
			$superClass = $parts[0];
		}
		$args[0] = '';
		$arguments = implode(',', $args);
		if (!isset($this->classes[$superClass])) {
			new Error($this->errors['noSuperClass'], array($errorText, $superClass));
		}
		$code = $this->classes[$superClass]['content'];
		if (empty($code) || !preg_match('/\bfunction +'.$funcName.'*\(/', $code)) {
			new Error($this->errors['noThisSuperClassMethod'], array($errorText, $funcName, $superClass));
		}
		return array('args' => $arguments, 'superClass' => $superClass, 'method' => $funcName);
	}
}