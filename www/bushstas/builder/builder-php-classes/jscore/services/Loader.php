<?php

	$data = array(
		'name' => 'Loader',
		'var' => 'Loader',
		'define' => true,
		'mode' => 2,
		'before' => "
			var requests = {};
		",
		'privateMethods' => array(
			'getRequest' => array(
				'args' => array('url', 'th'),
				'body' => "
					return requests[url] || createRequest(url, th);
				"
			),
			'createRequest' => array(
				'args' => array('url', 'th'),
				'body' => "
					var ajr = ".CONST_GLOBAL.".get('AjaxRequest');
					requests[url] = new ajr(url, null, null, th);
					return requests[url];	
				"
			)
		),
		'thisMethods' => array(
			'get' => array(
				'args' => array('url', 'data', 'callback', 'th'),
				'body' => "
					this.doAction('GET', url, data, callback, th);
				"
			),
			'post' => array(
				'args' => array('url', 'data', 'callback', 'th'),
				'body' => "
					this.doAction('POST', url, data, callback, th);
				"
			),
			'put' => array(
				'args' => array('url', 'data', 'callback', 'th'),
				'body' => "
					this.doAction('PUT', url, data, callback, th);
				"
			),
			'delete' => array(
				'args' => array('url', 'data', 'callback', 'th'),
				'body' => "
					this.doAction('DELETE', url, data, callback, th);
				"
			),
			'doAction' => array(
				'args' => array('method', 'url', 'data', 'callback', 'th'),
				'body' => "
					var req = getRequest(url, th);
					if (isFunction(callback)) req.setCallback(callback);
					req.send(method, data);
				"
			)
		)
	);
?>