<?php

	$data = array(
		'name' => 'IfSwitch',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
			this.cur = null;
		",
		'condition' => CONST_ENTERCOND,
		'privateMethods' => array(
			'isChanged' => array(
				'body' => "
					var v = this.params['is']()['is'], c = this.cur;
					if (!isArray(v)) v = [v];
					for (var i = 0; i < v.length; i++) {
						if (!!v[i]) {
							this.cur = i;
							return i !== c;
						}
					}
					this.cur = null;
					return c !== null;
				"
			),
			'createLevels' => array(
				'args' => array('isUpdating'),
				'body' => "
					var p = this.params['is']();
					var c = p['c'], d = p['d'];
					if (!isArray(c)) c = [c];
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