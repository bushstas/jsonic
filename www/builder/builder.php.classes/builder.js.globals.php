<?php

class JSGlobals
{
	private static $output	= array();
	private static $varNames = array(
		'textConstants'    => '__',
		'apiConfig'        => 'CONFIG',
		'dataConstants'    => '__V',
		'pathToDictionary' => '__DICTURL',
		'tags'             => '__TAGS',
		'props'            => '__A',
		'decls'            => '__DW',
		'events'           => '__EVENTTYPES',
		'routes'           => '__ROUTES',
		'errorRoutes'      => '__ERRORROUTES',
		'hashRouter'       => '__HASHROUTER',
		'indexRoute'       => '__INDEXROUTE',
		'defaultRoute'     => '__DEFAULTROUTE',
		'viewContainer'    => '__VIEWCONTAINER'
	);

	private static $errors = array(
		'invalidApiConfig' => "Файл конфигурации путей к api <b>config.js</b> не корректен. Содержимое должно иметь вид <xmp>var CONFIG = {\n\t'items': {\n\t\t'get': 'items/get.php',\n\t\t'add': 'items/add.php',\n\t\t'remove': 'items/remove.php'\n\t}\n}</xmp>"
	);
	
	public static function run($data) {
		self::addTextConstants($data['texts']);
		self::addApiConfig($data['config']);
		self::addDataConstants($data['data']);
		self::addPathToDictionary($data['pathToDictionary']);
		self::addTags($data['tags']);
		self::addProps($data['props']);
		self::addDecls($data['decls']);
		self::addEvents($data['events']);
		self::addRoutes($data['routes']);
		self::addErrorRoutes($data['errorRoutes']);
		self::addHashRouter($data['hashRouter']);
		self::addIndexRoute($data['indexRoute']);
		self::addDefaultRoute($data['defaultRoute']);
		self::addViewContainer($data['viewContainer']);
		printArr(self::$output);
	}

	public static function getVarName($key) {
		return self::$varNames[$key];
	}

	private static function add($key, $content) {
		self::$output[] = "var ".self::$varNames[$key]." = ".$content.';';
	}

	private static function addTextConstants($data) {
		self::add('textConstants', str_replace('"', "'", json_encode($data['texts'])));
	}

	private static function addApiConfig($apiConfig) {
		TextParser::transformIntoValidJson($apiConfig);
		$apiConfigObject = json_decode($apiConfig, true);
		if ($apiConfigObject === null) {
			new Error(self::$errors['invalidApiConfig']);
		}
		TextParser::createObjectString($apiConfig, array('/\\\/', ''));
		self::add('apiConfig', $apiConfig);
	}

	private static function addDataConstants($data) {
		$allData = array();
		foreach ($data['data'] as $item) {
			$item = trim(trim($item, '}'), '{');
			$allData[] = $item;
		}
		self::add('dataConstants', '{'.str_replace('"', "'", implode(',', $allData)).'}');
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
		self::add('decls', str_replace('"', "'", json_encode($decls)));
	}

	private static function addEvents($events) {
		self::add('events', str_replace('"', "'", json_encode($events)));
	}

	private static function addRoutes($routes) {
		TextParser::createObjectString($routes, array('/view":"([^"]+)"/', "view':$1"));
		self::add('routes', $routes);
	}

	private static function addErrorRoutes($routes) {
		TextParser::createObjectString($routes, array('/":"([^"]+)"/', "':$1"));
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
	}

	

	
}