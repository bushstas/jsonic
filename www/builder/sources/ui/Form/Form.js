component Form

function onSubmit() {
	if (this.isValid()) {
		var form = this.getElement();
		var ajax = form.getData('ajax');
		if (ajax) {
			this.sendAjaxRequest();
		} else {
			this.setFormKey();
			form.submit();
		}
	}
};

function sendAjaxRequest() {
	var form = this.getElement();
	var action = form.attr('action');
	var method = form.attr('method');
	if (action) {
		Loader.doAction(method, action, this.getControlsData(), this.handleResponse, this);
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
	
};