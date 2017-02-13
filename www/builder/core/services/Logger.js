_c = function() {
	this.log = function(message, method, object, opts) {
		window.console.log(message);
	};
}
{{GLOBAL}}.set(_c, 'Logger');