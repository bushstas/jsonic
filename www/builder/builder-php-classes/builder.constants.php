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
		
		'DICTIONARY_SHORT'=> '__D',
		'OBJECTS_SHORT'   => '__O',
		'POPUPER_SHORT'   => '__P',
		'DATES_SHORT'     => '__DA',
		'ROUTER_SHORT'    => '__R',
		'USER_SHORT'      => '__U',
		'STATE_SHORT'     => '__S',
		'DIALOGER_SHORT'  => '__DI',
		'STORE_SHORT'     => '__ST',
		
		'CORE'            => '__A',
		'CONTROLLERS'     => '__CT',
		'CONTROLLER'      => '__C'
		
	);

	private static $publicVarList = array(
		'CONFIG'     => 'CONFIG',
		'LOGGER'     => 'Logger',
		'ROUTER'     => 'Router',
		'USER'       => 'User',
		'DATES'      => 'Dates',
		'DECLINER'   => 'Decliner',
		'OBJECTS'    => 'Objects',
		'POPUPER'    => 'Popuper',
		'DICTIONARY' => 'Dictionary',
		'STATE'      => 'State',
		'DIALOGER'   => 'Dialoger',
		'STORE'      => 'StoreKeeper'
	);

	private static $definitionsList = array(
		'ENTERCOND' => '!this||this==window',
		'BREAK'     => '_brk',
		'COMPONENT' => 'c',
		'PROTO'     => 'p',
		'LESS'      => '_#_LESS_#_',
		'MORE'      => '_#_MORE_#_'
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