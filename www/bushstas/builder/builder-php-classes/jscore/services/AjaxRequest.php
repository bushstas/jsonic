<?php

	$data = array(
		'name' => 'AjaxRequest',
		'args' => array('url', 'callback', 'params', 'thisObj'),
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.url = url;
			this.callback = callback;
			this.params = params;
			this.thisObj = thisObj;
		",
		'privateMethods' => array(
			'correctUrl' => array(
				'args' => array('u'),
				'body' => "
					u = u.replace(/^[\.\/]+/, '');
					if (isString(".CONST_APIDIR.")) {
						var regExp = new RegExp('^' + ".CONST_APIDIR." + \"\/\");
						u = ".CONST_APIDIR." + '/' + u.replace(regExp, '');
					}
					return '/' + u;
				"
			),
			'createRequest' => array(
				'args' => array(''),
				'body' => "
					this.request = new XMLHttpRequest();
					this.request.onreadystatechange = onReadyStateChange.bind(this);
				"
			),
			'getRequestContent' => array(
				'args' => array('method', 'pars'),
				'body' => "
					if (".CONST_OBJECTS.".empty(pars)) return '';
					if (!isObject(pars)) {
						return pars.toString();
					} else if (pars instanceof FormData) {
						return pars;
					} else if (method == 'GET') {
						var content = [];
						for (var k in pars) {				
							content.push(k + '=' + (!!pars[k] || pars[k] == 0 ? pars[k] : '').toString());
						}
						return '?' + content.join('&');
					}
					return JSON.stringify(pars || '');
				"
			),
			'onReadyStateChange' => array(
				'args' => array('e'),
				'body' => "
					var req = e.target;
					if (this.active && req.readyState == 4) {
						this.active = false;
						var response = req.response;
						var data;
						try {
							data = JSON.parse(response);
						} catch (e) {
							data = response;
						}
						if (isFunction(this.callback)) {
							this.callback.call(this.thisObj || null, data);
						}
					}
				"
			)
		),
		"methods" => array(
			'setHeaders' => array(
				'args' => array('h'),
				'body' => "
					this.headers = h;
				"
			),
			'setResponseType' => array(
				'args' => array('r'),
				'body' => "
					this.responseType = r;
				"
			),
			'setWithCredentials' => array(
				'args' => array('w'),
				'body' => "
					this.withCredentials = w;
				"
			),
			'setCallback' => array(
				'args' => array('cb'),
				'body' => "
					this.callback = cb;
				"
			),
			'execute' => array(
				'args' => array('pars'),			
				'body' => "
					this.active = true;
					pars = pars || this.params;
					var u = this.tempUrl || this.url,
						method = this.method || 'POST',
						content = getRequestContent.call(this, method, pars);
					createRequest.call(this);
					if (method == 'GET') {
						u += content;
						content = '';
					}
					try {
					    this.request.open(method, correctUrl.call(this, u), true);
					} catch (err) {
					    log('Error opening XMLHttpRequest: ' + err.message, 'execute', this);
					    return;
					}
					if (isObject(this.headers)) {
						for (var k in this.headers) {
					    	this.request.setRequestHeader(k, this.headers[k]);
						};
					}
					if (method != 'GET' && (!this.headers || !this.headers['Content-Type'])) {
						this.request.setRequestHeader('Content-Type', 'application/json');
					}
					if (this.responseType) {
						this.request.responseType = this.responseType;
					}
					this.request.withCredentials = this.withCredentials;
					this.request.send(content);
				"
			),
			'send' => array(
				'args' => array('method', 'pars', 'u'),
				'body' => "
					this.method = method;
					this.tempUrl = u;
					this.execute(pars);
					this.method = null;
					this.tempUrl = null;
				"
			)
		)
	);
?>