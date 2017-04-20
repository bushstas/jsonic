<?php

	$data = array(
		'name' => 'Logger',
		'var' => AUTOCRR_LOGGER,
		'define' => true,
		'mode' => 2,
		'thisMethods' => array(
			'log' => array(
				'args' => array('message', 'method', 'object', 'opts'),
				'body' => "
					window.console.log(message);
				"
			)
		)
	);
?>