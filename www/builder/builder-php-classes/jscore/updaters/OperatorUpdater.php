<?php

	$data = array(
		'name' => 'OperatorUpdater',
		'args' => array('operator', 'params', 'names'),
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.operator = operator;
			this.names = isArray(names) ? names : [names];
		",
		'methods' => array(
			'getKeys' => array(
				'body' => "
					return this.names;
				"
			),
			'react' => array(
				'body' => "
					this.operator.update();
				"
			),
			'dispose' => array(
				'body' => "
					this.operator = null;
					this.names = null;
				"
			)
		)
	);
?>