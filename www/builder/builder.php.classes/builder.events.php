<?php

class Events 
{	
	private static $list = array(
		'click','mouseover','mouseout','mouseenter','mouseleave','mousemove','contextmenu','dblclick','mousedown','mouseup','keydown','keyup','keypress','blur','change','focus',
		'focusin','focusout','input','invalid','reset','search','select','submit','drag','dragend','dragenter','dragleave','dragover','dragstart','drop','copy','cut','paste',
		'popstate','wheel','storage','show','toggle','touchend','touchmove','touchstart','touchcancel','message','error','open','transitionend','abort','play','pause','load',
		'durationchange','progress','resize','scroll','unload','hashchange','beforeunload','pageshow','pagehide'
	);	

	public static function getList() {
		return self::$list;
	}

	public static function get($event) {
		return array_search($event, self::$list);
	}
}