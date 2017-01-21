<?php

	// priority 5
	
	$content = preg_replace('/--> *(\w+) *(\((.*)\))* *;*/', "this.dispatchEvent('$1',$3);", $content);
	$content = preg_replace('/==> *(\w+) *(\((.*)\))* *;*/', "State.dispatchEvent('$1',$3);", $content);