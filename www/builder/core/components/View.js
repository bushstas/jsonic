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
	this.onReady();
};
View.prototype.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
View.prototype.getTitleParams=function(){};
View.prototype.onReady=function(){};
