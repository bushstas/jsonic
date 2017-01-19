<?php
	
	// priority 20
	// code before : element->id;
	// code after  : element.getData('id');

	$content = preg_replace('/([>\)\]\w]) *-> *(\w+)/', "$1.getData('$2')", $content);