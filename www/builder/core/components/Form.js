function Form() {}

Form.prototype.initiate = function() {
	this.options = {};
};

Form.prototype.initOptions = function(options) {
	if (isObject(options)) {
		this.options = options;
		if (!options['ajax']) {
			this.createFormElement();
		} else {
			this.createAjaxRequest();
		}
	} else {
		log('no form options');
	}
};

Form.prototype.createFormElement = function() {
	var formElement = document.createElement('form');
	if (isString(this.options['method'])) {
		formElement.setAttribute('method', this.options['method']);
	}
	if (isString(this.options['action'])) {
		var action = this.correctAction(this.options['action']);
		formElement.setAttribute('action', action);
	}
	this.parentElement.appendChild(formElement);
	this.setScope(formElement);
	this.parentElement = formElement;
	var iframeId = generateRandomKey();
	this.createTargetIframe(iframeId);
	formElement.setAttribute('target', iframeId);
	this.formElement = formElement;
};

Form.prototype.correctAction = function(url) {
	url = url.replace(/^[\.\/]+/, '');
	if (isString(__APIDIR)) {
		var regExp = new RegExp('^' + __APIDIR + "\/");
		url = __APIDIR + '/' + url.replace(regExp, '');
	}
	return '/' + url;
};

Form.prototype.onRenderComplete = function() {
	var controlsContainer = this.options['container'];
	if (controlsContainer && isString(controlsContainer)) {
		controlsContainer = this.findElement('.' + controlsContainer);
	}
	controlsContainer = controlsContainer || this.parentElement;
	if (this.options['ajax']) {
		this.setScope(controlsContainer);
	}	
	if (isArray(this.options['controls'])) {
		var control;
		for (var i = 0; i < this.options['controls'].length; i++) {
			if (isObject(this.options['controls'][i])) {
				this.createControl(this.options['controls'][i], controlsContainer);
			}
		}
	}
	if (isObject(this.options['submit'])) {
		this.createSubmit(controlsContainer);
	}
};

Form.prototype.createControl = function(options, parentElement) {
	var control;
	switch (options['type']) {
		case 'select':

		break;

		case 'textarea':

		break;

		default:
			control = this.createInput(options);
	}
	this.addChild(control, parentElement);
};

Form.prototype.createInput = function(options) {
	return new Input(options);
};

Form.prototype.createSubmit = function(parentElement) {
	var control = new Submit(this.options['submit']);
	this.addChild(control, parentElement);
	this.addListener(control, 'submit', this.onSubmit);
};

Form.prototype.createTargetIframe = function(id) {
	var iframe = document.createElement('iframe');
	iframe.setAttribute('id', id);
	iframe.setAttribute('name', id);
	iframe.style.display = 'none';
	this.parentElement.appendChild(iframe);
};

Form.prototype.createAjaxRequest = function() {
	this.request = new AjaxRequest(this.options['action'], this.handleResponse.bind(this));
};

Form.prototype.onSubmit = function() {
	if (this.isValid()) {
		if (this.formElement) {
			this.setFormKey();
			this.formElement.submit();
		} else if (this.request) {
			this.request.send(this.options['method'] || 'POST', this.getData());
		}
	}
};

Form.prototype.setFormKey = function() {
	this.formKey = generateRandomKey();
	window[this.formKey] = this;
	if (!isElement(this.keyInput)) {
		this.keyInput = document.createElement('input');
		this.keyInput.setAttribute('name', 'formKey');
		this.keyInput.setAttribute('type', 'hidden');
		this.keyInput.value = this.formKey;
		this.getElement().appendChild(this.keyInput);
	}
};

Form.prototype.isValid = function() {
	return true;
};

Form.prototype.getData = function() {
	var data = {};
	this.forEachControlChild(function(child) {
		var name = child.getName();
		if (!!name && isString(name)) {
			data[name] = child.getValue();
		}
	});
	return data;
};

Form.prototype.forEachControlChild = function(callback) {
	this.forEachChild(function(child, index) {
		if (child.instanceOf(Control)) {
			callback.call(this, child, index);
		}
	});
};

Form.prototype.handleResponse = function(data) {
	if (isString(data)) {
		try {
			data = JSON.parse(data);
		} catch (e) {
			log('incorrect form response', 'handleResponse', this, {'data': data});
		}
	}
	if (isObject(data) && data['success']) {
		this.onSuccess(data);
	} else {
		this.onFailure(data);
	}
	if (isString(this.formKey)) {
		this.formKey = null;
		delete window[this.formKey];
	}
};

Form.prototype.onSuccess = function(data) {};
Form.prototype.onFailure = function(data) {
	var error = isObject(data) && isString(data['error']) ? data['error'] : '';
	this.log(error, 'onFailure', data);
};

Form.prototype.disposeInternal = function() {
	this.options = null;
	this.request = null;
	this.formElement = null;
};