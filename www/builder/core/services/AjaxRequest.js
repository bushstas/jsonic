function AjaxRequest(url, callback, params, thisObj) {
	var self = this, tempUrl, active = false, 
		withCredentials = false, headers, request, 
		responseType;
	
	this.setHeaders = function(head) {
		headers = head;
	};
	this.setResponseType = function(respType) {
		responseType = respType;
	};
	this.setWithCredentials = function(withCred) {
		withCredentials = withCred;
	};
	this.execute = function(pars) {
		active = true;
		pars = pars || params;
		var u = tempUrl || url,
			method = this.method || 'POST',
			content = getRequestContent(method, pars);
		createRequest();
		if (method == 'GET') {
			u += content;
			content = '';
		}
		try {
		    request.open(method, correctUrl(u), true);
		} catch (err) {
		    log('Error opening XMLHttpRequest: ' + err.message, 'execute', this);
		    return;
		}
		if (isObject(headers)) {
			for (var k in headers) {
		    	request.setRequestHeader(k, headers[k]);
			};
		}
		if (method != 'GET' && (!headers || !headers['Content-Type'])) {
			request.setRequestHeader('Content-Type', 'application/json');
		}
		if (responseType) {
			request.responseType = responseType;
		}
		request.withCredentials = withCredentials;
		request.send(content);
	};
	this.send = function(method, pars, u) {
		this.method = method;
		tempUrl = u;
		this.execute(pars);
		this.method = null;
		tempUrl = null;
	};
	var correctUrl = function(u) {
		u = u.replace(/^[\.\/]+/, '');
		if (isString(__APIDIR)) {
			var regExp = new RegExp('^' + __APIDIR + "\/");
			u = __APIDIR + '/' + u.replace(regExp, '');
		}
		return '/' + u;
	};
	var createRequest = function() {
		request = new XMLHttpRequest();
		request.onreadystatechange = onReadyStateChange.bind(self);
	};
	var getRequestContent = function(method, pars) {
		if (Objects.empty(pars)) return '';
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
	};
	var onReadyStateChange = function(e) {
		var req = e.target;
		if (active && req.readyState == 4) {
			active = false;
			var response = req.response;
			var data;
			try {
				data = JSON.parse(response);
			} catch (e) {
				data = response;
			}
			if (isFunction(callback)) {
				callback.call(thisObj || null, data);
			}
		}
	};
}