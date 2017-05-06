<?php
	
	include '../init.php';	
	$filters = file_get_contents('filters.json');
	$filters = json_decode($filters, true);

	Obfuscator::obfuscate($filters);

	die(json_encode($filters));

?>