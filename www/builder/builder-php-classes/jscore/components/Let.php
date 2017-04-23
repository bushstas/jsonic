<?php

	$data = array(
		'name' => 'Let',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
		",
		'condition' => CONST_ENTERCOND,
		'privateMethods' => array(
			'createLevels' => array(
				'args' => array('isUpdating'),
				'body' => "
					".CONST_CORE.".createLevel.call(this, this.params['l'](), isUpdating);
				"
			)
		),
		'methods' => array(
			'render' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					".CONST_CORE.".initOperator.call(this, pe, pl);
					createLevels.call(this, false);
				"
			),
			'update' => array(
				'args' => array(''),
				'body' => "
					".CONST_CORE.".disposeLevels.call(this);
					createLevels.call(this, true);
				"
			),
			'dispose' => array(
				'body' => "
					".CONST_CORE.".disposeOperator.call(this);
				"
			)
		)
	);
?>