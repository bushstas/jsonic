<?php

	$data = array(
		'name' => 'ComponentUpdater',
		'args' => array('cmp', 'params'),
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.cmp = cmp;
			this.params = params;
		",
		'methods' => array(
			'getKeys' => array(
				'body' => "
					var a = [], p = this.params;
					for (var k in p['n']) {
						if (a.indexOf(p['n'][k]) == -1) {
							if (isString(p['n'][k])) a.push(p['n'][k]);
							else a.push.apply(a, p['n'][k]);
						}
					}
					return a;
				"
			),
			'react' => array(
				'args' => array('d'),
				'body' => "
					var p = this.params,
						pp = p['p'](), cp = {},
						pc = !!p['n']['props'];
					if (pc && isObject(pp['p'])) {
						cp =  pp['p'];
					}
					for (var k in p['n']) {
						if (isString(p['n'][k]) && !isUndefined(d[p['n'][k]])) {
							cp[k] = pc && pp['ap'] ? pp['ap'][k] : pp['p'][k];
						}
					}
					this.cmp.set(cp);
				"
			),
			'dispose' => array(
				'body' => "
					this.cmp = null;
					this.params = null;
				"
			)
		)
	);
?>