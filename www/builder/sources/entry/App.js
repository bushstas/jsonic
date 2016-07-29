application App

function onNoErrors() {

}

function onError(errorCode) {
	this.getChild('menu').setAppended(false);
}