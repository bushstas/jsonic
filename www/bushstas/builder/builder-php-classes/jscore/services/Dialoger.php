<?php

	$data = array(
		'name' => 'Dialoger',
		'var' => 'Dialoger',
		'isToCheckUsing' => true,
		'define' => true,
		'mode' => 2,
		'before' => "
			var ds = {};
			var cid, dc, d, opts;
		",
		'after' => "window.addEventListener('popstate', closeAll);",
		'privateMethods' => array(
			'defineId' => array(
				'args' => array('c', 'id'),
				'body' => "
					dc = c;
					if (!isFunction(c)) return '_';
					cid = c.name + (isPrimitive(id) ? '_' + id : '');
				"
			),
			'defineDialog' => array(
				'body' => "
					if (isUndefined(ds[cid])) {
						ds[cid] = new dc();
						".CONST_CORE.".initiate.call(ds[cid]);
						ds[cid].render(document.body);
					}
					d = ds[cid];
				"
			),
			'showDialog' => array(
				'body' => "
					if (isObject(opts)) d.set(opts);
					d.show();
				"
			),
			'closeAll' => array(
				'body' => "
					for (var k in ds) ds[k].hide();
				"
			)
		),
		'thisMethods' => array(
			'show' => array(
				'args' => array('c', 'options'),
				'body' => "
					if (isString(c)) c = ".CONST_GLOBAL.".get(c);
					if (isFunction(c)) {
						var id;
						if (isObject(options)) {
							id = options['did'];
						}
						opts = options;
						defineId(c, id);
						defineDialog();
						showDialog();
					}
				"
			),
			'hide' => array(
				'args' => array('c', 'id'),
				'body' => "
					defineId(c, id);
					if (ds[cid]) ds[cid].close();
				"
			),
			'get' => array(
				'args' => array('c', 'id'),
				'body' => "
					defineId(c, id);
					return ds[cid];
				"
			),
			'expand' => array(
				'args' => array('c', 'id'),
				'body' => "
					defineId(c, id);
					if (ds[cid]) ds[cid].expand(true);
				"
			),
			'minimize' => array(
				'args' => array('c', 'id'),
				'body' => "
					defineId(c, id);
					if (ds[cid]) ds[cid].expand(false);
				"
			),
			'dispose' => array(
				'args' => array('c', 'id'),
				'body' => "
					defineId(c, id);
					if (ds[cid]) ds[cid].dispose();
					delete ds[cid];
				"
			)
		)
	);
?>