<?php
	
	// priority 3
	// code before : data[] = 2;
	// code after  : data.push(2);

	$content = preg_replace('/([\w\]\)]) *\[\] *= *([^;\n]+)/i', "$1.push($2)", $content);