var Logger = new (function() {
	this.log = function(message, method, object, opts) {
		window.console.log(message);
	};
})();
{{GLOBAL}}.set(Logger, 'Logger');