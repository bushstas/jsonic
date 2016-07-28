component Form

function initiate() {
	this.controls = {};
};


function onSubmit() {
	if (this.isValid()) {
		if (this.formElement) {
			this.setFormKey();
			this.formElement.submit();
		} else if (this.request) {
			this.request.send(this.options['method'] || 'POST', this.getData());
		}
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

function getData() {
	var data = {};
	for (var k in this.controls) {
		data[k] = this.controls[k].getValue();
	}
	return data;
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

function getControl(name) {
	return this.controls[name];
};

function getCotrolAt(index) {
	return Objects.getByIndex(this.controls, index);
};

function setControlValue(name, value) {
	if (isString(name)) {
		var control = this.getControl(name);
		if (control) {
			control.setValue(value);
		}
	}
};

function enableControl(name, isEnabled) {
	if (isString(name)) {
		var control = this.getControl(name);
		if (control) {
			control.setEnabled(isEnabled);
		}
	}
};

function disposeInternal() {
	this.options = null;
	this.controls = null;
	this.request = null;
	this.formElement = null;
};