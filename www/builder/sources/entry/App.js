application App

function onClick() {
	console.log(this.attachController)
}

function onError(errorCode) {
	this.getChild('menu').setAppended(false);
}