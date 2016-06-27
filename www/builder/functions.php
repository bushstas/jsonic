<?php
	
	$cssCounters = array(0,0,0);	
	$cssClassA = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m');
	$cssClassB = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9');
	$cssClassC = array('q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','0','1','2','3','4','5','6','7','8','9');
	shuffle($cssClassA);
	shuffle($cssClassB);
	shuffle($cssClassC);

	$textNodes = array();
	$propsShortcuts = array(
		'className' => 'c',
		'class' => 'c',
		'id' => 'i',
		'value' => 'v',
		'title' => 't',
		'placeholder' => 'p',
		'type' => 'tp',
		'href' => 'h',
		'src' => 's',
		'target' => 'tr',
		'method' => 'm',
		'style' => 'st',
		'width' => 'w',
		'height' => 'ht',
		'size' => 'sz',
		'maxlength' => 'mx',
		'action' => 'a',
		'name' => 'n',
		'scope' => 'sc',
		'role' => 'r',
		'cellpadding' => 'cp',
		'cellspacing' => 'cs'
	);
	$propsShortcutsFlipped = array_flip($propsShortcuts);

	function addTextNode($text) {
		global $textNodes;
		$index = array_search($text, $textNodes);
		if ($index !== false) {
			return $index;
		}
		$textNodes[] = $text;
		return count($textNodes) - 1;
	}

	function getTagShortcuts() {
		return array(
			'div', 'span', 'table', 'tbody', 'thead', 'tr', 'td', 'th', 'ul', 'ol', 'li', 'p', 'a', 'form', 'input', 'img', 'video', 'audio', 'aside',
			'article', 'b', 'big', 'blockquote', 'button', 'canvas', 'caption', 'center', 'code', 'col', 'colgroup', 'footer', 'font', 'h1', 'h2', 'h3',
			'h4', 'h5', 'h6', 'header', 'hr', 'i', 'iframe', 'label', 'menu', 'pre', 's', 'section', 'select', 'strong', 'textarea', 'u', 'small'
		);
	}

	$eventTypesShortcuts = array(
		'click','mouseover','mouseout','mouseenter','mouseleave','mousemove','contextmenu','dblclick','mousedown','mouseup','keydown','keyup','keypress','blur','change','focus',
		'focusin','focusout','input','invalid','reset','search','select','submit','drag','dragend','dragenter','dragleave','dragover','dragstart','drop','copy','cut','paste',
		'popstate','wheel','storage','show','toggle','touchend','touchmove','touchstart','touchcancel','message','error','open','transitionend','abort','play','pause','load',
		'durationchange','progress','resize','scroll','unload','hashchange','beforeunload','pageshow','pagehide'
	);	

	$solidMethods = array(
		'render' => 0,
		'processInitials' => ''
	);
	$methods = array(
		'onRendered'
	);


	function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	function gatherFiles($dir, $list, $getContent = false) {
		$extensions = array('js', 'css', 'template', 'texts', 'data', 'cssconst', 'include');
		if (is_dir($dir)) {
			$files = scandir($dir);
			if (is_array($files)) {
				foreach ($files as $file) {
					$path = $dir."/".$file;
					if (is_dir($path)) {
						if ($file != '..' && $file != '.') {
							$list = gatherFiles($path, $list, $getContent);
						}
					} elseif (file_exists($path)) {
						$path_info = pathinfo($path);
						$ext = strtolower($path_info['extension']);
    					if (array_search($ext, $extensions) !== false) {
							$data = array('path' => $path, 'ext' => $ext, 'filename' => $file, 'name' => $path_info['filename']);
							if ($getContent === true) {
								$data['content'] = file_get_contents($data['path']);
							}
							$list[] = $data;
						}
					}
				}
			}
		}
		return $list;
	}

	function createFile($path, $content) {
		$parts = explode('/', $path);
		if (count($parts) > 1) {
			$parts[count($parts) - 1] = '';
			$pathToFolder = implode('/', $parts);
			if (!is_dir($pathToFolder)) {
				createDir($pathToFolder);
			}
		}
		file_put_contents($path, $content);
	}

	function createDir($path) {
		$parts = explode('/', trim($path));
		$path = array();
		foreach ($parts as $part) {
			$path[] = $part;
			if (!empty($part) && $part != '.' && $parts != '..') {
				$currentPath = implode('/', $path);
				if (!is_dir($currentPath)) {
					mkdir($currentPath);
				}
			}
		}
	}

	function createViewDirs($routes, $pathToViews) {		
		foreach ($routes as $route) {
			createViewDir($pathToViews.'/'.$route['view'], $route['view']);
			if (is_array($route['children'])) {
				createViewDirs($route['children'], $pathToViews);
			}			
		}
	}

	function createErrorViewDirs($errorViews, $pathToViews) {
		global $pathToBlanks;
		if (is_array($errorViews)) {
			foreach ($errorViews as $errorCode => $errorViewName) {
				$defaultTemplateContent = '';
				$defaultCssContent = '';
				$pathToBlankErrorViewTemplate = $pathToBlanks.'/error'.$errorCode.'.template';
				if (file_exists($pathToBlankErrorViewTemplate)) {
					$defaultTemplateContent = file_get_contents($pathToBlankErrorViewTemplate);
				}
				$pathToBlankErrorViewCss = $pathToBlanks.'/error'.$errorCode.'.css';
				if (file_exists($pathToBlankErrorViewCss)) {
					$defaultCssContent = file_get_contents($pathToBlankErrorViewCss);
				}
				createViewDir($pathToViews.'/'.$errorViewName, $errorViewName, $defaultTemplateContent, $defaultCssContent);
			}
		}
	}

	function createViewDir($dir, $viewName, $templateContent = '', $cssContent = '') {
		if (!is_dir($dir)) {
			createDir($dir);
		}
		$pathToViewFile = $dir.'/'.$viewName.'.js';
		if (!file_exists($pathToViewFile)) {
			createFile($pathToViewFile, 'view '.$viewName.VIEW_DEFAULT_CODE);
		}
		$pathToViewTemplate = $dir.'/'.$viewName.'.template';
		if (!file_exists($pathToViewTemplate)) {
			if (empty($templateContent)) {
				$templateContent = VIEW_DEFAULT_TEMPLATE_CONTENT;
			}
			createFile($pathToViewTemplate, $templateContent);
		}
		if (!empty($cssContent)) {
			$pathToViewCss = $dir.'/'.$viewName.'.css';
			if (!file_exists($pathToViewCss)) {
				createFile($pathToViewCss, $cssContent);
			}
		}
	}

	function checkErrorRoute($route, $name) {
		if (!is_string($route)) {
			error("Параметр <b>router['".$name."']</b> не является строкой");
		}
		if (preg_match('/[^\w]/', $route)) {
			error("Параметр <b>router['".$name."']</b> = '<b>".$route."</b>' содержит запрещенные символы");
		}
		if (!preg_match('/^[A-Z]\w*/', $route)) {
			error("Параметр <b>router['".$name."']</b> = '<b>".$route."</b>' не соответствует паттерну [A-Z]\w+");
		}		
	}

	function validateRoutes($routes, &$routeControllersToLoad, &$routeControllersByViews) {
		foreach ($routes as $route) {
			$routeControllersByViews[$route['view']] = array();
			if (!is_array($route)) {
				error("Один из пунктов параметра конфигурации <b>routes</b> не является массивом");
			}
			if (empty($route['name']) || !is_string($route['name'])) {
				error("Параметр name = '<b>".$route['name']."</b>' одного из пунктов <b>routes</b> отсутствует или не является строкой");
			}
			if (empty($route['view']) || !is_string($route['view'])) {
				error("Параметр view = '<b>".$route['view']."</b>' у маршрута с именем <b>".$route['name']."</b> отсутствует или не является строкой");	
			}
			if (preg_match('/[^\w]/', $route['name'])) {
				error("Параметр name = '<b>".$route['name']."</b>' одного из пунктов <b>routes</b> содержит запрещенные символы");
			}
			if (!preg_match('/^[a-z]\w*/', $route['name'])) {
				error("Параметр name = '<b>".$route['name']."</b>' одного из пунктов <b>routes</b> не соответствует паттерну <b>[a-z]\w+</b>");
			}			
			if (preg_match('/[^\w]/', $route['view'])) {
				error("Параметр view = '<b>".$route['view']."</b>' у маршрута с именем <b>".$route['name']."</b> содержит запрещенные символы");
			}
			if (!preg_match('/^[A-Z]\w*/', $route['view'])) {
				error("Параметр view = '<b>".$route['view']."</b>' у маршрута с именем <b>".$route['name']."</b> не соответствует паттерну <b>[A-Z]\w+</b>");
			}
			if (isset($route['title'])) {
				if (!is_string($route['title'])) {
					error("Параметр <b>title</b> у маршрута с именем <b>".$route['name']."</b> не является строкой");
				}
				if (preg_match('/\$/', $route['title']) && !preg_match('/^\$[a-z]\w+$/', $route['title'])) {
					error("Значение title = '<b>".$route['title']."</b>' у маршрута с именем <b>".$route['name']."</b> содержащее символ $ не соответствует паттерну <b>^\\$[a-z]\w+$</b>");
				}				
			}
			if (isset($route['accessLevel'])) {
				if (!is_int($route['accessLevel'])) {
					error("Параметр <b>accessLevel</b> у маршрута с именем <b>".$route['name']."</b> не является числом");
				}
			}
			if (isset($route['params'])) {
				if (!is_array($route['params'])) {
					error("Параметр <b>params</b> у маршрута с именем <b>".$route['name']."</b> не является ассоциативным массивом");
				}
				foreach ($route['params'] as $key => $value) {
					if (!is_string($key)) {
						error("Ключ <b>params[".$key."]</b> у маршрута с именем <b>".$route['name']."</b> не является строкой");
					}
					if (preg_match('/[^\w]/', $key)) {
						error("Ключ <b>params[".$key."]</b> у маршрута с именем <b>".$route['name']."</b> содержит запрещенные символы");
					}
					if (preg_match('/\$/', $value) && !preg_match('/^\$\d+$/', $value)) {
						error("Значение <b>params[".$key."]</b> у маршрута с именем <b>".$route['name']."</b> содержащее символ $ не соответствует паттерну <b>^\\$\d+$</b>");
					}
				}
			}
			if (isset($route['load'])) {
				if (!is_array($route['load'])) {
					error("Параметр <b>load</b> у маршрута с именем <b>".$route['name']."</b> не является массивом");
				}
				foreach ($route['load'] as $controllerToLoad) {
					if (!is_string($controllerToLoad)) {
						error("Один из элементов параметра <b>load</b> у маршрута с именем <b>".$route['name']."</b> не является строкой");
					}
					if (empty($controllerToLoad)) {
						error("Один из элементов параметра <b>load</b> у маршрута с именем <b>".$route['name']."</b> пуст");
					}
					$routeControllersByViews[$route['view']][] = $controllerToLoad;
					$routeControllersToLoad[] = $controllerToLoad;
				}
			}
			if (isset($route['children'])) {
				if (!is_array($route['children'])) {
					error("Параметр <b>children</b> одного из пунктов routes не является массивом");
				}
				validateRoutes($route['children'], $routeControllersToLoad, $routeControllersByViews);
			}
		}
		$routeControllersToLoad = array_unique($routeControllersToLoad);
	}

	function isRoute($routeName, $routes) {
		foreach ($routes as $route) {
			if ($route['name'] == $routeName) {
				return true;
			}
			if (is_array($route['children']) && isRoute($routeName, $route['children'])) {
				return true;
			}
		}
		return false;
	}

	function parseJsClass($content, &$classes, $file) {
		$content = trim($content);
		$parts = preg_split('/\n/', $content);
		$originalFirstLine = trim($parts[0]);
		$firstLine = trim(preg_replace('/\s*,\s*/', ',', $originalFirstLine), ';');
		if (empty($content)) {
			error("Файл <b>".$file['path']."</b> пуст");
		}
		if (preg_match('/[а-я]/si', $firstLine)) {
			error("Недопустимые кириллические символы в первой строке файла <b>".$file['path']."</b>");
		}
		$lineParts = preg_split('/\s+/', $firstLine);
		$classType = strtolower($lineParts[0]);
		
		if (!preg_match('/^[a-z]+$/', $classType)) {
			error("Недопустимые символы в ключевом слове <b>".$classType."</b> определяющем тип класса в файле <b>".$file['path']."</b>");
		}
		if (!is_array($classes[$classType])) {
			if (!isset($lineParts[1])) {
				error('Отсутствует корректное определение класса <b>'.$file['name'].'</b> в файле <b>'.$file['path'].'</b>');	
			}
			error('Неизвестный тип класса <b>'.$classType.'</b> в файле <b>'.$file['path'].'</b>.<br>Допустимые значения: <b>application</b>, <b>component</b>, <b>view</b>, <b>controller</b>, <b>dialog</b>, <b>form</b>, <b>menu</b>, <b>control</b>');
		}
		$classNameRegExp = "/^[A-Z][a-zA-Z\d]+$/";
		$className = $lineParts[1];
		if (!preg_match($classNameRegExp, $className)) {
			error("Название класса <b>".$className."</b> недопустимо. Используйте запись вида <b>ClassName</b>");
		}
		if (isset($lineParts[2]) && $lineParts[2] != 'extends') {
			error("Недопустимое ключевое слово <b>".$lineParts[2]."</b> в первой строке файла <b>".$file['path']."</b>. Ожидается ключевое слово <b>extends</b>");
		}
		$extends = array();
		if (isset($lineParts[3])) {
			$extendsString = $lineParts[3];
			$extends = explode(',', $extendsString);
		}
		if (isset($lineParts[4])) {
			error("Недопустимое определение класса <b>".$originalFirstLine."</b> в файле <b>".$file['path']."</b>");
		}
		unset($parts[0]);
		$content = implode("\n", $parts);
		if ($className != $file['name']) {
			error('Файл <b>'.$file['path'].'</b> должен содержать класс <b>'.$file['name'].'</b>, тогда как содержит класс с именем <b>'.$className.'</b>');
		}

		$properExtends = array();
		foreach ($extends as $superClassName) {
			if (!preg_match($classNameRegExp, $superClassName)) {
				error("Название супер-класса <b>".$superClassName."</b> для ".$className." недопустимо. Используйте запись вида <b>ClassName</b>");
			}
			$properExtends[] = $superClassName;
		}
		array_unshift($properExtends, ucfirst($classType));
		$classes[$classType][$className] = array(
			'content' => $content,
			'extends' => $properExtends,
			'name'    => $className,
		);		
	}

	function removeQuotedText($text) {
		return preg_replace('/"[^"]+"/', '"1"', preg_replace("/'[^']+'/", '"1"', $text));
	}

	function parseClassInitials(&$components) {
		$availableInitials = array('loader', 'controllers', 'props', 'globals', 'actions', 'options', 'args', 'helpers', 'followers');

		foreach ($components as &$component) {
			$component['controllers'] = array();
			$component['callbacks'] = array();
			$component['initials'] = array();
			$content = $component['content'].'@EOF';
			preg_match_all('/\binitial\s+([\s\S]+?)(?=(initial|function|@EOF))/', $content, $matches);
			$initials = $matches[1];
			foreach ($initials as $initial) {
				$initial = trim($initial);
				preg_match_all('/^([a-zA-Z]\w*)\s*=\s*([\s\S]+?)[;\s]*$/', $initial, $matches);
				$initialType = trim($matches[1][0]);
				$initialValue = trim($matches[2][0]);

				$error = 'Ошибка в парсинге кода initial параметра в классе <b>'.$component['name'].'</b><xmp>initial '.$initial.'</xmp>Описание должно иметь вид <b>initial props = {...}</b> или <b>initial controllers = [...]</b>';
				if (empty($initialType) || empty($initialValue)) {
					error($error);
				}
				if (!in_array($initialType, $availableInitials)) {
					error('Неизвестный initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b>');
				}
				$initial = parseInitialValue($initialType, $initialValue, $component);
				if (!empty($initial)) {
					$component['initials'][$initialType] = $initial;
				}
				$component['content'] = preg_replace('/(\b)initial\s+(\w+\s*=\s*[\{\[][\s\S]+?)(?=(initial|function|@EOF))/', "$1", $content);
				$component['content'] = str_replace('@EOF', '', $component['content']);
			}
		}
	}

	function parseInitialValue($initialType, $initialValue, &$component) {
		$object = getInitialsObject($initialValue, $initialType, $component);
		$initials = $object['initials'];
		$component['binds'] = $object['binds'];

		switch ($initialType) {
			case 'args':
			case 'options':
			case 'props':
				validateDefaultInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'actions':
				validateActionsInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'loader':
				validateLoaderInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'globals':
				validateGlobalsInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'helpers':
				validateHelpersInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'followers':
				validateFollowersInitials($initials, $initialValue, $initialType, $component);
			break;

			case 'controllers':
				validateControllersInitials($initials, $initialValue, $initialType, $component);
			break;
		}

		return $initialValue;
	}

	function isAssocArray($obj, $value = '') {
		if (!is_array($obj) || $value[0] == '[') return false;
		if (empty($obj)) return true;
		$keys = array_keys($obj);
		return is_string($keys[0]);
	}

	function isArray($arr, $value = '') {
		if (!is_array($arr) || $value[0] == '{') return false;
		if (empty($obj)) return true;
		$keys = array_keys($obj);
		return is_int($keys[0]);
	}

	function initialAssocArrayTypeError($initialValue, $initialType, $component) {
		error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> должен быть ассоциативным массивом (объектом).<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
	}

	function initialArrayTypeError($initialValue, $initialType, $component) {
		error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> должен быть простым массивом.<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
	}

	function validateObjectFields($obj, $initialValue, $initialType, $component) {
		if (is_array($obj)) {
			foreach ($obj as $key => $value) {
				if (!preg_match('/^[a-z]\w*$/', $key)) {
					error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$key.'</b> не удовлетворяющее паттерну <b>^[a-z]\w*$</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp>');
				}
				if (isAssocArray($value)) {
					validateObjectFields($value, $initialValue, $initialType, $component);
				}
			}
		}
	}

	function validateCallback($callback, $initialValue, $initialType, &$component, $field, $name = '', $value = '') {
		if (empty($callback)) return;
		if ($callback !== null && $callback !== false && $callback !== '') {
			$callback = str_replace('__BIND__', '', $callback);
			if (!preg_match('/^this\.[a-z]\w*$/i', $callback)) {
				if (!empty($name)) {
					error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$field.'</b>, в котором параметр <b>'.$name.'</b> имеет некорректное значение <b>'.$callback.'</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
				} else {
					error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$field.'</b> с некорректным значением <b>'.$value.'</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp>Ожидается функция обработчик вида <b>this.'.($initialType == 'globals' ? 'onChangeGlobalVar' : 'onChangeSomeProp').'</b><br><br><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));					
				}
			}
		}
		$binds = $component['binds'];
		if (is_array($binds)) {
			foreach ($binds as $bind) {
				$parts = preg_split('/\s*,\s*/', $bind);
				if ($parts[0] != 'this' && $parts[0] != 'null') {

					error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$field.'</b>, в котором параметр <b>'.$name.'</b> имеет некорректный первый аргумент <b>'.$bind.'</b> вызова bind  для метода <b>'.$callback.'</b><br><br>Ожидается <b>this</b> или <b>null</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
				}
			}
			for ($i = 1; $i < count($parts); $i++) {
				$part = removeQuotedText($parts[$i]);
				if (!preg_match('/^@\w+$/', $part) && json_decode("[".$part."]") === null) {
					error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$field.'</b>, в котором параметр <b>'.$name.'</b> имеет некорректный аргумент <b>'.$parts[$i].'</b> вызова bind  для метода <b>'.$callback.'</b><br><br>Ожидается JSON валидная запись или текстовая контстанта вида <b>@shortcut</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
				}
			}
		}
		$callback = str_replace('__BIND__', '', str_replace('this.', '', $callback));
		$component['callbacks'][] = $callback;
	}

	function validateDefaultInitials($initials, $initialValue, $initialType, $component) {
		global $componentLikeClassTypes;
		if (($initialType == 'args' || $initialType == 'props') && !in_array($component['type'], $componentLikeClassTypes)) {
			error('Класс <b>'.$component['name'].'</b> с типом <b>'.$component['type'].'</b> не может содержать initial параметр <b>'.$initialType.'</b>');
		}
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initials, $initialValue, $initialType, $component);
	}

	function validateActionsInitials($initials, $initialValue, $initialType, &$component) {
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initials, $initialValue, $initialType, $component);
		foreach ($initials as $key => $value) {
			if (!isAssocArray($value, $initialValue)) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$key.'</b>, которое не является ассоциативным массивом<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			if (empty($value)) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$key.'</b>, которое является пустым<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			if (empty($value['url'])) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>'.$key.'</b>, в котором отсутствует параметр <b>url</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			validateCallback($value['callback'], $initialValue, $initialType, $component, $key, 'callback');
		}
		$component['actions'] = $initials;
	}

	function validateLoaderInitials($initials, $initialValue, $initialType, &$component) {
		global $componentLikeClassTypes;
		if (!in_array($component['type'], $componentLikeClassTypes)) {
			error('Класс <b>'.$component['name'].'</b> с типом <b>'.$component['type'].'</b> не может содержать initial параметр <b>loader</b>');
		}
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initial, $initialValues, $initialType, $component);
		if (empty($initials['controller'])) {
			error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> не имеет поля <b>controller</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
		}
		if (isset($initials['async']) && !is_bool($initials['async'])) {
			error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>async</b>, но его тип не <b>boolean</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
		}
		if (isset($initials['options']) && !isAssocArray($initials['options'])) {
			error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет поле <b>options</b>, но он не является ассоциативным массивом<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
		}
		$component['controllers'][] = $initials['controller'];
	}

	function validatePropsInitials($initials, $initialValue, $initialType, $component) {
		global $componentLikeClassTypes;
		if (!in_array($component['type'], $componentLikeClassTypes)) {
			error('Класс <b>'.$component['name'].'</b> с типом <b>'.$component['type'].'</b> не может содержать initial параметр <b>props</b>');
		}
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initials, $initialValue, $initialType, $component);
	}

	function validateGlobalsInitials($initials, $initialValue, $initialType, &$component) {
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initials, $initialValue, $initialType, $component);
		foreach ($initials as $key => $value) {
			validateCallback($value, $initialValue, $initialType, $component, $key, '', $value);
		}
	}


	function validateHelpersInitials($initials, $initialValue, $initialType, &$component) {
		global $componentLikeClassTypes;
		if (!in_array($component['type'], $componentLikeClassTypes)) {
			error('Класс с типом <b>'.$component['type'].'</b> не может содержать initial параметр <b>helpers</b>');
		}
		if (!isArray($initials, $initialValue)) {
			initialArrayTypeError($initialValue, $initialType, $component);
		}
		foreach ($initials as $i => $value) {
			if (!isAssocArray($value)) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет элемент с индексом <b>'.$i.'</b>, который не является ассоциативным массивом<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			if (empty($value['helper'])) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет элемент с индексом <b>'.$i.'</b>, у которого отсутствует параметр <b>helper</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			validateCallback($value['callback'], $initialValue, $initialType, $component, $i, 'callback');
		}
	}

	function validateFollowersInitials($initials, $initialValue, $initialType, &$component) {
		global $componentLikeClassTypes;
		if (!in_array($component['type'], $componentLikeClassTypes)) {
			error('Класс с типом <b>'.$component['type'].'</b> не может содержать initial параметр <b>followers</b>');
		}
		if (!isAssocArray($initials, $initialValue)) {
			initialAssocArrayTypeError($initialValue, $initialType, $component);
		}
		validateObjectFields($initials, $initialValue, $initialType, $component);
		foreach ($initials as $key => $value) {
			validateCallback($value, $initialValue, $initialType, $component, $key, '', $value);
		}
	}

	function validateControllersInitials($initials, $initialValue, $initialType, &$component) {
		if (!isArray($initials, $initialValue)) {
			initialArrayTypeError($initialValue, $initialType, $component);
		}
		$component['onActions'] = array();
		foreach ($initials as $i => $value) {
			validateObjectFields($value, $initialValue, $initialType, $component);
			if (!isAssocArray($value)) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет элемент с индексом <b>'.$i.'</b>, который не является ассоциативным массивом<xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			if (empty($value['controller'])) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет элемент с индексом <b>'.$i.'</b>, у которого отсутствует параметр <b>controller</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			if (empty($value['on'])) {
				error('Initial параметр <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b> имеет элемент с индексом <b>'.$i.'</b>, у которого отсутствует параметр <b>on</b><xmp>initial '.$initialType.' = '.$initialValue.'</xmp><b>Параметр должен иметь вид:</b> '.getInitialParamExample($initialType));
			}
			validateObjectFields($value['on'], $initialValue, $initialType, $component);
			foreach ($value['on'] as $action => $callback) {
				validateCallback($callback, $initialValue, $initialType, $component, $i, 'callback');
				$component['onActions'][] = array('controller' => $value['controller'], 'action' => $action);
			}
			$component['controllers'][] = $value['controller'];
		}
	}

	function transformIntoValidJson($value) {
		$regexp = '/\s*([\{\}\[\],:])\s*/';
		preg_match_all($regexp, $value, $signs);
		$signs = $signs[1];
		
		$parts = preg_split($regexp, $value);
		foreach ($parts as $i => &$part) {
			if (!empty($part) && $parts != 'null' && $part != 'false' && $part != 'true' && !is_numeric($part)) {
				$part = trim(trim($part, '"'), "'");
				$part = '"'.str_replace('"', "'", $part).'"';
			}
		}
		$text = '';
		for ($i = 0; $i < count($parts); $i++) {
			$text .= $parts[$i];
			if (isset($signs[$i])) {
				$text .= $signs[$i];
			}
		}		
		return $text;
	}

	function getInitialsObject($initialValue, $initialType, &$component) {
		$originalValue = $initialValue;
		$initialValue = trim(preg_replace('/[\r\n\t]/', '', $initialValue));
		if ($initialValue == '{}' || $initialValue == '[]') {
			return array('initials' => array(), 'binds' => array());
		}
		$initialValue = preg_replace('/:\s+/', ':', $initialValue);
		$regexp = '/\.bind\(([^\)]+)\)/';		
		preg_match_all($regexp, $initialValue, $binds);
		$binds = $binds[1];
		$text = transformIntoValidJson(preg_replace($regexp, '__BIND__', $initialValue));
		
		$initialsObject = json_decode($text, true);
		if ($initialsObject === null) {
			$example = getInitialParamExample($initialType);
			error('Ошибка в парсинге кода initial параметра <b>'.$initialType.'</b> в классе <b>'.$component['name'].'</b><xmp>initial '.$initialType.' = '.$originalValue.'</xmp><b>Параметр должен иметь вид:</b> '.$example);
		}
		return array('initials' => $initialsObject, 'binds' => $binds);
	}

	function getInitialParamExample($type) {
		switch ($type) {
			case 'actions':
				return "<xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'GET',\n\t\t'callback': this.onLoad\n\t}\n}</xmp><b>или</b><xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'POST',\n\t\t'callback': this.onLoad.bind(this, ...args)\n\t}\n}</xmp>";
			case 'loader':
				return "<xmp>initial loader = {\n\t'controller': ControllerClass,\n\t'async': true,\n\t'options': {\n\t\t'key': 'value'\n\t}\n}</xmp>";
			case 'options':
				return "<xmp>initial options = {\n\t'key': 'id',\n\t'store': true,\n\t'storeAs': 'items',\n\t'storePeriod': '1day',\n\t'clone': true\n}</xmp>";
			case 'args':
				return "<xmp>initial args = {\n\t'name': 'Name',\n\t'price': 1000,\n\t'tags': ['tag1', 'tag2']\n}</xmp>";
			case 'globals':
				return "<xmp>initial globals = {\n\t'userOnline': this.onChangeUserOnlineStatus,\n\t'siteBackground': this.onChangeSiteBackground.bind(this, true)\n}</xmp>";
			case 'props':
				return "<xmp>initial props = {\n\t'width': 100,\n\t'color': '#FFFFFF'\n}</xmp>";
			case 'helpers':
				return "<xmp>initial helpers = [\n\t{\n\t\t'helper': HelperClass,\n\t\t'callback': this.handleHelperClass,\n\t\t'options': {\n\t\t\t...\n\t\t}\n\t}\n]</xmp>";
			case 'followers':
				return "<xmp>initial followers = {\n\t'somePropName': this.onChangeSomeProp,\n\t'somePropName2': this.onChangeSomeProp2\n}</xmp>";
			case 'controllers':
				return "<xmp>initial controllers = [\n\t{\n\t\t'controller': ControllerClass,\n\t\t'on': {\n\t\t\t'load': this.onLoad,\n\t\t\t'add': this.onAdd\n\t\t}\n\t}\n]</xmp>";
		
		}
	}

	function parseClassFunctions(&$components) {		
		foreach ($components as &$component) {			
			$code = 'function(){}'.trim($component['content']);
			$code = preg_replace("/@(\w+)/", "__.$1", $code);
			$braces = preg_replace("/[^\{\}]/", "", $code);
			$parts = preg_split("/[\{\}]/", $code);
						
			$properParts = array();
			for ($i = 0; $i < count($parts); $i++) {
				$properParts[] = $parts[$i];
				if (!empty($braces[$i])) {
					$properParts[] = $braces[$i];
				}
			}
			$temp = '';
			$opening = 0;
			$closing = 0;
			$functions = array();
			$functionList = array();
			$functionName = "__constructor";
			for ($i = 1; $i < count($properParts); $i++) {				
				$part = $properParts[$i];
				if ($part == '{') {
					$opening++;
					if ($opening == 1) {
						$part = "";
					}
				} elseif ($part == '}') {
					$closing++;
				}
				if ($opening > 0 && $opening == $closing) {
					$functions[] = array('name' => $functionName, 'args' => $arguments, 'code' => $temp);
					$functionList[] = $functionName;
					$nextPart = $properParts[$i + 1];
					if (preg_replace("/[\s;]/", "", $nextPart) != "") {
						preg_match_all("/[\s;]*function {1,}(\w{1,}) *\(([^\)]*)\) *$/", $nextPart, $matches);
						$functionName = $matches[1][0];
						$arguments = $matches[2][0];
						$i++;
						
						if ($properParts[$i + 2] == '{' || empty($functionName)) {
							error("Ошибка в валидации кода класса <b>".$className.'</b>');
						}
					}
					$opening = 0;
					$closing = 0;
					$temp = '';
				} else {
					$temp .= $part;
				}
			}
			checkSolidMethodsUsing($functions, $component);
			checkSuperClassesCallings($functions, $component);
			$component['functions'] = $functions;
			$component['functionList'] = $functionList;
			unset($component['content']);
			foreach ($component['callbacks'] as $callback) {
				if (!in_array($callback, $functionList)) {
					error("Обработчик события <b>".$callback."</b> не найден среди методов класса <b>".$component['name']."</b>");
				}
			}
		}
	}

	function checkSolidMethodsUsing($functions, $component) {
		global $componentLikeClassTypes, $solidMethods, $methods;
		$isComponentLike = in_array($component['type'], $componentLikeClassTypes);
		foreach ($functions as $func) {
			if ($isComponentLike) {
				if (isset($solidMethods[$func['name']])) {
					if (is_numeric($solidMethods[$func['name']])) {
						error("Класс <b>".$component['name']."</b> не может иметь метод <b>".$func['name']."</b>, т.к. он приватный и наследуется от класса <b>Component</b>. Вместо этого используйте метод <b>".$methods[$solidMethods[$func['name']]]."</b>");
					} elseif (!empty($solidMethods[$func['name']])) {
						error("Класс <b>".$component['name']."</b> не может иметь метод <b>".$func['name']."</b>, т.к. он приватный и наследуется от класса <b>Component</b>. Вместо этого используйте ".$solidMethods[$func['name']]);
					} else {
						error("Класс <b>".$component['name']."</b> не может иметь метод <b>".$func['name']."</b>, т.к. он приватный и наследуется от класса <b>Component</b>");
					}
				}
			}
		}
	}

	function checkSuperClassesCallings(&$functions, $component) {
		foreach ($functions as &$func) {
			$regExp = '/\bsuper\(([^\)]*)\)/';
			$parts = preg_split($regExp, $func['code']);
			if (count($parts) > 1) {
				preg_match_all($regExp, $func['code'], $matches);
				$callings = $matches[1];
				foreach ($callings as &$call) {
					$arguments = '';
					$errorText = 'Ошибка вызова <b>super('.$call.')</b> в методе <b>'.$func['name'].'</b> класса <b>'.$component['name'].'</b>. ';
					if (empty($call)) {
						$superClass = getSuperClassWithMethod($func['name'], $component, $errorText);
					} else {
						$callOpts = getArgumentsOfSuperClassCalling($call, $func['name'], $errorText);
						$arguments = $callOpts['args'];
						$superClass = $callOpts['superClass'];
					}
					$call = $superClass.'.prototype.'.$func['name'].'.call(this'.$arguments.')';
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

	function getArgumentsOfSuperClassCalling($call, $funcName, $errorText) {
		global $classesList;
		$args = explode(',', $call);
		$superClass = trim($args[0]);
		$args[0] = '';
		$arguments = implode(',', $args);
		if (!isset($classesList[$superClass])) {
			error($errorText."Супер-класс <b>".$superClass."</b> не найден");
		}
		$code = $classesList[$superClass]['content'];
		if (empty($code) || !preg_match('/\bfunction +'.$funcName.'*\(/', $code)) {
			error($errorText."Данный метод отсутствует у супер-класса <b>".$superClass.'</b>');
		}
		return array('args' => $arguments, 'superClass' => $superClass);
	}

	function getSuperClassWithMethod($funcName, $component, $errorText) {
		global $classesList;
		global $sourcesList;
		$extends = $component['extends'];
		if (empty($extends) || !is_array($extends)) {
			error($errorText."У данного класса отсутствуют супер-классы");
		}
		$superClasses = array();
		foreach ($extends as $className) {
			$code = $classesList[$className]['content'];
			if (!empty($code) && preg_match('/\bfunction +'.$funcName.'*\(/', $code)) {
				$superClasses[] = $className;
			}
			$code = $sourcesList[$className]['content'];
			if (!empty($code) && preg_match('/\bprototype\.'.$funcName.'\b/', $code)) {
				$superClasses[] = $className;
			}
		}
		if (empty($superClasses)) {
			error($errorText."Данный метод не найден у супер-классов");
		} else if (count($superClasses) > 1) {
			error($errorText."У данного класса есть несколько супер-классов с данным методом. Используйте запись <b>super(ClassName)</b>");
		}
		return $superClasses[0];
	}

	function hasParentMainTemplate($component) {
		global $classes;
		global $templates;
		if (!is_array($component['extends']) || empty($component['extends'])) {
			return false;
		}
		foreach ($classes as $classesOfType) {
			foreach ($classesOfType as $name => $class) {
				$template = $templates[$name];
				if (!empty($template) && in_array($name, $component['extends'])) {
					if (preg_match("/\{template +\.main *\}/", $template) || hasParentMainTemplate($class)) {
						return true;
					}
				}
			}
		}
		return false;
	}
 
	function getTemplateFunctions($template, $component, $class = null) {
		global $calledComponents;
		$template = preg_replace('/[\t\r\n]/', '', $template);
		$template = preg_replace('/ {2,}/', ' ', $template);
		$template = preg_replace('/&nbsp;/', '\u00A0', $template);

		$regexp = "/\{template +\.(\w+) *\}/";
		preg_match_all($regexp, $template, $matches);
		$templateNames = $matches[1];
		if (!empty($templateNames)) {
			if (!empty($class) && !preg_match("/\{template +\.main *\}/", $template) && !hasParentMainTemplate($component) && in_array($component['name'], $calledComponents)) {
				error('Шаблон <b>main</b> класса <b>'.$class.'</b> не найден среди прочих');
			}
			$templateContents = preg_split($regexp, $template);
			array_shift($templateContents);
		} else {
			$templateNames = array('main');
			$templateContents = array($template);
		}
		
		$templates = array();
		for ($i = 0; $i < count($templateNames); $i++) {
			$templates[] = getParsedTemplate($templateContents[$i], $templateNames[$i], $regexp, $component); 
		}

		$isSingle = count($templates) == 1;
		$templateFunctions = array();
		foreach ($templates as $template) {
			$data = json_encode($template['children']);
			if ($data == '[]') {
				$data = ' null';
			} else {
				$data = str_replace('"', "'", $data);
		
				$data = str_replace("\'", "'", $data);

				$data = preg_replace("/'<nq>/", '', $data);
				$data = preg_replace("/<nq>'/", '', $data);
				$data = preg_replace("/<nq>/", '', $data);

				$data = str_replace('<plus>', "'+", $data);
				$data = str_replace('<\/plus>', "+'", $data);
				$data = str_replace('\\', '', $data);
				
				$data = preg_replace("/\[*<function>\[*(',)*/", 'function(){return ', $data);
				$data = preg_replace("/(,')*\]*<\/function>\]*/", '}', $data);

				$data = preg_replace("/\[*<function_returns_array>\[*(',)*/", 'function(){return[', $data);
				$data = preg_replace("/(,')*\]*<\/function_returns_array>\]*/", ']}', $data);

				$data = preg_replace("/\['<foreach ([^>]+)>',/", "(function($1){return[", $data);
				$data = preg_replace("/,*'<\/foreach>'\]/", ']}).bind(this)', $data);

				$data = preg_replace("/''\+|\+''/", "", $data);
				
				$data = preg_replace("/([^\d])\.(\d+)/", "$1[$2]", $data);

				$data = preg_replace("/return\[(\d+)\]/", "return $1", $data);
			}
			$templateFunctions[] = array('content' => $data, 'name' => $template['name']);
		}
		return $templateFunctions;
	}

	function getParsedTemplate($content, $name, $regexp, $component) {
		$html = preg_replace($regexp, '', $content);
		$parts = preg_split('/\{\/template\}/', $html);
		$html = $parts[0];

		$regexp = "/(<\/*[a-z]+[^>]*>|\{\/*foreach[^\}]*\}|\{\/*if[^\}]*\}|\{else\})/i";
		preg_match_all($regexp, $html, $matches);
		$tags = $matches[1];			
		$parts = preg_split($regexp, $html);
		
		$list = array();
		for ($j = 0; $j < count($parts); $j++) {
			$part = $parts[$j];
			if (!empty($part)) {
				$list[] = array('type' => 'text', 'content' => $part);
			}
			if (isset($tags[$j])) {
				preg_match("/^[<\{]\/*([a-z]+\d*) */i", $tags[$j], $match);
				$tagName = strtolower($match[1]);
				$tagContent = $tags[$j];
				$isClosing = isTagClosing($tagName, $tagContent);
				$list[] = array('type' => 'tag', 'content' => $tagContent, 'tagName' => $tagName, 'isClosing' => $isClosing);
			}
		}
		return array('name' => $name, 'children' => getHtmlChildren($list, $component));
	}

	function isTagClosing($tagName, $tagContent) {
		if (isSimpleTag($tagName)) return false;
		return preg_match("/^[<\{]\//", $tagContent);
	}

	function getHtmlChildren($list, $component) {
		if (empty($list)) {
			return array();
		}
		$children = array();
		$elseChildren = array();
		$currentList = array();
		$isElse = false;
		$currentIf = null;
		for ($i = 0; $i < count($list); $i++) {
			$item = $list[$i];
			$tagName = $item['tagName'];
			if ($item['type'] == 'text') {
				parseTextNode($item['content'], $children, $component);
			} elseif ($tagName == 'br') {
				$children[] = '<br>';
			} elseif (!$item['isClosing']) {
				if ($tagName == 'else') {
					$isElse = true;
				}
				$content = $item['content'];
				if (isSimpleTag($tagName))
				{
					$child = array('t' => getTagIndex($tagName));
					getTagProperties($item['content'], $child, $component);
				}
				elseif ($tagName == 'template')
				{
					preg_match("/<template +[\"']*(\w+)[\"']*/i",  $content, $match);
					$child = array('tmp' => '<nq>this.getTemplate'.ucfirst($match[1]).'<nq>');
					getTemplateProperties($item['content'], $child, $component);
				}
				elseif ($tagName == 'include')
				{
					preg_match("/<include +[\"']*(\w+)[\"']*/i",  $content, $match);
					$child = array('tmp' => '<nq>includeGeneralTemplate'.ucfirst($match[1]).'<nq>');
					getTemplateProperties($item['content'], $child, $component);
				}
				elseif ($tagName == 'component')
				{
					preg_match("/<component +[\"']*(\w+)[\"']*/i",  $content, $match);
					$child = array('cmp' => '<nq>'.$match[1].'<nq>');
					getTagProperties($item['content'], $child, $component);
				}
				else
				{					
					$childrenList = array();
					$openedTagsCount = 1;
					$i++;
					while (isset($list[$i])) {
						if ($list[$i]['type'] == 'tag') {
							if (!$list[$i]['isClosing'] && $list[$i]['tagName'] == $tagName) {
								$openedTagsCount++;
							} elseif ($list[$i]['isClosing'] && $list[$i]['tagName'] == $tagName) {
								$openedTagsCount--;
							}
						}
						if ($openedTagsCount > 0) {
							$childrenList[] = $list[$i];
							$i++;
						} else {
							break;
						}
					}
					$child = array();
					$data = getHtmlChildren($childrenList, $component);
					if (!empty($data)) {
						if (!isset($data['c'])) {
							$child['c'] = $data;
						} else {
							$child['c'] = $data['c'];
							$child['e'] = $data['e'];
						}
					}
					if (!is_array($child['c'])) {
						$child['c'] = array();
					}
					if ($tagName != 'foreach' && $tagName != 'if') {						
						$child['t'] = getTagIndex($tagName);
						getTagProperties($item['content'], $child, $component);
					} else {
						if ($tagName == 'if') {
							preg_match("/^\{if +([^\}]+)\}/i",  $item['content'], $match);
							checkIfConditionForContainigProps($match[1], $child, $component);
						} elseif ($tagName == 'foreach') {
							getForeach($item, $child, $component);
						}	
					}
				}
				if (empty($child['c'])) {
					unset($child['c']);
				} elseif (count($child['c']) == 1) {
					$child['c'] = $child['c'][0];
				}
				if (!$isElse) {
					$children[] = $child;
				} else {
					$elseChildren[] = $child;
				}
			} else {
				if ($tagName == 'if') {
					$isElse = false;
				}
			}
		}
		if (!empty($elseChildren) && is_array($elseChildren[0]['c'])) {
			array_unshift($elseChildren[0]['c'], '<function_returns_array>');
			array_push($elseChildren[0]['c'], '</function_returns_array>');
			return array('c' => $children, 'e' => $elseChildren[0]['c']);
		} else {
			return $children;
		}		
	}

	function getForeach($item, &$child, $component) {
		$child['h'] = $child['c'];
		unset($child['c']);

		$content = preg_replace('/\s{2,}/', ' ', $item['content']);
		$content = preg_replace('/\{foreach\s+|\}/', '', $content);
		$parts = explode(' ', trim($content));
		
		if ($parts[1] != 'as' || (isset($parts[3]) && $parts[3] != '=>')) {
			error('Невалидный код <b>foreach</b> в шаблоне класса <b>'.$component['name'].'</b>: <b>'.$item['content'].'</b>');
		}
		$variable = $parts[0];
		if (isset($parts[4])) {
			$key = $parts[2];
			$val = $parts[4];
		} else {
			$key = '';
			$val = $parts[2];
			$parts = explode('=>', $val);
			if (isset($parts[1])) {
				$key = $parts[0];
				$val = $parts[1];
			}
		}
		if (!preg_match_all('/^([\$&])(\w[\w\.]*)$/', $variable, $matches)) {
			error('Невалидный код <b>foreach</b> в шаблоне класса <b>'.$component['name'].'</b>: <b>'.$item['content'].'</b>');
		}
		$sign = $matches[1][0];
		$variableParts = explode('.', $matches[2][0]);
		$variable = $variableParts[0];
		if ($sign == '&') {
			$variableParts[0] = '';
			$variableParts = implode('.', $variableParts);
			$variable .= $variableParts;
		} else {
			if (isset($variableParts[1])) {
				error('Невалидный код <b>foreach</b> в шаблоне класса <b>'.$component['name'].'</b>: <b>'.$item['content'].'</b>');
			}
		}

		if (!preg_match('/^\&\w+$/', $val)) {
			error('Невалидный код <b>foreach</b> в шаблоне класса <b>'.$component['name'].'</b>: <b>'.$item['content'].'</b>');
		}
		if (!empty($key) && !preg_match('/^\&\w+$/', $key)) {
			error('Невалидный код <b>foreach</b> в шаблоне класса <b>'.$component['name'].'</b>: <b>'.$item['content'].'</b>');
		}
		if ($sign == '&') {
			$child['p'] = '<nq>'.$variable.'<nq>';
		} else {
			$child['p'] = "<nq>\$('".$variable."')<nq>";
			$child['f'] = $variable;
		}
		if (!empty($key)) {
			$key = ','.str_replace('&', '', $key);
		}
		$val = str_replace('&', '', $val);
		array_unshift($child['h'], '<foreach '.$val.$key.'>');
		array_push($child['h'], '</foreach>');
		return $child;
	}

	function parseTextNode($content, &$children, $component) {
		if (!empty($content)) {
			$items = checkTextForContainigProps($content, $component);
			foreach ($items as $item) {
				if (is_array($item)) {
					$children[] = $item[0];
				} else if (strlen($item) > 3) {
					$children[] = '<nq>__T['.addTextNode($item).']<nq>';
				} else {
					$children[] = $item;
				}
			}
		}
	}

	function isSimpleTag($tagName) {
		return in_array($tagName, array('br', 'input', 'img', 'hr'));
	}

	function getTagIndex($tagName) {
		$tagNameIndex = array_search($tagName, getTagShortcuts());
		return $tagNameIndex !== false ? $tagNameIndex : $tagName;
	}

	function checkIfConditionForContainigProps($ifCondition, &$child, $component) {
		if (is_array($component) && preg_match('/\$\w+[\.\[]/', $ifCondition)) {
			error('Шаблон класса <b>'.$component['name'].'</b> содержит некорректный код <b>'.$ifCondition.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
		}
		$hasCode = preg_match('/\$\w/', $ifCondition);
		if (is_string($component)) {
			error('Шаблон, содержащийся в файле <b>'.$component.'</b> содержит код с реактивными переменными <b>'.$ifCondition.'</b><br><br>Глобальные шаблоны с типом <b>include</b> не могут содержать их. Допускается использование только входящих аргументов (локальных переменных) <b>&var</b>');
		}
		$signs = '[\+\-><\!=\/\*%\?:&\|]';
		$ifCondition = preg_replace('/\s+(?='.$signs.')/', '', $ifCondition);
		$ifCondition = preg_replace('/('.$signs.')\s+/', "$1", $ifCondition);
		$ifCondition = preg_replace('/\&(\w+)/', "$1", $ifCondition);
		$ifCondition = preg_replace('/~(\w+)/', "_['$1']", $ifCondition);
		if ($hasCode) {
			$ifCondition = preg_replace('/\$(\w+)/', "\$('$1')", $ifCondition);
			preg_match_all("/p\('([^']+)'\)/", $ifCondition, $matches);
			if (!empty($matches[1])) {
				$child['p'] = $matches[1];
			}
			$child['i'] = '<nq>function(){return('.$ifCondition.')}<nq>';
			array_unshift($child['c'], '<nq><function_returns_array>');
			array_push($child['c'], '</function_returns_array><nq>');
		} else {
			if (!preg_match('/'.$signs.'/', $ifCondition)) {
				$child['i'] = '<nq>!!'.$ifCondition.'<nq>';
			} else {
				$child['i'] = '<nq>'.$ifCondition.'<nq>';
			}
		}
	}

	function hasCondition($code) {
		$code = preg_replace('/\'[^\']+\'|"[^"]+"/', '', $code);
		return preg_match('/\?/', $code);
	}

	function hasCode($text) {
		return preg_match("/\{[^\}]+\}/", $text);
	}

	function hasClassVar($code) {
		return preg_match('/\$\w/', $code);
	}

	function hasFunctionCall($code) {
		return preg_match('/\.\w+\(/', $code);
	}

	function getTagProperties($html, &$child, $component) {
		global $obfuscate;
		global $propsShortcuts;
		global $eventTypesShortcuts;
		$props = array();
		$names = array();
		$ifCondition = false;
		$else = null;
		preg_match_all("/ +([a-z][\w\-]*)=\"([^\"]+)\"/", $html, $matches1);
		preg_match_all("/ +([a-z][\w\-]*)='([^']+)'/", $html, $matches2);
		$propNames = array_merge($matches1[1], $matches2[1]);
		$propValues = array_merge($matches1[2], $matches2[2]);
		for ($i = 0; $i < count($propNames); $i++) {			
			$propName = $propNames[$i];
			$propValue = $propValues[$i];
			$hasCode = hasCode($propValue);
			$fullPropName = $propName;
			$isObfClName = $obfuscate === true && $fullPropName == 'class';

			if (is_numeric($child['t']) || !empty($child['cmp'])) {
				if ($propName == 'scope') {
					$props[$propsShortcuts[$propName]] = 1;
					continue;
				}
				if ($propName == 'if') {
					$ifCondition = $propValue;
					continue;
				}
				if ($propName == 'else') {
					$else = $propValue;
					continue;
				}
				if (isset($propsShortcuts[$propName])) {
					$propName = $propsShortcuts[$propName];
				} else {
					$propName = preg_replace('/^data-/', '_', $propName);
				}
				if (preg_match("/\bon(\w+)/i", $propName, $match)) {
					if ($hasCode) {
						error('Фигурные скобки внутри атрибута события <b>'.$propName.'</b>. Ожидается название функции обработчика!');
					}
					if (!is_array($child['e'])) {
						$child['e']	= array();
					}
					$eventType = strtolower($match[1]);
					$once = false;
					$parts = preg_split('/once/i', $eventType);
					if (isset($parts[1]) && empty($parts[1])) {
						$eventType = preg_replace("/once$/i", '', $eventType);
						$once = true;
					}
					$callback = preg_replace("/[^\w]/", "", $propValue);
					if (is_array($component) && !isMethodInClass($callback, $component)) {
						error('Функция обработчик события <b>'.$callback.'</b> не найдена среди методов класса <b>'.$component['name'].'</b>');
					}
					$eventTypeIndex = array_search($eventType, $eventTypesShortcuts);
					if ($eventTypeIndex > -1) {
						$eventType = $eventTypeIndex;
					}
					$child['e'][] = $eventType;
					$child['e'][] = '<nq>this.'.$callback.'.bind(this)<nq>';
					if ($once) {
						$child['e'][] = true;
					}
					continue;
				}
			}
			$props[$propName] = $propValue;
			if ($hasCode) {
				$propValue = preg_replace("/&(\w+)/", "$1", $propValue);
				$propValue = preg_replace("/~(\w+)/", "_['$1']", $propValue);
				$propValue = preg_replace("/@(\w+)/", "<nq>__.$1<nq>", $propValue);
				$regexp = '/\{([^\}]*)\}/';
				$hasClassVar = hasClassVar($propValue);
				
				preg_match_all($regexp, $propValue, $matches);
				$codes = $matches[1];
				$parts = preg_split($regexp, $propValue);
				$names[$propName] = array();
				$attrContent = '';
				$attrParts = array();
				foreach ($parts as $idx => $part) {
					if ($part !== '') {
						if ($isObfClName) {
							$part = getObfuscatedClassName($part);
						}
						$attrContent .= $part;
						$attrParts[] = $part;
					}
					if (isset($codes[$idx])) {
						$code = $codes[$idx];
						if ($isObfClName) {
							$code = getObfuscatedClassName($code, true);
						}
						if (hasClassVar($code)) {
							$code = parseAttributeClassVars($code, $names[$propName], $component);
							$attrParts[] = '<nq>'.$code.'<nq>';
						} else {
							if ($hasClassVar) {
								$attrParts[] = '<nq>'.$code.'<nq>';
							} else {
								if (hasCondition($code)) {
									$attrContent .= '<plus>('.$code.')</plus>';
								} else {
									$attrContent .= '<plus>'.$code.'</plus>';
								}
							}
						}
					}
				}
				
				if ($hasClassVar) {
					$attrContent = implode('"+"', $attrParts);
				}

				$hasFunctionCall = hasFunctionCall($attrContent);
				if ($hasFunctionCall) {
					$attrContent = preg_replace('/\.(\w+)\(([^\)]*)\)/', "this.$1($2)", $attrContent);
				}

				if ($hasClassVar) {
					$binding1 = !empty($hasFunctionCall) ? '(' : '';
					$binding2 = !empty($hasFunctionCall) ? ').bind(this)' : '';
					$attrContent = '<nq>'.$binding1.'<function>"'.$attrContent.'"</function>'.$binding2.'<nq>';
				}

				$props[$propName] = correctTagAttributeText($propName, $attrContent);
				$names[$propName] = array_unique($names[$propName]);
				if (count($names[$propName]) == 1) {
					$names[$propName] = $names[$propName][0];
				}
				if (empty($names[$propName])) {
					unset($names[$propName]);
				}
			} else if ($isObfClName) {
				$props[$propName] = getObfuscatedClassName($propValue);
			}
		}
		if (!empty($props)) {
			$child['p'] = $props;
		}
		if (!empty($names)) {
			$child['n'] = $names;
		}
		if (!empty($ifCondition) || !empty($else)) {
			addIfConditionToChild(trim($ifCondition), trim($else), $child, $component);
		}
	}

	function addIfConditionToChild($ifCondition, $else, &$child, $component) {
		if (!empty($else) && empty($ifCondition)) {
			error('Элемент в шаблоне класса <b>'.$component['name'].'</b> содержит атрибут <b>else</b>, но не содержит атрибут <b>if</b>');
		}
		if (!preg_match('/^\{[^\}]+\}$/', $ifCondition)) {
			error('Элемент в шаблоне класса <b>'.$component['name'].'</b> содержит некорректный атрибут <b>if = "'.$ifCondition.'"</b><br><br>Атрибут должен иметь вид <b>if = "{$a === true}"</b> или <b>if = "{!&name}"</b>');
		}
		$ifCondition = ltrim($ifCondition, '{');
		$ifCondition = rtrim($ifCondition, '}');
		$child = array('c' => $child);
		if (!empty($else)) {
			$else = ltrim($else, '{');
			$else = rtrim($else, '}');
			if (preg_match('/\$\w+[\.\[]/', $else)) {
				error('Шаблон класса <b>'.$component['name'].'</b> содержит некорректный код <b>'.$else.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
			}
			$child['e'] = parseCode($else, true);
		}
		checkIfConditionForContainigProps($ifCondition, $child, $component);
	}

	function getObfuscatedClassName($value, $isCode = false) {
		global $cssClassIndex;
		if (!$isCode) {
			$value = "'".$value."'";
		}
		$regexp = '/"[^"]+"|\'[^\']+\'/';
		preg_match_all($regexp, $value, $matches);
		$strings = $matches[0];
		$codeParts = preg_split($regexp, $value);
		$obfuscatedValue = '';
		foreach ($codeParts as $i => $codePart) {
			$obfuscatedValue .= $codePart;
			if (isset($strings[$i])) {
				$string = preg_replace('/["\']/', '', $strings[$i]);
				$parts = explode(' ', $string);
				$newClassName = array();
				foreach ($parts as $part) {
					if (!empty($part) && isset($cssClassIndex[$part])) {
						$newClassName[] = $cssClassIndex[$part];
					} else {
						if (!empty($part)) {
							$part = addToCssClassIndex($part);
						}
						$newClassName[] = $part;
					}
				}
				$obfuscatedValue .= "'".implode(' ', $newClassName)."'";
			}
		}		
		if (!$isCode) {
			$obfuscatedValue = trim($obfuscatedValue, "'");
		}
		return preg_replace('/ {2,}/', ' ', $obfuscatedValue);
	}

	function getTemplateProperties($html, &$child, $component) {
		$regexp = '/\{([^\}]+)\}/';
		$props = array();
		$names = array();
		preg_match_all("/ +([a-z][\w\-]*)=\"([^\"]+)\"/", $html, $matches1);
		preg_match_all("/ +([a-z][\w\-]*)='([^']+)'/", $html, $matches2);
		$propNames = array_merge($matches1[1], $matches2[1]);
		$propValues = array_merge($matches1[2], $matches2[2]);
		$ifCondition = false;
		$else = null;
		for ($i = 0; $i < count($propNames); $i++) {
			$propName = $propNames[$i];
			$propValue = $propValues[$i];
			if ($propName == 'if') {
				$ifCondition = $propValue;
				continue;
			}
			if ($propName == 'else') {
				$else = $propValue;
				continue;
			}			
			$hasCode = hasCode($propValue);
			if ($hasCode) {
				if (is_string($component)) {
					error('Шаблон, содержащийся в файле <b>'.$component.'</b> содержит код с реактивными переменными <b>'.$propValue.'</b><br><br>Глобальные шаблоны с типом <b>include</b> не могут содержать их. Допускается использование только входящих аргументов (локальных переменных) <b>&var</b>');
				}
				if (preg_match('/\$\w+[\.\[]/', $propValue)) {
					error('Шаблон класса <b>'.$component['name'].'</b> содержит некорректный код <b>'.$propValue.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
				}				
				preg_match_all($regexp, $propValue, $matches);
				$codes = $matches[1];
				if (!empty($codes)) {
					$parts = preg_split($regexp, $propValue);
					$content = array();
					foreach ($parts as $j => $part) {
						if (!empty($part)) {
							$content[] = $part;
						}
						if (isset($codes[$j])) {
							$content[] = '<plus>'.parseCode($codes[$j], true).'</plus>';
						}
					}
					$propValue = implode($content);
				}
			}
			$props[$propName] = $propValue;
		}
		if (!empty($props)) {
			$child['p'] = $props;
		}
		if (!empty($ifCondition) || !empty($else)) {
			addIfConditionToChild(trim($ifCondition), $else, $child, $component);
		}
	}

	function isMethodInClass($funcName, $component) {
		foreach ($component['functions'] as $method) {
			if ($method['name'] == $funcName) return true;
		}
		return false;
	}

	function parseAttributeClassVars($code, &$names, $component) {
		if (preg_match('/\$\w+[\.\[]/', $code)) {
			if (is_array($component)) {
				error('Элемент в шаблоне класса <b>'.$component['name'].'</b> содержит атрибут с некорректным кодом <b>'.$code.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
			}
		}
		$regexp = '/\$(\w+)/';
		preg_match_all($regexp, $code, $matches);
		if (!empty($matches[1])) {
			if (is_string($component)) {
				error('Шаблон, содержащийся в файле <b>'.$component.'</b> содержит код с реактивными переменными <b>'.$code.'</b><br><br>Глобальные шаблоны с типом <b>include</b> не могут содержать их. Допускается использование только входящих аргументов (локальных переменных) <b>&var</b>');
			}
			foreach ($matches[1] as $i => $match) {
				$names[] = $match;
			}
		}
		if (preg_match('/.\?[^:]+:./', $code)) {
			$code = '('.$code.')';
		}
		return preg_replace($regexp, "\$('$1')", $code);
	}

	function correctTagAttributeText($propName, $text) {
		if ($propName == 'st') {
			$text = preg_replace('/:\s+/', ':', $text);
		}
		return $text;
	}

	function checkTextForContainigProps($text, $component) {
		$regexp = '/\{([^\}]+)\}/';
		preg_match_all($regexp, $text, $matches);
		$codes = $matches[1];
		if (empty($codes)) {
			return array($text);
		}
		if (preg_match('/\$\w+[\.\[]/', $text)) {
			if (is_array($component)) {
				error('Шаблон класса <b>'.$component['name'].'</b> содержит некорректный код <b>'.$text.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
			} else {
				error('Один из шаблонов файле <b>'.$component.'</b> содержит некорректный код <b>'.$text.'</b><br><br>Реактивные переменные класса должны иметь вид <b>$var</b>. Использование <b>$var.name</b> или <b>$var["name"]</b> недопустимо');
			}
		}
		$parts = preg_split($regexp, $text);
		$content = array();
		foreach ($parts as $i => $part) {
			if (!empty($part)) {
				$content[] = $part;
			}
			if (isset($codes[$i])) {
				$content[] = array(parseCode($codes[$i]));
			}
		}	
		return $content;
	}

	function parseCode($code, $noClassVars = false) {
		$code = trim($code);
		$code = preg_replace('/\s*@(\w+)\s*/', "__.$1", $code);
		if (!$noClassVars) {
			$code = preg_replace('/^\s*\$(\w+)\s*$/', "{'pr':'$1','p':\$('$1')}", $code);
		}
		$code = preg_replace('/^&([a-z])/i', "$1", $code);
		$code = preg_replace('/([^&])&([a-z])/i', "$1$2", $code);
		$code = preg_replace('/~([a-z]\w*)/i', "_['$1']", $code);		
		$code = preg_replace('/^\s*\.(\w+)\((.+?)\)\s*$/', "this.$1($2)", $code);
		return '<nq>'.preg_replace('/\s+([\?:\+\-><=\!]{1,3})\s+/', "$1", $code).'<nq>';
	}

	function addConstructorFunction(&$js, $class, $isComponent) {
		global $advancedMode, $routerMenu;
		$args = $isComponent ? 'props' : '';
		$js[] = 'function '.$class.'('.$args.') {';
		if ($isComponent) {
			$js[] = "\tInitialization.initiate.call(this, props);";
			if (is_array($routerMenu) && in_array($class, $routerMenu)) {
				$js[] = "\tRouter.addMenu(this);";
				$js[] = "\tthis.isRouteMenu = true;";
			}			
		} else {
			$js[] = "\tInitialization.initiate.call(this);";
			$js[] = "\n\tthis.processInitials();";
		}
		$js[] = '};';
	}

	function addPrototypeFunction(&$js, $class, $name, $args = '', $code = '') {
		$js[] = $class.'.prototype.'.$name.' = function('.$args.') {';
		$js[] = $code;
		$js[] = '};';
	}

	function addTemplateFunction(&$js, $class, $templateHtml, $component) {
		$templateFunctions = getTemplateFunctions($templateHtml, $component, $class);
		foreach ($templateFunctions as $templateFunction) {
			addPrototypeFunction($js, $class, 'getTemplate'.ucfirst($templateFunction['name']), '$, _', "\n\treturn".$templateFunction['content']);
		}
	}

	function addGeneralTemplateFunction(&$js, $templateHtml, $file) {
		$templateFunctions = getTemplateFunctions($templateHtml, $file);
		foreach ($templateFunctions as $templateFunction) {
			$js[] = 'function includeGeneralTemplate'.ucfirst($templateFunction['name']).'(_) {';
			$js[] = "\n\treturn".$templateFunction['content']."\n}";
		}
	}

	function addGetInitialsFunction(&$js, $class, $initials) {
		$objCode = array();
		foreach ($initials as $name => $code) {
			if (!empty($code)) {
				$code = removeExtraSymbold(preg_replace('/(:\s*)@(\w+)/', "$1__.$2", $code));
				$spacelessCode = preg_replace('/\s/', '', $code);
				if ($spacelessCode != '{}' && $spacelessCode != '[]') {
					$objCode[] = "\n\t\t'".$name."':".$code;
				}
			}
		}		
		if (!empty($objCode)) {
			$js[] = $class.".prototype".($advancedMode ? "['_gi']" : '.getInitials')." = function() {";
			$js[] = "\n\treturn {\n";
			$js[] = implode(",\n", $objCode);
			$js[] = "\t};\n};";
		}
	}

	function addLoadControllerFunction(&$js, $viewClass) {
		global $routeControllersByViews;
		if (is_array($routeControllersByViews[$viewClass]) && !empty($routeControllersByViews[$viewClass])) {
			$js[] = $viewClass.'.prototype.getControllersToLoad = function() {';
			$js[] = "\n\treturn [".implode(',', $routeControllersByViews[$viewClass])."];\n};";
		}
	}

	function removeExtraSymbold($text) {
		return preg_replace("/ {2,}/", ' ', preg_replace("/[\t\r\n]/", '', $text));
	}

	function createObjectString($name, $object, $replacements = null) {
		if (is_array($object)) {
			$string = json_encode($object);
		} else {
			$string = $object;
		}
		if (is_array($replacements)) {
			for ($i = 0; $i < count($replacements); $i++) {
				if (is_string($replacements[$i + 1])) {
					$string = preg_replace($replacements[$i], $replacements[$i + 1], $string);
				}
				$i++;
			}
		}
 		return "var ".$name." = ".str_replace('"', "'", $string).';';
	}

	function isComponent($classType) {
		global $componentLikeClassTypes;
		return in_array($classType, $componentLikeClassTypes);
	}

	function printArr($arr, $isExit = false) {
		if (!is_array($arr)) {
			$arr = array($arr);
		}
		print('<xmp>');
		print_r($arr);
		print('</xmp>');
		if ($isExit) exit();
	}

	function getDataConstants($data, &$dataIndex) {
		$data = implode('', $data);
		$regexp = '\#(\w+)\s*=\s*';
		preg_match_all('/'.$regexp.'/', $data, $matches);
		$vars = $matches[1];
		foreach ($vars as $i => $v) {
			if (isset($dataIndex[$v])) {
				error('Обнаружено повторное определение контстанты данных с именем <b>'.$v.'</b>');
			}
			$dataIndex[$v] = $i;
		}
		$var = transformIntoValidJson('{'.trim(preg_replace('/;*\s*'.$regexp.'/', ",'$1':", $data), ',').'}');
		
		$var = preg_replace('/@(\w+)/', "<nq>__.$1<nq>", $var);
		$data = json_decode($var, true);
		if ($data === null) {
			error('Ошибка парсинга контстанты данных<br><br>'.$var);
		}
		$dataIndex = array_keys($dataIndex);
		return array_values($data);
	}

	function getTextConstants($texts, &$textsIndex) {
		$constants = array();
		$regexp = '/@(\w+)\s*:\s*/';
		foreach ($texts as $text) {
			preg_match_all($regexp, $text, $matches);
			$varNames = $matches[1];
			if (!empty($varNames)) {
				$parts = preg_split($regexp, $text);
				array_shift($parts);
				foreach ($parts as $i => $part) {
					$constants[$varNames[$i]] = trim($part);
				}
			}
		}
		$textsIndex = array_keys($constants);
		return array_values($constants);
	}

	function generateTree($routes, $fileName, $html, $parentPath = '') {
		$reserved = array('');
		foreach ($routes as $route) {
			$path = DEFAULT_PATH.$parentPath.$route['name'];
			if (!is_dir($path)) {
				createDir($path);
			}
			$pathToFile = $path.'/'.$fileName;
			file_put_contents($pathToFile, $html);
		}
	}

	function error($errors) {
		if (!is_array($errors)) {
			$errors = array($errors);
		}
		$errors = implode('<div class="delimiter"></div>', $errors);
		die($errors);
	}

	function obfuscateCss($css, &$indexArr) {
		$regexp = '/url\([^\)]+\)/';
		preg_match_all($regexp, $css, $matches);
		$urls = $matches[0];
		$css = preg_replace($regexp, '__URL__', $css);

		preg_match_all('/\.([a-z][\w\-]{3,})/i', $css, $matches);
		$classes = array_unique($matches[1]);
		foreach ($classes as $class) {
			$indexArr[$class] = generateObfiscatedCssClassName();
		}
		foreach ($indexArr as $k => $v) {
			$css = preg_replace('/\.'.$k.'([\s\.\#,\{:])/', '.'.$v."$1", $css);
		}
		$parts = explode('__URL__', $css);
		$css = '';
		foreach ($parts as $i => $part) {
			$css .= $part;
			if (isset($urls[$i])) {
				$css .= $urls[$i];
			}
		}
		return $css;
	}

	function generateObfiscatedCssClassName() {
		global $cssCounters;
		global $cssClassA, $cssClassB, $cssClassC;
		$l1 = $cssClassA[$cssCounters[0]];
		$l2 = $cssClassB[$cssCounters[1]];
		$l3 = $cssClassC[$cssCounters[2]];
		$cssCounters[2]++;
		if ($cssCounters[2] == count($cssClassC)) {
			$cssCounters[2] = 0;
			$cssCounters[1]++;
		}
		if ($cssCounters[1] == count($cssClassB)) {
			$cssCounters[1] = 0;
			$cssCounters[0]++;
		}
		return $l1.$l2.$l3;
	}

	function addToCssClassIndex($className) {
		global $cssClassIndex;
		$obfuscatedClassName = generateObfiscatedCssClassName();
		$cssClassIndex[$className] = $obfuscatedClassName;
		return $obfuscatedClassName;
	}

	function getCssConstants($texts) {
		$constants = array();
		$regexp = '/\$(\w+)\s*:\s*/';
		foreach ($texts as $text) {
			preg_match_all($regexp, $text, $matches);
			$varNames = $matches[1];
			if (!empty($varNames)) {
				$parts = preg_split($regexp, $text);
				array_shift($parts);
				foreach ($parts as $i => $part) {
					$constants[$varNames[$i]] = trim($part);
				}
			}
		}
		return $constants;
	}

?>