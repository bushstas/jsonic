application App

function onNoErrors() {
	this.appendChild('menu', true);
}

function onError(errorCode) {
	this.appendChild('menu', false);
}