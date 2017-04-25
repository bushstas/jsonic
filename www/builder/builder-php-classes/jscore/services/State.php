<?php

	$data = array(
		'name' => 'State',
		'var' => 'State',
		'define' => true,
		'mode' => 2,
		'before' => "
			var listeners = {},
				subscribers = {},
				updaters = {},
				vars = {};
		",
		'thisMethods' => array(
			'subscribe' => array(
				'args' => array('subscriber', 'name', 'callback'),
				'body' => "
					var s = subscribers[name] = subscribers[name] || [];
					s.push([callback, subscriber]);
				"
			),
			'unsubscribe' => array(
				'args' => array('subscriber', 'name'),
				'body' => "
					var s = subscribers[name];
					if (isArray(s)) {
						var done = false;
						while (!done) {
							done = true;
							for (var i = 0; i < s.length; i++) {
								if (s[i][1] == subscriber) {
									s.splice(i, 1);
									done = false;
									break;
								}
							}
						}
					}	
				"
			),
			'get' => array(
				'args' => array('name'),
				'body' => "
					return vars[name];
				"
			),
			'set' => array(
				'args' => array('name', 'value'),
				'body' => "
					var updated, data = name;
					if (!isUndefined(value)) {
						data = {};
						data[name] = value;
					}
					var changed = {}, isChanged = false;
					for (var k in data) {
						if (vars[k] == data[k]) continue;
						if (isArray(vars[k]) && isArray(data[k]) && ".CONST_OBJECTS.".equals(vars[k], data[k])) continue;
						isChanged = true;
						changed[k] = data[k];
					}
					if (isChanged) {
						for (var k in changed) {
							vars[k] = changed[k];
							var s = subscribers[k];
							if (isArray(s)) {
								for (var i = 0; i < s.length; i++) {
									if (isFunction(s[i][0])) {
										s[i][0].call(s[i][1] || null, changed[k], k);
									}
								}
							}
							var u = updaters[k];
							if (isArray(u)) {
								updated = [];
								for (var i = 0; i < u.length; i++) {
									if (updated.indexOf(u[i]) == -1) {
										u[i].react(changed);
										updated.push(u[i]);
									}
								}
							}
						}
					}
					updated = changed = data = null;
				"
			),
			'listen' => array(
				'args' => array('listener', 'name', 'callback'),
				'body' => "
					if (!isArray(listeners[name])) listeners[name] = [];
					listeners[name].push([callback, listener]);
				"
			),
			'unlisten' => array(
				'args' => array('name', 'listener'),
				'body' => "
					if (isArray(listeners[name])) {
						var indexes = [];
						for (var i = 0; i < listeners[name].length; i++) {
							if (listeners[name][i][1] == listener) indexes.push(i);
						}
						listeners[name].removeIndexes(indexes);
					}
				"
			),
			'dispatchEvent' => array(
				'args' => array('name', 'args'),
				'body' => "
					if (isArray(listeners[name])) {
						for (var i = 0; i < listeners[name].length; i++) {
							if (isFunction(listeners[name][i][0])) {
								listeners[name][i][0].apply(listeners[name][i][1] || null, args);
							}
						}
					}
				"
			),
			'createUpdater' => array(
				'args' => array('updater', 'component', 'obj', 'props'),
				'body' => "
					var u = new updater(obj, props, props['g']);
					var keys = u.getKeys()
					for (var i = 0; i < keys.length; i++) {
						updaters[keys[i]] = updaters[keys[i]] || [];
						updaters[keys[i]].push(u);
					}
				"
			),
			'dispose' => array(
				'args' => array('subscriber'),
				'body' => "
					var k, i, s;
					for (k in subscribers) {
						s = [];
						for (i = 0; i < subscribers[k].length; i++) {
							if (subscribers[k][i] != subscriber) s.push(subscribers[k][i]);
							else alert(111222)
						}
						subscribers[k] = s;
					}
				"
			)
		)
	);
?>