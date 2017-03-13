<?php
	
	session_start();
	
	$data = json_decode(file_get_contents('php://input'), true);
	$login = $data['login'];
	$password = $data['password'];
	
	if (!empty($login) && !empty($password)) {
		$_SESSION['accessLevel'] = 30;
		die('{}');
	}


	die(json_encode(array(
		'status' => array(
			'type' => 'guest',
			'accessLevel' => (int)$_SESSION['accessLevel']
		),
		'attributes' => array(				
		 	'name' => 'Бушмакин Стас',
		 	'email' => 'bushstas@mail.ru',
		 	'phone' => '89125954311'
		 ),
		'settings' => array(

		)
	)));

?>