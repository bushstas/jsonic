<?php

	// priority 5
	
	$content = preg_replace('/--> *(\w+) *(\((.*)\))* *;*/', "this.dispatchEvent('$1',$3);", $content);
	$content = preg_replace('/===> *(\w+) *(\((.*)\))* *;*/', "StateManager.dispatchEvent(this,1,'$1',$3);", $content);
	$content = preg_replace('/==> *(\w+) *(\((.*)\))* *;*/', "StateManager.dispatchEvent(this,0,'$1',$3);", $content);