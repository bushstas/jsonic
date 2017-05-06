<?php

	$data = array(
		'mode' => 2,
		'var' => CONST_CONTROLLER,
		'define' => true,
		'name' => CONST_CONTROLLER,
		'thisMethods' => array(
			'get' => array(
				'args' => array('id'),
				'body' => "
					if (isString(".CONST_CONTROLLERS."[id])) {
						".CONST_CONTROLLERS."[id] = ".CONST_GLOBAL.".get(".CONST_CONTROLLERS."[id]);
					}
					if (isFunction(".CONST_CONTROLLERS."[id])) {
						".CONST_CONTROLLERS."[id] = new ".CONST_CONTROLLERS."[id]();
						".CONST_CORE.".initiate.call(".CONST_CONTROLLERS."[id]);
					}
					return ".CONST_CONTROLLERS."[id];
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