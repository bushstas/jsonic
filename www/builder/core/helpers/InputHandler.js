function InputHandler() {
	var subscribers = [];
	var options = [];	
	this.subscribe = function(subscriber, opts) {
		if (isObject(opts['callbacks']) && isString(opts['inputSelector']) && subscribers.indexOf(subscriber) == -1) {
			var input = subscriber.findElement(opts['inputSelector']);
			if (input) {
				opts['input'] = input;
				subscribers.push(subscriber);
				options.push(opts);
				var index = subscribers.length - 1;
				input.addEventListener('keyup', onInput.bind(null, index), false);
			}
		}
	};
	var onInput = function(index, e) {
		var keyCode = e.keyCode;
		var keyName = getKeyName(keyCode);
		var opts = options[index];
		var subscriber = subscribers[index];
		var cb = opts['callbacks'];
		if (keyName && isFunction(cb[keyName])) cb[keyName].call(subscriber, e);
		else if (isFunction(cb[keyCode])) cb[keyCode].call(subscriber, e);
	};
	var getKeyName = function(keyCode) {
		return ({'13': 'enter', '27': 'esc'})[keyCode];
	};
}
InputHandler = new InputHandler();