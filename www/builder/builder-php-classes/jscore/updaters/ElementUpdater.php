<?php

	$data = array(
		'name' => 'ElementUpdater',
		'args' => array('element', 'params', 'names'),
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.element = element;
			this.params = params;
			this.names = names;
		",
		'methods' => array(
			'getKeys' => array(
				'body' => "
					var a = [], n = this.names;
					for (var k in n) {
						if (isString(n[k])) a.push(n[k]);
						else a.push.apply(a, n[k]);
					}
					return a;
				"
			),
			'react' => array(
				'args' => array('d'),
				'body' => "
					var n = this.names,
						p = this.params, 
						k, i, pn;
					for (k in n) {
						pn = n[k];
						if (isString(pn)) pn = [pn];
						for (i = 0; i < pn.length; i++) {
							if (!isUndefined(d[pn[i]])) {
								this.element.attr(".CONST_ATTRIBUTES."[k] || k, p['p']()[k] || '');
								break;
							}
						}
					}
				"
			),
			'dispose' => array(
				'body' => "
					this.element = null;
					this.params = null;
					this.names = null;
				"
			)
		)
	);
?>