<?php

class JSGlobals
{
	private static $usingLoader = false;
	private static $isSplitMode = false;
	private static $dataForLoader;
	private static $output	= array();
	private static $globals	= array();
	private static $sets = array();
	private static $excluded = array();

	private static $errors = array(
		'invalidApiConfig' => "Файл конфигурации путей к api <b>config.js</b> не корректен. Содержимое должно иметь вид <xmp>var {?} = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>",
		'textConstNotFound' => 'Текстовая константа {??} используемая в шаблоне {??} класса {??} не найдена',
		'textConstNotFound2' => 'Текстовая константа {??} используемая в методе {??} класса {??} не найдена',
		'dataConstNotFound' => 'Константа данных {??} не найдена',
		'noApiConfigPath' => 'Параметр <b>{?}.{?}.{?}</b> не найден в файле конфигурации <b>config.js</b>',
		'noDeclFilesFound' => "Обнаружено использование утилины <b>Decliner</b>, но не найден ни один файл с расширением <b>decl</b><br><br>Пример содержимого такого файла:<xmp>@item: штука,штуки,штук\n@ball: мяч,мяча,мячей</xmp>"
	);

	public static function run(&$jsOutput, $data) {
		self::$usingLoader = $data['isDataLoader'];
		self::$isSplitMode = $data['isSplitMode'];
		self::addApiConfig($data['config'], $jsOutput);
		if (!self::$usingLoader) {
			self::addTextNodes();
			self::addTextConstants($data['texts']);
			self::addPathToDictionary($data['pathToDictionary']);
		} else {
			self::$output[] = 'var '.CONST_CONSTANTS.','.CONST_TEXTS.','.CONST_DICTURL.';';
			self::$dataForLoader = array(
				'textConstants' => $data['texts'],
				'textNodes' => TemplateParser::getTextNodes()
			);
			self::addLoadUrl($data['pathToLoadAppApi']);
		}
		self::addDataConstants($data['data'], self::$usingLoader);
		self::addTags($data['tags']);
		self::addProps($data['props']);
		self::addDecls($data['decls']);
		self::addEvents($data['events']);
		self::addRoutes($data['routes'], $data['controllers']);
		self::addErrorRoutes($data['errorRoutes']);
		self::addHashRouter($data['hashRouter']);
		self::addIndexRoute($data['indexRoute']);
		self::addDefaultRoute($data['defaultRoute']);
		self::addViewContainer($data['viewContainer']);
		self::addTooltipClass($data['tooltipClass']);
		self::addTooltipApi($data['tooltipApi']);
		self::addPathToApi($data['pathToApi']);
		self::addPagetitle($data['pagetitle']);
		self::addUserOptions($data['user']);
		self::addControllers($data['controllers']);
		self::addNullFunction();
		self::addStopPropagationFunction();
		self::addPreventDefaultFunction();
		self::addGetFuncFunction();
		
		self::addGlobals();

		$jsOutput = implode("\n", self::$output)."\n".$jsOutput;
		self::parseTextConstants($jsOutput, $data['texts']);
		self::parseDataConstants($jsOutput, $data['data']);
	}

	public static function getDataForLoader() {
		return self::$dataForLoader;
	}

	public static function exclude($key) {
		self::$excluded[] = $key;
	}

	public static function getConstantsListToDefineInChunks() {
		return array(
			CONST_USER, CONST_ROUTER, CONST_CONFIG, CONST_CORE, CONST_FUNCS, CONST_TEXTS, CONST_CONSTANTS, CONST_DATA, CONST_FUNCTION, CONST_CONTROLLERS, CONST_STOP, CONST_PREVENT, CONST_GETFUNC, CONST_POPUPER, CONST_STATE, CONST_DICTIONARY, CONST_OBJECTS, CONST_CONTROLLER, CONST_DATES, CONST_DIALOGER, CONST_DECLINER, CONST_STORE
		);
	}

	public static function getReservedPrivateVarNames() {
		return Constants::getReservedPrivateVarNames();
	}

	public static function getReservedPublicVarNames() {
		return Constants::getReservedPublicVarNames();
	}

	public static function getAllReservedVarNames() {
		return array_merge(Constants::getReservedPrivateVarNames(), Constants::getReservedPublicVarNames());
	}

	private static function add($key, $content) {
		if (!in_array($key, self::$excluded)) {
			self::$globals[] = self::normJsonStr($key." = ".$content);
			return true;
		}
	}

	private static function addGlobals() {		
		self::$output[] = 'var '.implode(",\n", self::$globals).';';
		if (self::$isSplitMode) {
			self::$output[] = implode(";", self::$sets).';';
		}
	}

	private static function addGlobalAddingCall($key) {
		self::$sets[] = CONST_GLOBAL.".set(".$key.",'".$key."')";
	} 

	public static function normJsonStr($str){
		$str = preg_replace('/\\\\u00A0/i', '_nbsp_', $str);
	    $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
	    $str = str_replace('_nbsp_', '\\u00A0', $str);
    	return $str;
    	//return iconv('cp1251', 'utf-8', $str);
	}

	private static function addTextNodes() {
		$textNodes = TemplateParser::getTextNodes();
		self::add(CONST_TEXTS, preg_replace("/\\\{2,}/", '\\', str_replace('"', "'", json_encode($textNodes))));
		self::addGlobalAddingCall(CONST_TEXTS);
	}

	private static function addTextConstants($data) {
		self::add(CONST_CONSTANTS, str_replace('"', "'", json_encode($data['texts'])));
		self::addGlobalAddingCall(CONST_CONSTANTS);
	}

	public static function parseTextConstants(&$jsOutput, $data) {
		if (is_array($data['index'])) {
			$varName = CONST_CONSTANTS;
			$regexp = '/\b'.$varName.'\.\w+\b/';
			$output = $jsOutput;
			preg_match_all($regexp, $jsOutput, $matches);
			$codes = $matches[0];
			$parts = preg_split($regexp, $jsOutput);
			$jsOutput = '';
			foreach ($parts as $i => $part) {
				$jsOutput .= $part;
				if (isset($codes[$i])) {
					$parts2 = explode('.', $codes[$i]);
					$index = array_search($parts2[1], $data['index']);
					if (is_bool($index)) {
						$p = preg_split('/\b'.$varName.'\.'.$parts2[1].'\b/', $output);
						preg_match_all('/(\w+)\.prototype\.(\w+)\s*=\s*function/', $p[0], $mtchs);
						$cnt = count($mtchs[0]);
						$cln = $mtchs[1][$cnt - 1];
						$fnn = $mtchs[2][$cnt - 1];
						preg_match('/^getTemplate([A-Z]\w*)$/', $fnn, $mtch);
						if (isset($mtch[1])) {
							new Error(self::$errors['textConstNotFound'], array($parts2[1], strtolower($mtch[1]), $cln));
						}
						new Error(self::$errors['textConstNotFound2'], array($parts2[1], $fnn, $cln));
					}
					$jsOutput .= $varName.'['.$index.']';
				}
			}
		}
	}

	public static function parseDataConstants(&$jsOutput, $data) {
		if (is_array($data['index'])) {
			$varName = CONST_DATA;
			$regexp = '/<data>\w+\b/';
			preg_match_all($regexp, $jsOutput, $matches);
			$codes = $matches[0];
			$parts = preg_split($regexp, $jsOutput);			
			$jsOutput = '';
			foreach ($parts as $i => $part) {
				$jsOutput .= $part;
				if (isset($codes[$i])) {
					$parts2 = explode('<data>', $codes[$i]);
					$index = array_search($parts2[1], $data['index']);
					if (is_bool($index)) {
						new Error(self::$errors['dataConstNotFound'], array($parts2[1]));
					}
					$jsOutput .= $parts2[0].$varName.'['.$index.']';
				}
			}
		}
	}

	private static function addApiConfig($apiConfig, $jsOutput) {
		$cfg = CONST_CONFIG;
		TextParser::transformIntoValidJson($apiConfig);
		$apiConfigObject = json_decode($apiConfig, true);
		if ($apiConfigObject === null) {
			new Error(self::$errors['invalidApiConfig'], $cfg);
		}
		preg_match_all('/'.$cfg.'\.(\w+)\.(\w+)/', $jsOutput, $matches);
		foreach ($matches[1] as $i => $match) {
			if (empty($apiConfigObject[$match][$matches[2][$i]])) {
				new Error(self::$errors['noApiConfigPath'], array($cfg, $match, $matches[2][$i]));
			}
		}
		TextParser::createObjectString($apiConfig, array('/\\\/', ''));
		self::add($cfg, $apiConfig);
		self::addGlobalAddingCall($cfg);
	}

	private static function addDataConstants($data, $usingLoader) {
		$data = str_replace('"', "'", json_encode(array_values(self::getDataConstants($data))));
		if ($usingLoader) {
			$data = 'function(){return '.$data.'}';
		}
		self::add(CONST_DATA, $data);
		if (!$usingLoader) {
			self::addGlobalAddingCall(CONST_DATA);
		}
	}

	private static function getDataConstants($data) {
		$allData = array();
		foreach ($data['data'] as $item) {
			$allData = array_merge($allData, $item);
		}
		return $allData;
	}

	private static function addPathToDictionary($url) {
		self::add(CONST_DICTURL, "'".$url."'");
	}

	private static function addTags($tags) {
		self::add(CONST_TAGS, str_replace('"', "'", json_encode($tags)));
	}

	private static function addLoadUrl($url) {
		self::add(CONST_LOADURL, "'".preg_replace('/^\.+/', '', trim($url))."'");
	}

	private static function addProps($props) {
		self::add(CONST_ATTRIBUTES, str_replace('"', "'", json_encode($props)));
	}

	private static function addDecls($decls) {
		if (self::add(CONST_WORDS, str_replace('"', "'", json_encode($decls))) && empty($decls)) {
			new Error(self::$errors['noDeclFilesFound']);
		}
	}

	private static function addEvents($events) {
		self::add(CONST_EVENTTYPES, str_replace('"', "'", json_encode($events)));
	}

	private static function addRoutes($routes, $controllers) {
		TextParser::createObjectString($routes);
		foreach ($controllers as $i => $ctr) {
			$routes = preg_replace('/\''.$ctr.'\'/', $i, $routes);
		}
		self::add(CONST_ROUTES, $routes);
	}

	private static function addErrorRoutes($routes) {
		TextParser::createObjectString($routes);
		self::add(CONST_ERRORROUTES, $routes);
	}

	private static function addHashRouter($isHashRouter) {
		self::add(CONST_HASHROUTER, $isHashRouter ? 'true' : 'false');
	}

	private static function addIndexRoute($indexRoute) {
		self::add(CONST_INDEXROUTE, !empty($indexRoute) ? "'".$indexRoute."'" : 'null');
	}

	private static function addDefaultRoute($defaultRoute) {
		self::add(CONST_DEFAULTROUTE, !empty($defaultRoute) ? "'".$defaultRoute."'" : 'null');
	}

	private static function addViewContainer($viewContainer) {
		self::add(CONST_VIEWCONTAINER, "'->>".$viewContainer."'");
		self::add(CONST_PARENTALVIEWCNT, "'->>parental-view-container'");
	}

	private static function addTooltipClass($tooltipClass) {
		self::add(CONST_TOOLTIPCLASS, !empty($tooltipClass) ? "'".$tooltipClass."'" : 'null');
	}

	private static function addTooltipApi($tooltipApi) {
		self::add(CONST_TOOLTIPAPI, "'".$tooltipApi."'");
	}

	private static function addPathToApi($pathToApi) {
		self::add(CONST_APIDIR, "'".$pathToApi."'");
	}

	private static function addPagetitle($pagetitle) {
		self::add(CONST_PAGETITLE, "'".$pagetitle."'");
	}

	private static function addUserOptions($userOptions) {
		TextParser::createObjectString($userOptions, array('/\\\/', ''));
		self::add(CONST_USEROPTIONS, $userOptions);
	}

	private static function addNullFunction() {
		self::add(CONST_FUNCTION, 'function(){return}');
		self::addGlobalAddingCall(CONST_FUNCTION);
	}

	private static function addControllers($controllers) {
		self::add(CONST_CONTROLLERS, str_replace('"', "'", json_encode($controllers)));
	}

	private static function addStopPropagationFunction() {
		self::add(CONST_STOP, 'function(e){e.stopPropagation()}');
		self::addGlobalAddingCall(CONST_STOP);
	}

	private static function addPreventDefaultFunction() {
		self::add(CONST_PREVENT, 'function(e){e.preventDefault()}');
		self::addGlobalAddingCall(CONST_PREVENT);
	}

	private static function addGetFuncFunction() {
		self::add(CONST_GETFUNC, 'function(){return new Function}');
		self::addGlobalAddingCall(CONST_GETFUNC);
	}
}