<?php

	$data = array(
		'name' => 'From',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
		",
		'condition' => '!this||this==window',
		'privateMethods' => array(
			'createLevels' => array(
				'args' => array('isUpdating'),
				'body' => "
					var p = this.params, f = p['f'];
					p = (isFunction(p['p']) ? p['p']() : p['p']) || [];
					var a = ~~p[0], b = ~~p[1], s = ~~p[2] || 1;
					for (var i = a; i <= b; i += s) {
						".CONST_CORE.".createLevel.call(this, f(i), isUpdating);
					}
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
