<?php

	// priority 1000
	// code before : each (items as item) {
	// code after  : var b=value,idx;for(idx=0;idx<b.length;idx++){var v=b[idx];

	$itemsName = '_items';
	$names = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','_');		
	$regexp = '/\beach\s*\(([\w\.\[\]]+) +as +(\w+)\)\s*\{/';
	$data = Splitter::split($regexp, $content);
	if (!empty($data['items'])) {
		$properNames = array();

		foreach ($names as $name) {
			if (!preg_match('/\b'.$name.'\b/', $content)) {
				$properNames[] = $name;
				if (count($properNames) >= count($data['delimiters'])) {
					break;
				}
			}
		}
		$content = '';
		foreach ($data['items'] as $i => $item) {
			$content .= $item;
			if (isset($data['delimiters'][$i])) {
				$v = $properNames[$i];
				$idx = $i + 1;
				if ($idx == 1) $idx = '';
				$d = $data['delimiters'][$i];
				$content .= preg_replace($regexp, "var ".$v."=$1,idx".$idx.";for(idx".$idx."=0;idx".$idx."<".$v.".length;idx".$idx."++){var $2=".$v."[idx".$idx."];", $d);
			}
		}
	}