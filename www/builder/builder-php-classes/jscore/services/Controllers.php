<?php

	$data = array(
		'mode' => 2,
		'var' => AUTOCRR_CONTROLLER,
		'define' => true,
		'name' => 'Controllers',
		'thisMethods' => array(
			'get' => array(
				'args' => array('id'),
				'body' => "
					if (isString({{".AUTOCRR_CONTROLLERS."}}[id])) {
						{{".AUTOCRR_CONTROLLERS."}}[id] = {{GLOBAL}}.get({{".AUTOCRR_CONTROLLERS."}}[id]);
					}
					if (isFunction({{".AUTOCRR_CONTROLLERS."}}[id])) {
						{{".AUTOCRR_CONTROLLERS."}}[id] = new {{".AUTOCRR_CONTROLLERS."}}[id]();
						{{".AUTOCRR_CORE."}}.initiate.call({{".AUTOCRR_CONTROLLERS."}}[id]);
					}
					return {{".AUTOCRR_CONTROLLERS."}}[id];
				"
			),
			'load' => array(
				'args' => array('ids'),
				'body' => "
					var ctr;
					if (!isArray(ids)) ids = [ids];
					for (var i = 0; i < ids.length; i++) {
						ctr = this.get(ids[i]);
						if (isController(ctr)) {
							ctr.doAction(null, 'load');
						}
					}
				"
			)
		)
	);
?>