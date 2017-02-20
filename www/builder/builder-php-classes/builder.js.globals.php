<?php

class JSGlobals
{
	private static $usingLoader = false;
	private static $jsConfig, $dataForLoader;
	private static $output	= array();
	private static $excluded = array();
	private static $varNames = array(
		'textConstants'    => '__',
		'textNodes'        => '__T',
		'apiConfig'        => 'CONFIG',
		'dataConstants'    => '__V',
		'pathToDictionary' => '__DICTURL',
		'dictionary'       => '__D',
		'tags'             => '__TAGS',
		'props'            => '__A',
		'decls'            => '__DW',
		'events'           => '__EVENTTYPES',
		'routes'           => '__ROUTES',
		'errorRoutes'      => '__ERRORROUTES',
		'hashRouter'       => '__HASHROUTER',
		'indexRoute'       => '__INDEXROUTE',
		'defaultRoute'     => '__DEFAULTROUTE',
		'viewContainer'    => '__VIEWCONTAINER',
		'viewContainer2'   => '__VIEWCONTAINER2',
		'tooltipClass'     => '__TC',
		'tooltipApi'       => '__TA',
		'pathToApi'        => '__APIDIR',
		'pagetitle'        => '__PAGETITLE',
		'user'             => '__USEROPTIONS',
		'nullFunction'     => '__FNC',
		'controllers'      => '__CTR',
		'controller'       => '__C',
		'stop'             => '__SP',
		'prevent'          => '__PD',
		'dialoger'         => '__DI',
		'global'           => '_G_',
		'funcs'            => '_F_',
		'objects'          => '__O',
		'popuper'          => '__P',
		'state'            => '__S',
		'proto'            => 'p',
		'component'        => 'c'
	);
	
	private static $varKeys = array(
		'textConstants' => '_tc',
		'textNodes' => '_tn',
		'apiConfig' => '_ac',
		'dataConstants' => '_dc',
		'nullFunction' => '_nf',
		'controller' => 'Controllers',
		'stop' => '_sp',
		'prevent' => '_pd',
		'objects' => 'Objects',
		'popuper' => 'Popuper',
		'state' => 'State',
		'dictionary' => 'Dictionary'
	);

	private static $errors = array(
		'invalidApiConfig' => "���� ������������ ����� � api <b>config.js</b> �� ���������. ���������� ������ ����� ��� <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>",
		'textConstNotFound' => '��������� ��������� {??} ������������ � ������� {??} ������ {??} �� �������',
		'textConstNotFound2' => '��������� ��������� {??} ������������ � ������ {??} ������ {??} �� �������',
		'dataConstNotFound' => '��������� ������ {??} �� �������',
		'noApiConfigPath' => '�������� <b>CONFIG.{?}.{?}</b> �� ������ � ����� ������������ <b>config.js</b>',
		'noDeclFilesFound' => "���������� ������������� ������� <b>Decliner</b>, �� �� ������ �� ���� ���� � ����������� <b>decl</b><br><br>������ ����������� ������ �����:<xmp>@item: �����,�����,����\n@ball: ���,����,�����</xmp>"
	);

	public static function init($jsConfig) {
		self::$jsConfig = $jsConfig;
	}

	public static function run(&$jsOutput, $data) {
		self::$usingLoader = $data['isDataLoader'];
		self::addApiConfig($data['config'], $jsOutput);
		if (!self::$usingLoader) {
			self::addTextNodes();
			self::addTextConstants($data['texts']);
			self::addDataConstants($data['data']);
			self::addPathToDictionary($data['pathToDictionary']);
		} else {
			self::$output[] = 'var '.self::$varNames['textConstants'].','.self::$varNames['textNodes'].','.self::$varNames['dataConstants'].';';
			self::$dataForLoader = array(
				'textConstants' => json_encode($data['texts']),
				'textNodes' => preg_replace("/\\\{2,}/", '\\', json_encode(TemplateParser::getTextNodes())),
				'dataConstants' => json_encode(array_values(self::getDataConstants($data['data'])))
			);
		}
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

		$jsOutput = implode("\n", self::$output)."\n".$jsOutput;
		self::parseTextConstants($jsOutput, $data['texts']);
		self::parseDataConstants($jsOutput, $data['data']);
	}

	public static function getDataForLoader() {
		return self::$dataForLoader;
	}

	public static function getUsedNames() {
		return array(
			'FUNCS'           => self::$varNames['funcs'],
			'GLOBAL'          => self::$varNames['global'],
			'ATTRIBUTES'      => self::$varNames['props'],
			'EVENTTYPES'      => self::$varNames['events'],
			'TAGS'            => self::$varNames['tags'],
			'ROUTES'          => self::$varNames['routes'],
			'ERRORROUTES'     => self::$varNames['errorRoutes'],
			'VIEWCONTAINER'   => self::$varNames['viewContainer'],
			'PARENTALVIEWCNT' => self::$varNames['viewContainer2'],
			'PAGETITLE'       => self::$varNames['pagetitle'],
			'DICTURL'         => self::$varNames['pathToDictionary'],
			'DICTIONARY'      => self::$varNames['dictionary'],
			'TOOLTIPCLASS'    => self::$varNames['tooltipClass'],
			'TOOLTIPAPI'      => self::$varNames['tooltipApi'],
			'APIDIR'          => self::$varNames['pathToApi'],
			'INDEXROUTE'      => self::$varNames['indexRoute'],
			'DEFAULTROUTE'    => self::$varNames['defaultRoute'],
			'HASHROUTER'      => self::$varNames['hashRouter'],
			'USEROPTIONS'     => self::$varNames['user'],
			'WORDS'           => self::$varNames['decls'],
			'TEXTS'           => self::$varNames['textNodes'],
			'CONSTANTS'       => self::$varNames['textConstants'],
			'CONTROLLERS'     => self::$varNames['controllers'],
			'CONTROLLER'      => self::$varNames['controller'],
			'DIALOGER'        => self::$varNames['dialoger'],
			'JSBASE'          => self::$jsConfig['file'],
			'OBJECTS'         => self::$jsConfig['objects'],
			'COMPONENT'       => self::$varNames['component'],
			'PROTO'           => self::$varNames['proto']
		);
	}

	public static function exclude($varKey) {
		self::$excluded[] = $varKey;
	}

	public static function getVarName($key) {
		return self::$varNames[$key];
	}

	public static function getVarKeys() {
		return self::$varKeys;
	}

	public static function getVarNames() {
		return self::$varNames;
	}

	private static function add($key, $content) {
		if (!in_array($key, self::$excluded)) {
			self::$output[] = self::normJsonStr("var ".self::$varNames[$key]." = ".$content.';');
			return true;
		}
	}

	private static function addGlobalAddingCall($key) {
		return ";".self::$varNames['global'].".set(".self::$varNames[$key].",'".self::$varKeys[$key]."');";
	} 

	private static function normJsonStr($str){
		$str = preg_replace('/\\\\u00A0/i', '_nbsp_', $str);
	    $str = preg_replace_callback('/\\\\u([a-f0-9]{4})/i', create_function('$m', 'return chr(hexdec($m[1])-1072+224);'), $str);
	    $str = str_replace('_nbsp_', '\\u00A0', $str);
    	return $str;
    	//return iconv('cp1251', 'utf-8', $str);
	}

	private static function addTextNodes() {
		$textNodes = TemplateParser::getTextNodes();
		self::add('textNodes', preg_replace("/\\\{2,}/", '\\', str_replace('"', "'", json_encode($textNodes))).self::addGlobalAddingCall('textNodes'));
	}

	private static function addTextConstants($data) {
		self::add('textConstants', str_replace('"', "'", json_encode($data['texts'])).self::addGlobalAddingCall('textConstants'));
	}

	public static function parseTextConstants(&$jsOutput, $data) {
		if (is_array($data['index'])) {
			$varName = self::getVarName('textConstants');
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
			$varName = self::getVarName('dataConstants');
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
		TextParser::transformIntoValidJson($apiConfig);
		$apiConfigObject = json_decode($apiConfig, true);
		if ($apiConfigObject === null) {
			new Error(self::$errors['invalidApiConfig']);
		}
		preg_match_all('/CONFIG\.(\w+)\.(\w+)/', $jsOutput, $matches);
		foreach ($matches[1] as $i => $match) {
			if (empty($apiConfigObject[$match][$matches[2][$i]])) {
				new Error(self::$errors['noApiConfigPath'], array($match, $matches[2][$i]));
			}
		}
		TextParser::createObjectString($apiConfig, array('/\\\/', ''));
		self::add('apiConfig', $apiConfig.self::addGlobalAddingCall('apiConfig'));
	}

	private static function addDataConstants($data) {		
		self::add('dataConstants', str_replace('"', "'", json_encode(array_values(self::getDataConstants($data)))).self::addGlobalAddingCall('dataConstants'));
	}

	private static function getDataConstants($data) {
		$allData = array();
		foreach ($data['data'] as $item) {
			$allData = array_merge($allData, $item);
		}
		return $allData;
	}

	private static function addPathToDictionary($url) {
		self::add('pathToDictionary', "'".$url."'");
	}

	private static function addTags($tags) {
		self::add('tags', str_replace('"', "'", json_encode($tags)));
	}

	private static function addProps($props) {
		self::add('props', str_replace('"', "'", json_encode($props)));
	}

	private static function addDecls($decls) {
		if (self::add('decls', str_replace('"', "'", json_encode($decls))) && empty($decls)) {
			new Error(self::$errors['noDeclFilesFound']);
		}
	}

	private static function addEvents($events) {
		self::add('events', str_replace('"', "'", json_encode($events)));
	}

	private static function addRoutes($routes, $controllers) {
		TextParser::createObjectString($routes);
		foreach ($controllers as $i => $ctr) {
			$routes = preg_replace('/\''.$ctr.'\'/', $i, $routes);
		}
		self::add('routes', $routes);
	}

	private static function addErrorRoutes($routes) {
		TextParser::createObjectString($routes);
		self::add('errorRoutes', $routes);
	}

	private static function addHashRouter($isHashRouter) {
		self::add('hashRouter', $isHashRouter ? 'true' : 'false');
	}

	private static function addIndexRoute($indexRoute) {
		self::add('indexRoute', !empty($indexRoute) ? "'".$indexRoute."'" : 'null');
	}

	private static function addDefaultRoute($defaultRoute) {
		self::add('defaultRoute', !empty($defaultRoute) ? "'".$defaultRoute."'" : 'null');
	}

	private static function addViewContainer($viewContainer) {
		self::add('viewContainer', "'->>".$viewContainer."'");
		self::add('viewContainer2', "'->>parental-view-container'");
	}

	private static function addTooltipClass($tooltipClass) {
		self::add('tooltipClass', !empty($tooltipClass) ? "'".$tooltipClass."'" : 'null');
	}

	private static function addTooltipApi($tooltipApi) {
		self::add('tooltipApi', "'".$tooltipApi."'");
	}

	private static function addPathToApi($pathToApi) {
		self::add('pathToApi', "'".$pathToApi."'");
	}

	private static function addPagetitle($pagetitle) {
		self::add('pagetitle', "'".$pagetitle."'");
	}

	private static function addUserOptions($userOptions) {
		TextParser::createObjectString($userOptions, array('/\\\/', ''));
		self::add('user', $userOptions);
	}

	private static function addNullFunction() {
		self::add('nullFunction', 'function(){return}'.self::addGlobalAddingCall('nullFunction'));
	}

	private static function addControllers($controllers) {
		self::add('controllers', str_replace('"', "'", json_encode($controllers)));
	}

	private static function addStopPropagationFunction() {
		self::add('stop', 'function(e){e.stopPropagation()}'.self::addGlobalAddingCall('stop'));
	}

	private static function addPreventDefaultFunction() {
		self::add('prevent', 'function(e){e.preventDefault()}'.self::addGlobalAddingCall('prevent'));
	}
}