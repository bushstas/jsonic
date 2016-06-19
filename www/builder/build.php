<!DOCTYPE>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		<meta name="description" content="" />
	    <title>Builder</title>
	    <style>
			body {
				font-family: Segoe UI, Georgia, Arial;
			}
			b {
				display: inline-block;
				padding: 0 3px;
				background-color: #f1e600;
				border-radius: 2px;
			}
		</style>
	</head>
	<body>
	<?php

		$advancedMode = !empty($_GET['advanced']);
		$create = !empty($_GET['create']);
		include_once 'functions.php';
	
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

		$defaultConfig = array(
			"indexPage" => DEFAULT_PAGE,
			"title"     => DEFAULT_PAGETITLE,
			"entry"     => DEFAULT_ENTRY,
			"scope"     => DEFAULT_SCOPE,
			"sources"   => PATH_TO_SOURCES,
			"blanks"    => PATH_TO_BLANKS
		);


		if (!file_exists(CONFIG_FILENAME)) {
			print("Файл конфигурации не найден. Использованы настройки по умолчанию.<br>");
			$config = $defaultConfig;
		} else {
			$config = json_decode(file_get_contents(CONFIG_FILENAME), true);
			if (!is_array($config)) {
				print("Файл конфигурации не корректен. Использованы настройки по умолчанию.<br>");
				$config = $defaultConfig;
			}
		}

		$cssFolder = $config['cssFolder'];
		$jsFolder = $config['jsFolder'];
		$compiledCss = $config['compiledCss'];
		$compiledJs = $config['compiledJs'];		
		$pathToIndexFile = $config['indexPage'];
		if (empty($cssFolder)) {
			$cssFolder = DEFAULT_CSS_FOLDER;
		}
		if (empty($cssFolder)) {
			$jsFolder = DEFAULT_JS_FOLDER;
		}
		if (empty($compiledCss)) {
			$compiledCss = DEFAULT_CSS_COMPILED;
		}
		if (empty($compiledJs)) {
			$compiledJs = DEFAULT_JS_COMPILED;
		}
		if (empty($pathToIndexFile)) {
			$pathToIndexFile = DEFAULT_PAGE;
		}
		$pathToCompiledCss = $cssFolder.'/'.$compiledCss.'.css';
		$pathToCompiledJs = $jsFolder.'/'.$compiledJs.'.js';

		$pageTitle = $config['title'];
		if (empty($pageTitle)) {
			$pageTitle = DEFAULT_PAGETITLE;
		}

		$charset = $config['charset'];
		if (empty($charset)) {
			$charset = DEFAULT_CHARSET;
		}		

		$viewContainer = $config['container'];
		if (empty($viewContainer)) {
			$viewContainer = DEFAULT_CONTAINER;
		}

		$pathToSrc = $config['scope'];
		if (empty($pathToSrc)) {
			print("Параметр конфигурации <b>scope</b> не найден. Использованы настройки по умолчанию.<br>");
			$pathToSrc = DEFAULT_SCOPE;
		}

		$pathToBlanks = $config['blanks'];
		if (empty($pathToBlanks)) {
			print("Параметр конфигурации <b>blanks</b> не найден. Использованы настройки по умолчанию.<br>");
			$pathToBlanks = PATH_TO_BLANKS;
		}
		$pathToIndexFileContent = $pathToBlanks.'/index.html';
		if (!file_exists($pathToIndexFileContent)) {
			error("Файл <b>".$pathToIndexFileContent.'</b> не найден');
		}
		$indexFileContent = file_get_contents($pathToIndexFileContent);
		if (empty($indexFileContent)) {
			error("Файл <b>".$pathToIndexFileContent.'</b> пуст');
		}
		if (!is_dir($pathToSrc)) {
			error("Директория указанная в параметре <b>scope</b> не найдена");
		}
		
		if (isset($config['pathToApi'])) {
			$pathToApi = $config['pathToApi'];
			if (empty($pathToApi) || !is_string($pathToApi)) {
				error("Параметр конфигурации <b>pathToApi</b> существует, но пуст или не является строкой");
			}
			$apiDir = '../'.$pathToApi;
			if (!is_dir($apiDir)) {
				error("Директория <b>".$pathToApi."</b> указанная в параметре конфигурации <b>pathToApi</b> не найдена в корневом каталоге");	
			}
		}

		$router = $config['router'];
		if (empty($router) || !is_array($router)) {
			error("Параметр конфигурации <b>router</b> отсутствует или не является массивом");
		}
		$routes = $router['routes'];
		if (empty($routes) || !is_array($routes)) {
			error("Параметр конфигурации <b>router['routes']</b> отсутствует или не является массивом");
		}
		$routeControllersToLoad = array();
		$routeControllersByViews = array();
		validateRoutes($routes, $routeControllersToLoad, $routeControllersByViews);

		if (isset($router['menu'])) {
			if (!is_string($router['menu'])) {
				error("Параметр конфигурации <b>router['menu']</b> не является строкой");
			}
			$routerMenu = explode(',', preg_replace('/\s/', '', $router['menu']));
			$properRouterMenu = array();
			foreach ($routerMenu as $menuClass) {
				if (!empty($menuClass)) {					
					if (!preg_match('/^[A-Z]\w*$/', $menuClass)) {
						error("Параметр конфигурации <b>router['menu']</b> содержит название класса <b>".$menuClass."</b> не удовлетворяющее паттерну ^[A-Z]\w*$");
					}
					$properRouterMenu[] = $menuClass;
				}
			}
			$routerMenu = $properRouterMenu;
		}
		if (empty($router['defaultRoute']) && empty($router['404'])) {
			error("Параметры конфигурации <b>router['defaultRoute']</b> и <b>router['404']</b> оба отсутствуют. Хотя один из них должен обязательно присутствовать");
		}
		$defaultRoute = null;
		if (!empty($router['defaultRoute'])) {
			if (!is_string($router['defaultRoute'])) {
				error("Параметр конфигурации <b>router['defaultRoute']</b> не является строкой");
			}
			if (!isRoute($router['defaultRoute'], $routes)) {
				error("Параметр конфигурации <b>router['defaultRoute']</b> = '<b>".$router['defaultRoute']."</b>' не найден среди указанных в <b>router['routes']</b>");
			}
			$defaultRoute = $router['defaultRoute'];
		}
		$indexRoute = $router['indexRoute'];
		if (empty($indexRoute) || !is_string($indexRoute)) {
			error("Параметр конфигурации <b>router['indexRoute']</b> отсутствует или не является строкой");
		}
		if (!isRoute($indexRoute, $routes)) {
			error("Параметр конфигурации <b>router['indexRoute']<b/> = '<b>".$indexRoute."</b>' не найден среди указанных в <b>router['routes']</b>");
		}
		$isHashRouter = $router['hash'];
		if ($isHashRouter !== null && !is_bool($isHashRouter)) {
			error("Параметр конфигурации <b>router['hash']</b> должен быть равен null, true или false");
		}
		$user = $config['user'];
		$isUser = is_array($user);
		if ($isUser) {
			if (isset($user['login']) && !is_string($user['login'])) {
				error("Параметр конфигурации <b>user['login']</b> должен быть строкой");
			}
			if (isset($user['logout']) && !is_string($user['logout'])) {
				error("Параметр конфигурации <b>user['logout']</b> должен быть строкой");
			}
			if (isset($user['save']) && !is_string($user['save'])) {
				error("Параметр конфигурации <b>user['save']</b> должен быть строкой");
			}
		}
		$errorViews = array();
		$errorRoutes = array();
		$errorCodes = array('404', '401');
		foreach ($errorCodes as $errorCode) {
			if (!empty($router[$errorCode])) {
				checkErrorRoute($router[$errorCode], $errorCode);
				$errorViews[$errorCode] = $router[$errorCode];
				$errorRoutes[$errorCode] = $router[$errorCode];
			}
		}		

		if (!empty($create)) {
			$defaultViewCode = '';
			if (file_exists($pathToBlanks.'/view.js')) {
				$defaultViewCode = file_get_contents($pathToBlanks.'/view.js');
			}
			define('VIEW_DEFAULT_CODE', $defaultViewCode);
			$defaultViewTemplateContent = '';
			if (file_exists($pathToBlanks.'/view.template')) {
				$defaultViewTemplateContent = file_get_contents($pathToBlanks.'/view.template');
			}
			define('VIEW_DEFAULT_TEMPLATE_CONTENT', $defaultViewTemplateContent);
			
			$viewsFolder = $config['views'];
			if (empty($viewsFolder)) {
				$viewsFolder = 'views';
			}
			$pathToViews = trim($pathToSrc, '/').'/'.$viewsFolder; 
			if (!is_dir($pathToViews)) {
				createDir($pathToViews);
			}
			createViewDirs($routes, $pathToViews);
			createErrorViewDirs($errorViews, $pathToViews);
		}


		if (empty($config['entry'])) {
			print("\nПараметр конфигурации entry не найден. Использованы настройки по умолчанию.");
			$config['entry'] = DEFAULT_ENTRY;
		}

		if (empty($config['sources'])) {
			$config['sources'] = $defaultConfig['sources'];
		}

		$files = gatherFiles($pathToSrc, array());

		$coreClasses = array(
			'Component', 'Controller', 'Application', 'View', 'Dialog', 'Form', 'Menu'
		);
		$reservedNames = array(
			'Component', 'Controller', 'Application', 'View', 'Level',
			'Form', 'Control', 'AjaxRequest', 'Router', 'Objects',
			'Condition', 'Core', 'Menu', 'EventHandler', 'Dialoger', 'Foreach',
			'Globals', 'User', 'StoreKeeper',

			'__', '__T', '__ROUTES', '__TAGS', '__A', '__EVENTTYPES', '__HASHROUTER', '__DEFAULTROUTE', '__ERRORROUTES',
			'__VIEWCONTAINER', '__USEROPTIONS'
		);

		$classes = array(
			'plugin'      => array(),
			'application' => array(),
			'view'        => array(),
			'component'   => array(),
			'controller'  => array(),
			'dialog'      => array(),			
			'form'        => array(),
			'control'     => array(),
			'menu'        => array()
		);

		$superClasses = array('component', 'dialog', 'form', 'control', 'menu');
		$componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
		$jsSourcesFiles = gatherFiles($config['sources'], array(), true);
		$sourcesList = array();
		foreach ($jsSourcesFiles as $jsSourcesFile) {
			$sourcesList[$jsSourcesFile['name']] = $jsSourcesFile;
		}

		$texts = array();
		$templates = array();
		$css = array();
		$apiConfig = '';
		foreach ($files as $file) {
			$content = file_get_contents($file['path']);
			$content = preg_replace('/^\s+|\s+$/', '', $content);
			$cache[$file['ext']][] = $content;
			if ($file['ext'] == 'js') {
				$content = preg_replace("/\/\*[\S\s]*?\*\//", "", $content);
				if ($file['name'] == 'config') {
					if (!preg_match('/^\s*var +CONFIG *= *\{/', $content)) {
						error('Файл конфигурации путей к api <b>config.js</b> должен иметь вид <b>var CONFIG = {...}</b>');
					}
					$apiConfig = preg_replace('/^\s*var +CONFIG *= *|[;\r\n\t]/', '', $content);
				} else {
					parseJsClass($content, $classes, $file);
				}
			} elseif ($file['ext'] == 'template') {
				$templates[$file['name']] = $content;
			} elseif ($file['ext'] == 'css') {
				$css[] = '/* '.$file['name'].' */';
				$css[] = $compiledCss = preg_replace("/\/\*[^\*]*\*\//", "", $content);
			} elseif ($file['ext'] == 'texts') {
				$texts[] = $content;
			}
		}

		$classNames = array();
		$doubles = array();
		$classesList = array();
		foreach ($classes as $type => &$classesOfType) {
			foreach ($classesOfType as $className => &$classData) {
				$classData['isSuper'] = in_array($type, $superClasses);
				$classData['type'] = $type;
				$classesList[$className] = $classData;
				if ($classNames[$className] === true) {
					$doubles[] = $className;
				}
				$classNames[$className] = true;
			}
		}
		if (!empty($doubles)) {
		 	error('Найдены классы с одинаковыми именами: <b>'.implode(', ', $doubles).'</b>');
		}

		// Checking application
		if (empty($classes['application'])) {
			error('Класс с типом <b>application</b> не найден');
		}
		$appClasses = array_keys($classes['application']);
		if (count($appClasses) > 1) {
			error('Найдено несколько классов с типом <b>application</b>');
		}
		$application = $classes['application'][$appClasses[0]];
		if (count($application['extends']) > 1) {
			error('Класс <b>'.$appClasses[0].'</b> имеет тип <b>application</b> и не может расширяться другими классами');
		}

		// Checking views
		$views = $classes['view'];
		if (is_array($routes)) {
			foreach ($routes as $route) {
				if (!empty($route['view']) && !isset($views[$route['view']])) {
					error('Класс <b>'.$route['view'].'</b> с типом <b>view</b> упомянутый в параметре конфигурации routes не найден');
				}
			}
		}
		if (!empty($router['404'])) {
			if (!is_string($router['404'])) {
				error("Параметр конфигурации <b>router['404']</b> не является строкой");
			}
			if (!isset($views[$router['404']])) {
				error('Класс <b>'.$router['404'].'</b> с типом <b>view</b> указанный для обработки ошибки 404 не найден');
			}
		}

		// Checking components
		$usedComponents = array();
		$filesOfUsedComponents = array();
		foreach ($templates as $filename => $file) {
			preg_match_all("/<component +[\"']*([^\"'\s>]+)/i", $file, $matches);
			foreach ($matches[1] as $match) {
				$usedComponents[] = $match;
				if (!is_array($filesOfUsedComponents[$match])) {
					$filesOfUsedComponents[$match] = array();
				}
				$filesOfUsedComponents[$match][] = $filename;
			}
		}
		foreach ($classes['component'] as $class) {
			if (is_array($class['extends'])) {
				foreach ($class['extends'] as $superClass) {
					if (array_search($superClass, $coreClasses) === false) {
						if (!isset($classesList[$superClass])) {
							error("Используемый в качестве супер-класса для <b>".$class['name']."</b>, класс <b>".$superClass."</b> не найден");
						}
						if ($classesList[$superClass]['type'] != $class['type']) {
							error("Класс <b>".$class['name']."</b> не может быть унаследован от класса <b>".$superClass."</b>. Они должны быть одинакового типа");
						}
						$usedComponents[] = $superClass;
					}
				}
			}
		}
		$usedComponents = array_reverse(array_unique($usedComponents));
		foreach ($usedComponents as $usedComponent) {
			if (!preg_match("/^[A-Z][a-zA-Z\d]+$/", $usedComponent)) {
				error("Название класса <b>".$usedComponent."</b> не валидно. Используйте запись вида <b>ClassName</b>");
			}
			$inClasses = '';
			if (!isset($classNames[$usedComponent])) {
				if (is_array($filesOfUsedComponents[$usedComponent])) {
					$ending = count($filesOfUsedComponents[$usedComponent]) > 1 ? 'ов' : 'а';
					$inClasses = implode(', ', $filesOfUsedComponents[$usedComponent]);
				}
				if (!empty($inClasses)) {
					$error = "Класс <b>".$usedComponent."</b> упомянутый в шаблоне класс".$ending." <b>".$inClasses."</b> не найден";
				} else {
					$error = "Класс <b>".$usedComponent."</b> не найден";
				}
				error($error);
			}
		}

		foreach ($classes as $classType => &$classesByType) {
			parseClassInitials($classesByType);
			parseClassFunctions($classesByType);
		}
		foreach ($classes['controller'] as $controller) {
			if (empty($controller['initials']['actions'])) {
				error('У контроллера <b>'.$controller['name'].'</b> отсутствуют initial параметр <b>actions</b>. Используйте запись <b>initial actions = {"load": {"url": "items/load.php" ...}}</b>');
			}
			if (!preg_match('/\bload[\'"]*\s*:/i', $controller['initials']['actions'])) {
				error('У контроллера <b>'.$controller['name'].'</b> initial параметр <b>actions</b> обязательно должен содержать action <b>load</b>. Используйте запись <b>initial actions = {"load": {"url": "items/load.php" ...}}</b>');
			}
		}
		$routerMenuClasses = $routerMenu;
		$types = array_keys($classes);
		foreach ($types as $type) {
			foreach ($classes[$type] as $className => $component) {
				if (array_search($className, $reservedNames) !== false) {
					error("Название класса <b>".$className."</b> зарезервировано системой");
				}
				if (is_array($routerMenuClasses)) {
					$index = array_search($className, $routerMenuClasses);
					if ($index !== false) {
						unset($routerMenuClasses[$index]);
					}
				}
				if (is_array($component['controllers'])) {
					foreach ($component['controllers'] as $controller) {
						if (!isset($classes['controller'][$controller])) {
							error("Контроллер <b>".$controller."</b> упомянутый в классе <b>".$component['name']."</b> не найден");
						}
					}
				}
				if (is_array($component['helpers'])) {
					foreach ($component['helpers'] as $helper) {						
						if (!isset($sourcesList[$helper])) {
							error("Хелпер <b>".$helper."</b> упомянутый в классе <b>".$component['name']."</b> не найден");
						}
						$code = $sourcesList[$helper]['content'];
						if (!preg_match('/\bthis\.subscribe\s*=\s*function\b/', $code) && !preg_match('/\b'.$helper.'\.prototype\.subscribe\s*=\s*function\b/', $code)) {
							error("У хелпера <b>".$helper."</b> упомянутого в классе <b>".$component['name']."</b> отсутствует метод <b>subscribe</b>");
						}
					}
				}
				if (is_array($component['dialogs'])) {
					if (is_array($component['dialogs'])) {
						foreach ($component['dialogs'] as $dialog) {
							if (!isset($classes['dialog'][$dialog])) {
								error("Диалоговое окно <b>".$dialog."</b> упомянутое в классе <b>".$component['name']."</b> не найдено");
							}
						}
					}
				}
			}
		}
		foreach ($routeControllersToLoad as $routeControllersToLoad) {
			if (!is_array($classes['controller'][$routeControllersToLoad])) {
				error("Контроллер <b>".$routeControllersToLoad."</b> упомянутый в конфигурации роутера не найден");
			}
		}
		if (!empty($routerMenuClasses)) {
			error("Класс(ы) <b>".implode(', ', $routerMenuClasses)."</b> указанные в параметре <b>router['menu']</b> не найдены");
		}

		$sourcesCache = array();
		if (!empty($texts)) {
			$textsIndex = array();
			$globals[] = "var __ = ".str_replace('"', "'", json_encode(getTextConstants($texts, $textsIndex))).';';
		}
		$dictionaryUrl = $config['pathToDictionary'];
		$globals[] = "var __DICTURL = '".$dictionaryUrl."';";
		$globals[] = "var __TAGS = ".str_replace('"', "'", json_encode(getTagShortcuts())).';';
		$globals[] = "var __A = ".str_replace('"', "'", json_encode($propsShortcutsFlipped)).';';
		$globals[] = "var __EVENTTYPES = ".str_replace('"', "'", json_encode($eventTypesShortcuts)).';';
		$globals[] = createObjectString('__ROUTES', $routes, array('/view":"([^"]+)"/', "view':$1"));
		$globals[] = createObjectString('__ERRORROUTES', $errorRoutes, array('/":"([^"]+)"/', "':$1"));
		$globals[] = "var __HASHROUTER = ".($isHashRouter ? 'true' : 'false').';';
		$globals[] = "var __INDEXROUTE = ".($indexRoute ? "'".$indexRoute."'" : 'null').';';
		$globals[] = "var __DEFAULTROUTE = ".($defaultRoute ? "'".$defaultRoute."'" : 'null').';';
		$globals[] = "var __VIEWCONTAINER = '".$viewContainer."';";
		$globals[] = "var __I = '".($advancedMode ? '_i' : 'initiate')."';";
		$globals[] = "var __GI = '".($advancedMode ? '_gi' : 'getInitials')."';";
		
		if (!empty($pathToApi)) {
			$globals[] = "var __APIDIR = '".$pathToApi."';";
		}
		$globals[] = "var __PAGETITLE = '".$pageTitle."';";
		if ($isUser) {
			$globals[] = createObjectString('__USEROPTIONS', $user, array('/\\\/', ''));
		}
		if (!empty($apiConfig)) {
			$globals[] = createObjectString('CONFIG', $apiConfig, array('/\\\/', ''));
		}
		
		$compiledJs = array();
		$compiledJs[] = implode("\n", $globals);
		foreach ($jsSourcesFiles as $jsSourcesFile) {
			$compiledJs[] = $jsSourcesFile['content'];
		}

	
		$types = array_keys($classes);
		foreach ($types as $type) {
			foreach ($classes[$type] as $className => &$component) {
				if (is_array($component['functions'])) {
					foreach ($component['functions'] as $func) {
						$constructorCode = '';
						$args = !empty($func['args']) ? $func['args'] : '';
						if ($func['name'] != '__constructor') {						
							addPrototypeFunction($compiledJs, $className, $func['name'], $args, $func['code']);
						} else {
							addConstructorFunction($compiledJs, $className, isComponent($type));
						}						
					}
				}
				if (!empty($templates[$className])) {
					addTemplateFunction($compiledJs, $className, $templates[$className], $component);
				}
				if (!empty($component['initials'])) {
					addGetInitialsFunction($compiledJs, $className, $component['initials']);
				}
				if ($type == 'view') {
					addLoadControllerFunction($compiledJs, $className);
				}
			}
		}

		$inherits = array();
		foreach ($classes as $classedByType) {
			foreach ($classedByType as $usedClass => $class) {
				$inherits[$usedClass] = $class['extends'];
			}
		}
		$addedClasses = array();
		foreach ($inherits as $usedClass => $extClasses) {
			foreach ($extClasses as $extClass) {
				if (is_array($inherits[$extClass]) && !empty($inherits[$extClass])) {
					$inherits[$usedClass] = array_diff($inherits[$usedClass], $inherits[$extClass]);
				} else {
					$addedClasses[] = $extClass;
				}
			}
		}
		$usedClasses = array_keys($inherits);
		$addedClasses = array_unique($addedClasses);
		$usedClassesCount = count($usedClasses) + count($addedClasses);
		
		$inherited = array(
			'Core' => array('Component','Foreach','Condition'),
			'Component' => array('Application','View','Form','Control','Menu')
		);
		while (count($addedClasses) < $usedClassesCount) {
			foreach ($inherits as $usedClass => $extClasses) {
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
		$inherits = array();
		foreach ($inherited as $parentClass => $childClasses) {
			$inherits[] = $parentClass;
			$inherits[] = '['.implode(',',$childClasses).']';
		}
		
		$compiledJs[] = "Initialization.inherits([".implode(',', $inherits).']);';

		$controllers = array('Objects', 'Router', 'User');
		$controllers = array_merge($controllers, array_keys($classes['controller']));
		if (!empty($controllers)) {
			foreach ($controllers as $controller) {
				$compiledJs[] = $controller." = new ".$controller."();";
			}
		}

		$compiledJs[] = "User.load(".$config['entry'].");";
		$compiledJs[] = "})();";

		array_unshift($compiledJs, "var __T = ".preg_replace("/\\\{2,}/", '\\', str_replace('"', "'", json_encode($textNodes))).';');
		array_unshift($compiledJs, ';(function() {');
		$compiledJs = implode("\n", $compiledJs);
		$compiledJs = preg_replace("/[\n\r]\s*[\n\r]/", "\n", $compiledJs);
		if (is_array($textsIndex)) {
			$regexp = '/\b__\.\w+\b/';
			preg_match_all($regexp, $compiledJs, $matches);
			$codes = $matches[0];
			$parts = preg_split($regexp, $compiledJs);			
			$compiledJs = '';
			foreach ($parts as $i => $part) {
				$compiledJs .= $part;
				if (isset($codes[$i])) {
					$parts2 = explode('.', $codes[$i]);
					$index = array_search($parts2[1], $textsIndex);
					if (is_bool($index)) {
						error('Текстовая константа <b>'.$parts2[1].'</b> не найдена');
					}
					$compiledJs .= '__['.$index.']';
				}
			}
		}
		if ($advancedMode) {
			$compiledJs = preg_replace('/\.prototype\.initiate\b/', '.prototype["_i"]', $compiledJs);
			$compiledJs = preg_replace('/\.prototype\.getInitials\b/', '.prototype["_gi"]', $compiledJs);
		}
		
		if ($advancedMode) {
			createFile('base.js', $compiledJs);
			exec('java -jar compiler.jar --js base.js --compilation_level ADVANCED_OPTIMIZATIONS --js_output_file base2.js');
			unlink('base.js');
			rename('base2.js', DEFAULT_PATH.$pathToCompiledJs);
		} else {
			createFile(DEFAULT_PATH.$pathToCompiledJs, $compiledJs);
		}
		
		
		$replacements = array(
			'charset' => $charset,
			'title' => $pageTitle,
			'css' => $pathToCompiledCss,
			'js' => $pathToCompiledJs
		);
		foreach ($replacements as $k => $v) {
			$indexFileContent = str_replace('{$'.$k.'}', $v, $indexFileContent);
		}
		createFile(DEFAULT_PATH.$pathToIndexFile, $indexFileContent);
		
		if ($isHashRouter !== true && $router['generateTree'] === true) {
			generateTree($routes, $pathToIndexFile, $indexFileContent);
		}

		// CSS
		if (!empty($css)) {
			$compiledCss = implode("\n", $css);
			$compiledCss = preg_replace("/\t/", " ", $compiledCss);
			$compiledCss = preg_replace("/[\r\n]/", "", $compiledCss);
			$compiledCss = preg_replace("/\}/", "}\n", $compiledCss);
			$compiledCss = preg_replace("/ {1,}\{/", "{", $compiledCss);
			$compiledCss = preg_replace("/([:\{;,]) {1,}/", "$1", $compiledCss);
			$compiledCss = preg_replace("/\*\/[ \t]*([^\r\n])/", "*/\n$1", $compiledCss);
			createFile(DEFAULT_PATH.$pathToCompiledCss, $compiledCss);
		}

	?>
	</body>
</html>