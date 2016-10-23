function Logger() {
	this.log = function(message, method, object, opts) {
		window.console.log(message);
	};
}
Logger = new Logger();