<?php
	
	include '../init.php';
	$input = json_decode(file_get_contents('php://input'), true);
	extract($input);
	
	$filters = json_decode(file_get_contents('filters.json'), true);
	
	foreach ($filters as &$filter) {
		if ($filter['filterId'] == $filterId) {
			$filter[$param] = $value;
		}
	}
	file_put_contents('filters.json', json_encode($filters));
	die(json_encode(array('success' => true)));

?>