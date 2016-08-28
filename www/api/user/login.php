<?php
	
	session_start();
	if (isset($_POST['formKey'])) {
		$login = $_POST['login'];
		$password = $_POST['password'];
	} else {
		$data = json_decode(file_get_contents('php://input'), true);
		$login = $data['login'];
		$password = $data['password'];
	}

	if (!empty($login) || !empty($password)) {
		$_SESSION['accessLevel'] = 30;
		$output = json_encode(array(
			'accessLevel' => 30,
	 		'type' => 'guest',
	 		'success' => true
		));
	} else {
		$output = json_encode(array(
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
		));
	}
	if (isset($_POST['formKey'])) {
		die('<script>parent["'.$_POST['formKey'].'"].handleResponse(\''.$output.'\')</script>');
	} else {
		die($output);
	}

?>