{{GLOBAL}}.set({{COMPONENT}} = function(){}, 'View');
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.onRenderComplete = function() {
	this.dispatchReadyEvent();
};
{{PROTO}}.setOnReadyHandler = function(handler) {
	this.onReadyHandler = handler;
};
{{PROTO}}.dispatchReadyEvent = function() {
	if (isFunction(this.onReadyHandler)) {
		this.onReadyHandler();
	}
	this.onReady();
};
{{PROTO}}.activate = function(isActivated) {
	if (isActivated) {
		this.dispatchReadyEvent();
	}
};
{{PROTO}}.getTitleParams=function(){};
{{PROTO}}.onReady=function(){};