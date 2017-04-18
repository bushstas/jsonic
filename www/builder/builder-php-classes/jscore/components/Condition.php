<?php

	$data = array(
		'mode' => 2,
		'name' => 'Condition',
		'args' => array('params'),
		'before' => "		
			var isTrue = !!params['i'](), level, parentElement, parentLevel;
		",
		'privateMethods' => array(
			'createLevel' => array(
				'args' => array('isUpdating'),
				'body' => "
					var l = {{".AUTOCRR_GLOBAL."}}.get('Level');
					level = new l(parentLevel.getComponent());
					var nextSiblingChild = isUpdating ? {{".AUTOCRR_GLOBAL."}}.get('Core').getNextSiblingChild.call(this) : null;
					level.render(getChildren.call(this), parentElement, parentLevel, nextSiblingChild);
				"
			),
			'disposeLevel' => array(
				'body' => "
					if (level) level.dispose();
					level = null;
				"
			),
			'getChildren' => array(
				'body' => "
					if (isTrue) return isFunction(params['c']) ? params['c']() : params['c'];
					return isFunction(params['e']) ? params['e']() : params['e'];
				"
			)
		),
		'thisMethods' => array(
			'render' => array(
				'args' => array('pe', 'pl'),
				'body' => "
					parentElement = pe;
					parentLevel = pl;
					createLevel.call(this);
				"
			),
			'update' => array(
				'body' => "
					var i = !!params['i']();
					if (i != isTrue) {
						isTrue = i;
						disposeLevel();
						createLevel.call(this, true);
					}
				"
			),
			'dispose' => array(
				'body' => "
					{{".AUTOCRR_GLOBAL."}}.get('Core').disposeLinks.call(this);
					disposeLevel();
					parentElement = null;
					parentLevel = null;
					params = null;
				"
			)
		)
	);
?>
