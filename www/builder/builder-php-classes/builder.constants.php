<?php
	
class Constants
{
	private static $privateVarList = array(
		'GLOBAL'          => '_G_',
		'FUNCS'           => '_F_',		
		'CORE'            => '_C_',
		'ATTRIBUTES'      => '__AT',
		'EVENTTYPES'      => '__ET',
		'TAGS'            => '__TG',
		'ROUTES'          => '__RT',
		'ERRORROUTES'     => '__ER',
		'HASHROUTER'      => '__HR',
		'INDEXROUTE'      => '__IR',
		'DEFAULTROUTE'    => '__DR',
		'VIEWCONTAINER'   => '__VC',
		'PARENTALVIEWCNT' => '__PV',
		'PAGETITLE'       => '__PT',
		'DICTURL'         => '__DU',
		'DICTIONARY'      => '__D',
		'TOOLTIPCLASS'    => '__TC',
		'TOOLTIPAPI'      => '__TA',
		'APIDIR'          => '__AD',		
		'USEROPTIONS'     => '__UO',
		'WORDS'           => '__DW',
		'TEXTS'           => '__T',
		'CONSTANTS'       => '__',
		'DATA'            => '__V',
		'CONTROLLERS'     => '__CT',
		'CONTROLLER'      => '__C',
		'DIALOGER'        => '__DI',
		'JSBASE'          => '__JS',
		'OBJECTS'         => '__O',
		'LOADURL'         => '__LU',
		'PREVENT'         => '__PD',
		'STOP'            => '__SP',
		'STATE'           => '__S',
		'POPUPER'         => '__P',
		'CALLBACK'        => '__CB',
		'GETFUNC'         => '__F',
		'FUNCTION'        => '__FN',
		'LOADEDDATA'      => '__DT'
	);

	private static $publicVarList = array(
		'CONFIG'    => 'CONFIG',
		'LOGGER'    => 'Logger',
		'ROUTER'    => 'Router',
		'USER'      => 'User'
	);

	private static $definitionsList = array(		
		'BREAK'     => '_brk',
		'COMPONENT' => 'c',
		'PROTO'     => 'p'		
	);

	public static function init() {
		foreach (self::$privateVarList as $k => $v) {
			define('CONST_'.$k, $v);
		}
		foreach (self::$publicVarList as $k => $v) {
			define('CONST_'.$k, $v);
		}
		foreach (self::$definitionsList as $k => $v) {
			define('CONST_'.$k, $v);
		}
	}

	public static function getReservedPrivateVarNames() {
		return array_values(self::$privateVarList);
	}

	public static function getReservedPublicVarNames() {
		return array_values(self::$publicVarList);
	}
	
}
?>