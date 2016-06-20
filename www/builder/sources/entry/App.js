application App

function onNoErrors() {
	var menu = this.getChildById('menu');
	//menu.setAppended(true);
};

function onError(errorCode) {
	var menu = this.getChildById('menu');
	menu.setAppended(false);
};
