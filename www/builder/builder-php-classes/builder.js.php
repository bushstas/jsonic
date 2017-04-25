<?php

class JSCompiler 
{
	private $configProvider, $config, $apiConfig;

	private $errors = array(
		'entryNotFound' => 'Параметр конфигурации <b>entry</b>, обозначающий класс точку входа, не найден',
		'entryNotString' => 'Значение параметра конфигурации <b>entry</b> не является строкой',
		'forbiddenEntrySymbols' => "Параметр конфигурации entry = '{??}' содержит запрещенные символы",
		'noPatternEntry' => "Параметр конфигурации entry = '{??}' не соответствует паттерну <b>[A-Z]\w+</b>",
		'folderIsNotString' => 'Значение параметра конфигурации <b>jsFolder</b> не является строкой',
		'folderNameIsInvalid' => 'Значение параметра конфигурации <b>jsFolder</b> содержит запрещенные символы {??}',
		'tooltipClassNotString' => 'Параметр конфигурации <b>tooltipClass</b> должен быть строкой, содержащей название класса',
		'tooltipApiNotString' => 'Параметр конфигурации <b>tooltipApi</b> должен быть строкой, содержащей путь к api для загрузки текста подсказки',
		'jsFilesNotFound' => 'JS файлы для компиляции приложения, находящиеся внутри директории указанной в параметре конфигурации <b>scope</b>, не найдены',
		'coreFilesNotFound' => 'JS файлы ядра приложения, находящиеся внутри директории указанной в параметре конфигурации <b>so</b>, не найдены',
		'incorrectConfig' => "Файл конфигурации путей к api <b>config.js</b> должен иметь вид <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>",
		'configExists' => 'Обнаружено несколько файлов <b>config.js</b>',
		'emptyFile' => 'Файл {??} пуст',
		'cyrSymbols' => 'Недопустимые кириллические символы в первой строке файла {??}',
		'incorrectKeyword' => 'Недопустимые символы в ключевом слове {??} определяющем тип класса в файле {??}',
		'incorrectDefinition' => 'Отсутствует корректное определение класса {??} в файле {??}',
		'unknownClassType' => 'Неизвестный тип класса {??} в файле {??}.<br>Допустимые значения: {?}',
		'incorrectClassName' => 'Название класса {??} недопустимо. Используйте запись вида <b>ClassName</b>',
		'extendsExpected' => 'Недопустимое ключевое слово {??} в первой строке файла {??}. Ожидается ключевое слово <b>extends</b>',
		'extendsEmpty' => 'Супер-классы не указаны после ключевого слова <b>extends</b> в первой строке файла {??}. Ожидается имя класса или имена классов через запятую',
		'incorrectFirstLine' => 'Недопустимое определение класса {??} в файле {??}',
		'differentNames' => 'Файл {??} должен содержать класс {??}, тогда как содержит класс с именем {??}',
		'differentNames2' => 'Файл {??} должен иметь название {??} как класс, который в нем содержится',
		'incorrectSuper' => 'Название супер-класса {??} для {??} недопустимо. Используйте запись вида <b>ClassName</b>',
		'classExists' => 'Найдено несколько классов с одинаковым именем {??}',
		'appNotFound' => 'Класс с типом <b>application</b> не найден',
		'fewAppClasses' => 'Найдено несколько классов с типом <b>application</b>',
		'appExtends' => 'Класс {??} имеет тип <b>application</b> и не может расширяться другими классами',
		'viewNotFound' => 'Класс {??} с типом <b>view</b> упомянутый в параметре конфигурации routes не найден',
		'404NotFound' => 'Класс {??} с типом <b>view</b>, указанный для обработки ошибки 404, не найден',
		'superClassNotFound' => 'Используемый в качестве супер-класса для {??}, класс {??} не найден',
		'incorrectSuperClass' => 'Класс {??} не может быть унаследован от класса {??}. Они должны быть одинакового типа',
		'duplicateMethod' => 'Обнаружено более одного метода с именем {??} в классе {??}',
		'noSuperClasses' => '{?}У данного класса отсутствуют супер-классы',
		'noSuperClassMethod' => '{?}Данный метод не найден у супер-классов',
		'fewSuperMethods' => '{?}У данного класса есть несколько супер-классов с данным методом. Используйте запись <b>super(ClassName)</b>',
		'noSuperClass' => '{?}Супер-класс {??} не найден',
		'noThisSuperClassMethod' => '{?}Метод {??} отсутствует у супер-класса {??}',
		'nameReserved' => 'Название класса {??} зарезервировано системой',
		'varNameReserved' => 'Название {??} зарезервировано за системой переменной и не может использоваться в качестве имени класса',
		'noController' => 'Контроллер {??} упомянутый в классе {??} не найден',
		'noHelper' => 'Хелпер {??} упомянутый в классе {??} не найден',
		'noHelperSubscribe' => 'У хелпера {??} упомянутого в классе {??} отсутствует метод <b>subscribe</b>',
		'noDialog' => 'Диалоговое окно {??} упомянутое в классе {??} не найдено',
		'actionNotFound' => 'Событие {??} указаннное в initial параметре <b>controllers</b> класса {??} не найдено в initial параметре <b>actions</b> контроллера {??}',
		'noRouterMenuClass' => "Класс {??}, указанный в параметре конфигурации <b>router['menu']</b>, не найден",
		'incorrectRouterMenuClass' => "Класс {??}, указанный в параметре конфигурации <b>router['menu']</b>, должен иметь тип <b>menu</b>",
		'diffClassType' => 'Класс {??} имеет тип {??}, однако вызывается с типом {??} в шаблоне класса {??}',
		'noRouteController' => 'Контроллер {??} упомянутый в конфигурации роутера не найден',
		'noTooltipClass' => 'Класс {??} указанный в параметре конфигурации <b>tooltipClass</b> не найден',
		'noMethodFound' => 'В одном из шаблонов класса {??} обнаружен вызов неизвестного метода {??}',
		'noMethodFound2' => 'Обработчик события {??} не найден среди методов класса {??}',
		'noMethodFound3' => 'Ошибка вызова {??} из метода {??} в коде класса {??}. Метод не найден',
		'noMethodFound4' => 'Обработчик события {??}, указанный в одном из initial параметров, не найден среди методов класса {??}',
		'globalVarUsing' => 'В классе {??} обнаружено использование зарезервированных системой имен переменных: {??}',
		'creatingInstance' => 'В классе {??} обнаружено создание экземпляра класса {??}{?}',
		'obfuscatorError' => 'Ошибка обфусцирующего компилятора:<br><br>{?}<br><br>{?}',
		'noCorrectMethod' => 'В классе-корректоре {??} отсутсвует метод <b>correct</b>',
		'overrideController' => 'Обнаружено переопределение существующего класса {??} в одном из методов класса {??}',
		'overrideGlobal' => 'Обнаружено переопределение системной переменной {??} в одном из методов класса {??}',
		'overrideUtilsFunc' => 'Обнаружено переопределение системной функции {??} в одном из методов класса {??}',
		'noMainTemplate' => 'У класса {??} и ни у одного из его супер-классов не найден шаблон <b>main</b>'
	);

	private $coreClasses = array(
		'Component', 'Controller', 'Application', 'View', 'Dialog', 'Menu', 'Control'
	);

	private $classTypes = array(
		'application', 'view', 'component', 'controller', 'dialog', 'form', 'control', 'menu', 'corrector'
	);

	private $superClasses = array('component', 'dialog', 'form', 'control', 'menu');
	private $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	public static $componentLikeClasses = array('Dialog', 'Form', 'Control', 'Menu', 'View', 'Application');
	private $inheritance = array();
	private $classes = array();
	private $classesByTypes = array();
	private $sources = array();
	private $correctors = array();
	private $helpers = array();
	private $JSFileNames = array();
	private $reservedNames = array();
	private $jsCode = '';
	private $jsOutput = array();
	private $jsOutputByViews = array();
	private $extendsCount = array();
	private $bottomOutput = array();
	private $includesTemplateIndex = array();
	private $initialsParser;
	private $templateCompiler;
	private $textsCompiler;
	private $dataCompiler;
	private $validator;
	private $declCompiler;
	private $testsCompiler;
	private $utilsCompiler;
	private $routesCompiler;
	private $cssCompiler;
	private $usedComponents;
	private $includesTemplateIndexCount = 0;

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
		$this->initialsParser = new InitialsParser();
	}

	public function init($jsFiles, $coreFiles, $scriptFiles, $dataFiles, $utilsFiles) {
		if (!is_array($jsFiles) || empty($jsFiles)) {
			new Error($this->errors['jsFilesNotFound']);
		}
		$provider = $this->configProvider;
		$builder = $provider->getBuilder();
		$this->config = $provider->getJsConfig();
		$this->templateCompiler = $builder->getCompiler('template');
		$this->textsCompiler = $builder->getCompiler('texts');
		$this->dataCompiler = $builder->getCompiler('data');
		$this->declCompiler = $builder->getCompiler('decl');
		$this->routesCompiler = $builder->getCompiler('routes');
		$this->testsCompiler = $builder->getCompiler('tests');
		$this->cssCompiler = $builder->getCompiler('css');
		$this->validator = $builder->getCompiler('validator');
		$this->utilsCompiler = $builder->getCompiler('utils');

		$this->validateEntry();
		$this->validateJsFolder();
		$this->validateTooltipHelper();

		JSInterpreter::init();
		foreach ($jsFiles as $jsFile) {
			$this->processJSFile($jsFile);
		}
		$this->files = array(
			$jsFiles, $coreFiles, $scriptFiles, $dataFiles, $utilsFiles
		);
	}

	public function run() {
		list($jsFiles, $coreFiles, $scriptFiles, $dataFiles, $utilsFiles) = $this->files;
	
		$this->dataCompiler->run($dataFiles);
		$this->initCore($coreFiles);
		$this->validateApplication();
		$this->validateViews();
		$this->validateSuperClasses();
		$this->addClassesFromTemplates();
		$this->initialsParser->run($this->classes);
		$this->parseClasses();
		$this->checkClasses();
		$this->addSources();
		if ($this->configProvider->isTest()) {
			$this->addTests();
		}
		
		$router = $this->config['router'];
		$disabledViews = $this->routesCompiler->getDisabledRoutes();
		ClassAnalyzer::attachCorrectors($this->configProvider->isSplitMode());
		$this->addClasses();
		$errorRoutes = $this->routesCompiler->getErrorRoutes();
		$routes = $router['routes'];
		foreach ($errorRoutes as $errRoute) {
			$routes[] = array(
				'view' => $errRoute,
				'name' => strtolower(preg_replace('/[^\d]/', '', $errRoute)),
				'title' => ''
			);
		}
		if ($this->configProvider->isSplitMode()) {
			foreach ($routes as $route) {
				if (!in_array($route['view'], $disabledViews)) {
					$this->addClasses($route);
				}
			}
		}		
		$this->addIncludes();
		$this->addUtils($utilsFiles);
		$this->addInheritance();
		if ($this->configProvider->isSplitMode()) {
			foreach ($routes as $route) {
				if (!in_array($route['view'], $disabledViews)) {
					$this->addInheritance($route);
				}
			}
		}
		$this->addGlobals();		
		FileManager::emptyFolder(DEFAULT_PATH.$this->config['folder']);
		$this->finish();
		if ($this->configProvider->isSplitMode()) {
			foreach ($routes as $route) {
				if (!in_array($route['view'], $disabledViews)) {
					$this->finish($route);
				}
			}
		}
		$this->addScripts($scriptFiles);
	}

	public function getClasses() {
		return $this->classes;
	}

	public function getInitials() {
		return $this->initialsParser->getOriginal();
	}

	public function getClassInheritance() {
		return $this->inheritance;
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
		$this->usedComponents = ClassAnalyzer::getUsedClasses();
		foreach ($this->classes as $class) {
			if (is_array($class['extends'])) {
				foreach ($class['extends'] as $superClass) {
					if (!isset($this->extendsCount[$superClass])) {
						$this->extendsCount[$superClass] = 0;
					}
					$this->extendsCount[$superClass]++;
					if (array_search($superClass, $this->coreClasses) === false) {
						if (!isset($this->classes[$superClass])) {
							new Error($this->errors['superClassNotFound'], array($class['name'], $superClass));
						}
						if ($this->classes[$superClass]['type'] != 'component' && $this->classes[$superClass]['type'] != $class['type']) {
							new Error($this->errors['incorrectSuperClass'], array($class['name'], $superClass));
						}
					}
				}
			}
		}
	}

	private function isValidClassName($name) {
		return preg_match("/^[A-Z][a-zA-Z\d]*$/", $name);
	}

	private function isClassNameInCode($className) {
		$configJson = $this->configProvider->getConfigJson();
		$modifiedJs = preg_replace('/(component|control|menu|form|dialog)\s+'.$className.'\b/', '', $this->jsCode);
		$regexp = '/\b'.$className.'\b/';
		return preg_match($regexp, $modifiedJs) || preg_match($regexp, $configJson);
	}

	private function addClassesFromTemplates() {
		$templateClasses = $this->templateCompiler->getTemplateClasses();
		$jsClasses = array_keys($this->classes);
		foreach ($templateClasses as $templateClass) {
			if (!in_array($templateClass, $jsClasses)) {
				$data = array(
					'name' => $templateClass,
					'content' => '',
					'type' => 'component',
					'extends' => array('Component'),
					'isSuper' => true
				);
				$this->classes[$templateClass] = $data;
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
			if (preg_match('/[A-Z]\w*/', $coreFile['name'])) {
				$this->reservedNames[] = $coreFile['name'];
			}
			$this->sources[$coreFile['name']] = $coreFile;
		}
		$this->checkHelpersUse();
		$this->checkUtilsUse();
	}

	private function checkHelpersUse() {
		$missingHelpers = array();
		foreach ($this->helpers as $helper) {
			$isMissing = false;
			if (!preg_match('/\b'.$helper.'\b/', $this->jsCode)) {
				$isMissing = true;
				if ($helper == 'Tooltiper') {					
					$isMissing = empty($this->config['tooltipClass']);
				} elseif ($helper == 'State') {
					$isMissing = !$this->isStatesUsed();
				} elseif ($helper == 'StoreKeeper') {
					$isMissing = !preg_match('/\bstoreAs\b/', ' '.$this->jsCode);
				} elseif ($helper == 'MouseHandler') {
					$isMissing = !$this->isMouseHandlerUsed();
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

	private function isStatesUsed() {
		$code = $this->jsCode;
		return preg_match('/\binitial listeners\b/', $code) ||
			   preg_match('/\bState\b/', $code) || 
			   preg_match('/\$:{1,2}\w/', $code);
	}

	private function isMouseHandlerUsed() {
		return preg_match('/\binitial events\b/', $this->jsCode);
	}

	private function checkUtilsUse() {
		$missingUtils = array();
		$classes = CoreValidator::$utilsClasses;
		foreach ($classes as $utilsClass) {
			$regexp = '/\b'.$utilsClass.'\b/';
			if (!preg_match($regexp, $this->jsCode)) {
				$missingUtils[] = $utilsClass;
			}
		}
		foreach ($missingUtils as $utilsClass) {
			if (isset($this->sources[$utilsClass])) {
				if ($utilsClass == 'Decliner') {
					JSGlobals::exclude(CONST_WORDS);
				}
				unset($this->sources[$utilsClass]);
			}
		}
	}

	private function processJSFile(&$jsFile) {
		$content = &$jsFile['content'];
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
		if (preg_match('/[а-я]/si', $firstLine)) {
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
		$className = $lineParts[1];
		if (!$this->isValidClassName($className)) {
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
			if ($this->isValidClassName($jsFile['name'])) {
				new Error($this->errors['differentNames'], array($jsFile['path'], $jsFile['name'], $className));
			} else {
				new Error($this->errors['differentNames2'], array($jsFile['path'], $className.'.js'));
			}
		}
		$properExtends = array();
		foreach ($extends as $superClassName) {
			if (!$this->isValidClassName($superClassName)) {
				new Error($this->errors['incorrectSuper'], array($superClassName, $className));
			}
			$properExtends[] = $superClassName;
		}
		if (!empty($properExtends)) {
			$this->inheritance[$className] = $properExtends;
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
		if ($this->configProvider->needJsObfuscation()) {
			JSObfuscator::obfuscate($content);
		} else {
			JSObfuscator::removeMarks($content);
		}
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
		$texts = TextParser::getTexts($className);
		if (!empty($texts) && TagClassNameParser::parseTexts($texts, $className)) {
			TextParser::setTexts($texts, $className);
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
		$сonstants = JSGlobals::getAllReservedVarNames();
		foreach ($this->classes as $className => &$class) {
			if (in_array($className, $this->reservedNames)) {
				new Error($this->errors['nameReserved'], $className);
			}
			if (in_array($className, $сonstants)) {
				new Error($this->errors['varNameReserved'], $className);
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
			if ($class['type'] == 'corrector' && !in_array('correct', $class['functionList'])) {
				new Error($this->errors['noCorrectMethod'], array($class['name']));
			}
			$class['extends'] = array_unique($this->getAllExtendClasses($class['extends']));
		}
	}

	private function addGlobals() {
		$this->jsOutput = implode("\n", $this->jsOutput);
		$data = array(
			'texts'            => $this->textsCompiler->get(),
			'config'           => $this->apiConfig,
			'data'             => $this->dataCompiler->get(),
			'pathToDictionary' => $this->config['pathToDictionary'],
			'tags'             => Tags::getList(),
			'props'            => Props::getList(true),
			'events'           => Events::getList(),
			'decls'            => $this->declCompiler->get(),
			'routes'           => $this->routesCompiler->getRoutes(),
			'errorRoutes'      => $this->routesCompiler->getErrorRoutes(),
			'hashRouter'       => $this->routesCompiler->getHashRouter(),
			'indexRoute'       => $this->routesCompiler->getIndexRoute(),
			'defaultRoute'     => $this->routesCompiler->getDefaultRoute(),
			'viewContainer'    => $this->config['viewContainer'],
			'tooltipClass'     => $this->config['tooltipClass'],
			'tooltipApi'       => $this->config['tooltipApi'],
			'pathToApi'        => $this->config['pathToApi'],
			'pagetitle'        => $this->config['pagetitle'],
			'user'             => $this->config['user'],
			'controllers'      => array_keys($this->classesByTypes['controller']),
			'isDataLoader'     => $this->configProvider->isUsingDataLoader(),
			'pathToLoadAppApi' => $this->configProvider->getPathToLoadAppApi()
		);
		JSGlobals::run($this->jsOutput, $data);
	}

	private function parseChunkConstants(&$jsOutput) {
		JSGlobals::parseTextConstants($jsOutput, $this->textsCompiler->get());
		JSGlobals::parseDataConstants($jsOutput, $this->dataCompiler->get());
	}

	private function decodeTexts(&$jsOutput, $route = null) {
		$isSplitted = $this->configProvider->isSplitMode();
		$keys = array_keys($this->classes);
		if ($route === null) {
			$usedClasses = ClassAnalyzer::getUsedClasses($isSplitted ? true : null);
		} else {
			$usedClasses = ClassAnalyzer::getUsedClasses($route['view']);
		}
		$properKeys = array();
		foreach ($keys as $key) {
			if (in_array($key, $usedClasses)) {
				$properKeys[] = $key;
			}
		}
		TextParser::decode($jsOutput, $properKeys);
	}

	private function finish($route = null) {
		$this->configProvider->createAppLoader();
		$isNoRoute = $route === null;
		$isSplitted = $this->configProvider->isSplitMode();

		if ($isNoRoute) {
			$bottomOutput = $this->bottomOutput['_'];
			$jsOutput = $this->jsOutput;
			$fileName = $this->config['path'].'.js';
			$this->decodeTexts($jsOutput);
		} else {
			$bottomOutput = $this->bottomOutput[$route['name']];
			$jsOutput = implode("\n", $this->jsOutputByViews[$route['name']]);
			$fileName = $this->config['path'].'_'.$route['name'].'_chunk.js';
			$this->parseChunkConstants($jsOutput);
			$this->decodeTexts($jsOutput, $route);
		}		
		if ($isNoRoute) {
			$top = array(
				"'use strict';",
				"var ".CONST_GLOBAL.";",
				"new (function() {",
				"(function(){",
				"var cs={};",
				"this.create=function(k){var c=this.get(k);if(c instanceof Function)cs[k]=new c()}",
				"this.get=function(k,i){if(i){this.create(k)}return cs[k]}",
				"this.set=function(c,k,i) {if(cs[k])return;cs[k]=c;if(i){this.create(k)}}",
				"}).call(".CONST_GLOBAL."=this);",
				"(function(){var ".CONST_CORE.($isSplitted ? ",".CONST_FUNCS : '').",".CONST_COMPONENT.",".CONST_PROTO.";"
			);
			$jsOutput = implode("\n", $top).$jsOutput;
		} else {
			$top = array(
				"'use strict';",
				"(function(){",
				"var g=".CONST_GLOBAL.".get,"
			);
			$list = JSGlobals::getConstantsListToDefineInChunks();
			$line = array(CONST_COMPONENT, CONST_PROTO);
			foreach ($list as $name) {
				$line[] = $name."=g('".$name."')";
			}
			$jsOutput = implode("\n", $top).implode(",", $line).";\n".$jsOutput;
		}
		$jsOutput = preg_replace("/, *\)/", ')', $jsOutput);
		$jsOutput = preg_replace("/'<nq>/", '', $jsOutput);
		$jsOutput = preg_replace("/<nq>'/", '', $jsOutput);
		$jsOutput = preg_replace("/<nq>/", '', $jsOutput);
		$jsOutput = preg_replace("/;{2,}/", ';', $jsOutput);
		$jsOutput = preg_replace("/ {2,}/", ' ', $jsOutput);
		$jsOutput = preg_replace("/[\n\r]\s*[\n\r]/", "\n", $jsOutput);
		$jsOutput = preg_replace("/".CONST_COMPONENT."\s*=\s*function/", CONST_COMPONENT.'=fnc_', $jsOutput);
		$jsOutput = preg_replace("/function\s*\(\s*\)\s*\{\s*\}/", CONST_FUNCTION, $jsOutput);
		$jsOutput = str_replace(CONST_COMPONENT."=fnc_", CONST_COMPONENT.'=function', $jsOutput);

		if ($this->configProvider->needCssObfuscation()) {
			$this->cssCompiler->obfuscateJs($jsOutput);
		} else {
			$this->cssCompiler->removeMarks($jsOutput);
		}
		$this->parseIncludeTemplates($jsOutput);
		if ($isSplitted) {
			$this->parseUtilsFunctions($jsOutput, empty($route));
			if (!empty($route)) {
				$this->parseObjectsCallngs($jsOutput);
			}
		}
		$jsOutput = preg_replace('/\{\s+\}/', '{}', $jsOutput);
		$jsOutput .= "\n".implode("\n", $bottomOutput);
		$pathToCompiledJs = DEFAULT_PATH.$fileName;
		if ($this->configProvider->isAdvancedMode()) {		
			FileManager::createFile('temp.js', $jsOutput);
			exec('java -jar compiler.jar --js temp.js --compilation_level ADVANCED_OPTIMIZATIONS --js_output_file temp2.js 2>&1', $output);	
			if (!empty($output[0]) && preg_match('/ERROR/', $output[0])) {
				new Error($this->errors['obfuscatorError'], array($output[0], $output[1]));
			}
			unlink('temp.js');
			rename('temp2.js', $pathToCompiledJs);
		} else {
			FileManager::createFile($pathToCompiledJs, $jsOutput);
		}
	}

	private function parseIncludeTemplates(&$jsOutput) {
		if (preg_match('/__INC_TEMPLATE=/', $jsOutput)) {
			preg_match_all('/__INC_TEMPLATE=([\w]+)__/', $jsOutput, $matches);
			$matches = array_unique($matches[1]);
			foreach ($matches as $match) {
				$jsOutput = str_replace('__INC_TEMPLATE='.$match.'__', $this->includesTemplateIndex[$match], $jsOutput);
			}
		}
	}

	private function parseUtilsFunctions(&$jsOutput, $isBase) {
		$utilsFuncs = $this->validator->getUtilsFunctionNames();
		$customFuncs = $this->utilsCompiler->getFunctionsList();
		$utilsFuncs = array_merge($utilsFuncs, $customFuncs);
		if ($isBase) {
			$jsOutput .= "\n";
			$jsOutput .= CONST_FUNCS.'=['.implode(',', $utilsFuncs)."];".CONST_GLOBAL.'.set('.CONST_FUNCS.",'".CONST_FUNCS."');";
		} else {
			$regexp = implode('|', $utilsFuncs);
			preg_match_all('/[^\w\.]('.$regexp.')(?=[\s;,\(])/', $jsOutput, $matches);
			$matches = array_unique($matches[1]);
			foreach ($matches as $match) {
				$index = array_search($match, $utilsFuncs);
				$jsOutput = preg_replace('/([^\w\.])'.$match.'(?=[\s;,\(])/', "$1".CONST_FUNCS.'['.$index.']', $jsOutput);
			}
		}
	}

	private function parseObjectsCallngs(&$jsOutput) {
		$list = array(			
			CONST_OBJECTS => 'Objects',
			CONST_POPUPER => 'Popuper',
			CONST_STATE => 'State',
			CONST_DICTIONARY => 'Dictionary'
		);
		foreach ($list as $k => $v) {
			if (preg_match('/[^\w\.]'.$v.'\b/', $jsOutput)) {
				$jsOutput = preg_replace('/([^\w\.])'.$v.'(?=[\s;,\.\[])/', "$1".$k, $jsOutput);
			}
		}
	}

	private function addScripts($scriptFiles) {
		if (is_array($scriptFiles)) {
			$additionalContent = '';
			foreach ($scriptFiles as $file) {
				if ($file['ext'] == 'js') {
					$additionalContent .= rtrim($file['content'], ';').';';
				}
			}
			if (!empty($additionalContent)) {
				$pathToCompiledJs = DEFAULT_PATH.$this->config['path'].'.js';
				$jsCompiledContent = file_get_contents($pathToCompiledJs);
				$jsCompiledContent = $additionalContent."\n\n".$jsCompiledContent;
				Gatherer::createFile($pathToCompiledJs, $jsCompiledContent);
			}
		}
	}

	private function addSources() {
		foreach ($this->sources as $src) {
			$this->jsOutput[] = $src['content'];
		}
	}

	private function addTests() {
		$this->testsCompiler->checkClasses($this->classes, $this->sources);
	}

	private function addClasses($route = null) {
		$isSplitted = $this->configProvider->isSplitMode();
		if (is_array($route)) {
			if (!$isFirstTime) {
				$isFirstTime = true;
			}
			$this->jsOutputByViews[$route['name']] = array();
			$output = &$this->jsOutputByViews[$route['name']];
			$usedClasses = ClassAnalyzer::getUsedClasses($route['view']);
		} else {
			$output = &$this->jsOutput;
			if ($this->configProvider->isUsingDataLoader()) {
				$output[] = "var ".CONST_CALLBACK." = function(".CONST_LOADEDDATA.") {\n__T = ".CONST_LOADEDDATA."['texts'];\n__ = ".CONST_LOADEDDATA."['textsConstants'];\n__V = __V();";
				$list = array(CONST_TEXTS, CONST_CONSTANTS, CONST_DATA);
				foreach ($list as $name) {
					$output[] = CONST_GLOBAL.".set(".$name.",'".$name."');";
				}
			}
			$usedClasses = ClassAnalyzer::getUsedClasses($isSplitted ? true : null);
		}
		$this->currentRoute = $route;
		
		$templates = $this->templateCompiler->getTemplates();
		TemplateParser::init(
			array(
				'classes' => $this->classes,
				'sources' => $this->sources,
				'templates' => $templates,
				'includes' => $this->templateCompiler->getIncludesNames(),
				'obfuscateCss' => $this->configProvider->needCssObfuscation(),
				'utilsFuncs' => $this->validator->getUtilsFunctionNames(),
				'userUtilsFuncs' => $this->utilsCompiler->getFunctionsList(),
				'initialsParser' => $this->initialsParser
			)
		);
		foreach ($this->classes as $className => &$class) {
			$type = $class['type'];
			if (!in_array($className, $usedClasses)) {
				continue;
			}
			if (is_array($class['functions'])) {
				foreach ($class['functions'] as $func) {
					$constructorCode = '';
					$args = !empty($func['args']) ? $func['args'] : '';
					if ($func['name'] != '__constructor') {
						$this->addPrototypeFunction($className, $func['name'], $args, $func['code']);
					} else {
						$this->addConstructorFunction($className, in_array($type, $this->componentLikeClassTypes), $type);
						$output[] = "\n".CONST_PROTO."=".CONST_COMPONENT.".prototype;";
					}						
				}
			}
			if (!empty($templates[$className])) {
				$this->addTemplateFunction($className, $templates[$className], $class);
				if (is_array($class['tmpCallbacks'])) {
					foreach ($class['tmpCallbacks'] as $callback) {
						if (!$this->hasComponentMethod($callback, $class)) {
							new Error($this->errors['noMethodFound'], array($className, $callback));
						}
					}
				}
			}
			if (!empty($class['initials'])) {
				$this->addGetInitialsFunction($className, $class['initials']);
			}

			if (is_array($class['callbacks'])) {
				foreach ($class['callbacks'] as $callback) {
					if (!$this->hasComponentMethod($callback, $class)) {
						new Error($this->errors['noMethodFound2'], array($callback, $className));
					}
				}
			}
			if (is_array($class['initialCallbacks'])) {
				foreach ($class['initialCallbacks'] as $callback) {
					if (!$this->hasComponentMethod($callback, $class)) {
						new Error($this->errors['noMethodFound4'], array($callback, $className));
					}
				}
			}
			if (is_array($class['calledMethods'])) {
				foreach ($class['calledMethods'] as $callback => $callerFunc) {
					if (!$this->hasComponentMethod($callback, $class)) {
						$isError = true;
						if (!in_array($className, $this->usedComponents)) {
							$childClasses = array();
							$this->getChildClasses($className, $childClasses);
							foreach ($childClasses as $chcls) {
								if ($this->hasComponentMethod($callback, $this->classes[$chcls])) {
									$isError = false;
									break;
								}
							}
						}
						if ($isError) {
							new Error($this->errors['noMethodFound3'], array($callback, $callerFunc, $className));
						}
					}
				}
			}
		}
		if (!$isSplitted) {
			$usedComponents = ClassAnalyzer::getUsedComponents();
		} else {
			$usedComponents = ClassAnalyzer::getUsedComponents(!empty($route) ? $route['view'] : true);
		}
		foreach ($usedComponents as $className) {
			if (!$this->hasMainTemplate($className)) {
				
				new Error($this->errors['noMainTemplate'], $className);
			}
		}
	}

	private function hasMainTemplate($className) {
		$ownTemplates = $this->classes[$className]['templatesList'];		
		if (is_array($ownTemplates) && in_array('main', $ownTemplates)) {
			return true;
		}
		$parents = $this->classes[$className]['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parentClassName) {
				if ($this->hasMainTemplate($parentClassName)) {
					return true;
				}
			}
		}		
	}

	private function addIncludes() {
		$includes = $this->templateCompiler->getIncludes();
		foreach ($includes as $file => $include) {
			$this->addIncludeFunction($include, $file);
		}
	}

	private function addUtils($utilsFiles) {
		foreach ($utilsFiles as $utilsFile) {
			$this->jsOutput[] = "\n".$utilsFile['content'];
		}
	}

	private function addInheritance($route = null) {
		$isSplitted = $this->configProvider->isSplitMode();
		if ($route === null) {
			$this->bottomOutput['_'] = array();
			$bottomOutput = &$this->bottomOutput['_'];
		} else {
			$this->bottomOutput[$route['name']] = array();
			$bottomOutput = &$this->bottomOutput[$route['name']];
		}
		if (!$isSplitted) {
			$used = ClassAnalyzer::getUsedClasses();
		} else {
			if (empty($route)) {
				$used = ClassAnalyzer::getUsedClasses(true);
			} else {
				$used = ClassAnalyzer::getUsedClasses($route['view']);
			}
		}
		$inheritance = array();		
		foreach ($this->classes as $className => $class) {
			if (in_array($className, $used) && is_array($class['extends']) && !empty($class['extends'])) {
				$inheritance[$className] = $class['extends'];
			}
		}
		$addedClasses = array();
		foreach ($inheritance as $usedClass => $extClasses) {
			foreach ($extClasses as $extClass) {
				if (is_array($inheritance[$extClass]) && !empty($inheritance[$extClass])) {
					$inheritance[$usedClass] = array_diff($inheritance[$usedClass], $inheritance[$extClass]);
				} else {
					$addedClasses[] = $extClass;
				}
			}
		}
		$usedClasses = array_keys($inheritance);

		$addedClasses = array_unique($addedClasses);
		$usedClassesCount = count($usedClasses) + count($addedClasses);		
		if (empty($route)) {
			$inherited = array(
				'Component' => array('Application', 'View', 'Control', 'Menu')
			);
		} else {
			$inherited = array(
				'Component' => array()
			);
		}
		while (count($addedClasses) < $usedClassesCount) {
			foreach ($inheritance as $usedClass => $extClasses) {
				if (array_search($usedClass, $addedClasses) === false) {
					$diff = array_diff($extClasses, $addedClasses);
					if (empty($diff)) {
						$addedClasses[] = $usedClass;
						foreach ($extClasses as $extClass) {
							if (!isset($inherited[$extClass])) {
								$inherited[$extClass] = array();
							}
							$inherited[$extClass][] = $usedClass;
						}
					}
				}
			}
		}
		$inheritance = array();
		$disabledRoutes = $this->routesCompiler->getDisabledRoutes();
		foreach ($inherited as $parentClass => $childClasses) {
			if ($parentClass == 'View' && !empty($disabledRoutes)) {
				$enabledClasses = array();
				foreach ($childClasses as $childClass) {
					if (!in_array($childClass, $disabledRoutes) || in_array($childClass, $used)) {
						$enabledClasses[] = $childClass;
					}
				}
				$childClasses = $enabledClasses;
			}
			if (!empty($childClasses)) {
				$inheritance[] = "'".$parentClass."'";
				$inheritance[] = "['".implode("','", $childClasses)."']";
			}
		}	
		if (empty($route)) {
			$bottomOutput[] = CONST_CORE.".inherits([".implode(",", $inheritance)."]);";
		} else {
			$bottomOutput[] = CONST_CORE.".inherits([".implode(",", $inheritance)."]);";
		}
		if (empty($route)) {
			$entry = $this->config['entry'];
			if ($this->configProvider->isUsingDataLoader()) {
				$bottomOutput[] = "if (isObject(".CONST_LOADEDDATA."['user'])) ".CONST_USER.".setData(".CONST_LOADEDDATA."['user']);";
				$bottomOutput[] = "if (isObject(".CONST_LOADEDDATA."['dictionary'])) Dictionary.setData(".CONST_ROUTER.".getCurrentRoute()['name'], ".CONST_LOADEDDATA."['dictionary']);";
				$bottomOutput[] = "var ".$entry."=".CONST_GLOBAL.".get('".$entry."',1);";
				$bottomOutput[] = CONST_CORE.".initiate.call(".$entry.");";
				$bottomOutput[] = $entry.".run();";
				$bottomOutput[] = "};";
			}
			$controllers = array(CONST_ROUTER, CONST_USER);
			if (!empty($controllers)) {
				foreach ($controllers as $controller) {
					if ($controller == CONST_USER && !$this->config['hasUser']) {
						continue;
					}
					$bottomOutput[] = "var ".$controller."=".CONST_GLOBAL.".get('".$controller."',1);";
				}
				
			}
			if (!$this->configProvider->isUsingDataLoader()) {
				$bottomOutput[] = "var ".$entry."=".CONST_GLOBAL.".get('".$entry."',1);";
				$bottomOutput[] = CONST_CORE.".initiate.call(".$entry.");";
				if ($this->config['hasUser']) {
					$bottomOutput[] = CONST_USER.".load(".$entry.");";
				} else {
					$bottomOutput[] = $entry.".run();";
				}
			} else {
				$bottomOutput[] = "Loader.get(__LU, {'route': ".CONST_ROUTER.".getCurrentRoute()['name']},".CONST_CALLBACK.");";
			}
			$bottomOutput[] = "})();\n});";
		} else {
			$bottomOutput[] = "})();";
		}
	}

	private function hasComponentMethod($method, $class) {
		if (is_array($class['functionList']) && in_array($method, $class['functionList'])) return true;
		$parents = $class['extends'];
		if (is_array($parents)) {
			foreach ($parents as $parent) {
				if (is_array($this->classes[$parent]) && $this->hasComponentMethod($method, $this->classes[$parent])) {
					return true;
				}
				if (is_array($this->sources[$parent]) && preg_match('/\b'.CONST_PROTO.'\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', $this->sources[$parent]['content'])) {
					return true;	
				}
				if (in_array($parent, self::$componentLikeClasses) && preg_match('/\b'.CONST_PROTO.'\.'.$method.'\s*=\s*function\s*\(([^\)]*)\)/', $this->sources['Component']['content'])) {
					return true;
				}
			}
		}
		return false;
	}

	private	function getChildClasses($className, &$classes) {
		foreach ($this->classes as $class => $data) {
			if (is_array($data['extends']) && in_array($className, $data['extends'])) {
				$classes[] = $class;
				$this->getChildClasses($class, $classes);
			}
		}
	}

	private function addConstructorFunction($className, $isComponent, $type) {
		if (empty($this->currentRoute)) {
			$output = &$this->jsOutput;
		} else {
			$output = &$this->jsOutputByViews[$this->currentRoute['name']];
		}
		$routerMenuClasses = $this->config['routerMenu'];
		$isRouterMenu = $isComponent && is_array($routerMenuClasses) && in_array($className, $routerMenuClasses);
		$fnc = CONST_GETFUNC.'()';
		$fnc2 = '';
		if ($isRouterMenu) {
			$fnc = 'function(){';
			$fnc2 = "}\n";
		}
		$content = CONST_GLOBAL.".set(".CONST_COMPONENT.'='.$fnc;
		if ($isRouterMenu) {
			$content .= "\n".CONST_ROUTER.".addMenu(this);";
			$content .= "\nthis.isRouteMenu=true;";
		}
		$isSingleton = '';
		if ($type == 'corrector') {
			$isSingleton = ',1';
		}
		$content .= $fnc2.",'".$className."'".$isSingleton.");";
		$output[] = $content;
	}

	private function addPrototypeFunction($className, $method, $args = '', $code = '') {
		if (empty($this->currentRoute)) {
			$output = &$this->jsOutput;
		} else {
			$output = &$this->jsOutputByViews[$this->currentRoute['name']];
		}
		$output[] = CONST_PROTO.'.'.$method.'=function('.$args.'){';
		$output[] = $code;
		$output[] = '};';
	}

	private	function addTemplateFunction($className, $templateContent, &$class) {
		if (empty($this->currentRoute)) {
			$output = &$this->jsOutput;
		} else {
			$output = &$this->jsOutputByViews[$this->currentRoute['name']];
		}
		$tmpids = array();
		if ($this->configProvider->needJsObfuscation()) {
			JSObfuscator::obfuscate($templateContent);
		} else {
			JSObfuscator::removeMarks($templateContent);
		}
		$templateFunctions = TemplateParser::parse($templateContent, $class, $className, $tmpids);
		foreach ($templateFunctions as $templateFunction) {
			$let = '';
			if (!empty($templateFunction['let'])) {
				$let = "\t".$templateFunction['let'].";";
			}
			if (!empty($templateFunction['content'])) {
				$fl = $templateFunction['content'][0];
				if ($fl != '{' && $fl != '[' && $fl != '(') {
					$templateFunction['content'] = ' '.$templateFunction['content'];
				}
				$content = $let."\n\treturn ".$templateFunction['content'];
			} else {
				$content = $let."\n";
			}
			$this->addPrototypeFunction($className, 'getTemplate'.ucfirst($templateFunction['name']), '_,$', $content);
		}
		if (!empty($tmpids)) {
			foreach ($tmpids as $k => &$v) $v = '<nq>'.CONST_PROTO.'.getTemplate'.ucfirst($v).'<nq>';
			$output[] = CONST_PROTO.'.templatesById='.str_replace('"', "'", preg_replace('/"<nq>|<nq>"/', '', json_encode($tmpids))).';';
		}
	}

	private	function addIncludeFunction($templateContent, $file) {
		$idx = &$this->includesTemplateIndexCount;
		$templateFunctions = TemplateParser::parse($templateContent, $file);
		foreach ($templateFunctions as $templateFunction) {
			$this->includesTemplateIndex[$templateFunction['name']] = $idx;
			$this->jsOutput[] = CONST_GLOBAL.'.set(function(_,$){';
			$this->jsOutput[] = "\n\treturn ".$templateFunction['content']."\n},'i_".$idx."');";
			$idx++;
		}
	}

	private function addGetInitialsFunction($className, $initials) {
		if (empty($this->currentRoute)) {
			$output = &$this->jsOutput;
		} else {
			$output = &$this->jsOutputByViews[$this->currentRoute['name']];
		}
		$objCode = array();
		foreach ($initials as $name => $code) {
			if (!empty($code)) {
				TagClassNameParser::parseTexts($code, $className, true);
				$code = preg_replace('/(:\s*)@(\w+)/', "$1__.$2", $code);
				$code = preg_replace('/\#([a-z]\w*)/i', "<data>$1", $code);
				$code = preg_replace("/[\t\r\n]/", '', $code);
				$code = preg_replace("/ {2,}/", ' ', $code);				
				$spacelessCode = preg_replace('/\s/', '', $code);
				if ($spacelessCode != '{}' && $spacelessCode != '[]') {
					$objCode[] = "\n\t\t'".$name."':".$code;
				}
			}
		}
		$objCode = implode(",\n", $objCode);
		ControllersParser::parseInitialsCode($objCode);
		if (!empty($objCode)) {
			$output[] = CONST_PROTO.".getInitials=function(){";
			$output[] = "\n\treturn {\n";
			$output[] = $objCode;
			$output[] = "\t};\n};";
		}
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
		ControllersParser::init($this->classes, $this->sources, $this->classesByTypes['controller'], $this->initialsParser);
		DialogsParser::init($this->classes, $this->sources, $this->classesByTypes['dialog'], $this->initialsParser);
		$classNames = array_keys($this->classes);
		$coreClassNames = $this->reservedNames;
		$classNames = array_merge($classNames, $coreClassNames);
		$utilsFuncs = $this->validator->getUtilsFunctionNames();
		$implodedClasses = implode('|', array_values($classNames));

		$privateConstants = JSGlobals::getReservedPrivateVarNames();
		$implodedPrivateConstants = implode('|', $privateConstants);

		$allConstants = JSGlobals::getReservedPublicVarNames();
		$implodedConstants = implode('|', $allConstants);

		$regexp1 = '/[^\w\$]('.$implodedPrivateConstants.')\b/';
		$regexp2 = '/[^\w\$]new\s+('.$implodedClasses.')\b/';
		$regexp3 = '/[^\w\$]('.$implodedClasses.')\s*=(?!=)/';
		$regexp4 = '/[^\w\$]('.$implodedConstants.')\s*=(?!=)/';
		$regexp5 = '/[^\w\$]('.implode('|', $utilsFuncs).')\s*=(?!=)/';
		foreach ($this->classes as $className => &$class) {
			preg_match_all($regexp4, $class['content'], $matches);
			if (!empty($matches[1])) {
				new Error($this->errors['overrideGlobal'], array($matches[1][0], $className));
			}
			if (preg_match_all($regexp1, $class['content'], $matches)) {
				new Error($this->errors['globalVarUsing'], array($className, implode(', ', $matches[0])));
			}
			if (preg_match($regexp2, $class['content'], $matches)) {
				$instance = preg_replace('/^new\s+/', '', $matches[0]);
				$isForm = isset($this->classesByTypes['form'][$instance]);
				$isMenu = isset($this->classesByTypes['menu'][$instance]);
				$ending = '. Используйте шаблоны для рендеринга компонента.<xmp><'.($isForm ? 'form' : ($isMenu ? 'menu' : 'component')).' class="'.$instance.'" param1="1" param2="2"></xmp>';
				if (in_array($instance, $coreClassNames)) {
					$ending = '. Данный класс является системным и его нельзя использовать таким образом';
				} elseif (isset($this->classesByTypes['application'][$instance])) {
					$ending = '. Данный класс является приложением и синглтоном.<br>Используйте код вида:<xmp>'.$instance.'.getView("main");</xmp>';
				} elseif (isset($this->classesByTypes['controller'][$instance])) {
					$ending = '. Данный класс является контроллером и синглтоном.<br>Используйте код вида:<xmp>'.$instance.'.doAction("load");</xmp>';
				} elseif (isset($this->classesByTypes['view'][$instance])) {
					$ending = ', который имеет тип <b>view</b>.<br>Данный тип классов рендерит само приложение и их нельзя использовать';
				} elseif (isset($this->classesByTypes['dialog'][$instance])) {
					$ending = '. Данный класс является диалоговым окном.<br>Для его показа используйте код вида:<xmp>Dialoger.show('.$instance.');</xmp> или сокращением <xmp>++> '.$instance.'</xmp>';
				}
				new Error($this->errors['creatingInstance'], array($className, $matches[0], $ending));
			}
			preg_match_all($regexp3, $class['content'], $matches);
			if (!empty($matches[1])) {
				new Error($this->errors['overrideController'], array($matches[1][0], $className));
			}
			preg_match_all($regexp5, $class['content'], $matches);
			if (!empty($matches[1])) {
				new Error($this->errors['overrideUtilsFunc'], array($matches[1][0], $className));
			}
			JSParser::init($this->correctors);
			JSParser::parse($class);
			JSChecker::check($class);
			ControllersParser::parse($class);
			DialogsParser::parse($class);

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
					$errorText = 'Ошибка вызова <b>super('.$call.')</b> в методе <b>'.$func['name'].'</b> класса <b>'.$class['name'].'</b>. ';
					$method = $func['name'];
					if (empty($call)) {
						$superClass = $this->getSuperClassWithMethod($func['name'], $class, $errorText);
					} else {
						$callOpts = $this->getArgumentsOfSuperClassCalling($call, $func['name'], $errorText);
						$arguments = $callOpts['args'];
						$superClass = $callOpts['superClass'];
						$method = $callOpts['method'];
					}
					$call = CONST_GLOBAL.".get('".$superClass."').prototype.".$method.'.call(this'.$arguments.')';
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

	public	function getSuperClassWithMethod($funcName, $class, $errorText) {
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