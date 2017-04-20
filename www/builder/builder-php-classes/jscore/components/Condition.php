<?php

	$data = array(
		'name' => 'Condition',
		'args' => array('params'),
		'afterCondition' => "
			this.params = params;
			this.isTrue = !!params['i']();
		",
		'condition' => '!this||this==window',
		'privateMethods' => array(
			'createLevel' => array(
				'args' => array('isUpdating'),
				'body' => "
					var l = {{".AUTOCRR_GLOBAL."}}.get('Level');
					this.level = new l(this.parentLevel.getComponent());
					var nextSiblingChild = isUpdating ? {{".AUTOCRR_CORE."}}.getNextSiblingChild.call(this) : null;
					this.level.render(getChildren.call(this), this.parentElement, this.parentLevel, nextSiblingChild);
				"
			),
			'disposeLevel' => array(
				'body' => "
					if (this.level) this.level.dispose();
					this.level = null;
				"
			),
			'getChildren' => array(
				'body' => "
					var p = this.params;
					if (this.isTrue) return isFunction(p['c']) ? p['c']() : p['c'];
					return isFunction(p['e']) ? p['e']() : p['e'];
				"
			)
		),
		'methods' => array(
			'render' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					this.parentElement = pe;
					this.parentLevel = pl;
					createLevel.call(this);
				"
			),
			'update' => array(
				'body' => "
					var i = !!this.params['i']();
					if (i != this.isTrue) {
						this.isTrue = i;
						disposeLevel.call(this);
						createLevel.call(this, 1);
					}
				"
			),
			'dispose' => array(
				'body' => "
					{{".AUTOCRR_CORE."}}.disposeLinks.call(this);
					disposeLevel.call(this);
					this.parentElement = null;
					this.parentLevel = null;
					this.params = null;
				"
			)
		)
	);
?>
