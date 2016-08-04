component Form

function onSubmit() {
	if (this.isValid()) {
		this.form = this.form || this.findElement('form');
		var ajax = this.form.getData('ajax');
		if (ajax) {
			this.sendAjaxRequest();
		} else {
			this.setFormKey();
			this.form.submit();
		}
	}
};

function sendAjaxRequest() {
	var action = this.form.attr('action');
	var method = this.form.attr('method');
	if (action) {
		this.request = this.request || new AjaxRequest(action, this.handleResponse, null, this);
		this.request.send(method, this.getControlsData());
	}
};

function setFormKey() {
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

function isValid() {
	return true;
};

function handleResponse(data) {
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

function onSuccess(data) {

}

function onFailure(data) {
	var error = isObject(data) && isString(data['error']) ? data['error'] : '';
	this.log(error, 'onFailure', data);
};

function disposeInternal() {
	this.form = null;
	this.request = null;
};