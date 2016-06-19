function View() {}
View.prototype.onRenderComplete = function() {
	this.dispatchReadyEvent();
};
View.prototype.setOnReadyHandler = function(handler) {
	this.onReadyHandler = handler;
};
View.prototype.dispatchReadyEvent = function() {
	if (isFunction(this.onReadyHandler)) {
		this.onReadyHandler();
	}
};
View.prototype.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
View.prototype.initControllers = function() {
	var controllersToLoad = this.getControllersToLoad();
	if (isArray(controllersToLoad)) {
		for (var i = 0; i < controllersToLoad.length; i++) {
			if (isObject(controllersToLoad[i])) {
				controllersToLoad[i].load();
			}
		}
	}
};
View.prototype.getTitleParams = function() {};

View.prototype.getControllersToLoad = function() {
	return null;
};
