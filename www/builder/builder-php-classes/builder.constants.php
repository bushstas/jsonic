<?php
	
class Constants
{
	private static $privateVarList = array(
		'GLOBAL'          => '__G',
		'FUNCS'           => '__B',		
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
		'LOADURL'         => '__LU',
		'PREVENT'         => '__PD',
		'STOP'            => '__SP',
		'CALLBACK'        => '__CB',
		'GETFUNC'         => '__F',
		'FUNCTION'        => '__FN',
		'LOADEDDATA'      => '__DT',		
		
		
		'STATE'           => '__S',
		'POPUPER'         => '__P',
		'CORE'            => '__A',
		'CONTROLLERS'     => '__CT',
		'CONTROLLER'      => '__C',
		'OBJECTS'         => '__O',
		'DIALOGER'        => '__DI'
	);

	private static $publicVarList = array(
		'CONFIG'    => 'CONFIG',
		'LOGGER'    => 'Logger',
		'ROUTER'    => 'Router',
		'USER'      => 'User'
	);

	private static $definitionsList = array(
		'ENTERCOND' => '!this||this==window',
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