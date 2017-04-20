<?php
	
class AutoCorrectionParams
{
	private static $list = array(
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
		'JSBASE'          => 'JSBASE',
		'OBJECTS'         => 'OBJECTS',
		'COMPONENT'       => 'COMPONENT',
		'PROTO'           => 'PROTO',
		'LOADURL'         => 'LOADURL',
		'BREAK'           => 'BREAK'
	);

	public static function init() {
		foreach (self::$list as $k => $v) {
			define('CONST_'.$k, $v);	
		}	
	}
	
}
?>