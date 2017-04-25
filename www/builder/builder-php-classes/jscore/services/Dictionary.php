<?php

	$data = array(
		'name' => 'Dictionary',
		'var' => 'Dictionary',
		'define' => true,
		'mode' => 2,
		'before' => "
			var dictionaryUrl = ".CONST_DICTURL.";
			var items = {}, callbacks, loaded = {};
		",
		'privateMethods' => array(
			'onLoad' => array(
				'args' => array('data'),
				'body' => "
					if (isObject(data)) {
						for (var k in data) this.set(k, data[k]);
						if (!isArray(callbacks)) return;
						for (var i = 0; i < callbacks.length; i++) {
							if (isFunction(callbacks[i][0])) {
								callbacks[i][0].call(callbacks[i][1] || null);
							} else if (isString(callbacks[i][0]) && isComponentLike(callbacks[i][1])) {
								callbacks[i][1].set(callbacks[i][0], items[callbacks[i][2]]);
							}
						}
						callbacks = null;
					}
				"
			)
		),
		'thisMethods' => array(
			'load' => array(
				'args' => array('routeName'),
				'body' => "
					if (loaded[routeName]) return;
					if (!isNone(dictionaryUrl)) {
						Loader.get(dictionaryUrl, {'route': routeName}, onLoad, this);
					}
					loaded[routeName] = true;
				"
			),
			'get' => array(
				'args' => array('key', 'callbackOrPropName', 'thisObj'),
				'body' => "
					var item = ".CONST_OBJECTS.".get(items, key);
					if (item) return item;
					callbacks = callbacks || [];
					callbacks.push([callbackOrPropName, thisObj, key]);
				"
			),
			'set' => array(
				'args' => array('key', 'value'),
				'body' => "
					items[key] = value;
				"
			),
			'setData' => array(
				'args' => array('routeName', 'data'),
				'body' => "
					loaded[routeName] = true;
					for (var k in data) this.set(k, data[k]);
				"
			)	
		)
	);
?>