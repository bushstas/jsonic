<?php

	$data = array(
		'name' => 'StoreKeeper',
		'var' => 'StoreKeeper',
		'define' => true,
		'mode' => 2,
		'before' => "
			var x = 'stored_',
				s = {
				'month': 2592000,
				'day'  : 86400,
				'hour' : 3600,
				'min'  : 60
			};
		",
		'privateMethods' => array(
			'g' => array(
				'args' => array('k'),
				'body' => "
					return x + k;
				"
			),
			'gm' => array(
				'args' => array('p'),
				'body' => "
					var n  = ~~p.replace(/[^\d]/g, '');
					var m = p.replace(/\d/g, '');
					if (!n) return 0;
					if (!s[m]) return 0;
					return s[m] * n * 1000;
				"
			),
			'gi' => array(
				'args' => array('k'),
				'body' => "
					var lk = g(k);
					var i = localStorage.getItem(lk);
					if (!i) return null;
					try {
						i = JSON.parse(i);
					} catch (e) {
						return null;
					}
					return i;
				"
			),
			'ia' => array(
				'args' => array('sm', 'p'),
				'body' => "
					var nm = Date.now(), pm = gm(p);
					if (isString(sm)) sm = stringToNumber(sm);
					return pm && sm && nm - sm < pm;
				"
			)
		),
		'thisMethods' => array(
			'set' => array(
				'args' => array('k', 'v'),
				'body' => "
					var lk = g(k);
					var i = JSON.stringify({
						'data': v,
						'timestamp': Date.now().toString()
					});
					localStorage.setItem(lk, i);
				"
			),
			'get' => array(
				'args' => array('k'),
				'body' => "
					var i = gi(k);
					return ".CONST_OBJECTS.".has(i, 'data') ? i['data'] : null;
				"
			),
			'getActual' => array(
				'args' => array('k', 'p'),
				'body' => "
					var i = gi(k);
					return ".CONST_OBJECTS.".has(i, 'data') && ia(i['timestamp'], p) ? i['data'] : null;
				"
			),
			'remove' => array(
				'args' => array('k'),
				'body' => "
					var lk = g(k);
					localStorage.removeItem(lk);
				"
			)
		)
	);
?>