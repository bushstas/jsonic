<?php

	$data = array(
		'name' => 'NodeUpdater',
		'args' => array('node', 'params', 'names'),
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.node = node;
			this.params = params;
			this.names = isArray(names) ? names : [names];
		",
		'methods' => array(
			'getKeys' => array(
				'body' => "
					return this.names;
				"
			),
			'react' => array(
				'args' => array('d'),
				'body' => "
					var t;
					if (isFunction(this.params['v'])) t = this.params['v'](); 
					else t = d[this.names[0]];
					this.node.textContent = t || '';
				"
			),
			'dispose' => array(
				'body' => "
					this.node = null;
					this.params = null;
					this.names = null;
				"
			)
		)
	);
?>