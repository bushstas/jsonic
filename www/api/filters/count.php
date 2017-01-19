<?php
	
	include '../init.php';	
	Obfuscator::obfuscate($_GET, true);
	$filterId = $_GET['filterId'];

	$filters = json_decode('{"filterId":'.$filterId.',"numbers":{"today":31,"yesterday":0,"week":302,"month":1282,"current":385}}', true);

	Obfuscator::obfuscate($filters);

	die(json_encode($filters));

?>