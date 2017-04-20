<?php

	$data = array(
		'name' => 'Validator',
		'mode' => 3,
		'thisMethods' => array(
			'assert' => array(
				'args' => array('v','m','e'),
				'body' => "
					if (!m(v)) console.log(e);
					return v;
				"
			)
		)
	);
?>