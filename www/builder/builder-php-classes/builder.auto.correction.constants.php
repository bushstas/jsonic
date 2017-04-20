<?php
	
class AutoCorrectionParams
{
	private static $list = array(
		'FUNCS'           => 'FUNCS',
		'GLOBAL'          => 'GLOBAL',
		'CORE'            => 'CORE',
		'ATTRIBUTES'      => 'ATTRIBUTES',
		'EVENTTYPES'      => 'EVENTTYPES',
		'TAGS'            => 'TAGS',
		'ROUTES'          => 'ROUTES',
		'ERRORROUTES'     => 'ERRORROUTES',
		'VIEWCONTAINER'   => 'VIEWCONTAINER',
		'PARENTALVIEWCNT' => 'PARENTALVIEWCNT',
		'PAGETITLE'       => 'PAGETITLE',
		'DICTURL'         => 'DICTURL',
		'DICTIONARY'      => 'DICTIONARY',
		'TOOLTIPCLASS'    => 'TOOLTIPCLASS',
		'TOOLTIPAPI'      => 'TOOLTIPAPI',
		'APIDIR'          => 'APIDIR',
		'INDEXROUTE'      => 'INDEXROUTE',
		'DEFAULTROUTE'    => 'DEFAULTROUTE',
		'HASHROUTER'      => 'HASHROUTER',
		'USEROPTIONS'     => 'USEROPTIONS',
		'WORDS'           => 'WORDS',
		'TEXTS'           => 'TEXTS',
		'CONSTANTS'       => 'CONSTANTS',
		'DATA'            => 'DATA',
		'CONTROLLERS'     => 'CONTROLLERS',
		'CONTROLLER'      => 'CONTROLLER',
		'DIALOGER'        => 'DIALOGER',
		'JSBASE'          => 'JSBASE',
		'OBJECTS'         => 'OBJECTS',
		'COMPONENT'       => 'COMPONENT',
		'PROTO'           => 'PROTO',
		'LOADURL'         => 'LOADURL',
		'BREAK'           => 'BREAK'
	);

	public static function init() {
		foreach (self::$list as $k => $v) {
			define('AUTOCRR_'.$k, $v);	
		}	
	}

	public static function getList() {
		return self::$list;
	}
	
}
?>