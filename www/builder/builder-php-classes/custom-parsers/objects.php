<?php

	// priority 4
	// code before : $(data, 'name', defaultName);
	// code after  : Objects.get(name, defaultName);

	$content = preg_replace('/\$\(/', "Objects.get(", $content);	
