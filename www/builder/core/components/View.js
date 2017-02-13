_c = function() {};
_p=_c.prototype;
_p.onRenderComplete = function() {
	this.dispatchReadyEvent();
};
_p.setOnReadyHandler = function(handler) {
	this.onReadyHandler = handler;
};
_p.dispatchReadyEvent = function() {
	if (isFunction(this.onReadyHandler)) {
		this.onReadyHandler();
	}
	this.onReady();
};
_p.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
_p.getTitleParams=function(){};
_p.onReady=function(){};
{{GLOBAL}}.set(_c, 'View');