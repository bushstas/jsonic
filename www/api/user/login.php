<?php
	
	session_start();
	$data = json_decode(file_get_contents('php://input'), true);

	$login = $data['login'];
	$password = $data['password'];

	if (!empty($login) || !empty($password)) {
		$_SESSION['accessLevel'] = 30;
		die(json_encode(array(
			'accessLevel' => 30,
	 		'type' => 'guest',
	 		'success' => true
		)));
	}

	die(json_encode(array(
		'accessLevel' => (int)$_SESSION['accessLevel'],
	 	'type' => 'guest'
	)));

?>