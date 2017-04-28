<?php

	$data = array(
		'mode' => 4,
		'prototypeOf' => 'String',
		'methods' => array(
			'isEmpty' => array(
				'body' => "
					return !(/[^\s]/).test(this);					
				"
			),
			'toArray' => array(
				'args' => array('delimiter'),
				'body' => "
					delimiter = delimiter || ',';
					var ar = [];
					var parts= this.split(delimiter);
					for (var i = 0; i < parts.length; i++) {
						if (parts[i]) ar.push(parts[i].trim());
					}
					return ar;
				"
			)
		)
	);
?>