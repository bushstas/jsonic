	<?php
		include_once 'header.php';
		$advancedMode = !empty($_GET['advanced']);
		$create = !empty($_GET['create']);
		$obfuscate = !empty($_GET['obfuscate']);
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
			print("���� ������������ �� ������. ������������ ��������� �� ���������.<br>");
			$config = $defaultConfig;
		} else {
			$config = json_decode(file_get_contents(CONFIG_FILENAME), true);
			if (!is_array($config)) {
				print("���� ������������ �� ���������. ������������ ��������� �� ���������.<br>");
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
			print("�������� ������������ <b>scope</b> �� ������. ������������ ��������� �� ���������.<br>");
			$pathToSrc = DEFAULT_SCOPE;
		}

		$pathToBlanks = $config['blanks'];
		if (empty($pathToBlanks)) {
			print("�������� ������������ <b>blanks</b> �� ������. ������������ ��������� �� ���������.<br>");
			$pathToBlanks = PATH_TO_BLANKS;
		}
		$pathToIndexFileContent = $pathToBlanks.'/index.html';
		if (!file_exists($pathToIndexFileContent)) {
			error("���� <b>".$pathToIndexFileContent.'</b> �� ������');
		}
		$indexFileContent = file_get_contents($pathToIndexFileContent);
		if (empty($indexFileContent)) {
			error("���� <b>".$pathToIndexFileContent.'</b> ����');
		}
		if (!is_dir($pathToSrc)) {
			error("���������� ��������� � ��������� <b>scope</b> �� �������");
		}
		
		if (isset($config['pathToApi'])) {
			$pathToApi = $config['pathToApi'];
			if (empty($pathToApi) || !is_string($pathToApi)) {
				error("�������� ������������ <b>pathToApi</b> ����������, �� ���� ��� �� �������� �������");
			}
			$apiDir = '../'.$pathToApi;
			if (!is_dir($apiDir)) {
				error("���������� <b>".$pathToApi."</b> ��������� � ��������� ������������ <b>pathToApi</b> �� ������� � �������� ��������");	
			}
		}

		$router = $config['router'];
		if (empty($router) || !is_array($router)) {
			error("�������� ������������ <b>router</b> ����������� ��� �� �������� ��������");
		}
		$routes = $router['routes'];
		if (empty($routes) || !is_array($routes)) {
			error("�������� ������������ <b>router['routes']</b> ����������� ��� �� �������� ��������");
		}
		$routeControllersToLoad = array();
		$routeControllersByViews = array();
		validateRoutes($routes, $routeControllersToLoad, $routeControllersByViews);

		if (isset($router['menu'])) {
			if (!is_string($router['menu'])) {
				error("�������� ������������ <b>router['menu']</b> �� �������� �������");
			}
			$routerMenu = explode(',', preg_replace('/\s/', '', $router['menu']));
			$properRouterMenu = array();
			foreach ($routerMenu as $menuClass) {
				if (!empty($menuClass)) {					
					if (!preg_match('/^[A-Z]\w*$/', $menuClass)) {
						error("�������� ������������ <b>router['menu']</b> �������� �������� ������ <b>".$menuClass."</b> �� ��������������� �������� ^[A-Z]\w*$");
					}
					$properRouterMenu[] = $menuClass;
				}
			}
			$routerMenu = $properRouterMenu;
		}
		if (empty($router['defaultRoute']) && empty($router['404'])) {
			error("��������� ������������ <b>router['defaultRoute']</b> � <b>router['404']</b> ��� �����������. ���� ���� �� ��� ������ ����������� ��������������");
		}
		$defaultRoute = null;
		if (!empty($router['defaultRoute'])) {
			if (!is_string($router['defaultRoute'])) {
				error("�������� ������������ <b>router['defaultRoute']</b> �� �������� �������");
			}
			if (!isRoute($router['defaultRoute'], $routes)) {
				error("�������� ������������ <b>router['defaultRoute']</b> = '<b>".$router['defaultRoute']."</b>' �� ������ ����� ��������� � <b>router['routes']</b>");
			}
			$defaultRoute = $router['defaultRoute'];
		}
		$indexRoute = $router['indexRoute'];
		if (empty($indexRoute) || !is_string($indexRoute)) {
			error("�������� ������������ <b>router['indexRoute']</b> ����������� ��� �� �������� �������");
		}
		if (!isRoute($indexRoute, $routes)) {
			error("�������� ������������ <b>router['indexRoute']<b/> = '<b>".$indexRoute."</b>' �� ������ ����� ��������� � <b>router['routes']</b>");
		}
		$isHashRouter = $router['hash'];
		if ($isHashRouter !== null && !is_bool($isHashRouter)) {
			error("�������� ������������ <b>router['hash']</b> ������ ���� ����� null, true ��� false");
		}
		$user = $config['user'];
		$isUser = is_array($user);
		if ($isUser) {
			if (isset($user['login']) && !is_string($user['login'])) {
				error("�������� ������������ <b>user['login']</b> ������ ���� �������");
			}
			if (isset($user['logout']) && !is_string($user['logout'])) {
				error("�������� ������������ <b>user['logout']</b> ������ ���� �������");
			}
			if (isset($user['save']) && !is_string($user['save'])) {
				error("�������� ������������ <b>user['save']</b> ������ ���� �������");
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
		if (isset($config['tooltipClass']) && $config['tooltipClass'] !== null && $config['tooltipClass'] !== false) {
			if (!is_string($config['tooltipClass'])) {
				error("�������� ������������ <b>tooltipClass</b> ������ ���� �������, ���������� �������� ������");
			}
			$tooltipClass = $config['tooltipClass'];
		}
		if (isset($config['tooltipApi'])) {
			if (!is_string($config['tooltipApi'])) {
				error("�������� ������������ <b>tooltipApi</b> ������ ���� �������, ���������� ���� � api ��� �������� ������ ���������");
			}
			$tooltipApi = $config['tooltipApi'];
		}


		if (empty($config['entry'])) {
			print("\n�������� ������������ entry �� ������. ������������ ��������� �� ���������.");
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
			'Globals', 'User', 'StoreKeeper', 'Switch', 'Tooltiper', 'IfSwitch',

			'__', '__T', '__ROUTES', '__TAGS', '__A', '__EVENTTYPES', '__HASHROUTER', '__DEFAULTROUTE', '__ERRORROUTES',
			'__VIEWCONTAINER', '__USEROPTIONS', '__D', '__V'
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
		$cssconsts = array();
		$data = array();
		$templates = array();
		$includes = array();
		$css = array();
		$cssData = array();
		$apiConfig = '';
		$isConfigJsFile = false;
		foreach ($files as $file) {
			$content = file_get_contents($file['path']);
			$content = preg_replace('/^\s+|\s+$/', '', $content);
			$cache[$file['ext']][] = $content;
			if ($file['ext'] == 'js') {
				$content = preg_replace("/\/\*[\S\s]*?\*\//", "", $content);
				if ($file['name'] == 'config') {
					if (!preg_match('/^\s*var +CONFIG *= *\{/', $content)) {
						error("���� ������������ ����� � api <b>config.js</b> ������ ����� ��� <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>");
					}
					$apiConfig = preg_replace('/^\s*var +CONFIG *= *|[;\r\n\t]/', '', $content);
					$isConfigJsFile = true;
				} else {
					parseJsClass($content, $classes, $file);
				}
			} elseif ($file['ext'] == 'template') {
				$templates[$file['name']] = preg_replace("/<\!--.*?-->/", "", $content);
			} elseif ($file['ext'] == 'css') {
				$cssData[] = $file;
				$css[] =  '/* '.$file['name'].' */'.preg_replace("/\/\*[^\*]*\*\//", "", $content);
			} elseif ($file['ext'] == 'texts') {
				$texts[] = $content;
			} elseif ($file['ext'] == 'data') {
				$data[] = $content;
			} elseif ($file['ext'] == 'cssconst') {
				$cssconsts[] = $content;
			} elseif ($file['ext'] == 'include') {
				$file['content'] = $content;
				$includes[] = $file;
			}
		}
		if (!$isConfigJsFile) {
			error("���� ������������ ����� � api <b>config.js</b> �� ������.<br>��������� ��� � ����� ���������� ������ ����������.<br><b>���������� ����� ������ ����� ���:</b>
				<xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp><br><b>�������������:</b><br><br>CONFIG.items.get
			");
		}
		$cssconsts = getCssConstants($cssconsts);

		// compiling CSS
		if (!empty($css)) {
			foreach ($css as $i => &$cssFile) {
				$regexp = '/\$imgsrc\s*=\s*([^\s]+)/';
				preg_match_all($regexp, $cssFile, $matches);
				if (count($matches[0]) > 1) {
					error('���������� ����� ������ <b>$imgsrc</b> ����������� ���� � ������� ������������ � ����� ������ <b>'.$cssData[$i]['path'].'</b>');
				}
				$pathToImages = '';
				if (count($matches[1]) > 0) {
					$pathToImages = rtrim($matches[1][0], '/').'/';
					$cssFile = preg_replace($regexp, '', $cssFile);
					$cssFile = preg_replace('/\$*(png|jpg|jpeg|gif)\s*=\s*([^\s\)]+)/i', "background-image:url##".$pathToImages."$2.$1##;", $cssFile);
				}
			}
			$compiledCss = implode("\n", $css);
			$regexp = '/\$\s*\(([^\)]+)\)/';
			preg_match_all($regexp, $compiledCss, $matches);
			$matches = $matches[1];
			$parts = preg_split($regexp, $compiledCss);
			$compiledCss = '';
			foreach ($parts as $i => $part) {
				$compiledCss .= $part;
				if (isset($matches[$i])) {
					$styles = preg_split('/[ \$]/', $matches[$i]);
					foreach ($styles as $style) {
						if (!empty($style)) {
							if (!preg_match('/^background-image/', $style)) {
								$compiledCss .= '$'.trim($style, '$').' ';
							} else {
								$compiledCss .= $style;
							}
						}
					}
				}
			}
			$compiledCss = str_replace('url##', 'url(', str_replace('##;', ');', $compiledCss));
			$shorts = array(
				'l' => 'left', 'r' => 'right', 't' => 'top', 'b' => 'bottom', 'w' => 'width', 'h' => 'height', 'z' => 'z-index',
				'p' => 'padding', 'pl' => 'padding-left', 'pr' => 'padding-right', 'pt' => 'padding-top', 'pb' => 'padding-bottom',
				'm' => 'margin', 'ml' => 'margin-left', 'mr' => 'margin-right', 'mt' => 'margin-top', 'mb' => 'margin-bottom',
				'fs' => 'font-size', 'lh' => 'line-height', 'br' => 'border-radius', 'mah' => 'max-height', 'mih' => 'min-height',
				'maw' => 'max-width', 'miw' => 'min-width', 'bp' => 'background-position'
			);
			foreach ($shorts as $k => $v) {
				$regexp = '/\$'.$k.' *(-*\#*[\d\._]+)(%)*/';
				$px = !in_array($k, array('z')) ? 'px' : '';
				$compiledCss = preg_replace($regexp, $v.":$1".$px."$2;", $compiledCss);
				$compiledCss = preg_replace('/([:\s])(\d+%*)_(?=\d)/', "$1$2px ", $compiledCss);
			}
			$shorts = array('c' => 'color', 'bc' => 'background-color', 'boc' => 'border-color');
			foreach ($shorts as $k => $v) {
				$regexp = '/\$'.$k.'\# *(\w{3,6})/';
				$compiledCss = preg_replace($regexp, $v.":#$1;", $compiledCss);
			}
			$shorts = array('bo' => 'border', 'bol' => 'border-left', 'bot' => 'border-top', 'bor' => 'border-right', 'bob' => 'border-bottom');
			foreach ($shorts as $k => $v) {
				$regexp = '/\$'.$k.'\# *(\w{3,6})/';
				$compiledCss = preg_replace($regexp, $v.":1px solid #$1;", $compiledCss);
			}

			$compiledCss = preg_replace('/\$rot(-*\d+)/', "transform:rotate($1deg);", $compiledCss);
			$compiledCss = preg_replace('/\$wh(\d+)(%)*/', "width:$1px$2;height:$1px$2;", $compiledCss);


			$compiledCss = str_replace('px%', '%', $compiledCss);
			$compiledCss = str_replace('%px', '%', $compiledCss);
			$compiledCss = preg_replace('/\s0(px|%)/', " 0", $compiledCss);
			$compiledCss = preg_replace('/:0(px|%)/', ":0", $compiledCss);
			$compiledCss = preg_replace('/;{2,}/', ';', $compiledCss);
			$regexp = '/\$\w+/';
			preg_match_all($regexp, $compiledCss, $matches);
			$matches = $matches[0];
			if (!empty($matches) && empty($cssconsts)) {
				error('���������� ������������� css ��������, �� �� ������ �� ���� ���� <b>.cssconst</b> ��� �� ��������<br>���������� ���� � ����� ������ � ������ ����������� � ����� ���������� ������ ����������<br><br><b>���������� ����� ������ ����� ���:</b><br><br>$white: #FFFFFF<br>$block: display: block;<br>$area: position: relative; margin: auto; background-color: #fff;<br><br><b>�������������:</b><br><br>.selector {<br>&nbsp;&nbsp;&nbsp;&nbsp;color: $white;<br>&nbsp;&nbsp;&nbsp;&nbsp;$block<br>&nbsp;&nbsp;&nbsp;&nbsp;$area<br>}');
			}
			$parts = preg_split($regexp, $compiledCss);
			$compiledCss = '';
			foreach ($parts as $i => $part) {
				$compiledCss .= $part;
				if (isset($matches[$i])) {
					if (!isset($cssconsts[trim($matches[$i], '$')])) {
						error('���������� ����������� css ��������� <b>'.$matches[$i].'</b>');
					}
					$compiledCss .= $cssconsts[trim($matches[$i], '$')];
				}
			}

			$cssClassIndex = array();
			if ($obfuscate === true) {
				$compiledCss = obfuscateCss($compiledCss, $cssClassIndex);
			}
			$compiledCss = preg_replace("/\t/", " ", $compiledCss);
			$compiledCss = preg_replace("/[\r\n]/", "", $compiledCss);
			$compiledCss = preg_replace("/\}/", "}\n", $compiledCss);
			$compiledCss = preg_replace("/ {1,}\{/", "{", $compiledCss);
			$compiledCss = preg_replace("/([:\{;,]) {1,}/", "$1", $compiledCss);
			$compiledCss = preg_replace("/\*\/[ \t]*([^\r\n])/", "*/\n$1", $compiledCss);
			createFile(DEFAULT_PATH.$pathToCompiledCss, $compiledCss);
		}

		$classNames = array();
		$doubles = array();
		$classesList = getComponentClassData();
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
		 	error('������� ������ � ����������� �������: <b>'.implode(', ', $doubles).'</b>');
		}

		// Checking application
		if (empty($classes['application'])) {
			error('����� � ����� <b>application</b> �� ������');
		}
		$appClasses = array_keys($classes['application']);
		if (count($appClasses) > 1) {
			error('������� ��������� ������� � ����� <b>application</b>');
		}
		$application = $classes['application'][$appClasses[0]];
		if (count($application['extends']) > 1) {
			error('����� <b>'.$appClasses[0].'</b> ����� ��� <b>application</b> � �� ����� ����������� ������� ��������');
		}

		// Checking views
		$views = $classes['view'];
		if (is_array($routes)) {
			foreach ($routes as $route) {
				if (!empty($route['view']) && !isset($views[$route['view']])) {
					error('����� <b>'.$route['view'].'</b> � ����� <b>view</b> ���������� � ��������� ������������ routes �� ������');
				}
			}
		}
		if (!empty($router['404'])) {
			if (!is_string($router['404'])) {
				error("�������� ������������ <b>router['404']</b> �� �������� �������");
			}
			if (!isset($views[$router['404']])) {
				error('����� <b>'.$router['404'].'</b> � ����� <b>view</b> ��������� ��� ��������� ������ 404 �� ������');
			}
		}

		// Checking components
		$usedComponents = array();
		$calledComponents = array();
		$filesOfUsedComponents = array();
		foreach ($templates as $filename => $file) {
			preg_match_all("/<component +[\"']*([^\"'\s>]+)/i", $file, $matches);
			foreach ($matches[1] as $match) {
				$usedComponents[] = $match;
				$calledComponents[$match] = 1;
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
							error("������������ � �������� �����-������ ��� <b>".$class['name']."</b>, ����� <b>".$superClass."</b> �� ������");
						}
						if ($classesList[$superClass]['type'] != $class['type']) {
							error("����� <b>".$class['name']."</b> �� ����� ���� ����������� �� ������ <b>".$superClass."</b>. ��� ������ ���� ����������� ����");
						}
						$usedComponents[] = $superClass;
					}
				}
			}
		}
		$usedComponents = array_reverse(array_unique($usedComponents));
		foreach ($usedComponents as $usedComponent) {
			if (!preg_match("/^[A-Z][a-zA-Z\d]+$/", $usedComponent)) {
				error("�������� ������ <b>".$usedComponent."</b> �� �������. ����������� ������ ���� <b>ClassName</b>");
			}
			$inClasses = '';
			if (!isset($classNames[$usedComponent])) {
				if (is_array($filesOfUsedComponents[$usedComponent])) {
					$ending = count($filesOfUsedComponents[$usedComponent]) > 1 ? '��' : '�';
					$inClasses = implode(', ', $filesOfUsedComponents[$usedComponent]);
				}
				if (!empty($inClasses)) {
					$error = "����� <b>".$usedComponent."</b> ���������� � ������� �����".$ending." <b>".$inClasses."</b> �� ������";
				} else {
					$error = "����� <b>".$usedComponent."</b> �� ������";
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
				error('� ����������� <b>'.$controller['name'].'</b> ����������� initial �������� <b>actions</b>.<br><br><b>�������� ������ ����� ���:</b> '.getInitialParamExample('actions'));
			}
			if (!preg_match('/\bload[\'"]*\s*:/i', $controller['initials']['actions'])) {
				error('� ����������� <b>'.$controller['name'].'</b> initial �������� <b>actions</b> ����������� ������ ��������� action <b>load</b>. ����������� ������ <b>initial actions = {"load": {"url": "items/load.php" ...}}</b>');
			}
		}
		$routerMenuClasses = $routerMenu;
		$types = array_keys($classes);
		foreach ($types as $type) {
			foreach ($classes[$type] as $className => $component) {
				$classesList[$className] = $component;
				if (array_search($className, $reservedNames) !== false) {
					error("�������� ������ <b>".$className."</b> ��������������� ��������");
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
							error("���������� <b>".$controller."</b> ���������� � ������ <b>".$component['name']."</b> �� ������");
						}
					}
				}
				if (is_array($component['helpers'])) {
					foreach ($component['helpers'] as $helper) {						
						if (!isset($sourcesList[$helper])) {
							error("������ <b>".$helper."</b> ���������� � ������ <b>".$component['name']."</b> �� ������");
						}
						$code = $sourcesList[$helper]['content'];
						if (!preg_match('/\bthis\.subscribe\s*=\s*function\b/', $code) && !preg_match('/\b'.$helper.'\.prototype\.subscribe\s*=\s*function\b/', $code)) {
							error("� ������� <b>".$helper."</b> ����������� � ������ <b>".$component['name']."</b> ����������� ����� <b>subscribe</b>");
						}
					}
				}
				if (is_array($component['dialogs'])) {
					if (is_array($component['dialogs'])) {
						foreach ($component['dialogs'] as $dialog) {
							if (!isset($classes['dialog'][$dialog])) {
								error("���������� ���� <b>".$dialog."</b> ���������� � ������ <b>".$component['name']."</b> �� �������");
							}
						}
					}
				}
				if (is_array($component['onActions'])) {
					foreach ($component['onActions'] as $componentAction) {
						$controller = $classes['controller'][$componentAction['controller']];
						$actions = $controller['actions'];
						if (!is_array($actions)) {
							$actions = array();
						}
						if (!isset($actions[$componentAction['action']])) {
							error('������� <b>'.$componentAction['action'].'</b> ���������� � initial ��������� <b>controllers</b> ������ <b>'.$component['name'].'</b> �� ������� � initial ��������� <b>actions</b> ����������� <b>'.$componentAction['controller'].'</b>');
						}
					}
				}
			}
		}
		foreach ($routeControllersToLoad as $routeControllersToLoad) {
			if (!is_array($classes['controller'][$routeControllersToLoad])) {
				error("���������� <b>".$routeControllersToLoad."</b> ���������� � ������������ ������� �� ������");
			}
		}
		if (!empty($routerMenuClasses)) {
			error("�����(�) <b>".implode(', ', $routerMenuClasses)."</b> ��������� � ��������� ������������ <b>router['menu']</b> �� �������");
		}
		if (!empty($tooltipClass) && !isset($classNames[$tooltipClass])) {
			error("����� <b>".$tooltipClass."</b> ��������� � ��������� ������������ <b>tooltipClass</b> �� ������");
		}

		$sourcesCache = array();
		if (!empty($texts)) {
			$textsIndex = array();
			$globals[] = "var __ = ".str_replace('"', "'", json_encode(getTextConstants($texts, $textsIndex))).';';
		}
		if (!empty($data)) {
			$dataIndex = array();
			$globals[] = "var __V = ".str_replace('"', "'", json_encode(getDataConstants($data, $dataIndex))).';';
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
		$globals[] = "var __VIEWCONTAINER = '->>".$viewContainer."';";
		$globals[] = "var __I = '".($advancedMode ? '_i' : 'initiate')."';";
		$globals[] = "var __GI = '".($advancedMode ? '_gi' : 'getInitials')."';";
		$globals[] = "var __TC = ".(!empty($tooltipClass) ? $tooltipClass : 'null').";";
		$globals[] = "var __TA = '".$tooltipApi."';";

		if (!empty($pathToApi)) {
			$globals[] = "var __APIDIR = '".$pathToApi."';";
		}
		$globals[] = "var __PAGETITLE = '".$pageTitle."';";
		if ($isUser) {
			$globals[] = createObjectString('__USEROPTIONS', $user, array('/\\\/', ''));
		}
		if (!empty($apiConfig)) {
			$apiConfigJson = transformIntoValidJson($apiConfig);
			$apiConfigObject = json_decode($apiConfigJson, true);
			if ($apiConfigObject === null) {
				error("���� ������������ ����� � api <b>config.js</b> �� ���������. ���������� ������ ����� ��� <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>");
			}
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
					if (!empty($component['tmpCallbacks'])) {
						foreach ($component['tmpCallbacks'] as $callback) {
							if (!hasComponentMethod($callback, $component)) {
								error("������ ������ ������ <b>".$callback."</b> ������ <b>".$component['name']."</b> �� ��� �������. ����� �� ������");
							}
						}
					}
				}
				if (!empty($component['initials'])) {
					addGetInitialsFunction($compiledJs, $className, $component['initials']);
				}
				if ($type == 'view') {
					addLoadControllerFunction($compiledJs, $className);
				}
				if (is_array($component['callbacks'])) {
					foreach ($component['callbacks'] as $callback) {
						if (!hasComponentMethod($callback, $component)) {
							error("���������� ������� <b>".$callback."</b> �� ������ ����� ������� ������ <b>".$component['name']."</b>");
						}
					}
				}
			}
		}

		foreach ($includes as $incl) {
			addGeneralTemplateFunction($compiledJs, $incl['content'], $incl['path']);
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
			'Component' => array('Application','View','Form','Control','Menu'),
			'Foreach' => array('Switch', 'IfSwitch')
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
		$controllers = array('Router', 'User');
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
		$compiledJs = preg_replace("/'<nq>/", '', $compiledJs);
		$compiledJs = preg_replace("/<nq>'/", '', $compiledJs);
		$compiledJs = preg_replace("/<nq>/", '', $compiledJs);
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
						error('��������� ��������� <b>'.$parts2[1].'</b> �� �������');
					}
					$compiledJs .= '__['.$index.']';
				}
			}
		}
		if (is_array($dataIndex)) {
			$regexp = '/[=:\[,]\s*\#\w+/';
			preg_match_all($regexp, $compiledJs, $matches);
			$codes = $matches[0];
			$parts = preg_split($regexp, $compiledJs);			
			$compiledJs = '';
			foreach ($parts as $i => $part) {
				$compiledJs .= $part;
				if (isset($codes[$i])) {
					$parts2 = explode('#', $codes[$i]);
					$index = array_search($parts2[1], $dataIndex);
					if (is_bool($index)) {
						error('��������� ������ <b>'.$parts2[1].'</b> �� �������');
					}
					$compiledJs .= $parts2[0].'__V['.$index.']';
				}
			}
		}
		if ($obfuscate === true) {
			$regexp = '/->>\s*([a-z][\w\-]{3,})/';
			preg_match_all($regexp, $compiledJs, $matches);
			$cssClasses = $matches[1];
			$parts = preg_split($regexp, $compiledJs);
			$compiledJs = '';
			foreach ($parts as $i => $part) {
				$compiledJs .= $part;
				if (isset($cssClasses[$i])) {
					if (!isset($cssClassIndex[$cssClasses[$i]])) {
						$cssClassIndex[$cssClasses[$i]] = addToCssClassIndex($cssClasses[$i]);
					}
					$compiledJs .= $cssClassIndex[$cssClasses[$i]];
				}
			}
		}
		preg_match_all('/CONFIG\.(\w+)\.(\w+)/', $compiledJs, $matches);
		foreach ($matches[1] as $i => $match) {
			if (empty($apiConfigObject[$match][$matches[2][$i]])) {
				error("�������� <b>CONFIG.".$match.".".$matches[2][$i]."</b> �� ������ � ����� ������������ <b>config.js</b>");
			}
		}
		
		$compiledJs = preg_replace('/->> {0,1}/', '', $compiledJs);
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

		if (!empty($config['scripts'])) {
			if (!is_string($config['scripts'])) {
				error("\n�������� ������������ <b>scripts</b> ������������, �� �� �������� �������.");
			}
			if (!is_dir($config['scripts'])) {
				error("\n����������, ��������� � ��������� ������������ <b>scripts</b> �� �������.");
			}
			$files = gatherFiles($config['scripts'], array(), true);
			$additionalContent = '';
			foreach ($files as $file) {
				if ($file['ext'] == 'js') {
					$additionalContent .= rtrim($file['content'], ';').';';
				}
			}
			if (!empty($additionalContent)) {
				$jsCompiledContent = file_get_contents(DEFAULT_PATH.$pathToCompiledJs);
				$jsCompiledContent = $additionalContent."\n\n".$jsCompiledContent;
				createFile(DEFAULT_PATH.$pathToCompiledJs, $jsCompiledContent);
			}
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

		include_once 'footer.php';
	?>