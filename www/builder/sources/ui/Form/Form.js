component Form

function onSubmit() {
	if (this.isValid()) {
		this.send();
	}
};

function send() {
	get action, method;
	if (action) {
		Loader.doAction(method || 'POST', action, this.getControlsData(), this.handleResponse, this);
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
	if (isObject(data) && !data['error']) {
		this.onSuccess(data);
	} else {
		this.onFailure(data);
	}
};

function onSuccess(data) {

}

function onFailure(data) {
	
};