<?php

	$data = array(
		'name' => 'Switch',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
			this.cur = null;
		",
		'condition' => CONST_ENTERCOND,
		'privateMethods' => array(
			'isChanged' => array(
				'body' => "
					var p = this.params['sw']();
					var v = p['sw'], vs = p['cs'], c = this.cur;
					if (!isUndefined(vs)) {
						if (!isArray(vs)) vs = [vs];
						for (var i = 0; i < vs.length; i++) {
							if (v === vs[i]) {
								this.cur = i;
								return i !== c;
							}
						}
					}
					this.cur = null;
					return c !== null;
				"
			),
			'createLevels' => array(
				'args' => array('isUpdating'),
				'body' => "
					var p = this.params['sw']();
					var c = p['c'], d = p['d'];
					if (this.cur !== null) {
						".CONST_CORE.".createLevel.call(this, c[this.cur], isUpdating);
					} else if (!isUndefined(d)) {
						".CONST_CORE.".createLevel.call(this, d, isUpdating);
					}
				"
			)
		),
		'methods' => array(
			'render' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					".CONST_CORE.".initOperator.call(this, pe, pl);
					isChanged.call(this);
					createLevels.call(this, false);
				"
			),
			'update' => array(
				'args' => array(''),
				'body' => "
					if (isChanged.call(this)) {
						".CONST_CORE.".disposeLevels.call(this);
						createLevels.call(this, true);
					}
				"
			),
			'dispose' => array(
				'body' => "
					".CONST_CORE.".disposeOperator.call(this);
					this.cur = null;
				"
			)
		)
	);
?>